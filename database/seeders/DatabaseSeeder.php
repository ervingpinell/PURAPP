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
            RolesAndUsersSeeder::class,
            InitialSetupSeeder::class,
            ToursSeeder::class,
            ItinerariesSeeder::class,
            FaqSeeder::class,
            AssignViatorCodesSeeder::class,
            PoliciesSeeder::class,
            TranslationSeeder::class,
        ]);
    }
}
