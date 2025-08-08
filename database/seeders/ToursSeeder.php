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

        // === HORARIOS COMPARTIDOS ===
        DB::table('schedules')->updateOrInsert(
            ['start_time' => '07:30', 'end_time' => '11:30'],
            ['label' => 'AM', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now]
        );
        $sharedAmId = DB::table('schedules')->where('start_time', '07:30')->where('end_time', '11:30')->value('schedule_id');

        DB::table('schedules')->updateOrInsert(
            ['start_time' => '13:00', 'end_time' => '16:30'],
            ['label' => 'PM', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now]
        );
        $sharedPmId = DB::table('schedules')->where('start_time', '13:00')->where('end_time', '16:30')->value('schedule_id');

        DB::table('schedules')->updateOrInsert(
            ['start_time' => '07:30', 'end_time' => '13:30'],
            ['label' => 'AM', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now]
        );
        $sharedMidId = DB::table('schedules')->where('start_time', '07:30')->where('end_time', '13:30')->value('schedule_id');

        DB::table('schedules')->updateOrInsert(
            ['start_time' => '07:30', 'end_time' => '16:30'],
            ['label' => 'AM', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now]
        );
        $nlc = DB::table('schedules')->where('start_time', '07:30')->where('end_time', '16:30')->value('schedule_id');

        // === Caminata al Volcán Arenal ===
        $volcanoOverview = 'Descubre el Parque Nacional Volcán Arenal en un tour de día completo que combina caminata y aguas termales desde La Fortuna, y explora el impresionante paisaje de una cordillera volcánica activa.
Sigue a tu guía por un sendero de 3,2 km (2 millas) que atraviesa bosques primarios y secundarios, y cruza las afiladas rocas de un campo de lava seca.
Observa las plantas y formaciones distintivas que cubren las laderas del volcán más icónico de Costa Rica.
• Explora una cordillera volcánica activa en un tour de caminata.
• Descubre las diversas especies que habitan en los bosques y campos de lava.
• Grupo pequeño para garantizar una experiencia personalizada.';

        $volcano = DB::table('tours')->insertGetId([
            'name' => 'Caminata al Volcán Arenal',
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

        DB::table('schedule_tour')->insert([
            ['tour_id' => $volcano, 'schedule_id' => $sharedAmId],
            ['tour_id' => $volcano, 'schedule_id' => $sharedPmId],
        ]);

        DB::table('tour_language_tour')->insert([
            ['tour_id' => $volcano, 'tour_language_id' => 1],
            ['tour_id' => $volcano, 'tour_language_id' => 2]
        ]);
        foreach ([1, 2, 3, 4] as $a) DB::table('amenity_tour')->insert(['tour_id' => $volcano, 'amenity_id' => $a, 'is_active' => true]);
        foreach ([5, 6, 7, 8, 9] as $a) DB::table('excluded_amenity_tour')->insert(['tour_id' => $volcano, 'amenity_id' => $a, 'is_active' => true]);

        // === Safari Flotante ===
        $safariOverview = 'Navega por el río Peñas Blancas en un bote de remo durante este recorrido de 3,5 horas desde La Fortuna.
Escucha los comentarios de tu guía naturalista mientras mantienes la vista atenta para observar monos, iguanas y una gran variedad de aves.
Finaliza con una parada en una finca local para degustar sus bocadillos caseros y café.
Incluye transporte de ida y vuelta desde hoteles seleccionados. Ideal para toda la familia.
Grupo pequeño para garantizar un servicio personalizado.
Recogida y regreso al hotel gratuitos. Guía informativo, amable y profesional.';

        $safari = DB::table('tours')->insertGetId([
            'name' => 'Safari Flotante',
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

        DB::table('schedule_tour')->insert([
            ['tour_id' => $safari, 'schedule_id' => $sharedAmId],
            ['tour_id' => $safari, 'schedule_id' => $sharedPmId],
        ]);

        DB::table('tour_language_tour')->insert([
            ['tour_id' => $safari, 'tour_language_id' => 1],
            ['tour_id' => $safari, 'tour_language_id' => 2]
        ]);
        foreach ([1, 2, 3, 4, 6] as $a) DB::table('amenity_tour')->insert(['tour_id' => $safari, 'amenity_id' => $a, 'is_active' => true]);
        foreach ([5, 7, 8, 9] as $a) DB::table('excluded_amenity_tour')->insert(['tour_id' => $safari, 'amenity_id' => $a, 'is_active' => true]);

        // === Puentes Colgantes ===
        $hangingOverview = 'Disfruta de un emocionante encuentro cercano con la vida silvestre de Costa Rica en este tour de 4 horas al Parque Mistico de Puentes Colgantes desde La Fortuna, a la sombra del Volcán Arenal.
Adéntrate en el corazón de la selva tropical en un circuito de 3,2 km (2 millas) que incluye 15 puentes colgantes, y ten la oportunidad de avistar hasta 350 especies de aves, incluyendo colibríes, campaneros, tucanes y el majestuoso Tucancito Esmeralda.
• Disfruta de una caminata guiada por un circuito de 15 puentes especialmente diseñados.
• Observa colibríes y tucanes en su hábitat natural.
• ¡Una excelente opción para familias!';

        $hanging = DB::table('tours')->insertGetId([
            'name' => 'Puentes Colgantes',
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

        DB::table('schedule_tour')->insert([
            ['tour_id' => $hanging, 'schedule_id' => $sharedAmId],
            ['tour_id' => $hanging, 'schedule_id' => $sharedPmId],
        ]);

        DB::table('tour_language_tour')->insert([
            ['tour_id' => $hanging, 'tour_language_id' => 1],
            ['tour_id' => $hanging, 'tour_language_id' => 2]
        ]);
        foreach ([1, 2, 3, 4] as $a) DB::table('amenity_tour')->insert(['tour_id' => $hanging, 'amenity_id' => $a, 'is_active' => true]);
        foreach ([5, 6, 7, 8, 9] as $a) DB::table('excluded_amenity_tour')->insert(['tour_id' => $hanging, 'amenity_id' => $a, 'is_active' => true]);

        // === Nature Lover Combo 1 ===
        $natureOverview = 'Combina tres actividades llenas de aventura en un solo tour: una caminata al Volcán Arenal, visita a los Puentes Colgantes y exploración de la Catarata La Fortuna.
Este tour de día completo desde La Fortuna, perfecto para los amantes de la naturaleza, incluye caminatas y la oportunidad de nadar en medio del hermoso paisaje natural de Costa Rica.
Incluye recogida y regreso al hotel.
• Tour de aventura de día completo en Costa Rica.
• Caminata alrededor del Volcán Arenal y baño bajo la Catarata La Fortuna.
• Cruza 16 puentes colgantes en la selva tropical.
• Experiencia personalizada: tour en grupo pequeño limitado a 12 personas.';

        $nature = DB::table('tours')->insertGetId([
            'name' => 'Nature Lover Combo 1 (Puentes Colgantes + Catarata de La Fortuna + Almuerzo + Caminata al Volcán Arenal)',
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

        DB::table('schedule_tour')->insert([['tour_id' => $nature, 'schedule_id' => $nlc]]);
        DB::table('tour_language_tour')->insert([
            ['tour_id' => $nature, 'tour_language_id' => 1],
            ['tour_id' => $nature, 'tour_language_id' => 2]
        ]);
        foreach ([1, 2, 3, 4, 5] as $a) DB::table('amenity_tour')->insert(['tour_id' => $nature, 'amenity_id' => $a, 'is_active' => true]);
        foreach ([6, 7, 8, 9] as $a) DB::table('excluded_amenity_tour')->insert(['tour_id' => $nature, 'amenity_id' => $a, 'is_active' => true]);

        // === Minicombo 1 ===
        $minicombo1Overview = 'Descubre las atracciones naturales de La Fortuna en este tour guiado de día completo.
Perfecto para toda la familia y para quienes cuentan con poco tiempo, el recorrido incluye una parada en la Catarata La Fortuna, así como un almuerzo tradicional costarricense.
• Cruza puentes colgantes sobre la selva tropical para disfrutar de vistas únicas.
• Conoce las principales atracciones de La Fortuna en un solo día. Observa aves, monos y otra fauna local.
• Disfruta la oportunidad de nadar en las aguas cristalinas de la catarata.
• El tour finaliza con un almuerzo típico de la cocina costarricense.';

        $minicombo1 = DB::table('tours')->insertGetId([
            'name' => 'Minicombo 1 (Puentes Colgantes + Catarata de La Fortuna + Almuerzo)',
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

        DB::table('schedule_tour')->insert([['tour_id' => $minicombo1, 'schedule_id' => $sharedMidId]]);
        DB::table('tour_language_tour')->insert([
            ['tour_id' => $minicombo1, 'tour_language_id' => 1],
            ['tour_id' => $minicombo1, 'tour_language_id' => 2]
        ]);
        foreach ([1, 2, 3, 4, 5] as $a) DB::table('amenity_tour')->insert(['tour_id' => $minicombo1, 'amenity_id' => $a, 'is_active' => true]);
        foreach ([6, 7, 8, 9] as $a) DB::table('excluded_amenity_tour')->insert(['tour_id' => $minicombo1, 'amenity_id' => $a, 'is_active' => true]);

        // === Minicombo 2 ===
        $minicombo2Overview = 'Si has venido a Costa Rica por sus increíbles volcanes y su biodiversidad, esta excursión de un día es ideal para ti.
Te llevaremos a uno de los mejores miradores con vista al Volcán Arenal, donde, sin importar el clima, podrás apreciar su belleza simétrica.
Luego, visitarás la Catarata La Fortuna, donde podrás refrescarte en su reluciente poza natural.
• Almuerzo con café local incluido.
• Una forma sencilla de visitar dos de las principales atracciones de Costa Rica.
• Evita los buses calurosos y viaja con la comodidad del aire acondicionado. Usa zapatos cómodos y prepárate para caminar hasta el mirador y la catarata.
• Incluye recogida en la zona del centro de La Fortuna.';

        $minicombo2 = DB::table('tours')->insertGetId([
            'name' => 'Minicombo 2 (Caminata al Volcán Arenal + Catarata de La Fortuna + Almuerzo)',
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

        DB::table('schedule_tour')->insert([['tour_id' => $minicombo2, 'schedule_id' => $sharedMidId]]);
        DB::table('tour_language_tour')->insert([
            ['tour_id' => $minicombo2, 'tour_language_id' => 1],
            ['tour_id' => $minicombo2, 'tour_language_id' => 2]
        ]);
        foreach ([1, 2, 3, 4, 5] as $a) DB::table('amenity_tour')->insert(['tour_id' => $minicombo2, 'amenity_id' => $a, 'is_active' => true]);
        foreach ([6, 7, 8, 9] as $a) DB::table('excluded_amenity_tour')->insert(['tour_id' => $minicombo2, 'amenity_id' => $a, 'is_active' => true]);

        // === Minicombo 3 ===
        $minicombo3Overview = 'La Catarata La Fortuna, con sus 70 metros (230 pies) de altura y rodeada de un exuberante bosque lluvioso, es una de las atracciones más fotografiadas de la región.
En este tour de medio día, disfruta de la ruta escénica mientras navegas por el río en una balsa y luego refréscate nadando bajo la cascada.
En el camino, mantente atento para avistar aves exóticas, monos aulladores y perezosos; visita el hogar de una familia tradicional costarricense y saborea un delicioso almuerzo en un restaurante local.
• Vistas magníficas y oportunidad de nadar en la Catarata La Fortuna.
• Tour exprés: combina vida silvestre y cultura en este itinerario de medio día.
• Recogida y regreso sin complicaciones en tu hotel en La Fortuna.
• Tour íntimo en grupo pequeño, con un máximo de 12 personas (6 por balsa).';

        $minicombo3 = DB::table('tours')->insertGetId([
            'name' => 'Minicombo 3 (Safari Flotante + Catarata de La Fortuna + Almuerzo)',
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

        DB::table('schedule_tour')->insert([['tour_id' => $minicombo3, 'schedule_id' => $sharedMidId]]);
        DB::table('tour_language_tour')->insert([
            ['tour_id' => $minicombo3, 'tour_language_id' => 1],
            ['tour_id' => $minicombo3, 'tour_language_id' => 2]
        ]);
        foreach ([1, 2, 3, 4, 5, 6] as $a) DB::table('amenity_tour')->insert(['tour_id' => $minicombo3, 'amenity_id' => $a, 'is_active' => true]);
        foreach ([7, 8, 9] as $a) DB::table('excluded_amenity_tour')->insert(['tour_id' => $minicombo3, 'amenity_id' => $a, 'is_active' => true]);
    }
}
