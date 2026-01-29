<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductType;
use App\Models\ProductTypeSubcategory;

class ProductTypeSubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Creando subtipos de producto...');

        // Get product types
        $productType = ProductType::where('name->es', 'LIKE', '%Tour%')->first();
        $transferType = ProductType::where('name->es', 'LIKE', '%Transfer%')->first();
        $activityType = ProductType::where('name->es', 'LIKE', '%Actividad%')->first();

        // Product Subtypes
        if ($productType) {
            $this->command->info("📍 Creando subtipos para: {$productType->getTranslatedName()}");
            
            ProductTypeSubcategory::updateOrCreate(
                ['slug' => 'full-day'],
                [
                    'product_type_id' => $productType->product_type_id,
                    'name' => [
                        'es' => 'Día Completo',
                        'en' => 'Full Day',
                        'fr' => 'Journée Complète',
                        'pt' => 'Dia Inteiro',
                        'de' => 'Ganztägig'
                    ],
                    'description' => 'Productos de día completo (6+ horas)',
                    'meta_title' => [
                        'es' => 'Productos de Día Completo',
                        'en' => 'Full Day Products'
                    ],
                    'meta_description' => [
                        'es' => 'Explora nuestros tours de día completo con experiencias inolvidables',
                        'en' => 'Explore our full day tours with unforgettable experiences'
                    ],
                    'icon' => 'fas fa-sun',
                    'color' => '#FFA500',
                    'sort_order' => 1,
                    'is_active' => true,
                ]
            );

            ProductTypeSubcategory::updateOrCreate(
                ['slug' => 'half-day'],
                [
                    'product_type_id' => $productType->product_type_id,
                    'name' => [
                        'es' => 'Medio Día',
                        'en' => 'Half Day',
                        'fr' => 'Demi-Journée',
                        'pt' => 'Meio Dia',
                        'de' => 'Halbtägig'
                    ],
                    'description' => 'Productos de medio día (2-5 horas)',
                    'meta_title' => [
                        'es' => 'Productos de Medio Día',
                        'en' => 'Half Day Products'
                    ],
                    'meta_description' => [
                        'es' => 'Descubre nuestros tours de medio día perfectos para tu itinerario',
                        'en' => 'Discover our half day tours perfect for your itinerary'
                    ],
                    'icon' => 'fas fa-clock',
                    'color' => '#4CAF50',
                    'sort_order' => 2,
                    'is_active' => true,
                ]
            );

            ProductTypeSubcategory::updateOrCreate(
                ['slug' => 'multi-day'],
                [
                    'product_type_id' => $productType->product_type_id,
                    'name' => [
                        'es' => 'Multi-Día',
                        'en' => 'Multi-Day',
                        'fr' => 'Multi-Jours',
                        'pt' => 'Multi-Dia',
                        'de' => 'Mehrtägig'
                    ],
                    'description' => 'Productos de múltiples días',
                    'icon' => 'fas fa-calendar-alt',
                    'color' => '#9C27B0',
                    'sort_order' => 3,
                    'is_active' => true,
                ]
            );

            ProductTypeSubcategory::updateOrCreate(
                ['slug' => 'night'],
                [
                    'product_type_id' => $productType->product_type_id,
                    'name' => [
                        'es' => 'Nocturno',
                        'en' => 'Night Product',
                        'fr' => 'Produit de Nuit',
                        'pt' => 'Produto Noturno',
                        'de' => 'Nachtprodukt'
                    ],
                    'description' => 'Productos nocturnos',
                    'icon' => 'fas fa-moon',
                    'color' => '#1A237E',
                    'sort_order' => 4,
                    'is_active' => true,
                ]
            );
        }

        // Transfers Subtypes
        if ($transferType) {
            $this->command->info("📍 Creando subtipos para: {$transferType->getTranslatedName()}");
            
            ProductTypeSubcategory::updateOrCreate(
                ['slug' => 'private'],
                [
                    'product_type_id' => $transferType->product_type_id,
                    'name' => [
                        'es' => 'Privado',
                        'en' => 'Private',
                        'fr' => 'Privé',
                        'pt' => 'Privado',
                        'de' => 'Privat'
                    ],
                    'description' => 'Transfers privados exclusivos',
                    'icon' => 'fas fa-car',
                    'color' => '#2196F3',
                    'sort_order' => 1,
                    'is_active' => true,
                ]
            );

            ProductTypeSubcategory::updateOrCreate(
                ['slug' => 'shared'],
                [
                    'product_type_id' => $transferType->product_type_id,
                    'name' => [
                        'es' => 'Compartido',
                        'en' => 'Shared Shuttle',
                        'fr' => 'Navette Partagée',
                        'pt' => 'Transporte Compartilhado',
                        'de' => 'Geteilter Shuttle'
                    ],
                    'description' => 'Transfers compartidos económicos',
                    'icon' => 'fas fa-bus',
                    'color' => '#FF9800',
                    'sort_order' => 2,
                    'is_active' => true,
                ]
            );
        }

        // Activities Subtypes
        if ($activityType) {
            $this->command->info("📍 Creando subtipos para: {$activityType->getTranslatedName()}");
            
            ProductTypeSubcategory::updateOrCreate(
                 ['slug' => 'extreme'],
                 [
                    'product_type_id' => $activityType->product_type_id,
                    'name' => [
                        'es' => 'Extremo',
                        'en' => 'Extreme',
                        'fr' => 'Extrême',
                        'pt' => 'Extremo',
                        'de' => 'Extrem'
                    ],
                    'description' => 'Actividades de aventura extrema',
                    'icon' => 'fas fa-mountain',
                    'color' => '#F44336',
                    'sort_order' => 1,
                    'is_active' => true,
                ]
            );

            ProductTypeSubcategory::updateOrCreate(
                ['slug' => 'water'],
                [
                    'product_type_id' => $activityType->product_type_id,
                    'name' => [
                        'es' => 'Acuático',
                        'en' => 'Water Activities',
                        'fr' => 'Activités Aquatiques',
                        'pt' => 'Atividades Aquáticas',
                        'de' => 'Wasseraktivitäten'
                    ],
                    'description' => 'Actividades acuáticas',
                    'icon' => 'fas fa-water',
                    'color' => '#00BCD4',
                    'sort_order' => 2,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('✅ Subtipos de producto creados correctamente');
    }
}
