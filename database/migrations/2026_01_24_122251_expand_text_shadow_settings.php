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
        // Add text-muted shadow toggle
        BrandingSetting::firstOrCreate(
            ['key' => 'text_shadow_text_muted'],
            [
                'value' => '0',
                'type' => 'boolean',
                'category' => 'text_shadow',
                'description' => 'Aplicar sombra a .text-muted',
            ]
        );

        // Add breadcrumbs shadow toggle
        BrandingSetting::firstOrCreate(
            ['key' => 'text_shadow_breadcrumbs'],
            [
                'value' => '0',
                'type' => 'boolean',
                'category' => 'text_shadow',
                'description' => 'Aplicar sombra a breadcrumbs',
            ]
        );

        // Move text_shadow_enabled from layout to text_shadow category
        $setting = BrandingSetting::where('key', 'text_shadow_enabled')->first();
        if ($setting) {
            $setting->category = 'text_shadow';
            $setting->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        BrandingSetting::whereIn('key', [
            'text_shadow_text_muted',
            'text_shadow_breadcrumbs',
        ])->delete();

        // Move back to layout
        $setting = BrandingSetting::where('key', 'text_shadow_enabled')->first();
        if ($setting) {
            $setting->category = 'layout';
            $setting->save();
        }
    }
};
