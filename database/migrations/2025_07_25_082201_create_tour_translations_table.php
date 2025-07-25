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
Schema::create('tour_translations', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('tour_id');
    $table->string('locale', 5);
    $table->string('name');
    $table->text('overview')->nullable();
    $table->timestamps();

    $table->foreign('tour_id')->references('tour_id')->on('tours')->onDelete('cascade');
    $table->unique(['tour_id', 'locale']);
});



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_translations');
    }
};
