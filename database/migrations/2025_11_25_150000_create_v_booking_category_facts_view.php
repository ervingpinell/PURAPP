<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        // Add GIN index for faster JSON queries
        if ($driver === 'pgsql') {
            // Create GIN index for JSON queries (PostgreSQL)
            // Note: categories is json type, so we cast to jsonb for the index
            DB::statement("
                CREATE INDEX IF NOT EXISTS idx_booking_details_categories_gin 
                ON booking_details USING GIN ((categories::jsonb))
            ");

            // Create category facts view
            DB::statement(
                <<<'SQL'
CREATE OR REPLACE VIEW v_booking_category_facts AS
SELECT
  b.booking_id,
  b.booking_reference,
  b.booking_date,
  b.status,
  b.user_id,
  bd.details_id,
  bd.tour_date,
  bd.tour_id,
  bd.tour_language_id,
  bd.schedule_id,
  bd.hotel_id,
  -- Category details from JSON
  (cat->>'category_id')::int AS category_id,
  cat->>'category_name' AS category_name,
  cat->>'category_slug' AS category_slug,
  (cat->>'quantity')::int AS quantity,
  (cat->>'price')::numeric(10,2) AS unit_price,
  ((cat->>'quantity')::int * (cat->>'price')::numeric(10,2)) AS line_total,
  -- Date buckets for aggregation
  DATE_TRUNC('day', b.booking_date)::date AS day_bucket,
  DATE_TRUNC('week', b.booking_date)::date AS week_bucket,
  DATE_TRUNC('month', b.booking_date)::date AS month_bucket,
  DATE_TRUNC('year', b.booking_date)::date AS year_bucket,
  -- Tour date buckets
  DATE_TRUNC('day', bd.tour_date)::date AS tour_day_bucket,
  DATE_TRUNC('week', bd.tour_date)::date AS tour_week_bucket,
  DATE_TRUNC('month', bd.tour_date)::date AS tour_month_bucket,
  DATE_TRUNC('year', bd.tour_date)::date AS tour_year_bucket,
  b.is_active
FROM bookings b
JOIN booking_details bd ON bd.booking_id = b.booking_id
LEFT JOIN LATERAL jsonb_array_elements(COALESCE(bd.categories::jsonb, '[]'::jsonb)) AS cat ON TRUE
WHERE cat IS NOT NULL;
SQL
            );
        } elseif ($driver === 'mysql') {
            DB::statement(
                <<<'SQL'
CREATE OR REPLACE VIEW v_booking_category_facts AS
SELECT
  b.booking_id,
  b.booking_reference,
  b.booking_date,
  b.status,
  b.user_id,
  bd.details_id,
  bd.tour_date,
  bd.tour_id,
  bd.tour_language_id,
  bd.schedule_id,
  bd.hotel_id,
  -- Category details from JSON
  jt.category_id,
  jt.category_name,
  jt.category_slug,
  jt.quantity,
  jt.price AS unit_price,
  (jt.quantity * jt.price) AS line_total,
  -- Date buckets
  DATE(b.booking_date) AS day_bucket,
  DATE(DATE_FORMAT(b.booking_date, '%Y-%m-01')) AS month_bucket,
  DATE(DATE_FORMAT(b.booking_date, '%Y-01-01')) AS year_bucket,
  DATE(bd.tour_date) AS tour_day_bucket,
  DATE(DATE_FORMAT(bd.tour_date, '%Y-%m-01')) AS tour_month_bucket,
  DATE(DATE_FORMAT(bd.tour_date, '%Y-01-01')) AS tour_year_bucket,
  b.is_active
FROM bookings b
JOIN booking_details bd ON bd.booking_id = b.booking_id
LEFT JOIN JSON_TABLE(
  IFNULL(bd.categories, JSON_ARRAY()),
  '$[*]' COLUMNS (
    category_id INT PATH '$.category_id',
    category_name VARCHAR(255) PATH '$.category_name',
    category_slug VARCHAR(64) PATH '$.category_slug',
    quantity INT PATH '$.quantity',
    price DECIMAL(10,2) PATH '$.price'
  )
) AS jt ON TRUE
WHERE jt.category_id IS NOT NULL;
SQL
            );
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        DB::statement("DROP VIEW IF EXISTS v_booking_category_facts");

        if ($driver === 'pgsql') {
            DB::statement("DROP INDEX IF EXISTS idx_booking_details_categories_gin");
        }
    }
};
