<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        /**
         * Proveedores de reseñas (configurables)
         */
        Schema::create('review_providers', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique(); // ej: local, viator, google, gyg
            $t->string('driver');
            $t->boolean('indexable')->default(false);
            $t->json('settings')->nullable();
            $t->integer('cache_ttl_sec')->default(3600);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        /**
         * Reseñas
         */
        Schema::create('reviews', function (Blueprint $t) {
            $t->id();

            // FK a tours.tour_id
            $t->bigInteger('tour_id');
            $t->foreign('tour_id')->references('tour_id')->on('tours')->cascadeOnDelete();

            // FK a bookings.booking_id
            $t->bigInteger('booking_id')->nullable();
            $t->foreign('booking_id')->references('booking_id')->on('bookings')->nullOnDelete();

            // FK a users.user_id
            $t->bigInteger('user_id')->nullable();
            $t->foreign('user_id')->references('user_id')->on('users')->nullOnDelete();

            $t->string('provider')->default('local');
            $t->string('provider_review_id')->nullable();

            $t->tinyInteger('rating');
            $t->string('title')->nullable();
            $t->text('body');

            $t->string('language', 8)->default('es');
            $t->string('author_name')->nullable();
            $t->string('author_country')->nullable();

            $t->boolean('is_verified')->default(false);
            $t->boolean('is_public')->default(true);
            $t->enum('status', ['pending','published','hidden','flagged'])->default('pending');

            $t->string('source_url')->nullable();
            $t->timestamps();

            $t->index(['tour_id', 'status', 'created_at']);
        });

        /**
         * Respuestas de administradores
         */
        Schema::create('review_replies', function (Blueprint $t) {
            $t->id();

            $t->bigInteger('review_id');
            $t->foreign('review_id')->references('id')->on('reviews')->cascadeOnDelete();

            $t->bigInteger('admin_user_id');
            $t->foreign('admin_user_id')->references('user_id')->on('users')->cascadeOnDelete();

            $t->text('body');
            $t->boolean('public')->default(true);
            $t->timestamps();
        });

        /**
         * Solicitudes de reseñas post-compra
         */
        Schema::create('review_requests', function (Blueprint $t) {
            $t->id();

            $t->bigInteger('booking_id');
            $t->foreign('booking_id')->references('booking_id')->on('bookings')->cascadeOnDelete();

            $t->bigInteger('user_id')->nullable();
            $t->foreign('user_id')->references('user_id')->on('users')->nullOnDelete();

            $t->bigInteger('tour_id');
            $t->foreign('tour_id')->references('tour_id')->on('tours')->cascadeOnDelete();

            $t->string('email');
            $t->string('token')->unique();
            $t->enum('status', ['created','sent','reminded','fulfilled','expired'])->default('created');
            $t->timestamp('sent_at')->nullable();
            $t->timestamp('reminded_at')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamps();
        });

        /**
         * Bitácora de moderación
         */
        Schema::create('review_moderation_logs', function (Blueprint $t) {
            $t->id();

            $t->bigInteger('review_id');
            $t->foreign('review_id')->references('id')->on('reviews')->cascadeOnDelete();

            $t->bigInteger('admin_user_id');
            $t->foreign('admin_user_id')->references('user_id')->on('users')->cascadeOnDelete();

            $t->string('action');
            $t->json('meta')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_moderation_logs');
        Schema::dropIfExists('review_requests');
        Schema::dropIfExists('review_replies');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('review_providers');
    }
};
