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
        DB::table('settings')->insert([
            'key' => 'taxes.included',
            'value' => '0',
            'type' => 'boolean',
            'category' => 'taxes',
            'label' => 'Impuestos Incluidos en Precio',
            'description' => 'Si está activado, los precios mostrados incluyen impuestos. Si está desactivado, los impuestos se agregan al precio base.',
            'validation_rules' => json_encode(['boolean']),
            'is_public' => false,
            'sort_order' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'taxes.included')->delete();
    }
};
