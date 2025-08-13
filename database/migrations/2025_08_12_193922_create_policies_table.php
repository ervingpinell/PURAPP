<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('policies', function (Blueprint $table) {
            $table->id('policy_id');
            $table->string('type');                 // cancelacion, reembolso, terminos, privacidad, etc.
            $table->string('name');                 // nombre interno visible en admin
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->timestamps();

            // Índices útiles
            $table->index(['type', 'is_active']);
            $table->index('is_default');
            $table->index(['effective_from', 'effective_to']);
            // (Opcional) Si quieres evitar duplicados por (type, name):
            // $table->unique(['type', 'name']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('policies');
    }
};
