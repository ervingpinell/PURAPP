<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->index('booking_date', 'bookings_booking_date_idx');
            $table->index('status', 'bookings_status_idx');
            $table->index('tour_id', 'bookings_tour_id_idx');
        });
        Schema::table('booking_details', function (Blueprint $table) {
            $table->index('tour_id', 'bd_tour_id_idx');
            $table->index('tour_language_id', 'bd_tour_language_id_idx');
            $table->index('tour_date', 'bd_tour_date_idx');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_booking_date_idx');
            $table->dropIndex('bookings_status_idx');
            $table->dropIndex('bookings_tour_id_idx');
        });
        Schema::table('booking_details', function (Blueprint $table) {
            $table->dropIndex('bd_tour_id_idx');
            $table->dropIndex('bd_tour_language_id_idx');
            $table->dropIndex('bd_tour_date_idx');
        });
    }
};
