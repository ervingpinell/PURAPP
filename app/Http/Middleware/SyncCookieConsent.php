<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class SyncCookieConsent
{
    public function handle(Request $request, Closure $next)
    {
        // Solo si la sesión está disponible (StartSession ya corrió)
        if (method_exists($request, 'hasSession') && $request->hasSession()) {

            // --- Migración de nombres antiguos a uno único ---
            $current = $request->cookies->get('gv_cookie_consent', null);

            // Cookies legadas que queremos eliminar
            $legacyNames = ['cookies_accepted', 'cookie_consent'];

            // Si no existe la cookie "buena", pero sí alguna de las legadas, migramos.
            if ($current === null) {
                foreach ($legacyNames as $name) {
                    if ($request->cookies->has($name)) {
                        $legacyVal = (string) $request->cookies->get($name);
                        $current   = ($legacyVal === '1' || $legacyVal === 'true') ? '1' : '0';

                        // Seteamos la oficial por 1 año
                        Cookie::queue(cookie(
                            name:     'gv_cookie_consent',
                            value:    $current,
                            minutes:  60 * 24 * 365,
                            path:     '/',
                            domain:   config('session.domain'),
                            secure:   (bool) config('session.secure', false),
                            httpOnly: false,
                            raw:      false,
                            sameSite: config('session.same_site', 'lax')
                        ));

                        break;
                    }
                }
            }

            // Borramos siempre las legadas para que no vuelvan a aparecer
            foreach ($legacyNames as $name) {
                if ($request->cookies->has($name)) {
                    Cookie::queue(Cookie::forget(
                        name:   $name,
                        path:   '/',
                        domain: config('session.domain')
                    ));
                }
            }

            // --- Sincronizar cookie → sesión ---
            $val = (string) ($current ?? $request->cookies->get('gv_cookie_consent', ''));
            if ($val === '1') {
                $request->session()->put('cookies.accepted', true);
            } else {
                $request->session()->forget('cookies.accepted');
            }
        }

        return $next($request);
    }
}
