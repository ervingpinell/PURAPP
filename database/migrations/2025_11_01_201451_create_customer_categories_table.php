<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('slug', 50)->unique(); // 'adult', 'child', 'infant', 'senior'
            $table->string('name', 100); // 'Adulto', 'Niño', etc.
            $table->unsignedTinyInteger('age_from'); // Edad desde (inclusive)
            $table->unsignedTinyInteger('age_to')->nullable(); // Edad hasta (inclusive), NULL = sin límite
            $table->unsignedTinyInteger('order')->default(0); // Orden de visualización
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_categories');
    }
};
