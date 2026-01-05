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
        // NO truncar la tabla para no perder usuarios existentes
        // DB::table('users')->truncate(); 

        $now = Carbon::now();

        // Obtener roles de Spatie
        $superAdminRole = Role::where('name', 'super-admin')->first();
        $adminRole = Role::where('name', 'admin')->first();

        // Eliminar usuarios específicos solicitados (Axel y Demo)
        User::whereIn('email', ['axelpaniaguab54@gmail.com', 'cliente@example.com'])->delete();

        // Datos comunes de dirección
        $commonAddress = [
            'address' => 'La Fortuna',
            'city'    => 'La Fortuna',
            'state'   => 'Alajuela',
            'zip'     => '21007',
            'country' => 'CR',
            'status'  => true,
            'email_verified_at' => $now,
            // No sobreescribimos created_at para mantener historial si existen
        ];

        // 1. Erving Pinell (Super Admin)
        $erving = User::updateOrCreate(
            ['email' => 'ervingpinell@gmail.com'],
            array_merge([
                'full_name' => 'Erving Pinell',
                'password'  => Hash::make('-erving1234'), // Se actualizará la contraseña si se corre el seeder
                'phone'     => '+50624791471',
                'is_super_admin' => true,
            ], $commonAddress)
        );
        $erving->assignRole($superAdminRole);

        // 2. Erving Pinell Alt (Super Admin)
        $ervingAlt = User::updateOrCreate(
            ['email' => 'ervingpinell01@gmail.com'],
            array_merge([
                'full_name' => 'Erving Pinell Alt',
                'password'  => Hash::make('-erving1234'),
                'phone'     => '+50624791471',
                'is_super_admin' => true,
            ], $commonAddress)
        );
        $ervingAlt->assignRole($superAdminRole);

        // 3. Green Vacations (Admin)
        $green = User::updateOrCreate(
            ['email' => 'info@greenvacationscr.com'],
            array_merge([
                'full_name' => 'Green Vacations',
                'password'  => Hash::make('Green1974*'),
                'phone'     => '+50624791471',
                'is_super_admin' => false,
            ], $commonAddress)
        );
        $green->assignRole($adminRole);

        // Si anteriormente este usuario se llamaba 'Admin', el updateOrCreate por email actualizó su nombre a 'Green Vacations' exitosamente.
    }
}
