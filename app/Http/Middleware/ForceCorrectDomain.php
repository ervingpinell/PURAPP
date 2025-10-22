<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceCorrectDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $correctDomain = 'greenvacationscr.com';
        $currentHost = $request->getHost();

        // Si no es el dominio correcto, redirige permanentemente
        if ($currentHost !== $correctDomain && $currentHost !== 'www.' . $correctDomain) {
            return redirect()->away(
                'https://' . $correctDomain . $request->getRequestUri(),
                301
            );
        }

        return $next($request);
    }
}
