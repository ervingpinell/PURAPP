<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserRegisterController extends Controller
{
    // Mostrar lista de usuarios
    public function index(Request $request)
    {
        $roles = Role::all();
        $query = User::with('role');

        if ($request->filled('rol')) {
            $query->where('id_role', $request->rol);
        }

        $users = $query->get();

        return view('admin.users.users', compact('users', 'roles'));
    }

    // No se usa create (modal en el blade)
    public function create()
    {
        return redirect()->route('admin.users.index');
    }

    // Guardar nuevo usuario
    public function store(Request $request)
    {
        try {
            $request->validate([
                'full_name' => 'required|string|max:100',
                'email' => 'required|string|email|max:200|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'id_role' => 'required|exists:roles,id_role',
                'phone' => 'nullable|string|max:20',
            ]);

            User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'id_role' => $request->id_role,
                'status' => true,
                'phone'=>$request->phone,
            ]);

            return redirect()->route('admin.users.index')->with('success', 'Usuario registrado correctamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Error solo de contraseña
            if (
                $e->validator->errors()->count() === 1 &&
                $e->validator->errors()->has('password')
            ) {
                return redirect()->back()
                    ->withErrors($e->validator)
                    ->with('error_password', $e->validator->errors()->first('password'))
                    ->withInput()
                    ->with('show_register_modal', true); // para mantener el modal abierto
            }

            throw $e;
        }
    }



    
    public function edit($id)
    {
        return redirect()->route('admin.users.index');
    }

    // Actualizar usuario
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:200|unique:users,email,' . $id . ',id_user',
            'password' => 'nullable|string|min:6|confirmed',
            'id_role' => 'required|exists:roles,id_role',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->full_name = $request->full_name;
        $user->email = $request->email;
        $user->id_role = $request->id_role;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    // Eliminar usuario
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
