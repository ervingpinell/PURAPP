<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ---- En carrito (antes del pago) ----
        Schema::table('carts', function (Blueprint $t) {
            $t->timestamp('terms_accepted_at')->nullable()->index();
            $t->string('terms_version', 32)->nullable()->index();
            $t->string('privacy_version', 32)->nullable()->index();
            $t->json('policies_snapshot')->nullable();   // Foto exacta de los textos mostrados
            $t->string('terms_ip', 64)->nullable();
            $t->string('policies_sha256', 64)->nullable()->index(); // Hash para trazabilidad
        });

        // ---- En booking (post-pago) ----
        Schema::table('bookings', function (Blueprint $t) {
            $t->timestamp('terms_accepted_at')->nullable()->index();
            $t->string('terms_version', 32)->nullable()->index();
            $t->string('privacy_version', 32)->nullable()->index();
            $t->json('policies_snapshot')->nullable();
            $t->string('terms_ip', 64)->nullable();
            $t->string('policies_sha256', 64)->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $t) {
            $t->dropColumn([
                'terms_accepted_at','terms_version','privacy_version',
                'policies_snapshot','terms_ip','policies_sha256'
            ]);
        });

        Schema::table('bookings', function (Blueprint $t) {
            $t->dropColumn([
                'terms_accepted_at','terms_version','privacy_version',
                'policies_snapshot','terms_ip','policies_sha256'
            ]);
        });
    }
};
