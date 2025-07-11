<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class InitialSetupSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Roles
        DB::table('roles')->insertOrIgnore([
            ['role_id' => 1, 'role_name' => 'Admin',      'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['role_id' => 2, 'role_name' => 'Supervisor', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['role_id' => 3, 'role_name' => 'Customer',   'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Users
        DB::table('users')->insertOrIgnore([
            [
                'full_name' => 'Erving Pinell',
                'email' => 'ervingpinell@gmail.com',
                'password' => Hash::make('-erving1234'),
                'role_id' => 1,
                'phone' => '24791471',
                'status' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'full_name' => 'Axel Paniagua',
                'email' => 'axelpaniaguab54@gmail.com',
                'password' => Hash::make('-12345678.'),
                'role_id' => 1,
                'phone' => '72612748',
                'status' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'full_name' => 'Test Test',
                'email' => 'test@a.com',
                'password' => Hash::make('-12345678'),
                'role_id' => 3,
                'phone' => '24791471',
                'status' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],

        ]);

        // Tour Languages
        DB::table('tour_languages')->insertOrIgnore([
            ['name' => 'Español', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'English', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Tour Types
        DB::table('tour_types')->insertOrIgnore([
            ['name' => 'Full Day', 'description' => '6 a 9 horas',  'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Half Day', 'description' => '2 a 4 horas',  'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Itinerary Items
        DB::table('itinerary_items')->insertOrIgnore([
            [
                'title' => 'Arenal 1968 Volcano View and Lava Trails',
                'description' => "The 1968 Arenal Volcano Park is where you will start our activity. It's a 2.30 hour hike to the base of Arenal Volcano. The hike is fairly easy and most of it is over flat ground. The goal is to arrive at a dry lava field from the 1968 eruption (this part of the trail is rocky). There you can see the Volcano and our guide will explain you all the history about the Arenal Volcano.",
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => 'La Fortuna Waterfall',
                'description' => "The hike to the base of the waterfall has about 500 steps (which you will need to climb back up). Once at the base, you will have some time to swim and enjoy the waterfall. In case that you can't go down, there is a viewpoint with a restaurant on the top where you can wait for the group.",
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => 'Soda Fortuna',
                'description' => "This is the restaurant where we will enjoy our lunch.The lunch will be a Costa Rican Meal Call “Casado” Which is Rice, Beans, Green Salad, mashed potatoes or yuca puree and the protein that you can choose between fish, chicken, pork, beef or vegetarian, and a natural drink. Our guide will ask you for your protein choice on the road the day of the tour.
                Let us know in advance any allergies, and health issues.",
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => 'Mistico Park',
                'description' => "You will spend about 2 hours hiking through the rainforest and crossing 6 hanging and 10 static bridges. This hike is different from the volcano trails as all the trail is made of concrete and part of the trail has accessibility for wheelchair. In case you don’t want or can't walk more, there are shortcuts and also a restaurant where you can wait for the rest of the group. Remember that it is a tropical forest, so expect rain and bring your rain gear.",
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => 'Safari Put in',
                'description' => "Located next to La Perla bridge of La Fortuna. Here we will be from 15 to 25 minutes getting the equipment ready while using the bathrooms. The guide will get everything ready meanwhile you suit your lifejacket and get your paddle.",
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => 'Peñas Blancas River',
                'description' => "This section lasts from 1:30 to 2:00 hours depending on the intensity of the current and how many stops to see wildlife we are able to do. This river is surrounded by forest and during this section, our guide will answer all your questions while trying to spot as much wildlife as possible and explain you about Costa Rica. This is one of the best tours to spot sloths, crocodiles, and monkeys.",
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'title' => 'Portón del Río',
                'description' => "This is a local Costa Rican farm owned by Hidalgo's Family. There they will prepare for us a snack of natural bread, plantains, cheese, coffee or juice from what they produce. What they prepare is mostly different depending on the season. While you are enjoying your snack, our guide and driver will get all the equipment ready for departure.",
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);

        // Amenities
        $amenities = [
            'Agua Embotellada',
            'Guía Bilingüe',
            'Transporte',
            'Tickets de entradas',
            'Almuerzo',
            'Snack',
            'Equipo óptico',
            'Desayuno',
            'Bebidas Alcoholicas',
        ];

        foreach ($amenities as $name) {
            DB::table('amenities')->updateOrInsert(
                ['name' => $name],
                ['is_active' => true, 'created_at' => $now, 'updated_at' => $now]
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

        // --------------------------
        // Crear 4 horarios para $tour1
        // --------------------------
        $schedule1 = DB::table('schedules')->insertGetId([
            'start_time' => '08:00',
            'end_time'   => '12:00',
            'label'      => 'AM',
            'is_active'  => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], 'schedule_id'); 

        $schedule2 = DB::table('schedules')->insertGetId([
            'start_time' => '13:00',
            'end_time'   => '17:00',
            'label'      => 'PM',
            'is_active'  => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], 'schedule_id');

        $schedule3 = DB::table('schedules')->insertGetId([
            'start_time' => '05:00',
            'end_time'   => '09:00',
            'label'      => 'AM Early',
            'is_active'  => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], 'schedule_id');

        $schedule4 = DB::table('schedules')->insertGetId([
            'start_time' => '17:00',
            'end_time'   => '21:00',
            'label'      => 'PM Late',
            'is_active'  => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], 'schedule_id');

        // --------------------------
        // Relacionar los horarios con el tour en la tabla pivot
        // --------------------------
        DB::table('schedule_tour')->insert([
            ['tour_id' => $tour1, 'schedule_id' => $schedule1],
            ['tour_id' => $tour1, 'schedule_id' => $schedule2],
            ['tour_id' => $tour1, 'schedule_id' => $schedule3],
            ['tour_id' => $tour1, 'schedule_id' => $schedule4],
        ]);





    }
}
