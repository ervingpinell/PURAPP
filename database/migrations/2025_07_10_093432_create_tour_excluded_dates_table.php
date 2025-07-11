<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tour_excluded_dates', function (Blueprint $table) {
            // PK 
            $table->id('tour_excluded_date_id');

            // FK 
            $table->unsignedBigInteger('tour_id');
            $table->foreign('tour_id')
                  ->references('tour_id')
                  ->on('tours')
                  ->onDelete('cascade');

            // Rango de fechas
            $table->date('start_date');
            $table->date('end_date')->nullable();

            // Razón opcional
            $table->string('reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_excluded_dates');
    }
};
