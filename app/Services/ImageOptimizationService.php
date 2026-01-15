<?php

namespace App\Services;

use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ImageOptimizationService
{
    /**
     * Maximum dimensions for images
     */
    private const MAX_WIDTH = 1920;
    private const MAX_HEIGHT = 1920;

    /**
     * JPEG quality (80-85 is optimal balance)
     */
    private const JPEG_QUALITY = 82;

    /**
     * WebP quality
     */
    private const WEBP_QUALITY = 85;

    /**
     * Optimize and save an uploaded image
     *
     * @param UploadedFile $file
     * @param string $path Storage path (e.g., 'types/1')
     * @param string|null $filename Custom filename (optional)
     * @return array ['original' => 'path/to/image.jpg', 'webp' => 'path/to/image.webp']
     */
    public function optimizeAndSave(UploadedFile $file, string $path, ?string $filename = null): array
    {
        // Generate filename if not provided
        if (!$filename) {
            $filename = 'cover-' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
        }

        // Load and optimize image
        $image = Image::read($file->getRealPath());

        // Resize if too large (maintain aspect ratio)
        if ($image->width() > self::MAX_WIDTH || $image->height() > self::MAX_HEIGHT) {
            $image->scale(
                width: $image->width() > self::MAX_WIDTH ? self::MAX_WIDTH : null,
                height: $image->height() > self::MAX_HEIGHT ? self::MAX_HEIGHT : null
            );
        }

        // Save optimized original format
        $originalPath = $path . '/' . $filename;
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['jpg', 'jpeg'])) {
            $encoded = $image->toJpeg(quality: self::JPEG_QUALITY);
        } elseif ($extension === 'png') {
            $encoded = $image->toPng();
        } else {
            // Fallback to original
            $encoded = $image->encode();
        }

        Storage::disk('public')->put($originalPath, $encoded);

        // Generate WebP version for modern browsers
        $webpFilename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
        $webpPath = $path . '/' . $webpFilename;
        $webpEncoded = $image->toWebp(quality: self::WEBP_QUALITY);
        Storage::disk('public')->put($webpPath, $webpEncoded);

        return [
            'original' => $originalPath,
            'webp' => $webpPath,
        ];
    }

    /**
     * Delete image and its WebP version
     *
     * @param string $path Path to original image
     * @return void
     */
    public function delete(string $path): void
    {
        // Delete original
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        // Delete WebP version
        $webpPath = pathinfo($path, PATHINFO_DIRNAME) . '/' .
            pathinfo($path, PATHINFO_FILENAME) . '.webp';

        if (Storage::disk('public')->exists($webpPath)) {
            Storage::disk('public')->delete($webpPath);
        }
    }

    /**
     * Get WebP path for an image
     *
     * @param string $originalPath
     * @return string|null
     */
    public function getWebpPath(string $originalPath): ?string
    {
        $webpPath = pathinfo($originalPath, PATHINFO_DIRNAME) . '/' .
            pathinfo($originalPath, PATHINFO_FILENAME) . '.webp';

        return Storage::disk('public')->exists($webpPath) ? $webpPath : null;
    }
}
