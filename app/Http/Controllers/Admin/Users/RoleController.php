<?php

namespace App\Http\Controllers\Admin\Users;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-roles'])->only(['index', 'show']);
        $this->middleware(['can:create-roles'])->only(['create', 'store']);
        $this->middleware(['can:edit-roles'])->only(['edit', 'update']);
        $this->middleware(['can:publish-roles'])->only(['toggle']);
        $this->middleware(['can:delete-roles'])->only(['destroy']);
        $this->middleware(['can:assign-roles'])->only(['assign']);
    }
    public function index(Request $request)
    {
        $q      = trim((string) $request->get('q', ''));
        $sort   = $request->get('sort', 'name');
        $dir    = $request->get('dir', 'asc');
        $status = $request->get('status', 'all');

        $rolesQ = Role::query(); // Uses App\Models\Role which extends Spatie

        // Ocultar rol super-admin si el usuario no es super admin
        if (!auth()->user()->hasRole('super-admin')) {
            $rolesQ->where('name', '!=', 'super-admin');
        }

        if ($q !== '') {
            $rolesQ->where('name', 'like', "%{$q}%");
        }

        if ($status === 'active') {
            $rolesQ->where('is_active', true);
        } elseif ($status === 'inactive') {
            $rolesQ->where('is_active', false);
        }

        if ($sort === 'id') {
            $rolesQ->orderBy('id', $dir);
        } else {
            $rolesQ->orderBy('name', $dir);
        }

        $roles = $rolesQ->withCount('permissions')->get();

        return view('admin.roles.index', compact('roles', 'q', 'sort', 'dir', 'status'));
    }



    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('admin.roles.edit', compact('role'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_name'   => 'required|string|max:50|unique:roles,name',
            'description' => 'nullable|string',
        ]);

        Role::create([
            'name' => $validated['role_name'],
            'description' => $validated['description'],
            'guard_name' => 'web',
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Rol creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'role_name'   => 'required|string|max:50|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
        ]);

        $role->update([
            'name' => $validated['role_name'],
            'description' => $validated['description'],
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function toggle($id)
    {
        $role = Role::findOrFail($id);
        $role->is_active = ! $role->is_active;
        $role->save();

        $msg = $role->is_active ? 'Rol activado correctamente.' : 'Rol desactivado correctamente.';
        return redirect()->back()->with('success', $msg);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        try {
            $role->delete();
            return redirect()->route('admin.roles.index')
                ->with('success', 'Rol eliminado correctamente.');
        } catch (QueryException $e) {

            if ($e->getCode() === '23000') {
                return redirect()->back()->with(
                    'error',
                    'No se puede eliminar el rol porque está relacionado con usuarios o permisos. ' .
                        'Desasócielo primero y vuelva a intentarlo.'
                );
            }

            return redirect()->back()->with('error', 'No se pudo eliminar el rol. Detalle: ' . $e->getMessage());
        }
    }
    public function permissions($id)
    {
        $role = Role::findOrFail($id);

        $permissions = \Spatie\Permission\Models\Permission::all();

        // Agrupar por módulo
        $permissionGroups = $permissions->groupBy(function ($perm) {
            // Ejemplo: view-users -> [view, users]
            // Ejemplo: view-review-providers -> [view, review, providers]
            $parts = explode('-', $perm->name);

            // Si tiene solo una parte (ej: access-admin, bueno, ese tiene 2)
            // Asumimos que el primer segmento es la accion y el resto es el modulo
            if (count($parts) < 2) return 'general';

            $action = $parts[0];
            $module = implode('_', array_slice($parts, 1)); // usamos guion bajo para claves de traducción

            // Caso especial para access-admin
            if ($module === 'admin') return 'admin';

            return $module;
        })->sortBy(function ($permissions, $key) {
            return trans("permissions.modules.{$key}");
        });

        return view('admin.roles.permissions', compact('role', 'permissionGroups'));
    }

    public function updatePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $user = $request->user();

        // Solo super-admin puede modificar cualquier rol
        if (!$user->hasRole('super-admin')) {
            // Verificar si el usuario tiene este rol
            if ($user->hasRole($role->name)) {
                return redirect()->back()->with('error', 'No puedes modificar los permisos de tu propio rol.');
            }

            // Verificar si está intentando dar permisos que él no tiene
            $requestedPermissions = $request->get('permissions', []);
            foreach ($requestedPermissions as $permissionName) {
                if (!$user->can($permissionName)) {
                    return redirect()->back()->with('error', 'No puedes asignar permisos que tú no tienes.');
                }
            }
        }

        // Sincronizamos los permisos enviados desde el formulario
        // Si no se envía ninguno, se quitan todos (sync con array vacío)
        $role->syncPermissions($request->get('permissions', []));

        return redirect()->route('admin.roles.index')
            ->with('success', 'Permisos actualizados correctamente para el rol ' . $role->name);
    }
}
