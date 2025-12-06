<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Fortify\Features;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        // Limpia contadores/limiter por usuario
        if ($user) {
            Cache::forget('auth:fail:' . $user->getKey());
            RateLimiter::clear('unlock:mail:' . $user->getKey());
        }

        // Sanitiza intended para evitar loops (login/2fa/verify/etc.)
        $intended     = (string) (session('url.intended') ?? '');
        $intendedPath = parse_url($intended, PHP_URL_PATH) ?: '';
        if ($intendedPath && preg_match('#^/(login|two-factor-challenge|email/verify|password|register)#', $intendedPath)) {
            $intended = null;
            session()->forget('url.intended');
        }

        // Verificar si es admin usando Spatie
        $isAdmin = $user && ($user->isSuperAdmin() || $user->hasRole(['admin', 'super-admin']));

        // Construye el redirect primero
        if ($isAdmin) {
            $response = $intended ? redirect()->to($intended) : redirect()->intended('/admin');
        } else {
            if ($intendedPath && str_starts_with($intendedPath, '/admin')) {
                $intended = null;
                session()->forget('url.intended');
            }
            $response = $intended ? redirect()->to($intended) : redirect()->intended('/');
        }

        // Adjunta el cookie "recordar dispositivo" del 2FA si corresponde
        if ($cookie = $this->makeTwoFactorRememberCookieIfNeeded($request)) {
            $response = $response->withCookie($cookie);
        }

        return $response;
    }

    /**
     * Crea el cookie "two_factor_remember" si:
     *  - 2FA está activo,
     *  - es el POST de two-factor-challenge,
     *  - y el checkbox 'remember' viene marcado.
     */
    private function makeTwoFactorRememberCookieIfNeeded(Request $request)
    {
        if (! Features::enabled(Features::twoFactorAuthentication())) {
            return null;
        }
        if (! $request->boolean('remember')) {
            return null;
        }

        $isTwoFactorPost = $request->routeIs('two-factor.login') || $request->is('two-factor-challenge');
        if (! $isTwoFactorPost) {
            return null;
        }

        /** @var \App\Models\User|null $user */
        $user = $request->user();
        if (! $user) {
            return null;
        }

        $name     = 'two_factor_remember';
        $minutes  = 60 * 24 * 365 * 5;                      // 5 años (ajústalo si quieres)
        $domain   = config('session.domain');               // null en local
        $secure   = (bool) config('session.secure', false); // true en prod con HTTPS
        $sameSite = config('session.same_site', 'lax');

        // ✅ Formato que Fortify reconoce, y SIN encrypt() manual
        $payload = (string) $user->getAuthIdentifier();

        return Cookie::make(
            $name,
            $payload,  // <- sin encrypt(): EncryptCookies ya lo cifrará
            $minutes,
            '/',
            $domain,
            $secure,
            true,   // httpOnly
            false,  // raw
            $sameSite
        );
    }
}
