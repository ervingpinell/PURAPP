<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ItinerariesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // === Nature Lover Combo 1 ===
        $natureId = DB::table('itineraries')->insertGetId([
            'name' => 'Nature Lover Combo 1',
            'description' => 'Begin your full-day Costa Rican adventure with pickup from your accommodation in Fortuna Town. Then head to Arenal Volcano Park for a 2.5-hour hike traversing flat grounds and rocky terrain to witness the remnants of the 1968 eruption and learn about the volcano\'s history.',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], 'itinerary_id');

        foreach (['Arenal 1968 Volcano View and Lava Trails', 'La Fortuna Waterfall', 'Soda Fortuna', 'Mistico Park'] as $index => $title) {
            $itemId = DB::table('itinerary_items')->where('title', $title)->value('item_id');
            if ($itemId) {
                DB::table('itinerary_item_itinerary')->insert([
                    'itinerary_id' => $natureId,
                    'itinerary_item_id' => $itemId,
                    'item_order' => $index + 1,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }
        DB::table('tours')
            ->where('name', 'Nature Lover Combo 1 (Hanging Bridges + La Fortuna Waterfall + Lunch + Volcano Hike)')
            ->update(['itinerary_id' => $natureId]);

        // === Minicombo 1 ===
        $mini1 = DB::table('itineraries')->insertGetId([
            'name' => 'Minicombo 1',
            'description' => 'Begin your semi full-day Costa Rican adventure with pickup from your accommodation in Fortuna Town. Then head to Mistico Park for a 2-hour walk around the rainforest treetops.',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], 'itinerary_id');

        foreach (['Mistico Park', 'La Fortuna Waterfall', 'Soda Fortuna'] as $index => $title) {
            $itemId = DB::table('itinerary_items')->where('title', $title)->value('item_id');
            DB::table('itinerary_item_itinerary')->insert([
                'itinerary_id' => $mini1,
                'itinerary_item_id' => $itemId,
                'item_order' => $index + 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
        DB::table('tours')->where('name', 'Minicombo 1 (Hanging Bridges + La Fortuna Waterfall + Lunch)')->update(['itinerary_id' => $mini1]);

        // === Minicombo 2 ===
        $mini2 = DB::table('itineraries')->insertGetId([
            'name' => 'Minicombo 2',
            'description' => 'Begin your semi full-day Costa Rican adventure with pickup from your accommodation in Fortuna Town. Then head to Arenal 1968 Volcano View and Lava Trails for a 2-hour walk.',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], 'itinerary_id');

        foreach (['Arenal 1968 Volcano View and Lava Trails', 'La Fortuna Waterfall', 'Soda Fortuna'] as $index => $title) {
            $itemId = DB::table('itinerary_items')->where('title', $title)->value('item_id');
            DB::table('itinerary_item_itinerary')->insert([
                'itinerary_id' => $mini2,
                'itinerary_item_id' => $itemId,
                'item_order' => $index + 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
        DB::table('tours')->where('name', 'Minicombo 2 (Volcano Hike + La Fortuna Waterfall + Lunch)')->update(['itinerary_id' => $mini2]);

        // === Minicombo 3 ===
        $mini3 = DB::table('itineraries')->insertGetId([
            'name' => 'Minicombo 3',
            'description' => 'Begin your semi full-day Costa Rican adventure with pickup from your accommodation in Fortuna Town. Then head to Safari Put In for your wildlife experience.',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], 'itinerary_id');

        foreach (['Safari Put in', 'Portón del Río', 'La Fortuna Waterfall', 'Soda Fortuna'] as $index => $title) {
            $itemId = DB::table('itinerary_items')->where('title', $title)->value('item_id');
            DB::table('itinerary_item_itinerary')->insert([
                'itinerary_id' => $mini3,
                'itinerary_item_id' => $itemId,
                'item_order' => $index + 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
        DB::table('tours')->where('name', 'Minicombo 3 (Safari Float + La Fortuna Waterfall + Lunch)')->update(['itinerary_id' => $mini3]);

        // === Safari Float ===
        $safari = DB::table('itineraries')->insertGetId([
            'name' => 'Safari Float',
            'description' => 'The trip begins with a 20-minute drive from Fortuna to the river. Float down the river for approximately two hours, during which you will have an opportunity to see much of Costa Rica’s famous wildlife and have an interactive conversation with the guide.
The boat trip down the Peñas Blancas River offers a relaxing way to enjoy the abundant wildlife surrounding the banks of this lush paradise. Take pictures of the rainforest canopy teeming with monkeys or take a closer look at a tropical bird with your binoculars while you float in the oar boat. The naturalist will paddle and you can just relax like the iguanas nearby sunning themselves on the branches.',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], 'itinerary_id');

        foreach (['Safari Put in', 'Portón del Río'] as $index => $title) {
            $itemId = DB::table('itinerary_items')->where('title', $title)->value('item_id');
            DB::table('itinerary_item_itinerary')->insert([
                'itinerary_id' => $safari,
                'itinerary_item_id' => $itemId,
                'item_order' => $index + 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
        DB::table('tours')->where('name', 'Safari en el Río Peñas Blancas')->update(['itinerary_id' => $safari]);

        // === Arenal Volcano Hike ===
        $volcano = DB::table('itineraries')->insertGetId([
            'name' => 'Arenal Volcano Hike',
            'description' => 'The walk lasts around 2.5 hours of hiking at the base of the Arenal Volcano. To get to the viewpoint, we have to walk in the middle of the dry lava field of the eruption of 1968. At the viewpoint, you can see the Arenal Volcano and the Arenal Lake on the other side.',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], 'itinerary_id');

        $itemId = DB::table('itinerary_items')->where('title', 'Arenal 1968 Volcano View and Lava Trails')->value('item_id');
        DB::table('itinerary_item_itinerary')->insert([
            'itinerary_id' => $volcano,
            'itinerary_item_id' => $itemId,
            'item_order' => 1,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('tours')->where('name', 'Arenal Volcano Hike')->update(['itinerary_id' => $volcano]);

        // === Hanging Bridges ===
        $hanging = DB::table('itineraries')->insertGetId([
            'name' => 'Hanging Bridges',
            'description' => 'A journey of 3.2 km that will allow the visitor to comfortably enjoy the attractive forest along a path that has a total of 15 bridges, built with German technology. Our bridges have an ideal to provide an ideally forest contemplative experience to our visitors. The height and strategic position of each of the bridges can observe in a favorable light to light the different attractions and the thousands of species that inhabit our reservation, trees, flowers, plants, insects, mammals, reptiles, frogs and many other living in an ecosystem in perfect balance.',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], 'itinerary_id');

        $itemId = DB::table('itinerary_items')->where('title', 'Mistico Park')->value('item_id');
        DB::table('itinerary_item_itinerary')->insert([
            'itinerary_id' => $hanging,
            'itinerary_item_id' => $itemId,
            'item_order' => 1,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('tours')->where('name', 'Hanging Bridges')->update(['itinerary_id' => $hanging]);
    }
}
