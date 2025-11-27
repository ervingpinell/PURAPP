<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'booking.max_future_days'],
            [
                'value' => '730',
                'category' => 'booking',
                'type' => 'integer',
                'label' => 'Ventana Máxima de Reserva (Días)',
                'description' => 'Número máximo de días en el futuro para permitir reservas (ej. 730 para 2 años).',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down()
    {
        DB::table('settings')->where('key', 'booking.max_future_days')->delete();
    }
};
