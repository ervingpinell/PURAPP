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
