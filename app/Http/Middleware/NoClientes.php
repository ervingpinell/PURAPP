<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class NoClientes
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role->role_name === 'Cliente') {
            return redirect('/')
                ->with('error', 'Acceso denegado. No tienes permisos para acceder al panel administrativo.');
        }

        return $next($request);
    }
}
