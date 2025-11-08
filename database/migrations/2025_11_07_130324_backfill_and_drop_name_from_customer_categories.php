<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Backfill: copiar name -> translations (locale app por defecto, ej. es)
        $defaultLocale = substr(strtolower(config('app.locale', 'es')), 0, 2);

        $rows = DB::table('customer_categories')
            ->select('category_id','name')
            ->whereNotNull('name')
            ->get();

        foreach ($rows as $r) {
            DB::table('customer_category_translations')->updateOrInsert(
                ['category_id' => $r->category_id, 'locale' => $defaultLocale],
                ['name' => $r->name, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // 2) Quitar columna name de la tabla principal
        Schema::table('customer_categories', function (Blueprint $table) {
            if (Schema::hasColumn('customer_categories', 'name')) {
                $table->dropColumn('name');
            }
        });

        // 3) (Opcional pero recomendado) índice único de slug
        // Postgres-friendly e idempotente
        DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS customer_categories_slug_unique ON customer_categories (slug)");
    }

    public function down(): void
    {
        // 1) Restaurar columna name
        Schema::table('customer_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_categories', 'name')) {
                $table->string('name')->nullable()->after('slug');
            }
        });

        // 2) Intentar rellenar name con el locale por defecto (si existe en traducciones)
        $defaultLocale = substr(strtolower(config('app.locale', 'es')), 0, 2);

        $rows = DB::table('customer_category_translations')
            ->select('category_id','name')
            ->where('locale', $defaultLocale)
            ->get();

        foreach ($rows as $r) {
            DB::table('customer_categories')
                ->where('category_id', $r->category_id)
                ->update(['name' => $r->name]);
        }

        // 3) Quitar índice si hace falta (no obligatorio)
        DB::statement("DROP INDEX IF EXISTS customer_categories_slug_unique");
    }
};
