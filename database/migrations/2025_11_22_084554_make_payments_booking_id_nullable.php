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
        Schema::table('payments', function (Blueprint $table) {
            // Make booking_id nullable to allow payments to exist without a booking
            // This is essential for audit trail when bookings are force-deleted
            $table->unsignedBigInteger('booking_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Restore NOT NULL constraint
            // WARNING: This will fail if there are any payments with NULL booking_id
            $table->unsignedBigInteger('booking_id')->nullable(false)->change();
        });
    }
};
