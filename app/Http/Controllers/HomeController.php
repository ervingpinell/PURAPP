<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use App\Models\HotelList;
use App\Models\MeetingPoint;
use App\Models\Tour;
use App\Models\TourExcludedDate;
use App\Models\TourType;
use App\Services\Reviews\ReviewAggregator;
use App\Support\CacheKey;
use App\Support\Traits\RemembersSafely;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class HomeController extends Controller
{
    use RemembersSafely;

    /** ===========================
     *  HOME (seccionado)
     *  ===========================*/
    public function index(ReviewAggregator $agg)
    {
        $currentLocale  = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'es');

        try {
            // 1) Meta de tipos + Tours (usando helpers)
            $typeMeta   = $this->loadTypeMeta($currentLocale, $fallbackLocale);
            $tours      = $this->loadActiveToursWithTranslations($currentLocale, $fallbackLocale);

            $toursByType = $tours
                ->sortBy('tour_type_id_group', SORT_NATURAL | SORT_FLAG_CASE)
                ->groupBy(fn ($tour) => $tour->tour_type_id_group);

            // 2) REVIEWS HOME – reparto por tour (2 o 3 por tour)
            $cacheTtl     = 60 * 60 * 24;
            $forceRefresh = (bool) request()->boolean('refresh', false);

            // Ajusta a tu gusto:
            $TARGET_TOTAL  = 24; // límite global
            $PER_TOUR_GOAL = 2;  // 2 o 3 por tour

            $cacheKey = CacheKey::make('home_reviews2', [
                'loc'    => 'all',
                'target' => $TARGET_TOTAL,
                'per'    => $PER_TOUR_GOAL,
            ], 2); // bump version

            if ($forceRefresh) Cache::forget($cacheKey);

            $homeReviews = Cache::remember($cacheKey, $cacheTtl, function () use (
                $agg, $tours, $currentLocale, $fallbackLocale, $TARGET_TOTAL, $PER_TOUR_GOAL
            ) {
                return $this->buildHomeReviews(
                    $agg,
                    $tours,
                    $currentLocale,
                    $fallbackLocale,
                    $TARGET_TOTAL,
                    $PER_TOUR_GOAL
                );
            });

            // 3) Meeting Points
            $meetingPoints = MeetingPoint::active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id','name','pickup_time']);

            return view('public.home', compact('toursByType', 'typeMeta', 'homeReviews', 'meetingPoints'));
        } catch (Throwable $e) {
            Log::error('home.index.error', [
                'msg'  => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            $toursByType   = collect();
            $typeMeta      = collect();
            $homeReviews   = collect();
            $meetingPoints = collect();

            return view('public.home', compact('toursByType', 'typeMeta', 'homeReviews', 'meetingPoints'));
        }
    }

    /** ===========================
     *  SHOW TOUR (seccionado)
     *  ===========================*/
    public function showTour(int $id, ReviewAggregator $agg)
    {
        $loc = app()->getLocale();
        $fb  = config('app.fallback_locale', 'es');

        try {
            $tour = $this->loadTourWithTranslations($id, $loc, $fb);
            [$blockedGeneral, $blockedBySchedule, $fullyBlockedDates] = $this->computeTourBlocks($tour);
            $tourReviews  = $this->buildTourReviews($agg, $tour->tour_id);

            $hotels        = HotelList::orderBy('name')->get();
            $cancelPolicy  = $tour->cancel_policy ?? null;
            $refundPolicy  = $tour->refund_policy ?? null;
            $meetingPoints = $this->loadMeetingPoints(true);

            return view('public.tour-show', compact(
                'tour',
                'hotels',
                'cancelPolicy',
                'refundPolicy',
                'blockedGeneral',
                'blockedBySchedule',
                'fullyBlockedDates',
                'meetingPoints',
                'tourReviews'
            ));
        } catch (Throwable $e) {
            Log::error('tour.show.failed', ['tour_id' => $id, 'error' => $e->getMessage()]);
            abort(404);
        }
    }

    /* ===========================
       PRIVADOS — HOME
       ===========================*/

    private function loadTypeMeta(string $loc, string $fb): Collection
    {
        return TourType::active()
            ->with('translations')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($type) use ($loc, $fb) {
                $tr = $this->pickTranslation($type->translations, $loc, $fb);
                return [
                    $type->tour_type_id => [
                        'id'          => $type->tour_type_id,
                        'title'       => $tr->name ?? $type->name,
                        'duration'    => $tr->duration ?? $type->duration ?? '',
                        'description' => $tr->description ?? $type->description ?? '',
                        'cover_url'   => $type->cover_url,
                    ],
                ];
            });
    }

    private function loadActiveToursWithTranslations(string $loc, string $fb): Collection
    {
        return Tour::with(['tourType.translations', 'itinerary.items', 'translations'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($tour) use ($loc, $fb) {
                $tr = $this->pickTranslation($tour->translations, $loc, $fb);
                $tour->translated_name     = $tr->name ?? $tour->name;
                $tour->translated_overview = $tr->overview ?? $tour->overview;
                $tour->tour_type_id_group  = optional($tour->tourType)->tour_type_id ?? 'uncategorized';
                return $tour;
            });
    }

    /**
     * Construye colección de reseñas del home con reparto por tour:
     * - Prioriza indexables por tour.
     * - Si faltan para llegar a $PER_TOUR_GOAL, rellena con iframes (proveedores no indexables).
     */
    private function buildHomeReviews(
        ReviewAggregator $agg,
        Collection $tours,
        string $locale,
        string $fallback,
        int $TARGET_TOTAL = 24,
        int $PER_TOUR_GOAL = 2
    ): Collection {
        // 1) Traer ancho, dedupe
        $all = $agg->aggregate(['limit' => 2000]);
        $all = $all->unique(function ($r) {
            return md5(
                mb_strtolower(trim((string)($r['body'] ?? ''))) . '|' .
                mb_strtolower(trim((string)($r['author_name'] ?? ''))) . '|' .
                trim((string)($r['date'] ?? ''))
            );
        })->values();

        // 2) Índices útiles
        $activeTourIds = $tours->pluck('tour_id')->filter()->values();
        $providersPref = ['local', 'viator', 'tripadvisor', 'google', 'gyg', 'getyourguide'];
        $byTour        = $all->groupBy(fn ($r) => (int) ($r['tour_id'] ?? 0));

        $picked    = collect();
        $seenKeys  = [];
        $nthCursor = []; // "prov:tour" => nth

        $makeSeenKey = function (array $r): string {
            $base = ($r['provider'] ?? 'p') . '#' . ($r['provider_review_id'] ?? '');
            if (!empty($r['provider_review_id'])) return $base;
            return $base . '#' . md5(
                mb_strtolower(trim((string)($r['body'] ?? ''))) . '|' .
                mb_strtolower(trim((string)($r['author_name'] ?? ''))) . '|' .
                trim((string)($r['date'] ?? ''))
            );
        };

        $pushIframe = function (int $tourId, string $prov) use (&$picked, &$nthCursor) {
            $cursorKey = strtolower($prov) . ':' . $tourId;
            $nth       = ($nthCursor[$cursorKey] ?? 0) + 1;
            $nthCursor[$cursorKey] = $nth;

            $picked->push([
                'provider'     => strtolower($prov),
                'indexable'    => false,
                'iframe_limit' => 1,
                'tour_id'      => $tourId,
                'nth'          => $nth,
            ]);
        };

        $addIndexableFromBuckets = function (Collection $indexables, int $tourId, int $need) use (
            &$picked, &$seenKeys, $providersPref, $makeSeenKey
        ): int {
            if ($need <= 0) return 0;
            $buckets = $indexables->groupBy(fn($r) => strtolower((string)($r['provider'] ?? 'local')));
            $added   = 0; $i = 0;
            while ($added < $need && $buckets->isNotEmpty() && $i < 1000) {
                foreach ($providersPref as $pv) {
                    if ($added >= $need) break;
                    $bucket = $buckets->get($pv);
                    if (!$bucket || $bucket->isEmpty()) continue;
                    $item = $bucket->shift();
                    $key  = $makeSeenKey($item);
                    if (!isset($seenKeys[$key])) {
                        $seenKeys[$key] = true;
                        $picked->push($item);
                        $added++;
                    }
                    if ($bucket->isEmpty()) $buckets->forget($pv);
                    else $buckets->put($pv, $bucket);
                }
                $i++;
            }
            return $added;
        };

        // 3) Reparto por tour
        $tourOrder = $activeTourIds->shuffle()->values();
        foreach ($tourOrder as $tourId) {
            if ($picked->count() >= $TARGET_TOTAL) break;

            $goal  = $PER_TOUR_GOAL;
            $group = ($byTour->get((int)$tourId) ?? collect());

            // 3.1 Indexables primero
            $added = $addIndexableFromBuckets(
                $group->where('indexable', true)->values(),
                (int)$tourId,
                $goal
            );
            $goal -= $added;

            // 3.2 Relleno con iframes (rota proveedores no locales)
            if ($goal > 0) {
                foreach ($providersPref as $prov) {
                    if ($prov === 'local') continue;
                    if ($goal <= 0) break;
                    $pushIframe((int)$tourId, $prov);
                    $goal--;
                }
            }
        }

        // 4) Completar global con indexables sueltos
        if ($picked->count() < $TARGET_TOTAL) {
            foreach ($all->where('indexable', true)->shuffle() as $it) {
                if ($picked->count() >= $TARGET_TOTAL) break;
                $key = $makeSeenKey($it);
                if (!isset($seenKeys[$key])) {
                    $seenKeys[$key] = true;
                    $picked->push($it);
                }
            }
        }

        // 5) Adjuntar nombres de tour
        $picked = $this->attachTourNames($picked, $locale, $fallback);

        // Mezcla final
        return $picked->shuffle()->take($TARGET_TOTAL)->values();
    }

    /* ===========================
       PRIVADOS — SHOW TOUR
       ===========================*/

    private function loadTourWithTranslations(int $id, string $loc, string $fb): Tour
    {
        /** @var Tour $tour */
        $tour = Tour::with([
            'tourType.translations',
            'schedules' => function ($q) {
                $q->where('schedules.is_active', true)
                  ->wherePivot('is_active', true)
                  ->orderBy('schedules.start_time');
            },
            'languages' => function ($q) {
                $q->wherePivot('is_active', true)
                  ->where('tour_languages.is_active', true)
                  ->orderBy('name');
            },
            'itinerary.items.translations',
            'itinerary.translations',
            'amenities.translations',
            'excludedAmenities.translations',
            'translations',
        ])->findOrFail($id);

        $tr = $this->pickTranslation($tour->translations, $loc, $fb);
        $tour->translated_name     = $tr->name     ?? $tour->name;
        $tour->translated_overview = $tr->overview ?? $tour->overview;

        if ($tour->itinerary) {
            $itTr = $this->pickTranslation($tour->itinerary->translations, $loc, $fb);
            $tour->itinerary->translated_name        = $itTr->name        ?? $tour->itinerary->name;
            $tour->itinerary->translated_description = $itTr->description ?? $tour->itinerary->description;

            foreach ($tour->itinerary->items as $item) {
                $itemTr = $this->pickTranslation($item->translations, $loc, $fb);
                $item->translated_title       = $itemTr->title       ?? $item->title;
                $item->translated_description = $itemTr->description ?? $item->description;
            }
        }

        foreach ($tour->amenities as $amenity) {
            $amenityTr = $this->pickTranslation($amenity->translations, $loc, $fb);
            $amenity->translated_name = $amenityTr->name ?? $amenity->name;
        }

        foreach ($tour->excludedAmenities as $amenity) {
            $amenityTr = $this->pickTranslation($amenity->translations, $loc, $fb);
            $amenity->translated_name = $amenityTr->name ?? $amenity->name;
        }

        return $tour;
    }

    private function computeTourBlocks(Tour $tour): array
    {
        $visibleScheduleIds = $tour->schedules->pluck('schedule_id')->map(fn ($sid) => (int) $sid)->all();

        $blockedRows = TourExcludedDate::query()
            ->where('tour_id', $tour->tour_id)
            ->where(function ($query) use ($visibleScheduleIds) {
                $query->whereNull('schedule_id');
                if (!empty($visibleScheduleIds)) $query->orWhereIn('schedule_id', $visibleScheduleIds);
            })
            ->get(['schedule_id', 'start_date', 'end_date']);

        // Global blocks
        $blockedGeneral = [];
        foreach ($blockedRows->whereNull('schedule_id') as $row) {
            $startDate = Carbon::parse($row->start_date)->toDateString();
            $endDate   = $row->end_date ? Carbon::parse($row->end_date)->toDateString() : $startDate;
            foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
                $blockedGeneral[] = $date->toDateString();
            }
        }
        $blockedGeneral = array_values(array_unique($blockedGeneral));

        // Schedule-specific
        $blockedBySchedule = [];
        foreach ($blockedRows->whereNotNull('schedule_id') as $row) {
            $scheduleKey = (string) $row->schedule_id;
            $startDate   = Carbon::parse($row->start_date)->toDateString();
            $endDate     = $row->end_date ? Carbon::parse($row->end_date)->toDateString() : $startDate;
            foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
                $blockedBySchedule[$scheduleKey][] = $date->toDateString();
            }
        }
        foreach ($blockedBySchedule as $scheduleKey => $dates) {
            $blockedBySchedule[$scheduleKey] = array_values(array_unique($dates));
        }

        // Fully blocked
        $fullyBlockedDates = [];
        if (!empty($visibleScheduleIds)) {
            $visibleCount = count($visibleScheduleIds);
            $blocksPerDay = [];
            foreach ($blockedGeneral as $date) $blocksPerDay[$date] = ($blocksPerDay[$date] ?? 0) + $visibleCount;
            foreach ($blockedBySchedule as $dates) {
                foreach ($dates as $date) $blocksPerDay[$date] = ($blocksPerDay[$date] ?? 0) + 1;
            }
            foreach ($blocksPerDay as $date => $count) {
                if ($count >= $visibleCount) $fullyBlockedDates[] = $date;
            }
            $fullyBlockedDates = array_values(array_unique($fullyBlockedDates));
        }

        return [$blockedGeneral, $blockedBySchedule, $fullyBlockedDates];
    }

    private function loadMeetingPoints(bool $full = false): Collection
    {
        $base = MeetingPoint::active()->orderBy('sort_order')->orderBy('name');

        return $full
            ? $base->get(['id','name','pickup_time','address','map_url'])
            : $base->get(['id','name','pickup_time']);
    }

    /* ===========================
       HELPERS COMUNES
       ===========================*/

    private function pickTranslation($translations, string $locale, string $fallback)
    {
        $collection = $translations ?? collect();
        return $collection->firstWhere('locale', $locale)
            ?: $collection->firstWhere('locale', $fallback);
    }

    private function attachTourNames(Collection $items, string $locale, string $fallback): Collection
    {
        $ids = $items->pluck('tour_id')->filter()->unique()->values();
        if ($ids->isEmpty()) return $items;

        $tours = Tour::with('translations')->whereIn('tour_id', $ids)->get()->keyBy('tour_id');

        return $items->map(function ($r) use ($tours, $locale, $fallback) {
            if (!empty($r['tour_id']) && empty($r['tour_name'])) {
                $t = $tours->get((int) $r['tour_id']);
                if ($t) {
                    $tr = $this->pickTranslation($t->translations, $locale, $fallback);
                    $r['tour_name'] = $tr->name ?? $t->name ?? '';
                }
            }
            return $r;
        });
    }

    /* ===========================
       Contacto
       ===========================*/

    public function contact()
    {
        try {
            $localeMap = [
                'es' => 'es', 'es-CR' => 'es',
                'en' => 'en', 'en-US' => 'en', 'en-GB' => 'en',
                'fr' => 'fr', 'fr-FR' => 'fr',
                'pt' => 'pt', 'pt-PT' => 'pt', 'pt-BR' => 'pt-BR',
                'de' => 'de', 'de-DE' => 'de',
                'it' => 'it', 'nl' => 'nl', 'ru' => 'ru', 'ja' => 'ja',
                'zh' => 'zh-CN', 'zh-CN' => 'zh-CN', 'zh-TW' => 'zh-TW',
            ];

            $mapLang      = $localeMap[app()->getLocale()] ?? 'en';
            $placeName    = 'Agencia de Viajes Green Vacations CR';
            $placeAddr    = 'La Fortuna, San Carlos, Costa Rica';
            $centerLat    = 10.4556623;
            $centerLng    = -84.6532029;
            $encodedQuery = rawurlencode("{$placeName}, {$placeAddr}");

            $mapSrc = "https://maps.google.com/maps?hl={$mapLang}&gl=CR&q={$encodedQuery}&ll={$centerLat},{$centerLng}&z=16&iwloc=near&output=embed";

            return view('public.contact', compact('mapLang', 'mapSrc'));
        } catch (Throwable $e) {
            Log::error('contact.view.failed', ['error' => $e->getMessage()]);
            abort(500);
        }
    }

    public function sendContact(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'    => 'bail|required|string|min:2|max:100',
                'email'   => 'bail|required|email',
                'subject' => 'bail|required|string|min:3|max:150',
                'message' => 'bail|required|string|min:5|max:1000',
                'website' => 'nullable|string|max:50',
            ]);

            // Honeypot
            if (!empty(data_get($validated, 'website'))) {
                return back()->with('success', 'Your message has been sent.');
            }

            $recipient = config('mail.to.contact', config('mail.from.address', 'info@greenvacationscr.com'));
            Mail::to($recipient)->send(new ContactMessage($validated));

            return back()->with('success', 'Your message has been sent successfully. We will contact you soon.');
        } catch (Throwable $e) {
            Log::error('contact.send.failed', ['ip' => $request->ip(), 'error' => $e->getMessage()]);
            return back()->withInput()->withErrors([
                'email' => 'An error occurred while sending your message. Please try again in a few minutes.',
            ]);
        }
    }

    /** ===========================
     *  Reviews para página de tour (25 mezclando locales + iframes)
     *  Puedes mantener tu implementación existente si ya la tenías.
     *  Lo dejo aquí como recordatorio de que se llama desde showTour()
     *  ===========================*/
    private function buildTourReviews(ReviewAggregator $agg, int $tourId): Collection
    {
        $ttl       = 60 * 60 * 24;
        $targetTot = 25;

        $cacheKey = CacheKey::make('tour_reviews', [
            'tour' => $tourId,
            'loc'  => 'all',
            'lim'  => 500,
        ], 1);

        return Cache::remember($cacheKey, $ttl, function () use ($agg, $tourId, $targetTot) {
            $all = $agg->aggregate(['tour_id' => $tourId, 'limit' => 500]);

            $indexable = $all->where('indexable', true)->values();
            $nonIndex  = $all->where('indexable', false)->values();

            $takeIndexable = min($targetTot, $indexable->count());
            $picked = $indexable->shuffle()->take($takeIndexable)->values();

            $remaining = $targetTot - $picked->count();
            if ($remaining > 0) {
                $byProvider = $nonIndex
                    ->groupBy(fn ($r) => strtolower((string)($r['provider'] ?? 'viator')))
                    ->sortByDesc(fn ($g) => $g->count());

                $provKeys = $byProvider->keys()->values();
                $nthCursor = [];
                $pi = 0;

                while ($remaining > 0 && $provKeys->isNotEmpty()) {
                    $prov = $provKeys[$pi % $provKeys->count()];
                    $key  = $prov . ':' . $tourId;
                    $nth  = ($nthCursor[$key] ?? 0) + 1;
                    $nthCursor[$key] = $nth;

                    $picked->push([
                        'provider'     => $prov,
                        'indexable'    => false,
                        'iframe_limit' => 1,
                        'tour_id'      => $tourId,
                        'nth'          => $nth,
                    ]);

                    $remaining--;
                    $pi++;
                }
            }

            return $picked->shuffle()->values();
        });
    }
}
