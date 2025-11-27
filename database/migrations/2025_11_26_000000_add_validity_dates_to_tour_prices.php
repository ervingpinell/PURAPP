<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_prices', function (Blueprint $table) {
            // Agregar campos de validez temporal
            $table->date('valid_from')->nullable()->after('is_active');
            $table->date('valid_until')->nullable()->after('valid_from');

            // Agregar índice compuesto para optimizar búsquedas por fecha
            $table->index(['tour_id', 'category_id', 'valid_from', 'valid_until'], 'tour_prices_validity_idx');
        });

        // Eliminar constraint único que impedía múltiples precios por categoría
        Schema::table('tour_prices', function (Blueprint $table) {
            $table->dropUnique('tour_category_unique');
        });
    }

    public function down(): void
    {
        Schema::table('tour_prices', function (Blueprint $table) {
            // Eliminar índice
            $table->dropIndex('tour_prices_validity_idx');

            // Eliminar columnas
            $table->dropColumn(['valid_from', 'valid_until']);
        });

        // Restaurar constraint único
        Schema::table('tour_prices', function (Blueprint $table) {
            $table->unique(['tour_id', 'category_id'], 'tour_category_unique');
        });
    }
};
