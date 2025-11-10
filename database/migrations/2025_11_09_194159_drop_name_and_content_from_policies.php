<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Solo continuamos si existe la tabla base
        if (!Schema::hasTable('policies')) {
            return;
        }

        // Asegura que ya hiciste el backfill a translations antes de borrar columnas
        // (no paramos si no existe translations, pero advertimos en comentario)
        $translationsTable = null;
        if (Schema::hasTable('policies_translations')) {
            $translationsTable = 'policies_translations';
        } elseif (Schema::hasTable('policy_translations')) {
            $translationsTable = 'policy_translations';
        }

        // Quitar columnas si existen (sin requerir doctrine/dbal)
        if (Schema::hasColumn('policies', 'name')) {
            DB::statement('ALTER TABLE policies DROP COLUMN IF EXISTS name');
        }
        if (Schema::hasColumn('policies', 'content')) {
            DB::statement('ALTER TABLE policies DROP COLUMN IF EXISTS content');
        }

        // Si quisieras también eliminar "description", descomenta:
        // if (Schema::hasColumn('policies', 'description')) {
        //     DB::statement('ALTER TABLE policies DROP COLUMN IF EXISTS description');
        // }
    }

    public function down(): void
    {
        if (!Schema::hasTable('policies')) {
            return;
        }

        // Detectar tabla de traducciones para repoblar desde 'es'
        $translationsTable = null;
        if (Schema::hasTable('policies_translations')) {
            $translationsTable = 'policies_translations';
        } elseif (Schema::hasTable('policy_translations')) {
            $translationsTable = 'policy_translations';
        }

        // 1) Volver a crear columnas si no existen
        if (!Schema::hasColumn('policies', 'name')) {
            DB::statement('ALTER TABLE policies ADD COLUMN name varchar(255)');
        }
        if (!Schema::hasColumn('policies', 'content')) {
            DB::statement('ALTER TABLE policies ADD COLUMN content text');
        }

        // 2) Repoblar desde translations (locale = 'es'), si existe la tabla
        if ($translationsTable) {
            DB::statement("
                UPDATE policies p
                SET
                    name    = t.name,
                    content = t.content
                FROM {$translationsTable} t
                WHERE t.policy_id = p.policy_id
                  AND t.locale = 'es'
            ");
        }
    }
};
