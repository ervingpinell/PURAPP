<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ClienteRegisterController extends Controller
{
    public function create()
    {
        return view('auth.register'); // usa la vista simple para clientes
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);


        User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'id_role' => 3, 
            'status' => true,
        ]);


        return redirect()->route('login')->with('success', 'Cuenta creada correctamente. Inicia sesión.');
    }
}

