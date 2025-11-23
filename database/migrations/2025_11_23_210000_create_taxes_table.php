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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id('tax_id');
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->decimal('rate', 8, 4);
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->enum('apply_to', ['per_person', 'subtotal', 'total'])->default('subtotal');
            $table->boolean('is_inclusive')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
