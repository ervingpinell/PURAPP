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
    public function index()
    {
        $users = User::with('role')->get(); // Carga la relación con roles
        $roles = Role::all(); // Para el <select> en el blade

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
        $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:200|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'id_role' => 'required|exists:roles,id_role',
        ]);

        User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'id_role' => $request->id_role,
            'status' => true,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario registrado correctamente.');
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
        ]);

        $user->full_name = $request->full_name;
        $user->email = $request->email;
        $user->id_role = $request->id_role;

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
