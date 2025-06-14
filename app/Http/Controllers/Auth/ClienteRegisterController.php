<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ClienteRegisterController extends Controller
{
public function create(Request $request)
{

    $locale = session('locale', config('app.locale'));
    app()->setLocale($locale);

    return view('adminlte::auth.register');
}

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => [
            'required',
            'string',
            'min:8',
            'regex:/[0-9]/',
            'regex:/[.:!@#$%^&*()_+\-]/',
            'confirmed',
        ],
        ]);


        User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => 3, 
            'status' => true,
        ]);


   return redirect()->route('login')->with('success', __('adminlte::adminlte.account_created'));
    }
}

