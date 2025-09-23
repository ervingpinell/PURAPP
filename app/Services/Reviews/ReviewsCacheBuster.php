<?php

namespace App\Services\Reviews;

use App\Models\Review;
use App\Support\CacheKey;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ReviewsCacheBuster
{
    /**
     * Invalida caches relacionadas a reviews:
     * - Aggregator (local)
     * - Home reviews
     * - Reviews por tour
     */
    public function bust(?Review $review = null): void
    {
        try {
            // 1) Si tienes un agregador con API de flush, úsalo
            if (class_exists(\App\Services\Reviews\ReviewAggregator::class)) {
                $agg = app(\App\Services\Reviews\ReviewAggregator::class);

                foreach (['flush', 'flushCache', 'clear', 'clearCache'] as $m) {
                    if (method_exists($agg, $m)) { $agg->{$m}('local'); break; }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('reviews.cachebust.aggregator_fail', ['err' => $e->getMessage()]);
        }

        // 2) Home (varía por parámetros; limpiamos las combinaciones típicas)
        try {
            $combos = [
                ['loc'=>'all','target'=>24,'per'=>2],
                ['loc'=>'all','target'=>24,'per'=>3],
            ];
            foreach ($combos as $c) {
                $k = CacheKey::make('home_reviews2', $c, 2); Cache::forget($k);
                $k = CacheKey::make('home_reviews2', $c, 1); Cache::forget($k);
            }
        } catch (\Throwable $e) {
            Log::warning('reviews.cachebust.home_fail', ['err' => $e->getMessage()]);
        }

        // 3) Página de tour (clave incluye tour_id + min)
        if ($review && $review->tour_id) {
            foreach ([4,5] as $min) {
                foreach ([1,2] as $ver) {
                    $k = CacheKey::make('tour_reviews', [
                        'tour' => (int)$review->tour_id,
                        'loc'  => 'all',
                        'lim'  => 500,
                        'min'  => $min,
                    ], $ver);
                    Cache::forget($k);
                }
            }
        }

        // 4) Si usas un driver con TAGS (Redis/Memcached), podrías:
        // if (method_exists(Cache::getStore(), 'tags')) {
        //     Cache::tags(['reviews'])->flush();
        // }
    }
}
