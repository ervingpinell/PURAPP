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
            $table->string('checkout_token', 64)->nullable()->unique()->after('booking_reference');
            $table->timestamp('checkout_token_expires_at')->nullable()->after('checkout_token');
            $table->timestamp('checkout_accessed_at')->nullable()->after('checkout_token_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['checkout_token', 'checkout_token_expires_at', 'checkout_accessed_at']);
        });
    }
};
