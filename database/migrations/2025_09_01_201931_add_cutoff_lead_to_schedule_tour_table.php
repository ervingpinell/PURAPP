<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('schedule_tour', function (Blueprint $table) {
            $table->string('cutoff_hour', 5)->nullable()->after('is_active');
            $table->unsignedTinyInteger('lead_days')->nullable()->after('cutoff_hour');
        });
    }

    public function down(): void {
        Schema::table('schedule_tour', function (Blueprint $table) {
            $table->dropColumn(['cutoff_hour', 'lead_days']);
        });
    }
};
