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

        // ==============================
        // CHECK 1: public_readonly mode
        // ==============================
        if (config('site.public_readonly') === true) {
            // Rutas que SIEMPRE se bloquean en modo readonly (incluso para usuarios autenticados)
            $alwaysBlocked = [
                'register',      // No permitir nuevos registros
                'contact',       // No permitir formulario de contacto
                'contact/*',
            ];

            foreach ($alwaysBlocked as $pat) {
                if ($request->is($pat)) {
                    return $this->blockedResponse();
                }
            }
        }

        // ==============================
        // CHECK 2: allow_guest_checkout (INDEPENDENT of public_readonly)
        // ==============================
        $allowGuestCheckout = (bool) config('site.allow_guest_checkout', true);

        // If guest checkout is disabled, block guests from cart/checkout routes
        if (!$allowGuestCheckout && !$request->user()) {
            $guestBlockedRoutes = [
                'apply-promo',
                'api/apply-promo',
                'carrito/*',    // Legacy
                'mi-carrito',   // Legacy
                'my-cart',
                'checkout*',
                'carts/*',
                'payment/*',
            ];

            foreach ($guestBlockedRoutes as $pat) {
                if ($request->is($pat)) {
                    return $this->blockedResponse();
                }
            }
        }

        // ==============================
        // CHECK 3: allow_public_registration (INDEPENDENT of public_readonly)
        // ==============================
        $allowPublicRegistration = (bool) config('site.allow_public_registration', true);

        if (!$allowPublicRegistration && $request->is('register')) {
            return $this->blockedResponse();
        }

        return $next($request);
    }

    /**
     * Return a 503 "Service Unavailable" response for blocked routes.
     */
    protected function blockedResponse(): Response
    {
        return response()
            ->view('errors.public-readonly', [], 503)
            ->header('Retry-After', '3600');
    }
}

