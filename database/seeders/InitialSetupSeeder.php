<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class InitialSetupSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();


        // ✅ 5) Insertar Hotels
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

        foreach ($hotels as $name) {
            DB::table('hotels_list')->updateOrInsert(
                ['name' => $name],
                ['is_active' => true, 'created_at' => $now, 'updated_at' => $now]
            );
        }

     

        DB::table('tour_languages')->insertOrIgnore([
            ['name'=>'Español','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['name'=>'English','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
        ]);

        DB::table('tour_types')->insertOrIgnore([
            ['name'=>'Full Day','description'=>'6 a 9 horas','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['name'=>'Half Day','description'=>'2 a 4 horas','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
        ]);

        foreach ([
            'Arenal 1968 Volcano View and Lava Trails',
            'La Fortuna Waterfall','Soda Fortuna',
            'Mistico Park','Safari Put in',
            'Peñas Blancas River','Portón del Río'
        ] as $title) {
            DB::table('itinerary_items')->updateOrInsert(
                ['title'=>$title],
                ['description'=>'Description for '.$title,'is_active'=>true,'created_at'=>$now,'updated_at'=>$now]
            );
        }

        foreach ([
            'Agua Embotellada','Guía Bilingüe','Transporte',
            'Tickets de entradas','Almuerzo','Snack',
            'Equipo óptico','Desayuno','Bebidas Alcoholicas'
        ] as $name) {
            DB::table('amenities')->updateOrInsert(
                ['name'=>$name],
                ['is_active'=>true,'created_at'=>$now,'updated_at'=>$now]
            );
        }

    }
}
