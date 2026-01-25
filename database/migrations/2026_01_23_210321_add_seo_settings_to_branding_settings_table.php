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
        // Add SEO settings to branding_settings table
        // Spanish versions are the source, other languages will be auto-translated
        $seoSettings = [
            // Home Page SEO
            [
                'key' => 'seo_home_title_es',
                'value' => 'Descubre Tours Increíbles en Costa Rica',
                'type' => 'text',
                'category' => 'seo',
                'description' => 'SEO title for home page (Spanish)',
            ],
            [
                'key' => 'seo_home_description_es',
                'value' => 'Explora las mejores aventuras y tours en Costa Rica. Reserva experiencias únicas con los mejores guías locales.',
                'type' => 'text',
                'category' => 'seo',
                'description' => 'SEO description for home page (Spanish)',
            ],

            // Tours Page SEO
            [
                'key' => 'seo_tours_title_es',
                'value' => 'Todos Nuestros Tours y Experiencias',
                'type' => 'text',
                'category' => 'seo',
                'description' => 'SEO title for tours page (Spanish)',
            ],
            [
                'key' => 'seo_tours_description_es',
                'value' => 'Descubre nuestra colección completa de tours y experiencias en Costa Rica. Encuentra la aventura perfecta para ti.',
                'type' => 'text',
                'category' => 'seo',
                'description' => 'SEO description for tours page (Spanish)',
            ],

            // Contact Page SEO
            [
                'key' => 'seo_contact_title_es',
                'value' => 'Contáctanos - Estamos Aquí para Ayudarte',
                'type' => 'text',
                'category' => 'seo',
                'description' => 'SEO title for contact page (Spanish)',
            ],
            [
                'key' => 'seo_contact_description_es',
                'value' => 'Ponte en contacto con nosotros. Nuestro equipo está listo para ayudarte a planificar tu próxima aventura en Costa Rica.',
                'type' => 'text',
                'category' => 'seo',
                'description' => 'SEO description for contact page (Spanish)',
            ],

            // Placeholder entries for auto-translated versions (will be populated by DeepL)
            // English
            ['key' => 'seo_home_title_en', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO title for home page (English - auto-translated)'],
            ['key' => 'seo_home_description_en', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO description for home page (English - auto-translated)'],
            ['key' => 'seo_tours_title_en', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO title for tours page (English - auto-translated)'],
            ['key' => 'seo_tours_description_en', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO description for tours page (English - auto-translated)'],
            ['key' => 'seo_contact_title_en', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO title for contact page (English - auto-translated)'],
            ['key' => 'seo_contact_description_en', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO description for contact page (English - auto-translated)'],

            // French
            ['key' => 'seo_home_title_fr', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO title for home page (French - auto-translated)'],
            ['key' => 'seo_home_description_fr', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO description for home page (French - auto-translated)'],
            ['key' => 'seo_tours_title_fr', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO title for tours page (French - auto-translated)'],
            ['key' => 'seo_tours_description_fr', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO description for tours page (French - auto-translated)'],
            ['key' => 'seo_contact_title_fr', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO title for contact page (French - auto-translated)'],
            ['key' => 'seo_contact_description_fr', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO description for contact page (French - auto-translated)'],

            // German
            ['key' => 'seo_home_title_de', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO title for home page (German - auto-translated)'],
            ['key' => 'seo_home_description_de', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO description for home page (German - auto-translated)'],
            ['key' => 'seo_tours_title_de', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO title for tours page (German - auto-translated)'],
            ['key' => 'seo_tours_description_de', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO description for tours page (German - auto-translated)'],
            ['key' => 'seo_contact_title_de', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO title for contact page (German - auto-translated)'],
            ['key' => 'seo_contact_description_de', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO description for contact page (German - auto-translated)'],

            // Portuguese
            ['key' => 'seo_home_title_pt', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO title for home page (Portuguese - auto-translated)'],
            ['key' => 'seo_home_description_pt', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO description for home page (Portuguese - auto-translated)'],
            ['key' => 'seo_tours_title_pt', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO title for tours page (Portuguese - auto-translated)'],
            ['key' => 'seo_tours_description_pt', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO description for tours page (Portuguese - auto-translated)'],
            ['key' => 'seo_contact_title_pt', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO title for contact page (Portuguese - auto-translated)'],
            ['key' => 'seo_contact_description_pt', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO description for contact page (Portuguese - auto-translated)'],
        ];

        foreach ($seoSettings as $setting) {
            DB::table('branding_settings')->insert($setting);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all SEO settings
        DB::table('branding_settings')->where('category', 'seo')->delete();
    }
};
