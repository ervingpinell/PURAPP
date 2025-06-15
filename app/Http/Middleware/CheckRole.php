<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Solo los usuarios con role_id 1 o 2 pueden acceder
        $allowedRoleIds = [1, 2];

        if (!in_array(Auth::user()->role_id, $allowedRoleIds)) {
            return redirect('/')
                ->with('error', 'Acceso denegado. No tienes permisos para acceder al panel administrativo.');
        }

        return $next($request);
    }
}
