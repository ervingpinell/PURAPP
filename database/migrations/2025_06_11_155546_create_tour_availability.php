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
        Schema::create('tour_availability', function (Blueprint $table) {
            $table->id('availability_id');

            $table->unsignedBigInteger('tour_id');
            $table->unsignedBigInteger('schedule_id')->nullable();

            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->boolean('available')->default(true);
            $table->boolean('is_active')->default(true);

            $table->timestamps();


            $table->foreign('tour_id')
                  ->references('tour_id')
                  ->on('tours')
                  ->onDelete('cascade');

            $table->foreign('schedule_id')
                  ->references('schedule_id')
                  ->on('schedules')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_availability');
    }
};
