<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user   = $request->user();
        $roleId = (int) ($user->role_id ?? 0);

        $intended     = session('url.intended'); // puede ser null
        $intendedPath = $intended ? (string) parse_url($intended, PHP_URL_PATH) : null;

        // Nunca redirigir a páginas de auth/flujo sensible post-login
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
            // Admins: evita intended problemática; destino por defecto /admin
            return $intended ? redirect()->to($intended) : redirect()->intended('/admin');
        }

        // Clientes: si intended apunta a /admin, ignóralo
        if ($intendedPath && str_starts_with($intendedPath, '/admin')) {
            $intended = null;
        }

        return $intended ? redirect()->to($intended) : redirect()->intended('/');
    }
}
