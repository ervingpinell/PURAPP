<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('policy_section_translations')
            && Schema::hasColumn('policy_section_translations', 'title')
            && !Schema::hasColumn('policy_section_translations', 'name')) {
            Schema::table('policy_section_translations', function (Blueprint $table) {
                $table->renameColumn('title', 'name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('policy_section_translations')
            && Schema::hasColumn('policy_section_translations', 'name')
            && !Schema::hasColumn('policy_section_translations', 'title')) {
            Schema::table('policy_section_translations', function (Blueprint $table) {
                $table->renameColumn('name', 'title');
            });
        }
    }
};
