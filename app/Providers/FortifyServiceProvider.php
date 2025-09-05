<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse as FailedPwdLinkContract;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse as SuccessPwdLinkContract;
use Laravel\Fortify\Contracts\FailedPasswordResetResponse as FailedPwdResetContract;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Respuestas personalizadas post login/logout/register
        $this->app->singleton(LoginResponseContract::class,    \App\Http\Responses\LoginResponse::class);
        $this->app->singleton(LogoutResponseContract::class,   \App\Http\Responses\LogoutResponse::class);
        $this->app->singleton(RegisterResponseContract::class, \App\Http\Responses\RegisterResponse::class);

        // Reset de contraseña (compatibles con tu versión de Fortify)
        $this->app->singleton(FailedPwdLinkContract::class,    \App\Http\Responses\PasswordResetLinkFailedResponse::class);
        $this->app->singleton(SuccessPwdLinkContract::class,   \App\Http\Responses\PasswordResetLinkSentResponse::class);
        $this->app->singleton(FailedPwdResetContract::class,   \App\Http\Responses\PasswordResetFailedResponse::class);
        // Nota: NO hay contrato de "SuccessfulPasswordResetResponse" en esta versión.
    }

    public function boot(): void
    {
        // Actions Fortify
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // Vistas Fortify
        Fortify::loginView(fn () => view('auth.login'));
        Fortify::registerView(fn () => view('auth.register'));
        Fortify::requestPasswordResetLinkView(fn () => view('auth.forgot-password'));
        Fortify::resetPasswordView(fn ($request) => view('auth.reset-password', ['request' => $request]));
        Fortify::confirmPasswordView(fn () => view('auth.confirm-password'));
        Fortify::twoFactorChallengeView(fn () => view('auth.two-factor-challenge'));
        if (method_exists(Fortify::class, 'verifyEmailView')) {
            Fortify::verifyEmailView(fn () => view('auth.verify-email'));
        }

        // Reglas por defecto para contraseñas
        Password::defaults(fn () => Password::min(8)->uncompromised());

        // Rate limiting
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email');
            return [
                Limit::perMinute(5)->by($email.$request->ip()),
                Limit::perMinute(30)->by($request->ip()),
            ];
        });
        RateLimiter::for('two-factor', fn (Request $r) => Limit::perMinute(5)->by($r->ip()));

        // Autenticación con mensajes localizados (AdminLTE)
        Fortify::authenticateUsing(function (Request $request) {
            $email    = mb_strtolower(trim((string) $request->input('email')));
            $password = (string) $request->input('password');

            $user = User::where('email', $email)->first();

            if (! $user) {
                throw ValidationException::withMessages([
                    'email' => __('adminlte::validation.invalid_credentials'),
                ]);
            }

            if (method_exists($user, 'isLocked') && $user->isLocked()) {
                throw ValidationException::withMessages([
                    'email' => __('adminlte::auth.account.locked'),
                ]);
            }

            if (is_null($user->email_verified_at)) {
                throw ValidationException::withMessages([
                    'email' => __('adminlte::auth.verify.message'),
                ]);
            }

            if (isset($user->status) && ! $user->status) {
                throw ValidationException::withMessages([
                    'email' => __('adminlte::validation.invalid_credentials'),
                ]);
            }

            if (! Hash::check($password, (string) $user->password)) {
                throw ValidationException::withMessages([
                    'email' => __('adminlte::validation.invalid_credentials'),
                ]);
            }

            return $user;
        });
    }
}
