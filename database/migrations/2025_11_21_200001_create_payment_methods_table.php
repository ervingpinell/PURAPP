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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id('payment_method_id');
            $table->unsignedBigInteger('user_id');

            // Gateway information
            $table->string('gateway'); // stripe, tilopay, etc.
            $table->string('gateway_customer_id')->nullable(); // Stripe customer ID, etc.
            $table->string('gateway_payment_method_id')->nullable(); // Stripe payment method ID

            // Payment method details
            $table->string('type')->default('card'); // card, bank_account, etc.
            $table->string('card_brand')->nullable(); // visa, mastercard, amex
            $table->string('card_last4')->nullable();
            $table->string('card_exp_month')->nullable();
            $table->string('card_exp_year')->nullable();
            $table->string('card_fingerprint')->nullable()->index(); // For duplicate detection

            // Bank account details (if applicable)
            $table->string('bank_name')->nullable();
            $table->string('account_last4')->nullable();

            // Settings
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            // Metadata
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Soft delete for security/audit

            // Foreign keys
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');

            // Indexes
            $table->index(['user_id', 'is_default']);
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
