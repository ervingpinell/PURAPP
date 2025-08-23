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

            $table->id('tour_excluded_date_id');


            $table->unsignedBigInteger('tour_id');
            $table->foreign('tour_id')
                  ->references('tour_id')
                  ->on('tours')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('schedule_id')->nullable();
            $table->foreign('schedule_id')
                  ->references('schedule_id')
                  ->on('schedules')
                  ->onDelete('set null');

            $table->date('start_date');
            $table->date('end_date')->nullable();

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
