<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Product;
use App\Models\ProductPrice; // 🆕 Updated from TourPrice
use App\Models\CustomerCategory;
use App\Models\Itinerary;
use App\Models\ItineraryItem;

class ProductsSeeder extends Seeder
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
            $itinerary = Itinerary::create([
                'name' => ['es' => $name], // Spatie set JSON
                'description' => ['es' => "Itinerario para {$name}"],
                'is_active' => true
            ]);

            foreach ($items as $index => $itemData) {
                $item = ItineraryItem::create([
                    'title' => ['es' => $itemData['title']],
                    'description' => ['es' => $itemData['description'] ?? null],
                    'is_active' => true
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

        // Helper para crear producto con traducción en español
        $createProduct = function (string $name, string $overview, array $productData): Product {
             // Generate slug for checking existence
             $slug = \Illuminate\Support\Str::slug($name);
             $product = Product::where('slug', $slug)->first();

             if ($product) {
                 // Update existing? Or just return?
                 // For seeding, maybe update.
                 $product->update($productData);
             } else {
                 $product = Product::create($productData);
             }

            // Spatie Translations (Setting default locale 'es')
            $product->setTranslation('name', 'es', $name);
            $product->setTranslation('overview', 'es', $overview);
            $product->save();

            return $product;
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

        $volcano = $createProduct('Caminata al Volcán Arenal', 'Descubre el Parque Nacional Volcán Arenal...', [
            'length'       => 4,
            'max_capacity' => 12,
            'group_size'   => 12,
            'product_type_id' => 2,
            'color'        => '#ABABAB',
            'is_active'    => true,
            'is_draft'     => false,
            'current_step' => 6,
            'itinerary_id' => $volcanoItineraryId,
            'cutoff_hour'  => '18:00',
            'lead_days'    => 1,
        ]);

        $this->translateProduct($volcano, ['en', 'fr', 'de', 'pt']);
        $this->createPricesForProduct($volcano, ['adult' => 75, 'kid' => 55, 'infante' => 0]);

        // Schedules
        $volcano->schedules()->syncWithoutDetaching([
            $sharedAmId => ['base_capacity' => 12, 'is_active' => true],
            $sharedPmId => ['base_capacity' => 12, 'is_active' => true]
        ]);

        // Languages
        $volcano->languages()->syncWithoutDetaching([
            1 => ['is_active' => true],
            2 => ['is_active' => true]
        ]);

        // Amenities
        $volcano->amenities()->syncWithoutDetaching(
            collect([1, 2, 3, 4])->mapWithKeys(fn($id) => [$id => ['is_active' => true]])->toArray()
        );


        // ========== Safari Flotante ==========
        $safariItineraryId = $createItinerary('Safari Flotante', [
            ['title' => 'Salida', 'description' => 'Recogida en el hotel'],
            ['title' => 'Río Peñas Blancas', 'description' => 'Inicio del recorrido en balsa'],
            ['title' => 'Avistamiento', 'description' => 'Monos, aves, perezosos'],
            ['title' => 'Refrigerio', 'description' => 'Parada en finca local'],
            ['title' => 'Regreso', 'description' => 'Vuelta al hotel']
        ]);

        $safari = $createProduct('Safari Flotante', 'Navega por el río Peñas Blancas...', [
            'length'       => 4,
            'max_capacity' => 12,
            'group_size'   => 12,
            'product_type_id' => 2,
            'color'        => '#4F8BD8',
            'is_active'    => true,
            'is_draft'     => false,
            'current_step' => 6,
            'itinerary_id' => $safariItineraryId,
            'cutoff_hour'  => '18:00',
            'lead_days'    => 1,
        ]);

        $this->translateProduct($safari, ['en', 'fr', 'de', 'pt']);
        $this->createPricesForProduct($safari, ['adult' => 60, 'kid' => 45, 'infante' => 0]);

        // Schedules
        $safari->schedules()->syncWithoutDetaching([
            $sharedAmId => ['base_capacity' => 12, 'is_active' => true],
            $sharedPmId => ['base_capacity' => 12, 'is_active' => true]
        ]);

        // Languages
        $safari->languages()->syncWithoutDetaching([
            1 => ['is_active' => true],
            2 => ['is_active' => true]
        ]);

        // Amenities
        $safari->amenities()->syncWithoutDetaching(
            collect([1, 2, 3, 4, 6])->mapWithKeys(fn($id) => [$id => ['is_active' => true]])->toArray()
        );

        // ========== Puentes Colgantes ==========
        $hangingItineraryId = $createItinerary('Puentes Colgantes', [
            ['title' => 'Salida', 'description' => 'Recogida en el hotel'],
            ['title' => 'Parque Mistico', 'description' => 'Llegada a la reserva'],
            ['title' => 'Sendero', 'description' => 'Caminata por puentes colgantes'],
            ['title' => 'Regreso', 'description' => 'Vuelta al hotel']
        ]);

        $hanging = $createProduct('Puentes Colgantes', 'Disfruta de un emocionante encuentro cercano con la vida silvestre...', [
            'length'       => 4,
            'max_capacity' => 12,
            'group_size'   => 12,
            'product_type_id' => 2,
            'color'        => '#56D454',
            'is_active'    => true,
            'is_draft'     => false,
            'current_step' => 6,
            'itinerary_id' => $hangingItineraryId,
            'cutoff_hour'  => '18:00',
            'lead_days'    => 1,
        ]);

        $this->translateProduct($hanging, ['en', 'fr', 'de', 'pt']);
        $this->createPricesForProduct($hanging, ['adult' => 82, 'kid' => 61, 'infante' => 0]);

        // Schedules
        $hanging->schedules()->syncWithoutDetaching([
            $sharedAmId => ['base_capacity' => 12, 'is_active' => true],
            $sharedPmId => ['base_capacity' => 12, 'is_active' => true]
        ]);

        // Languages
        $hanging->languages()->syncWithoutDetaching([
            1 => ['is_active' => true],
            2 => ['is_active' => true]
        ]);

        // Amenities
        $hanging->amenities()->syncWithoutDetaching(
             collect([1, 2, 3, 4])->mapWithKeys(fn($id) => [$id => ['is_active' => true]])->toArray()
        );

        // ========== Nature Lover Combo 1 ==========
        $natureItineraryId = $createItinerary('Nature Lover Combo', [
            ['title' => 'Salida', 'description' => 'Recogida temprano'],
            ['title' => 'Volcán', 'description' => 'Caminata matutina'],
            ['title' => 'Catarata', 'description' => 'Visita y baño'],
            ['title' => 'Almuerzo', 'description' => 'Comida típica'],
            ['title' => 'Puentes', 'description' => 'Caminata vespertina'],
            ['title' => 'Regreso', 'description' => 'Vuelta al hotel']
        ]);

        $nature = $createProduct('Nature Lover Combo 1 (Puentes Colgantes + Catarata de La Fortuna + Almuerzo + Caminata al Volcán Arenal)', 'Combina tres actividades llenas de aventura...', [
            'length'       => 9,
            'max_capacity' => 12,
            'group_size'   => 12,
            'product_type_id' => 1,
            'color'        => '#DC626D',
            'is_active'    => true,
            'is_draft'     => false,
            'current_step' => 6,
            'itinerary_id' => $natureItineraryId,
            'cutoff_hour'  => '18:00',
            'lead_days'    => 1,
        ]);

        $this->translateProduct($nature, ['en', 'fr', 'de', 'pt']);
        $this->createPricesForProduct($nature, ['adult' => 154, 'kid' => 115, 'infante' => 0]);

        // Schedules
        $nature->schedules()->syncWithoutDetaching([
            $nlc => ['base_capacity' => 12, 'is_active' => true]
        ]);

        // Languages
        $nature->languages()->syncWithoutDetaching([
            1 => ['is_active' => true],
            2 => ['is_active' => true]
        ]);

        // Amenities
        $nature->amenities()->syncWithoutDetaching(
             collect([1, 2, 3, 4, 5])->mapWithKeys(fn($id) => [$id => ['is_active' => true]])->toArray()
        );


        // ========== Minicombo 1 ==========
        $mini1ItineraryId = $createItinerary('Minicombo 1', [
            ['title' => 'Salida', 'description' => 'Recogida'],
            ['title' => 'Puentes', 'description' => 'Caminata'],
            ['title' => 'Catarata', 'description' => 'Visita'],
            ['title' => 'Almuerzo', 'description' => 'Comida típica'],
            ['title' => 'Regreso', 'description' => 'Vuelta']
        ]);

        $minicombo1 = $createProduct('Minicombo 1 (Puentes Colgantes + Catarata de La Fortuna + Almuerzo)', 'Descubre las atracciones naturales...', [
            'length'       => 6,
            'max_capacity' => 12,
            'group_size'   => 12,
            'product_type_id' => 1,
            'color'        => '#DC626D',
            'is_active'    => true,
            'is_draft'     => false,
            'current_step' => 6,
            'itinerary_id' => $mini1ItineraryId,
            'cutoff_hour'  => '18:00',
            'lead_days'    => 1,
        ]);

        $this->translateProduct($minicombo1, ['en', 'fr', 'de', 'pt']);
        $this->createPricesForProduct($minicombo1, ['adult' => 136, 'kid' => 102, 'infante' => 0]);

        // Schedules_
        $minicombo1->schedules()->syncWithoutDetaching([
            $sharedMidId => ['base_capacity' => 12, 'is_active' => true]
        ]);

        // Languages
        $minicombo1->languages()->syncWithoutDetaching([
            1 => ['is_active' => true],
            2 => ['is_active' => true]
        ]);

        // Amenities
        $minicombo1->amenities()->syncWithoutDetaching(
             collect([1, 2, 3, 4, 5])->mapWithKeys(fn($id) => [$id => ['is_active' => true]])->toArray()
        );

        // ========== Minicombo 2 ==========
        $mini2ItineraryId = $createItinerary('Minicombo 2', [
            ['title' => 'Salida', 'description' => 'Recogida'],
            ['title' => 'Volcán', 'description' => 'Caminata'],
            ['title' => 'Catarata', 'description' => 'Visita'],
            ['title' => 'Almuerzo', 'description' => 'Comida'],
            ['title' => 'Regreso', 'description' => 'Vuelta']
        ]);

        $minicombo2 = $createProduct('Minicombo 2 (Caminata al Volcán Arenal + Catarata de La Fortuna + Almuerzo)', 'Si has venido a Costa Rica por sus increíbles volcanes...', [
            'length'       => 6,
            'max_capacity' => 12,
            'group_size'   => 12,
            'product_type_id' => 1,
            'color'        => '#DC626D',
            'is_active'    => true,
            'is_draft'     => false,
            'current_step' => 6,
            'itinerary_id' => $mini2ItineraryId,
            'cutoff_hour'  => '18:00',
            'lead_days'    => 1,
        ]);

        $this->translateProduct($minicombo2, ['en', 'fr', 'de', 'pt']);
        $this->createPricesForProduct($minicombo2, ['adult' => 136, 'kid' => 102, 'infante' => 0]);

        // Schedules
        $minicombo2->schedules()->syncWithoutDetaching([
            $sharedMidId => ['base_capacity' => 12, 'is_active' => true]
        ]);

        // Languages
        $minicombo2->languages()->syncWithoutDetaching([
            1 => ['is_active' => true],
            2 => ['is_active' => true]
        ]);

        // Amenities
        $minicombo2->amenities()->syncWithoutDetaching(
             collect([1, 2, 3, 4, 5])->mapWithKeys(fn($id) => [$id => ['is_active' => true]])->toArray()
        );

        // ========== Minicombo 3 ==========
        $mini3ItineraryId = $createItinerary('Minicombo 3', [
            ['title' => 'Salida', 'description' => 'Recogida'],
            ['title' => 'Safari', 'description' => 'Bote en río'],
            ['title' => 'Catarata', 'description' => 'Visita'],
            ['title' => 'Almuerzo', 'description' => 'Comida'],
            ['title' => 'Regreso', 'description' => 'Vuelta']
        ]);

        $minicombo3 = $createProduct('Minicombo 3 (Safari Flotante + Catarata de La Fortuna + Almuerzo)', 'La Catarata La Fortuna, con sus 70 metros...', [
            'length'       => 6,
            'max_capacity' => 12,
            'group_size'   => 12,
            'product_type_id' => 1,
            'color'        => '#DC626D',
            'is_active'    => true,
            'is_draft'     => false,
            'current_step' => 6,
            'itinerary_id' => $mini3ItineraryId,
            'cutoff_hour'  => '18:00',
            'lead_days'    => 1,
        ]);

        $this->translateProduct($minicombo3, ['en', 'fr', 'de', 'pt']);
        $this->createPricesForProduct($minicombo3, ['adult' => 136, 'kid' => 102, 'infante' => 0]);

        // Schedules
        $minicombo3->schedules()->syncWithoutDetaching([
            $sharedMidId => ['base_capacity' => 12, 'is_active' => true]
        ]);

        // Languages
        $minicombo3->languages()->syncWithoutDetaching([
            1 => ['is_active' => true],
            2 => ['is_active' => true]
        ]);

        // Amenities
        $minicombo3->amenities()->syncWithoutDetaching(
             collect([1, 2, 3, 4, 5, 6])->mapWithKeys(fn($id) => [$id => ['is_active' => true]])->toArray()
        );
        
        $this->command->info('✅ Products seeded successfully with itineraries, schedules and correct pricing');
    }

    /**
     * Translate product name and overview to target locales using DeepL
     */
    private function translateProduct(Product $product, array $targetLocales): void
    {
        if (!$this->translator) {
            $this->command->warn("⚠️  Skipping translations for product: {$product->name}");
            return;
        }

        foreach ($targetLocales as $locale) {
            try {
                $translatedName = $this->translator->translateText(
                    $product->getTranslation('name', 'es'),
                    'es',
                    $locale
                )->text;

                $translatedOverview = $this->translator->translateText(
                    $product->getTranslation('overview', 'es'),
                    'es',
                    $locale
                )->text;

                $product->setTranslation('name', $locale, $translatedName);
                $product->setTranslation('overview', $locale, $translatedOverview);
                $product->save();

                $this->command->info("  ✓ Translated to {$locale}: {$translatedName}");
            } catch (\Exception $e) {
                $this->command->error("  ✗ Failed to translate to {$locale}: " . $e->getMessage());
            }
        }
    }

    /**
     * Create prices for product using customer categories
     */
    private function createPricesForProduct(Product $product, array $prices): void
    {
        $categories = CustomerCategory::whereIn('slug', array_keys($prices))->get();

        foreach ($categories as $category) {
            if (isset($prices[$category->slug]) && $prices[$category->slug] >= 0) {
                ProductPrice::create([
                    'product_id' => $product->product_id,
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
