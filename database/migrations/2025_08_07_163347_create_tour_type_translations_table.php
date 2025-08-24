<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tour_type_translations')) {
            return;
        }

        Schema::create('tour_type_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tour_type_id')
                  ->constrained('tour_types', 'tour_type_id')
                  ->cascadeOnDelete();

            $table->string('locale', 5);
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('duration', 191)->nullable();
            $table->timestamps();

            $table->unique(['tour_type_id', 'locale'], 'tour_type_translations_tour_type_id_locale_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_type_translations');
    }
};
