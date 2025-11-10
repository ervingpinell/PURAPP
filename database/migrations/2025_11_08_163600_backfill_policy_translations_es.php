<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 0) Detecta tabla de traducciones (preferida en plural)
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

        // 1) Determina de forma segura qué usar como "name" y "content" desde policies
        //    (tu tabla policies NO tiene name/content, así que usamos slug como name por defecto)
        $nameExpr = Schema::hasColumn('policies', 'name')        ? "p.name"
                 : (Schema::hasColumn('policies', 'title')       ? "p.title"
                 : (Schema::hasColumn('policies', 'slug')        ? "p.slug"
                 :                                               "'(sin nombre)'" ));

        $contentExpr = Schema::hasColumn('policies', 'content')      ? "p.content"
                     : (Schema::hasColumn('policies', 'description') ? "p.description"
                     :                                               "NULL");

        // 2) Crear tabla backup si no existe (para permitir rollback)
        DB::statement("
            CREATE TABLE IF NOT EXISTS policies_translations_backfill_backup (
                policy_id   BIGINT PRIMARY KEY,
                old_name    TEXT NULL,
                old_content TEXT NULL,
                existed     BOOLEAN NOT NULL DEFAULT FALSE
            )
        ");

        // Limpia restos de una corrida anterior
        DB::statement("DELETE FROM policies_translations_backfill_backup");

        // 3) Guardar snapshot previo de traducciones 'es'
        DB::statement("
            INSERT INTO policies_translations_backfill_backup (policy_id, old_name, old_content, existed)
            SELECT p.policy_id,
                   t.name    AS old_name,
                   t.content AS old_content,
                   (t.policy_id IS NOT NULL) AS existed
            FROM policies p
            LEFT JOIN {$translationsTable} t
              ON t.policy_id = p.policy_id
             AND t.locale = 'es'
        ");

        // 4) UPSERT en tabla de traducciones con datos provenientes de policies
        DB::statement("
            INSERT INTO {$translationsTable} (policy_id, locale, name, content, created_at, updated_at)
            SELECT p.policy_id, 'es', {$nameExpr}, {$contentExpr}, NOW(), NOW()
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
               SET name      = b.old_name,
                   content   = b.old_content,
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
