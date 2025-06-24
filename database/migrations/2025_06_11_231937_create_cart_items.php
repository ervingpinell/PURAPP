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
        $table->unsignedBigInteger('tour_language_id');
        $table->date('tour_date');
        
        $table->unsignedBigInteger('hotel_id')->nullable();
        $table->boolean('is_other_hotel')->default(false);
        $table->string('other_hotel_name')->nullable();  
        
        $table->integer('adults_quantity');
        $table->integer('kids_quantity');
        $table->timestamps();

        // Foreign keys
        $table->foreign('cart_id')->references('cart_id')->on('carts')->onDelete('cascade');
        $table->foreign('tour_id')->references('tour_id')->on('tours')->onDelete('cascade');
        $table->foreign('tour_schedule_id')->references('tour_schedule_id')->on('tour_schedules')->onDelete('set null');
        $table->foreign('hotel_id')->references('hotel_id')->on('hotels_list')->onDelete('set null');
        $table->foreign('tour_language_id')->references('tour_language_id')->on('tour_languages')->onDelete('restrict');
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
