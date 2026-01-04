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
            // Payment tracking
            $table->boolean('is_paid')->default(false)->after('status');
            $table->decimal('paid_amount', 10, 2)->nullable()->after('is_paid');
            $table->timestamp('paid_at')->nullable()->after('paid_amount');

            // Expiration management
            $table->timestamp('pending_expires_at')->nullable()->after('paid_at');
            $table->integer('extension_count')->default(0)->after('pending_expires_at');

            // Reserve-now-pay-later
            $table->boolean('is_pay_later')->default(false)->after('extension_count');
            $table->timestamp('auto_charge_at')->nullable()->after('is_pay_later');
            $table->timestamp('payment_reminder_sent_at')->nullable()->after('auto_charge_at');

            // Admin notifications
            $table->timestamp('expiry_warning_sent_at')->nullable()->after('payment_reminder_sent_at');
            $table->string('extend_token', 64)->nullable()->after('expiry_warning_sent_at');

            // Payment link system (payment_token already exists, just add related fields)
            $table->timestamp('payment_link_expires_at')->nullable()->after('payment_token');
            $table->boolean('payment_link_sent')->default(false)->after('payment_link_expires_at');
            $table->timestamp('payment_link_sent_at')->nullable()->after('payment_link_sent');

            // Indexes for scheduled jobs and queries
            $table->index(['status', 'is_paid', 'pending_expires_at'], 'idx_pending_expiry');
            $table->index(['is_pay_later', 'auto_charge_at'], 'idx_auto_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_pending_expiry');
            $table->dropIndex('idx_auto_charge');

            // Drop columns
            $table->dropColumn([
                'is_paid',
                'paid_amount',
                'paid_at',
                'pending_expires_at',
                'extension_count',
                'is_pay_later',
                'auto_charge_at',
                'payment_reminder_sent_at',
                'expiry_warning_sent_at',
                'extend_token',
                'payment_link_expires_at',
                'payment_link_sent',
                'payment_link_sent_at',
            ]);
        });
    }
};
