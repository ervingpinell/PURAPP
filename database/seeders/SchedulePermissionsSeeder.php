<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SchedulePermissionsSeeder extends Seeder
{
    public function run()
    {
        // 1. Crear Permisos de borrado lógico
        $permissions = [
            'restore-schedules',
            'force-delete-schedules',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 2. Asignar a Roles (admin, super-admin)
        $roles = ['admin', 'super-admin'];

        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo($permissions);
            }
        }
    }
}
