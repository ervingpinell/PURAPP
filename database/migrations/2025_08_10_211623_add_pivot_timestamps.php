<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tour_language_tour', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_language_tour', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('tour_language_tour', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        Schema::table('amenity_tour', function (Blueprint $table) {
            if (!Schema::hasColumn('amenity_tour', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('amenity_tour', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        Schema::table('excluded_amenity_tour', function (Blueprint $table) {
            if (!Schema::hasColumn('excluded_amenity_tour', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('excluded_amenity_tour', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        Schema::table('schedule_tour', function (Blueprint $table) {
            if (!Schema::hasColumn('schedule_tour', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('schedule_tour', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tour_language_tour', function (Blueprint $table) {
            if (Schema::hasColumn('tour_language_tour', 'created_at')) $table->dropColumn('created_at');
            if (Schema::hasColumn('tour_language_tour', 'updated_at')) $table->dropColumn('updated_at');
        });

        Schema::table('amenity_tour', function (Blueprint $table) {
            if (Schema::hasColumn('amenity_tour', 'created_at')) $table->dropColumn('created_at');
            if (Schema::hasColumn('amenity_tour', 'updated_at')) $table->dropColumn('updated_at');
        });

        Schema::table('excluded_amenity_tour', function (Blueprint $table) {
            if (Schema::hasColumn('excluded_amenity_tour', 'created_at')) $table->dropColumn('created_at');
            if (Schema::hasColumn('excluded_amenity_tour', 'updated_at')) $table->dropColumn('updated_at');
        });

        Schema::table('schedule_tour', function (Blueprint $table) {
            if (Schema::hasColumn('schedule_tour', 'created_at')) $table->dropColumn('created_at');
            if (Schema::hasColumn('schedule_tour', 'updated_at')) $table->dropColumn('updated_at');
        });
    }
};
