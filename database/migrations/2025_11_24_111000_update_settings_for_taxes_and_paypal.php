<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Remove obsolete 'taxes.included' setting
        DB::table('settings')->where('key', 'taxes.included')->delete();

        // 2. Add 'payment.gateway.paypal' setting
        DB::table('settings')->insert([
            'key' => 'payment.gateway.paypal',
            'value' => '0', // Disabled by default
            'type' => 'boolean',
            'category' => 'payment',
            'label' => 'PayPal',
            'description' => 'Habilitar pagos con PayPal',
            'sort_order' => 2, // After Stripe (assuming Stripe is 1)
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Remove 'payment.gateway.paypal' setting
        DB::table('settings')->where('key', 'payment.gateway.paypal')->delete();

        // 2. Restore 'taxes.included' setting (defaulting to false as per original migration)
        DB::table('settings')->insert([
            'key' => 'taxes.included',
            'value' => '0',
            'type' => 'boolean',
            'category' => 'taxes',
            'label' => 'Impuestos Incluidos',
            'description' => 'Si está activado, los precios de los tours se mostrarán con impuestos incluidos.',
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
