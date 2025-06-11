<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('adminlte::auth.login');
    }

    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // Check if user exists and password matches
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials are incorrect.',
            ])->onlyInput('email');
        }

        // Check if user is inactive
        if (!$user->status) {
            return back()->withErrors([
                'email' => 'Your account is inactive. Please contact the administrator.',
            ])->onlyInput('email');
        }

        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        return $this->authenticated($request, $user);
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
        if ($user->id_role == 1 || $user->id_role == 2) {
            return redirect()->route('admin.home'); // Admin o Colaborador
        }

        return redirect()->route('home'); // Cliente
    }
}
