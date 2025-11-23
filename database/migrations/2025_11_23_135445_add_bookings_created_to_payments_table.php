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
            $table->boolean('bookings_created')->default(false)->after('status');
            $table->timestamp('bookings_created_at')->nullable()->after('bookings_created');

            // Add index for faster queries
            $table->index(['bookings_created', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['bookings_created', 'status']);
            $table->dropColumn(['bookings_created', 'bookings_created_at']);
        });
    }
};
