<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Muestra la vista de edición del perfil, según el rol del usuario.
     */
    public function edit()
    {
        $user = Auth::user();

        return in_array($user->role_id, [1, 2])
            ? view('admin.profile.profile', compact('user'))
            : view('profile.profile', compact('user'));
    }

    /**
     * Actualiza los datos del perfil del usuario autenticado.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
        ];

        // Validaciones específicas según rol
        if (in_array($user->role_id, [1, 2])) {
            $rules['password'] = 'nullable|string|min:6|confirmed';
        } else {
            $rules['phone'] = 'nullable|string|max:20';
            $rules['password'] = [
                'nullable',
                'string',
                'min:8',
                'regex:/[0-9]/', // al menos un número
                'regex:/[.:!@#$%^&*()_+\-]/', // al menos un carácter especial
                'confirmed',
            ];
        }

        $validated = $request->validate($rules);

        // Actualiza los campos permitidos
        $user->full_name = $validated['full_name'];
        $user->email = $validated['email'];

        if (!in_array($user->role_id, [1, 2]) && isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
    }
}
