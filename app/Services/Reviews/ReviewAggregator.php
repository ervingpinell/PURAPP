<?php

namespace App\Services\Reviews;

use App\Services\Reviews\Drivers\Contracts\ReviewSource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Agregador neutral: delega el fetch a los drivers y normaliza.
 * NO filtra por rating/idioma (eso lo hacen los drivers según settings).
 */
class ReviewAggregator
{
    /** @var array<string, ReviewSource> */
    protected array $sources;

    public function __construct(iterable $sources = [])
    {
        $this->sources = is_array($sources) ? $sources : iterator_to_array($sources);
    }

    /**
     * @param array{
     *   provider?: string|null,
     *   tour_id?: int|string|null,
     *   limit?: int
     * } $opts
     */
    public function aggregate(array $opts = []): Collection
    {
        $want     = max(1, (int)($opts['limit'] ?? 50));
        $provider = $opts['provider'] ?? null;
        $tourId   = $opts['tour_id'] ?? null;

        $enabled = $this->enabledProviders($provider);
        $rows    = collect();

        foreach ($enabled as $slug => $driver) {
            $fetched = $driver->fetch($opts) ?? [];
            if (!is_array($fetched)) $fetched = [];

            foreach ($fetched as $r) {
                $rows->push($this->normalizeShape($slug, $r));
            }
        }

        // 🟢 Filtro Global: Rating Mínimo (ej: 4 o más)
        // Ahora usamos configuración por proveedor (settings['min_stars'])
        $minRatings = $this->getMinRatingMap();
        $globalMin  = (int) config('reviews.min_public_rating', 4);

        $rows = $rows->filter(function ($r) use ($minRatings, $globalMin) {
            $provider = strtolower($r['provider'] ?? '');

            // Si el proveedor tiene configuración específica, usarla
            if (array_key_exists($provider, $minRatings)) {
                return $r['rating'] >= $minRatings[$provider];
            }

            // Fallback: usar default global
            return $r['rating'] >= $globalMin;
        });

        // Deduplicación única por clave
        $rows = $rows->unique(fn($r) => $this->makeUniqueKey($r));

        // Adjuntar nombres de tour a todas las reviews
        $rows = $this->attachTourNames($rows);

        // CRÍTICO: Si se pidió un tour_id específico, filtrar ANTES de shuffle
        if ($tourId !== null) {
            $rows = $rows->filter(fn($r) => (int)($r['tour_id'] ?? 0) === (int)$tourId);
        }

        // Shuffle y limitar (NO hacer shuffle si ya viene filtrado por tour)
        return $rows->take($want)->values();
    }

    /** @return array<string, ReviewSource> */
    protected function enabledProviders(?string $only = null): array
    {
        if ($only) {
            $only = strtolower($only);
            return array_key_exists($only, $this->sources)
                ? [$only => $this->sources[$only]]
                : [];
        }
        return $this->sources;
    }

    /**
     * Normaliza campos esperados por blades/JS.
     * Marca `indexable` en base a config.
     */
    protected function normalizeShape(string $provider, array $r): array
    {
        $provider = strtolower($provider);

        $rating = (int) max(0, min(5, (int)($r['rating'] ?? 0)));
        $title  = trim((string) ($r['title'] ?? ''));
        $body   = trim((string) ($r['body'] ?? ''));
        $author = trim((string) ($r['author_name'] ?? ''));
        $date   = $r['date'] ?? null;
        $tourId = $r['tour_id'] ?? null;

        $indexableProviders = config('reviews.indexable_providers', ['local']);
        $indexable = in_array($provider, $indexableProviders, true);

        // Si tiene product_code pero no tour_id, intentar mapear desde settings
        $productCode = $r['product_code'] ?? null;
        if (!$tourId && $productCode) {
            $tourId = $this->mapProductCodeToTour($productCode);
        }

        return [
            'provider'           => $provider,
            'indexable'          => $indexable,
            'provider_review_id' => $r['provider_review_id'] ?? null,
            'rating'             => $rating,
            'title'              => $title !== '' ? $title : null,
            'body'               => $body,
            'author_name'        => $author !== '' ? $author : null,
            'date'               => $date ?: null,
            'tour_id'            => $tourId,
            'product_code'       => $productCode,
            'tour_name'          => $r['tour_name'] ?? null,
            'avatar_url'         => $r['avatar_url'] ?? null,
            'language'           => $r['language'] ?? null,
        ];
    }

    /**
     * Genera clave única para deduplicación
     */
    private function makeUniqueKey(array $r): string
    {
        $provider = strtolower($r['provider'] ?? 'p');

        // Si tiene ID único del proveedor
        if (!empty($r['provider_review_id'])) {
            return $provider . '#' . $r['provider_review_id'];
        }

        // Sino, hash por contenido
        return $provider . '#' . md5(
            mb_strtolower(trim($r['body'] ?? '')) . '|' .
                mb_strtolower(trim($r['author_name'] ?? '')) . '|' .
                trim($r['date'] ?? '')
        );
    }

    /**
     * Mapea product_code a tour_id usando los product_map de settings de proveedores
     */
    private function mapProductCodeToTour(?string $productCode): ?int
    {
        if (!$productCode) return null;

        static $cache = null;

        // Construir mapa inverso una sola vez (product_code => tour_id)
        if ($cache === null) {
            $cache = [];

            $providers = DB::table('review_providers')
                ->where('is_active', true)
                ->get(['slug', 'settings']);

            foreach ($providers as $prov) {
                $settings = is_string($prov->settings)
                    ? json_decode($prov->settings, true)
                    : (array) $prov->settings;

                $productMap = (array) ($settings['product_map'] ?? []);

                // Invertir: product_code => tour_id
                foreach ($productMap as $tourId => $code) {
                    if (is_string($code) && trim($code) !== '') {
                        $cache[strtolower(trim($code))] = (int) $tourId;
                    }
                }
            }
        }

        $key = strtolower(trim($productCode));
        return $cache[$key] ?? null;
    }

    /**
     * Adjunta tour_name a reviews que tienen tour_id pero no tour_name
     */
    private function attachTourNames(Collection $reviews): Collection
    {
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'es');

        // IDs únicos que necesitan nombres
        $needNames = $reviews->filter(
            fn($r) =>
            !empty($r['tour_id']) && empty($r['tour_name'])
        )->pluck('tour_id')->unique()->values();

        if ($needNames->isEmpty()) {
            return $reviews;
        }

        // Cargar tours con traducciones
        $tours = DB::table('tours')
            ->select('tour_id', 'name')
            ->whereIn('tour_id', $needNames->all())
            ->get()
            ->keyBy('tour_id');

        $translations = DB::table('tour_translations')
            ->select('tour_id', 'locale', 'name')
            ->whereIn('tour_id', $needNames->all())
            ->whereIn('locale', [$locale, $fallback])
            ->get()
            ->groupBy('tour_id');

        // Mapear nombres
        $tourNames = [];
        foreach ($tours as $tourId => $tour) {
            $trans = $translations->get($tourId, collect());
            $localized = $trans->firstWhere('locale', $locale);
            $fallbackTrans = $trans->firstWhere('locale', $fallback);

            $tourNames[(int)$tourId] = $localized->name ?? $fallbackTrans->name ?? $tour->name ?? '';
        }

        // Adjuntar nombres
        return $reviews->map(function ($r) use ($tourNames) {
            if (!empty($r['tour_id']) && empty($r['tour_name'])) {
                $r['tour_name'] = $tourNames[(int)$r['tour_id']] ?? '';
            }
            return $r;
        });
    }
    private function getMinRatingMap(): array
    {
        // Cachear resultado (ReviewProviderController hace flush)
        return \Illuminate\Support\Facades\Cache::remember('review_providers_min_stars', 3600, function () {
            return DB::table('review_providers')
                ->get(['slug', 'settings'])
                ->mapWithKeys(function ($p) {
                    $s = is_string($p->settings) ? json_decode($p->settings, true) : (array) $p->settings;
                    // Default 0 (todas) si no está definido en settings
                    return [$p->slug => (int) ($s['min_stars'] ?? 0)];
                })
                ->all();
        });
    }
}
