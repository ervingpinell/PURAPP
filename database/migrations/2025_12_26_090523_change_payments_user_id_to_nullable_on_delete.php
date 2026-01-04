<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Change payments.user_id foreign key from CASCADE to SET NULL
     * to preserve payment history when users are deleted.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // 1. Drop existing foreign key constraint
            $table->dropForeign(['user_id']);

            // 2. Make user_id nullable
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // 3. Re-add foreign key with SET NULL on delete
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('set null');  // Changed from cascade
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // 1. Drop the SET NULL foreign key
            $table->dropForeign(['user_id']);

            // 2. Make user_id NOT nullable again
            $table->unsignedBigInteger('user_id')->nullable(false)->change();

            // 3. Re-add foreign key with CASCADE (original behavior)
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};
