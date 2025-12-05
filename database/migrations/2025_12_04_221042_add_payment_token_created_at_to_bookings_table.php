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
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('payment_token_created_at')->nullable()->after('payment_token');
        });

        // Backfill existing tokens with current timestamp
        DB::table('bookings')
            ->whereNotNull('payment_token')
            ->update(['payment_token_created_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('payment_token_created_at');
        });
    }
};
