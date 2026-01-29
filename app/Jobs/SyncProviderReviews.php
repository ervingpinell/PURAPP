<?php

namespace App\Jobs;

use App\Models\Review;
use App\Models\ReviewProvider;
use App\Services\Reviews\ReviewAggregator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncProviderReviews implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public ?string $providerSlug = null, public int $limit = 200) {}

    public function handle(ReviewAggregator $agg): void
    {
        $providers = ReviewProvider::query()
            ->when($this->providerSlug, fn($q, $p) => $q->where('slug', $p))
            ->where('is_active', true)
            ->where('slug', '!=', ReviewProvider::LOCAL_SLUG) // <-- evita el local
            ->get();
        foreach ($providers as $prov) {
            $rows = $agg->aggregate([
                'provider' => $prov->slug,
                'limit'    => $this->limit,
            ]);

            // Reverse map (para viator) si viene product_code sin product_id
            $productMap = (array) $prov->getSetting('product_map', []);
            $revMap = array_flip($productMap); // product_code => product_id

            foreach ($rows as $r) {
                $productId = $r['product_id'] ?? null;

                if (!$productId && !empty($r['product_code'] ?? null) && isset($revMap[$r['product_code']])) {
                    $productId = (int) $revMap[$r['product_code']];
                }

                Review::updateOrCreate(
                    [
                        'provider'           => $prov->slug,
                        'provider_review_id' => $r['provider_review_id'] ?? null,
                    ],
                    [
                        'product_id'      => $productId ?: 0,
                        'rating'       => (int)($r['rating'] ?? 0),
                        'title'        => $r['title'] ?? null,
                        'body'         => $r['body'] ?? '',
                        'language'     => $r['language'] ?? 'es',
                        'author_name'  => $r['author_name'] ?? null,
                        'is_verified'  => false,
                        'is_public'    => (bool)($prov->getSetting('auto_publish', true)),
                        'status'       => $prov->getSetting('auto_publish', true) ? 'published' : 'pending',
                        'source_url'   => null,
                    ]
                );
            }
        }
    }
}
