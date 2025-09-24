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
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse as FailedPwdLinkContract;
use Laravel\Fortify\Contracts\FailedPasswordResetResponse as FailedPwdResetContract;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse as SuccessPwdLinkContract;
use Laravel\Fortify\Fortify;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;

class FortifyServiceProvider extends ServiceProvider
{
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

        Fortify::loginView(fn () => view('auth.login'));
        Fortify::registerView(fn () => view('auth.register'));
        Fortify::requestPasswordResetLinkView(fn () => view('auth.forgot-password'));
        Fortify::resetPasswordView(fn ($request) => view('auth.reset-password', ['request' => $request]));
        Fortify::confirmPasswordView(fn () => view('auth.confirm-password'));
        Fortify::twoFactorChallengeView(fn () => view('auth.two-factor-challenge'));
        if (method_exists(Fortify::class, 'verifyEmailView')) {
            Fortify::verifyEmailView(fn () => view('auth.verify-email'));
        }

        Password::defaults(fn () => Password::min(8)->uncompromised());

        // RateLimiter solo para 2FA
        RateLimiter::for('two-factor', function (Request $request) {
            $key = (string) $request->session()->get('login.id', $request->ip());
            return [ Limit::perMinute(5)->by('2fa|'.$key) ];
        });

        /**
         * BLOQUEO POR FALLOS + verificación por email (login permitido SOLO si está verificado):
         *  1) usuario existe
         *  2) si está bloqueado → manejar y cortar
         *  3) validar contraseña; si es incorrecta → contar fallos/bloquear
         *  4) estado inactivo
         *  5) si NO verificó email (password CORRECTA) → (re)enviar verificación con cooldown y CORTAR login
         *  6) login ok
         */
        Fortify::authenticateUsing(function (Request $request) {
            $email    = mb_strtolower(trim((string) $request->input('email')));
            $password = (string) $request->input('password');

            $maxFails  = 6;   // umbral de bloqueos
            $unlockTTL = 60;  // minutos de validez del link
            $resendTTL = 10;  // cooldown (min) para reenvío (lock/verify)

            /** @var \App\Models\User|null $user */
            $user = User::where('email', $email)->first();

            if (! $user) {
                throw ValidationException::withMessages([
                    'email' => __('adminlte::validation.invalid_credentials'),
                ]);
            }

            // === YA BLOQUEADO ===
            if (!empty($user->is_locked)) {
                $resendKey = 'unlock:mail:'.$user->getKey();

                if (! RateLimiter::tooManyAttempts($resendKey, 1)) {
                    RateLimiter::hit($resendKey, $resendTTL * 60);

                    try {
                        $unlockUrl = URL::temporarySignedRoute(
                            'unlock.process',
                            now()->addMinutes($unlockTTL),
                            ['user' => $user->getKey(), 'hash' => sha1($user->email)]
                        );

                        $user->notify(new AccountLockedNotification($unlockUrl));
                        Log::info('Reenvío de desbloqueo (usuario ya bloqueado)', [
                            'uid' => $user->getKey(), 'to' => $user->email
                        ]);
                    } catch (\Throwable $e) {
                        Log::error('Fallo reenvío desbloqueo (locked)', [
                            'uid' => $user->getKey(), 'err' => $e->getMessage()
                        ]);
                    }
                }

                throw ValidationException::withMessages([
                    'email' => __('adminlte::auth.account.locked'),
                ]);
            }

            // === PASSWORD INCORRECTO === (NO disparamos verificación en este caso)
            if (! Hash::check($password, (string) $user->password)) {
                $failKey = 'auth:fail:'.$user->getKey();

                if (! Cache::has($failKey)) {
                    Cache::forever($failKey, 0);
                }
                $fails = (int) Cache::increment($failKey); // 1..N, persistente

                if ($fails >= $maxFails) {
                    // Bloquear
                    $user->is_locked = true;
                    $user->save();

                    RateLimiter::hit('unlock:mail:'.$user->getKey(), 10 * 60);

                    // Enviar mail de bloqueo
                    try {
                        $unlockUrl = URL::temporarySignedRoute(
                            'unlock.process',
                            now()->addMinutes($unlockTTL),
                            ['user' => $user->getKey(), 'hash' => sha1($user->email)]
                        );

                        $user->notify(new AccountLockedNotification($unlockUrl));
                        Log::info('Notificación de bloqueo enviada', [
                            'uid' => $user->getKey(), 'to' => $user->email
                        ]);
                    } catch (\Throwable $e) {
                        Log::error('Fallo enviando notificación de bloqueo', [
                            'uid' => $user->getKey(), 'err' => $e->getMessage()
                        ]);
                    }

                    throw ValidationException::withMessages([
                        'email' => __('adminlte::auth.account.locked'),
                    ]);
                }

                // Intentos restantes (1..5)
                $remaining = max(0, $maxFails - $fails);
                $msg = trans_choice('auth.login.remaining_attempts', $remaining, ['count' => $remaining])
                    ?: __('adminlte::validation.invalid_credentials');

                throw ValidationException::withMessages([
                    'email' => $msg,
                ]);
            }

            // === PASSWORD CORRECTO A PARTIR DE AQUÍ ===
            Cache::forget('auth:fail:'.$user->getKey());

            // Estado inactivo
            if (isset($user->status) && ! $user->status) {
                throw ValidationException::withMessages([
                    'email' => __('adminlte::validation.invalid_credentials'),
                ]);
            }

            // Email NO verificado → reenvía (con cooldown) y CORTA el login
            if ($user instanceof MustVerifyEmailContract && ! $user->hasVerifiedEmail()) {
                $resendKey = 'verify:mail:'.$user->getKey();

                if (! RateLimiter::tooManyAttempts($resendKey, 1)) {
                    RateLimiter::hit($resendKey, $resendTTL * 60);
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

                // Importante: NO permitimos el login si no está verificado
                throw ValidationException::withMessages([
                    'email' => __('adminlte::auth.verify.message'),
                ]);
            }

            // === LOGIN OK ===
            return $user;
        });
    }
}
