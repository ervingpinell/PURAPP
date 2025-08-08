<?php

namespace App\Http\Controllers\Admin\Users;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    /**
     * Mostrar todos los roles.
     */
    public function index()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Mostrar el formulario de edición de un rol.
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Guardar un nuevo rol.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:50|unique:roles,role_name',
            'description' => 'nullable|string',
        ]);

        Role::create($validated);

        return redirect()->back()->with('success', 'Rol creado correctamente.');
    }

    /**
     * Actualizar un rol existente.
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'role_name' => 'required|string|max:50|unique:roles,role_name,' . $role->role_id . ',role_id',
            'description' => 'nullable|string',
        ]);

        $role->update($validated);

        return redirect()->route('admin.roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Activar o desactivar un rol (toggle).
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        $role->is_active = !$role->is_active;
        $role->save();

        $message = $role->is_active
            ? 'Rol activado correctamente.'
            : 'Rol desactivado correctamente.';

        return redirect()->back()->with('success', $message);
    }
}
