<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TourTypePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define soft delete permissions for tour types
        $permissions = [
            'restore-tour-types',
            'force-delete-tour-types',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to Admin and Super Admin roles
        $roles = ['admin', 'super-admin'];

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->givePermissionTo($permissions);
        }

        $this->command->info('Tour Types soft delete permissions seeded successfully.');
    }
}
