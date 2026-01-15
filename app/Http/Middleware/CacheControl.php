<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheControl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Cache static assets for 1 year
        if ($request->is('build/*')) {
            $response->header('Cache-Control', 'public, max-age=31536000, immutable');
            $response->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }

        // Cache storage files (images, etc.) for 1 year
        if ($request->is('storage/*')) {
            $response->header('Cache-Control', 'public, max-age=31536000, immutable');
            $response->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }

        // Cache images for 1 year
        if (preg_match('/\.(jpg|jpeg|png|gif|webp|svg|ico)$/i', $request->path())) {
            $response->header('Cache-Control', 'public, max-age=31536000, immutable');
            $response->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }

        // Cache fonts for 1 year
        if (preg_match('/\.(woff|woff2|ttf|eot|otf)$/i', $request->path())) {
            $response->header('Cache-Control', 'public, max-age=31536000, immutable');
            $response->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }

        // Cache CSS/JS for 1 day
        if (preg_match('/\.(css|js)$/i', $request->path())) {
            $response->header('Cache-Control', 'public, max-age=86400');
            $response->header('Expires', gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
        }

        return $response;
    }
}
