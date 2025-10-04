<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Solo agregar si NO existe
        if (! Schema::hasColumn('policies', 'slug')) {
            Schema::table('policies', function (Blueprint $table) {
                $table->string('slug', 255)->nullable()->after('title');
                // Si quieres índice único (permite múltiples NULL en Postgres):
                // $table->unique('slug');
            });
        }
    }

    public function down(): void
    {
        // Solo borrar si SÍ existe
        if (Schema::hasColumn('policies', 'slug')) {
            Schema::table('policies', function (Blueprint $table) {
                // Si creaste índice único arriba, bórralo primero:
                // $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            });
        }
    }
};
