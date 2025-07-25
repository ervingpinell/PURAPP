<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('amenity_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('amenity_id');
            $table->string('locale', 5);
            $table->string('name');
            $table->timestamps();

            $table->foreign('amenity_id')->references('amenity_id')->on('amenities')->onDelete('cascade');
            $table->unique(['amenity_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amenity_translations');
    }
};
