<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_prices', function (Blueprint $table) {
            $table->id('tour_price_id');
            $table->unsignedBigInteger('tour_id');
            $table->unsignedBigInteger('category_id');

            // Precio para esta categoría en este tour
            $table->decimal('price', 10, 2)->default(0);

            // Cantidad mínima y máxima permitida de esta categoría por reserva
            $table->unsignedTinyInteger('min_quantity')->default(0);
            $table->unsignedTinyInteger('max_quantity')->default(12);

            // Estado
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Foreign keys
            $table->foreign('tour_id')
                ->references('tour_id')
                ->on('tours')
                ->onDelete('cascade');

            $table->foreign('category_id')
                ->references('category_id')
                ->on('customer_categories')
                ->onDelete('cascade');

            // Constraint: Una categoría solo puede tener un precio por tour
            $table->unique(['tour_id', 'category_id'], 'tour_category_unique');

            // Índices
            $table->index(['tour_id', 'is_active']);
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_prices');
    }
};
