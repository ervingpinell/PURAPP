<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('policy_sections', function (Blueprint $table) {
            $table->id('section_id');
            $table->unsignedBigInteger('policy_id');      // FK a categories (policies)
            $table->string('key')->nullable();            // opcional: clave interna
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('policy_id')
                ->references('policy_id')->on('policies')
                ->cascadeOnDelete();

            $table->index(['policy_id', 'is_active']);
            $table->index('sort_order');
        });
    }

    public function down(): void {
        Schema::dropIfExists('policy_sections');
    }
};
