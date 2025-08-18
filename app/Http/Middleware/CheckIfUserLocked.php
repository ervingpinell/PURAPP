<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckIfUserLocked
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_locked) {
            Auth::logout();

            return redirect()->route('account.locked')
                ->withErrors([
                    'email' => __('Tu cuenta está bloqueada. Revisa tu correo para desbloquearla o contacta soporte.')
                ]);
        }

        return $next($request);
    }
}
