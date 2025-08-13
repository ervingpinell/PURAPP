<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('policy_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('policy_id');
            $table->string('locale', 10);   // es, en, pt_BR, etc.
            $table->string('title');
            $table->longText('content');
            $table->timestamps();

            $table->unique(['policy_id', 'locale']); // una traducción por idioma
            $table->foreign('policy_id')
                ->references('policy_id')
                ->on('policies')
                ->cascadeOnDelete();

            $table->index('locale');
        });
    }

    public function down(): void {
        Schema::dropIfExists('policy_translations');
    }
};
