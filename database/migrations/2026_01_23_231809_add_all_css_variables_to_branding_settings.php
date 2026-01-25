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
        $colors = [
            // Títulos de Página
            ['key' => 'color_page_title', 'value' => '#2c6e49', 'description' => 'Para big-title y títulos principales'],
            
            // Botones y Acciones
            ['key' => 'color_button_primary', 'value' => '#256d1b', 'description' => 'Botones de aceptación/verdes'],
            ['key' => 'color_button_primary_hover', 'value' => '#1d5315', 'description' => 'Hover de botones (más oscuro)'],
            ['key' => 'color_button_secondary', 'value' => '#2c6e49', 'description' => 'Botones secundarios/verdes'],
            ['key' => 'color_button_secondary_hover', 'value' => '#3a8f5f', 'description' => 'Hover de botones secundarios (más claro y vibrante)'],
            ['key' => 'color_tab_primary', 'value' => '#1a5229', 'description' => 'Color de tabs primarios'],
            
            // Backgrounds y Bordes
            ['key' => 'color_surface_dark', 'value' => '#0f2419', 'description' => 'Backgrounds de modales, header, footer, bordes'],
            
            // Cards y Badges
            ['key' => 'color_card_accent', 'value' => '#e74c3c', 'description' => 'Background de títulos en cards'],
            ['key' => 'price_color', 'value' => '#e74c3c', 'description' => 'Color de precios'],
            ['key' => 'alert_background', 'value' => '#ffe69c', 'description' => 'Background de alertas'],
            
            // Enlaces y Texto
            ['key' => 'color_link', 'value' => '#fff', 'description' => 'Nav links y footer links'],
            ['key' => 'color_link_hover', 'value' => '#8bc34a', 'description' => 'Hover para todos los enlaces'],
            ['key' => 'color_text_on_accent', 'value' => '#fff', 'description' => 'Texto sobre backgrounds de acento'],
            
            // Backgrounds de Cards
            ['key' => 'theme_card_background', 'value' => '#fff', 'description' => 'Background de cards (independiente del color del título)'],
            
            // Colores de Texto
            ['key' => 'text_dark', 'value' => '#333', 'description' => 'Texto oscuro'],
            ['key' => 'text_muted', 'value' => '#6b7280', 'description' => 'Texto atenuado'],
            ['key' => 'text_light', 'value' => '#fff', 'description' => 'Texto claro'],
            
            // Colores de Fondo
            ['key' => 'bg_white', 'value' => '#fff', 'description' => 'Fondo blanco'],
            ['key' => 'bg_light', 'value' => '#f9fafb', 'description' => 'Fondo claro'],
            ['key' => 'bg_soft', 'value' => '#f7faf7', 'description' => 'Fondo suave'],
            
            // Bordes y Separadores
            ['key' => 'border_color', 'value' => '#d9d9d9', 'description' => 'Color de bordes'],
            ['key' => 'border_light', 'value' => '#dcdcdc', 'description' => 'Borde claro'],
            ['key' => 'border_gray', 'value' => '#e5e7eb', 'description' => 'Borde gris'],
        ];

        foreach ($colors as $color) {
            // Only create if it doesn't exist
            BrandingSetting::firstOrCreate(
                ['key' => $color['key']],
                [
                    'value' => $color['value'],
                    'type' => 'color',
                    'category' => 'colors',
                    'description' => $color['description'],
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = [
            'color_page_title',
            'color_button_primary',
            'color_button_primary_hover',
            'color_button_secondary',
            'color_button_secondary_hover',
            'color_tab_primary',
            'color_surface_dark',
            'color_card_accent',
            'price_color',
            'alert_background',
            'color_link',
            'color_link_hover',
            'color_text_on_accent',
            'theme_card_background',
            'text_dark',
            'text_muted',
            'text_light',
            'bg_white',
            'bg_light',
            'bg_soft',
            'border_color',
            'border_light',
            'border_gray',
        ];

        BrandingSetting::whereIn('key', $keys)->delete();
    }
};
