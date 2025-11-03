<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_availability', function (Blueprint $table) {
            // 1. Agregar schedule_id para overrides por horario específico
            $table->unsignedBigInteger('schedule_id')->nullable()->after('tour_id');
            $table->foreign('schedule_id')->references('schedule_id')->on('schedules')->onDelete('cascade');

            // 2. Cambiar 'available' por 'max_capacity' (más explícito)
            // Si ya tienes datos, puedes migrarlos primero
            $table->integer('max_capacity')->nullable()->after('date');

            // 3. Renombrar 'available' a 'is_blocked' (más claro)
            // $table->renameColumn('available', 'is_blocked');
            // O si prefieres hacer drop/add:
            $table->dropColumn('available');
            $table->boolean('is_blocked')->default(false)->after('max_capacity');

            // 4. Quitar start_time y end_time (eso lo maneja schedule_id)
            $table->dropColumn(['start_time', 'end_time']);

            // 5. Agregar índices para performance
            $table->index(['tour_id', 'date']);
            $table->index(['tour_id', 'schedule_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::table('tour_availability', function (Blueprint $table) {
            $table->dropForeign(['schedule_id']);
            $table->dropColumn(['schedule_id', 'max_capacity', 'is_blocked']);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('available')->default(true);
        });
    }
};
