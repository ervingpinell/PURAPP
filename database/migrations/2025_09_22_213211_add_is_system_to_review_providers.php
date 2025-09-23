<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Agregar columnas si no existen (sin mover posición: Postgres ignora ->after())
        Schema::table('review_providers', function (Blueprint $table) {
            if (!Schema::hasColumn('review_providers', 'is_system')) {
                $table->boolean('is_system')->default(false); // ->after('is_active') se omite en PG
            }
            if (!Schema::hasColumn('review_providers', 'driver')) {
                $table->string('driver')->nullable(); // ->after('name') se omite en PG
            }
        });

        // 2) Índice/constraint UNIQUE en slug si no existe (PostgreSQL)
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            // Verificar por nombre de índice en pg_indexes
            $exists = DB::table('pg_indexes')
                ->where('schemaname', 'public')
                ->where('indexname', 'review_providers_slug_unique')
                ->exists();

            if (!$exists) {
                // Crea un UNIQUE INDEX si no existe
                DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS review_providers_slug_unique ON review_providers (slug)');
            }
        } else {
            // Fallback simple para MySQL/MariaDB (por si lo usas en otro entorno)
            $indexes = collect(DB::select('SHOW INDEX FROM review_providers'))
                ->pluck('Key_name')->all();

            if (!in_array('review_providers_slug_unique', $indexes, true)) {
                Schema::table('review_providers', function (Blueprint $table) {
                    $table->unique('slug', 'review_providers_slug_unique');
                });
            }
        }
    }

    public function down(): void
    {
        // Quitar columna is_system si existe
        Schema::table('review_providers', function (Blueprint $table) {
            if (Schema::hasColumn('review_providers', 'is_system')) {
                $table->dropColumn('is_system');
            }
            // No tocamos 'driver' ni el UNIQUE si no quieres afectar otros entornos
        });

        // (Opcional) Si quieres revertir también el índice en PG:
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS review_providers_slug_unique');
        } else {
            Schema::table('review_providers', function (Blueprint $table) {
                // Evita excepción si no existe
                try { $table->dropUnique('review_providers_slug_unique'); } catch (\Throwable $e) {}
            });
        }
    }
};
