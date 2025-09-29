<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tour_translations', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_translations', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }
            // Evita duplicados de slug por locale globalmente
            if (!Schema::hasColumn('tour_translations', 'slug')) return;

            // índice compuesto útil: (locale, slug)
            $hasComposite = false;
            // (no hay API directa para detectar índices compuestos; asumimos que no existe)
            if (!$hasComposite) {
                $table->unique(['locale', 'slug'], 'tour_tr_locale_slug_unique');
            }

            // unicidad por tour+locale sigue siendo útil:
            // $table->unique(['tour_id', 'locale']); // si no existe
        });
    }

    public function down(): void
    {
        Schema::table('tour_translations', function (Blueprint $table) {
            if (Schema::hasColumn('tour_translations', 'slug')) {
                $table->dropUnique('tour_tr_locale_slug_unique');
                $table->dropColumn('slug');
            }
        });
    }
};
