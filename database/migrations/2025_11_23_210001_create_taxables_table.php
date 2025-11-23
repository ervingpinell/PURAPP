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
        Schema::create('taxables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tax_id');
            $table->unsignedBigInteger('taxable_id');
            $table->string('taxable_type');
            $table->timestamps();

            $table->foreign('tax_id')
                ->references('tax_id')
                ->on('taxes')
                ->onDelete('cascade');

            $table->index(['taxable_type', 'taxable_id']);
            $table->index('tax_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxables');
    }
};
