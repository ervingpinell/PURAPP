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
    Schema::create('tour_language_tour', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('tour_id');
    $table->unsignedBigInteger('tour_language_id');
        $table->boolean('is_active')->default(true);
    $table->foreign('tour_id')->references('tour_id')->on('tours')->onDelete('cascade');
    $table->foreign('tour_language_id')->references('tour_language_id')->on('tour_languages')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_language_tour');
    }
};
