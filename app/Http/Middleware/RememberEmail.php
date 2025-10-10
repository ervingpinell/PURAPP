<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;

class RememberEmail
{
    public function handle($request, Closure $next)
    {
        // === Actuar ANTES de $next() para que funcione incluso si el login falla ===
        if ($request->isMethod('post') && $request->has('email')) {
            $remember = $request->boolean('remember_email', false);
            $email    = (string) $request->input('email', '');

            // Alinear con config de sesión
            $domain   = config('session.domain');               // null en local
            $secure   = (bool) config('session.secure', false); // null/false en local
            $sameSite = config('session.same_site', 'lax');     // 'lax' por defecto
            $path     = config('session.path', '/');

            if ($remember && $email !== '') {
                // Crear/actualizar cookie por 90 días
                $minutes = 60 * 24 * 90;

                Cookie::queue(cookie(
                    'remembered_email',
                    $email,
                    $minutes,
                    $path,
                    $domain,
                    $secure,
                    false,      // httpOnly=false: la leemos en Blade como request()->cookie(...)
                    false,      // raw
                    $sameSite
                ));
            } else {
                // Borrar cookie usando los mismos atributos (path/domain) para garantizar el overwrite
                // Nota: la firma de forget en Laravel solo acepta (name, path, domain)
                Cookie::queue(Cookie::forget('remembered_email', $path, $domain));
            }
        }

        return $next($request);
    }
}
