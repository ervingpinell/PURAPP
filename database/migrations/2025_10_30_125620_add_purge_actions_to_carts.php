<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // carts
        DB::statement('CREATE INDEX IF NOT EXISTS carts_is_active_expires_at_index ON carts (is_active, expires_at)');
        DB::statement('CREATE INDEX IF NOT EXISTS carts_expires_at_index ON carts (expires_at)');
        DB::statement('CREATE INDEX IF NOT EXISTS carts_user_id_index ON carts (user_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS carts_created_at_index ON carts (created_at)');

        // cart_items
        DB::statement('CREATE INDEX IF NOT EXISTS cart_items_cart_id_index ON cart_items (cart_id)');
    }

    public function down(): void
    {
        // carts
        DB::statement('DROP INDEX IF EXISTS carts_is_active_expires_at_index');
        DB::statement('DROP INDEX IF EXISTS carts_expires_at_index');
        DB::statement('DROP INDEX IF EXISTS carts_user_id_index');
        DB::statement('DROP INDEX IF EXISTS carts_created_at_index');

        // cart_items
        DB::statement('DROP INDEX IF EXISTS cart_items_cart_id_index');
    }
};
