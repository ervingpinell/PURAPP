<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->unsignedInteger('usage_limit')->nullable()->after('valid_until'); // null = ilimitado
            $table->unsignedInteger('usage_count')->default(0)->after('usage_limit');
        });
    }

    public function down(): void
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropColumn(['usage_limit', 'usage_count']);
        });
    }
};
