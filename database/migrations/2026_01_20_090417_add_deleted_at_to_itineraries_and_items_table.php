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
        Schema::table('itineraries', function (Blueprint $table) {
            if (!Schema::hasColumn('itineraries', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('itinerary_items', function (Blueprint $table) {
             if (!Schema::hasColumn('itinerary_items', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('itinerary_items', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
