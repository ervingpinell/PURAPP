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
            ]
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
        'description' => "This is the restaurant where we will enjoy our lunch. Soda Fortuna offers exquisite food based on traditional Costa Rican cooking.",
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
        // Tours
        $tour1 = DB::table('tours')->insertGetId([
            'name' => 'Aventura en el Volcán Arenal',
            'overview' => 'Una experiencia inolvidable...',
            'adult_price' => 75.00,
            'kid_price' => 55.00,
            'length' => 6,
            'tour_type_id' => 1,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now
        ], 'tour_id');

        $tour2 = DB::table('tours')->insertGetId([
            'name' => 'Safari en el Río Peñas Blancas',
            'overview' => 'Observa la vida silvestre...',
            'adult_price' => 60.00,
            'kid_price' => 45.00,
            'length' => 4,
            'tour_type_id' => 2,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now
        ], 'tour_id');

        $tour3 = DB::table('tours')->insertGetId([
            'name' => 'Puentes Colgantes y Bosque Lluvioso',
            'overview' => 'Explora el bosque tropical...',
            'adult_price' => 80.00,
            'kid_price' => 65.00,
            'length' => 6,
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


    }
}