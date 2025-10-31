<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add fields to control how many times a cart hold can be extended.
     * - extended_count: how many times the hold has been extended
     * - last_extended_at: when it was last extended
     */
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Veces que se ha extendido el hold (para limitar, p.ej. 1 sola vez)
            $table->unsignedSmallInteger('extended_count')
                  ->default(0)
                  ->after('expires_at');

            // Fecha/hora del último extend (con zona horaria; Postgres recomendado)
            if (method_exists($table, 'timestampTz')) {
                $table->timestampTz('last_extended_at')->nullable()->after('extended_count');
            } else {
                $table->timestamp('last_extended_at')->nullable()->after('extended_count');
            }

            // (Opcional pero útil): índice compuesto para consultas de holds activos
            // carts_active_expires_idx: WHERE is_active AND expires_at
            $table->index(['is_active', 'expires_at'], 'carts_active_expires_idx');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Quita índice si existe
            try { $table->dropIndex('carts_active_expires_idx'); } catch (\Throwable $e) {}

            // Quita columnas
            $table->dropColumn(['extended_count', 'last_extended_at']);
        });
    }
};
