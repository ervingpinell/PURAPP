<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // Guardamos el correo para mostrarlo en la vista de "verifica tu email"
        $email = optional($request->user())->email;

        if ($email) {
            session([
                'registered_email' => $email,
            ]);
            session()->flash('just_registered', true);
        }

        // IMPORTANTE: cerrar sesión inmediatamente después del registro
        if ($request->user()) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Redirigir a la vista pública de verificación
        return redirect()->route('verification.notice');
        // (ya tienes esa ruta como guest y mostrando auth.verify-email)
    }
}
