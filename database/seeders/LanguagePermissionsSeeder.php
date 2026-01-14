<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class LanguagePermissionsSeeder extends Seeder
{
    public function run()
    {
        // 1. Create permissions
        $permissions = [
            'restore-tour-languages',
            'force-delete-tour-languages',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Assign to Admin role
        $role = Role::firstOrCreate(['name' => 'admin']);
        $role->givePermissionTo($permissions);

        $this->command->info('Language permissions created and assigned to admin.');
    }
}
