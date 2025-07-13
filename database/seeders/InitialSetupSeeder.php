<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class InitialSetupSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('tour_languages')->insertOrIgnore([
            ['name'=>'Español','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['name'=>'English','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
        ]);

        DB::table('tour_types')->insertOrIgnore([
            ['name'=>'Full Day','description'=>'6 a 9 horas','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['name'=>'Half Day','description'=>'2 a 4 horas','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
        ]);

        foreach ([
            'Arenal 1968 Volcano View and Lava Trails',
            'La Fortuna Waterfall','Soda Fortuna',
            'Mistico Park','Safari Put in',
            'Peñas Blancas River','Portón del Río'
        ] as $title) {
            DB::table('itinerary_items')->updateOrInsert(
                ['title'=>$title],
                ['description'=>'Description for '.$title,'is_active'=>true,'created_at'=>$now,'updated_at'=>$now]
            );
        }

        $hotels = [
            'The Springs Resort and Spa',
            'Arenal Kioro',
            'Volcano Lodge',
            'Arenal Manoa',
            'Arenal Springs',
            'San Bosco Inn',
            'Lomas del Volcán',
            'Lost Iguana',
            'Socialtel La Fortuna',
            'Montaña de Fuego',
            'Paradise Hot Springs',
            'Los Lagos',
            'The Royal Corin',
            'Lavas Tacotal',
            'Arenal Roca Lodge Suites',
            'Arenal Roca Lodge Bungalows',
            'Casa del Rio',
            'Nayara Gardens',
            'Nayara Tented Camp',
            'Nayara Springs',
            'Hotel El Secreto',
            'La Fortuna Downtown',
            'Arenal Backpackers',
            'Arenal Rabfer',
            'Arenal Rooms',
            'Miradas Arenal',
            'Volcano Inn',
            'La Choza Inn',
            'Arenal Xilopalo',
        ];

        foreach ($hotels as $name) {
            DB::table('hotels_list')->updateOrInsert(
                ['name' => $name],
                ['is_active' => true, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        // Itinerary Nature Lover Combo 1
        $itinerary = DB::table('itineraries')->insertGetId([
            'name' => 'Nature Lover Combo 1',
            'description' => "Begin your full-day Costa Rican adventure with pickup from your accommodation in Fortuna Town. Then head to Arenal Volcano Park for a 2.5-hour hike traversing flat grounds and rocky terrain to witness the remnants of the 1968 eruption and learn about the volcano's history.",
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], 'itinerary_id');

        // Obtener los item_id reales
        $item1 = DB::table('itinerary_items')->where('title', 'Arenal 1968 Volcano View and Lava Trails')->value('item_id');
        $item2 = DB::table('itinerary_items')->where('title', 'La Fortuna Waterfall')->value('item_id');
        $item3 = DB::table('itinerary_items')->where('title', 'Soda Fortuna')->value('item_id');
        $item4 = DB::table('itinerary_items')->where('title', 'Mistico Park')->value('item_id');

        // Insertar relaciones pivote
        DB::table('itinerary_item_itinerary')->insert([
            [
                'itinerary_id' => $itinerary,
                'itinerary_item_id' => $item1,
                'item_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'itinerary_id' => $itinerary,
                'itinerary_item_id' => $item2,
                'item_order' => 2,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'itinerary_id' => $itinerary,
                'itinerary_item_id' => $item3,
                'item_order' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'itinerary_id' => $itinerary,
                'itinerary_item_id' => $item4,
                'item_order' => 4,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);

        // Tours
        $tour1 = DB::table('tours')->insertGetId([
            'name' => 'Aventura en el Volcán Arenal',
            'overview' => 'Discover Arenal Volcano National Park on a full-day hiking and hot springs tour from La Fortuna, and explore the remarkable landscape of an active volcanic range. Follow your guide along a 2-mile (3.2-km) trail that passes through primary and secondary forest, and cross the jagged rocks of a dry lava field. Spot the distinctive plants and formations that climb the sides of Costa Rica’s most iconic volcano.Explore an active volcanic range on a hiking tour Discover the various species that live in the forests and lava fields A small group ensures a personalized experience',
            'adult_price' => 75.00,
            'kid_price' => 55.00,
            'length' => 4,
            'tour_type_id' => 1,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now
        ], 'tour_id');

        $tour2 = DB::table('tours')->insertGetId([
            'name' => 'Safari en el Río Peñas Blancas',
            'overview' => "Float down the Peñas Blancas River in a rowboat on this 3.5-hour journey from La Fortuna. Listen to your naturalist guide's commentary while keeping a lookout for monkeys, iguanas, and a variety of bird life. Finish up with a stop at a local farm to sample their homemade snacks and coffee. Round-trip transport from selected hotels included. Family friendly Small group ensures personal service Free hotel pickup and drop-off included Informative, friendly and professional guide",
            'adult_price' => 60.00,
            'kid_price' => 45.00,
            'length' => 4,
            'tour_type_id' => 2,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now
        ], 'tour_id');

        $tour3 = DB::table('tours')->insertGetId([
            'name' => 'Nature Lover Combo 1',
            'overview' => "Combine three adventurous activities in a single tour: a hike to Arenal Volcano, visit to the Hanging Bridges, and exploration of La Fortuna Waterfall. This full-day tour from La Fortuna, perfect for nature lovers, includes hiking and swimming amidst Costa Rica's beautiful natural scenery. Hotel pickup and drop-off included.\n
        • Full-day Costa Rica adventure tour\n
        • Hike around Arenal Volcano and swim beneath La Fortuna Waterfall\n
        • Cross 16 hanging bridges in the rainforest\n
        • Hotel pickup and drop-off included\n
        • Personalized experience: small group tour limited to 12",
            'adult_price' => 154.00,
            'kid_price' => 115.00,
            'length' => 9,
            'tour_type_id' => 1,
            'is_active' => true,
            'created_at' => $now,
            'itinerary_id' => 1,
            'updated_at' => $now
        ], 'tour_id');

        // Idiomas en la tabla pivote
        DB::table('tour_language_tour')->insert([
            ['tour_id' => $tour1, 'tour_language_id' => 1], // Español
            ['tour_id' => $tour2, 'tour_language_id' => 2], // English
            ['tour_id' => $tour3, 'tour_language_id' => 1], // Español
        ]);

        DB::table('amenity_tour')->insert([
            ['tour_id' => $tour1, 'amenity_id' => 1, 'is_active' => true],
            ['tour_id' => $tour1, 'amenity_id' => 2, 'is_active' => true],
            ['tour_id' => $tour1, 'amenity_id' => 3, 'is_active' => true],
            ['tour_id' => $tour1, 'amenity_id' => 4, 'is_active' => true],

            ['tour_id' => $tour2, 'amenity_id' => 1, 'is_active' => true],
            ['tour_id' => $tour2, 'amenity_id' => 2, 'is_active' => true],
            ['tour_id' => $tour2, 'amenity_id' => 3, 'is_active' => true],
            ['tour_id' => $tour2, 'amenity_id' => 4, 'is_active' => true],
            ['tour_id' => $tour2, 'amenity_id' => 6, 'is_active' => true],

            ['tour_id' => $tour3, 'amenity_id' => 1, 'is_active' => true],
            ['tour_id' => $tour3, 'amenity_id' => 2, 'is_active' => true],
            ['tour_id' => $tour3, 'amenity_id' => 3, 'is_active' => true],
            ['tour_id' => $tour3, 'amenity_id' => 4, 'is_active' => true],
            ['tour_id' => $tour3, 'amenity_id' => 5, 'is_active' => true],

        ]);

        DB::table('excluded_amenity_tour')->insert([
            ['tour_id' => $tour1, 'amenity_id' => 5, 'is_active' => true],
            ['tour_id' => $tour1, 'amenity_id' => 6, 'is_active' => true],
            ['tour_id' => $tour1, 'amenity_id' => 7, 'is_active' => true],
            ['tour_id' => $tour1, 'amenity_id' => 8, 'is_active' => true],

            ['tour_id' => $tour2, 'amenity_id' => 5, 'is_active' => true],
            ['tour_id' => $tour2, 'amenity_id' => 7, 'is_active' => true],
            ['tour_id' => $tour2, 'amenity_id' => 8, 'is_active' => true],
            ['tour_id' => $tour2, 'amenity_id' => 9, 'is_active' => true],

            ['tour_id' => $tour3, 'amenity_id' => 6, 'is_active' => true],
            ['tour_id' => $tour3, 'amenity_id' => 7, 'is_active' => true],
            ['tour_id' => $tour3, 'amenity_id' => 8, 'is_active' => true],

        ]);


        DB::table('tour_schedules')->insert([
            [
                'tour_id' => $tour1,
                'start_time' => '07:30:00',
                'end_time' => '11:30:00',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'tour_id' => $tour1,
                'start_time' => '13:30:00',
                'end_time' => '16:30:00',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'tour_id' => $tour2,
                'start_time' => '7:30:00',
                'end_time' => '11:30:00',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'tour_id' => $tour2,
                'start_time' => '13:30:00',
                'end_time' => '16:30:00',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],

            [
                'tour_id' => $tour3,
                'start_time' => '7:30:00',
                'end_time' => '16:30:00',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
        foreach ([
            'Agua Embotellada','Guía Bilingüe','Transporte',
            'Tickets de entradas','Almuerzo','Snack',
            'Equipo óptico','Desayuno','Bebidas Alcoholicas'
        ] as $name) {
            DB::table('amenities')->updateOrInsert(
                ['name'=>$name],
                ['is_active'=>true,'created_at'=>$now,'updated_at'=>$now]
            );
        }
    }
}
