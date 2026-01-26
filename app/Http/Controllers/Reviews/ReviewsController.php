<?php

namespace App\Http\Controllers\Reviews;

use Illuminate\Routing\Controller;
use App\Http\Requests\Reviews\StoreReviewRequest;
use App\Models\Review;
use App\Models\Product;
use App\Services\Reviews\ReviewAggregator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * ReviewsController
 *
 * Handles tour review operations.
 */
class ReviewsController extends Controller
{
    private int $defaultTtl = 60 * 60 * 24; // 24h

    public function __construct()
    {
        $this->middleware('web');
        $this->middleware('throttle:6,1')->only('store');
    }

    /**
     * Índice de reviews agrupadas por tour
     */
public function index(Request $request)
{
    $locale   = app()->getLocale();
    $fallback = config('app.fallback_locale', 'es');
    $ttl      = $this->defaultTtl;

    // 1) Tours activos CON SLUG
    $q = Product::where('is_active', true);
    if (Schema::hasColumn('tours', 'sort_order')) {
        $q->orderByRaw('sort_order IS NULL, sort_order ASC');
    }
    $q->orderBy('name');
    $tours = $q->get(['product_id', 'name', 'slug']);

    // 2) Traducir nombres
    $tours = $tours->map(function ($t) use ($locale, $fallback) {
        $tr = ($t->translations ?? collect())->firstWhere('locale', $locale)
            ?: ($t->translations ?? collect())->firstWhere('locale', $fallback);
        $t->display_name = $tr->name ?? $t->name ?? '';
        return $t;
    });

    // 3) Traer reviews y preparar slides (~5-6 por tour, sin repetir)
    $tours = $tours->map(function ($tour) use ($ttl) {
        $cacheKey = "reviews:tour:{$tour->product_id}:index";

        $reviews = Cache::remember($cacheKey, $ttl, function () use ($tour) {
            return app(ReviewAggregator::class)->aggregate([
                'product_id' => $tour->product_id,
                'limit'   => 10, // Pool más grande
            ]);
        });

        // Deduplicar reviews (proveedor+ID o hash por contenido)
        $reviews = $reviews->unique(function($r) {
            $provider = strtolower($r['provider'] ?? 'p');
            if (!empty($r['provider_review_id'])) {
                return $provider . '#' . $r['provider_review_id'];
            }
            return $provider . '#' . md5(
                mb_strtolower(trim($r['body'] ?? '')) . '|' .
                mb_strtolower(trim($r['author_name'] ?? '')) . '|' .
                trim($r['date'] ?? '')
            );
        })->values();

        // Separar indexables vs no-indexables
        $indexables    = $reviews->where('indexable', true)->take(4)->values();
        $nonIndexables = $reviews->where('indexable', false)->values();

        // Construir slides (mezcla local + remoto)
        $slides = collect();

        // 1) Añadir indexables como slides locales
        foreach ($indexables as $r) {
            $slides->push(['type' => 'local', 'data' => $r]);
        }

        // 2) Añadir remotos: **1 slide por proveedor**, no una por cada review
        //    (evita duplicar la misma reseña cuando el proveedor sólo tiene 1 review)
        $providersBySlug = $nonIndexables->groupBy('provider');
        foreach ($providersBySlug as $provSlug => $items) {
            $slides->push([
                'type'     => 'remote',
                'provider' => $provSlug,
                'nth'      => 1,                 // siempre arrancamos en 1
                'pool'     => $items->count(),   // cuántas reviews reales tenemos de ese proveedor
            ]);
        }

        // Propiedades para el blade
        $tour->indexable_reviews = $indexables;
        $tour->slides            = $slides->take(6); // Máximo 6 slides mixtas
        $tour->needs_iframe      = $providersBySlug->isNotEmpty();
        $tour->iframe_slug       = $providersBySlug->keys()->first() ?? 'viator';
        $tour->pool_limit        = 30; // fallback (para iframes cuando no tengamos pool real)

        return $tour;
    });

    return view('reviews.index', compact('tours'));
}

/**
 * Reviews de un tour específico (mínimo 12-15, sin repetir)
 */
public function tour(
        Product $tour, // CAMBIADO: recibe el modelo directamente
        ReviewAggregator $agg,
        Request $request
    ) {
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'es');

        // Ya no necesitas find(), Laravel resolvió el tour
        $tour->load('translations');

        $tr = ($tour->translations ?? collect())->firstWhere('locale', $locale)
            ?: ($tour->translations ?? collect())->firstWhere('locale', $fallback);
        $tourName = $tr->name ?? $tour->name ?? '';
        $tourId = $tour->product_id;

        $target = 15; // Objetivo: 15 reviews

        // 1) Traer reviews del tour (pedir más para tener pool)
        $ownReviews = $agg->aggregate([
            'product_id' => $tourId,
            'limit'   => 50, // Pool grande
        ]);

        // Deduplicar estrictamente
        $ownReviews = $ownReviews->unique(function($r) {
            $provider = strtolower($r['provider'] ?? 'p');

            if (!empty($r['provider_review_id'])) {
                return $provider . '#' . $r['provider_review_id'];
            }

            return $provider . '#' . md5(
                mb_strtolower(trim($r['body'] ?? '')) . '|' .
                mb_strtolower(trim($r['author_name'] ?? '')) . '|' .
                trim($r['date'] ?? '')
            );
        })->values();

        // 2) Si no hay suficientes, traer de otros tours
        if ($ownReviews->count() < $target) {
            $needed = $target - $ownReviews->count();

            $othersReviews = $agg->aggregate([
                'limit' => $needed * 3, // Pool más grande
            ])->filter(fn($r) => ($r['product_id'] ?? null) != $tourId)
              ->unique(function($r) {
                  $provider = strtolower($r['provider'] ?? 'p');

                  if (!empty($r['provider_review_id'])) {
                      return $provider . '#' . $r['provider_review_id'];
                  }

                  return $provider . '#' . md5(
                      mb_strtolower(trim($r['body'] ?? '')) . '|' .
                      mb_strtolower(trim($r['author_name'] ?? '')) . '|' .
                      trim($r['date'] ?? '')
                  );
              })->take($needed);

            $ownReviews = $ownReviews->merge($othersReviews);
        }

        // 3) Tomar hasta el objetivo (sin repetir)
        $reviews = $ownReviews->take($target)->values();

        // 4) Adjuntar nombre del tour a TODAS las reviews
        $reviews = $reviews->map(function ($r) use ($tourName, $tourId) {
            if (empty($r['tour_name'])) {
                $r['tour_name'] = $tourName;
            }
            if (empty($r['product_id'])) {
                $r['product_id'] = $tourId;
            }
            return $r;
        });

        return view('reviews.tour', compact('reviews', 'tourId', 'tourName'));
    }

/**
 * Embed para iframes
 */
public function embed(Request $request, ReviewAggregator $agg, string $provider)
{
    $lang     = app()->getLocale();
    $provider = strtolower(trim($provider)) ?: 'viator';

    $limit   = min(60, max(1, (int) $request->query('limit', 12)));
    $tourId  = $request->query('product_id');
    $nth     = max(1, (int) $request->query('nth', 1));
    $ttlMin  = (int) $request->query('ttl', 60 * 24);
    $ttl     = max(60, $ttlMin) * 60;

    $layout  = (string) $request->query('layout', 'hero');
    $theme   = (string) $request->query('theme', $layout === 'card' ? 'site' : 'embed');
    $base    = (int) $request->query('base', $layout === 'card' ? 500 : 460);
    $uid     = (string) $request->query('uid', 'u' . substr(sha1(uniqid('', true)), 0, 10));

    $cacheKey = 'reviews:embed:' . md5(json_encode([
        'p' => $provider, 'tour' => $tourId, 'lim' => $limit
    ]));

    $reviews = Cache::remember($cacheKey, $ttl, function () use ($agg, $provider, $limit, $tourId) {
        return $agg->aggregate([
            'provider' => $provider,
            'limit'    => $limit * 2,
            'product_id'  => $tourId,
        ]);
    });

    $reviews = $reviews->unique(function($r) {
        $prov = strtolower($r['provider'] ?? 'p');
        if (!empty($r['provider_review_id'])) {
            return $prov . '#' . $r['provider_review_id'];
        }
        return $prov . '#' . md5(
            mb_strtolower(trim($r['body'] ?? '')) . '|' .
            mb_strtolower(trim($r['author_name'] ?? '')) . '|' .
            trim($r['date'] ?? '')
        );
    })->values();

    $count = $reviews->count();
    if ($count > 0) {
        $idx = ($nth - 1) % $count;
        $reviews = collect([$reviews->get($idx)]);
    } else {
        $reviews = collect();
    }

    $reqTname = trim((string) $request->query('tname', ''));
    $fallback = config('app.fallback_locale', 'es');

    if ($reviews->isNotEmpty()) {
        $reviews = $reviews->map(function ($r) use ($reqTname, $tourId, $lang, $fallback) {
            if ($reqTname !== '') {
                $r['tour_name'] = $reqTname;
                return $r;
            }

            $id = (int)($r['product_id'] ?? $tourId ?? 0);
            if ($id > 0) {
                $tour = \App\Models\Product::find($id);
                if ($tour) {
                    $tr = ($tour->translations ?? collect())->firstWhere('locale', $lang)
                        ?: ($tour->translations ?? collect())->firstWhere('locale', $fallback);
                    $resolved = $tr->name ?? $tour->name ?? null;
                    if ($resolved) {
                        $r['tour_name'] = $resolved;
                        $r['product_id']   = $id;
                        return $r;
                    }
                }
            }

            return $r;
        });
    }

    $hashOfSelected = $reviews->isNotEmpty() ? sha1(json_encode($reviews->first())) : 'empty';
    $etag = sprintf('rev:%s|tour:%s|nth:%s|h:%s',
        $provider, $tourId ?: 'all', $nth, $hashOfSelected
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
        ->header('Cache-Control', 'public, max-age=900, s-maxage=900, stale-while-revalidate=300')
        ->header('Vary', 'Accept-Language')
        ->header('X-Robots-Tag', 'noindex, nofollow, noarchive'); // evita indexación total

    if ($response->isNotModified($request)) {
        return $response;
    }

    return $response;
}


    /**
     * Guardar reseña local
     */
    public function store(StoreReviewRequest $request)
    {
        $key = sprintf('review:%s:%s', $request->ip(), (string) $request->input('product_id'));

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
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

            return back()->with('success', __('reviews.public.thanks'));
        } catch (Throwable $e) {
            Log::error('review.store.failed', ['msg' => $e->getMessage()]);
            return back()->withInput()
                ->withErrors(['body' => __('reviews.public.fail')]);
        }
    }
}
