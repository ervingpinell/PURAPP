<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureReadOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.read_only')) {
            if (in_array($request->method(), ['POST','PUT','PATCH','DELETE'], true)) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => __('Acción deshabilitada temporalmente.')], 423);
                }
                $home = route(app()->getLocale().'.home');
                return redirect($home)->with('status', __('Sitio en revisión: acciones deshabilitadas.'));
            }
        }
        return $next($request);
    }
}
