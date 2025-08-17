<?php

namespace App\Http\Controllers\Admin\Users;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
public function index(Request $request)
{
    $q      = trim((string) $request->get('q', ''));
    $sort   = $request->get('sort', 'name');     // id | name
    $dir    = $request->get('dir', 'asc');       // asc | desc
    $status = $request->get('status', 'all');    // all | active | inactive

    $rolesQ = Role::query();

    if ($q !== '') {
        $rolesQ->where('role_name', 'like', "%{$q}%");
    }

    // Filtro por estado
    if ($status === 'active') {
        $rolesQ->where('is_active', true);
    } elseif ($status === 'inactive') {
        $rolesQ->where('is_active', false);
    }

    // 👇 Orden principal: activos primero (is_active DESC)
    $rolesQ->orderBy('is_active', 'desc');

    // Orden secundario: por id o nombre
    if ($sort === 'id') {
        $rolesQ->orderBy('role_id', $dir);
    } else { // name
        $rolesQ->orderBy('role_name', $dir);
    }

    $roles = $rolesQ->get();

    return view('admin.roles.index', compact('roles', 'q', 'sort', 'dir', 'status'));
}


    /**
     * Formulario de edición.
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Crear nuevo rol.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_name'   => 'required|string|max:50|unique:roles,role_name',
            'description' => 'nullable|string',
        ]);

        Role::create($validated);

        return redirect()->back()->with('success', 'Rol creado correctamente.');
    }

    /**
     * Actualizar rol existente.
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'role_name'   => 'required|string|max:50|unique:roles,role_name,' . $role->role_id . ',role_id',
            'description' => 'nullable|string',
        ]);

        $role->update($validated);

        return redirect()->route('admin.roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Activar/Desactivar (toggle) un rol.
     */
    public function toggle($id)
    {
        $role = Role::findOrFail($id);
        $role->is_active = ! $role->is_active;
        $role->save();

        $msg = $role->is_active ? 'Rol activado correctamente.' : 'Rol desactivado correctamente.';
        return redirect()->back()->with('success', $msg);
    }

    /**
     * Eliminar (DELETE) un rol.
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        try {
            $role->delete();
            return redirect()->route('admin.roles.index')
                ->with('success', 'Rol eliminado correctamente.');
        } catch (QueryException $e) {
            // Error típico de restricción de clave foránea: SQLSTATE 23000
            if ($e->getCode() === '23000') {
                return redirect()->back()->with('error',
                    'No se puede eliminar el rol porque está relacionado con usuarios o permisos. ' .
                    'Desasócielo primero y vuelva a intentarlo.'
                );
            }

            // Otros errores
            return redirect()->back()->with('error', 'No se pudo eliminar el rol. Detalle: ' . $e->getMessage());
        }
    }
}
