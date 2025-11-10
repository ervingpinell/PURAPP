<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Detecta tabla de traducciones (preferida plural)
        $translationsTable = null;
        if (Schema::hasTable('policies_translations')) {
            $translationsTable = 'policies_translations';
        } elseif (Schema::hasTable('policy_translations')) {
            $translationsTable = 'policy_translations';
        }

        // Requisitos mínimos
        if (!$translationsTable || !Schema::hasTable('policies')) {
            return;
        }

        // Determina cómo construir el contenido destino
        // Si existe policies.description, se concatena al final con doble salto de línea.
        $hasDescription = Schema::hasColumn('policies', 'description');
        $contentExpr = $hasDescription
            ? "COALESCE(p.content, '') || CASE WHEN p.description IS NOT NULL AND p.description <> '' THEN E'\n\n' || p.description ELSE '' END"
            : "p.content";

        // 1) Crear tabla backup si no existe
        //    Guarda el snapshot de las traducciones ES anteriores para rollback.
        DB::statement("
            CREATE TABLE IF NOT EXISTS policies_translations_backfill_backup (
                policy_id BIGINT PRIMARY KEY,
                old_name  TEXT NULL,
                old_content TEXT NULL,
                existed BOOLEAN NOT NULL DEFAULT FALSE
            )
        ");

        // Limpiamos posibles restos de una corrida anterior incompleta
        DB::statement("DELETE FROM policies_translations_backfill_backup");

        // 2) Insertar en backup:
        //    - Si ya existe fila ES en translations: guardar nombre y contenido y existed=true
        //    - Si no existe fila ES: guardar registro con existed=false (sin valores)
        DB::statement("
            INSERT INTO policies_translations_backfill_backup (policy_id, old_name, old_content, existed)
            SELECT p.policy_id,
                   t.name  AS old_name,
                   t.content AS old_content,
                   (t.policy_id IS NOT NULL) AS existed
            FROM policies p
            LEFT JOIN {$translationsTable} t
                   ON t.policy_id = p.policy_id AND t.locale = 'es'
        ");

        // 3) UPSERT a translations con los valores actuales de policies
        //    - Inserta si no existe
        //    - Si existe, sobrescribe name y content (como pediste)
        DB::statement("
            INSERT INTO {$translationsTable} (policy_id, locale, name, content, created_at, updated_at)
            SELECT p.policy_id, 'es', p.name, {$contentExpr}, NOW(), NOW()
            FROM policies p
            ON CONFLICT (policy_id, locale)
            DO UPDATE SET
                name = EXCLUDED.name,
                content = EXCLUDED.content,
                updated_at = NOW()
        ");
    }

    public function down(): void
    {
        // Detectar tabla de traducciones
        $translationsTable = null;
        if (Schema::hasTable('policies_translations')) {
            $translationsTable = 'policies_translations';
        } elseif (Schema::hasTable('policy_translations')) {
            $translationsTable = 'policy_translations';
        }

        if (!$translationsTable || !Schema::hasTable('policies_translations_backfill_backup')) {
            return;
        }

        // 1) Restaurar filas ES que existían previamente (existed=true)
        DB::statement("
            UPDATE {$translationsTable} t
            SET
                name = b.old_name,
                content = b.old_content,
                updated_at = NOW()
            FROM policies_translations_backfill_backup b
            WHERE b.existed = TRUE
              AND t.policy_id = b.policy_id
              AND t.locale = 'es'
        ");

        // 2) Eliminar filas ES que fueron creadas por el backfill (existed=false)
        DB::statement("
            DELETE FROM {$translationsTable} t
            USING policies_translations_backfill_backup b
            WHERE b.existed = FALSE
              AND t.policy_id = b.policy_id
              AND t.locale = 'es'
        ");

        // 3) Limpiar backup
        DB::statement("DROP TABLE IF EXISTS policies_translations_backfill_backup");
    }
};
