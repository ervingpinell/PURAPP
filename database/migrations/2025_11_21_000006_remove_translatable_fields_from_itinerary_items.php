<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Elimina los campos traducibles (title, description) de la tabla itinerary_items.
     * Estos campos ahora existen únicamente en itinerary_item_translations.
     */
    public function up(): void
    {
        Schema::table('itinerary_items', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Restaura los campos eliminados en caso de rollback.
     */
    public function down(): void
    {
        Schema::table('itinerary_items', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->text('description')->nullable();
        });
    }
};
