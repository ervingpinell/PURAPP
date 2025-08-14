<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
        $locale = session('locale', config('app.locale'));
        app()->setLocale($locale);

        return view('adminlte::auth.register');
    }

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
                'phone'                 => ['nullable','string','max:20'],
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
                // Combina ambos requisitos en un solo mensaje (número + especial)
                'password.regex'     => __('adminlte::validation.password_requirements.special').' '.__('adminlte::validation.password_requirements.number'),
                // 👇 mismatch (NO COINCIDEN)
                'password.confirmed' => __('adminlte::validation.custom.password.confirmed'),

                // password confirmation
                'password_confirmation.required' => __('adminlte::validation.required_password_confirmation'),
                'password_confirmation.min'      => __('adminlte::validation.password_requirements.length'),
            ],
            // Atributos legibles (fallback)
            [
                'full_name'             => __('adminlte::validation.attributes.full_name'),
                'email'                 => __('adminlte::validation.attributes.email'),
                'phone'                 => __('adminlte::validation.attributes.phone'),
                'password'              => __('adminlte::validation.attributes.password'),
                'password_confirmation' => __('adminlte::validation.attributes.password_confirmation'),
            ]
        );

        User::create([
            'full_name' => $validated['full_name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'] ?? null,
            'password'  => Hash::make($validated['password']),
            'role_id'   => 3,
            'status'    => true,
        ]);

        return redirect()->route('login')
            ->with('success', __('adminlte::adminlte.account_created'));
    }
}
