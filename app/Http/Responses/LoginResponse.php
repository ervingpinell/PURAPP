<?php

namespace App\Http\Responses;

use Illuminate\Support\Arr;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();
        $roleId = (int) ($user->role_id ?? 0);

        // Lee la URL "intended" que Laravel guardó antes de redirigir a /login
        $intended = session('url.intended'); // puede ser null
        $intendedPath = $intended ? parse_url($intended, PHP_URL_PATH) : null;

        $isAdmin = in_array($roleId, [1, 2], true);

        if ($isAdmin) {
            // Admin: respeta intended si existe; si no, manda a /admin
            return redirect()->intended('/admin');
        }

        // Cliente (role_id = 3 por tu definición):
        // Si la intended cae en /admin, la ignoramos para evitar 403/bucles.
        if ($intendedPath && str_starts_with($intendedPath, '/admin')) {
            return redirect('/');
        }

        // Cliente: respeta intended si existe; si no, manda a la home
        return $intended ? redirect()->to($intended) : redirect()->intended('/');
    }
}
