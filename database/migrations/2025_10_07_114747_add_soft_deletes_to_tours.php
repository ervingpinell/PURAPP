<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tours', function (Blueprint $t) {
            if (!Schema::hasColumn('tours', 'deleted_at')) {
                $t->softDeletes(); // agrega deleted_at
            }
        });
    }
    public function down(): void {
        Schema::table('tours', function (Blueprint $t) {
            if (Schema::hasColumn('tours', 'deleted_at')) {
                $t->dropSoftDeletes();
            }
        });
    }
};
