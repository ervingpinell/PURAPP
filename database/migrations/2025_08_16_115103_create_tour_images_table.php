<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tour_images', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('tour_id');
            $t->string('path');                 
            $t->string('caption')->nullable();  
            $t->unsignedInteger('position')->default(0);
            $t->boolean('is_cover')->default(false);
            $t->timestamps();

            $t->foreign('tour_id')->references('tour_id')->on('tours')->onDelete('cascade');
            $t->index(['tour_id', 'position']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('tour_images');
    }
};
