<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class OptimizeImages extends Command
{
    protected $signature = 'images:optimize
                            {--path= : Specific path to optimize}
                            {--dry-run : Show what would be done without making changes}
                            {--webp : Convert images to WebP format}';

    protected $description = 'Optimize images by adding dimensions and optionally converting to WebP';

    public function handle()
    {
        $path = $this->option('path') ?: 'public/storage/products';
        $dryRun = $this->option('dry-run');
        $convertWebP = $this->option('webp');

        $this->info("Scanning images in: {$path}");

        $images = $this->getImages($path);
        $this->info("Found {$images->count()} images");

        $bar = $this->output->createProgressBar($images->count());
        $bar->start();

        $stats = [
            'processed' => 0,
            'skipped' => 0,
            'converted' => 0,
            'errors' => 0,
        ];

        foreach ($images as $image) {
            try {
                $dimensions = @getimagesize($image);

                if ($dimensions === false) {
                    $stats['skipped']++;
                    $bar->advance();
                    continue;
                }

                $this->line("\n📸 {$image}");
                $this->line("   Dimensions: {$dimensions[0]}x{$dimensions[1]}");

                if ($convertWebP && !$dryRun) {
                    $webpPath = $this->convertToWebP($image);
                    if ($webpPath) {
                        $stats['converted']++;
                        $this->line("   ✅ Converted to WebP: {$webpPath}");
                    }
                }

                $stats['processed']++;
            } catch (\Exception $e) {
                $stats['errors']++;
                $this->error("   ❌ Error: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);
        $this->info('=== Summary ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Processed', $stats['processed']],
                ['Skipped', $stats['skipped']],
                ['Converted to WebP', $stats['converted']],
                ['Errors', $stats['errors']],
            ]
        );

        if ($dryRun) {
            $this->warn('This was a dry run. No changes were made.');
        }

        return 0;
    }

    private function getImages(string $path): \Illuminate\Support\Collection
    {
        $fullPath = base_path($path);

        if (!File::exists($fullPath)) {
            $this->error("Path does not exist: {$fullPath}");
            return collect();
        }

        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $images = collect();

        foreach (File::allFiles($fullPath) as $file) {
            if (in_array(strtolower($file->getExtension()), $extensions)) {
                $images->push($file->getPathname());
            }
        }

        return $images;
    }

    private function convertToWebP(string $imagePath): ?string
    {
        $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

        if ($ext === 'webp') {
            return null; // Already WebP
        }

        $webpPath = preg_replace('/\.(jpe?g|png|gif)$/i', '.webp', $imagePath);

        if (File::exists($webpPath)) {
            return null; // WebP version already exists
        }

        try {
            $image = null;

            switch ($ext) {
                case 'jpg':
                case 'jpeg':
                    $image = @imagecreatefromjpeg($imagePath);
                    break;
                case 'png':
                    $image = @imagecreatefrompng($imagePath);
                    break;
                case 'gif':
                    $image = @imagecreatefromgif($imagePath);
                    break;
            }

            if ($image === false || $image === null) {
                return null;
            }

            // Convert to WebP with 85% quality
            $success = imagewebp($image, $webpPath, 85);
            imagedestroy($image);

            return $success ? $webpPath : null;
        } catch (\Exception $e) {
            $this->error("Failed to convert {$imagePath}: {$e->getMessage()}");
            return null;
        }
    }
}
