<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('setting')) {
    /**
     * Get a setting value from database with caching
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            return $setting?->value ?? $default;
        });
    }
}

if (!function_exists('setting_update')) {
    /**
     * Update a setting value and clear cache
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    function setting_update(string $key, $value): bool
    {
        $updated = Setting::where('key', $key)->update([
            'value' => $value,
            'updated_by' => auth()->id(),
        ]);

        Cache::forget("setting.{$key}");

        return (bool) $updated;
    }
}

if (!function_exists('settings_by_category')) {
    /**
     * Get all settings grouped by category
     *
     * @param string|null $category
     * @return \Illuminate\Support\Collection
     */
    function settings_by_category(?string $category = null)
    {
        $query = Setting::orderBy('category')->orderBy('sort_order');

        if ($category) {
            $query->where('category', $category);
        }

        return $query->get()->groupBy('category');
    }
}
