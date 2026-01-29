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
        $reviewsEmbedColors = [
            // Colores principales
            ['key' => 'reviews_embed_green', 'value' => '#1A5229', 'description' => 'Verde principal - títulos de product, enlaces (Reviews Embed)'],
            ['key' => 'reviews_embed_warn', 'value' => '#e74c3c', 'description' => 'Color para badge de review label (Reviews Embed)'],
            
            // Colores de texto
            ['key' => 'reviews_embed_text_dark', 'value' => '#222', 'description' => 'Texto principal del contenido (Reviews Embed)'],
            ['key' => 'reviews_embed_text_muted', 'value' => '#6c757d', 'description' => 'Texto secundario - fecha (Reviews Embed)'],
            ['key' => 'reviews_embed_text_rating', 'value' => '#555', 'description' => 'Número de rating (Reviews Embed)'],
            
            // Colores de fondo
            ['key' => 'reviews_embed_bg_white', 'value' => '#fff', 'description' => 'Fondo de la card (Reviews Embed)'],
            ['key' => 'reviews_embed_bg_avatar', 'value' => '#e9ecef', 'description' => 'Fondo del avatar por defecto (Reviews Embed)'],
            
            // Colores de botones/enlaces
            ['key' => 'reviews_embed_toggle_color', 'value' => '#256D1B', 'description' => 'Color del botón ver más/menos (Reviews Embed)'],
            
            // Colores de estrellas
            ['key' => 'reviews_embed_stars_color', 'value' => '#ffc107', 'description' => 'Color de las estrellas (Reviews Embed)'],
        ];

        foreach ($reviewsEmbedColors as $color) {
            BrandingSetting::firstOrCreate(
                ['key' => $color['key']],
                [
                    'value' => $color['value'],
                    'type' => 'color',
                    'category' => 'reviews_embed',
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
            'reviews_embed_green',
            'reviews_embed_warn',
            'reviews_embed_text_dark',
            'reviews_embed_text_muted',
            'reviews_embed_text_rating',
            'reviews_embed_bg_white',
            'reviews_embed_bg_avatar',
            'reviews_embed_toggle_color',
            'reviews_embed_stars_color',
        ];

        BrandingSetting::whereIn('key', $keys)->delete();
    }
};
