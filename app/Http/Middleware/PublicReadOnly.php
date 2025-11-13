<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicReadOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        // SIEMPRE permite admin, sin importar el modo readonly
        if ($request->is('admin/*')) {
            return $next($request);
        }

        // Solo si readonly está activo, bloquea rutas públicas
        if (config('gv.public_readonly') === true) {
            // Bloquea todo intento de compra / carrito / promo / reservas públicas
            $blocked = [
                'apply-promo',
                'api/apply-promo',
                'carrito/*',
                'mi-carrito',
                'my-bookings*',
                'register',
            ];

            foreach ($blocked as $pat) {
                if ($request->is($pat)) {
                    return response()
                        ->view('errors.public-readonly', [], 503)
                        ->header('Retry-After', '3600');
                }
            }
        }

        return $next($request);
    }
}
