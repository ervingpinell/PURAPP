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
            $table->string('subcategory', 50)
                  ->nullable()
                  ->after('product_type_id')
                  ->comment('Subcategoría: full-day, private, etc');
            
            $table->index(['product_type_id', 'subcategory']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product2', function (Blueprint $table) {
            $table->dropIndex(['product_type_id', 'subcategory']);
            $table->dropColumn('subcategory');
        });
    }
};
