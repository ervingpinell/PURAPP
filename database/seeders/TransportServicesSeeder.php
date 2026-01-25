<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Tour;
use App\Models\TourPrice;
use App\Models\TourTranslation;
use App\Models\CustomerCategory;
use App\Models\TourType;

class TransportServicesSeeder extends Seeder
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

        // Crear o obtener TourType "Transporte"
        // Check if TourType with "Transporte" translation already exists
        $transportType = DB::table('tour_type_translations')
            ->where('locale', 'es')
            ->where('name', 'Transporte')
            ->first();

        if ($transportType) {
            $transportTypeId = $transportType->tour_type_id;
            $this->command->info("✅ TourType 'Transporte' found with ID: {$transportTypeId}");
        } else {
            // Create new TourType
            $transportTypeId = DB::table('tour_types')->insertGetId([
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ], 'tour_type_id');

            // Create Spanish translation
            DB::table('tour_type_translations')->insert([
                'tour_type_id' => $transportTypeId,
                'locale' => 'es',
                'name' => 'Transporte',
                'description' => 'Servicios de transporte privado desde La Fortuna a diferentes destinos en Costa Rica.',
                'duration' => 'Variable',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Create English translation
            DB::table('tour_type_translations')->insert([
                'tour_type_id' => $transportTypeId,
                'locale' => 'en',
                'name' => 'Transportation',
                'description' => 'Private transportation services from La Fortuna to different destinations in Costa Rica.',
                'duration' => 'Variable',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $this->command->info("✅ TourType 'Transporte' created with ID: {$transportTypeId}");
        }

        // Helper para crear tour con traducción en español
        $createTour = function (string $name, string $overview, array $tourData) use ($now): Tour {
            $tourData['name'] = $name;
            $tourData['overview'] = $overview;

            $tour = Tour::create($tourData);

            // Create Spanish translation
            TourTranslation::create([
                'tour_id' => $tour->tour_id,
                'locale' => 'es',
                'name' => $name,
                'overview' => $overview,
            ]);

            return $tour;
        };

        // Definir rutas de transporte: [destino, precio]
        $routes = [
            ['Aeropuerto SJO', 175],
            ['Aeropuerto LIR', 185],
            ['Papagayo', 220],
            ['Manuel Antonio', 280],
            ['Tamarindo', 250],
            ['Jaco', 210],
            ['Monteverde', 200],
            ['Puerto Viejo', 300],
            ['Samara', 300],
            ['La Pavona', 230],
            ['Guapiles', 190],
        ];

        foreach ($routes as [$destination, $basePrice]) {
            $tourName = "Transporte Privado: La Fortuna → {$destination}";
            $overview = "Servicio de transporte privado desde La Fortuna hasta {$destination}. Viaje cómodo, seguro y puntual con conductor profesional. Horario flexible según su conveniencia.";

            $tour = $createTour($tourName, $overview, [
                'length' => 0, // Sin duración específica
                'max_capacity' => 8, // Capacidad típica de vehículo
                'group_size' => 8,
                'tour_type_id' => $transportTypeId,
                'color' => '#2563EB', // Azul para transportes
                'is_active' => true,
                'is_draft' => false,
                'current_step' => 6, // Completado
                'itinerary_id' => null, // Sin itinerario
                'cutoff_hour' => null, // Sin horario de corte
                'lead_days' => 0, // Puede reservarse el mismo día
            ]);

            // Traducir a inglés
            $this->translateTour($tour, ['en']);

            // Crear precios por número de personas (1-8 pasajeros)
            // El precio base es para 1-4 personas, luego incrementa
            $this->createTransportPrices($tour, $basePrice);

            // Idiomas: Español e Inglés
            DB::table('tour_language_tour')->insert([
                ['tour_id' => $tour->tour_id, 'tour_language_id' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now], // Español
                ['tour_id' => $tour->tour_id, 'tour_language_id' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now], // Inglés
            ]);

            // Amenidades básicas de transporte
            foreach ([1, 2] as $amenityId) { // Guía y transporte
                DB::table('amenity_tour')->insert([
                    'tour_id' => $tour->tour_id,
                    'amenity_id' => $amenityId,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $this->command->info("✅ Created transport: {$tourName}");
        }

        $this->command->info('✅ All transport services seeded successfully');
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
     * Create transport prices based on number of passengers
     * Price structure: base price for 1-4 pax, +25% for 5-6 pax, +50% for 7-8 pax
     */
    private function createTransportPrices(Tour $tour, float $basePrice): void
    {
        // Get adult category (standard passenger)
        $adultCategory = CustomerCategory::where('slug', 'adult')->first();

        if (!$adultCategory) {
            $this->command->error("  ✗ Adult category not found");
            return;
        }

        // Create price tiers based on passenger count
        $priceTiers = [
            ['min' => 1, 'max' => 4, 'price' => $basePrice],
            ['min' => 5, 'max' => 6, 'price' => $basePrice * 1.25],
            ['min' => 7, 'max' => 8, 'price' => $basePrice * 1.50],
        ];

        foreach ($priceTiers as $tier) {
            TourPrice::create([
                'tour_id' => $tour->tour_id,
                'category_id' => $adultCategory->category_id,
                'price' => $tier['price'],
                'is_active' => true,
                'min_quantity' => $tier['min'],
                'max_quantity' => $tier['max'],
                'valid_from' => null,
                'valid_until' => null,
            ]);

            $this->command->info("  ✓ Price tier {$tier['min']}-{$tier['max']} pax: \${$tier['price']}");
        }
    }
}
