<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireTwoFactorForAdmins
{
    public function handle(Request $request, Closure $next)
    {
        $u = $request->user();

        // Verificar si el usuario tiene permiso de acceso al admin (incluye supervisor)
        if ($u && $u->can('access-admin')) {
            // Deja pasar perfil admin para poder activar 2FA
            if (empty($u->two_factor_secret) && !$request->routeIs('admin.profile.*')) {
                return redirect()->route('admin.profile.edit')
                    ->with('error', __('adminlte::adminlte.enable_2fa_to_continue'));
            }
        }

        return $next($request);
    }
}
