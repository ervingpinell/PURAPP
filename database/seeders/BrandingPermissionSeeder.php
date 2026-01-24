<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BrandingPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the 'manage branding' permission
        $permission = Permission::firstOrCreate(
            ['name' => 'manage branding'],
            ['guard_name' => 'web']
        );

        // Assign permission to super-admin role
        $superAdminRole = Role::where('name', 'super-admin')->first();
        
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permission);
            $this->command->info('✓ Permission "manage branding" assigned to super-admin role');
        } else {
            $this->command->warn('⚠ super-admin role not found. Please create it first.');
        }
    }
}
