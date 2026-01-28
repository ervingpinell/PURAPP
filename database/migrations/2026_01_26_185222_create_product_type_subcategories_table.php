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
        Schema::create('product_type_subcategories', function (Blueprint $table) {
            $table->id('subtype_id');
            
            // Relación con product_types
            $table->unsignedBigInteger('product_type_id');
            $table->foreign('product_type_id')
                  ->references('product_type_id')
                  ->on('product_types')
                  ->onDelete('cascade');
            
            // Información básica (traducible)
            $table->json('name'); // {"es": "Día Completo", "en": "Full Day"}
            $table->string('slug', 50)->unique();
            $table->text('description')->nullable();
            
            // SEO (opcional, puede venir de branding)
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            
            // UI
            $table->string('icon', 50)->nullable(); // fas fa-sun
            $table->string('color', 20)->nullable(); // #FF5733
            
            // Ordenamiento y estado
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['product_type_id', 'is_active']);
            $table->index('slug');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_type_subcategories');
    }
};
