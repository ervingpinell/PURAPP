<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('itinerary_item_itinerary', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('itinerary_item_id');
    $table->unsignedBigInteger('itinerary_id');
    $table->integer('item_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->foreign('itinerary_item_id')->references('item_id')->on('itinerary_items')->onDelete('cascade');
    $table->foreign('itinerary_id')->references('itinerary_id')->on('itineraries')->onDelete('cascade');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('itinerary_item_itinerary');
    }
};
