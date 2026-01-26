<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop View dependent on product2.name
        DB::statement('DROP VIEW IF EXISTS v_booking_facts');
        
        // 2. Fix for Product2 table
        if (Schema::hasTable('product2')) {
             Schema::table('product2', function (Blueprint $table) {
                // Drop old columns if they exist
                if (Schema::hasColumn('product2', 'name')) $table->dropColumn('name');
                if (Schema::hasColumn('product2', 'description')) $table->dropColumn('description');
                if (Schema::hasColumn('product2', 'overview')) $table->dropColumn('overview');
                if (Schema::hasColumn('product2', 'recommendations')) $table->dropColumn('recommendations');
             });
             
             Schema::table('product2', function (Blueprint $table) {
                $table->json('name')->nullable();
                $table->json('description')->nullable();
                $table->json('overview')->nullable();
                $table->json('recommendations')->nullable();
             });
        }
        
        // 3. Recreate View (Adapting for JSON name)
        // We will extract the JSON value, prioritising English or Spanish or taking the whole JSON as text if DB supports it.
        // Postgres: name->>'en' gets text.
        // We can use COALESCE(name->>'en', name->>'es') to be safe.
        // Since we are using standard SQL for potential portability (though likely PGSQL here given 'pdo_pgsql' in composer):
        
        DB::statement("
            CREATE VIEW v_booking_facts AS
            SELECT 
                b.booking_id,
                b.product_id,
                b.booking_date,
                b.status,
                b.total as total_amount,
                b.created_at,
                b.user_id,
                CAST(p.name->>'en' AS VARCHAR) as product_name, 
                p.product_category,
                ptt.name as product_type_name,
                u.first_name as customer_first_name,
                u.last_name as customer_last_name,
                u.email as customer_email
            FROM bookings b
            LEFT JOIN product2 p ON b.product_id = p.product_id
            LEFT JOIN product_types pt ON p.product_type_id = pt.product_type_id
            LEFT JOIN product_type_translations ptt ON pt.product_type_id = ptt.product_type_id AND ptt.locale = 'es'
            LEFT JOIN users u ON b.user_id = u.user_id
        ");
    }

    public function down(): void
    {
        // ...
    }
};
