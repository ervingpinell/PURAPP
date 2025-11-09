<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('policy_translations')) {
            // Ya existe (quedó creada en el intento anterior). Salimos.
            return;
        }

        Schema::create('policy_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('policy_id')->index();
            $table->string('locale', 5)->index();        // es, en, fr, pt, de
            $table->string('name')->nullable();
            $table->longText('content')->nullable();
            $table->timestamps();

            $table->unique(['policy_id', 'locale']);

            $table->foreign('policy_id')
                  ->references('policy_id')->on('policies')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policy_translations');
    }
};
