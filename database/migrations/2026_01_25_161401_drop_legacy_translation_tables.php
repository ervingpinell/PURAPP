<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'amenity_translations',
            'customer_category_translations',
            'faq_translations',
            'itinerary_item_translations',
            'itinerary_translations',
            'meeting_point_translations',
            'policy_section_translations',
            'policy_translations',
            'product_translations',
            'product_type_translations',
        ];

        foreach ($tables as $table) {
            DB::statement("DROP TABLE IF EXISTS {$table} CASCADE");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We cannot easily recreate these tables.
    }
};
