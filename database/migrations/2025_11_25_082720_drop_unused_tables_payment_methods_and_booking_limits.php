<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Drops unused tables:
     * - payment_methods: Empty table, not actively used
     * - booking_limits: Has 1 record but code reads from app_settings instead
     */
    public function up(): void
    {
        // Drop payment_methods table (empty, not used)
        Schema::dropIfExists('payment_methods');

        // Drop booking_limits table (code uses app_settings instead)
        Schema::dropIfExists('booking_limits');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate payment_methods table
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id('payment_method_id');
            $table->unsignedBigInteger('user_id');

            // Gateway information
            $table->string('gateway'); // stripe, tilopay, etc.
            $table->string('gateway_customer_id')->nullable();
            $table->string('gateway_payment_method_id')->nullable();

            // Payment method details
            $table->string('type')->default('card');
            $table->string('card_brand')->nullable();
            $table->string('card_last4')->nullable();
            $table->string('card_exp_month')->nullable();
            $table->string('card_exp_year')->nullable();
            $table->string('card_fingerprint')->nullable()->index();

            // Bank account details
            $table->string('bank_name')->nullable();
            $table->string('account_last4')->nullable();

            // Settings
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            // Metadata
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');

            // Indexes
            $table->index(['user_id', 'is_default']);
            $table->index(['user_id', 'is_active']);
        });

        // Recreate booking_limits table (structure unknown, leaving empty)
        Schema::create('booking_limits', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};
