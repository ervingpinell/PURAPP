<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tour;

class AssignViatorCodesSeeder extends Seeder
{
    public function run(): void
    {
        // Mapeo de nombres de tours a códigos Viator
        $tourCodes = [
            'Caminata al Volcán Arenal' => '12732P1',
            'Safari Flotante' => '12732P3',
            'Puentes Colgantes' => '12732P2',
            'Nature Lover Combo 1 (Puentes Colgantes + Catarata de La Fortuna + Almuerzo + Caminata al Volcán Arenal)' => '12732P5',
            'Minicombo 1 (Puentes Colgantes + Catarata de La Fortuna + Almuerzo)' => '12732P11',
            'Minicombo 2 (Caminata al Volcán Arenal + Catarata de La Fortuna + Almuerzo)' => '12732P10',
            'Minicombo 3 (Safari Flotante + Catarata de La Fortuna + Almuerzo)' => '12732P9',
        ];

        foreach ($tourCodes as $tourName => $code) {
            $tour = Tour::where('name', $tourName)->first();

            if ($tour) {
                $tour->viator_code = $code;
                $tour->save();
                $this->command->info("✔️ Tour '{$tourName}' actualizado con código Viator: {$code}");
            } else {
                $this->command->warn("⚠️ Tour '{$tourName}' no encontrado.");
            }
        }

        $this->command->info('✅ Códigos Viator asignados exitosamente.');
    }
}
