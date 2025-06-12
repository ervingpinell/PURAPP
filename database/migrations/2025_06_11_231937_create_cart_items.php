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
        Schema::create('cart_items', function (Blueprint $table) {
    $table->id('item_id');
    $table->unsignedBigInteger('cart_id');
    $table->unsignedBigInteger('tour_id');
    $table->unsignedBigInteger('tour_schedule_id')->nullable();
    $table->date('tour_date');
    $table->unsignedBigInteger('hotel_id')->nullable();
    $table->string('other_hotel_name')->nullable();
    $table->unsignedBigInteger('language_id');
    $table->integer('adults_quantity');
    $table->integer('kids_quantity');
    $table->decimal('adult_price', 10, 2);
    $table->decimal('kid_price', 10, 2);
    $table->timestamps();

    $table->foreign('cart_id')->references('cart_id')->on('carts')->onDelete('cascade');
    $table->foreign('tour_id')->references('tour_id')->on('tours')->onDelete('cascade');
    $table->foreign('tour_schedule_id')->references('tour_schedule_id')->on('tour_schedules')->onDelete('set null');
    $table->foreign('hotel_id')->references('hotel_id')->on('hotels_list')->onDelete('set null');
    $table->foreign('language_id')->references('language_id')->on('languages')->onDelete('restrict');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
