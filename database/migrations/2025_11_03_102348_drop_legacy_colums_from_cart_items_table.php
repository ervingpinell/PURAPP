<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Eliminar columnas legacy si existen
            if (Schema::hasColumn('cart_items', 'adults_quantity')) {
                $table->dropColumn('adults_quantity');
            }
            if (Schema::hasColumn('cart_items', 'kids_quantity')) {
                $table->dropColumn('kids_quantity');
            }
            if (Schema::hasColumn('cart_items', 'adult_price')) {
                $table->dropColumn('adult_price');
            }
            if (Schema::hasColumn('cart_items', 'kid_price')) {
                $table->dropColumn('kid_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Restaurar columnas legacy (por si necesitas rollback)
            if (!Schema::hasColumn('cart_items', 'adults_quantity')) {
                $table->integer('adults_quantity')->nullable()->after('other_hotel_name');
            }
            if (!Schema::hasColumn('cart_items', 'kids_quantity')) {
                $table->integer('kids_quantity')->nullable()->after('adults_quantity');
            }
            if (!Schema::hasColumn('cart_items', 'adult_price')) {
                $table->decimal('adult_price', 10, 2)->nullable()->after('kids_quantity');
            }
            if (!Schema::hasColumn('cart_items', 'kid_price')) {
                $table->decimal('kid_price', 10, 2)->nullable()->after('adult_price');
            }
        });
    }
};
