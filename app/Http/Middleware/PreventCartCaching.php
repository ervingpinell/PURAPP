<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventCartCaching
{
    /**
     * Prevent browser from caching pages with cart data.
     * This ensures expired cart items can't be restored via browser back button.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only apply to HTML responses
        if (
            $response->headers->get('Content-Type') &&
            str_contains($response->headers->get('Content-Type'), 'text/html')
        ) {

            // Prevent caching
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }

        return $response;
    }
}
