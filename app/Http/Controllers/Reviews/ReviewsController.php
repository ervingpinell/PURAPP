<?php

namespace App\Http\Controllers\Reviews;

use Illuminate\Routing\Controller;
use App\Http\Requests\Reviews\StoreReviewRequest;
use App\Models\Review;
use App\Models\Tour;
use App\Services\Reviews\ReviewAggregator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Throwable;

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

        // 1) Tours activos
        $q = Tour::with('translations')->where('is_active', true);
        if (Schema::hasColumn('tours', 'sort_order')) {
            $q->orderByRaw('sort_order IS NULL, sort_order ASC');
        }
        $q->orderBy('name');
        $tours = $q->get(['tour_id', 'name']);

        // 2) Traducir nombres
        $tours = $tours->map(function ($t) use ($locale, $fallback) {
            $tr = ($t->translations ?? collect())->firstWhere('locale', $locale)
                ?: ($t->translations ?? collect())->firstWhere('locale', $fallback);
            $t->display_name = $tr->name ?? $t->name ?? '';
            return $t;
        });

        // 3) Traer reviews y preparar slides (~5-6 por tour, sin repetir)
        $tours = $tours->map(function ($tour) use ($ttl) {
            $cacheKey = "reviews:tour:{$tour->tour_id}:index";

            $reviews = Cache::remember($cacheKey, $ttl, function () use ($tour) {
                return app(ReviewAggregator::class)->aggregate([
                    'tour_id' => $tour->tour_id,
                    'limit'   => 10, // Pool más grande
                ]);
            });

            // Deduplicar reviews
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

            // Separar indexables vs no-indexables (tomar hasta 6 total)
            $indexables = $reviews->where('indexable', true)->take(4);
            $nonIndexables = $reviews->where('indexable', false)->take(6 - $indexables->count());

            // Construir slides (mezcla local + remoto)
            $slides = collect();

            // Añadir indexables como slides locales
            foreach ($indexables as $r) {
                $slides->push(['type' => 'local', 'data' => $r]);
            }

            // Añadir remotos como iframes (agrupados por proveedor)
            $providersBySlug = $nonIndexables->groupBy('provider');
            foreach ($providersBySlug as $provSlug => $items) {
                foreach ($items as $i => $r) {
                    $slides->push([
                        'type'     => 'remote',
                        'provider' => $provSlug,
                        'nth'      => $i + 1,
                    ]);
                }
            }

            // Propiedades para el blade
            $tour->indexable_reviews = $indexables;
            $tour->slides = $slides->take(6); // Máximo 6 slides
            $tour->needs_iframe = $nonIndexables->isNotEmpty();
            $tour->iframe_slug = $nonIndexables->first()['provider'] ?? 'viator';
            $tour->pool_limit = 30;

            return $tour;
        });

        return view('reviews.index', compact('tours'));
    }

    /**
     * Reviews de un tour específico (mínimo 12-15, sin repetir)
     */
    public function tour(
        int|string $tourId,
        ReviewAggregator $agg,
        Request $request
    ) {
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'es');

        // Cargar el tour para obtener su nombre traducido
        $tour = Tour::with('translations')->find($tourId);

        if (!$tour) {
            abort(404);
        }

        $tr = ($tour->translations ?? collect())->firstWhere('locale', $locale)
            ?: ($tour->translations ?? collect())->firstWhere('locale', $fallback);
        $tourName = $tr->name ?? $tour->name ?? '';

        $target = 15; // Objetivo: 15 reviews

        // 1) Traer reviews del tour (pedir más para tener pool)
        $ownReviews = $agg->aggregate([
            'tour_id' => $tourId,
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
            ])->filter(fn($r) => ($r['tour_id'] ?? null) != $tourId)
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
            if (empty($r['tour_id'])) {
                $r['tour_id'] = $tourId;
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
        $tourId  = $request->query('tour_id');
        $nth     = max(1, (int) $request->query('nth', 1));
        $ttlMin  = (int) $request->query('ttl', 60 * 24);
        $ttl     = max(60, $ttlMin) * 60;

        $layout  = (string) $request->query('layout', 'hero');
        $theme   = (string) $request->query('theme', $layout === 'card' ? 'site' : 'embed');
        $base    = (int) $request->query('base', $layout === 'card' ? 500 : 460);
        $uid     = (string) $request->query('uid', 'u' . substr(sha1(uniqid('', true)), 0, 10));

        // Cache key
        $cacheKey = 'reviews:embed:' . md5(json_encode([
            'p' => $provider, 'tour' => $tourId, 'lim' => $limit
        ]));

        $reviews = Cache::remember($cacheKey, $ttl, function () use ($agg, $provider, $limit, $tourId) {
            return $agg->aggregate([
                'provider' => $provider,
                'limit'    => $limit * 2,
                'tour_id'  => $tourId,
            ]);
        });

        // Deduplicar
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

        // Elegir n-ésimo
        $count = $reviews->count();
        if ($count > 0) {
            $idx = ($nth - 1) % $count;
            $reviews = collect([$reviews->get($idx)]);
        } else {
            $reviews = collect();
        }

        // HTTP caching
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
            ->header('Vary', 'Accept-Language');

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
        $key = sprintf('review:%s:%s', $request->ip(), (string) $request->input('tour_id'));

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
