<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Itinerary;
use App\Models\ItineraryItem;

class ItinerariesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // === Nature Lover Combo 1 ===
        $nature = Itinerary::create(['is_active' => true]);
        DB::table('itinerary_translations')->insert([
            'itinerary_id' => $nature->itinerary_id,
            'locale' => 'es',
            'name' => 'Nature Lover Combo 1',
            'description' => 'Comienza tu aventura costarricense de día completo con la recogida en tu alojamiento en el centro de La Fortuna. Luego, dirígete al Parque Volcán Arenal para una caminata de 2,5 horas que atraviesa terrenos planos y rocosos, donde podrás observar los vestigios de la erupción de 1968 y aprender sobre la historia del volcán.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $itemOrder = 1;
        foreach (['Arenal 1968 Volcano View and Lava Trails', 'La Fortuna Waterfall', 'Soda Fortuna', 'Mistico Park'] as $title) {
            $item = ItineraryItem::whereHas('translations', function ($q) use ($title) {
                $q->where('title', $title);
            })->first();
            if ($item) {
                DB::table('itinerary_item_itinerary')->insert([
                    'itinerary_id' => $nature->itinerary_id,
                    'itinerary_item_id' => $item->item_id,
                    'item_order' => $itemOrder++,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }

        DB::table('products')
            ->where('name', 'Nature Lover Combo 1 (Puentes Colgantes + Catarata de La Fortuna + Almuerzo + Caminata al Volcán Arenal)')
            ->update(['itinerary_id' => $nature->itinerary_id]);

        // === Minicombo 1 ===
        $mini1 = Itinerary::create(['is_active' => true]);
        DB::table('itinerary_translations')->insert([
            'itinerary_id' => $mini1->itinerary_id,
            'locale' => 'es',
            'name' => 'Minicombo 1',
            'description' => 'Comienza tu aventura costarricense de medio día con la recogida en tu alojamiento en el centro de La Fortuna. Luego, dirígete al Parque Mistico para una caminata de 2 horas entre las copas de los árboles de la selva tropical.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $itemOrder = 1;
        foreach (['Mistico Park', 'La Fortuna Waterfall', 'Soda Fortuna'] as $title) {
            $item = ItineraryItem::whereHas('translations', function ($q) use ($title) {
                $q->where('title', $title);
            })->first();
            if ($item) {
                DB::table('itinerary_item_itinerary')->insert([
                    'itinerary_id' => $mini1->itinerary_id,
                    'itinerary_item_id' => $item->item_id,
                    'item_order' => $itemOrder++,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }

        DB::table('products')
            ->where('name', 'Minicombo 1 (Puentes Colgantes + Catarata de La Fortuna + Almuerzo)')
            ->update(['itinerary_id' => $mini1->itinerary_id]);

        // === Minicombo 2 ===
        $mini2 = Itinerary::create(['is_active' => true]);
        DB::table('itinerary_translations')->insert([
            'itinerary_id' => $mini2->itinerary_id,
            'locale' => 'es',
            'name' => 'Minicombo 2',
            'description' => 'Comienza tu aventura costarricense de medio día con la recogida en tu alojamiento en el centro de La Fortuna. Luego, dirígete al Parque Arenal 1968 para una caminata de 2 horas.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $itemOrder = 1;
        foreach (['Arenal 1968 Volcano View and Lava Trails', 'La Fortuna Waterfall', 'Soda Fortuna'] as $title) {
            $item = ItineraryItem::whereHas('translations', function ($q) use ($title) {
                $q->where('title', $title);
            })->first();
            if ($item) {
                DB::table('itinerary_item_itinerary')->insert([
                    'itinerary_id' => $mini2->itinerary_id,
                    'itinerary_item_id' => $item->item_id,
                    'item_order' => $itemOrder++,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }

        DB::table('products')
            ->where('name', 'Minicombo 2 (Caminata al Volcán Arenal + Catarata de La Fortuna + Almuerzo)')
            ->update(['itinerary_id' => $mini2->itinerary_id]);

        // === Minicombo 3 ===
        $mini3 = Itinerary::create(['is_active' => true]);
        DB::table('itinerary_translations')->insert([
            'itinerary_id' => $mini3->itinerary_id,
            'locale' => 'es',
            'name' => 'Minicombo 3',
            'description' => 'Comienza tu aventura costarricense de medio día con la recogida en tu alojamiento en el centro de La Fortuna. Luego, dirígete a Safari Put In para tu experiencia de vida silvestre.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $itemOrder = 1;
        foreach (['Safari Put in', 'Portón del Río', 'La Fortuna Waterfall', 'Soda Fortuna'] as $title) {
            $item = ItineraryItem::whereHas('translations', function ($q) use ($title) {
                $q->where('title', $title);
            })->first();
            if ($item) {
                DB::table('itinerary_item_itinerary')->insert([
                    'itinerary_id' => $mini3->itinerary_id,
                    'itinerary_item_id' => $item->item_id,
                    'item_order' => $itemOrder++,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }

        DB::table('products')
            ->where('name', 'Minicombo 3 (Safari Flotante + Catarata de La Fortuna + Almuerzo)')
            ->update(['itinerary_id' => $mini3->itinerary_id]);

        // === Safari Flotante ===
        $safari = Itinerary::create(['is_active' => true]);
        DB::table('itinerary_translations')->insert([
            'itinerary_id' => $safari->itinerary_id,
            'locale' => 'es',
            'name' => 'Safari Flotante',
            'description' => 'El recorrido comienza con un viaje de 20 minutos desde Fortuna hasta el río. Flota por el río durante aproximadamente dos horas, durante las cuales tendrás la oportunidad de ver gran parte de la famosa vida silvestre de Costa Rica y tener una conversación interactiva con el guía.
El viaje en bote por el río Peñas Blancas ofrece una forma relajante de disfrutar de la abundante vida silvestre que rodea las orillas de este exuberante paraíso. Toma fotos del dosel del bosque lluvioso lleno de monos o observa más de cerca a un ave tropical con tus binoculares mientras flotas en el bote de remos. El naturalista remará y tú podrás relajarte como las iguanas cercanas que se asolean en las ramas.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $itemOrder = 1;
        foreach (['Safari Put in', 'Portón del Río'] as $title) {
            $item = ItineraryItem::whereHas('translations', function ($q) use ($title) {
                $q->where('title', $title);
            })->first();
            if ($item) {
                DB::table('itinerary_item_itinerary')->insert([
                    'itinerary_id' => $safari->itinerary_id,
                    'itinerary_item_id' => $item->item_id,
                    'item_order' => $itemOrder++,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }

        DB::table('products')
            ->where('name', 'Safari Flotante')
            ->update(['itinerary_id' => $safari->itinerary_id]);

        // === Caminata al Volcán Arenal ===
        $volcano = Itinerary::create(['is_active' => true]);
        DB::table('itinerary_translations')->insert([
            'itinerary_id' => $volcano->itinerary_id,
            'locale' => 'es',
            'name' => 'Caminata al Volcán Arenal',
            'description' => 'La caminata dura alrededor de 2.5 horas de senderismo en la base del Volcán Arenal. Para llegar al mirador, tenemos que caminar en medio del campo de lava seca de la erupción de 1968. Desde el mirador, se puede ver el Volcán Arenal y el Lago Arenal al otro lado.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $item = ItineraryItem::whereHas('translations', function ($q) {
            $q->where('title', 'Arenal 1968 Volcano View and Lava Trails');
        })->first();
        if ($item) {
            DB::table('itinerary_item_itinerary')->insert([
                'itinerary_id' => $volcano->itinerary_id,
                'itinerary_item_id' => $item->item_id,
                'item_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        DB::table('products')
            ->where('name', 'Caminata al Volcán Arenal')
            ->update(['itinerary_id' => $volcano->itinerary_id]);

        // === Puentes Colgantes ===
        $hanging = Itinerary::create(['is_active' => true]);
        DB::table('itinerary_translations')->insert([
            'itinerary_id' => $hanging->itinerary_id,
            'locale' => 'es',
            'name' => 'Puentes Colgantes',
            'description' => 'Un recorrido de 3.2 km que permitirá al visitante disfrutar cómodamente del atractivo bosque a lo largo de un sendero que cuenta con un total de 15 puentes, construidos con tecnología alemana. Nuestros puentes tienen un diseño ideal para proporcionar a nuestros visitantes una experiencia contemplativa en el bosque. La altura y la posición estratégica de cada uno de los puentes permiten observar en una luz favorable las diferentes atracciones y las miles de especies que habitan nuestra reserva, árboles, flores, plantas, insectos, mamíferos, reptiles, ranas y muchos otros que viven en un ecosistema en perfecto equilibrio.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $item = ItineraryItem::whereHas('translations', function ($q) {
            $q->where('title', 'Mistico Park');
        })->first();
        if ($item) {
            DB::table('itinerary_item_itinerary')->insert([
                'itinerary_id' => $hanging->itinerary_id,
                'itinerary_item_id' => $item->item_id,
                'item_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        DB::table('products')
            ->where('name', 'Puentes Colgantes')
            ->update(['itinerary_id' => $hanging->itinerary_id]);

        $this->command->info('✅ Itineraries seeded successfully');
    }
}
