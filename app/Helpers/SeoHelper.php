<?php

use App\Models\BrandingSetting;

if (!function_exists('seo_meta')) {
    /**
     * Get SEO meta tag content for a specific page and field
     *
     * @param string $page Page identifier (home, tours, contact)
     * @param string $field Field type (title, description)
     * @return string SEO content or fallback
     */
    function seo_meta(string $page, string $field = 'title'): string
    {
        $locale = app()->getLocale();
        
        // Map locale to supported languages
        $supportedLocales = ['es', 'en', 'fr', 'de', 'pt'];
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'es'; // Default to Spanish
        }
        
        // Build the branding key
        $key = "seo_{$page}_{$field}_{$locale}";
        
        // Get from branding with fallback to translation file
        $fallbackKey = "adminlte::adminlte.meta.{$page}_{$field}";
        $fallback = __($fallbackKey);
        
        // If translation key wasn't found, use empty string
        if ($fallback === $fallbackKey) {
            $fallback = '';
        }
        
        return branding($key, $fallback);
    }
}
