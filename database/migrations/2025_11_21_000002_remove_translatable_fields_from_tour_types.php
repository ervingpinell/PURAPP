<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Elimina los campos traducibles (name, description, duration) de la tabla tour_types.
     * Estos campos ahora existen únicamente en tour_type_translations.
     */
    public function up(): void
    {
        Schema::table('tour_types', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'duration']);
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Restaura los campos eliminados en caso de rollback.
     */
    public function down(): void
    {
        Schema::table('tour_types', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('duration')->nullable();
        });
    }
};
