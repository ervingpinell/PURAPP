<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->boolean('is_reserved')->default(false)->after('categories');
            $table->timestamp('reserved_at')->nullable()->after('is_reserved');
            $table->string('reservation_token', 64)->nullable()->after('reserved_at')->index();

            // Index for cleanup queries
            $table->index(['is_reserved', 'reserved_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex(['is_reserved', 'reserved_at']);
            $table->dropColumn(['is_reserved', 'reserved_at', 'reservation_token']);
        });
    }
};
