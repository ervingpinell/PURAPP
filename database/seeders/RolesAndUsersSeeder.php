<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Seed roles and a default super admin user.
     * 
     * Client-specific users should be created during deployment.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Get Spatie roles
        $superAdminRole = Role::where('name', 'super-admin')->first();

        if (!$superAdminRole) {
            $this->command->warn('⚠ super-admin role not found. Run PermissionsSeeder first.');
            return;
        }

        // Common address data (to be customized during deployment)
        $commonAddress = [
            'address' => 'To be configured',
            'city'    => 'To be configured',
            'state'   => 'To be configured',
            'zip'     => '00000',
            'country' => 'CR',
            'status'  => true,
            'email_verified_at' => $now,
        ];

        // Create default super admin (to be customized during deployment)
        $admin = User::updateOrCreate(
            ['email' => 'erving@purappcr.com'],
            array_merge([
                'first_name' => 'Erving',
                'last_name'  => 'Pinell',
                'password'   => Hash::make('erving1234'),
                'phone'      => '+50600000000',
                'is_super_admin' => true,
            ], $commonAddress)
        );
        $admin->assignRole($superAdminRole);

        $this->command->info('✓ Super admin created: erving@purappcr.com');
    }
}
