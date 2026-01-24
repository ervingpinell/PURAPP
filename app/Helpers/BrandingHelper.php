<?php

use App\Models\BrandingSetting;

if (!function_exists('branding')) {
    /**
     * Get a branding setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function branding(string $key, $default = null)
    {
        return BrandingSetting::get($key, $default);
    }
}
