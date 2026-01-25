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
        // Add keywords fields for each page and language
        $keywords = [
            // Spanish (source)
            ['key' => 'seo_home_keywords_es', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO keywords for home page (Spanish)'],
            ['key' => 'seo_tours_keywords_es', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO keywords for tours page (Spanish)'],
            ['key' => 'seo_contact_keywords_es', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO keywords for contact page (Spanish)'],
            
            // English (auto-translated)
            ['key' => 'seo_home_keywords_en', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO keywords for home page (English - auto-translated)'],
            ['key' => 'seo_tours_keywords_en', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO keywords for tours page (English - auto-translated)'],
            ['key' => 'seo_contact_keywords_en', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO keywords for contact page (English - auto-translated)'],
            
            // French (auto-translated)
            ['key' => 'seo_home_keywords_fr', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO keywords for home page (French - auto-translated)'],
            ['key' => 'seo_tours_keywords_fr', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO keywords for tours page (French - auto-translated)'],
            ['key' => 'seo_contact_keywords_fr', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO keywords for contact page (French - auto-translated)'],
            
            // German (auto-translated)
            ['key' => 'seo_home_keywords_de', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO keywords for home page (German - auto-translated)'],
            ['key' => 'seo_tours_keywords_de', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO keywords for tours page (German - auto-translated)'],
            ['key' => 'seo_contact_keywords_de', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO keywords for contact page (German - auto-translated)'],
            
            // Portuguese (auto-translated)
            ['key' => 'seo_home_keywords_pt', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO keywords for home page (Portuguese - auto-translated)'],
            ['key' => 'seo_tours_keywords_pt', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO keywords for tours page (Portuguese - auto-translated)'],
            ['key' => 'seo_contact_keywords_pt', 'value' => '', 'type' => 'text', 'category' => 'seo', 'description' => 'SEO keywords for contact page (Portuguese - auto-translated)'],
        ];

        foreach ($keywords as $setting) {
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
        $keys = [
            'seo_home_keywords_es', 'seo_tours_keywords_es', 'seo_contact_keywords_es',
            'seo_home_keywords_en', 'seo_tours_keywords_en', 'seo_contact_keywords_en',
            'seo_home_keywords_fr', 'seo_tours_keywords_fr', 'seo_contact_keywords_fr',
            'seo_home_keywords_de', 'seo_tours_keywords_de', 'seo_contact_keywords_de',
            'seo_home_keywords_pt', 'seo_tours_keywords_pt', 'seo_contact_keywords_pt',
        ];

        DB::table('branding_settings')->whereIn('key', $keys)->delete();
    }
};
