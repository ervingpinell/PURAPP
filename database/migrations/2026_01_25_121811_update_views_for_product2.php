<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop vistas existentes
        DB::statement('DROP VIEW IF EXISTS v_booking_facts');
        DB::statement('DROP VIEW IF EXISTS v_booking_category_facts');
        
        // Recrear v_booking_facts con nuevos nombres
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
                p.name as product_name,
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
        
        // Recrear v_booking_category_facts
        /*
        DB::statement("
            CREATE VIEW v_booking_category_facts AS
            SELECT 
                bd.id as booking_detail_id,
                bd.booking_id,
                bd.product_id,
                bd.customer_category_id,
                bd.quantity,
                bd.unit_price,
                bd.subtotal,
                cc.slug as category_slug,
                p.name as product_name,
                p.product_category
            FROM booking_details bd
            LEFT JOIN customer_categories cc ON bd.customer_category_id = cc.category_id
            LEFT JOIN product2 p ON bd.product_id = p.product_id
        ");
        */
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_booking_facts');
        DB::statement('DROP VIEW IF EXISTS v_booking_category_facts');
        
        // Recrear vistas originales si es necesario
        // (copiar de migraciones originales)
    }
};
