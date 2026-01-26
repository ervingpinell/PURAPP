<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ============================================
        // PARTE 1: RENOMBRAR TABLAS PRINCIPALES
        // ============================================
        
        // 1. Tipos
        if (Schema::hasTable('tour_types')) {
            Schema::rename('tour_types', 'product_types');
        }
        
        // 2. Tabla principal
        if (Schema::hasTable('tours')) {
            Schema::rename('tours', 'product2');
        }
        
        // 3. Tablas relacionadas
        $renames = [
            'tour_availability' => 'product_availability',
            'tour_excluded_dates' => 'product_excluded_dates',
            'tour_images' => 'product_images',
            'tour_prices' => 'product_prices',
            'tour_translations' => 'product_translations',
            'tour_type_translations' => 'product_type_translations',
            'tour_audit_log' => 'product_audit_log',
        ];
        
        foreach ($renames as $old => $new) {
            if (Schema::hasTable($old) && !Schema::hasTable($new)) {
                Schema::rename($old, $new);
            }
        }
        
        // 4. Tablas pivot
        $pivotRenames = [
            'amenity_tour' => 'amenity_product',
            'tour_language_tour' => 'product_language_product',
            'schedule_tour' => 'schedule_product',
            'excluded_tour_amenities' => 'excluded_product_amenities',
            'tour_type_tour_order' => 'product_type_product_order',
        ];
        
        foreach ($pivotRenames as $old => $new) {
            if (Schema::hasTable($old) && !Schema::hasTable($new)) {
                Schema::rename($old, $new);
            }
        }
    }

    public function down(): void
    {
        // Revertir en orden inverso
        $pivotRenames = [
            'product_type_product_order' => 'tour_type_tour_order',
            'excluded_product_amenities' => 'excluded_tour_amenities',
            'schedule_product' => 'schedule_tour',
            'product_language_product' => 'tour_language_tour',
            'amenity_product' => 'amenity_tour',
        ];
        
        foreach ($pivotRenames as $old => $new) {
            if (Schema::hasTable($old)) {
                Schema::rename($old, $new);
            }
        }
        
        $renames = [
            'product_audit_log' => 'tour_audit_log',
            'product_type_translations' => 'tour_type_translations',
            'product_translations' => 'tour_translations',
            'product_prices' => 'tour_prices',
            'product_images' => 'tour_images',
            'product_excluded_dates' => 'tour_excluded_dates',
            'product_availability' => 'tour_availability',
        ];
        
        foreach ($renames as $old => $new) {
            if (Schema::hasTable($old)) {
                Schema::rename($old, $new);
            }
        }
        
        if (Schema::hasTable('product2')) {
            Schema::rename('product2', 'tours');
        }
        
        if (Schema::hasTable('product_types')) {
            Schema::rename('product_types', 'tour_types');
        }
    }
};
