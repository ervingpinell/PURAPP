<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meeting_point_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_point_id')->constrained('meeting_points')->cascadeOnDelete();
            $table->string('locale', 5); // es, en, fr, pt, de
            $table->string('name', 1000)->nullable();
            $table->string('description', 1000)->nullable();
            $table->timestamps();

            $table->unique(['meeting_point_id', 'locale']);
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_point_translations');
    }
};
