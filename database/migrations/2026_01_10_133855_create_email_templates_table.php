<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Main templates table
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_key')->unique()->comment('Unique identifier: booking_created_customer, payment_success, etc.');
            $table->string('name')->comment('Human-readable name');
            $table->text('description')->nullable();
            $table->enum('category', ['customer', 'admin', 'other'])->default('customer');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category');
            $table->index('is_active');
        });

        // Template content per language
        Schema::create('email_template_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_template_id')->constrained()->onDelete('cascade');
            $table->string('locale', 5)->comment('Language code: es, en, de, fr, pt');
            $table->string('subject');
            $table->json('content')->comment('Editable sections as key-value pairs');
            $table->timestamps();

            $table->unique(['email_template_id', 'locale']);
            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_template_contents');
        Schema::dropIfExists('email_templates');
    }
};
