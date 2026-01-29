<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/**
 * SitemapController
 *
 * Generates XML sitemaps.
 */
class SitemapController extends Controller
{
    public function sitemap(): Response
    {
        $base = rtrim(config('app.url'), '/');
        $locales = array_keys(config('routes.locales', ['es' => []]));

        $urls = [];

        // Páginas básicas por locale
        foreach ($locales as $loc) {
            $urls[] = $this->url("{$base}/{$loc}",            now(), 'daily',  '1.0');
            $urls[] = $this->url("{$base}/{$loc}/tours",      now(), 'daily',  '0.9');
            $urls[] = $this->url("{$base}/{$loc}/faq",        now(), 'weekly', '0.6');
            $urls[] = $this->url("{$base}/{$loc}/contact",    now(), 'weekly', '0.6');
            $urls[] = $this->url("{$base}/{$loc}/policies",   now(), 'monthly','0.4');
            $urls[] = $this->url("{$base}/{$loc}/reviews",    now(), 'daily',  '0.7');
        }

        // Products
        if (class_exists(\App\Models\Product::class)) {
            $products = \App\Models\Product::query()
                ->whereNull('deleted_at')
                ->get(['slug','updated_at']);

            foreach ($products as $product) {
                foreach ($locales as $loc) {
                    $urls[] = $this->url("{$base}/{$loc}/tours/{$product->slug}",
                        $product->updated_at ?? now(), 'daily', '0.85');
                }
            }
        }

        // Policies
        if (class_exists(\App\Models\Policy::class)) {
            $policies = \App\Models\Policy::query()
                ->whereNull('deleted_at')
                ->get(['slug','updated_at']);

            foreach ($policies as $policy) {
                foreach ($locales as $loc) {
                    $urls[] = $this->url("{$base}/{$loc}/policies/{$policy->slug}",
                        $policy->updated_at ?? now(), 'monthly', '0.5');
                }
            }
        }

        // Construcción XML
        $xml = $this->buildXml($urls);

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function url(string $loc, $lastmod, string $changefreq, string $priority): array
    {
        return [
            'loc'        => $loc,
            'lastmod'    => optional($lastmod)->format('c') ?? now()->format('c'),
            'changefreq' => $changefreq,
            'priority'   => $priority,
        ];
    }

    private function buildXml(array $urls): string
    {
        $items = array_map(function ($u) {
            return
                '  <url>' . "\n" .
                '    <loc>' . e($u['loc']) . '</loc>' . "\n" .
                '    <lastmod>' . e($u['lastmod']) . '</lastmod>' . "\n" .
                '    <changefreq>' . e($u['changefreq']) . '</changefreq>' . "\n" .
                '    <priority>' . e($u['priority']) . '</priority>' . "\n" .
                '  </url>';
        }, $urls);

        return
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n" .
            implode("\n", $items) . "\n" .
            '</urlset>';
    }
}
