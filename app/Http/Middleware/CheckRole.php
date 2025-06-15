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
            // Si no está autenticado, redirige al login
            return redirect()->route('login');
        }

        $allowedRoles = ['admin', 'supervisor']; // Roles permitidos para acceder a admin

        // Obtén el nombre del rol en minúsculas para comparar
        $userRole = strtolower(Auth::user()->role->role_name);

        if (!in_array($userRole, $allowedRoles)) {
            // Si no es admin o supervisor, redirige fuera del admin
            return redirect('/')
                ->with('error', 'Acceso denegado. No tienes permisos para acceder al panel administrativo.');
        }

        return $next($request);
    }
}
