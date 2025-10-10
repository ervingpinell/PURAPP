<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use App\Notifications\AccountLockedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\{
    FailedPasswordResetLinkRequestResponse as FailedPwdLinkContract,
    FailedPasswordResetResponse as FailedPwdResetContract,
    LoginResponse as LoginResponseContract,
    LogoutResponse as LogoutResponseContract,
    RegisterResponse as RegisterResponseContract,
    SuccessfulPasswordResetLinkRequestResponse as SuccessPwdLinkContract
};
use Laravel\Fortify\Fortify;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;

class FortifyServiceProvider extends ServiceProvider
{
    private int $maxFails          = 5;     // bloquea usuario tras 5 fallos
    private int $ipWindowMin       = 5;     // ventana temporal para IP (minutos)
    private int $captchaAfterFails = 3;     // muestra CAPTCHA desde 3 fallos
    private int $backoffMaxMs      = 1200;  // retraso progresivo

    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class,    \App\Http\Responses\LoginResponse::class);
        $this->app->singleton(LogoutResponseContract::class,   \App\Http\Responses\LogoutResponse::class);
        $this->app->singleton(RegisterResponseContract::class, \App\Http\Responses\RegisterResponse::class);
        $this->app->singleton(FailedPwdLinkContract::class,  \App\Http\Responses\PasswordResetLinkFailedResponse::class);
        $this->app->singleton(SuccessPwdLinkContract::class, \App\Http\Responses\PasswordResetLinkSentResponse::class);
        $this->app->singleton(FailedPwdResetContract::class, \App\Http\Responses\PasswordResetFailedResponse::class);
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Fortify::loginView(fn() => view('auth.login'));
        Fortify::registerView(fn() => view('auth.register'));
        Fortify::requestPasswordResetLinkView(fn() => view('auth.forgot-password'));
        Fortify::resetPasswordView(fn($request) => view('auth.reset-password', ['request' => $request]));
        Fortify::confirmPasswordView(fn() => view('auth.confirm-password'));
        Fortify::twoFactorChallengeView(fn() => view('auth.two-factor-challenge'));
        if (method_exists(Fortify::class, 'verifyEmailView')) {
            Fortify::verifyEmailView(fn() => view('auth.verify-email'));
        }

        Password::defaults(fn() => Password::min(8)->uncompromised());

        RateLimiter::for('two-factor', function (Request $request) {
            $key = (string) $request->session()->get('login.id', $request->ip());
            return [ Limit::perMinute(5)->by('2fa|'.$key) ];
        });

        Fortify::authenticateUsing(function (Request $request) {
            $env = config('app.env');

            // 🔹 Si no es producción, permitir login sin restricciones
            if ($env !== 'production') {
                $user = User::where('email', $request->input('email'))->first();
                if ($user && Hash::check($request->input('password'), (string) $user->password)) {
                    return $user;
                }
                throw ValidationException::withMessages([
                    'email' => __('adminlte::validation.invalid_credentials'),
                ]);
            }

            // 🔹 Producción: seguridad completa
            if (! $request->filled('email')) {
                throw ValidationException::withMessages([
                    'email' => __('validation.required', ['attribute' => 'email']),
                ]);
            }

            $emailRaw = (string) $request->input('email');
            if (! filter_var($emailRaw, FILTER_VALIDATE_EMAIL)) {
                throw ValidationException::withMessages([
                    'email' => __('validation.email', ['attribute' => 'email']),
                ]);
            }

            $email    = mb_strtolower(trim($emailRaw));
            $password = (string) $request->input('password');
            $ip       = $request->ip();

            $maxFails     = $this->maxFails;
            $captchaAfter = $this->captchaAfterFails;

            /** @var \App\Models\User|null $user */
            $user = User::where('email', $email)->first();

            // CAPTCHA si aplica
            if ($this->shouldRequireCaptcha($user, $ip)) {
                if (! $this->verifyTurnstile($request)) {
                    session()->put('login.captcha', true);
                    throw ValidationException::withMessages([
                        'email' => __('auth.captcha_failed') ?: __('adminlte::validation.invalid_credentials'),
                    ]);
                }
            }

            // Usuario inexistente → penaliza IP
            if (! $user) {
                $ipFails = $this->bumpIpFails($ip);
                $this->sleepBackoff($ipFails);

                if ($ipFails >= $captchaAfter) {
                    session()->put('login.captcha', true);
                }

                throw ValidationException::withMessages([
                    'email' => __('adminlte::validation.invalid_credentials'),
                ]);
            }

            // Usuario bloqueado
            if (!empty($user->is_locked)) {
                $this->triggerUnlockEmailOnce($user, 60, 10);
                throw ValidationException::withMessages([
                    'email' => __('adminlte::auth.account.locked'),
                ]);
            }

            // Contraseña incorrecta → penaliza IP y usuario
            if (! Hash::check($password, (string) $user->password)) {
                $userFails = $this->bumpUserFails($user->getKey());
                $ipFails   = $this->bumpIpFails($ip);
                $this->sleepBackoff(max($userFails, $ipFails));

                if (max($userFails, $ipFails) >= $captchaAfter) {
                    session()->put('login.captcha', true);
                }

                if ($userFails >= $maxFails) {
                    $user->is_locked = true;
                    $user->save();
                    $this->sendUnlockEmail($user, 60);
                    throw ValidationException::withMessages([
                        'email' => __('adminlte::auth.account.locked'),
                    ]);
                }

                $remaining = max(0, $maxFails - $userFails);
                $msg = trans_choice('auth.login.remaining_attempts', $remaining, ['count' => $remaining])
                    ?: __('adminlte::validation.invalid_credentials');

                throw ValidationException::withMessages([
                    'email' => $msg,
                ]);
            }

            // ✅ Login exitoso
            Cache::forget($this->failKeyUser($user->getKey()));
            Cache::forget($this->ipKey($ip));
            session()->forget('login.captcha');

            if (isset($user->status) && ! $user->status) {
                throw ValidationException::withMessages([
                    'email' => __('adminlte::validation.invalid_credentials'),
                ]);
            }

            if ($user instanceof MustVerifyEmailContract && ! $user->hasVerifiedEmail()) {
                $this->maybeSendVerificationEmail($user, 10);
                throw ValidationException::withMessages([
                    'email' => __('adminlte::auth.verify.message'),
                ]);
            }

            return $user;
        });
    }

    /* === Keys === */
    private function failKeyUser(int|string $userId): string { return 'auth:fail:' . $userId; }
    private function ipKey(string $ip): string { return 'auth:ip:' . sha1($ip); }

    /* === Counters === */
    private function bumpUserFails(int|string $userId): int
    {
        $key = $this->failKeyUser($userId);
        if (! Cache::has($key)) Cache::forever($key, 0);
        return (int) Cache::increment($key);
    }

    private function bumpIpFails(string $ip): int
    {
        $key = $this->ipKey($ip);
        if (! Cache::has($key)) Cache::put($key, 0, now()->addMinutes($this->ipWindowMin));
        return (int) Cache::increment($key);
    }

    /* === Logic === */
    private function sleepBackoff(int $fails): void
    {
        $base = 150;
        $ms = min($this->backoffMaxMs, $base * (2 ** max(0, $fails - 1)));
        usleep($ms * 1000);
    }

    private function shouldRequireCaptcha(?User $user, string $ip): bool
    {
        if ((bool) session('login.captcha')) return true;
        $userFails = $user ? (int) (Cache::get($this->failKeyUser($user->getKey())) ?? 0) : 0;
        $ipFails   = (int) (Cache::get($this->ipKey($ip)) ?? 0);
        return max($userFails, $ipFails) >= $this->captchaAfterFails;
    }

    private function verifyTurnstile(Request $request): bool
    {
        $secret = (string) config('services.turnstile.secret');
        if ($secret === '') return true;
        $token = $request->input('cf-turnstile-response');
        if (! $token) return false;

        try {
            $resp = Http::asForm()->timeout(5)->post(
                'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                [
                    'secret'   => $secret,
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ]
            );
            if (! $resp->ok()) return false;
            $data = $resp->json();
            return isset($data['success']) && $data['success'] === true;
        } catch (\Throwable $e) {
            Log::warning('Turnstile verify error', ['err' => $e->getMessage()]);
            return false;
        }
    }

    /* === Unlock & Verify === */
    private function triggerUnlockEmailOnce(User $user, int $ttlMinutes, int $cooldownMinutes): void
    {
        $resendKey = 'unlock:mail:' . $user->getKey();
        if (! RateLimiter::tooManyAttempts($resendKey, 1)) {
            RateLimiter::hit($resendKey, $cooldownMinutes * 60);
            $this->sendUnlockEmail($user, $ttlMinutes);
        }
    }

    private function sendUnlockEmail(User $user, int $ttlMinutes): void
    {
        try {
            $unlockUrl = URL::temporarySignedRoute(
                'unlock.process',
                now()->addMinutes($ttlMinutes),
                ['user' => $user->getKey(), 'hash' => sha1($user->email)]
            );
            $user->notify(new AccountLockedNotification($unlockUrl));
            Log::info('Notificación de bloqueo enviada', ['uid' => $user->getKey(), 'to' => $user->email]);
        } catch (\Throwable $e) {
            Log::error('Fallo enviando notificación de bloqueo', [
                'uid' => $user->getKey(),
                'err' => $e->getMessage(),
            ]);
        }
    }

    private function maybeSendVerificationEmail(User $user, int $cooldownMinutes): void
    {
        $resendKey = 'verify:mail:' . $user->getKey();
        if (! RateLimiter::tooManyAttempts($resendKey, 1)) {
            RateLimiter::hit($resendKey, $cooldownMinutes * 60);
            try {
                $user->sendEmailVerificationNotification();
                Log::info('Verification email sent on login attempt (blocked login)', [
                    'uid' => $user->getKey(),
                    'to'  => $user->email,
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed sending verification email on login attempt', [
                    'uid' => $user->getKey(),
                    'err' => $e->getMessage(),
                ]);
            }
        }
    }
}
