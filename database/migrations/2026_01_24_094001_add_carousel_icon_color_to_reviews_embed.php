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
            ['key' => 'reviews_embed_carousel_icon'],
            [
                'value' => '#1A5229',
                'type' => 'color',
                'category' => 'reviews_embed',
                'description' => 'Color de iconos del carrusel (Reviews Embed)',
            ]
        );
        
        // Also add footer opacity
        BrandingSetting::firstOrCreate(
            ['key' => 'footer_opacity'],
            [
                'value' => '0.95',
                'type' => 'number',
                'category' => 'effects',
                'description' => 'Footer opacity (0.0 to 1.0)',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        BrandingSetting::whereIn('key', ['reviews_embed_carousel_icon', 'footer_opacity'])->delete();
    }
};
