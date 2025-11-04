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
            echo "\n⚠️  Saltando: tabla tour_availability no existe.\n";
            return;
        }

        // === 1) schedule_id (columna + índice + FK) ===
        Schema::table('tour_availability', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_availability', 'schedule_id')) {
                // Nota: evita ->after(...) para PostgreSQL
                $table->unsignedBigInteger('schedule_id')->nullable();
            }
        });

        // Índice (solo si no existe)
        $idxSchedule = 'tour_availability_schedule_id_index';
        $idxExists = DB::table('pg_indexes')
            ->where('schemaname', 'public')
            ->where('tablename', 'tour_availability')
            ->where('indexname', $idxSchedule)
            ->exists();

        if (Schema::hasColumn('tour_availability', 'schedule_id') && !$idxExists) {
            Schema::table('tour_availability', function (Blueprint $table) use ($idxSchedule) {
                $table->index('schedule_id', $idxSchedule);
            });
        }

        // FK (solo si no existe y si existe la PK en schedules)
        $fkName = 'tour_availability_schedule_id_foreign';
        $fkMissing = !DB::table('information_schema.table_constraints')
            ->where('constraint_type', 'FOREIGN KEY')
            ->where('table_name', 'tour_availability')
            ->where('constraint_name', $fkName)
            ->exists();

        $schedulesPk = Schema::hasColumn('schedules', 'schedule_id') ? 'schedule_id'
            : (Schema::hasColumn('schedules', 'id') ? 'id' : null);

        if ($fkMissing && $schedulesPk && Schema::hasColumn('tour_availability', 'schedule_id')) {
            DB::statement(sprintf(
                'ALTER TABLE "tour_availability"
                 ADD CONSTRAINT %s FOREIGN KEY ("schedule_id")
                 REFERENCES "schedules" ("%s") ON DELETE CASCADE',
                $fkName,
                $schedulesPk
            ));
        }

        // === 2) max_capacity (solo si no existe) ===
        Schema::table('tour_availability', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_availability', 'max_capacity')) {
                $table->integer('max_capacity')->nullable();
            }
        });

        // === 3) is_blocked / available ===
        // Si no existe is_blocked, créalo (no uses renameColumn si no tienes doctrine/dbal)
        Schema::table('tour_availability', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_availability', 'is_blocked')) {
                $table->boolean('is_blocked')->default(false);
            }
        });

        // Si aún existe 'available', puedes migrar datos (opcional) y luego eliminarla
        if (Schema::hasColumn('tour_availability', 'available')) {
            // Si 'available' significaba "está disponible", entonces is_blocked = NOT available
            // Descomenta si quieres migrar el valor:
            // DB::statement('UPDATE "tour_availability" SET "is_blocked" = NOT "available"');

            Schema::table('tour_availability', function (Blueprint $table) {
                $table->dropColumn('available');
            });
        }

        // === 4) Eliminar start_time / end_time si existen ===
        Schema::table('tour_availability', function (Blueprint $table) {
            $toDrop = [];
            if (Schema::hasColumn('tour_availability', 'start_time')) $toDrop[] = 'start_time';
            if (Schema::hasColumn('tour_availability', 'end_time'))   $toDrop[] = 'end_time';
            if ($toDrop) {
                $table->dropColumn($toDrop);
            }
        });

        // === 5) Índices compuestos (solo si no existen) ===
        $idx1 = 'tour_availability_tour_id_date_index';
        $idx1Exists = DB::table('pg_indexes')
            ->where('schemaname', 'public')
            ->where('tablename', 'tour_availability')
            ->where('indexname', $idx1)
            ->exists();

        if (!$idx1Exists && Schema::hasColumn('tour_availability', 'tour_id') && Schema::hasColumn('tour_availability', 'date')) {
            Schema::table('tour_availability', function (Blueprint $table) use ($idx1) {
                $table->index(['tour_id', 'date'], $idx1);
            });
        }

        $idx2 = 'tour_availability_tour_id_schedule_id_date_index';
        $idx2Exists = DB::table('pg_indexes')
            ->where('schemaname', 'public')
            ->where('tablename', 'tour_availability')
            ->where('indexname', $idx2)
            ->exists();

        if (
            !$idx2Exists &&
            Schema::hasColumn('tour_availability', 'tour_id') &&
            Schema::hasColumn('tour_availability', 'schedule_id') &&
            Schema::hasColumn('tour_availability', 'date')
        ) {
            Schema::table('tour_availability', function (Blueprint $table) use ($idx2) {
                $table->index(['tour_id', 'schedule_id', 'date'], $idx2);
            });
        }

        echo "\n✅ tour_availability actualizado (idempotente).\n";
    }

    public function down(): void
    {
        if (!Schema::hasTable('tour_availability')) return;

        // Quitar FK si existe
        $fkName = 'tour_availability_schedule_id_foreign';
        DB::statement('ALTER TABLE "tour_availability" DROP CONSTRAINT IF EXISTS ' . $fkName);

        // Quitar índices si existen
        foreach ([
            'tour_availability_schedule_id_index',
            'tour_availability_tour_id_date_index',
            'tour_availability_tour_id_schedule_id_date_index',
        ] as $idx) {
            DB::statement('DROP INDEX IF EXISTS ' . $idx);
        }

        // Quitar columnas si existen
        Schema::table('tour_availability', function (Blueprint $table) {
            if (Schema::hasColumn('tour_availability', 'schedule_id'))  $table->dropColumn('schedule_id');
            if (Schema::hasColumn('tour_availability', 'max_capacity')) $table->dropColumn('max_capacity');
            if (Schema::hasColumn('tour_availability', 'is_blocked'))   $table->dropColumn('is_blocked');

            // Restaurar columnas antiguas si no existen
            if (!Schema::hasColumn('tour_availability', 'start_time'))  $table->time('start_time')->nullable();
            if (!Schema::hasColumn('tour_availability', 'end_time'))    $table->time('end_time')->nullable();
            if (!Schema::hasColumn('tour_availability', 'available'))   $table->boolean('available')->default(true);
        });

        echo "\n↩️  tour_availability revertido (si aplicaba).\n";
    }
};
