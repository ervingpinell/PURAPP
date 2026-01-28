<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductPricingStrategy;
use App\Models\ProductPricingRule;
use App\Models\CustomerCategory;

class PricingExamplesSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener categorías activas
        $adultCategory = CustomerCategory::where('slug', 'adult')->first();
        $childCategory = CustomerCategory::where('slug', 'kid')->first();

        if (!$adultCategory || !$childCategory) {
            $this->command->error('No se encontraron categorías de clientes (adult, kid). Ejecuta primero los seeders de categorías.');
            return;
        }

        // Obtener productos para ejemplos
        $products = Product::active()->limit(5)->get();

        if ($products->count() < 5) {
            $this->command->warn('Se necesitan al menos 5 productos activos. Solo se configurarán ' . $products->count());
        }

        // Ejemplo 1: Flat Rate
        if ($product1 = $products->get(0)) {
            $this->command->info("Configurando Flat Rate para: {$product1->name}");
            
            $strategy1 = ProductPricingStrategy::create([
                'product_id' => $product1->product_id,
                'strategy_type' => 'flat_rate',
                'is_active' => true,
                'priority' => 10,
            ]);

            ProductPricingRule::create([
                'strategy_id' => $strategy1->id,
                'min_passengers' => 1,
                'max_passengers' => 9,
                'price' => 300,
                'price_type' => 'per_group',
                'label' => 'Grupo Pequeño',
            ]);

            ProductPricingRule::create([
                'strategy_id' => $strategy1->id,
                'min_passengers' => 10,
                'max_passengers' => 20,
                'price' => 500,
                'price_type' => 'per_group',
                'label' => 'Grupo Grande',
            ]);
        }

        // Ejemplo 2: Per Person
        if ($product2 = $products->get(1)) {
            $this->command->info("Configurando Per Person para: {$product2->name}");
            
            $strategy2 = ProductPricingStrategy::create([
                'product_id' => $product2->product_id,
                'strategy_type' => 'per_person',
                'is_active' => true,
                'priority' => 10,
            ]);

            ProductPricingRule::create([
                'strategy_id' => $strategy2->id,
                'min_passengers' => 1,
                'max_passengers' => 99,
                'price' => 50,
                'price_type' => 'per_person',
            ]);
        }

        // Ejemplo 3: Per Category
        if ($product3 = $products->get(2)) {
            $this->command->info("Configurando Per Category para: {$product3->name}");
            
            $strategy3 = ProductPricingStrategy::create([
                'product_id' => $product3->product_id,
                'strategy_type' => 'per_category',
                'is_active' => true,
                'priority' => 10,
            ]);

            ProductPricingRule::create([
                'strategy_id' => $strategy3->id,
                'min_passengers' => 0,
                'max_passengers' => 99,
                'customer_category_id' => $adultCategory->category_id,
                'price' => 70,
                'price_type' => 'per_person',
                'label' => 'Adulto',
            ]);

            ProductPricingRule::create([
                'strategy_id' => $strategy3->id,
                'min_passengers' => 0,
                'max_passengers' => 99,
                'customer_category_id' => $childCategory->category_id,
                'price' => 35,
                'price_type' => 'per_person',
                'label' => 'Niño',
            ]);
        }

        // Ejemplo 4: Tiered
        if ($product4 = $products->get(3)) {
            $this->command->info("Configurando Tiered para: {$product4->name}");
            
            $strategy4 = ProductPricingStrategy::create([
                'product_id' => $product4->product_id,
                'strategy_type' => 'tiered',
                'is_active' => true,
                'priority' => 10,
            ]);

            ProductPricingRule::create([
                'strategy_id' => $strategy4->id,
                'min_passengers' => 2,
                'max_passengers' => 6,
                'price' => 100,
                'price_type' => 'per_person',
                'label' => 'Grupo Pequeño',
            ]);

            ProductPricingRule::create([
                'strategy_id' => $strategy4->id,
                'min_passengers' => 7,
                'max_passengers' => 10,
                'price' => 80,
                'price_type' => 'per_person',
                'label' => 'Grupo Grande',
            ]);
        }

        // Ejemplo 5: Tiered Per Category
        if ($product5 = $products->get(4)) {
            $this->command->info("Configurando Tiered Per Category para: {$product5->name}");
            
            $strategy5 = ProductPricingStrategy::create([
                'product_id' => $product5->product_id,
                'strategy_type' => 'tiered_per_category',
                'is_active' => true,
                'priority' => 10,
            ]);

            // Tier 1: 2-6 personas
            ProductPricingRule::create([
                'strategy_id' => $strategy5->id,
                'min_passengers' => 2,
                'max_passengers' => 6,
                'customer_category_id' => $adultCategory->category_id,
                'price' => 70,
                'price_type' => 'per_person',
                'label' => 'Adulto - Grupo Pequeño',
            ]);

            ProductPricingRule::create([
                'strategy_id' => $strategy5->id,
                'min_passengers' => 2,
                'max_passengers' => 6,
                'customer_category_id' => $childCategory->category_id,
                'price' => 50,
                'price_type' => 'per_person',
                'label' => 'Niño - Grupo Pequeño',
            ]);

            // Tier 2: 7-10 personas
            ProductPricingRule::create([
                'strategy_id' => $strategy5->id,
                'min_passengers' => 7,
                'max_passengers' => 10,
                'customer_category_id' => $adultCategory->category_id,
                'price' => 60,
                'price_type' => 'per_person',
                'label' => 'Adulto - Grupo Grande',
            ]);

            ProductPricingRule::create([
                'strategy_id' => $strategy5->id,
                'min_passengers' => 7,
                'max_passengers' => 10,
                'customer_category_id' => $childCategory->category_id,
                'price' => 40,
                'price_type' => 'per_person',
                'label' => 'Niño - Grupo Grande',
            ]);
        }

        $this->command->info('✅ Ejemplos de pricing creados exitosamente!');
    }
}
