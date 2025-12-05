<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds expires_at timestamp and composite index to optimize payment intent reuse queries.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add expires_at column (30 min from creation by default)
            $table->timestamp('expires_at')->nullable()->after('created_at');

            // Add composite index for efficient payment intent reuse lookups
            // This index covers the most common query pattern:
            // WHERE booking_id = ? AND gateway = ? AND status IN (?, ?) AND created_at > ?
            $table->index(['booking_id', 'gateway', 'status', 'created_at'], 'idx_payments_reuse');
        });

        // Set expires_at for existing pending/processing payments (30 min from created_at)
        DB::statement("
            UPDATE payments 
            SET expires_at = created_at + INTERVAL '30 minutes'
            WHERE status IN ('pending', 'processing') 
            AND expires_at IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_reuse');
            $table->dropColumn('expires_at');
        });
    }
};
