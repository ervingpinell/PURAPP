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
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->filled('estado')) {
            $query->where('status', $request->estado);
        }




        $users = $query->get();

        return view('admin.users.users', compact('users', 'roles'));
    }

    // No se usa create (modal en el blade)
    public function create()
    {
        $roles = Role::all(); // opcional si querés que el usuario elija un rol, o si vas a asignarlo por defecto
        return view('auth.register', compact('roles'));
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

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario registrado correctamente.')
                ->with('alert_type', 'creado');
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
            'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*(),.?":{}|<>_\-+=]/',
                'confirmed',
            ],

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

        return redirect()->route('admin.users.index')
        ->with('success', 'Usuario actualizado correctamente.')
        ->with('alert_type', 'actualizado');

        }

    // Desactivar usuario
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->status = !$user->status; // alterna estado
        $user->save();

        $mensaje = $user->status
            ? ['tipo' => 'activado', 'texto' => 'Usuario reactivado correctamente.']
            : ['tipo' => 'desactivado', 'texto' => 'Usuario desactivado correctamente.'];

        return redirect()->route('admin.users.index')
            ->with('alert_type', $mensaje['tipo'])
            ->with('success', $mensaje['texto']);

            }

}
