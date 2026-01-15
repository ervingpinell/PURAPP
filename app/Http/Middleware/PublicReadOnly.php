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

        // Solo si readonly está activo, aplicar restricciones
        if (config('site.public_readonly') === true) {
            // Rutas que SIEMPRE se bloquean en modo readonly (incluso para usuarios autenticados)
            $alwaysBlocked = [
                'register',      // No permitir nuevos registros
                'contact',       // No permitir formulario de contacto
                'contact/*',
            ];

            foreach ($alwaysBlocked as $pat) {
                if ($request->is($pat)) {
                    return response()
                        ->view('errors.public-readonly', [], 503)
                        ->header('Retry-After', '3600');
                }
            }

            // Rutas de carrito/checkout: verificar autenticación
            $checkoutRoutes = [
                'apply-promo',
                'api/apply-promo',
                'carrito/*',    // Legacy
                'mi-carrito',   // Legacy
                'my-bookings*',
                'checkout*',
                'carts/*',
                'payment/*',
            ];

            $allowGuestCheckout = (bool) config('site.allow_guest_checkout', true);

            foreach ($checkoutRoutes as $pat) {
                if ($request->is($pat)) {
                    // Si el usuario está autenticado, permitir acceso
                    if ($request->user()) {
                        return $next($request);
                    }

                    // Si es invitado y NO se permite guest checkout, bloquear
                    if (!$allowGuestCheckout) {
                        return response()
                            ->view('errors.public-readonly', [], 503)
                            ->header('Retry-After', '3600');
                    }

                    // Si es invitado y SÍ se permite guest checkout, permitir
                    return $next($request);
                }
            }
        }

        return $next($request);
    }
}
