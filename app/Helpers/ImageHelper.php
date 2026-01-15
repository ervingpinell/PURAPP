<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Get optimized image attributes for responsive images
     *
     * @param string $path Image path
     * @param array $sizes Sizes array ['sm' => 480, 'md' => 800, 'lg' => 1200]
     * @param string $alt Alt text
     * @return array Attributes for img tag
     */
    public static function responsive(string $path, array $sizes = [], string $alt = ''): array
    {
        $defaultSizes = [
            'sm' => 480,
            'md' => 800,
            'lg' => 1200,
        ];

        $sizes = array_merge($defaultSizes, $sizes);

        // Get image dimensions if local file
        $dimensions = self::getDimensions($path);

        $srcset = [];
        foreach ($sizes as $key => $width) {
            $srcset[] = "{$path} {$width}w";
        }

        return [
            'src' => $path,
            'srcset' => implode(', ', $srcset),
            'sizes' => '(max-width: 600px) 480px, (max-width: 900px) 800px, 1200px',
            'width' => $dimensions['width'] ?? null,
            'height' => $dimensions['height'] ?? null,
            'alt' => $alt,
            'loading' => 'lazy',
        ];
    }

    /**
     * Get image dimensions from path
     *
     * @param string $path
     * @return array|null
     */
    public static function getDimensions(string $path): ?array
    {
        // Remove asset() wrapper if present
        $cleanPath = str_replace([asset(''), url('')], '', $path);
        $cleanPath = ltrim($cleanPath, '/');

        $fullPath = public_path($cleanPath);

        if (!file_exists($fullPath) || !is_file($fullPath)) {
            return null;
        }

        $info = @getimagesize($fullPath);

        if ($info === false) {
            return null;
        }

        return [
            'width' => $info[0],
            'height' => $info[1],
            'type' => $info[2],
            'mime' => $info['mime'] ?? null,
        ];
    }

    /**
     * Generate img tag with optimal attributes
     *
     * @param string $src
     * @param string $alt
     * @param array $options
     * @return string
     */
    public static function tag(string $src, string $alt = '', array $options = []): string
    {
        $dimensions = self::getDimensions($src);

        $attributes = array_merge([
            'src' => $src,
            'alt' => $alt,
            'loading' => 'lazy',
            'width' => $dimensions['width'] ?? null,
            'height' => $dimensions['height'] ?? null,
        ], $options);

        // Remove null values
        $attributes = array_filter($attributes, fn($v) => $v !== null);

        $html = '<img';
        foreach ($attributes as $key => $value) {
            $html .= sprintf(' %s="%s"', $key, htmlspecialchars($value, ENT_QUOTES));
        }
        $html .= '>';

        return $html;
    }

    /**
     * Check if image should be converted to WebP
     *
     * @param string $path
     * @return bool
     */
    public static function shouldConvertToWebP(string $path): bool
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png']);
    }

    /**
     * Get WebP version of image path
     *
     * @param string $path
     * @return string
     */
    public static function getWebPPath(string $path): string
    {
        return preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
    }
}
