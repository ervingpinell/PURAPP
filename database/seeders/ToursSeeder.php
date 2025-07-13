<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ToursSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Horario compartido
        $sharedId = DB::table('schedules')->updateOrInsert(
            ['start_time' => '07:30', 'end_time' => '13:30'],
            ['label' => 'AM', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now]
        );
        $sharedId = DB::table('schedules')->where('start_time', '07:30')->where('end_time', '13:30')->value('schedule_id');

        // === Volcano Hike ===
        $volcanoOverview = 'Discover Arenal Volcano National Park on a full-day hiking and hot springs tour from La Fortuna, and explore the remarkable landscape of an active volcanic range. Follow your guide along a 2-mile (3.2-km) trail that passes through primary and secondary forest, and cross the jagged rocks of a dry lava field. Spot the distinctive plants and formations that climb the sides of Costa Rica\'s most iconic volcano. Explore an active volcanic range on a hiking tour. Discover the various species that live in the forests and lava fields. A small group ensures a personalized experience.';

        $volcano = DB::table('tours')->insertGetId([
            'name' => 'Arenal Volcano Hike',
            'overview' => $volcanoOverview,
            'adult_price' => 75,
            'kid_price' => 55,
            'length' => 4,
            'tour_type_id' => 2,
            'color' => '#ABABAB',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now
        ], 'tour_id');

        $s1 = DB::table('schedules')->insertGetId(['start_time' => '08:00', 'end_time' => '12:00', 'label' => 'AM', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now], 'schedule_id');
        $s2 = DB::table('schedules')->insertGetId(['start_time' => '13:00', 'end_time' => '17:00', 'label' => 'PM', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now], 'schedule_id');

        DB::table('schedule_tour')->insert([
            ['tour_id' => $volcano, 'schedule_id' => $s1],
            ['tour_id' => $volcano, 'schedule_id' => $s2]
        ]);
        DB::table('tour_language_tour')->insert([
            ['tour_id' => $volcano, 'tour_language_id' => 1],
            ['tour_id' => $volcano, 'tour_language_id' => 2]
        ]);
        foreach ([1, 2, 3, 4] as $a) DB::table('amenity_tour')->insert(['tour_id' => $volcano, 'amenity_id' => $a, 'is_active' => true]);
        foreach ([5, 6, 7, 8, 9] as $a) DB::table('excluded_amenity_tour')->insert(['tour_id' => $volcano, 'amenity_id' => $a, 'is_active' => true]);

        // === Safari Float ===
        $safariOverview = 'Float down the Peñas Blancas River in a rowboat on this 3.5-hour journey from La Fortuna. Listen to your naturalist guide\'s commentary while keeping a lookout for monkeys, iguanas, and a variety of bird life. Finish up with a stop at a local farm to sample their homemade snacks and coffee. Round-trip transport from selected hotels included. Family friendly. Small group ensures personal service. Free hotel pickup and drop-off included. Informative, friendly and professional guide.';

        $safari = DB::table('tours')->insertGetId([
            'name' => 'Safari en el Río Peñas Blancas',
            'overview' => $safariOverview,
            'adult_price' => 60,
            'kid_price' => 45,
            'length' => 4,
            'tour_type_id' => 2,
            'color' => '#4F8BD8',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now
        ], 'tour_id');
        DB::table('schedule_tour')->insert([['tour_id' => $safari, 'schedule_id' => $sharedId]]);
        DB::table('tour_language_tour')->insert([['tour_id' => $safari, 'tour_language_id' => 1], ['tour_id' => $safari, 'tour_language_id' => 2]]);
        foreach ([1, 2, 3, 4, 6] as $a) DB::table('amenity_tour')->insert(['tour_id' => $safari, 'amenity_id' => $a, 'is_active' => true]);
        foreach ([5, 7, 8, 9] as $a) DB::table('excluded_amenity_tour')->insert(['tour_id' => $safari, 'amenity_id' => $a, 'is_active' => true]);

        // === Hanging Bridges ===
$hangingOverview = 'Enjoy a thrilling close encounter with Costa Rica’s wildlife on this 4-hour tour to the Mistico Hanging Bridges Park from La Fortuna, in the shadows of the Arenal Volcano. Journey to the heart of the rainforest on a 2-mile (3.2km) circuit of 15 hanging bridges and have chance to spot up to 350 species of bird, including hummingbirds, bell birds, toucans and the majestic Tucancito Esmeralda.
• Enjoy a guided hike along a circuit of 15 specially designed bridges
• Spot hummingbirds and toucans in their natural environment
• Great choice for families!';

$hanging = DB::table('tours')->insertGetId([
    'name' => 'Hanging Bridges',
    'overview' => $hangingOverview,
    'adult_price' => 82,
    'kid_price' => 61,
    'length' => 4,
    'tour_type_id' => 2,
    'color' => '#56D454',
    'is_active' => true,
    'created_at' => $now,
    'updated_at' => $now
], 'tour_id');

$h1 = DB::table('schedules')->insertGetId(['start_time' => '07:30', 'end_time' => '11:30', 'label' => 'AM', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now], 'schedule_id');
$h2 = DB::table('schedules')->insertGetId(['start_time' => '13:00', 'end_time' => '16:00', 'label' => 'PM', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now], 'schedule_id');

DB::table('schedule_tour')->insert([['tour_id' => $hanging, 'schedule_id' => $h1], ['tour_id' => $hanging, 'schedule_id' => $h2]]);
DB::table('tour_language_tour')->insert([['tour_id' => $hanging, 'tour_language_id' => 1], ['tour_id' => $hanging, 'tour_language_id' => 2]]);
foreach ([1, 2, 3, 4] as $a) DB::table('amenity_tour')->insert(['tour_id' => $hanging, 'amenity_id' => $a, 'is_active' => true]);
foreach ([5, 6, 7, 8, 9] as $a) DB::table('excluded_amenity_tour')->insert(['tour_id' => $hanging, 'amenity_id' => $a, 'is_active' => true]);


        // === Nature Lover Combo 1 ===
        $natureOverview = 'Combine three adventurous activities in a single tour: a hike to Arenal Volcano, visit to the Hanging Bridges, and exploration of La Fortuna Waterfall. This full-day tour from La Fortuna, perfect for nature lovers, includes hiking and swimming amidst Costa Rica\'s beautiful natural scenery. Hotel pickup and drop-off included.
• Full-day Costa Rica adventure tour.
• Hike around Arenal Volcano and swim beneath La Fortuna Waterfall.
• Cross 16 hanging bridges in the rainforest.
• Hotel pickup and drop-off included.
• Personalized experience: small group tour limited to 12.';

        $nature = DB::table('tours')->insertGetId([
            'name' => 'Nature Lover Combo 1',
            'overview' => $natureOverview,
            'adult_price' => 154,
            'kid_price' => 115,
            'length' => 9,
            'tour_type_id' => 1,
            'color' => '#DC626D',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now
        ], 'tour_id');
        DB::table('schedule_tour')->insert([['tour_id' => $nature, 'schedule_id' => $sharedId]]);
        DB::table('tour_language_tour')->insert([['tour_id' => $nature, 'tour_language_id' => 1], ['tour_id' => $nature, 'tour_language_id' => 2]]);
        foreach ([1, 2, 3, 4, 5] as $a) DB::table('amenity_tour')->insert(['tour_id' => $nature, 'amenity_id' => $a, 'is_active' => true]);
        foreach ([6, 7, 8, 9] as $a) DB::table('excluded_amenity_tour')->insert(['tour_id' => $nature, 'amenity_id' => $a, 'is_active' => true]);

        // === Minicombo 1 ===
        $minicombo1Overview = 'Discover the natural attractions of La Fortuna on this all-day guided tour. Perfect for the whole family and those who are short on time, the tour includes a stop at La Fortuna Waterfall as well as a traditional Costa Rican lunch.
• Travel across suspension bridges hanging over the rainforest to take in unique views.
• See the top attractions of La Fortuna in one day. Spot bird species, monkeys, and other local wildlife.
• Get the chance to swim in the crystal-clear waters of the waterfall.
• The tour ends with a typical lunch featuring Costa Rican cuisine.';

        $minicombo1 = DB::table('tours')->insertGetId([
            'name' => 'Minicombo 1 (Hanging Bridges + La Fortuna Waterfall + Lunch)',
            'overview' => $minicombo1Overview,
            'adult_price' => 136,
            'kid_price' => 102,
            'length' => 6,
            'tour_type_id' => 1,
            'color' => '#DC626D',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now
        ], 'tour_id');
        DB::table('schedule_tour')->insert([['tour_id' => $minicombo1, 'schedule_id' => $sharedId]]);
        DB::table('tour_language_tour')->insert([['tour_id' => $minicombo1, 'tour_language_id' => 1], ['tour_id' => $minicombo1, 'tour_language_id' => 2]]);
        foreach ([1, 2, 3, 4, 5] as $a) DB::table('amenity_tour')->insert(['tour_id' => $minicombo1, 'amenity_id' => $a, 'is_active' => true]);
        foreach ([6, 7, 8, 9] as $a) DB::table('excluded_amenity_tour')->insert(['tour_id' => $minicombo1, 'amenity_id' => $a, 'is_active' => true]);

        // === Minicombo 2 ===
        $minicombo2Overview = 'If you\'ve come to Costa Rica for its incredible volcanoes and biodiversity, this day trip delivers. You\'ll be taken to one of the best viewpoints overlooking Volcán Arenal. Whatever the weather you can appreciate its symmetrical beauty. Next, visit the La Fortuna Waterfall, where you can cool off in the sparkling plunge pool.
• Lunch with local coffee is included.
• An easy way to visit two of Costa Rica’s highlights.
• Avoid sweaty buses and travel in air-conditioned comfort. Wear comfy shoes and be prepared to hike to the lookout and waterfall.
• Pickups from Fortuna town area are included.';

        $minicombo2 = DB::table('tours')->insertGetId([
            'name' => 'Minicombo 2 (Volcano Hike + La Fortuna Waterfall + Lunch)',
            'overview' => $minicombo2Overview,
            'adult_price' => 136,
            'kid_price' => 102,
            'length' => 6,
            'tour_type_id' => 1,
            'color' => '#DC626D',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now
        ], 'tour_id');
        DB::table('schedule_tour')->insert([['tour_id' => $minicombo2, 'schedule_id' => $sharedId]]);
        DB::table('tour_language_tour')->insert([['tour_id' => $minicombo2, 'tour_language_id' => 1], ['tour_id' => $minicombo2, 'tour_language_id' => 2]]);
        foreach ([1, 2, 3, 4, 5] as $a) DB::table('amenity_tour')->insert(['tour_id' => $minicombo2, 'amenity_id' => $a, 'is_active' => true]);
        foreach ([6, 7, 8, 9] as $a) DB::table('excluded_amenity_tour')->insert(['tour_id' => $minicombo2, 'amenity_id' => $a, 'is_active' => true]);

        // === Minicombo 3 ===
        $minicombo3Overview = 'The 230-foot (70-meter) high La Fortuna Waterfall, surrounded by verdant wet forest, is one of the region\'s most photographed attractions. On this half-day tour, take the scenic route as you float down the river on a raft, then cool off with a swim beneath the falls. Along the way, look out for exotic birds, howler monkeys, and sloths; visit a traditional Costa Rican family home; and tuck into a delicious lunch at a local restaurant.
• Magnificent views and chance to swim at Fortuna Waterfall.
• Express tour: combine wildlife and culture with this semi full-day itinerary.
• Hassle-free pickup and drop-off at your La Fortuna hotel.
• Intimate small-group tour with a maximum of 12 people (6 per raft).';

        $minicombo3 = DB::table('tours')->insertGetId([
            'name' => 'Minicombo 3 (Safari Float + La Fortuna Waterfall + Lunch)',
            'overview' => $minicombo3Overview,
            'adult_price' => 136,
            'kid_price' => 102,
            'length' => 6,
            'tour_type_id' => 1,
            'color' => '#DC626D',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now
        ], 'tour_id');
        DB::table('schedule_tour')->insert([['tour_id' => $minicombo3, 'schedule_id' => $sharedId]]);
        DB::table('tour_language_tour')->insert([['tour_id' => $minicombo3, 'tour_language_id' => 1], ['tour_id' => $minicombo3, 'tour_language_id' => 2]]);
        foreach ([1, 2, 3, 4, 5, 6] as $a) DB::table('amenity_tour')->insert(['tour_id' => $minicombo3, 'amenity_id' => $a, 'is_active' => true]);
        foreach ([7, 8, 9] as $a) DB::table('excluded_amenity_tour')->insert(['tour_id' => $minicombo3, 'amenity_id' => $a, 'is_active' => true]);
    }
}
