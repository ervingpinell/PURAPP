<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('payment_token', 64)->nullable()->unique()->after('booking_reference');
            $table->index('payment_token');
        });

        // Generate tokens for existing bookings using PHP
        $bookings = DB::table('bookings')->whereNull('payment_token')->get(['booking_id']);

        foreach ($bookings as $booking) {
            DB::table('bookings')
                ->where('booking_id', $booking->booking_id)
                ->update(['payment_token' => bin2hex(random_bytes(32))]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['payment_token']);
            $table->dropColumn('payment_token');
        });
    }
};
