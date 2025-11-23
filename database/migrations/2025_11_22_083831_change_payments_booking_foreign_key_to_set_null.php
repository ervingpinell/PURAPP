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
            // Drop existing foreign key
            $table->dropForeign(['booking_id']);

            // Recreate with SET NULL instead of CASCADE
            // This preserves payment records for audit trail even when booking is force-deleted
            $table->foreign('booking_id')
                ->references('booking_id')
                ->on('bookings')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop SET NULL foreign key
            $table->dropForeign(['booking_id']);

            // Restore CASCADE behavior
            $table->foreign('booking_id')
                ->references('booking_id')
                ->on('bookings')
                ->onDelete('cascade');
        });
    }
};
