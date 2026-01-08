<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Build CSP directives
        $directives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://integracion.alignetsac.com https://calidad.alignetsac.com https://d3fy5312uavt55.cloudfront.net",
            "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com",
            "font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com",
            "img-src 'self' data: https: blob:",
            "connect-src 'self' https://vpayment.verifika.com https://integracion.alignetsac.com https://calidad.alignetsac.com https://d3fy5312uavt55.cloudfront.net",
            "frame-src 'self' https://integracion.alignetsac.com https://calidad.alignetsac.com https://vpayment.verifika.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self' https://integracion.alignetsac.com https://calidad.alignetsac.com",
        ];

        $csp = implode('; ', $directives);

        // Set CSP header
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
