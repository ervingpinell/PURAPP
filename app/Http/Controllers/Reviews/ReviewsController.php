<?php

namespace App\Http\Controllers\Reviews;

use Illuminate\Routing\Controller;
use App\Http\Requests\Reviews\StoreReviewRequest;
use App\Models\Review;
use App\Models\Tour;
use App\Services\Reviews\ReviewAggregator;
use App\Support\CacheKey;
use App\Support\Traits\RemembersSafely;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class ReviewsController extends Controller
{
    use RemembersSafely;

    /** TTL por defecto: 24h (segundos) */
    private int $defaultTtl = 60 * 60 * 24;

    public function __construct()
    {
        $this->middleware('web');
        $this->middleware('throttle:6,1')->only('store');
    }

    public function index(Request $request, ReviewAggregator $agg)
    {
        $locale   = app()->getLocale();
        $fallback = config('app.fallback_locale', 'es');
        $ttl      = $this->defaultTtl;
        $force    = (bool) $request->boolean('refresh', false);

        // REV global para invalidar cachés locales (lo incrementa ReviewObserver)
        $reviewsRev = \Illuminate\Support\Facades\Cache::get('reviews.rev', 1);

        // 1) Tours activos
        $q = Tour::with('translations')->where('is_active', true);
        if (Schema::hasColumn('tours', 'sort_order')) {
            $q->orderByRaw('sort_order IS NULL, sort_order ASC');
        }
        $q->orderBy('name');
        $tours = $q->get(['tour_id', 'name']);

        // 2) Nombre traducido solo para mostrar
        $tours = $tours->map(function ($t) use ($locale, $fallback) {
            $tr = ($t->translations ?? collect())->firstWhere('locale', $locale)
                ?: ($t->translations ?? collect())->firstWhere('locale', $fallback);
            $t->display_name = $tr->name ?? $t->name ?? '';
            return $t;
        });

        // 3) Parámetros
        $WANT        = (int) $request->integer('per_tour', 5);
        $LOCAL_POOL  = max($WANT, 12);
        $REMOTE_POOL = (int) $request->integer('pool_limit', 30);
        $MIN_STARS   = (int) $request->integer('min_rating', 3);

        // 4) Proveedores
        $providers   = $this->getActiveProviders(); // keyed por slug
        $remoteOrder = array_keys($providers->where('indexable', false)->all());
        if (empty($remoteOrder)) $remoteOrder = ['viator'];

        // 5) Traer locales SIN filtrar por idioma + construir slides sin repetir
        $tours = $tours->map(function ($t) use ($agg, $locale, $fallback, $LOCAL_POOL, $ttl, $force, $MIN_STARS, $reviewsRev, $REMOTE_POOL, $providers, $remoteOrder) {

            $cacheKey = CacheKey::make('reviews:idx:tour', [
                'tour'  => $t->tour_id,
                'limit' => $LOCAL_POOL,
                'min'   => $MIN_STARS,
                'rev'   => $reviewsRev,
            ], 3);

            /** @var \Illuminate\Support\Collection $items */
            $items = $this->rememberSafe($cacheKey, $ttl, fn () =>
                $agg->aggregate([
                    'tour_id'        => $t->tour_id,
                    'limit'          => $LOCAL_POOL,
                    'only_indexable' => true,
                    'min_rating'     => $MIN_STARS,
                ])->values()
            , $force);

            $items = $items->filter(fn ($r) => (int) ($r['rating'] ?? 0) >= $MIN_STARS)->values();
            $items = $this->attachTourNames($items, $locale, $fallback, $t->tour_id, $t->display_name);
            $t->indexable_reviews = $items;

            // ======================
            // Construcción de slides
            // ======================
            $locals     = collect($t->indexable_reviews ?? [])->take($LOCAL_POOL)->values();
            $localCount = $locals->count();
            $want       = (int) request('per_tour', 5);
            $need       = max(0, $want - $localCount);

            $slides = [];
            $li = 0;

            // Sembrar locales (hasta WANT)
            while ($li < $localCount && count($slides) < $want) {
                $slides[] = ['type' => 'local', 'data' => $locals[$li]];
                $li++;
            }

            // Si aún faltan, agregamos remotos SOLO si existen realmente (sin duplicar)
            if ($need > 0) {
                $order = $this->providerOrderForTour((int)$t->tour_id, $providers, $remoteOrder);

                foreach ($order as $prov) {
                    if ($need <= 0) break;

                    $available = $this->remoteAvailableCount(
                        $agg, $prov, (int)$t->tour_id, $REMOTE_POOL, $MIN_STARS, $ttl, (bool) request()->boolean('refresh', false)
                    );

                    if ($available <= 0) continue;

                    // toma hasta 'need' pero nunca más de lo disponible
                    $take = min($need, $available);
                    for ($n = 1; $n <= $take; $n++) {
                        $slides[] = [
                            'type'     => 'remote',
                            'provider' => $prov,
                            'nth'      => $n,
                            // pasamos el disponible real para evitar repeticiones en el embed
                            'limit'    => $available,
                            'tour_id'  => (int)$t->tour_id,
                        ];
                    }
                    $need -= $take;
                }
            }

            $t->slides       = collect($slides);
            $t->needs_iframe = $t->slides->contains(fn($s) => $s['type'] === 'remote');

            return $t;
        });

        // Dedupe global por contenido (solo locales; remotos ya se limitan por count real)
        $seen = [];
        $tours = $tours->map(function ($t) use (&$seen) {
            $t->slides = $t->slides->filter(function ($slide) use (&$seen) {
                if (($slide['type'] ?? '') !== 'local') return true; // solo dedupe locales
                $r = $slide['data'] ?? [];
                $key = strtolower((string)($r['provider'] ?? 'p')) . '#' . md5(
                    mb_strtolower(trim((string)($r['body'] ?? ''))) . '|' .
                    mb_strtolower(trim((string)($r['author_name'] ?? ''))) . '|' .
                    trim((string)($r['date'] ?? ''))
                );
                if (isset($seen[$key])) return false;
                $seen[$key] = true;
                return true;
            })->values();
            return $t;
        });

        return view('reviews.index', compact('tours'));
    }

    public function tour(int|string $tourId, ReviewAggregator $agg, Request $request)
    {
        $lang   = app()->getLocale();
        $ttl    = $this->defaultTtl;
        $force  = (bool) $request->boolean('refresh', false);

        $cacheKey = CacheKey::make('reviews:idx:tourpage', [
            'tour'  => $tourId,
            'loc'   => 'all',
            'limit' => 200,
        ], 1);

        /** @var \Illuminate\Support\Collection $locals */
        $locals = $this->rememberSafe($cacheKey, $ttl, fn () =>
            $agg->aggregate([
                'tour_id'        => $tourId,
                'limit'          => 200,
                'only_indexable' => true,
            ])->values()
        , $force);

        $locals = $this->attachTourNames($locals, $lang, config('app.fallback_locale', 'es'));

        $WANT  = 20;
        $need  = max(0, $WANT - $locals->count());
        $items = $locals->values();

        if ($need > 0) {
            $providers   = $this->getActiveProviders();
            $remoteOrder = array_keys($providers->where('indexable', false)->all());
            if (empty($remoteOrder)) $remoteOrder = ['viator'];

            $mainProv  = $this->pickProviderForTourByBinding((int)$tourId, $providers, $remoteOrder);
            $available = $this->remoteAvailableCount(
                $agg, $mainProv, (int)$tourId, (int) $request->integer('pool_limit', 30), (int) $request->integer('min_rating', 3),
                $ttl, $force
            );

            // usa SOLO el número real disponible (si 0, no agregues iframe)
            $take = min($need, $available);
            if ($take > 0) {
                $items->push([
                    'provider'     => $mainProv,
                    'indexable'    => false,
                    'iframe_limit' => $take,  // la vista debe iterar nth=1..$take
                    'tour_id'      => (int)$tourId,
                ]);
            }
        }

        return view('reviews.tour', compact('items', 'tourId'));
    }

    public function embed(Request $request, ReviewAggregator $agg, string $provider)
    {
        $lang     = app()->getLocale();
        $provider = strtolower(trim($provider)) ?: 'viator';

        // --- parámetros saneados
        $limit     = min(60, max(1, (int) $request->query('limit', 12)));
        $tourId    = $request->query('tour_id');
        $minRating = max(0, (int) $request->query('min_rating', 4)); // default 4★

        $ttlMin = (int) $request->query('ttl', 60 * 24);
        $ttl    = max(60, $ttlMin) * 60;
        $force  = (bool) $request->boolean('refresh', false);
        $nth    = max(1, (int) $request->query('nth', 1));

        $layout = (string) $request->query('layout', 'hero'); // hero|card
        $theme  = (string) $request->query('theme', $layout === 'card' ? 'site' : 'embed');
        $base   = (int) $request->query('base', $layout === 'card' ? 500 : 460);
        $uid    = (string) $request->query('uid', 'u' . substr(sha1(uniqid('', true)), 0, 10));

        // --- pool por provider (+tour si aplica)
        $cacheKey = CacheKey::make('reviews:iframe', [
            'p'    => $provider,
            'tour' => $tourId ?: 'all',
            'loc'  => 'all',
            'lim'  => $limit,
            'min'  => $minRating,
        ], 3);

        $loader = fn () =>
            $agg->aggregate([
                'provider'   => $provider,
                'limit'      => max(50, $limit * 5),
                'tour_id'    => $tourId,
                'language'   => $lang,
                'min_rating' => $minRating,
            ])->filter(fn ($r) => (int)($r['rating'] ?? 0) >= $minRating)->values();

        /** @var \Illuminate\Support\Collection $reviews */
        $reviews = $this->rememberSafe($cacheKey, $ttl, $loader, $force);

        // Fallbacks (mismo provider sin tour, luego cualquiera)
        if ($reviews->isEmpty() && !empty($tourId)) {
            $fallbackKey = CacheKey::make('reviews:iframe', [
                'p'    => $provider, 'tour' => 'all', 'loc' => 'all', 'lim' => $limit, 'min' => $minRating, 'fb' => 1,
            ], 3);

            $reviews = $this->rememberSafe($fallbackKey, $ttl, function () use ($agg, $provider, $limit, $lang, $minRating) {
                return $agg->aggregate([
                    'provider'   => $provider,
                    'limit'      => max(50, $limit * 5),
                    'language'   => $lang,
                    'min_rating' => $minRating,
                ])->filter(fn ($r) => (int)($r['rating'] ?? 0) >= $minRating)->values();
            }, $force)->map(function ($r) use ($tourId) { $r['tour_id'] = $tourId; return $r; });
        }

        if ($reviews->isEmpty()) {
            $anyKey = CacheKey::make('reviews:iframe', [
                'p' => 'any', 'loc' => 'all', 'lim' => $limit, 'min' => $minRating, 'fb' => 2,
            ], 3);

            $reviews = $this->rememberSafe($anyKey, $ttl, function () use ($agg, $limit, $lang, $minRating) {
                return $agg->aggregate([
                    'limit'      => max(50, $limit * 5),
                    'language'   => $lang,
                    'min_rating' => $minRating,
                ])->filter(fn ($r) => (int)($r['rating'] ?? 0) >= $minRating)->values();
            }, $force)->map(function ($r) use ($tourId) { if ($tourId) $r['tour_id'] = $tourId; return $r; });
        }

        // Completar nombres de tour
        $reviews = $this->attachTourNames($reviews, $lang, config('app.fallback_locale', 'es'), $tourId);

        // Elegir n-ésimo
        $count = $reviews->count();
        if ($count > 0) {
            $idx = ($nth - 1) % $count;
            $reviews = collect([$reviews->values()->get($idx)]);
        } else {
            $reviews = collect();
        }

        // ===== HTTP caching (ETag + Cache-Control) =====
        $hashOfSelected = $reviews->isNotEmpty() ? sha1(json_encode($reviews->first())) : 'empty';
        $etag = sprintf('rev:%s|tour:%s|nth:%s|loc:%s|layout:%s|theme:%s|min:%d|h:%s',
            $provider,
            $tourId ?: 'all',
            $nth,
            $lang,
            $layout, $theme,
            $minRating,
            $hashOfSelected
        );

        $response = response()->view('reviews.embed', [
            'reviews'  => $reviews,
            'provider' => $provider,
            'base'     => $base,
            'uid'      => $uid,
            'layout'   => $layout,
            'theme'    => $theme,
        ]);

        $response->setEtag($etag)
            ->header('Cache-Control', 'public, max-age=900, s-maxage=900, stale-while-revalidate=300, stale-if-error=86400')
            ->header('Vary', 'Accept-Language');

        if ($response->isNotModified($request)) {
            return $response;
        }
        return $response;
    }

    /** Guardar reseña local */
    public function store(StoreReviewRequest $request)
    {
        $key = sprintf('review:%s:%s', $request->ip(), (string) $request->input('tour_id'));

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            // Traducción
            throw ValidationException::withMessages([
                'body' => [__('reviews.public.too_many', ['s' => $seconds])],
            ]);
        }

        try {
            $data = method_exists($request, 'validatedData')
                ? $request->validatedData()
                : $request->validated();

            if (Auth::check()) $data['user_id'] = Auth::id();

            Review::create($data);
            RateLimiter::hit($key, 600);

            // Traducción
            return back()->with('success', __('reviews.public.thanks'));
        } catch (Throwable $e) {
            Log::error('review.store.failed', ['msg' => $e->getMessage()]);
            // Traducción
            return back()->withInput()
                ->withErrors(['body' => __('reviews.public.fail')]);
        }
    }

    /* ================= Helpers ================= */

    /**
     * Proveedores activos desde review_providers (keyed por slug),
     * con ->settings ya decodificado a array.
     */
    private function getActiveProviders(): \Illuminate\Support\Collection
    {
        $rows = DB::table('review_providers')
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        return $rows->map(function ($r) {
            $r->settings  = $this->decodeJsonSafe($r->settings);
            $r->indexable = (bool) $r->indexable;
            return $r;
        })->keyBy('slug');
    }

    private function decodeJsonSafe($raw)
    {
        if (is_array($raw)) return $raw;
        if (!is_string($raw) || $raw === '') return [];
        try { return json_decode($raw, true, 512, JSON_THROW_ON_ERROR); }
        catch (\Throwable $e) { return []; }
    }

    /**
     * Elige el mejor proveedor remoto para un tour:
     * - si algún provider tiene settings.product_map[tour_id] => prioridad
     * - si no, primer remoto activo por orden de aparición
     */
    private function pickProviderForTourByBinding(int $tourId, \Illuminate\Support\Collection $providers, array $remoteOrder): string
    {
        foreach ($remoteOrder as $slug) {
            $prov = $providers->get($slug);
            if (!$prov) continue;
            $map = (array) data_get($prov->settings, 'product_map', []);
            if (array_key_exists((string)$tourId, $map) || array_key_exists((int)$tourId, $map)) {
                return $slug;
            }
        }
        // fallback: primero de la lista remota
        return $remoteOrder[0] ?? 'viator';
    }

    /**
     * Enriquecer colección con tour_name usando locale/fallback.
     */
    private function attachTourNames(
        Collection $items,
        string $locale,
        string $fallback,
        int|string|null $forceTourId = null,
        ?string $forceTourName = null
    ): Collection {
        $ids = $items->pluck('tour_id')->filter()->unique()->values();
        if ($forceTourId) $ids = $ids->push((int) $forceTourId)->unique()->values();

        if ($ids->isEmpty()) {
            if ($forceTourName) {
                return $items->map(function ($r) use ($forceTourName, $forceTourId) {
                    if (empty($r['tour_name'])) $r['tour_name'] = $forceTourName;
                    if ($forceTourId && empty($r['tour_id'])) $r['tour_id'] = $forceTourId;
                    return $r;
                });
            }
            return $items;
        }

        $tours = Tour::with('translations')->whereIn('tour_id', $ids)->get()->keyBy('tour_id');

        return $items->map(function ($r) use ($tours, $locale, $fallback, $forceTourId, $forceTourName) {
            $tid = $r['tour_id'] ?? $forceTourId ?? null;

            if (!empty($tid) && empty($r['tour_name'])) {
                $t = $tours->get((int) $tid);
                if ($t) {
                    $tr = ($t->translations ?? collect())->firstWhere('locale', $locale)
                        ?: ($t->translations ?? collect())->firstWhere('locale', $fallback);
                    $r['tour_name'] = $tr->name ?? $t->name ?? '';
                } elseif ($forceTourName) {
                    $r['tour_name'] = $forceTourName;
                }
            } elseif (empty($r['tour_name']) && $forceTourName) {
                $r['tour_name'] = $forceTourName;
            }

            if ($forceTourId && empty($r['tour_id'])) $r['tour_id'] = $forceTourId;

            return $r;
        });
    }

    /** Ordena proveedores poniendo primero el “bind” del tour */
    private function providerOrderForTour(int $tourId, \Illuminate\Support\Collection $providers, array $remoteOrder): array
    {
        $main = $this->pickProviderForTourByBinding($tourId, $providers, $remoteOrder);
        $rest = array_values(array_filter($remoteOrder, fn ($p) => $p !== $main));
        return array_values(array_unique(array_merge([$main], $rest)));
    }

    /** Cuenta cuántos reviews remotos (únicos) hay realmente para ese tour/proveedor */
    private function remoteAvailableCount(
        ReviewAggregator $agg,
        string $provider,
        int $tourId,
        int $poolLimit,
        int $minRating,
        int $ttl,
        bool $force
    ): int {
        $key = CacheKey::make('reviews:remote:count', [
            'p'    => $provider,
            'tour' => $tourId,
            'min'  => $minRating,
            'lim'  => $poolLimit,
        ], 1);

        $loader = function () use ($agg, $provider, $tourId, $poolLimit, $minRating) {
            $coll = $agg->aggregate([
                'provider'   => $provider,
                'limit'      => max(50, $poolLimit * 5), // pedir un pool razonable
                'tour_id'    => $tourId,
                'min_rating' => $minRating,
            ])->filter(fn ($r) => (int)($r['rating'] ?? 0) >= $minRating);

            // Unicidad por hash de contenido/autor/fecha
            $seen = [];
            $uniq = 0;
            foreach ($coll as $r) {
                $h = strtolower((string)($r['provider'] ?? $provider)) . '#' . md5(
                    mb_strtolower(trim((string)($r['body'] ?? ''))) . '|' .
                    mb_strtolower(trim((string)($r['author_name'] ?? ''))) . '|' .
                    trim((string)($r['date'] ?? ''))
                );
                if (!isset($seen[$h])) {
                    $seen[$h] = true;
                    $uniq++;
                }
            }
            return $uniq;
        };

        return (int) $this->rememberSafe($key, min($ttl, 1800), $loader, $force);
    }
}
