<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Essential system seeders only
        // Client-specific data should be added during deployment/branding setup
        $this->call([
            PermissionsSeeder::class,
            RolesAndUsersSeeder::class,
            SettingsSeeder::class,
            TaxSeeder::class,
            EmailTemplateSeeder::class,
            BrandingPermissionSeeder::class,
            PoliciesSeederTranslateWipe::class,
            TransportServicesSeeder::class,
        ]);
    }
}
