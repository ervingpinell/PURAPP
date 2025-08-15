<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('policy_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_id')
                  ->constrained('policies', 'policy_id')
                  ->cascadeOnDelete();
            $table->string('locale', 10);          // ej: es, en, pt_BR
            $table->string('title');               // título por idioma
            $table->longText('content')->nullable(); // descripción por idioma
            $table->timestamps();

            $table->unique(['policy_id', 'locale']);
            $table->index('locale');
        });
    }

    public function down(): void {
        Schema::dropIfExists('policy_translations');
    }
};
