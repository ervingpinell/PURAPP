<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('schedule_tour', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->index()->after('schedule_id');
        });

        DB::table('schedule_tour')->update(['is_active' => true]);
    }

    public function down(): void
    {
        Schema::table('schedule_tour', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
