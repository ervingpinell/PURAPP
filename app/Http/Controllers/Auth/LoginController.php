<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Notifications\AccountLockedNotification;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $locale = session('locale', config('app.locale'));
        app()->setLocale($locale);

        if ($request->has('redirect')) {
            session(['after_login_redirect' => $request->get('redirect')]);
        }

        return view('adminlte::auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(
            [
                'email'    => ['required','email'],
                'password' => ['required'],
            ],
            [
                'email.required'    => __('adminlte::validation.required_email'),
                'password.required' => __('adminlte::validation.required_password'),
            ]
        );

        $email = mb_strtolower(trim($credentials['email']));

        $throttleKey  = $this->throttleKeyFrom($email, $request->ip());
        $maxAttempts  = 5;
        $decaySeconds = 15 * 60;

        // Límite alcanzado: bloquear + enviar enlace (1 vez/ventana)
        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $user = User::where('email', $email)->first();
            $justSent = false;

            if ($user) {
                if (! $user->is_locked) {
                    $user->is_locked = true;
                    $user->save();
                }
                cache()->put('lock_key:'.$user->id, $throttleKey, $decaySeconds);
                $justSent = $this->sendUnlockLinkOnce($user, $decaySeconds);
            }

            $retry = RateLimiter::availableIn($throttleKey);
            $resp = redirect()
                ->route('account.locked')
                ->with('retry_seconds', $retry)
                ->with('locked_message', __('adminlte::auth.account.locked_message'));

            if ($justSent) {
                $resp->with('status', __('adminlte::auth.account.unlock_link_sent'));
            }

            return $resp;
        }

        $user = User::where('email', $email)->first();

        // Si ya está bloqueado: pantalla + (intenta enviar si limiter lo permite)
        if ($user && $user->is_locked) {
            if (! cache()->has('lock_key:'.$user->id)) {
                cache()->put('lock_key:'.$user->id, $throttleKey, $decaySeconds);
            }

            $justSent = $this->sendUnlockLinkOnce($user, $decaySeconds);

            $retry = RateLimiter::availableIn($throttleKey);
            $resp = redirect()
                ->route('account.locked')
                ->with('retry_seconds', $retry)
                ->with('locked_message', __('adminlte::auth.account.locked'));

            if ($justSent) {
                $resp->with('status', __('adminlte::auth.account.unlock_link_sent'));
            }

            return $resp;
        }

        // Bloquear login si NO está verificado (sin crear sesión)
        if ($user
            && method_exists($user, 'hasVerifiedEmail')
            && ! $user->hasVerifiedEmail()
            && Auth::validate(['email' => $email, 'password' => $credentials['password']])
        ) {
            $verifyKey = 'verify:'.$user->id;
            if (! RateLimiter::tooManyAttempts($verifyKey, 3)) {
                $user->sendEmailVerificationNotification();
                RateLimiter::hit($verifyKey, 10 * 60);
            }

            return back()
                ->withErrors(['email' => __('adminlte::auth.verify_message')])
                ->with('status', __('adminlte::auth.verify.link_sent'));
        }

        // Intento normal
        if (Auth::attempt(['email' => $email, 'password' => $credentials['password']], $request->filled('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $authUser = Auth::user();
            $authUser->refresh();

            // Defensa: no mantener sesión si no está verificado
            if (method_exists($authUser, 'hasVerifiedEmail') && ! $authUser->hasVerifiedEmail()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $verifyKey = 'verify:'.$authUser->id;
                if (! RateLimiter::tooManyAttempts($verifyKey, 3)) {
                    $authUser->sendEmailVerificationNotification();
                    RateLimiter::hit($verifyKey, 10 * 60);
                }

                return back()
                    ->withErrors(['email' => __('adminlte::auth.verify_message')])
                    ->with('status', __('adminlte::auth.verify.link_sent'));
            }

            // Defensa: bloqueo en caliente
            if ($authUser->is_locked ?? false) {
                Auth::logout();
                return redirect()
                    ->route('account.locked')
                    ->with('locked_message', __('adminlte::auth.account.locked'));
            }

            return $this->authenticated($request, $authUser);
        }

        // Falla: sumar intento y si alcanza límite, bloquear + enviar
        RateLimiter::hit($throttleKey, $decaySeconds);

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            if ($user) {
                if (! $user->is_locked) {
                    $user->is_locked = true;
                    $user->save();
                }
                cache()->put('lock_key:'.$user->id, $throttleKey, $decaySeconds);
                $justSent = $this->sendUnlockLinkOnce($user, $decaySeconds);

                $retry = RateLimiter::availableIn($throttleKey);
                $resp = redirect()
                    ->route('account.locked')
                    ->with('retry_seconds', $retry)
                    ->with('locked_message', __('adminlte::auth.account.locked_message'));

                if ($justSent) {
                    $resp->with('status', __('adminlte::auth.account.unlock_link_sent'));
                }

                return $resp;
            }

            $retry = RateLimiter::availableIn($throttleKey);
            return redirect()
                ->route('account.locked')
                ->with('retry_seconds', $retry)
                ->with('locked_message', __('adminlte::auth.account.locked_message'));
        }

        return back()->withErrors([
            'email' => __('adminlte::validation.invalid_credentials'),
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    protected function authenticated(Request $request, $user)
    {
        if (session()->has('after_login_redirect')) {
            $url = session()->pull('after_login_redirect');
            return redirect($url);
        }

        if ($user->role_id == 1 || $user->role_id == 2) {
            return redirect()->route('admin.home');
        }

        return redirect()->route('home');
    }

    protected function throttleKey(Request $request): string
    {
        return $this->throttleKeyFrom(
            mb_strtolower(trim((string)$request->input('email'))),
            $request->ip()
        );
    }

    protected function throttleKeyFrom(string $email, string $ip): string
    {
        return $email.'|'.$ip;
    }

    /**
     * Envía el enlace de desbloqueo como mucho una vez por ventana.
     * Devuelve true si se envió en esta llamada.
     */
    protected function sendUnlockLinkOnce(User $user, int $ttlSeconds): bool
    {
        $unlockLimiterKey = 'unlock-mail:'.$user->id;

        if (RateLimiter::tooManyAttempts($unlockLimiterKey, 1)) {
            return false;
        }

        $unlockUrl = URL::temporarySignedRoute(
            'account.unlock.process',
            now()->addMinutes(30),
            ['user' => $user, 'hash' => sha1($user->email)]
        );

        try {
            $user->notify(new AccountLockedNotification($unlockUrl));
            RateLimiter::hit($unlockLimiterKey, $ttlSeconds);
            return true;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }
}
