<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RolesAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar usuarios (las tablas de Spatie se limpian automáticamente por cascada)
        DB::table('users')->truncate();

        $now = Carbon::now();

        // Obtener roles de Spatie (deben existir del PermissionsSeeder)
        $superAdminRole = Role::where('name', 'super-admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $customerRole = Role::where('name', 'customer')->first();

        // Crear usuarios super admin
        $superAdmins = [
            [
                'full_name' => 'Erving Pinell',
                'email'     => 'ervingpinell@gmail.com',
                'password'  => Hash::make('-erving1234'),
                'phone'     => '+50624791471',
                'status'    => true,
                'is_super_admin' => true,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'full_name' => 'Erving Pinell Alt',
                'email'     => 'ervingpinell01@gmail.com',
                'password'  => Hash::make('-erving1234'),
                'phone'     => '+50624791471',
                'status'    => true,
                'is_super_admin' => true,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($superAdmins as $userData) {
            $user = User::create($userData);
            $user->assignRole($superAdminRole);
        }

        // Crear usuarios admin regulares
        $admins = [
            [
                'full_name' => 'Axel Paniagua',
                'email'     => 'axelpaniaguab54@gmail.com',
                'password'  => Hash::make('-12345678.'),
                'phone'     => '+50672612748',
                'status'    => true,
                'is_super_admin' => false,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'full_name' => 'Admin',
                'email'     => 'info@greenvacationscr.com',
                'password'  => Hash::make('Green1974*'),
                'phone'     => '+50624791471',
                'status'    => true,
                'is_super_admin' => false,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($admins as $userData) {
            $user = User::create($userData);
            $user->assignRole($adminRole);
        }

        // Crear un usuario customer de ejemplo
        $customer = User::create([
            'full_name' => 'Cliente Demo',
            'email'     => 'cliente@example.com',
            'password'  => Hash::make('password123'),
            'phone'     => '+50612345678',
            'status'    => true,
            'is_super_admin' => false,
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $customer->assignRole($customerRole);
    }
}
