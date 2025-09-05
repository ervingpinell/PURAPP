<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireTwoFactorForAdmins
{
    public function handle(Request $request, Closure $next)
    {
        $u = $request->user();

        if ($u && in_array((int)$u->role_id, [1,2], true)) {
            // Deja pasar perfil admin para poder activar 2FA
            if (empty($u->two_factor_secret) && !$request->routeIs('admin.profile.*')) {
                return redirect()->route('admin.profile.edit')
                    ->with('error', __('adminlte::adminlte.enable_2fa_to_continue'));
            }
        }

        return $next($request);
    }
}
