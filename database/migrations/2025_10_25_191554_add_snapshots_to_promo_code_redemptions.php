<?php
// database/migrations/2025_10_25_000001_add_snapshots_to_promo_code_redemptions.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('promo_code_redemptions', function (Blueprint $table) {
            $table->decimal('applied_amount', 8, 2)->default(0)->after('user_id');
            $table->string('operation_snapshot', 12)->default('subtract')->after('applied_amount');
            $table->decimal('percent_snapshot', 5, 2)->nullable()->after('operation_snapshot');
            $table->decimal('amount_snapshot', 8, 2)->nullable()->after('percent_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('promo_code_redemptions', function (Blueprint $table) {
            $table->dropColumn(['applied_amount','operation_snapshot','percent_snapshot','amount_snapshot']);
        });
    }
};
