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
        $validated = $request->validate(
            [
                'full_name' => ['bail','required','string','max:100'],
                'email'     => ['bail','required','email:filter','max:255','unique:users,email'],
                'phone'     => ['nullable','string','max:20'],
                'password'  => [
                    'bail','required','string','min:8',
                    'regex:/[0-9]/',
                    'regex:/[.:!@#$%^&*()_+\-]/',
                    'confirmed',
                ],
            ],
            // 👇 Mensajes explícitos (evitan ver "validation.unique")
            [
                'full_name.required' => __('adminlte::validation.required_full_name'),
                'email.required'     => __('adminlte::validation.required_email'),
                'email.email'        => __('adminlte::validation.invalid_email'),
                'email.unique'       => __('adminlte::validation.email_already_taken'),
                'password.required'  => __('adminlte::validation.required_password'),
                'password.min'       => __('adminlte::validation.password_requirements.length'),
                'password.regex'     => __('adminlte::validation.password_requirements.special'), // se complementa con el JS
                'password.confirmed' => __('adminlte::validation.custom.password.confirmed'),
                'password_confirmation.required' => __('adminlte::validation.required_password_confirmation'),
            ],
            // 👇 Nombres legibles (por si falta en validation.php)
            [
                'full_name' => __('adminlte::adminlte.full_name'),
                'email'     => __('adminlte::adminlte.email'),
                'phone'     => __('adminlte::adminlte.phone'),
                'password'  => __('adminlte::adminlte.password'),
                'password_confirmation' => __('adminlte::adminlte.retype_password'),
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

        return redirect()->route('login')->with('success', __('adminlte::adminlte.account_created'));
    }
}
