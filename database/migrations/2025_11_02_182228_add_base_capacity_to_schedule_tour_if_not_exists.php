<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_tour', function (Blueprint $table) {
            if (!Schema::hasColumn('schedule_tour', 'base_capacity')) {
                $table->unsignedInteger('base_capacity')->nullable()->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('schedule_tour', function (Blueprint $table) {
            if (Schema::hasColumn('schedule_tour', 'base_capacity')) {
                $table->dropColumn('base_capacity');
            }
        });
    }
};
