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
        Schema::create('tour_audit_logs', function (Blueprint $table) {
            $table->id('audit_id');

            // Relación con el tour
            $table->unsignedBigInteger('tour_id')->nullable();

            // Información del usuario que hizo el cambio
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable(); // Por si se elimina el usuario
            $table->string('user_email')->nullable();

            // Tipo de acción
            $table->enum('action', [
                'created',
                'updated',
                'deleted',
                'restored',
                'published',
                'unpublished',
                'draft_created',
                'draft_continued',
                'draft_deleted',
                'step_completed',
                'bulk_action'
            ])->index();

            // Contexto adicional
            $table->string('context')->nullable(); // wizard, admin, api, etc.
            $table->integer('wizard_step')->nullable(); // Para cambios en wizard

            // Datos del cambio
            $table->json('old_values')->nullable(); // Estado anterior
            $table->json('new_values')->nullable(); // Estado nuevo
            $table->json('changed_fields')->nullable(); // Lista de campos que cambiaron

            // Metadata
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->string('method', 10)->nullable(); // GET, POST, etc.

            // Descripción legible
            $table->text('description')->nullable();

            // Tags para categorizar
            $table->json('tags')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('tour_id')
                  ->references('tour_id')
                  ->on('tours')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');

            // Índices para búsquedas rápidas
            $table->index(['tour_id', 'created_at'], 'idx_audit_tour_date');
            $table->index(['user_id', 'created_at'], 'idx_audit_user_date');
            $table->index('created_at', 'idx_audit_date');
            $table->index(['action', 'created_at'], 'idx_audit_action_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_audit_logs');
    }
};
