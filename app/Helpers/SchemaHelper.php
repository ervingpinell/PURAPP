<?php

namespace App\Helpers;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class SchemaHelper
{
    /**
     * Generate Product schema for SEO
     */
    public static function generateProductSchema(Product $product, $reviews = null): array
    {
        $locale = app()->getLocale();
        $productName = $product->getTranslatedName();
        $overview = $product->getTranslatedOverview();

        // Get active prices
        $activeCategories = $product->activePricesForDate(now());

        // Calculate price range
        $prices = $activeCategories->pluck('price')->filter();
        $lowPrice = $prices->min() ?? 0;
        $highPrice = $prices->max() ?? 0;

        // Get images from gallery
        $images = self::getProductImages($product);

        // Build offers array
        $offers = [];
        foreach ($activeCategories as $priceRecord) {
            $category = $priceRecord->category;
            $categoryName = method_exists($category, 'getTranslatedName')
                ? $category->getTranslatedName($locale)
                : ($category->display_name ?? $category->name ?? 'N/A');

            $offers[] = [
                '@type' => 'Offer',
                'name' => $categoryName,
                'price' => (string) $priceRecord->price,
                'priceCurrency' => 'USD',
                'availability' => 'https://schema.org/InStock',
                'validFrom' => now()->toIso8601String(),
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $productName,
            'description' => strip_tags($overview ?? ''),
        ];

        // Add images if available
        if (!empty($images)) {
            $schema['image'] = $images;
        }

        // Add AggregateRating if reviews exist
        if ($reviews && $reviews->isNotEmpty()) {
            $ratingValue = $reviews->avg('rating');
            $reviewCount = $reviews->count();

            if ($ratingValue && $reviewCount) {
                $schema['aggregateRating'] = [
                    '@type' => 'AggregateRating',
                    'ratingValue' => number_format($ratingValue, 1),
                    'reviewCount' => $reviewCount,
                    'bestRating' => '5',
                    'worstRating' => '1'
                ];
            }
        }

        // Add offers
        if (count($offers) > 0) {
            if (count($offers) === 1) {
                $schema['offers'] = $offers[0];
            } else {
                $schema['offers'] = [
                    '@type' => 'AggregateOffer',
                    'priceCurrency' => 'USD',
                    'lowPrice' => (string) $lowPrice,
                    'highPrice' => (string) $highPrice,
                    'offerCount' => (string) count($offers),
                    'offers' => $offers,
                ];
            }
        }

        // Product specific: Brand (instead of provider)
        $schema['brand'] = [
            '@type' => 'Brand',
            'name' => config('company.name'),
        ];

        return $schema;
    }

    /**
     * Get product images from gallery
     */
    protected static function getProductImages(Product $product): array
    {
        $images = [];

        // Try to get from gallery folder
        $productId = $product->product_id ?? $product->id;
        $folder = "products/{$productId}/gallery";

        if (Storage::disk('public')->exists($folder)) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $files = collect(Storage::disk('public')->files($folder))
                ->filter(fn($p) => in_array(strtolower(pathinfo($p, PATHINFO_EXTENSION)), $allowed, true))
                ->sort(fn($a, $b) => strnatcasecmp($a, $b))
                ->take(5) // Max 5 images for schema
                ->map(fn($file) => asset('storage/' . $file))
                ->values()
                ->toArray();

            $images = array_merge($images, $files);
        }

        // Fallback to cover image if no gallery
        if (empty($images) && $product->coverImage) {
            $images[] = $product->coverImage->url;
        }

        return $images;
    }

    /**
     * Generate BreadcrumbList schema
     */
    public static function generateBreadcrumbSchema(array $items): array
    {
        $listItems = [];

        foreach ($items as $index => $item) {
            $listItem = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
            ];

            // Add item URL if not the last item
            if (isset($item['url'])) {
                $listItem['item'] = $item['url'];
            }

            $listItems[] = $listItem;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ];
    }
}
