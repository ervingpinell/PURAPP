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
Schema::create('amenity_tour', function (Blueprint $table) {
    $table->unsignedBigInteger('tour_id');
    $table->unsignedBigInteger('amenity_id');
        $table->boolean('is_active')->default(true);

    $table->foreign('tour_id')->references('tour_id')->on('tours')->onDelete('cascade');
    $table->foreign('amenity_id')->references('amenity_id')->on('amenities')->onDelete('cascade');

    $table->primary(['tour_id', 'amenity_id']); // evita duplicados
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenity_tour');
    }
};
