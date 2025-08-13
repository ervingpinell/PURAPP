<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('policy_section_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('section_id');
            $table->string('locale', 10);
            $table->string('title');       // subtítulo
            $table->longText('content');   // contenido del subtítulo
            $table->timestamps();

            $table->unique(['section_id','locale']);

            $table->foreign('section_id')
                ->references('section_id')->on('policy_sections')
                ->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('policy_section_translations');
    }
};
