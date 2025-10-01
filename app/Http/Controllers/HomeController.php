<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use App\Models\HotelList;
use App\Models\MeetingPoint;
use App\Models\Tour;
use App\Models\TourExcludedDate;
use App\Models\TourType;
use App\Services\Reviews\ReviewDistributor;
use App\Services\Reviews\ReviewsCacheManager;
use App\Services\Reviews\ReviewAggregator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Throwable;

class HomeController extends Controller
{
    public function index(ReviewDistributor $distributor, ReviewsCacheManager $cacheManager)
    {
        $currentLocale  = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'es');

        try {
            // 1) Meta de tipos + Tours
            $typeMeta = $this->loadTypeMeta($currentLocale, $fallbackLocale);
            $tours    = $this->loadActiveToursWithTranslations($currentLocale, $fallbackLocale);

            $toursByType = $tours
                ->sortBy('tour_type_id_group', SORT_NATURAL | SORT_FLAG_CASE)
                ->groupBy(fn ($tour) => $tour->tour_type_id_group);

            // 2) Reviews para HOME - delegado al ReviewDistributor
            $cacheKey = 'home_reviews:' . $cacheManager->getRevision();

            $homeReviews = Cache::remember($cacheKey, 86400, function () use ($distributor, $tours) {
                return $distributor->forHome($tours, perTour: 3, maxTotal: 24);
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

 public function showTour(int $id, ReviewAggregator $agg, ReviewsCacheManager $cacheManager)
{
    $loc = app()->getLocale();
    $fb  = config('app.fallback_locale', 'es');

    try {
        $tour = $this->loadTourWithTranslations($id, $loc, $fb);
        [$blockedGeneral, $blockedBySchedule, $fullyBlockedDates] = $this->computeTourBlocks($tour);

        // Obtener nombre del tour
        $tourName = $this->pickTranslation($tour->translations, $loc, $fb)->name ?? $tour->name ?? '';

        // NO CACHEAR TEMPORALMENTE PARA DEBUG
        // $cacheKey = "tour_reviews_pool:{$id}:" . $cacheManager->getRevision("tour.{$id}");

        // $tourReviews = Cache::remember($cacheKey, 86400, function () use ($agg, $id, $tourName) {
            // Traer reviews con límite generoso
            $allReviews = $agg->aggregate([
                'tour_id' => $id,
                'limit'   => 100,
            ]);

            // DEBUG: Ver qué reviews trae
            Log::info("Tour {$id} - Total reviews antes de filtrar: " . $allReviews->count());

            // Ver tour_ids de las reviews
            $tourIds = $allReviews->pluck('tour_id')->unique()->values();
            Log::info("Tour {$id} - Tour IDs encontrados: " . json_encode($tourIds->toArray()));

            // FILTRO ESTRICTO: solo reviews de este tour
            $filtered = $allReviews->filter(function($r) use ($id) {
                return (int)($r['tour_id'] ?? 0) === (int)$id;
            });

            Log::info("Tour {$id} - Reviews después de filtrar: " . $filtered->count());

            // Deduplicar
            $unique = $filtered->unique(function($r) {
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

            Log::info("Tour {$id} - Reviews después de deduplicar: " . $unique->count());

            // Forzar tour_name correcto en todas
            $tourReviews = $unique->map(function ($r) use ($tourName, $id) {
                $r['tour_name'] = $tourName;
                $r['tour_id'] = $id;
                return $r;
            })->values();
        // });

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
       PRIVADOS - HELPERS
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

    private function loadTourWithTranslations(int $id, string $loc, string $fb): Tour
    {
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

        $blockedGeneral = [];
        foreach ($blockedRows->whereNull('schedule_id') as $row) {
            $startDate = Carbon::parse($row->start_date)->toDateString();
            $endDate   = $row->end_date ? Carbon::parse($row->end_date)->toDateString() : $startDate;
            foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
                $blockedGeneral[] = $date->toDateString();
            }
        }
        $blockedGeneral = array_values(array_unique($blockedGeneral));

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

    private function pickTranslation($translations, string $locale, string $fallback)
    {
        $collection = $translations ?? collect();
        return $collection->firstWhere('locale', $locale)
            ?: $collection->firstWhere('locale', $fallback);
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
}
