<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string'); // string, integer, boolean, json, email
            $table->string('category', 50)->index(); // cart, booking, email, payment, general
            $table->string('label');
            $table->text('description')->nullable();
            $table->text('validation_rules')->nullable(); // JSON
            $table->boolean('is_public')->default(false)->index();
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('updated_by')->references('user_id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
