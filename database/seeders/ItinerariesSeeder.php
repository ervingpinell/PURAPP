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
            'description' => 'Comienza tu aventura costarricense de día completo con la recogida en tu alojamiento en el centro de La Fortuna. Luego, dirígete al Parque Volcán Arenal para una caminata de 2,5 horas que atraviesa terrenos planos y rocosos, donde podrás observar los vestigios de la erupción de 1968 y aprender sobre la historia del volcán.',
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
            ->where('name', 'Nature Lover Combo 1 (Puentes Colgantes + Catarata de La Fortuna + Almuerzo + Caminata al Volcán Arenal)')
            ->update(['itinerary_id' => $natureId]);

        // === Minicombo 1 ===
        $mini1 = DB::table('itineraries')->insertGetId([
            'name' => 'Minicombo 1',
            'description' => 'Comienza tu aventura costarricense de medio día con la recogida en tu alojamiento en el centro de La Fortuna. Luego, dirígete al Parque Mistico para una caminata de 2 horas entre las copas de los árboles de la selva tropical.',
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
        DB::table('tours')->where('name', 'Minicombo 1 (Puentes Colgantes + Catarata de La Fortuna + Almuerzo)')->update(['itinerary_id' => $mini1]);

        // === Minicombo 2 ===
        $mini2 = DB::table('itineraries')->insertGetId([
            'name' => 'Minicombo 2',
            'description' => 'Comienza tu aventura costarricense de medio día con la recogida en tu alojamiento en el centro de La Fortuna. Luego, dirígete al Parque Arenal 1968 para una caminata de 2 horas.',
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
        DB::table('tours')->where('name', 'Minicombo 2 (Caminata al Volcán Arenal + Catarata de La Fortuna + Almuerzo)')->update(['itinerary_id' => $mini2]);

        // === Minicombo 3 ===
        $mini3 = DB::table('itineraries')->insertGetId([
            'name' => 'Minicombo 3',
            'description' => 'Comienza tu aventura costarricense de medio día con la recogida en tu alojamiento en el centro de La Fortuna. Luego, dirígete a Safari Put In para tu experiencia de vida silvestre.',
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
        DB::table('tours')->where('name', 'Minicombo 3 (Safari Flotante + Catarata de La Fortuna + Almuerzo)')->update(['itinerary_id' => $mini3]);

        // === Safari Flotante ===
        $safari = DB::table('itineraries')->insertGetId([
            'name' => 'Safari Flotante',
            'description' => 'El recorrido comienza con un viaje de 20 minutos desde Fortuna hasta el río. Flota por el río durante aproximadamente dos horas, durante las cuales tendrás la oportunidad de ver gran parte de la famosa vida silvestre de Costa Rica y tener una conversación interactiva con el guía.
El viaje en bote por el río Peñas Blancas ofrece una forma relajante de disfrutar de la abundante vida silvestre que rodea las orillas de este exuberante paraíso. Toma fotos del dosel del bosque lluvioso lleno de monos o observa más de cerca a un ave tropical con tus binoculares mientras flotas en el bote de remos. El naturalista remará y tú podrás relajarte como las iguanas cercanas que se asolean en las ramas.',
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
        DB::table('tours')->where('name', 'Safari Flotante')->update(['itinerary_id' => $safari]);

        // === Caminata al Volcán Arenal ===
        $volcano = DB::table('itineraries')->insertGetId([
            'name' => 'Caminata al Volcán Arenal',
            'description' => 'La caminata dura alrededor de 2.5 horas de senderismo en la base del Volcán Arenal. Para llegar al mirador, tenemos que caminar en medio del campo de lava seca de la erupción de 1968. Desde el mirador, se puede ver el Volcán Arenal y el Lago Arenal al otro lado.',
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
        DB::table('tours')->where('name', 'Caminata al Volcán Arenal')->update(['itinerary_id' => $volcano]);

        // === Puentes Colgantes ===
        $hanging = DB::table('itineraries')->insertGetId([
            'name' => 'Puentes Colgantes',
            'description' => 'Un recorrido de 3.2 km que permitirá al visitante disfrutar cómodamente del atractivo bosque a lo largo de un sendero que cuenta con un total de 15 puentes, construidos con tecnología alemana. Nuestros puentes tienen un diseño ideal para proporcionar a nuestros visitantes una experiencia contemplativa en el bosque. La altura y la posición estratégica de cada uno de los puentes permiten observar en una luz favorable las diferentes atracciones y las miles de especies que habitan nuestra reserva, árboles, flores, plantas, insectos, mamíferos, reptiles, ranas y muchos otros que viven en un ecosistema en perfecto equilibrio.',
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
        DB::table('tours')->where('name', 'Puentes Colgantes')->update(['itinerary_id' => $hanging]);
    }
}
