<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('policy_section_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')
                  ->constrained('policy_sections', 'section_id')
                  ->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('title');
            $table->longText('content')->nullable();
            $table->timestamps();

            $table->unique(['section_id', 'locale']);
            $table->index('locale');
        });
    }

    public function down(): void {
        Schema::dropIfExists('policy_section_translations');
    }
};
