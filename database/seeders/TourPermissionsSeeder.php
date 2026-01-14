<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TourPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos si no existen
        $permissions = [
            'restore-tours',
            'force-delete-tours',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Asignar permisos a Admin y Super Admin
        $adminRole = Role::where('name', 'admin')->first();
        $superAdminRole = Role::where('name', 'super-admin')->first();

        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
            $this->command->info('✅ Permisos asignados al rol Admin');
        }

        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
            $this->command->info('✅ Permisos asignados al rol Super Admin');
        }

        $this->command->info('✅ Seeder de permisos de Tours completado');
    }
}
