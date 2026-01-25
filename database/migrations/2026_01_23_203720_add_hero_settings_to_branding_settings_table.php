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
        // Add hero settings to branding_settings table
        $heroSettings = [
            [
                'key' => 'hero_title',
                'value' => 'Welcome to Your Adventure',
                'type' => 'text',
                'category' => 'hero',
                'description' => 'Main hero section title',
            ],
            [
                'key' => 'hero_subtitle',
                'value' => 'Discover amazing experiences and create unforgettable memories',
                'type' => 'text',
                'category' => 'hero',
                'description' => 'Hero section subtitle/description',
            ],
            [
                'key' => 'hero_button_text',
                'value' => 'Explore Tours',
                'type' => 'text',
                'category' => 'hero',
                'description' => 'Hero CTA button text',
            ],
            [
                'key' => 'hero_button_link',
                'value' => '/tours',
                'type' => 'text',
                'category' => 'hero',
                'description' => 'Hero CTA button URL',
            ],
            [
                'key' => 'hero_image',
                'value' => '',
                'type' => 'file',
                'category' => 'hero',
                'description' => 'Hero section background/feature image',
            ],
        ];

        foreach ($heroSettings as $setting) {
            DB::table('branding_settings')->insert($setting);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove hero settings
        DB::table('branding_settings')->whereIn('key', [
            'hero_title',
            'hero_subtitle',
            'hero_button_text',
            'hero_button_link',
            'hero_image',
        ])->delete();
    }
};
