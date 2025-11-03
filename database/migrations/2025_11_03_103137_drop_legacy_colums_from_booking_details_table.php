<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_details', function (Blueprint $table) {
            // Eliminar columnas legacy si existen
            if (Schema::hasColumn('booking_details', 'adults_quantity')) {
                $table->dropColumn('adults_quantity');
            }
            if (Schema::hasColumn('booking_details', 'kids_quantity')) {
                $table->dropColumn('kids_quantity');
            }
            if (Schema::hasColumn('booking_details', 'adult_price')) {
                $table->dropColumn('adult_price');
            }
            if (Schema::hasColumn('booking_details', 'kid_price')) {
                $table->dropColumn('kid_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('booking_details', function (Blueprint $table) {
            // Restaurar columnas legacy (por si necesitas rollback)
            if (!Schema::hasColumn('booking_details', 'adults_quantity')) {
                $table->integer('adults_quantity')->nullable()->after('tour_language_id');
            }
            if (!Schema::hasColumn('booking_details', 'kids_quantity')) {
                $table->integer('kids_quantity')->nullable()->after('adults_quantity');
            }
            if (!Schema::hasColumn('booking_details', 'adult_price')) {
                $table->decimal('adult_price', 10, 2)->nullable()->after('kids_quantity');
            }
            if (!Schema::hasColumn('booking_details', 'kid_price')) {
                $table->decimal('kid_price', 10, 2)->nullable()->after('adult_price');
            }
        });
    }
};
