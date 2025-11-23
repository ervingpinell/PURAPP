<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxes = [
            [
                'name' => 'IVA 13%',
                'code' => 'IVA',
                'rate' => 13.00,
                'type' => 'percentage',
                'apply_to' => 'subtotal',
                'is_inclusive' => false,
                'is_active' => true,
                'description' => 'Impuesto al Valor Agregado de Costa Rica',
                'sort_order' => 1,
            ],
            [
                'name' => 'Servicio 10%',
                'code' => 'SRV',
                'rate' => 10.00,
                'type' => 'percentage',
                'apply_to' => 'subtotal',
                'is_inclusive' => false,
                'is_active' => true,
                'description' => 'Cargo por servicio',
                'sort_order' => 2,
            ],
            [
                'name' => 'Impuesto Turístico',
                'code' => 'TUR',
                'rate' => 5.00,
                'type' => 'percentage',
                'apply_to' => 'subtotal',
                'is_inclusive' => false,
                'is_active' => false, // Desactivado por defecto
                'description' => 'Impuesto turístico opcional',
                'sort_order' => 3,
            ],
        ];

        foreach ($taxes as $taxData) {
            Tax::updateOrCreate(
                ['code' => $taxData['code']],
                $taxData
            );
        }
    }
}
