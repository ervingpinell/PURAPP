<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_pricing_strategies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            
            // Tipo de estrategia
            $table->enum('strategy_type', [
                'flat_rate',
                'per_person',
                'per_category',
                'tiered',
                'tiered_per_category'
            ]);
            
            // Configuración adicional (JSON)
            $table->json('config')->nullable()
                  ->comment('Configuración adicional de la estrategia');
            
            // Control de activación
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0)
                  ->comment('Para múltiples estrategias activas (mayor = más prioritario)');
            
            // Validez temporal (ej: temporada alta/baja)
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('product2')
                  ->onDelete('cascade');
            
            // Indexes
            $table->index(['product_id', 'is_active']);
            $table->index('strategy_type');
            $table->index(['valid_from', 'valid_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_pricing_strategies');
    }
};
