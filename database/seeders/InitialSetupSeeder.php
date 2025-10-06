<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Amenity;
use App\Models\ItineraryItem;
use App\Models\TourLanguage;
use App\Models\TourType;

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

        // Tour Languages
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

        // Tour Types
        $tourTypes = [
            [
                'name'        => 'Día completo',
                'description' => 'La opción perfecta para personas que buscan una experiencia completa en un solo día',
                'duration'    => '6 a 9 horas',
                'is_active'   => true,
            ],
            [
                'name'        => 'Medio día',
                'description' => 'Tours ideales para una aventura rápida para quienes tienen poco tiempo o quieren realizar otras actividades en la tarde.',
                'duration'    => '2 a 4 horas',
                'is_active'   => true,
            ],
        ];

        foreach ($tourTypes as $type) {
            TourType::updateOrCreate(
                ['name' => $type['name']],
                array_merge($type, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        // Itinerary Items
        $itineraryItems = [
            [
                'title' => 'Arenal 1968 Volcano View and Lava Trails',
                'description' => 'Caminata por los senderos de lava del Volcán Arenal con vistas espectaculares.',
                'is_active' => true,
            ],
            [
                'title' => 'La Fortuna Waterfall',
                'description' => 'Visita a la impresionante cascada La Fortuna de 70 metros de altura.',
                'is_active' => true,
            ],
            [
                'title' => 'Soda Fortuna',
                'description' => 'Almuerzo típico costarricense en restaurante local.',
                'is_active' => true,
            ],
            [
                'title' => 'Mistico Park',
                'description' => 'Recorrido por puentes colgantes en el dosel del bosque tropical.',
                'is_active' => true,
            ],
            [
                'title' => 'Safari Put in',
                'description' => 'Punto de inicio del safari flotante en el río Peñas Blancas.',
                'is_active' => true,
            ],
            [
                'title' => 'Peñas Blancas River',
                'description' => 'Recorrido en balsa por el río observando vida silvestre.',
                'is_active' => true,
            ],
            [
                'title' => 'Portón del Río',
                'description' => 'Punto de salida del safari flotante.',
                'is_active' => true,
            ],
        ];

        foreach ($itineraryItems as $item) {
            ItineraryItem::updateOrCreate(
                ['title' => $item['title']],
                array_merge($item, ['created_at' => $now, 'updated_at' => $now])
            );
        }

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
            'Bebidas Alcohólicas',
        ];

        foreach ($amenities as $name) {
            Amenity::updateOrCreate(
                ['name' => $name],
                ['is_active' => true, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        $this->command->info('✅ Initial setup completed successfully');
    }
}
