<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('policies', function (Blueprint $table) {
            $table->id('policy_id');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->timestamps();
            $table->index('name');
            $table->index('is_active');
            $table->index(['effective_from', 'effective_to']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('policies');
    }
};
