<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds unique constraint on cart_ref to prevent duplicate terms acceptance records
     * for the same cart. This ensures only one acceptance record per cart.
     */
    public function up(): void
    {
        // First, remove any duplicate records, keeping only the most recent one per cart_ref
        DB::statement("
            DELETE FROM terms_acceptances
            WHERE id NOT IN (
                SELECT MAX(id)
                FROM terms_acceptances
                WHERE cart_ref IS NOT NULL
                GROUP BY cart_ref
            )
            AND cart_ref IS NOT NULL
        ");

        // Add unique constraint on cart_ref
        Schema::table('terms_acceptances', function (Blueprint $table) {
            $table->unique('cart_ref');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terms_acceptances', function (Blueprint $table) {
            $table->dropUnique(['cart_ref']);
        });
    }
};
