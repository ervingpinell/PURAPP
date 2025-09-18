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

    /**
     * =========================
     * Página global (grid por tour)
     * =========================
     */
    public function index(Request $request, ReviewAggregator $agg)
    {
        $locale   = app()->getLocale();
        $fallback = config('app.fallback_locale', 'es');
        $ttl      = $this->defaultTtl;
        $force    = (bool) $request->boolean('refresh', false);

        // 1) Tours activos (sin columnas de proveedores)
        $q = Tour::with('translations')->where('is_active', true);
        if (Schema::hasColumn('tours', 'sort_order')) {
            $q->orderByRaw('sort_order IS NULL, sort_order ASC');
        }
        $q->orderBy('name');
        $tours = $q->get(['tour_id', 'name']);

        // Nombre traducido
        $tours = $tours->map(function ($t) use ($locale, $fallback) {
            $tr = ($t->translations ?? collect())->firstWhere('locale', $locale)
                ?: ($t->translations ?? collect())->firstWhere('locale', $fallback);
            $t->display_name = $tr->name ?? $t->name ?? '';
            return $t;
        });

        // 2) Parámetros
        $WANT        = (int) $request->integer('per_tour', 5);   // 5 por tour
        $LOCAL_POOL  = max($WANT, 12);                           // pool locales
        $REMOTE_POOL = (int) $request->integer('pool_limit', 30); // pool remoto para nth

        // 3) Proveedores activos (modular)
        $providers = $this->getActiveProviders(); // keyed por slug, con ->settings (array)
        $remoteOrder = array_keys($providers->where('indexable', false)->all()); // ej. ['viator','gyg','tripadvisor','google']
        if (empty($remoteOrder)) {
            // si no hay remotos definidos, aún así deja algo por defecto
            $remoteOrder = ['viator'];
        }

        // 4) Traer locales (indexables) por tour
        $tours = $tours->map(function ($t) use ($agg, $locale, $fallback, $LOCAL_POOL, $ttl, $force) {
            $cacheKey = CacheKey::make('reviews:idx:tour', [
                'tour'  => $t->tour_id,
                'loc'   => $locale,
                'limit' => $LOCAL_POOL,
            ], 1);

            $loader = fn() => $agg->aggregate([
                'tour_id'        => $t->tour_id,
                'limit'          => $LOCAL_POOL,
                'only_indexable' => true, // locales + cualquier provider marcado indexable
                'language'       => $locale,
            ])->values();

            /** @var \Illuminate\Support\Collection $items */
            $items = $this->rememberSafe($cacheKey, $ttl, $loader, $force);

            // completar tour_name
            $items = $this->attachTourNames($items, $locale, $fallback, $t->tour_id, $t->display_name);

            $t->indexable_reviews = $items;
            return $t;
        });

        // 5) Dedupe global por contenido
        $seen = [];
        $tours = $tours->map(function ($t) use (&$seen) {
            $items = collect($t->indexable_reviews ?? []);
            $items = $items->filter(function ($r) use (&$seen) {
                $key = strtolower((string)($r['provider'] ?? 'p')) . '#' . md5(
                    mb_strtolower(trim((string)($r['body'] ?? ''))) . '|' .
                    mb_strtolower(trim((string)($r['author_name'] ?? ''))) . '|' .
                    trim((string)($r['date'] ?? ''))
                );
                if (isset($seen[$key])) return false;
                $seen[$key] = true;
                return true;
            })->values();

            $t->indexable_reviews = $items;
            return $t;
        });

        // 6) Construir slides por tour: mezcla locales + remotos
        $tours = $tours->map(function ($t) use ($WANT, $REMOTE_POOL, $providers, $remoteOrder) {

            $locals = collect($t->indexable_reviews ?? [])->take($WANT)->values();
            $localCount = $locals->count();
            $need = max(0, $WANT - $localCount);

            // proveedor “mejor” para este tour (según product_map)
            $mainProv = $this->pickProviderForTourByBinding($t->tour_id, $providers, $remoteOrder);

            // si faltan, armamos remotos del mainProv (rotables por nth)
            $remoteSlides = [];
            if ($need > 0) {
                $seed = ($t->tour_id % $REMOTE_POOL) + 1; // semilla estable por tour
                for ($k = 0; $k < $need; $k++) {
                    $nth = (($seed - 1 + $k) % $REMOTE_POOL) + 1;
                    $remoteSlides[] = [
                        'type'     => 'remote',
                        'provider' => $mainProv,
                        'nth'      => $nth,
                        'limit'    => $REMOTE_POOL,
                    ];
                }
            }

            // alternar L/R empezando por local si hay
            $slides = [];
            $li = 0; $ri = 0;
            for ($i = 0; $i < $WANT; $i++) {
                $pickLocal = ($li < $localCount) && ( ($i % 2 === 0) || ($ri >= count($remoteSlides)) );
                if ($pickLocal) {
                    $slides[] = ['type' => 'local', 'data' => $locals[$li]];
                    $li++;
                } elseif ($ri < count($remoteSlides)) {
                    $slides[] = $remoteSlides[$ri];
                    $ri++;
                }
            }

            $t->slides       = collect($slides);
            $t->needs_iframe = count($remoteSlides) > 0;
            return $t;
        });

        return view('reviews.index', compact('tours'));
    }

    /**
     * Página específica del tour (sin cambios funcionales).
     */
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

        $loader = fn () =>
            $agg->aggregate([
                'tour_id'        => $tourId,
                'limit'          => 200,
                'only_indexable' => true,
            ])->values();

        /** @var \Illuminate\Support\Collection $locals */
        $locals = $this->rememberSafe($cacheKey, $ttl, $loader, $force);
        $locals = $this->attachTourNames($locals, $lang, config('app.fallback_locale', 'es'));

        $need  = max(0, 20 - $locals->count());
        $items = $locals->values();

        if ($need > 0) {
            $items->push([
                'provider'     => 'viator',
                'indexable'    => false,
                'iframe_limit' => $need,
                'tour_id'      => $tourId,
            ]);
        }

        return view('reviews.tour', compact('items', 'tourId'));
    }

    /**
     * Iframe de proveedor (embed) – sin cambios relevantes a tu versión.
     * Respeta layout=card|hero, theme=site|embed, limit/nth, tour_id, show_powered.
     */
    public function embed(Request $request, ReviewAggregator $agg, string $provider)
    {
        $lang     = app()->getLocale();
        $provider = strtolower(trim($provider)) ?: 'viator';

        $limit  = max(1, (int) $request->query('limit', 12));
        $tourId = $request->query('tour_id');

        $ttlMin = (int) $request->query('ttl', 60 * 24);
        $ttl    = max(60, $ttlMin) * 60;
        $force  = (bool) $request->boolean('refresh', false);
        $nth    = max(1, (int) $request->query('nth', 1));

        $layout = (string) $request->query('layout', 'hero'); // hero|card
        $theme  = (string) $request->query('theme', $layout === 'card' ? 'site' : 'embed');
        $base   = (int) $request->query('base', $layout === 'card' ? 500 : 460);
        $uid    = (string) $request->query('uid', 'u' . substr(sha1(uniqid('', true)), 0, 10));

        // Pool principal por provider (+ tour si viene)
        $cacheKey = CacheKey::make('reviews:iframe', [
            'p'    => $provider,
            'tour' => $tourId ?: 'all',
            'loc'  => 'all',
            'lim'  => $limit,
        ], 2);

        $loader = fn () =>
            $agg->aggregate([
                'provider' => $provider,
                'limit'    => max(50, $limit * 5),
                'tour_id'  => $tourId,     // el driver usará settings.product_map[tour_id]
                'language' => $lang,
            ])->values();

        /** @var \Illuminate\Support\Collection $reviews */
        $reviews = $this->rememberSafe($cacheKey, $ttl, $loader, $force);

        // Fallbacks (mismo provider sin tour, luego cualquiera)
        if ($reviews->isEmpty() && !empty($tourId)) {
            $fallbackKey = CacheKey::make('reviews:iframe', [
                'p'    => $provider, 'tour' => 'all', 'loc' => 'all', 'lim' => $limit, 'fb' => 1,
            ], 2);

            $reviews = $this->rememberSafe($fallbackKey, $ttl, function () use ($agg, $provider, $limit, $lang) {
                return $agg->aggregate([
                    'provider' => $provider,
                    'limit'    => max(50, $limit * 5),
                    'language' => $lang,
                ])->values();
            }, $force)->map(function ($r) use ($tourId) { $r['tour_id'] = $tourId; return $r; });
        }

        if ($reviews->isEmpty()) {
            $anyKey = CacheKey::make('reviews:iframe', [
                'p' => 'any', 'loc' => 'all', 'lim' => $limit, 'fb' => 2,
            ], 2);

            $reviews = $this->rememberSafe($anyKey, $ttl, function () use ($agg, $limit, $lang) {
                return $agg->aggregate([
                    'limit'    => max(50, $limit * 5),
                    'language' => $lang,
                ])->values();
            }, $force)->map(function ($r) use ($tourId) { if ($tourId) $r['tour_id'] = $tourId; return $r; });
        }

        // completar tour_name
        $reviews = $this->attachTourNames($reviews, $lang, config('app.fallback_locale', 'es'), $tourId);

        // Elegir el n-ésimo
        $count = $reviews->count();
        if ($count > 0) {
            $idx = ($nth - 1) % $count;
            $reviews = collect([$reviews->values()->get($idx)]);
        } else {
            $reviews = collect();
        }

        if ($request->boolean('debug')) {
            return response()->json([
                'provider' => $provider,
                'tour_id'  => $tourId,
                'layout'   => $layout,
                'theme'    => $theme,
                'count'    => $count,
                'selected' => $reviews->first(),
            ]);
        }

        return view('reviews.embed', [
            'reviews'  => $reviews,
            'provider' => $provider,
            'base'     => $base,
            'uid'      => $uid,
            'layout'   => $layout,
            'theme'    => $theme,
        ]);
    }

    /** Guardar reseña local */
    public function store(StoreReviewRequest $request)
    {
        $key = sprintf('review:%s:%s', $request->ip(), (string) $request->input('tour_id'));

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'body' => [__('Demasiadas reseñas seguidas. Intenta de nuevo en :s segundos.', ['s' => $seconds])],
            ]);
        }

        try {
            $data = method_exists($request, 'validatedData')
                ? $request->validatedData()
                : $request->validated();

            if (Auth::check()) $data['user_id'] = Auth::id();

            Review::create($data);
            RateLimiter::hit($key, 600);

            return back()->with('success', __('¡Gracias! Tu reseña fue recibida y será revisada.'));
        } catch (Throwable $e) {
            Log::error('review.store.failed', ['msg' => $e->getMessage()]);
            return back()->withInput()
                ->withErrors(['body' => __('No pudimos registrar tu reseña en este momento. Intenta de nuevo.')]);
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
            $r->settings = $this->decodeJsonSafe($r->settings);
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
}
