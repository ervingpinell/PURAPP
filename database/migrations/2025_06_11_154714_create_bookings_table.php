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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('tour_id');
            $table->unsignedBigInteger('tour_language_id')->after('tour_id');
            $table->string('booking_reference')->unique();
            $table->date('booking_date');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->decimal('total', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('tour_id')
                  ->references('tour_id')
                  ->on('tours')
                  ->onDelete('cascade');

            $table->foreign('tour_language_id')
                  ->references('tour_language_id')
                  ->on('tour_languages')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['tour_language_id']);
            $table->dropForeign(['tour_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('bookings');
    }
};
