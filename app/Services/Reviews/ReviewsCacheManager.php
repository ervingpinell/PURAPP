<?php

namespace App\Services\Reviews;

use Illuminate\Support\Facades\Cache;

class ReviewsCacheManager
{
    private const PREFIX = 'reviews:';
    private const REV_KEY = 'reviews.rev';

    /**
     * Invalida TODO el caché de reviews
     */
    public function flush(): void
    {
        $this->bumpRevision();

        // Si usas tags (Redis/Memcached)
        if (method_exists(Cache::getStore(), 'tags')) {
            Cache::tags(['reviews'])->flush();
        }
    }

    /**
     * Invalida caché de un tour específico
     */
    public function flushTour(int $tourId): void
    {
        $this->bumpRevision("tour.{$tourId}");
    }

    /**
     * Invalida caché de un proveedor
     */
    public function flushProvider(string $provider): void
    {
        $this->bumpRevision("provider.{$provider}");
    }

    /**
     * Obtiene revisión actual (para cache keys)
     */
    public function getRevision(?string $scope = null): int
    {
        $key = $scope ? self::REV_KEY . '.' . $scope : self::REV_KEY;
        return (int) Cache::get($key, 1);
    }

    private function bumpRevision(?string $scope = null): void
    {
        $key = $scope ? self::REV_KEY . '.' . $scope : self::REV_KEY;

        try {
            Cache::increment($key);
        } catch (\Throwable $e) {
            $val = (int) Cache::get($key, 0) + 1;
            Cache::put($key, $val, now()->addYears(5));
        }
    }
}
