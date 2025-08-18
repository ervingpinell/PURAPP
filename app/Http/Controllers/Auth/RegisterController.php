<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Models\User;

class RegisterController extends Controller
{
    /**
     * Muestra el formulario de registro
     */
    public function create(Request $request)
    {
        $locale = session('locale', config('app.locale'));
        app()->setLocale($locale);

        if ($request->has('redirect')) {
            session(['after_login_redirect' => $request->get('redirect')]);
        }

        return view('adminlte::auth.register');
    }

    /**
     * Guarda el usuario y envía correo de verificación
     */
    public function store(Request $request)
    {
        // Normaliza email
        $request->merge([
            'email' => $request->email ? mb_strtolower(trim($request->email)) : $request->email,
        ]);

        $validated = $request->validate(
            [
                'full_name'             => ['bail','required','string','max:100'],
                'email'                 => ['bail','required','string','email:rfc,dns,filter','max:255','unique:users,email'],
                'password'              => ['bail','required','string','min:8','regex:/[0-9]/','regex:/[.:!@#$%^&*()_+\-]/','confirmed'],
                'password_confirmation' => ['required','string','min:8'],
            ],
            [
                // full_name
                'full_name.required' => __('adminlte::validation.required_full_name'),

                // email
                'email.required' => __('adminlte::validation.required_email'),
                'email.email'    => __('adminlte::validation.invalid_email'),
                'email.unique'   => __('adminlte::validation.email_already_taken'),

                // password
                'password.required'  => __('adminlte::validation.required_password'),
                'password.min'       => __('adminlte::validation.password_requirements.length'),
                'password.regex'     => __('adminlte::validation.password_requirements.special') . ' ' . __('adminlte::validation.password_requirements.number'),
                'password.confirmed' => __('adminlte::validation.custom.password.confirmed'),

                // password confirmation
                'password_confirmation.required' => __('adminlte::validation.required_password_confirmation'),
                'password_confirmation.min'      => __('adminlte::validation.password_requirements.length'),
            ],
            [
                'full_name'             => __('adminlte::validation.attributes.full_name'),
                'email'                 => __('adminlte::validation.attributes.email'),
                'password'              => __('adminlte::validation.attributes.password'),
                'password_confirmation' => __('adminlte::validation.attributes.password_confirmation'),
            ]
        );

        // Crear usuario (NO iniciar sesión)
        $user = User::create([
            'full_name' => $validated['full_name'],
            'email'     => $validated['email'],
            'phone'     => $request->input('phone') ?: null,
            'password'  => Hash::make($validated['password']),
            'role_id'   => 3,     // Rol por defecto
            'status'    => true,  // Activo
        ]);

        // Dispara el evento de registro (envía email de verificación)
        event(new Registered($user));

        // Guardamos el correo en sesión (opcional: enmascarado)
        session()->flash('registered_email', $validated['email']);

        // Redirigimos a la página pública de agradecimiento
        return redirect()
            ->route('register.thanks')
            ->with('status', __('adminlte::adminlte.verify_email_sent'));
    }
}
