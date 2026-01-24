<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BrandingSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'category',
        'description',
    ];

    /**
     * Get a branding setting value by key with caching
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("branding.{$key}", function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }

            // Convert based on type
            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set a branding setting value
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function set(string $key, $value): bool
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        // Clear cache for this key
        Cache::forget("branding.{$key}");
        Cache::forget('branding.css_variables');

        return $setting->wasRecentlyCreated || $setting->wasChanged();
    }

    /**
     * Get all settings by category
     *
     * @param string $category
     * @return \Illuminate\Support\Collection
     */
    public static function getAllByCategory(string $category)
    {
        return self::where('category', $category)->get()->mapWithKeys(function ($setting) {
            return [$setting->key => self::castValue($setting->value, $setting->type)];
        });
    }

    /**
     * Get CSS variables for dynamic injection
     *
     * @return string
     */
    public static function getCssVariables(): string
    {
        return Cache::rememberForever('branding.css_variables', function () {
            $colors = self::where('category', 'colors')->get();
            
            $css = ":root {\n";
            
            foreach ($colors as $setting) {
                // Convert snake_case to kebab-case for CSS variables
                $cssVar = '--' . str_replace('_', '-', $setting->key);
                $css .= "  {$cssVar}: {$setting->value};\n";
            }
            
            $css .= "}\n";
            
            return $css;
        });
    }

    /**
     * Clear all branding caches
     *
     * @return void
     */
    public static function clearCache(): void
    {
        $settings = self::all();
        
        foreach ($settings as $setting) {
            Cache::forget("branding.{$setting->key}");
        }
        
        Cache::forget('branding.css_variables');
    }

    /**
     * Cast value based on type
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected static function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($value) ? (float) $value : $value,
            'color', 'text', 'file' => (string) $value,
            default => $value,
        };
    }
}
