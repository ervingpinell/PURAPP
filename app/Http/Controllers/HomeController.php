<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use App\Models\HotelList;
use App\Models\MeetingPoint;
use App\Models\Tour;
use App\Models\TourExcludedDate;
use App\Models\TourType;
use App\Services\Bookings\BookingCapacityService;
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
use Throwable;

class HomeController extends Controller
{
    public function index(ReviewDistributor $distributor, ReviewsCacheManager $cacheManager)
    {
        $currentLocale  = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'es');

        try {
            // 1) Meta de tipos
            $typeMeta = $this->loadTypeMeta($currentLocale, $fallbackLocale);

            // 2) Cargar tours activos con traducciones y precios (categorías) + translations de categoría
            $tours = $this->loadActiveToursWithTranslations($currentLocale, $fallbackLocale)
                ->map(function ($tour) use ($currentLocale, $fallbackLocale) {
                    $tr = $this->pickTranslation($tour->translations, $currentLocale, $fallbackLocale);
                    $tour->translated_name = $tr->name ?? $tour->name;
                    return $tour;
                });

            // Agrupa conservando el orden
            $toursByType = $tours->groupBy(fn ($tour) => $tour->tour_type_id_group);

            // 3) Reviews para HOME usando ReviewDistributor
            $cacheKey = 'home_reviews:' . $currentLocale . ':' . $cacheManager->getRevision();
            $homeReviews = Cache::remember($cacheKey, 86400, function () use ($distributor, $tours) {
                return $distributor->forHome($tours, perTour: 3, maxTotal: 24);
            });

            // 4) Asegurar nombre traducido y slug en cada review
            $homeReviews = $homeReviews->map(function ($review) use ($tours, $currentLocale, $fallbackLocale) {
                $tourId = (int)($review['tour_id'] ?? 0);
                if ($tourId) {
                    $tour = $tours->firstWhere('tour_id', $tourId);
                    if ($tour) {
                        if (empty($tour->translated_name)) {
                            $tr = $this->pickTranslation($tour->translations, $currentLocale, $fallbackLocale);
                            $tour->translated_name = $tr->name ?? $tour->name;
                        }
                        $review['tour_name'] = $tour->translated_name ?? $tour->name;
                        $review['tour_slug'] = $tour->slug;
                    }
                }
                return $review;
            });

            // 5) Meeting Points
            $meetingPoints = MeetingPoint::active()
                ->with('translations')
                ->orderByRaw('sort_order IS NULL, sort_order ASC')
                ->orderBy('name')
                ->get();

            // 6) Hotels para el formulario de reserva
            $hotels = HotelList::where('is_active', true)
                ->orderBy('name')
                ->get();

            return view('public.home', compact(
                'toursByType',
                'typeMeta',
                'homeReviews',
                'meetingPoints',
                'hotels'
            ));

        } catch (Throwable $e) {
            Log::error('home.index.error', [
                'msg'  => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            return view('public.home', [
                'toursByType'   => collect(),
                'typeMeta'      => collect(),
                'homeReviews'   => collect(),
                'meetingPoints' => collect(),
                'hotels'        => collect(),
            ]);
        }
    }

    public function showTour(
        Tour $tour,
        ReviewAggregator $agg,
        ReviewsCacheManager $cacheManager,
        BookingCapacityService $capacityService
    ) {
        $loc = app()->getLocale();
        $fb  = config('app.fallback_locale', 'es');

        try {
            $tour->load([
                'tourType.translations',
                'schedules' => fn($q) => $q->where('schedules.is_active', true)
                    ->wherePivot('is_active', true)
                    ->orderBy('schedules.start_time'),
                'languages' => fn($q) => $q->wherePivot('is_active', true)
                    ->where('tour_languages.is_active', true)
                    ->orderBy('name'),
                // 👇 Aquí cargamos translations de la categoría
                'prices' => fn($q) => $q->where('is_active', true)
                    ->whereHas('category', fn($cq) => $cq->where('is_active', true))
                    ->with(['category.translations'])
                    ->orderBy('category_id'),
                'itinerary.items.translations',
                'itinerary.translations',
                'amenities.translations',
                'excludedAmenities.translations',
                'translations',
            ]);

            // Traducciones del tour
            $tr = $this->pickTranslation($tour->translations, $loc, $fb);
            $tour->translated_name     = $tr->name ?? $tour->name;
            $tour->translated_overview = $tr->overview ?? $tour->overview;

            // Traducciones de itinerario
            if ($tour->itinerary) {
                $itTr = $this->pickTranslation($tour->itinerary->translations, $loc, $fb);
                $tour->itinerary->translated_name        = $itTr->name ?? $tour->itinerary->name;
                $tour->itinerary->translated_description = $itTr->description ?? $tour->itinerary->description;

                foreach ($tour->itinerary->items as $item) {
                    $itemTr = $this->pickTranslation($item->translations, $loc, $fb);
                    $item->translated_title       = $itemTr->title ?? $item->title;
                    $item->translated_description = $itemTr->description ?? $item->description;
                }
            }

            // Traducciones de amenidades
            foreach ($tour->amenities as $amenity) {
                $amenityTr = $this->pickTranslation($amenity->translations, $loc, $fb);
                $amenity->translated_name = $amenityTr->name ?? $amenity->name;
            }

            foreach ($tour->excludedAmenities as $amenity) {
                $amenityTr = $this->pickTranslation($amenity->translations, $loc, $fb);
                $amenity->translated_name = $amenityTr->name ?? $amenity->name;
            }

            // [1] Fechas bloqueadas
            [$blockedGeneral, $blockedBySchedule, $fullyBlockedDates] = $this->computeTourBlocks($tour);

            // [2] Fechas llenas por capacidad usando el servicio
            $capacityDisabled = [];
            $start = Carbon::today();
            $end   = Carbon::today()->addDays(90);

            foreach ($tour->schedules as $schedule) {
                $fullDates = [];
                $period = CarbonPeriod::create($start, $end);
                foreach ($period as $date) {
                    $dateStr = $date->toDateString();
                    $snap = $capacityService->capacitySnapshot(
                        $tour,
                        $schedule,
                        $dateStr,
                        excludeBookingId: null,
                        countHolds: false
                    );
                    if ($snap['blocked'] || $snap['available'] <= 0) {
                        $fullDates[] = $dateStr;
                    }
                }
                $capacityDisabled[(string) $schedule->schedule_id] = $fullDates;
            }

            foreach ($capacityDisabled as $sid => $dates) {
                $blockedBySchedule[$sid] = array_values(array_unique(array_merge(
                    $blockedBySchedule[$sid] ?? [],
                    $dates
                )));
            }

            // [3] Reviews con caché
            $tourName = $tour->translated_name;
            $tourId   = $tour->tour_id;
            $cacheKey = "tour_reviews_pool:{$tourId}:" . $cacheManager->getRevision("tour.{$tourId}");

            $tourReviews = Cache::remember($cacheKey, 86400, function () use ($agg, $tourId, $tourName) {
                return $agg->aggregate(['tour_id' => $tourId, 'limit' => 100])
                    ->filter(fn($r) => (int)($r['tour_id'] ?? 0) === (int)$tourId)
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
                    })
                    ->map(fn($r) => array_merge($r, ['tour_name' => $tourName, 'tour_id' => $tourId]))
                    ->values();
            });

            return view('public.tour-show', [
                'tour'               => $tour,
                'blockedGeneral'     => $blockedGeneral,
                'blockedBySchedule'  => $blockedBySchedule,
                'fullyBlockedDates'  => $fullyBlockedDates,
                'capacityDisabled'   => $capacityDisabled,
                'tourReviews'        => $tourReviews,
                'hotels'             => HotelList::where('is_active', true)->orderBy('name')->get(),
                'cancelPolicy'       => $tour->cancel_policy ?? null,
                'refundPolicy'       => $tour->refund_policy ?? null,
                'meetingPoints'      => $this->loadMeetingPoints(true),
            ]);

        } catch (Throwable $e) {
            Log::error('tour.show.failed', [
                'tour_id' => $tour->tour_id ?? 'unknown',
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            abort(404);
        }
    }

    /* ===========================
       HELPERS PRIVADOS
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
        return Tour::query()
            ->with([
                'tourType:tour_type_id,name',
                'tourType.translations',
                'translations',
                'coverImage',
                // 👇 Aquí también cargamos translations de la categoría
                'prices' => function($q) {
                    $q->where('is_active', true)
                      ->whereHas('category', fn($cq) => $cq->where('is_active', true))
                      ->with(['category.translations'])
                      ->orderBy('category_id');
                }
            ])
            ->leftJoin('tour_type_tour_order as o', function ($join) {
                $join->on('o.tour_id', '=', 'tours.tour_id')
                     ->on('o.tour_type_id', '=', 'tours.tour_type_id');
            })
            ->where('tours.is_active', true)
            ->orderBy('tours.tour_type_id')
            ->orderByRaw('CASE WHEN o.position IS NULL THEN 1 ELSE 0 END')
            ->orderBy('o.position')
            ->orderBy('tours.name')
            ->get([
                'tours.tour_id',
                'tours.name',
                'tours.slug',
                'tours.tour_type_id',
                'tours.length',
                'tours.max_capacity',
            ])
            ->map(function ($tour) use ($loc, $fb) {
                $tr = $this->pickTranslation($tour->translations, $loc, $fb);
                $tour->translated_name     = $tr->name ?? $tour->name;
                $tour->translated_overview = $tr->overview ?? $tour->overview;
                $tour->tour_type_id_group  = optional($tour->tourType)->tour_type_id ?? 'uncategorized';

                // Filtrar precios activos con categorías activas
                $activePrices = $tour->prices->filter(function($price) {
                    return $price->is_active &&
                           $price->category &&
                           $price->category->is_active;
                });

                // Precio mínimo para mostrar en listados
                $tour->min_price = $activePrices->min('price') ?? 0;

                // Legacy: buscar adult/kid por slug para compatibilidad
                $adultPrice = $activePrices->first(function($p) {
                    $slug = $p->category->slug ?? '';
                    return in_array($slug, ['adult', 'adulto', 'adults']);
                });

                $kidPrice = $activePrices->first(function($p) {
                    $slug = $p->category->slug ?? '';
                    return in_array($slug, ['kid', 'nino', 'child', 'kids', 'children']);
                });

                $tour->setAttribute('preview_adult_price', $adultPrice ? (float)$adultPrice->price : $tour->min_price);
                $tour->setAttribute('preview_kid_price',   $kidPrice   ? (float)$kidPrice->price   : null);

                return $tour;
            });
    }

    private function computeTourBlocks(Tour $tour): array
    {
        $visibleScheduleIds = $tour->schedules->pluck('schedule_id')->map(fn($sid) => (int)$sid)->all();

        $blockedRows = TourExcludedDate::where('tour_id', $tour->tour_id)
            ->where(function ($q) use ($visibleScheduleIds) {
                $q->whereNull('schedule_id');
                if (!empty($visibleScheduleIds)) {
                    $q->orWhereIn('schedule_id', $visibleScheduleIds);
                }
            })
            ->get(['schedule_id', 'start_date', 'end_date']);

        $blockedGeneral = [];
        foreach ($blockedRows->whereNull('schedule_id') as $row) {
            $start = Carbon::parse($row->start_date)->toDateString();
            $end   = $row->end_date ? Carbon::parse($row->end_date)->toDateString() : $start;
            foreach (CarbonPeriod::create($start, $end) as $date) {
                $blockedGeneral[] = $date->toDateString();
            }
        }
        $blockedGeneral = array_values(array_unique($blockedGeneral));

        $blockedBySchedule = [];
        foreach ($blockedRows->whereNotNull('schedule_id') as $row) {
            $scheduleKey = (string)$row->schedule_id;
            $start = Carbon::parse($row->start_date)->toDateString();
            $end   = $row->end_date ? Carbon::parse($row->end_date)->toDateString() : $start;
            foreach (CarbonPeriod::create($start, $end) as $date) {
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

            foreach ($blockedGeneral as $date) {
                $blocksPerDay[$date] = ($blocksPerDay[$date] ?? 0) + $visibleCount;
            }

            foreach ($blockedBySchedule as $dates) {
                foreach ($dates as $date) {
                    $blocksPerDay[$date] = ($blocksPerDay[$date] ?? 0) + 1;
                }
            }

            foreach ($blocksPerDay as $date => $count) {
                if ($count >= $visibleCount) {
                    $fullyBlockedDates[] = $date;
                }
            }
        }

        return [$blockedGeneral, $blockedBySchedule, array_values(array_unique($fullyBlockedDates))];
    }

    private function loadMeetingPoints(bool $full = false): Collection
    {
        $base = MeetingPoint::active()
            ->with('translations')
            ->orderByRaw('sort_order IS NULL, sort_order ASC')
            ->orderBy('name');

        return $full
            ? $base->get(['id', 'name', 'pickup_time', 'description', 'map_url'])
            : $base->get(['id', 'name', 'pickup_time', 'description']);
    }

    private function pickTranslation($translations, string $locale, string $fallback)
    {
        $collection = $translations ?? collect();
        return $collection->firstWhere('locale', $locale)
            ?: $collection->firstWhere('locale', $fallback);
    }

    /* ===========================
       CONTACTO
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
                'zh' => 'zh-CN', 'zh-TW' => 'zh-TW',
            ];

            $mapLang = $localeMap[app()->getLocale()] ?? 'en';
            $mapSrc = sprintf(
                "https://maps.google.com/maps?hl=%s&gl=CR&q=%s&ll=10.4556623,-84.6532029&z=16&iwloc=near&output=embed",
                $mapLang,
                rawurlencode('Agencia de Viajes Green Vacations CR, La Fortuna, San Carlos, Costa Rica')
            );

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

            if (!empty($validated['website'])) {
                return back()->with('success', 'Your message has been sent.');
            }

            $recipient = config('mail.to.contact', config('mail.from.address', 'info@greenvacationscr.com'));
            Mail::to($recipient)->queue(new ContactMessage($validated));

            return back()->with('success', 'Your message has been sent successfully. We will contact you soon.');
        } catch (Throwable $e) {
            Log::error('contact.send.failed', ['ip' => $request->ip(), 'error' => $e->getMessage()]);
            return back()->withInput()->withErrors([
                'email' => 'An error occurred while sending your message. Please try again in a few minutes.',
            ]);
        }
    }
}
