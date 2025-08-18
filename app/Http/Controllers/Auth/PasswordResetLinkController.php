<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Formulario para solicitar el enlace de reseteo.
     */
    public function create()
    {
        return view('auth.passwords.email');
    }

    /**
     * Envía el enlace de reseteo al correo.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => __('adminlte::validation.required_email'),
            'email.email'    => __('adminlte::validation.invalid_email'),
        ]);

        $status = Password::sendResetLink($request->only('email'));

        // Mapeo a tus traducciones de adminlte
        $messages = [
            Password::RESET_LINK_SENT => __('adminlte::auth.passwords.link_sent'),     // e.g. "Hemos enviado un enlace de recuperación a tu correo."
            Password::RESET_THROTTLED => __('adminlte::auth.passwords.throttled'),     // e.g. "Por favor, espera antes de intentar nuevamente."
            Password::INVALID_USER    => __('adminlte::auth.passwords.user'),          // e.g. "No encontramos un usuario con ese correo."
        ];

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', $messages[$status] ?? __($status));
        }

        // Para cualquier otro status, devolvemos error en 'email'
        return back()->withErrors([
            'email' => $messages[$status] ?? __($status),
        ]);
    }
}
