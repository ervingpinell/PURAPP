<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReviewController extends Controller
{
    public function fetchReviews(Request $request)
    {
        $validated = $request->validate([
            'productCode' => 'required|string',
            'count'       => 'nullable|integer|min:1|max:50',
            'start'       => 'nullable|integer|min:1',
            'provider'    => 'nullable|in:VIATOR,TRIPADVISOR,ALL',
            'sortBy'      => 'nullable|string',
        ]);

        $payload = [
            'productCode'                => $validated['productCode'],
            'count'                      => $validated['count'] ?? 5,
            'start'                      => $validated['start'] ?? 1,
            'provider'                   => $validated['provider'] ?? 'ALL',
            'sortBy'                     => $validated['sortBy'] ?? 'MOST_RECENT',
            'reviewsForNonPrimaryLocale' => true,
            'showMachineTranslated'      => true,
        ];

        $acceptLang = $this->mapLocale(app()->getLocale());
        $cacheKey   = $this->cacheKey($payload, $acceptLang);
        $ttl        = now()->addHours(6);

        if ($cached = Cache::get($cacheKey)) {
            if ($this->wantsHtml($request)) {
                return redirect()->back()->with('success', __('reviews.loaded'));
            }
            return response()->json($cached + ['cached' => true], 200);
        }

        $lock = Cache::lock("lock:$cacheKey", 10);
        try {
            if ($lock->get()) {
                if ($cached = Cache::get($cacheKey)) {
                    if ($this->wantsHtml($request)) {
                        return redirect()->back()->with('success', __('reviews.loaded'));
                    }
                    return response()->json($cached + ['cached' => true], 200);
                }

                $url = config('services.viator.reviews_base');
                $key = config('services.viator.key');

                $response = Http::withHeaders([
                        'exp-api-key'     => $key,
                        'Accept'          => 'application/json;version=2.0',
                        'Accept-Language' => $acceptLang,
                    ])
                    ->timeout(12)
                    ->retry(2, 300)
                    ->post($url, $payload);

                if (!$response->ok()) {
                    Log::warning('Viator reviews non-200', [
                        'status' => $response->status(),
                        'body'   => $response->body(),
                    ]);

                    $negative = ['reviews' => [], 'error' => 'upstream_'.$response->status()];
                    Cache::put($cacheKey, $negative, now()->addMinutes(5));

                    if ($this->wantsHtml($request)) {
                        return redirect()->back()->with('error', __('reviews.provider_error'));
                    }
                    return response()->json($negative + ['cached' => false], 200);
                }

                $data = $response->json();

                if (app()->environment('local')) {
                    Log::debug('Sample upstream review payload', [
                        'keys'  => array_keys(($data['reviews'][0] ?? [])),
                        'first' => ($data['reviews'][0] ?? null),
                    ]);
                }

                $normalized = [
                    'reviews' => $this->mapReviews($data, $validated['productCode']),
                ];

                Cache::put($cacheKey, $normalized, $ttl);

                if ($this->wantsHtml($request)) {
                    return redirect()->back()->with('success', __('reviews.loaded'));
                }
                return response()->json($normalized + ['cached' => false], 200);
            } else {
                if ($cached = Cache::get($cacheKey)) {
                    if ($this->wantsHtml($request)) {
                        return redirect()->back()->with('success', __('reviews.loaded'));
                    }
                    return response()->json($cached + ['cached' => true], 200);
                }

                if ($this->wantsHtml($request)) {
                    return redirect()->back()->with('error', __('reviews.service_busy'));
                }
                return response()->json(['reviews' => [], 'error' => 'busy'], 200);
            }
        } catch (\Throwable $e) {
            Log::error('Viator reviews exception', ['msg' => $e->getMessage()]);

            if ($this->wantsHtml($request)) {
                return redirect()->back()->with('error', __('reviews.unexpected_error'));
            }
            return response()->json(['reviews' => [], 'error' => 'exception'], 200);
        } finally {
            optional($lock)->release();
        }
    }

    /**
     * Batch endpoint: multiple products in one call.
     */
    public function fetchReviewsBatch(Request $request)
    {
        $validated = $request->validate([
            'productCodes'   => 'required|array|min:1|max:50',
            'productCodes.*' => 'string',
            'count'          => 'nullable|integer|min:1|max:50',
            'start'          => 'nullable|integer|min:1',
            'provider'       => 'nullable|in:VIATOR,TRIPADVISOR,ALL',
            'sortBy'         => 'nullable|string',
        ]);

        $count    = $validated['count']    ?? 5;
        $start    = $validated['start']    ?? 1;
        $provider = $validated['provider'] ?? 'ALL';
        $sortBy   = $validated['sortBy']   ?? 'MOST_RECENT';

        $acceptLang = $this->mapLocale(app()->getLocale());
        $url        = config('services.viator.reviews_base');
        $key        = config('services.viator.key');

        $results = [];
        $pendingPayloads = [];

        // Fill from cache first
        foreach ($validated['productCodes'] as $code) {
            $payload = [
                'productCode'                => $code,
                'count'                      => $count,
                'start'                      => $start,
                'provider'                   => $provider,
                'sortBy'                     => $sortBy,
                'reviewsForNonPrimaryLocale' => true,
                'showMachineTranslated'      => true,
            ];
            $cacheKey = $this->cacheKey($payload, $acceptLang);

            if ($cached = Cache::get($cacheKey)) {
                $results[$code] = $cached + ['cached' => true];
            } else {
                $pendingPayloads[$code] = [$payload, $cacheKey];
            }
        }

        // Parallel only for missing entries
        if (!empty($pendingPayloads)) {
            $responses = Http::pool(function ($pool) use ($pendingPayloads, $url, $key, $acceptLang) {
                $requests = [];
                foreach ($pendingPayloads as $code => [$payload]) {
                    $requests[$code] = $pool->as($code)->withHeaders([
                        'exp-api-key'     => $key,
                        'Accept'          => 'application/json;version=2.0',
                        'Accept-Language' => $acceptLang,
                    ])->timeout(12)->retry(2, 300)->post($url, $payload);
                }
                return $requests;
            });

            foreach ($responses as $code => $response) {
                if ($response->ok()) {
                    $data = $response->json();
                    $normalized = ['reviews' => $this->mapReviews($data, $code)];
                    Cache::put($pendingPayloads[$code][1], $normalized, now()->addHours(6));
                    $results[$code] = $normalized + ['cached' => false];
                } else {
                    $negative = ['reviews' => [], 'error' => 'upstream_'.$response->status()];
                    Cache::put($pendingPayloads[$code][1], $negative, now()->addMinutes(5));
                    $results[$code] = $negative + ['cached' => false];
                }
            }
        }

        if ($this->wantsHtml($request)) {
            return redirect()->back()->with('success', __('reviews.loaded'));
        }

        return response()->json(['results' => $results], 200);
    }

    /**
     * Idempotent GET version (cache-friendly).
     */
    public function fetchReviewsGet(Request $request, string $productCode)
    {
        $request->merge([
            'productCode' => $productCode,
            'count'       => $request->query('count'),
            'start'       => $request->query('start'),
            'provider'    => $request->query('provider'),
            'sortBy'      => $request->query('sortBy'),
        ]);

        $res = $this->fetchReviews($request);

        if (!method_exists($res, 'header')) {
            return $res;
        }

        return $res->header(
            'Cache-Control',
            'public, max-age=300, s-maxage=900, stale-while-revalidate=86400'
        );
    }

    // ---------- Helpers ----------

    private function wantsHtml(Request $request): bool
    {
        return ! $request->expectsJson() && ! $request->wantsJson() && ! $request->ajax();
    }

    private function cacheKey(array $payload, string $acceptLang): string
    {
        $keyParts = [
            'viator:reviews',
            $acceptLang,
            $payload['productCode'] ?? '',
            $payload['provider']    ?? 'ALL',
            $payload['sortBy']      ?? 'MOST_RECENT',
            'count='.$payload['count'],
            'start='.$payload['start'],
        ];
        return Str::slug(implode('|', $keyParts), ':');
    }

    /** Shape upstream data to the JS structure. */
    private function mapReviews(array $data, string $requestedCode): array
    {
        $out = [];

        foreach (($data['reviews'] ?? []) as $r) {
            $userName      = $r['userName']      ?? $this->displayName($r) ?? __('reviews.anonymous');
            $publishedIso  = $r['publishedDate'] ?? null;
            $avatarUrl     = $r['avatarUrl']     ?? $this->firstNonEmpty($r, [
                'user.avatar', 'user.avatarUrl', 'avatar', 'profilePhoto', 'reviewer.avatarUrl', 'reviewer.avatar',
            ]);
            $rating        = (float)($r['rating']
                                ?? $this->firstNonEmpty($r, ['overallRating', 'score', 'ratingOverall'])
                                ?? 0);
            $title         = $r['title'] ?? $this->firstNonEmpty($r, ['headline', 'summary']);
            $text          = $r['text']  ?? $this->firstNonEmpty($r, ['content', 'reviewText', 'body', 'comments']);
            $productTitle  = $r['productTitle']
                ?? $this->firstNonEmpty($r, ['product.title', 'productName', 'titleOfProduct'])
                ?? '';

            if (!$publishedIso) {
                $rawDate = $this->firstNonEmpty($r, [
                    'published', 'publishDate', 'publishedDate', 'submissionDate', 'reviewDate',
                    'created', 'createdTime', 'createdAt', 'reviewSubmissionTime',
                    'dates.published', 'dates.reviewDate', 'dates.created',
                ]);
                $publishedIso = $this->normalizeIso($rawDate);
            }

            $out[] = [
                'avatarUrl'     => $avatarUrl ?: null,
                'publishedDate' => $publishedIso,
                'rating'        => $rating,
                'userName'      => $userName,
                'title'         => $title ?: null,
                'text'          => $text  ?: null,
                'productTitle'  => $productTitle,
                'productCode'   => $r['productCode'] ?? $requestedCode,
            ];
        }

        return $out;
    }

    private function displayName(array $r): ?string
    {
        $anon = $this->firstNonEmpty($r, ['anonymous', 'isAnonymous', 'user.anonymous', 'reviewer.anonymous']);
        if ($anon === true || $anon === 'true' || $anon === 1 || $anon === '1') {
            return __('reviews.anonymous');
        }

        $direct = $this->firstNonEmpty($r, [
            'user.name', 'user.displayName',
            'reviewer.name', 'reviewer.displayName',
            'author', 'authorName', 'authorDisplayName',
            'consumerName', 'viatorConsumerName', 'reviewerName',
        ]);
        if (!empty($direct)) {
            return $direct;
        }

        $first = $this->firstNonEmpty($r, [
            'user.firstName', 'reviewer.firstName', 'consumer.firstName', 'traveler.firstName',
        ]);
        $last = $this->firstNonEmpty($r, [
            'user.lastName', 'reviewer.lastName', 'consumer.lastName', 'traveler.lastName',
        ]);
        if ($first && $last) return trim($first.' '.$last);
        if ($first)         return $first;
        if ($last)          return $last;

        $fi = $this->firstNonEmpty($r, ['user.firstInitial', 'reviewer.firstInitial']);
        $li = $this->firstNonEmpty($r, ['user.lastInitial',  'reviewer.lastInitial']);
        if ($fi || $li) return trim(($fi ?: '').($li ? ' '.$li : ''));

        return null;
    }

    private function firstNonEmpty(array $arr, array $keys)
    {
        foreach ($keys as $key) {
            $val = data_get($arr, $key);
            if (is_string($val)) $val = trim($val);
            if ($val !== null && $val !== '' && $val !== []) {
                return $val;
            }
        }
        return null;
    }

    private function normalizeIso($raw): ?string
    {
        if ($raw === null || $raw === '') return null;

        if (is_numeric($raw)) {
            $ts = (int) $raw;
            if ($ts > 9999999999) {
                $ts = (int) round($ts / 1000);
            }
            try {
                return Carbon::createFromTimestampUTC($ts)->toIso8601String();
            } catch (\Throwable $e) {
                return null;
            }
        }

        try {
            return Carbon::parse($raw)->toIso8601String();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function mapLocale(?string $appLocale): string
    {
        $appLocale = $appLocale ?: 'en';
        return match ($appLocale) {
            'es', 'es_CR', 'es_MX', 'es-CR', 'es-MX' => 'es-ES',
            'pt', 'pt_BR', 'pt-BR'                    => 'pt-BR',
            'fr', 'fr_FR', 'fr-FR'                    => 'fr-FR',
            'de', 'de_DE', 'de-DE'                    => 'de-DE',
            default                                   => 'en-US',
        };
    }


}
