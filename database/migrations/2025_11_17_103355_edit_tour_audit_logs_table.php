<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Soltar la FK actual
        DB::statement('ALTER TABLE tour_audit_logs DROP CONSTRAINT tour_audit_logs_tour_id_foreign');

        // 2) Hacer tour_id nullable y agregar snapshot
        Schema::table('tour_audit_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('tour_id')->nullable()->change();

            // Campos snapshot (ajusta nombres/tipos a tu gusto)
            $table->unsignedBigInteger('original_tour_id')->nullable()->after('tour_id');
            $table->string('original_tour_name')->nullable()->after('original_tour_id');
        });

        // 3) Volver a crear la FK pero con ON DELETE SET NULL
        Schema::table('tour_audit_logs', function (Blueprint $table) {
            $table->foreign('tour_id')
                ->references('tour_id')
                ->on('tours')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Aquí podrías revertir si lo necesitas
    }
};
