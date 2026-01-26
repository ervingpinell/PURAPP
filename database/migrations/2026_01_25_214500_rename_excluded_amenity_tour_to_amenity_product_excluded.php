<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Renombrar tabla pivot de amenidades excluidas
        if (Schema::hasTable('excluded_amenity_tour')) {
            Schema::rename('excluded_amenity_tour', 'amenity_product_excluded');
        }

        // Renombrar columna tour_id a product_id
        if (Schema::hasTable('amenity_product_excluded') && Schema::hasColumn('amenity_product_excluded', 'tour_id')) {
            Schema::table('amenity_product_excluded', function (Blueprint $table) {
                $table->renameColumn('tour_id', 'product_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir renombrado de columna
        if (Schema::hasTable('amenity_product_excluded') && Schema::hasColumn('amenity_product_excluded', 'product_id')) {
            Schema::table('amenity_product_excluded', function (Blueprint $table) {
                $table->renameColumn('product_id', 'tour_id');
            });
        }

        // Revertir renombrado de tabla
        if (Schema::hasTable('amenity_product_excluded')) {
            Schema::rename('amenity_product_excluded', 'excluded_amenity_tour');
        }
    }
};
