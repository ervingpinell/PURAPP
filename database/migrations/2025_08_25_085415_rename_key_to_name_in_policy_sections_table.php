<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('policy_sections')
            && Schema::hasColumn('policy_sections', 'key')
            && !Schema::hasColumn('policy_sections', 'name')) {
            Schema::table('policy_sections', function (Blueprint $table) {
                $table->renameColumn('key', 'name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('policy_sections')
            && Schema::hasColumn('policy_sections', 'name')
            && !Schema::hasColumn('policy_sections', 'key')) {
            Schema::table('policy_sections', function (Blueprint $table) {
                $table->renameColumn('name', 'key');
            });
        }
    }
};
