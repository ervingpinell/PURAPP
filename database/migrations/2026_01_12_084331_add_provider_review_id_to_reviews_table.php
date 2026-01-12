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
        Schema::table('reviews', function (Blueprint $table) {
            // Add provider_review_id to track external review IDs
            $table->string('provider_review_id')->nullable()->after('provider');

            // Add unique constraint to prevent duplicate external reviews
            $table->unique(['provider', 'provider_review_id'], 'provider_review_unique');

            // Add index for faster lookups
            $table->index('provider_review_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique('provider_review_unique');
            $table->dropIndex(['provider_review_id']);
            $table->dropColumn('provider_review_id');
        });
    }
};
