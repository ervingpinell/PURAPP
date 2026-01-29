<?php

namespace App\Services\Reviews;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Responsable ÚNICA de distribuir reviews entre tours respetando:
 * - No repetir reviews entre tours
 * - Distribuir entre proveedores disponibles
 * - Completar con otros proveedores si uno no tiene suficientes
 */
class ReviewDistributor
{
    public function __construct(
        private ReviewAggregator $aggregator
    ) {}

    /**
     * Para HOME: N reviews por producto, distribuidas entre proveedores
     */
    public function forHome(Collection $products, int $perProduct = 3, int $maxTotal = 24): Collection
    {
        $result = collect();
        $globalSeen = []; // Dedupe global

        foreach ($products->shuffle() as $product) {
            if ($result->count() >= $maxTotal) break;

            $productReviews = $this->distributedForProduct(
                $product->product_id,
                $perProduct,
                $globalSeen
            );

            $result = $result->merge($productReviews);
        }

        return $result->take($maxTotal);
    }

    /**
     * Para INDEX: ~5-6 reviews por tour
     */
    public function forIndex(Collection $products, int $perProduct = 5): Collection
    {
        $result = collect();
        $globalSeen = [];

        foreach ($products as $product) {
            $productReviews = $this->distributedForProduct(
                $product->product_id,
                $perProduct,
                $globalSeen
            );

            $result->push([
                'product' => $product,
                'reviews' => $productReviews
            ]);
        }

        return $result;
    }

    /**
     * Para TOUR específico: mínimo 12 reviews
     */
    public function forProductPage(int $productId, int $min = 12): Collection
    {
        $seen = [];

        // 1) Reviews del tour
        $own = $this->aggregator->aggregate([
            'product_id' => $productId,
            'limit' => $min * 2
        ])->filter(fn($r) => !$this->isDuplicate($r, $seen));

        $needed = $min - $own->count();

        // 2) Si faltan, traer de otros tours
        if ($needed > 0) {
            $others = $this->aggregator->aggregate([
                'limit' => $needed * 3
            ])->filter(fn($r) =>
                ($r['product_id'] ?? null) != $productId &&
                !$this->isDuplicate($r, $seen)
            )->take($needed);

            return $own->merge($others)->shuffle()->take($min);
        }

        return $own->take($min);
    }

    /**
     * Distribuye reviews para UN producto específico entre proveedores
     */
    private function distributedForProduct(int $productId, int $want, array &$globalSeen): Collection
    {
        $providers = $this->getActiveProviders();
        $result = collect();

        foreach ($providers as $provider) {
            if ($result->count() >= $want) break;

            $reviews = $this->aggregator->aggregate([
                'provider' => $provider,
                'product_id' => $productId,
                'limit' => 2 // Traer pocas por proveedor
            ]);

            foreach ($reviews as $r) {
                if ($this->isDuplicate($r, $globalSeen)) continue;

                $result->push($r);
                if ($result->count() >= $want) break 2;
            }
        }

        return $result;
    }

    private function isDuplicate(array $review, array &$seen): bool
    {
        $key = $this->makeKey($review);

        if (isset($seen[$key])) return true;

        $seen[$key] = true;
        return false;
    }

    private function makeKey(array $r): string
    {
        $provider = strtolower($r['provider'] ?? 'p');

        // Si tiene ID único del proveedor, usarlo
        if (!empty($r['provider_review_id'])) {
            return $provider . '#' . $r['provider_review_id'];
        }

        // Sino, por contenido
        return $provider . '#' . md5(
            mb_strtolower(trim($r['body'] ?? '')) . '|' .
            mb_strtolower(trim($r['author_name'] ?? '')) . '|' .
            trim($r['date'] ?? '')
        );
    }

    private function getActiveProviders(): array
    {
        return DB::table('review_providers')
            ->where('is_active', true)
            ->orderBy('id')
            ->pluck('slug')
            ->all();
    }
}
