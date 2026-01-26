<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Renombrar tabla tour_types a product_types
        Schema::rename('tour_types', 'product_types');

        // 2. Renombrar primary key tour_type_id a product_type_id
        Schema::table('product_types', function (Blueprint $table) {
            $table->renameColumn('tour_type_id', 'product_type_id');
        });

        // 3. Actualizar foreign key en tabla products
        Schema::table('products', function (Blueprint $table) {
            // Drop foreign key constraint si existe
            try {
                $table->dropForeign(['tour_type_id']);
            } catch (\Exception $e) {
                // Si no existe, continuar
            }
            
            // Renombrar columna
            $table->renameColumn('tour_type_id', 'product_type_id');
            
            // Recrear foreign key con nuevo nombre
            $table->foreign('product_type_id')
                  ->references('product_type_id')
                  ->on('product_types')
                  ->onDelete('set null');
        });

        // 4. Actualizar tabla tour_type_translations si existe
        if (Schema::hasTable('tour_type_translations')) {
            Schema::rename('tour_type_translations', 'product_type_translations');
            
            Schema::table('product_type_translations', function (Blueprint $table) {
                $table->renameColumn('tour_type_id', 'product_type_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir en orden inverso
        
        // 4. Revertir tour_type_translations
        if (Schema::hasTable('product_type_translations')) {
            Schema::table('product_type_translations', function (Blueprint $table) {
                $table->renameColumn('product_type_id', 'tour_type_id');
            });
            
            Schema::rename('product_type_translations', 'tour_type_translations');
        }

        // 3. Revertir foreign key en products
        Schema::table('products', function (Blueprint $table) {
            try {
                $table->dropForeign(['product_type_id']);
            } catch (\Exception $e) {
                // Si no existe, continuar
            }
            
            $table->renameColumn('product_type_id', 'tour_type_id');
            
            $table->foreign('tour_type_id')
                  ->references('tour_type_id')
                  ->on('tour_types')
                  ->onDelete('set null');
        });

        // 2. Revertir primary key
        Schema::table('product_types', function (Blueprint $table) {
            $table->renameColumn('product_type_id', 'tour_type_id');
        });

        // 1. Revertir nombre de tabla
        Schema::rename('product_types', 'tour_types');
    }
};
