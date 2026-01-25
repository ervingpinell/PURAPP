<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\BrandingSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        BrandingSetting::firstOrCreate(
            ['key' => 'color_cookie_accept_button'],
            [
                'value' => '#256d1b',
                'type' => 'color',
                'category' => 'colors',
                'description' => 'Color del botón Aceptar en cookie banner',
            ]
        );
        
        BrandingSetting::firstOrCreate(
            ['key' => 'color_cookie_accept_button_hover'],
            [
                'value' => '#1d5315',
                'type' => 'color',
                'category' => 'colors',
                'description' => 'Color hover del botón Aceptar en cookie banner',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        BrandingSetting::whereIn('key', ['color_cookie_accept_button', 'color_cookie_accept_button_hover'])->delete();
    }
};
