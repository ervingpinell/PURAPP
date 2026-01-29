<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Amenity;
use App\Models\ItineraryItem;
use App\Models\TourLanguage;
use App\Models\ProductType;

class InitialSetupSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Hotels List
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

        foreach ($hotels as $index => $name) {
            DB::table('hotels_list')->updateOrInsert(
                ['name' => $name],
                [
                    'is_active' => true,
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            );
        }

        // Product Languages
        $languages = [
            ['name' => 'Español', 'is_active' => true],
            ['name' => 'English', 'is_active' => true],
        ];

        foreach ($languages as $lang) {
            TourLanguage::updateOrCreate(
                ['name' => $lang['name']],
                array_merge($lang, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        // Product Types (with translations)
        $productTypes = [
            [
                'is_active'   => true,
                'translations' => [
                    'es' => [
                        'name' => 'Día completo',
                        'description' => 'La opción perfecta para personas que buscan una experiencia completa en un solo día',
                        'duration' => '8 Horas',
                    ],
                    'en' => [
                        'name' => 'Full Day',
                        'description' => 'The perfect option for people looking for a complete experience in a single day',
                        'duration' => '8 Hours',
                    ],
                ],
            ],
            [
                'is_active'   => true,
                'translations' => [
                    'es' => [
                        'name' => 'Medio día',
                        'description' => 'Productos ideales para una aventura rápida para quienes tienen poco tiempo o quieren realizar otras actividades en la tarde.',
                        'duration' => '4 horas',
                    ],
                    'en' => [
                        'name' => 'Half Day',
                        'description' => 'Ideal tours for a quick adventure for those with little time or who want to do other activities in the afternoon.',
                        'duration' => '4 Hours',
                    ],
                ],
            ],
        ];

        foreach ($productTypes as $typeData) {
            $translations = $typeData['translations'];
            unset($typeData['translations']);

            // Create product type with only is_active
            $type = ProductType::create(array_merge($typeData, ['created_at' => $now, 'updated_at' => $now]));

            // Create translations
            foreach ($translations as $locale => $trans) {
                DB::table('tour_type_translations')->insert([
                    'tour_type_id' => $type->tour_type_id,
                    'locale' => $locale,
                    'name' => $trans['name'],
                    'description' => $trans['description'],
                    'duration' => $trans['duration'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }

        // Itinerary Items (with translations)
        $itineraryItems = [
            [
                'is_active' => true,
                'translations' => [
                    'es' => [
                        'title' => 'Arenal 1968 Volcano View and Lava Trails',
                        'description' => 'Caminata por los senderos de lava del Volcán Arenal con vistas espectaculares.',
                    ],
                    'en' => [
                        'title' => 'Arenal 1968 Volcano View and Lava Trails',
                        'description' => 'Hike through the Arenal Volcano lava trails with spectacular views.',
                    ],
                ],
            ],
            [
                'is_active' => true,
                'translations' => [
                    'es' => [
                        'title' => 'La Fortuna Waterfall',
                        'description' => 'Visita a la impresionante cascada La Fortuna de 70 metros de altura.',
                    ],
                    'en' => [
                        'title' => 'La Fortuna Waterfall',
                        'description' => 'Visit the impressive 70-meter high La Fortuna waterfall.',
                    ],
                ],
            ],
        ];

        foreach ($itineraryItems as $itemData) {
            $translations = $itemData['translations'];
            unset($itemData['translations']);

            $item = ItineraryItem::create(array_merge($itemData, ['created_at' => $now, 'updated_at' => $now]));

            foreach ($translations as $locale => $trans) {
                DB::table('itinerary_item_translations')->insert(
                    array_merge($trans, [
                        'item_id' => $item->item_id,
                        'locale' => $locale,
                        'created_at' => $now,
                        'updated_at' => $now
                    ])
                );
            }
        }

        // Amenities (with translations)
        $amenities = [
            ['es' => 'Agua Embotellada', 'en' => 'Bottled Water'],
            ['es' => 'Guía Bilingüe', 'en' => 'Bilingual Guide'],
            ['es' => 'Transporte', 'en' => 'Transportation'],
            ['es' => 'Tickets de entradas', 'en' => 'Entrance Tickets'],
            ['es' => 'Almuerzo', 'en' => 'Lunch'],
            ['es' => 'Snack', 'en' => 'Snack'],
            ['es' => 'Equipo óptico', 'en' => 'Optical Equipment'],
        ];

        foreach ($amenities as $amenityTrans) {
            $amenity = Amenity::create([
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ]);

            foreach ($amenityTrans as $locale => $name) {
                DB::table('amenity_translations')->insert([
                    'amenity_id' => $amenity->amenity_id,
                    'locale' => $locale,
                    'name' => $name,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }

        $this->command->info('✅ Initial setup completed successfully');
    }
}
