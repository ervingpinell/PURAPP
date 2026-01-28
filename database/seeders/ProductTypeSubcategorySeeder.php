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
        $tourType = ProductType::where('name->es', 'LIKE', '%Tour%')->first();
        $transferType = ProductType::where('name->es', 'LIKE', '%Transfer%')->first();
        $activityType = ProductType::where('name->es', 'LIKE', '%Actividad%')->first();

        // Tours Subtypes
        if ($tourType) {
            $this->command->info("📍 Creando subtipos para: {$tourType->getTranslatedName()}");
            
            ProductTypeSubcategory::create([
                'product_type_id' => $tourType->product_type_id,
                'name' => [
                    'es' => 'Día Completo',
                    'en' => 'Full Day',
                    'fr' => 'Journée Complète',
                    'pt' => 'Dia Inteiro',
                    'de' => 'Ganztägig'
                ],
                'slug' => 'full-day',
                'description' => 'Tours de día completo (6+ horas)',
                'meta_title' => [
                    'es' => 'Tours de Día Completo',
                    'en' => 'Full Day Tours'
                ],
                'meta_description' => [
                    'es' => 'Explora nuestros tours de día completo con experiencias inolvidables',
                    'en' => 'Explore our full day tours with unforgettable experiences'
                ],
                'icon' => 'fas fa-sun',
                'color' => '#FFA500',
                'sort_order' => 1,
                'is_active' => true,
            ]);

            ProductTypeSubcategory::create([
                'product_type_id' => $tourType->product_type_id,
                'name' => [
                    'es' => 'Medio Día',
                    'en' => 'Half Day',
                    'fr' => 'Demi-Journée',
                    'pt' => 'Meio Dia',
                    'de' => 'Halbtägig'
                ],
                'slug' => 'half-day',
                'description' => 'Tours de medio día (2-5 horas)',
                'meta_title' => [
                    'es' => 'Tours de Medio Día',
                    'en' => 'Half Day Tours'
                ],
                'meta_description' => [
                    'es' => 'Descubre nuestros tours de medio día perfectos para tu itinerario',
                    'en' => 'Discover our half day tours perfect for your itinerary'
                ],
                'icon' => 'fas fa-clock',
                'color' => '#4CAF50',
                'sort_order' => 2,
                'is_active' => true,
            ]);

            ProductTypeSubcategory::create([
                'product_type_id' => $tourType->product_type_id,
                'name' => [
                    'es' => 'Multi-Día',
                    'en' => 'Multi-Day',
                    'fr' => 'Multi-Jours',
                    'pt' => 'Multi-Dia',
                    'de' => 'Mehrtägig'
                ],
                'slug' => 'multi-day',
                'description' => 'Tours de múltiples días',
                'icon' => 'fas fa-calendar-alt',
                'color' => '#9C27B0',
                'sort_order' => 3,
                'is_active' => true,
            ]);

            ProductTypeSubcategory::create([
                'product_type_id' => $tourType->product_type_id,
                'name' => [
                    'es' => 'Nocturno',
                    'en' => 'Night Tour',
                    'fr' => 'Tour de Nuit',
                    'pt' => 'Tour Noturno',
                    'de' => 'Nachttour'
                ],
                'slug' => 'night',
                'description' => 'Tours nocturnos',
                'icon' => 'fas fa-moon',
                'color' => '#1A237E',
                'sort_order' => 4,
                'is_active' => true,
            ]);
        }

        // Transfers Subtypes
        if ($transferType) {
            $this->command->info("📍 Creando subtipos para: {$transferType->getTranslatedName()}");
            
            ProductTypeSubcategory::create([
                'product_type_id' => $transferType->product_type_id,
                'name' => [
                    'es' => 'Privado',
                    'en' => 'Private',
                    'fr' => 'Privé',
                    'pt' => 'Privado',
                    'de' => 'Privat'
                ],
                'slug' => 'private',
                'description' => 'Transfers privados exclusivos',
                'icon' => 'fas fa-car',
                'color' => '#2196F3',
                'sort_order' => 1,
                'is_active' => true,
            ]);

            ProductTypeSubcategory::create([
                'product_type_id' => $transferType->product_type_id,
                'name' => [
                    'es' => 'Compartido',
                    'en' => 'Shared Shuttle',
                    'fr' => 'Navette Partagée',
                    'pt' => 'Transporte Compartilhado',
                    'de' => 'Geteilter Shuttle'
                ],
                'slug' => 'shared',
                'description' => 'Transfers compartidos económicos',
                'icon' => 'fas fa-bus',
                'color' => '#FF9800',
                'sort_order' => 2,
                'is_active' => true,
            ]);
        }

        // Activities Subtypes
        if ($activityType) {
            $this->command->info("📍 Creando subtipos para: {$activityType->getTranslatedName()}");
            
            ProductTypeSubcategory::create([
                'product_type_id' => $activityType->product_type_id,
                'name' => [
                    'es' => 'Extremo',
                    'en' => 'Extreme',
                    'fr' => 'Extrême',
                    'pt' => 'Extremo',
                    'de' => 'Extrem'
                ],
                'slug' => 'extreme',
                'description' => 'Actividades de aventura extrema',
                'icon' => 'fas fa-mountain',
                'color' => '#F44336',
                'sort_order' => 1,
                'is_active' => true,
            ]);

            ProductTypeSubcategory::create([
                'product_type_id' => $activityType->product_type_id,
                'name' => [
                    'es' => 'Acuático',
                    'en' => 'Water Activities',
                    'fr' => 'Activités Aquatiques',
                    'pt' => 'Atividades Aquáticas',
                    'de' => 'Wasseraktivitäten'
                ],
                'slug' => 'water',
                'description' => 'Actividades acuáticas',
                'icon' => 'fas fa-water',
                'color' => '#00BCD4',
                'sort_order' => 2,
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ Subtipos de producto creados correctamente');
    }
}
