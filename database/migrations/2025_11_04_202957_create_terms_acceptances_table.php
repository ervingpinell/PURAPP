<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('terms_acceptances', function (Blueprint $t) {
            $t->bigIncrements('id');

            // Enlazamos "blando" (sin FK estricta para no acoplar a nombres de PK personalizados)
            $t->unsignedBigInteger('user_id')->nullable()->index();
            $t->unsignedBigInteger('cart_ref')->nullable()->index();     // referencia al carrito (cart_id)
            $t->unsignedBigInteger('booking_ref')->nullable()->index();  // referencia a la reserva (booking_id)

            // Evidencia de aceptación
            $t->timestamp('accepted_at')->index();
            $t->string('terms_version', 32)->nullable()->index();
            $t->string('privacy_version', 32)->nullable()->index();
            $t->json('policies_snapshot')->nullable();
            $t->string('policies_sha256', 64)->nullable()->index();

            // Contexto
            $t->string('ip_address', 64)->nullable();
            $t->text('user_agent')->nullable();
            $t->string('locale', 12)->nullable()->index();    // es, en, fr, etc.
            $t->string('timezone', 64)->nullable();
            $t->string('consent_source', 24)->default('checkout')->index(); // checkout|admin|api...
            $t->string('referrer')->nullable();

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terms_acceptances');
    }
};
