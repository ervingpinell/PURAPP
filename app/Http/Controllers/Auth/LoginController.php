<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
public function showLoginForm(Request $request)
{
    // 🔁 Asegura que el idioma de la sesión se aplique correctamente en la vista
    $locale = session('locale', config('app.locale'));
    app()->setLocale($locale); // Forzar aplicación inmediata del idioma

    return view('adminlte::auth.login');
}

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // ✅ Captura el idioma actual antes de regenerar la sesión
        $previousLocale = session('locale', config('app.locale'));

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // ✅ Restaura el idioma después de regenerar la sesión
            session(['locale' => $previousLocale]);
            App::setLocale($previousLocale);

            return $this->authenticated($request, Auth::user());
        }

        return back()->withErrors([
            'email' => 'Las credenciales no son correctas.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // 🔐 Método que redirige según el rol
    public function authenticated(Request $request, $user)
    {
        if ($user->role_id == 1 || $user->role_id == 2) {
            return redirect()->route('admin.home'); // Admin o Colaborador
        }

        return redirect()->route('home'); // Cliente
    }
}
