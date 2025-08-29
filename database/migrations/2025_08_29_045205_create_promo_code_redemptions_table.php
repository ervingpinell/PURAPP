<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promo_code_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_code_id')->constrained('promo_codes')->cascadeOnDelete();
            $table->unsignedBigInteger('booking_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->unique(['promo_code_id', 'booking_id']); // 1 uso por reserva
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_code_redemptions');
    }
};
