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
    Schema::create('tours', function (Blueprint $table) {
            $table->id('tour_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('tour_language_id');
            $table->string('name');
            $table->text('description');
            $table->decimal('adult_price', 10, 2);
            $table->decimal('kid_price', 10, 2);
            $table->integer('length');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            
            $table->foreign('tour_language_id')->references('tour_language_id')->on('languages')->onDelete('restrict');
            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
