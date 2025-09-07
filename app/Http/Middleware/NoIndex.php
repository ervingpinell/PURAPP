<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NoIndex
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        return $response->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
