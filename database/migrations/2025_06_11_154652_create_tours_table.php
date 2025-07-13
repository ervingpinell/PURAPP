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
            $table->unsignedBigInteger('tour_type_id');
            $table->text('overview')->nullable();
            $table->string('name');
            $table->decimal('adult_price', 10, 2);
            $table->decimal('kid_price', 10, 2);
            $table->integer('length');
            $table->unsignedInteger('max_capacity')->default(12);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('itinerary_id')->nullable();
            $table->timestamps();

            
        $table->foreign('tour_type_id')->references('tour_type_id')->on('tour_types')->onDelete('restrict');
        $table->foreign('itinerary_id')->references('itinerary_id')->on('itineraries')->onDelete('set null');
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
