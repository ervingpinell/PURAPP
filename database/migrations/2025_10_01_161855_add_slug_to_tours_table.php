<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tours', 'slug')) {
            Schema::table('tours', function ($table) {
                $table->string('slug', 255)->nullable()->unique()->after('name');
                $table->index('slug');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tours', 'slug')) {
            Schema::table('tours', function ($table) {
                $table->dropIndex(['slug']);
                $table->dropColumn('slug');
            });
        }
    }
};
