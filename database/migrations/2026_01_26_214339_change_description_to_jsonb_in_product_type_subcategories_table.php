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
        // First, convert existing text data to JSON format
        DB::statement("
            UPDATE product_type_subcategories 
            SET description = json_build_object('es', description)::jsonb
            WHERE description IS NOT NULL 
            AND description != ''
            AND description !~ '^\\s*\\{.*\\}\\s*$'
        ");

        // Change column type to jsonb using raw SQL (PostgreSQL requires USING clause)
        DB::statement("
            ALTER TABLE product_type_subcategories 
            ALTER COLUMN description TYPE jsonb 
            USING CASE 
                WHEN description IS NULL OR description = '' THEN NULL
                WHEN description ~ '^\\s*\\{.*\\}\\s*$' THEN description::jsonb
                ELSE json_build_object('es', description)::jsonb
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert JSON back to text (extract 'es' locale)
        DB::statement("
            UPDATE product_type_subcategories 
            SET description = description->>'es'
            WHERE description IS NOT NULL
        ");

        // Change column type back to text
        Schema::table('product_type_subcategories', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });
    }
};
