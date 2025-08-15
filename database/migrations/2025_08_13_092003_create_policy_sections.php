<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('policy_sections', function (Blueprint $table) {
            $table->id('section_id');
            $table->foreignId('policy_id')
                  ->constrained('policies', 'policy_id')
                  ->cascadeOnDelete();
            $table->string('key')->nullable();     // identificador opcional (interno)
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['policy_id', 'sort_order']);
            $table->index('is_active');
            // Nota: "key" es palabra reservada en MySQL, pero Laravel la cita con backticks.
            // Si prefieres evitarlo, usa "code" o "slug".
        });
    }

    public function down(): void {
        Schema::dropIfExists('policy_sections');
    }
};
