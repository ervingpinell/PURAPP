<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Elimina los campos traducibles (name, description) de la tabla itineraries.
     * Estos campos ahora existen únicamente en itinerary_translations.
     */
    public function up(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Restaura los campos eliminados en caso de rollback.
     */
    public function down(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->text('description')->nullable();
        });
    }
};
