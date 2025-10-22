<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceCorrectDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        // ✅ No redirigir en ambiente local o de desarrollo
        if (app()->environment(['local', 'development'])) {
            return $next($request);
        }

        $correctDomain = 'greenvacationscr.com';
        $currentHost = $request->getHost();

        // Si no es el dominio correcto, redirige
        if ($currentHost !== $correctDomain && $currentHost !== 'www.' . $correctDomain) {
            return redirect()->away(
                'https://' . $correctDomain . $request->getRequestUri(),
                301
            );
        }

        return $next($request);
    }
}
