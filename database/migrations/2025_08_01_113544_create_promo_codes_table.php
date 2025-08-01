<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Ej: GREEN10
            $table->decimal('discount_amount', 8, 2)->nullable(); // Monto fijo ($)
            $table->decimal('discount_percent', 5, 2)->nullable(); // Porcentaje (10.00 = 10%)
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->unsignedBigInteger('used_by_booking_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('promo_codes');
    }
};
