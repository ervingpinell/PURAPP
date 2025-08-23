<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tour;

class AssignViatorCodesSeeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            1 => '12732P1',
            2 => '12732P3',
            3 => '12732P2',
            4 => '12732P5',
            5 => '12732P11',
            6 => '12732P10',
            7 => '12732P9',
        ];

        foreach ($codes as $id => $code) {
            $tour = Tour::find($id);
            if ($tour) {
                $tour->viator_code = $code;
                $tour->save();
                $this->command->info("✔️ Tour ID {$id} actualizado con código Viator: {$code}");
            } else {
                $this->command->warn("⚠️ Tour ID {$id} no encontrado.");
            }
        }

        $this->command->info('✅ Códigos Viator asignados exitosamente.');
    }
}
