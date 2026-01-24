<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Seed default tax structure.
     * 
     * Tax rates and names should be customized during deployment
     * based on the client's country and business requirements.
     */
    public function run(): void
    {
        $taxes = [
            [
                'name' => 'Sales Tax',
                'code' => 'TAX',
                'rate' => 0.00,
                'type' => 'percentage',
                'apply_to' => 'subtotal',
                'is_inclusive' => false,
                'is_active' => false, // Disabled by default - configure during deployment
                'description' => 'General sales tax - configure rate based on jurisdiction',
                'sort_order' => 1,
            ],
            [
                'name' => 'Service Charge',
                'code' => 'SRV',
                'rate' => 0.00,
                'type' => 'percentage',
                'apply_to' => 'subtotal',
                'is_inclusive' => false,
                'is_active' => false, // Disabled by default - configure during deployment
                'description' => 'Service charge - configure rate as needed',
                'sort_order' => 2,
            ],
        ];

        foreach ($taxes as $taxData) {
            Tax::updateOrCreate(
                ['code' => $taxData['code']],
                $taxData
            );
        }

        $this->command->info('✓ Default tax structure created (disabled)');
        $this->command->warn('⚠ Configure tax rates during deployment based on jurisdiction');
    }
}
