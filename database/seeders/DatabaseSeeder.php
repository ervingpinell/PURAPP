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
        $this->call([
            PermissionsSeeder::class,
            RolesAndUsersSeeder::class,
            TaxSeeder::class,
            SettingsSeeder::class,
            PayLaterSettingsSeeder::class,
            InitialSetupSeeder::class,
            ToursSeeder::class,
            ItinerariesSeeder::class,
            FaqSeeder::class,
            ReviewProviderSeeder::class,
            // AssignViatorCodesSeeder::class, // Commented out - viator_code field was removed
            PoliciesSeeder::class,
            TranslationSeeder::class,
            MeetingPointsSeeder::class,
            EmailTemplateSeeder::class,
        ]);
    }
}
