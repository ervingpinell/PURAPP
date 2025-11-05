<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tour_availability')) {
            return;
        }

        // 1) Asegurar is_active con default true
        Schema::table('tour_availability', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_availability', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });

        // Forzar default true aunque ya exista la columna (Postgres)
        // Nota: usa USING para castear si fuera necesario
        DB::statement('ALTER TABLE "tour_availability" ALTER COLUMN "is_active" SET DEFAULT true');

        // 2) Limpiar duplicados antes de UNIQUE (conserva el más reciente)
        DB::statement(<<<SQL
WITH ranked AS (
  SELECT
    availability_id,
    ROW_NUMBER() OVER (
      PARTITION BY tour_id, schedule_id, date
      ORDER BY availability_id DESC
    ) AS rn
  FROM tour_availability
)
DELETE FROM tour_availability ta
USING ranked r
WHERE ta.availability_id = r.availability_id
  AND r.rn > 1;
SQL);

        // 3) Agregar UNIQUE (tour_id, schedule_id, date) si no existe
        $uniq = 'tour_availability_tour_schedule_date_unique';
        $exists = DB::selectOne(<<<SQL
SELECT 1
FROM information_schema.table_constraints
WHERE table_name = 'tour_availability'
  AND constraint_type = 'UNIQUE'
  AND constraint_name = ?
SQL, [$uniq]);

        if (!$exists) {
            DB::statement(<<<SQL
ALTER TABLE "tour_availability"
ADD CONSTRAINT {$uniq}
UNIQUE ("tour_id","schedule_id","date");
SQL);
        }

        // 4) (Opcional) FK a tours si no existe
        $fk = 'tour_availability_tour_id_foreign';
        $fkMissing = !DB::table('information_schema.table_constraints')
            ->where('table_name', 'tour_availability')
            ->where('constraint_type', 'FOREIGN KEY')
            ->where('constraint_name', $fk)
            ->exists();

        if ($fkMissing && Schema::hasColumn('tour_availability', 'tour_id')) {
            DB::statement(<<<SQL
ALTER TABLE "tour_availability"
ADD CONSTRAINT {$fk}
FOREIGN KEY ("tour_id") REFERENCES "tours" ("tour_id") ON DELETE CASCADE
SQL);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('tour_availability')) return;

        // Quitar UNIQUE
        DB::statement('ALTER TABLE "tour_availability" DROP CONSTRAINT IF EXISTS tour_availability_tour_schedule_date_unique');

        // Quitar FK opcional
        DB::statement('ALTER TABLE "tour_availability" DROP CONSTRAINT IF EXISTS tour_availability_tour_id_foreign');

        // (Opcional) quitar default
        DB::statement('ALTER TABLE "tour_availability" ALTER COLUMN "is_active" DROP DEFAULT');
    }
};
