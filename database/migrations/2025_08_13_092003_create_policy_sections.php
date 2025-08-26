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
            $table->string('key')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['policy_id', 'sort_order']);
            $table->index('is_active');

        });
    }

    public function down(): void {
        Schema::dropIfExists('policy_sections');
    }
};
