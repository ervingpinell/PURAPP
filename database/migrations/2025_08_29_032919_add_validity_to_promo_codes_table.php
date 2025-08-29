<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            // Usamos DATE (día completo en timezone local)
            $table->date('valid_from')->nullable()->after('discount_amount');
            $table->date('valid_until')->nullable()->after('valid_from');
        });

        // (Opcional pero recomendado si usas PostgreSQL)
        // Evita rangos inválidos: valid_until >= valid_from
        try {
            DB::statement("
                ALTER TABLE promo_codes
                ADD CONSTRAINT promo_codes_valid_dates
                CHECK (
                    valid_from IS NULL OR valid_until IS NULL OR valid_until >= valid_from
                )
            ");
        } catch (\Throwable $e) {
            // Ignorar si no aplica o ya existe (por compatibilidad con otros motores)
        }
    }

    public function down(): void
    {
        // Quitar el CHECK si existe
        try {
            DB::statement("ALTER TABLE promo_codes DROP CONSTRAINT IF EXISTS promo_codes_valid_dates");
        } catch (\Throwable $e) {}

        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropColumn(['valid_from', 'valid_until']);
        });
    }
};
