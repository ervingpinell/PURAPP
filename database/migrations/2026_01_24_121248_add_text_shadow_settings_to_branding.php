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
        // Text shadow color
        BrandingSetting::firstOrCreate(
            ['key' => 'text_shadow_color'],
            [
                'value' => '#000000',
                'type' => 'color',
                'category' => 'text_shadow',
                'description' => 'Color de la sombra de texto',
            ]
        );

        // Text shadow horizontal offset
        BrandingSetting::firstOrCreate(
            ['key' => 'text_shadow_x'],
            [
                'value' => '2',
                'type' => 'number',
                'category' => 'text_shadow',
                'description' => 'Desplazamiento horizontal de la sombra (px)',
            ]
        );

        // Text shadow vertical offset
        BrandingSetting::firstOrCreate(
            ['key' => 'text_shadow_y'],
            [
                'value' => '2',
                'type' => 'number',
                'category' => 'text_shadow',
                'description' => 'Desplazamiento vertical de la sombra (px)',
            ]
        );

        // Text shadow blur
        BrandingSetting::firstOrCreate(
            ['key' => 'text_shadow_blur'],
            [
                'value' => '4',
                'type' => 'number',
                'category' => 'text_shadow',
                'description' => 'Difuminado de la sombra (px)',
            ]
        );

        // Text shadow opacity
        BrandingSetting::firstOrCreate(
            ['key' => 'text_shadow_opacity'],
            [
                'value' => '0.5',
                'type' => 'number',
                'category' => 'text_shadow',
                'description' => 'Opacidad de la sombra (0.0 - 1.0)',
            ]
        );

        // Enable/disable for specific elements
        BrandingSetting::firstOrCreate(
            ['key' => 'text_shadow_headings'],
            [
                'value' => '1',
                'type' => 'boolean',
                'category' => 'text_shadow',
                'description' => 'Aplicar sombra a encabezados (h1, h2, h3)',
            ]
        );

        BrandingSetting::firstOrCreate(
            ['key' => 'text_shadow_big_title'],
            [
                'value' => '1',
                'type' => 'boolean',
                'category' => 'text_shadow',
                'description' => 'Aplicar sombra a .big-title',
            ]
        );

        BrandingSetting::firstOrCreate(
            ['key' => 'text_shadow_lead'],
            [
                'value' => '1',
                'type' => 'boolean',
                'category' => 'text_shadow',
                'description' => 'Aplicar sombra a .lead',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        BrandingSetting::whereIn('key', [
            'text_shadow_color',
            'text_shadow_x',
            'text_shadow_y',
            'text_shadow_blur',
            'text_shadow_opacity',
            'text_shadow_headings',
            'text_shadow_big_title',
            'text_shadow_lead',
        ])->delete();
    }
};
