<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // si no existen ya:
            $table->index(['is_active', 'expires_at']);
            $table->index('expires_at');
            $table->index('user_id');
            $table->index('created_at');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->index('cart_id');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex(['carts_is_active_expires_at_index']);
            $table->dropIndex(['carts_expires_at_index']);
            $table->dropIndex(['carts_user_id_index']);
            $table->dropIndex(['carts_created_at_index']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex(['cart_items_cart_id_index']);
        });
    }
};
