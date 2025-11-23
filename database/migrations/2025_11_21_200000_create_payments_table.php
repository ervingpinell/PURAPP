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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('user_id');

            // Gateway information
            $table->string('gateway')->index(); // stripe, tilopay, banco_nacional, bac, bcr
            $table->string('gateway_transaction_id')->nullable()->index();
            $table->string('gateway_payment_intent_id')->nullable()->index();

            // Amount and currency
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD'); // USD, CRC
            $table->decimal('amount_refunded', 10, 2)->default(0);

            // Status
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'cancelled',
                'refunded',
                'partially_refunded'
            ])->default('pending')->index();

            // Payment method details (for display)
            $table->string('payment_method_type')->nullable(); // card, bank_transfer, etc.
            $table->string('card_brand')->nullable(); // visa, mastercard, amex
            $table->string('card_last4')->nullable();

            // Gateway response (for debugging and reconciliation)
            $table->json('gateway_response')->nullable();
            $table->json('metadata')->nullable(); // Additional data

            // Error tracking
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();

            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('booking_id')
                ->references('booking_id')
                ->on('bookings')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');

            // Indexes for common queries
            $table->index(['booking_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
