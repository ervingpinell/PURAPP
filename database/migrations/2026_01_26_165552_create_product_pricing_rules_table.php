<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('strategy_id');
            
            // Rango de pasajeros (tier)
            $table->unsignedInteger('min_passengers')->default(1);
            $table->unsignedInteger('max_passengers')->default(99);
            
            // Categoría de cliente (NULL si no aplica)
            $table->unsignedBigInteger('customer_category_id')->nullable();
            
            // Precio
            $table->decimal('price', 10, 2);
            $table->enum('price_type', ['per_person', 'per_group'])
                  ->default('per_person');
            
            // Metadata
            $table->string('label', 100)->nullable()
                  ->comment('Ej: "Temporada Alta", "Grupo Pequeño"');
            
            // Control
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('strategy_id')
                  ->references('id')
                  ->on('product_pricing_strategies')
                  ->onDelete('cascade');
            
            $table->foreign('customer_category_id')
                  ->references('category_id')
                  ->on('customer_categories')
                  ->onDelete('set null');
            
            // Indexes
            $table->index('strategy_id');
            $table->index('customer_category_id');
            $table->index(['min_passengers', 'max_passengers']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_pricing_rules');
    }
};
