<?php

namespace Database\Seeders;

use App\Models\MeetingPoint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class MeetingPointsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $now = now();

        $meetingPoints = [
            [
                'name' => 'Iglesia Católica de La Fortuna',
                'description' => 'Parque Central de La Fortuna, Alajuela, Costa Rica',
                'map_url' => 'https://maps.app.goo.gl/aa9M4RE4s5cvFzr27',
                'pickup_time' => 'TBD',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Oficina de Green Vacations',
                'description' => 'La Fortuna, Alajuela, Costa Rica',
                'map_url' => 'https://maps.app.goo.gl/pvAgqnc5LwnCNAoD6',
                'pickup_time' => 'TBD',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Arenal 1968',
                'description' => 'Arenal 1968, La Fortuna, Alajuela, Costa Rica',
                'map_url' => 'https://maps.app.goo.gl/QjpTBU2JvD6fmsMY9',
                'pickup_time' => 'TBD',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($meetingPoints as $pointData) {
            MeetingPoint::create($pointData);
        }

        $this->command->info('✅ Meeting points seeded successfully');
    }
}
