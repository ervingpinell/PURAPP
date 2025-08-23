<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('excluded_amenity_tour', function (Blueprint $table) {
            $table->id();


            $table->unsignedBigInteger('tour_id');
            $table->unsignedBigInteger('amenity_id');

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tour_id', 'amenity_id']);

            $table->foreign('tour_id')->references('tour_id')->on('tours')->onDelete('cascade');
            $table->foreign('amenity_id')->references('amenity_id')->on('amenities')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('excluded_amenity_tour');
    }
};
