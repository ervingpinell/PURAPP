<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // Guarda info útil para la UI (opcional)
        if ($request->user()) {
            session(['registered_email' => $request->user()->email]);
            session()->flash('just_registered', true);
        }

        // Lleva al aviso de verificación
        return redirect()->route('verification.notice');
        // Si prefieres tu página de gracias:
        // return redirect()->route('register.thanks');
    }
}
