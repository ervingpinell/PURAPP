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
        Schema::table('product2', function (Blueprint $table) {
            $table->unsignedBigInteger('product_subtype_id')
                  ->nullable()
                  ->after('product_type_id')
                  ->comment('Subtipo del producto (Full Day, Private, etc.)');
            
            $table->foreign('product_subtype_id')
                  ->references('subtype_id')
                  ->on('product_type_subcategories')
                  ->nullOnDelete();
            
            $table->index('product_subtype_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product2', function (Blueprint $table) {
            $table->dropForeign(['product_subtype_id']);
            $table->dropIndex(['product_subtype_id']);
            $table->dropColumn('product_subtype_id');
        });
    }
};
