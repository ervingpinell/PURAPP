<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicReadOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('gv.public_readonly') === true) {
            // Permite admin y API técnicas si hiciera falta
            if ($request->is('admin/*')) {
                return $next($request);
            }

            // Bloquea todo intento de compra / carrito / promo / reservas públicas
            // (ajusta patrones si agregas nuevas rutas)
            $blocked = [
                'apply-promo',
                'api/apply-promo',
                'cart/*',
                'carrito/*',
                'reservas/*',
                'my-cart', 'mi-carrito',
                'my-reservations*',
            ];

            foreach ($blocked as $pat) {
                if ($request->is($pat)) {
                    return response()
                        ->view('errors.public-readonly', [], 503)
                        ->header('Retry-After', '3600'); // opcional
                }
            }
        }

        return $next($request);
    }
}
