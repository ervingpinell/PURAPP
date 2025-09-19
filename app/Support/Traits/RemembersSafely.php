<?php

namespace App\Support\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

trait RemembersSafely
{
    /**
     * Cache con lock para evitar thundering herd.
     */
    protected function rememberSafe(string $key, int $ttl, \Closure $loader, bool $force = false)
    {
        if (!$force) {
            $cached = Cache::get($key);
            if (!is_null($cached)) return $cached;
        }

        $lock = Cache::lock("lock:$key", 10);

        try {
            if ($lock->get()) {
                if (!$force) {
                    $cached = Cache::get($key);
                    if (!is_null($cached)) return $cached;
                }
                $val = $loader();
                Cache::put($key, $val, $ttl);
                return $val;
            }

            // Si otro proceso tiene el lock, devolvemos lo último o vacío
            $cached = Cache::get($key);
            if (!is_null($cached)) return $cached;
            usleep(200 * 1000);
            return Cache::get($key, collect());
        } catch (Throwable $e) {
            Log::warning('cache.loader_error', ['key' => $key, 'err' => $e->getMessage()]);
            return Cache::get($key, collect());
        } finally {
            optional($lock)->release();
        }
    }
}
