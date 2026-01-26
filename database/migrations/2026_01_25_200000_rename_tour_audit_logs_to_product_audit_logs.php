<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('tour_audit_logs') && !Schema::hasTable('product_audit_logs')) {
            Schema::rename('tour_audit_logs', 'product_audit_logs');
        }

        if (Schema::hasTable('product_audit_logs')) {
            Schema::table('product_audit_logs', function (Blueprint $table) {
                if (Schema::hasColumn('product_audit_logs', 'tour_id') && !Schema::hasColumn('product_audit_logs', 'product_id')) {
                    $table->renameColumn('tour_id', 'product_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_audit_logs')) {
            Schema::table('product_audit_logs', function (Blueprint $table) {
                if (Schema::hasColumn('product_audit_logs', 'product_id')) {
                    $table->renameColumn('product_id', 'tour_id');
                }
            });
            Schema::rename('product_audit_logs', 'tour_audit_logs');
        }
    }
};
