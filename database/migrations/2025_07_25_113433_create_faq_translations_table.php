<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaqTranslationsTable extends Migration
{
    public function up(): void
    {
Schema::create('faq_translations', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('faq_id');
    $table->string('locale', 5);
    $table->string('question');
    $table->text('answer')->nullable();
    $table->timestamps();

    $table->foreign('faq_id')->references('id')->on('faqs')->onDelete('cascade');
    $table->unique(['faq_id', 'locale']);
});
    }

    public function down(): void
    {
        Schema::dropIfExists('faq_translations');
    }
}
