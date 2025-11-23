<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added this line for DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Delete obsolete settings
        $obsoleteKeys = [
            'booking.min_adults',
            'cart.extend_minutes',
            'cart.max_extensions',
            'booking.max_kids_per_booking', // Just in case
        ];
        DB::table('settings')->whereIn('key', $obsoleteKeys)->delete();

        // 2. Update refund percent to 100
        DB::table('settings')
            ->where('key', 'booking.cancellation_refund_percent')
            ->update(['value' => '100']);

        // 3. Ensure booking.max_persons_per_booking exists (and fix key if it was booking.max_persons)
        // First, check if the wrong key exists and rename it
        $wrongKey = DB::table('settings')->where('key', 'booking.max_persons')->first();
        if ($wrongKey) {
            DB::table('settings')
                ->where('key', 'booking.max_persons')
                ->update(['key' => 'booking.max_persons_per_booking']);
        } else {
            // If not, create it if it doesn't exist
            if (!DB::table('settings')->where('key', 'booking.max_persons_per_booking')->exists()) {
                DB::table('settings')->insert([
                    'key' => 'booking.max_persons_per_booking',
                    'value' => '12',
                    'type' => 'integer',
                    'category' => 'booking',
                    'label' => 'Máximo de personas por reserva',
                    'description' => 'Número máximo de personas permitidas en una sola reserva',
                    'validation_rules' => json_encode(['required', 'integer', 'min:1', 'max:50']),
                    'sort_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't restore deleted settings as they are obsolete.
        // We can revert the refund percent change if needed, but it's just a value change.
        DB::table('settings')
            ->where('key', 'booking.cancellation_refund_percent')
            ->update(['value' => '80']);
    }
};
