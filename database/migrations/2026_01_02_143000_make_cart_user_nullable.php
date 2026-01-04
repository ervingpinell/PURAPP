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
        Schema::table('carts', function (Blueprint $table) {
            // Make user_id nullable
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Add guest fields
            $table->string('guest_email')->nullable()->after('user_id');
            $table->string('guest_name')->nullable()->after('guest_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Revert changes (be careful with existing nulls in production, but for dev rollback strict is okay)
            $table->dropColumn(['guest_email', 'guest_name']);
            // We cannot easily revert user_id to not null if there are nulls. 
            // We assume down is only used immediately if something goes wrong.
            // $table->unsignedBigInteger('user_id')->nullable(false)->change(); 
        });
    }
};
