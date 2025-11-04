<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Cart Items
        Schema::table('cart_items', function (Blueprint $table) {
            $table->json('categories')->nullable()->after('kids_quantity');
        });

        // Booking Details
        Schema::table('booking_details', function (Blueprint $table) {
            $table->json('categories')->nullable()->after('kid_price');
        });
    }

    public function down()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('categories');
        });

        Schema::table('booking_details', function (Blueprint $table) {
            $table->dropColumn('categories');
        });
    }
};
