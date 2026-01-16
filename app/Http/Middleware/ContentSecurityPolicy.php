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

        // Build CSP directives - Less restrictive to avoid breaking existing functionality
        $directives = [
            "default-src 'self' 'unsafe-inline' 'unsafe-eval' data: blob: https:",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https: https://challenges.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https:",
            "font-src 'self' data: https:",
            "img-src 'self' data: blob: https:",
            "connect-src 'self' https: wss: ws:",
            "frame-src 'self' https: https://maps.google.com",
            "object-src 'none'",
            "base-uri 'self'",
        ];

        $csp = implode('; ', $directives);

        // Set CSP header in report-only mode first to test
        $response->headers->set('Content-Security-Policy-Report-Only', $csp);

        return $response;
    }
}
