<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('customer_category_translations', function (Blueprint $table) {
            $table->id('translation_id');

            $table->foreignId('category_id')
                  ->constrained('customer_categories', 'category_id')
                  ->cascadeOnDelete();

            $table->string('locale', 10);
            $table->string('name');
            $table->timestamps();

            $table->unique(['category_id', 'locale']);
            $table->index('locale');
        });
    }

    public function down(): void {
        Schema::dropIfExists('customer_category_translations');
    }
};
