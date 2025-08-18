<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NewPasswordController extends Controller
{
    /**
     * Form reset con token.
     */
    public function create(Request $request, $token)
    {
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Guarda nueva contraseña.
     */
    public function store(Request $request)
    {
        $request->validate([
            'token'                 => ['required'],
            'email'                 => ['required', 'email'],
            'password'              => ['required', 'confirmed', 'min:8', 'regex:/[0-9]/', 'regex:/[.:!@#$%^&*()_+\-]/'],
            'password_confirmation' => ['required', 'min:8'],
        ], [
            'email.required'                 => __('adminlte::validation.required_email'),
            'email.email'                    => __('adminlte::validation.invalid_email'),
            'password.required'              => __('adminlte::validation.required_password'),
            'password.min'                   => __('adminlte::validation.password_requirements.length'),
            'password.regex'                 => __('adminlte::validation.password_requirements.special') . ' ' . __('adminlte::validation.password_requirements.number'),
            'password.confirmed'             => __('adminlte::validation.custom.password.confirmed'),
            'password_confirmation.required' => __('adminlte::validation.required_password_confirmation'),
            'password_confirmation.min'      => __('adminlte::validation.password_requirements.length'),
        ]);

        $status = Password::reset(
            $request->only('email','password','password_confirmation','token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    // Si agregaste la columna, puedes reactivar la línea de abajo:
                    // 'remember_token' => Str::random(60),
                ])->save();
            }
        );

        $messages = [
            Password::PASSWORD_RESET => __('adminlte::auth.passwords.reset'),    // e.g. "Tu contraseña ha sido restablecida."
            Password::INVALID_TOKEN  => __('adminlte::auth.passwords.token'),    // e.g. "El token de recuperación es inválido."
            Password::INVALID_USER   => __('adminlte::auth.passwords.user'),     // e.g. "No encontramos un usuario con ese correo."
        ];

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', $messages[$status] ?? __($status));
        }

        return back()->withErrors([
            'email' => $messages[$status] ?? __($status),
        ]);
    }
}
