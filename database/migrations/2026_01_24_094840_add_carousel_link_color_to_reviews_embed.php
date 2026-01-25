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
            ['key' => 'reviews_embed_link_color'],
            [
                'value' => '#1A5229',
                'type' => 'color',
                'category' => 'reviews_embed',
                'description' => 'Color de enlaces en títulos de tours (Reviews Embed)',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        BrandingSetting::where('key', 'reviews_embed_link_color')->delete();
    }
};
