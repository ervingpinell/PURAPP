<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceCorrectDomain
{
    public function handle(Request $request, Closure $next)
    {
        // En entornos no productivos (local, dev_test) NO forzamos dominio
        if (! app()->environment('production')) {
            return $next($request);
        }

        // === PRODUCCIÓN ===
        // Dominio canónico
        $canonicalHost = 'greenvacationscr.com';

        $host = $request->getHost();

        // Permitimos también www
        if ($host === $canonicalHost || $host === 'www.' . $canonicalHost) {
            return $next($request);
        }

        // Cualquier otro host -> redirige al dominio canonical
        $uri = $request->getRequestUri();

        return redirect()->to('https://' . $canonicalHost . $uri, 301);
    }
}
