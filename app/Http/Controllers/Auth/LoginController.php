<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $locale = session('locale', config('app.locale'));
        app()->setLocale($locale);
        return view('adminlte::auth.login');
    }

    public function login(Request $request)
    {
        // Validación 100% backend con mensajes en adminlte::validation
        $credentials = $request->validate(
            [
                'email'    => ['required', 'email'],
                'password' => ['required'],
            ],
            [
                'email.required'    => __('adminlte::validation.required_email'),
                'email.email'       => __('adminlte::validation.invalid_email'),
                'password.required' => __('adminlte::validation.required_password'),
            ],
            [
                'email'    => __('adminlte::validation.attributes.email'),
                'password' => __('adminlte::validation.attributes.password'),
            ]
        );

        // Guardar/Restaurar locale en caso de regeneración de sesión
        $previousLocale = session('locale', config('app.locale'));

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            session(['locale' => $previousLocale]);
            App::setLocale($previousLocale);

            return $this->authenticated($request, Auth::user());
        }

        return back()->withErrors([
            'email' => __('adminlte::validation.invalid_credentials'),
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    protected function authenticated(Request $request, $user)
    {
        if (in_array($user->role_id, [1, 2])) {
            return redirect()->route('admin.home'); // Admin o Colaborador
        }
        return redirect()->route('home'); // Cliente
    }
}
