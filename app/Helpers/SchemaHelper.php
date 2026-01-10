<?php

namespace App\Helpers;

use App\Models\Tour;
use Illuminate\Support\Facades\Storage;

class SchemaHelper
{
    /**
     * Generate TouristAttraction schema for a tour
     */
    public static function generateTourSchema(Tour $tour, $reviews = null): array
    {
        $locale = app()->getLocale();
        $tourName = $tour->getTranslatedName();
        $overview = $tour->getTranslatedOverview();

        // Get active prices
        $activeCategories = $tour->activePricesForDate(now());

        // Calculate price range
        $prices = $activeCategories->pluck('price')->filter();
        $lowPrice = $prices->min() ?? 0;
        $highPrice = $prices->max() ?? 0;

        // Get images from gallery
        $images = self::getTourImages($tour);

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
            '@type' => 'TouristAttraction',
            'name' => $tourName,
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

        // Add duration if available
        if ($tour->length) {
            $schema['duration'] = 'PT' . $tour->length . 'H';
        }

        // Add geo coordinates (La Fortuna, Arenal area)
        $schema['geo'] = [
            '@type' => 'GeoCoordinates',
            'latitude' => '10.463',
            'longitude' => '-84.703',
        ];

        // Add address
        $schema['address'] = [
            '@type' => 'PostalAddress',
            'addressLocality' => 'La Fortuna',
            'addressRegion' => 'Alajuela',
            'addressCountry' => 'CR',
        ];

        // Add provider
        $schema['provider'] = [
            '@type' => 'TravelAgency',
            'name' => config('company.name'),
            'url' => url('/'),
        ];

        return $schema;
    }

    /**
     * Get tour images from gallery
     */
    protected static function getTourImages(Tour $tour): array
    {
        $images = [];

        // Try to get from gallery folder
        $tourId = $tour->tour_id ?? $tour->id;
        $folder = "tours/{$tourId}/gallery";

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
        if (empty($images) && $tour->coverImage) {
            $images[] = $tour->coverImage->url;
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
