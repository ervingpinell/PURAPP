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
    /** Lock settings */
    private int $maxFails          = 5;     // lock user after N failures
    private int $ipWindowMin       = 5;     // IP window in minutes
    private int $captchaAfterFails = 3;     // require captcha from N failures
    private int $backoffMaxMs      = 1200;  // backoff cap in ms

    /** Persistent cache store for counters */
    private string $counterStore   = 'file'; // 'file' | 'redis' | 'database'

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
            // Toggle strict mode from config/auth.php ('strict_login') or .env (AUTH_STRICT_LOGIN)
            $strict = (bool) config('auth.strict_login', true);

            // ── Bandera para bloquear login público ───────────────────────────────
            // Si está en false, SOLO pueden iniciar sesión usuarios con permiso 'access-admin'
            $allowPublicLogin = (bool) (config('gv.allow_public_login') ?? env('GV_ALLOW_PUBLIC_LOGIN', false));

            // ── Non-strict mode (for local testing) ───────────────────────────────
            if (! $strict) {
                $user = User::where('email', $request->input('email'))->first();
                if ($user && Hash::check($request->input('password'), (string) $user->password)) {
                    if (! $allowPublicLogin && ! $user->can('access-admin')) {
                        Log::warning('[Auth] Public login disabled (non-strict). User denied.', ['uid' => $user->getKey()]);
                        throw ValidationException::withMessages([
                            'email' => __('auth.login_disabled') ?: __('adminlte::validation.invalid_credentials'),
                        ]);
                    }
                    Log::info('[Auth] Non-strict mode: login allowed', ['uid' => $user->getKey()]);
                    return $user;
                }
                Log::warning('[Auth] Non-strict mode: invalid credentials', [
                    'email' => (string) $request->input('email'),
                    'ip'    => $request->ip(),
                ]);
                throw ValidationException::withMessages([
                    'email' => __('adminlte::validation.invalid_credentials'),
                ]);
            }

            // ── Strict mode ───────────────────────────────────────────────────────
            // Validate email
            if (! $request->filled('email')) {
                Log::warning('[Auth] Email missing', ['ip' => $request->ip()]);
                throw ValidationException::withMessages([
                    'email' => __('validation.required', ['attribute' => 'email']),
                ]);
            }
            $emailRaw = (string) $request->input('email');
            if (! filter_var($emailRaw, FILTER_VALIDATE_EMAIL)) {
                Log::warning('[Auth] Email format invalid', ['email' => $emailRaw, 'ip' => $request->ip()]);
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

            // Captcha if needed
            if ($this->shouldRequireCaptcha($user, $ip)) {
                if (! $this->verifyTurnstile($request)) {
                    session()->put('login.captcha', true);
                    Log::warning('[Auth] CAPTCHA verification failed', ['email' => $email, 'ip' => $ip]);
                    throw ValidationException::withMessages([
                        'email' => __('auth.captcha_failed') ?: __('adminlte::validation.invalid_credentials'),
                    ]);
                }
            }

            // User not found → penalize IP
            if (! $user) {
                $ipFails = $this->bumpIpFails($ip);
                $this->sleepBackoff($ipFails);

                if ($ipFails >= $captchaAfter) {
                    session()->put('login.captcha', true);
                }

                Log::info('[Auth] Invalid credentials (user not found)', [
                    'email'    => $email,
                    'ip'       => $ip,
                    'ip_fails' => $ipFails,
                ]);

                throw ValidationException::withMessages([
                    'email' => __('adminlte::validation.invalid_credentials'),
                ]);
            }

            // Locked user
            if (!empty($user->is_locked)) {
                $this->triggerUnlockEmailOnce($user, 60, 10);
                Log::warning('[Auth] Account is locked', ['uid' => $user->getKey(), 'email' => $email]);
                throw ValidationException::withMessages([
                    'email' => __('adminlte::auth.account.locked'),
                ]);
            }

            // Wrong password → penalize user + IP
            if (! Hash::check($password, (string) $user->password)) {
                $userFails = $this->bumpUserFails($user->getKey());
                $ipFails   = $this->bumpIpFails($ip);
                $this->sleepBackoff(max($userFails, $ipFails));

                if (max($userFails, $ipFails) >= $captchaAfter) {
                    session()->put('login.captcha', true);
                }

                Log::info('[Auth] Wrong password', [
                    'uid'        => $user->getKey(),
                    'email'      => $email,
                    'ip'         => $ip,
                    'user_fails' => $userFails,
                    'ip_fails'   => $ipFails,
                ]);

                if ($userFails >= $maxFails) {
                    $user->is_locked = true;
                    $user->save();
                    $this->sendUnlockEmail($user, 60);
                    Log::warning('[Auth] User locked due to too many failures', [
                        'uid'        => $user->getKey(),
                        'email'      => $email,
                        'user_fails' => $userFails,
                    ]);
                    throw ValidationException::withMessages([
                        'email' => __('adminlte::auth.account.locked'),
                    ]);
                }

                $remaining = max(0, $maxFails - $userFails);
                $msg = trans_choice('auth.login.remaining_attempts', $remaining, ['count' => $remaining])
                    ?: "Invalid credentials. {$remaining} attempt(s) remaining.";

                throw ValidationException::withMessages([
                    'email' => $msg,
                ]);
            }

            // ── Password OK: verificar si el login público está permitido ─────────
            if (! $allowPublicLogin && ! $user->can('access-admin')) {
                Log::warning('[Auth] Public login disabled. User denied.', ['uid' => $user->getKey(), 'email' => $email]);
                throw ValidationException::withMessages([
                    'email' => __('auth.login_disabled') ?: __('adminlte::validation.invalid_credentials'),
                ]);
            }

            // Password OK → clear counters
            $this->store()->forget($this->failKeyUser($user->getKey()));
            $this->store()->forget($this->ipKey($ip));
            session()->forget('login.captcha');

            // Inactive user
            if (isset($user->status) && ! $user->status) {
                Log::warning('[Auth] Inactive user tried to login', ['uid' => $user->getKey()]);
                throw ValidationException::withMessages([
                    'email' => __('adminlte::validation.invalid_credentials'),
                ]);
            }

            // Not verified → resend and block login
            if ($user instanceof MustVerifyEmailContract && ! $user->hasVerifiedEmail()) {
                $this->maybeSendVerificationEmail($user, 10);
                Log::info('[Auth] Email not verified. Verification resent (login blocked)', [
                    'uid' => $user->getKey(),
                    'to'  => $user->email,
                ]);
                throw ValidationException::withMessages([
                    'email' => __('adminlte::auth.verify.message'),
                ]);
            }

            Log::info('[Auth] Login successful', ['uid' => $user->getKey(), 'ip' => $ip]);
            return $user;
        });
    }

    /* ===== Persistent store ===== */
    private function store()
    {
        return Cache::store($this->counterStore);
    }

    /* ===== Keys ===== */
    private function failKeyUser(int|string $userId): string { return 'auth:fail:' . $userId; }
    private function ipKey(string $ip): string { return 'auth:ip:' . sha1($ip); }

    /* ===== Counters ===== */
    private function bumpUserFails(int|string $userId): int
    {
        $key = $this->failKeyUser($userId);
        if (! $this->store()->has($key)) {
            $this->store()->forever($key, 0);
        }
        return (int) $this->store()->increment($key);
    }

    private function bumpIpFails(string $ip): int
    {
        $key = $this->ipKey($ip);
        if (! $this->store()->has($key)) {
            $this->store()->put($key, 0, now()->addMinutes($this->ipWindowMin));
        }
        return (int) $this->store()->increment($key);
    }

    /* ===== Logic ===== */
    private function sleepBackoff(int $fails): void
    {
        $base = 150;
        $ms = min($this->backoffMaxMs, $base * (2 ** max(0, $fails - 1)));
        usleep($ms * 1000);
    }

    private function shouldRequireCaptcha(?User $user, string $ip): bool
    {
        if ((bool) session('login.captcha')) return true;

        $userFails = $user ? (int) ($this->store()->get($this->failKeyUser($user->getKey())) ?? 0) : 0;
        $ipFails   = (int) ($this->store()->get($this->ipKey($ip)) ?? 0);

        return max($userFails, $ipFails) >= $this->captchaAfterFails;
    }

    private function verifyTurnstile(Request $request): bool
    {
        $secret = (string) config('services.turnstile.secret'); // map TURNSTILE_SECRET in services.php
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
            Log::warning('[Auth] Turnstile verify error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /* ===== Unlock & Verify ===== */
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
            Log::info('[Auth] Lock email sent', ['uid' => $user->getKey(), 'to' => $user->email]);
        } catch (\Throwable $e) {
            Log::error('[Auth] Failed to send lock email', [
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
                Log::info('[Auth] Verification email sent (login blocked)', [
                    'uid' => $user->getKey(),
                    'to'  => $user->email,
                ]);
            } catch (\Throwable $e) {
                Log::error('[Auth] Failed to send verification email', [
                    'uid' => $user->getKey(),
                    'err' => $e->getMessage(),
                ]);
            }
        }
    }
}
