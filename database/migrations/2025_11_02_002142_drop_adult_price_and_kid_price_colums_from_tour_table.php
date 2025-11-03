<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            // Eliminar columnas hardcodeadas de precios
            $table->dropColumn(['adult_price', 'kid_price']);
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            // Restaurar columnas (por si necesitas rollback)
            $table->decimal('adult_price', 10, 2)->nullable()->after('overview');
            $table->decimal('kid_price', 10, 2)->nullable()->after('adult_price');
        });
    }
};
