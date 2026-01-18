<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing constraint
        DB::statement("ALTER TABLE review_requests DROP CONSTRAINT IF EXISTS review_requests_status_check");
        
        // Re-add constraint with 'skipped'
        DB::statement("ALTER TABLE review_requests ADD CONSTRAINT review_requests_status_check CHECK (status::text = ANY (ARRAY['created'::text, 'sent'::text, 'reminded'::text, 'fulfilled'::text, 'expired'::text, 'skipped'::text]))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop updated constraint
        DB::statement("ALTER TABLE review_requests DROP CONSTRAINT IF EXISTS review_requests_status_check");

        // Revert to original allowed values
        DB::statement("ALTER TABLE review_requests ADD CONSTRAINT review_requests_status_check CHECK (status::text = ANY (ARRAY['created'::text, 'sent'::text, 'reminded'::text, 'fulfilled'::text, 'expired'::text]))");
    }
};
