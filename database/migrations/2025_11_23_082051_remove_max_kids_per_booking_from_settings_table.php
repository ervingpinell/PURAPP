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
        DB::table('settings')->where('key', 'booking.max_kids_per_booking')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->insert([
            'key' => 'booking.max_kids_per_booking',
            'value' => '2',
            'type' => 'integer',
            'category' => 'booking',
            'label' => 'Máximo de niños por reserva',
            'description' => 'Límite global de niños permitidos por reserva',
            'validation_rules' => 'required|integer|min:0',
            'is_public' => false,
            'sort_order' => 20,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
