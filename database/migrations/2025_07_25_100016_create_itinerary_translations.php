<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('itinerary_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('itinerary_id');
            $table->string('locale', 5);
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('itinerary_id')->references('itinerary_id')->on('itineraries')->onDelete('cascade');
            $table->unique(['itinerary_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itinerary_translations');
    }
};
