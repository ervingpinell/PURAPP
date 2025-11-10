<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Verifica tablas necesarias
        if (!Schema::hasTable('policy_sections')) return;

        // Detecta tabla de traducciones (nombre canónico)
        $trTable = Schema::hasTable('policy_section_translations')
            ? 'policy_section_translations'
            : (Schema::hasTable('policy_sections_translations') ? 'policy_sections_translations' : null);

        if (!$trTable) return;

        // 1) Backfill/Upsert ES desde policy_sections → translations
        //    - Si ya existe (section_id, 'es'), se SOBREESCRIBE con lo de la base.
        //    - Si no existe, se inserta.
        // Compat PostgreSQL 9.5+: ON CONFLICT
        DB::statement("
            INSERT INTO {$trTable} (section_id, locale, name, content, created_at, updated_at)
            SELECT s.section_id, 'es', s.name, s.content, NOW(), NOW()
            FROM policy_sections s
            WHERE s.name IS NOT NULL OR s.content IS NOT NULL
            ON CONFLICT (section_id, locale)
            DO UPDATE SET
                name = EXCLUDED.name,
                content = EXCLUDED.content,
                updated_at = NOW()
        ");

        // 2) Eliminar columnas (si existen) SIN doctrine/dbal
        // Nota: usa IF EXISTS para no fallar si ya se quitaron
        DB::statement("ALTER TABLE policy_sections DROP COLUMN IF EXISTS name");
        DB::statement("ALTER TABLE policy_sections DROP COLUMN IF EXISTS content");
    }

    public function down(): void
    {
        if (!Schema::hasTable('policy_sections')) return;

        $trTable = Schema::hasTable('policy_section_translations')
            ? 'policy_section_translations'
            : (Schema::hasTable('policy_sections_translations') ? 'policy_sections_translations' : null);

        // 1) Restaurar columnas base si no existen
        if (!Schema::hasColumn('policy_sections', 'name')) {
            DB::statement("ALTER TABLE policy_sections ADD COLUMN name varchar(255)");
        }
        if (!Schema::hasColumn('policy_sections', 'content')) {
            DB::statement("ALTER TABLE policy_sections ADD COLUMN content text");
        }

        // 2) Repoblar desde traducciones ES → base
        if ($trTable) {
            DB::statement("
                UPDATE policy_sections s
                SET name = t.name, content = t.content
                FROM {$trTable} t
                WHERE t.section_id = s.section_id
                  AND t.locale = 'es'
            ");
        }

        // 3) (Opcional) Si quieres revertir el backfill ES en traducciones, descomenta:
        // if ($trTable) {
        //     DB::table($trTable)->where('locale', 'es')->delete();
        // }
    }
};
