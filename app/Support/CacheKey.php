<?php

namespace App\Support;

use Illuminate\Support\Str;

final class CacheKey
{
    /**
     * Genera una cache key estable: "<prefix>:v<version>:k1_v|k2_v..."
     */
    public static function make(string $prefix, array $parts = [], int $version = 1): string
    {
        $flat = collect($parts)->map(
            fn($v, $k) => $k.'='.Str::slug((string)$v, '_')
        )->implode('|');

        return "{$prefix}:v{$version}:{$flat}";
    }
}
