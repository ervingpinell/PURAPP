<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Evita error si por algún motivo aún no existe
        if (! Schema::hasTable('policy_translations')) {
            return;
        }

        // Inserta ES desde policies sólo si no existe ya la traducción ES
        DB::statement("
            INSERT INTO policy_translations (policy_id, locale, name, content, created_at, updated_at)
            SELECT p.policy_id, 'es', p.name, p.content, NOW(), NOW()
            FROM policies p
            WHERE NOT EXISTS (
                SELECT 1 FROM policy_translations t
                WHERE t.policy_id = p.policy_id AND t.locale = 'es'
            )
        ");
    }

    public function down(): void
    {
        if (Schema::hasTable('policy_translations')) {
            DB::table('policy_translations')->where('locale', 'es')->delete();
        }
    }
};
