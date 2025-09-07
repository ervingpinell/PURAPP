<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user   = $request->user();
        $roleId = (int) ($user->role_id ?? 0);

        // Limpia contador de fallos por usuario
        Cache::forget('auth:fail:'.$user->getKey());

        // Limpia limiter 2FA (si quedó algo colgado)
        $twoFaKey = (string) $request->session()->get('login.id', $request->ip());
        RateLimiter::clear('2fa|'.$twoFaKey);

        // Sanitiza intended para evitar bucles
        $intended     = session('url.intended'); // puede ser null
        $intendedPath = $intended ? (string) parse_url($intended, PHP_URL_PATH) : null;

        $deny = [
            '/login', '/register',
            '/forgot-password', '/reset-password',
            '/email/verify', '/email/verification-notification',
            '/two-factor-challenge',
            '/admin/profile', '/admin/profile/edit',
        ];

        if ($intendedPath && in_array($intendedPath, $deny, true)) {
            $intended = null;
            session()->forget('url.intended');
        }

        $isAdmin = in_array($roleId, [1, 2], true);

        if ($isAdmin) {
            return $intended ? redirect()->to($intended) : redirect()->intended('/admin');
        }

        if ($intendedPath && str_starts_with($intendedPath, '/admin')) {
            $intended = null;
            session()->forget('url.intended');
        }

        return $intended ? redirect()->to($intended) : redirect()->intended('/');
    }
}
