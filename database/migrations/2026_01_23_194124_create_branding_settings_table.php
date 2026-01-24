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
        Schema::create('branding_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, color, file, boolean, number
            $table->string('category')->default('general'); // colors, logos, layout, effects
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default values from app.css
        $defaults = [
            // ===== COLORS =====
            ['key' => 'color_page_title', 'value' => '#2c6e49', 'type' => 'color', 'category' => 'colors', 'description' => 'Color for page titles and big-title class'],
            ['key' => 'color_button_primary', 'value' => '#256d1b', 'type' => 'color', 'category' => 'colors', 'description' => 'Primary button color (green accept buttons)'],
            ['key' => 'color_button_primary_hover', 'value' => '#1d5315', 'type' => 'color', 'category' => 'colors', 'description' => 'Primary button hover color (darker)'],
            ['key' => 'color_button_secondary', 'value' => '#2c6e49', 'type' => 'color', 'category' => 'colors', 'description' => 'Secondary button color'],
            ['key' => 'color_button_secondary_hover', 'value' => '#3a8f5f', 'type' => 'color', 'category' => 'colors', 'description' => 'Secondary button hover color (lighter and vibrant)'],
            ['key' => 'color_tab_primary', 'value' => '#1a5229', 'type' => 'color', 'category' => 'colors', 'description' => 'Primary tab color'],
            ['key' => 'color_surface_dark', 'value' => '#0f2419', 'type' => 'color', 'category' => 'colors', 'description' => 'Dark surface color for modals, header, footer, borders'],
            ['key' => 'color_card_accent', 'value' => '#e74c3c', 'type' => 'color', 'category' => 'colors', 'description' => 'Card title background color'],
            ['key' => 'price_color', 'value' => '#e74c3c', 'type' => 'color', 'category' => 'colors', 'description' => 'Price display color'],
            ['key' => 'alert_background', 'value' => '#ffe69c', 'type' => 'color', 'category' => 'colors', 'description' => 'Alert background color'],
            ['key' => 'color_link', 'value' => '#fff', 'type' => 'color', 'category' => 'colors', 'description' => 'Navigation and footer link color'],
            ['key' => 'color_link_hover', 'value' => '#8bc34a', 'type' => 'color', 'category' => 'colors', 'description' => 'Link hover color'],
            ['key' => 'color_text_on_accent', 'value' => '#fff', 'type' => 'color', 'category' => 'colors', 'description' => 'Text color on accent backgrounds'],
            ['key' => 'theme_card_background', 'value' => '#fff', 'type' => 'color', 'category' => 'colors', 'description' => 'Card background color'],
            ['key' => 'text_dark', 'value' => '#333', 'type' => 'color', 'category' => 'colors', 'description' => 'Dark text color'],
            ['key' => 'text_muted', 'value' => '#6b7280', 'type' => 'color', 'category' => 'colors', 'description' => 'Muted text color'],
            ['key' => 'text_light', 'value' => '#fff', 'type' => 'color', 'category' => 'colors', 'description' => 'Light text color'],
            ['key' => 'bg_white', 'value' => '#fff', 'type' => 'color', 'category' => 'colors', 'description' => 'White background'],
            ['key' => 'bg_light', 'value' => '#f9fafb', 'type' => 'color', 'category' => 'colors', 'description' => 'Light background'],
            ['key' => 'bg_soft', 'value' => '#f7faf7', 'type' => 'color', 'category' => 'colors', 'description' => 'Soft background'],
            ['key' => 'border_color', 'value' => '#d9d9d9', 'type' => 'color', 'category' => 'colors', 'description' => 'Border color'],
            ['key' => 'border_light', 'value' => '#dcdcdc', 'type' => 'color', 'category' => 'colors', 'description' => 'Light border color'],
            ['key' => 'border_gray', 'value' => '#e5e7eb', 'type' => 'color', 'category' => 'colors', 'description' => 'Gray border color'],

            // ===== LOGOS =====
            ['key' => 'logo_main', 'value' => '/images/branding/logo-placeholder.png', 'type' => 'file', 'category' => 'logos', 'description' => 'Main site logo'],
            ['key' => 'logo_adminlte', 'value' => '/images/branding/logo-admin-placeholder.png', 'type' => 'file', 'category' => 'logos', 'description' => 'AdminLTE dashboard logo'],
            ['key' => 'favicon', 'value' => '/images/branding/favicon-placeholder.png', 'type' => 'file', 'category' => 'logos', 'description' => 'Site favicon'],

            // ===== LAYOUT =====
            ['key' => 'hero_enabled', 'value' => '1', 'type' => 'boolean', 'category' => 'layout', 'description' => 'Enable/disable hero section'],
            ['key' => 'background_image', 'value' => '', 'type' => 'file', 'category' => 'layout', 'description' => 'Body background image'],
            ['key' => 'background_enabled', 'value' => '0', 'type' => 'boolean', 'category' => 'layout', 'description' => 'Enable/disable background image'],

            // ===== EFFECTS =====
            ['key' => 'header_opacity', 'value' => '0.95', 'type' => 'number', 'category' => 'effects', 'description' => 'Header opacity (0-1)'],
            ['key' => 'header_blur', 'value' => '10', 'type' => 'number', 'category' => 'effects', 'description' => 'Header blur in pixels'],
            ['key' => 'text_shadow_enabled', 'value' => '1', 'type' => 'boolean', 'category' => 'effects', 'description' => 'Enable/disable text shadows on titles'],
        ];

        foreach ($defaults as $setting) {
            DB::table('branding_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branding_settings');
    }
};
