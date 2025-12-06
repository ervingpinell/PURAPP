<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Tour;
use App\Models\TourPrice;
use App\Models\TourTranslation;
use App\Models\CustomerCategory;
use App\Models\Itinerary;
use App\Models\ItineraryItem;

class ToursSeeder extends Seeder
{
    private $translator = null;

    public function run(): void
    {
        $now = Carbon::now();

        // Verificar si DeepL está configurado
        $deeplKey = config('services.deepl.key');
        if ($deeplKey) {
            try {
                $this->translator = new \DeepL\Translator($deeplKey);
                $this->command->info('✅ DeepL translator initialized');
            } catch (\Exception $e) {
                $this->command->warn('⚠️  DeepL not available: ' . $e->getMessage());
            }
        } else {
            $this->command->warn('⚠️  DeepL API key not configured. Skipping translations.');
        }

        // Helper para obtener/crear horario y devolver ID
        $scheduleId = function (string $start, string $end, ?string $label = null) use ($now): int {
            DB::table('schedules')->updateOrInsert(
                ['start_time' => $start, 'end_time' => $end],
                ['label' => $label, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now]
            );

            return (int) DB::table('schedules')
                ->where('start_time', $start)
                ->where('end_time', $end)
                ->value('schedule_id');
        };

        // Helper para crear itinerario
        $createItinerary = function (string $name, array $items) use ($now): int {
            $itinerary = Itinerary::create(['is_active' => true]);

            // Traducción del itinerario
            DB::table('itinerary_translations')->insert([
                'itinerary_id' => $itinerary->itinerary_id,
                'locale' => 'es',
                'name' => $name,
                'description' => "Itinerario para {$name}",
            ]);

            foreach ($items as $index => $itemData) {
                $item = ItineraryItem::create(['is_active' => true]);

                // Traducción del ítem
                DB::table('itinerary_item_translations')->insert([
                    'item_id' => $item->item_id,
                    'locale' => 'es',
                    'title' => $itemData['title'],
                    'description' => $itemData['description'] ?? null,
                ]);

                DB::table('itinerary_item_itinerary')->insert([
                    'itinerary_id' => $itinerary->itinerary_id,
                    'itinerary_item_id' => $item->item_id,
                    'item_order' => $index + 1,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            return $itinerary->itinerary_id;
        };

        // === HORARIOS COMPARTIDOS ===
        $sharedAmId  = $scheduleId('07:30', '11:30', 'AM');
        $sharedPmId  = $scheduleId('13:00', '16:30', 'PM');
        $sharedMidId = $scheduleId('07:30', '13:30', 'AM');
        $nlc         = $scheduleId('07:30', '16:30', 'AM');

        // ========== Caminata al Volcán Arenal ==========
        $volcanoItineraryId = $createItinerary('Caminata Volcán', [
            ['title' => 'Salida', 'description' => 'Recogida en el hotel'],
            ['title' => 'Llegada al Parque', 'description' => 'Entrada al sendero'],
            ['title' => 'Caminata', 'description' => 'Recorrido por el bosque y coladas de lava'],
            ['title' => 'Mirador', 'description' => 'Vistas del volcán y el lago'],
            ['title' => 'Regreso', 'description' => 'Vuelta al hotel']
        ]);

        $volcano = Tour::create([
            'name'         => 'Caminata al Volcán Arenal',
            'overview'     => 'Descubre el Parque Nacional Volcán Arenal...',
            'length'       => 4,
            'max_capacity' => 12,
            'group_size'   => 12,
            'tour_type_id' => 2,
            'color'        => '#ABABAB',
            'is_active'    => true,
            'is_draft'     => false,
            'current_step' => 6, // 🆕 Completado
            'itinerary_id' => $volcanoItineraryId, // 🆕 Asignado
            'cutoff_hour'  => '18:00',
            'lead_days'    => 1,
        ]);

        $this->translateTour($volcano, ['en', 'fr', 'de', 'pt']);
        // 🆕 Usando slugs correctos: kid, infante
        $this->createPricesForTour($volcano, ['adult' => 75, 'kid' => 55, 'infante' => 0]);

        DB::table('schedule_tour')->insert([
            ['tour_id' => $volcano->tour_id, 'schedule_id' => $sharedAmId, 'is_active' => true, 'base_capacity' => 12, 'created_at' => $now, 'updated_at' => $now],
            ['tour_id' => $volcano->tour_id, 'schedule_id' => $sharedPmId, 'is_active' => true, 'base_capacity' => 12, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('tour_language_tour')->insert([
            ['tour_id' => $volcano->tour_id, 'tour_language_id' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['tour_id' => $volcano->tour_id, 'tour_language_id' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        foreach ([1, 2, 3, 4] as $a) {
            DB::table('amenity_tour')->insert([
                'tour_id' => $volcano->tour_id,
                'amenity_id' => $a,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }


        // ========== Safari Flotante ==========
        $safariItineraryId = $createItinerary('Safari Flotante', [
            ['title' => 'Salida', 'description' => 'Recogida en el hotel'],
            ['title' => 'Río Peñas Blancas', 'description' => 'Inicio del recorrido en balsa'],
            ['title' => 'Avistamiento', 'description' => 'Monos, aves, perezosos'],
            ['title' => 'Refrigerio', 'description' => 'Parada en finca local'],
            ['title' => 'Regreso', 'description' => 'Vuelta al hotel']
        ]);

        $safari = Tour::create([
            'name'         => 'Safari Flotante',
            'overview'     => 'Navega por el río Peñas Blancas...',
            'length'       => 4,
            'max_capacity' => 12,
            'group_size'   => 12,
            'tour_type_id' => 2,
            'color'        => '#4F8BD8',
            'is_active'    => true,
            'is_draft'     => false,
            'current_step' => 6,
            'itinerary_id' => $safariItineraryId,
            'cutoff_hour'  => '18:00',
            'lead_days'    => 1,
        ]);

        $this->translateTour($safari, ['en', 'fr', 'de', 'pt']);
        $this->createPricesForTour($safari, ['adult' => 60, 'kid' => 45, 'infante' => 0]);

        DB::table('schedule_tour')->insert([
            ['tour_id' => $safari->tour_id, 'schedule_id' => $sharedAmId, 'is_active' => true, 'base_capacity' => 12, 'created_at' => $now, 'updated_at' => $now],
            ['tour_id' => $safari->tour_id, 'schedule_id' => $sharedPmId, 'is_active' => true, 'base_capacity' => 12, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('tour_language_tour')->insert([
            ['tour_id' => $safari->tour_id, 'tour_language_id' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['tour_id' => $safari->tour_id, 'tour_language_id' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        foreach ([1, 2, 3, 4, 6] as $a) {
            DB::table('amenity_tour')->insert([
                'tour_id' => $safari->tour_id,
                'amenity_id' => $a,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ========== Puentes Colgantes ==========
        $hangingItineraryId = $createItinerary('Puentes Colgantes', [
            ['title' => 'Salida', 'description' => 'Recogida en el hotel'],
            ['title' => 'Parque Mistico', 'description' => 'Llegada a la reserva'],
            ['title' => 'Sendero', 'description' => 'Caminata por puentes colgantes'],
            ['title' => 'Regreso', 'description' => 'Vuelta al hotel']
        ]);

        $hanging = Tour::create([
            'name'         => 'Puentes Colgantes',
            'overview'     => 'Disfruta de un emocionante encuentro cercano con la vida silvestre...',
            'length'       => 4,
            'max_capacity' => 12,
            'group_size'   => 12,
            'tour_type_id' => 2,
            'color'        => '#56D454',
            'is_active'    => true,
            'is_draft'     => false,
            'current_step' => 6,
            'itinerary_id' => $hangingItineraryId,
            'cutoff_hour'  => '18:00',
            'lead_days'    => 1,
        ]);

        $this->translateTour($hanging, ['en', 'fr', 'de', 'pt']);
        $this->createPricesForTour($hanging, ['adult' => 82, 'kid' => 61, 'infante' => 0]);

        DB::table('schedule_tour')->insert([
            ['tour_id' => $hanging->tour_id, 'schedule_id' => $sharedAmId, 'is_active' => true, 'base_capacity' => 12, 'created_at' => $now, 'updated_at' => $now],
            ['tour_id' => $hanging->tour_id, 'schedule_id' => $sharedPmId, 'is_active' => true, 'base_capacity' => 12, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('tour_language_tour')->insert([
            ['tour_id' => $hanging->tour_id, 'tour_language_id' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['tour_id' => $hanging->tour_id, 'tour_language_id' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        foreach ([1, 2, 3, 4] as $a) {
            DB::table('amenity_tour')->insert([
                'tour_id' => $hanging->tour_id,
                'amenity_id' => $a,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ========== Nature Lover Combo 1 ==========
        $natureItineraryId = $createItinerary('Nature Lover Combo', [
            ['title' => 'Salida', 'description' => 'Recogida temprano'],
            ['title' => 'Volcán', 'description' => 'Caminata matutina'],
            ['title' => 'Catarata', 'description' => 'Visita y baño'],
            ['title' => 'Almuerzo', 'description' => 'Comida típica'],
            ['title' => 'Puentes', 'description' => 'Caminata vespertina'],
            ['title' => 'Regreso', 'description' => 'Vuelta al hotel']
        ]);

        $nature = Tour::create([
            'name'         => 'Nature Lover Combo 1 (Puentes Colgantes + Catarata de La Fortuna + Almuerzo + Caminata al Volcán Arenal)',
            'overview'     => 'Combina tres actividades llenas de aventura...',
            'length'       => 9,
            'max_capacity' => 12,
            'group_size'   => 12,
            'tour_type_id' => 1,
            'color'        => '#DC626D',
            'is_active'    => true,
            'is_draft'     => false,
            'current_step' => 6,
            'itinerary_id' => $natureItineraryId,
            'cutoff_hour'  => '18:00',
            'lead_days'    => 1,
        ]);

        $this->translateTour($nature, ['en', 'fr', 'de', 'pt']);
        $this->createPricesForTour($nature, ['adult' => 154, 'kid' => 115, 'infante' => 0]);

        DB::table('schedule_tour')->insert([
            ['tour_id' => $nature->tour_id, 'schedule_id' => $nlc, 'is_active' => true, 'base_capacity' => 12, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('tour_language_tour')->insert([
            ['tour_id' => $nature->tour_id, 'tour_language_id' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['tour_id' => $nature->tour_id, 'tour_language_id' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        foreach ([1, 2, 3, 4, 5] as $a) {
            DB::table('amenity_tour')->insert([
                'tour_id' => $nature->tour_id,
                'amenity_id' => $a,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }


        // ========== Minicombo 1 ==========
        $mini1ItineraryId = $createItinerary('Minicombo 1', [
            ['title' => 'Salida', 'description' => 'Recogida'],
            ['title' => 'Puentes', 'description' => 'Caminata'],
            ['title' => 'Catarata', 'description' => 'Visita'],
            ['title' => 'Almuerzo', 'description' => 'Comida típica'],
            ['title' => 'Regreso', 'description' => 'Vuelta']
        ]);

        $minicombo1 = Tour::create([
            'name'         => 'Minicombo 1 (Puentes Colgantes + Catarata de La Fortuna + Almuerzo)',
            'overview'     => 'Descubre las atracciones naturales...',
            'length'       => 6,
            'max_capacity' => 12,
            'group_size'   => 12,
            'tour_type_id' => 1,
            'color'        => '#DC626D',
            'is_active'    => true,
            'is_draft'     => false,
            'current_step' => 6,
            'itinerary_id' => $mini1ItineraryId,
            'cutoff_hour'  => '18:00',
            'lead_days'    => 1,
        ]);

        $this->translateTour($minicombo1, ['en', 'fr', 'de', 'pt']);
        $this->createPricesForTour($minicombo1, ['adult' => 136, 'kid' => 102, 'infante' => 0]);

        DB::table('schedule_tour')->insert([
            ['tour_id' => $minicombo1->tour_id, 'schedule_id' => $sharedMidId, 'is_active' => true, 'base_capacity' => 12, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('tour_language_tour')->insert([
            ['tour_id' => $minicombo1->tour_id, 'tour_language_id' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['tour_id' => $minicombo1->tour_id, 'tour_language_id' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        foreach ([1, 2, 3, 4, 5] as $a) {
            DB::table('amenity_tour')->insert([
                'tour_id' => $minicombo1->tour_id,
                'amenity_id' => $a,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ========== Minicombo 2 ==========
        $mini2ItineraryId = $createItinerary('Minicombo 2', [
            ['title' => 'Salida', 'description' => 'Recogida'],
            ['title' => 'Volcán', 'description' => 'Caminata'],
            ['title' => 'Catarata', 'description' => 'Visita'],
            ['title' => 'Almuerzo', 'description' => 'Comida'],
            ['title' => 'Regreso', 'description' => 'Vuelta']
        ]);

        $minicombo2 = Tour::create([
            'name'         => 'Minicombo 2 (Caminata al Volcán Arenal + Catarata de La Fortuna + Almuerzo)',
            'overview'     => 'Si has venido a Costa Rica por sus increíbles volcanes...',
            'length'       => 6,
            'max_capacity' => 12,
            'group_size'   => 12,
            'tour_type_id' => 1,
            'color'        => '#DC626D',
            'is_active'    => true,
            'is_draft'     => false,
            'current_step' => 6,
            'itinerary_id' => $mini2ItineraryId,
            'cutoff_hour'  => '18:00',
            'lead_days'    => 1,
        ]);

        $this->translateTour($minicombo2, ['en', 'fr', 'de', 'pt']);
        $this->createPricesForTour($minicombo2, ['adult' => 136, 'kid' => 102, 'infante' => 0]);

        DB::table('schedule_tour')->insert([
            ['tour_id' => $minicombo2->tour_id, 'schedule_id' => $sharedMidId, 'is_active' => true, 'base_capacity' => 12, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('tour_language_tour')->insert([
            ['tour_id' => $minicombo2->tour_id, 'tour_language_id' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['tour_id' => $minicombo2->tour_id, 'tour_language_id' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        foreach ([1, 2, 3, 4, 5] as $a) {
            DB::table('amenity_tour')->insert([
                'tour_id' => $minicombo2->tour_id,
                'amenity_id' => $a,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ========== Minicombo 3 ==========
        $mini3ItineraryId = $createItinerary('Minicombo 3', [
            ['title' => 'Salida', 'description' => 'Recogida'],
            ['title' => 'Safari', 'description' => 'Bote en río'],
            ['title' => 'Catarata', 'description' => 'Visita'],
            ['title' => 'Almuerzo', 'description' => 'Comida'],
            ['title' => 'Regreso', 'description' => 'Vuelta']
        ]);

        $minicombo3 = Tour::create([
            'name'         => 'Minicombo 3 (Safari Flotante + Catarata de La Fortuna + Almuerzo)',
            'overview'     => 'La Catarata La Fortuna, con sus 70 metros...',
            'length'       => 6,
            'max_capacity' => 12,
            'group_size'   => 12,
            'tour_type_id' => 1,
            'color'        => '#DC626D',
            'is_active'    => true,
            'is_draft'     => false,
            'current_step' => 6,
            'itinerary_id' => $mini3ItineraryId,
            'cutoff_hour'  => '18:00',
            'lead_days'    => 1,
        ]);

        $this->translateTour($minicombo3, ['en', 'fr', 'de', 'pt']);
        $this->createPricesForTour($minicombo3, ['adult' => 136, 'kid' => 102, 'infante' => 0]);

        DB::table('schedule_tour')->insert([
            ['tour_id' => $minicombo3->tour_id, 'schedule_id' => $sharedMidId, 'is_active' => true, 'base_capacity' => 12, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('tour_language_tour')->insert([
            ['tour_id' => $minicombo3->tour_id, 'tour_language_id' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['tour_id' => $minicombo3->tour_id, 'tour_language_id' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        foreach ([1, 2, 3, 4, 5, 6] as $a) {
            DB::table('amenity_tour')->insert([
                'tour_id' => $minicombo3->tour_id,
                'amenity_id' => $a,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        foreach ([7, 8, 9] as $a) {
            DB::table('excluded_amenity_tour')->insert([
                'tour_id' => $minicombo3->tour_id,
                'amenity_id' => $a,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->command->info('✅ Tours seeded successfully with itineraries, schedules and correct pricing');
    }

    /**
     * Translate tour name and overview to target locales using DeepL
     */
    private function translateTour(Tour $tour, array $targetLocales): void
    {
        if (!$this->translator) {
            $this->command->warn("⚠️  Skipping translations for tour: {$tour->name}");
            return;
        }

        foreach ($targetLocales as $locale) {
            try {
                $translatedName = $this->translator->translateText(
                    $tour->name,
                    'es',
                    $locale
                )->text;

                $translatedOverview = $this->translator->translateText(
                    $tour->overview,
                    'es',
                    $locale
                )->text;

                TourTranslation::create([
                    'tour_id' => $tour->tour_id,
                    'locale' => $locale,
                    'name' => $translatedName,
                    'overview' => $translatedOverview,
                ]);

                $this->command->info("  ✓ Translated to {$locale}: {$translatedName}");
            } catch (\Exception $e) {
                $this->command->error("  ✗ Failed to translate to {$locale}: " . $e->getMessage());
            }
        }
    }

    /**
     * Create prices for tour using customer categories
     */
    private function createPricesForTour(Tour $tour, array $prices): void
    {
        $categories = CustomerCategory::whereIn('slug', array_keys($prices))->get();

        foreach ($categories as $category) {
            if (isset($prices[$category->slug]) && $prices[$category->slug] >= 0) { // Changed > 0 to >= 0 to allow free categories like infant
                TourPrice::create([
                    'tour_id' => $tour->tour_id,
                    'category_id' => $category->category_id,
                    'price' => $prices[$category->slug],
                    'is_active' => true,
                    'min_quantity' => 0,
                    'max_quantity' => 12,
                    'valid_from' => null,
                    'valid_until' => null,
                ]);

                $this->command->info("  ✓ Price created for {$category->slug}: \${$prices[$category->slug]}");
            }
        }
    }
}
