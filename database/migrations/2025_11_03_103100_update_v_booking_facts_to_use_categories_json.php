<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement(<<<'SQL'
CREATE OR REPLACE VIEW v_booking_facts AS
SELECT
  b.booking_id,
  b.user_id,
  b.tour_id,
  bd.tour_language_id,
  b.booking_reference,
  b.booking_date,
  b.status,
  COALESCE(b.total, 0) AS booking_total,

  /* detail_total desde categories */
  COALESCE(SUM( ((c.cat->>'quantity')::int) * ((c.cat->>'price')::numeric) ), 0) AS detail_total,

  /* qty por slug (cast a INT para mantener tipo previo) */
  COALESCE( (SUM( (c.cat->>'quantity')::int ) FILTER (WHERE c.cat->>'category_slug' = 'adult'))::int, 0) AS adults_qty,
  COALESCE( (SUM( (c.cat->>'quantity')::int ) FILTER (WHERE c.cat->>'category_slug' = 'kid'))::int,   0) AS kids_qty,

  /* precio unitario por slug (si hay varios, toma el MAX) */
  COALESCE(MAX( (c.cat->>'price')::numeric ) FILTER (WHERE c.cat->>'category_slug' = 'adult'), 0) AS adult_price,
  COALESCE(MAX( (c.cat->>'price')::numeric ) FILTER (WHERE c.cat->>'category_slug' = 'kid'),   0) AS kid_price,

  bd.tour_date,
  bd.schedule_id,
  bd.hotel_id,
  b.is_active,
  DATE_TRUNC('month', b.booking_date)::date AS month_bucket
FROM bookings b
JOIN booking_details bd ON bd.booking_id = b.booking_id
LEFT JOIN LATERAL jsonb_array_elements( COALESCE(bd.categories::jsonb, '[]'::jsonb) ) AS c(cat) ON TRUE
GROUP BY
  b.booking_id, b.user_id, b.tour_id, bd.tour_language_id, b.booking_reference, b.booking_date, b.status,
  bd.tour_date, bd.schedule_id, bd.hotel_id, b.is_active;
SQL);
        } elseif ($driver === 'mysql') {
            // Requiere MySQL 8.0+ por JSON_TABLE
            DB::statement(<<<'SQL'
CREATE OR REPLACE VIEW v_booking_facts AS
SELECT
  b.booking_id,
  b.user_id,
  b.tour_id,
  bd.tour_language_id,
  b.booking_reference,
  b.booking_date,
  b.status,
  IFNULL(b.total, 0) AS booking_total,

  /* detail_total desde categories */
  IFNULL(SUM( jt.quantity * jt.price ), 0) AS detail_total,

  /* qty por slug */
  IFNULL(SUM( CASE WHEN jt.category_slug = 'adult' THEN jt.quantity ELSE 0 END ), 0) AS adults_qty,
  IFNULL(SUM( CASE WHEN jt.category_slug = 'kid'   THEN jt.quantity ELSE 0 END ), 0) AS kids_qty,

  /* precio unitario por slug (si hay varios, toma el MAX) */
  IFNULL(MAX( CASE WHEN jt.category_slug = 'adult' THEN jt.price END ), 0) AS adult_price,
  IFNULL(MAX( CASE WHEN jt.category_slug = 'kid'   THEN jt.price END ), 0) AS kid_price,

  bd.tour_date,
  bd.schedule_id,
  bd.hotel_id,
  b.is_active,
  DATE(DATE_FORMAT(b.booking_date, '%Y-%m-01')) AS month_bucket
FROM bookings b
JOIN booking_details bd ON bd.booking_id = b.booking_id
LEFT JOIN JSON_TABLE(
  IFNULL(bd.categories, JSON_ARRAY()),
  '$[*]' COLUMNS (
    category_slug VARCHAR(64) PATH '$.category_slug',
    quantity      INT         PATH '$.quantity',
    price         DECIMAL(10,2) PATH '$.price'
  )
) AS jt ON TRUE
GROUP BY
  b.booking_id, b.user_id, b.tour_id, bd.tour_language_id, b.booking_reference, b.booking_date, b.status,
  bd.tour_date, bd.schedule_id, bd.hotel_id, b.is_active;
SQL);
        } elseif ($driver === 'sqlsrv') {
            DB::statement(<<<'SQL'
IF OBJECT_ID('v_booking_facts', 'V') IS NOT NULL
    DROP VIEW v_booking_facts;
EXEC('
CREATE VIEW v_booking_facts AS
SELECT
  b.booking_id,
  b.user_id,
  b.tour_id,
  bd.tour_language_id,
  b.booking_reference,
  b.booking_date,
  b.status,
  ISNULL(b.total, 0) AS booking_total,

  /* detail_total desde categories */
  ISNULL(SUM(CASE WHEN jt.price IS NOT NULL AND jt.quantity IS NOT NULL
                  THEN jt.quantity * jt.price ELSE 0 END), 0) AS detail_total,

  /* qty por slug */
  ISNULL(SUM(CASE WHEN jt.category_slug = ''adult'' THEN jt.quantity ELSE 0 END), 0) AS adults_qty,
  ISNULL(SUM(CASE WHEN jt.category_slug = ''kid''   THEN jt.quantity ELSE 0 END), 0) AS kids_qty,

  /* precio unitario por slug (MAX) */
  ISNULL(MAX(CASE WHEN jt.category_slug = ''adult'' THEN jt.price END), 0) AS adult_price,
  ISNULL(MAX(CASE WHEN jt.category_slug = ''kid''   THEN jt.price END), 0) AS kid_price,

  bd.tour_date,
  bd.schedule_id,
  bd.hotel_id,
  b.is_active,
  DATEFROMPARTS(YEAR(b.booking_date), MONTH(b.booking_date), 1) AS month_bucket
FROM bookings b
JOIN booking_details bd ON bd.booking_id = b.booking_id
OUTER APPLY OPENJSON(COALESCE(bd.categories, ''[]'')) WITH (
    category_slug nvarchar(64) ''$.category_slug'',
    quantity      int          ''$.quantity'',
    price         decimal(10,2) ''$.price''
) AS jt
GROUP BY
  b.booking_id, b.user_id, b.tour_id, bd.tour_language_id, b.booking_reference, b.booking_date, b.status,
  bd.tour_date, bd.schedule_id, bd.hotel_id, b.is_active;
');
SQL);
        }
    }

    public function down(): void
    {
        // Volver a la definición vieja (con columnas legacy).
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement(<<<'SQL'
CREATE OR REPLACE VIEW v_booking_facts AS
SELECT
  b.booking_id,
  b.user_id,
  b.tour_id,
  bd.tour_language_id,
  b.booking_reference,
  b.booking_date,
  b.status,
  COALESCE(b.total, 0)            AS booking_total,
  COALESCE(bd.total, 0)           AS detail_total,
  COALESCE(bd.adults_quantity, 0) AS adults_qty,
  COALESCE(bd.kids_quantity, 0)   AS kids_qty,
  COALESCE(bd.adult_price, 0)     AS adult_price,
  COALESCE(bd.kid_price, 0)       AS kid_price,
  bd.tour_date,
  bd.schedule_id,
  bd.hotel_id,
  b.is_active,
  DATE_TRUNC(''month'', b.booking_date)::date AS month_bucket
FROM bookings b
JOIN booking_details bd ON bd.booking_id = b.booking_id;
SQL);
        } elseif ($driver === 'mysql') {
            DB::statement(<<<'SQL'
CREATE OR REPLACE VIEW v_booking_facts AS
SELECT
  b.booking_id,
  b.user_id,
  b.tour_id,
  bd.tour_language_id,
  b.booking_reference,
  b.booking_date,
  b.status,
  IFNULL(b.total, 0)            AS booking_total,
  IFNULL(bd.total, 0)           AS detail_total,
  IFNULL(bd.adults_quantity, 0) AS adults_qty,
  IFNULL(bd.kids_quantity, 0)   AS kids_qty,
  IFNULL(bd.adult_price, 0)     AS adult_price,
  IFNULL(bd.kid_price, 0)       AS kid_price,
  bd.tour_date,
  bd.schedule_id,
  bd.hotel_id,
  b.is_active,
  DATE(DATE_FORMAT(b.booking_date, '%Y-%m-01')) AS month_bucket
FROM bookings b
JOIN booking_details bd ON bd.booking_id = b.booking_id;
SQL);
        } elseif ($driver === 'sqlsrv') {
            DB::statement(<<<'SQL'
IF OBJECT_ID('v_booking_facts', 'V') IS NOT NULL
    DROP VIEW v_booking_facts;
EXEC('
CREATE VIEW v_booking_facts AS
SELECT
  b.booking_id,
  b.user_id,
  b.tour_id,
  bd.tour_language_id,
  b.booking_reference,
  b.booking_date,
  b.status,
  ISNULL(b.total, 0)            AS booking_total,
  ISNULL(bd.total, 0)           AS detail_total,
  ISNULL(bd.adults_quantity, 0) AS adults_qty,
  ISNULL(bd.kids_quantity, 0)   AS kids_qty,
  ISNULL(bd.adult_price, 0)     AS adult_price,
  ISNULL(bd.kid_price, 0)       AS kid_price,
  bd.tour_date,
  bd.schedule_id,
  bd.hotel_id,
  b.is_active,
  DATEFROMPARTS(YEAR(b.booking_date), MONTH(b.booking_date), 1) AS month_bucket
FROM bookings b
JOIN booking_details bd ON bd.booking_id = b.booking_id;
');
SQL);
        }
    }
};
