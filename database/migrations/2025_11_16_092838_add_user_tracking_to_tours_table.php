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
        Schema::table('tours', function (Blueprint $table) {
            // Campo para rastrear quién creó el tour
            $table->unsignedBigInteger('created_by')->nullable()->after('is_draft');

            // Campo para rastrear quién hizo la última modificación
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');

            // Foreign keys - ajusta según tu tabla de usuarios
            // Si tu tabla se llama 'users' y el campo 'user_id':
            $table->foreign('created_by')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');

            $table->foreign('updated_by')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');

            // Índices compuestos para mejorar búsquedas comunes
            $table->index(['is_draft', 'created_by'], 'idx_tours_draft_creator');
            $table->index(['is_active', 'created_by'], 'idx_tours_active_creator');
            $table->index('updated_by', 'idx_tours_updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            // Eliminar foreign keys primero
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Eliminar índices
            $table->dropIndex('idx_tours_draft_creator');
            $table->dropIndex('idx_tours_active_creator');
            $table->dropIndex('idx_tours_updated_by');

            // Eliminar columnas
            $table->dropColumn(['created_by', 'updated_by']);
        });
    }
};
