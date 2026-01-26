<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use App\Models\HotelList;
use App\Models\MeetingPoint;
use App\Models\Product;
use App\Models\TourExcludedDate;
use App\Models\TourType;
use App\Models\CustomerCategory;
use App\Services\Bookings\BookingCapacityService;
use App\Services\Reviews\ReviewDistributor;
use App\Services\Reviews\ReviewsCacheManager;
use App\Services\Reviews\ReviewAggregator;
use App\Services\DeepLTranslator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * HomeController
 *
 * Handles homepage display and public tour listings.
 */
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
            $toursByType = $tours->groupBy(fn($tour) => $tour->tour_type_id_group);

            // 3) Reviews para HOME usando ReviewDistributor
            $cacheKey = 'home_reviews:' . $currentLocale . ':' . $cacheManager->getRevision();
            $homeReviews = Cache::remember($cacheKey, 86400, function () use ($distributor, $tours) {
                return $distributor->forHome($tours, perTour: 3, maxTotal: 24);
            });

            // 4) Asegurar nombre traducido y slug en cada review
            $homeReviews = $homeReviews->map(function ($review) use ($tours, $currentLocale, $fallbackLocale) {
                $tourId = (int)($review['product_id'] ?? 0);
                if ($tourId) {
                    $tour = $tours->firstWhere('product_id', $tourId);
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
                ->orderByRaw('sort_order IS NULL, sort_order ASC')
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

    public function showProduct(
        Product $product,
        ReviewAggregator $agg,
        ReviewsCacheManager $cacheManager,
        BookingCapacityService $capacityService
    ) {
        $loc = app()->getLocale();
        $fb  = config('app.fallback_locale', 'es');

        try {
            $product->load([
                'tourType.translations',
                'images',
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
            $tourId   = $tour->product_id;
            $cacheKey = "tour_reviews_pool:{$tourId}:" . $cacheManager->getRevision("tour.{$tourId}");

            $tourReviews = Cache::remember($cacheKey, 86400, function () use ($agg, $tourId, $tourName) {
                return $agg->aggregate(['product_id' => $tourId, 'limit' => 100])
                    ->filter(fn($r) => (int)($r['product_id'] ?? 0) === (int)$tourId)
                    ->unique(function ($r) {
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
                    ->map(fn($r) => array_merge($r, ['tour_name' => $tourName, 'product_id' => $tourId]))
                    ->values();
            });

            // [4] Datos para el formulario de reserva (Refactorizado)
            $reservationData = $this->prepareReservationData($product);

            return view('public.product-show', array_merge([
                'tour'               => $product, // Keep 'tour' key for view compatibility for now, or rename in view
                'blockedGeneral'     => $blockedGeneral,
                'blockedBySchedule'  => $blockedBySchedule,
                'fullyBlockedDates'  => $fullyBlockedDates,
                'capacityDisabled'   => $capacityDisabled,
                'tourReviews'        => $tourReviews,
                'hotels'             => HotelList::where('is_active', true)->orderBy('name')->get(),
                'cancelPolicy'       => $tour->cancel_policy ?? null,
                'refundPolicy'       => $tour->refund_policy ?? null,
                'meetingPoints'      => $this->loadMeetingPoints(true),
            ], $reservationData));
        } catch (Throwable $e) {
            Log::error('tour.show.failed', [
                'product_id' => $tour->product_id ?? 'unknown',
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            abort(404);
        }
    }

    public function allProducts(Request $request)
    {
        $loc = app()->getLocale();
        $fb  = config('app.fallback_locale', 'es');

        // =========================
        // QUERY DE TOURS
        // =========================
        $query = Product::query()
            ->active() // scopeActive: is_active = true & is_draft = false
            ->with([
                'coverImage',
                'prices.category.translations',
                'itinerary.items.translations',
                'tourType.translations',
            ]);

        // Filtro por texto
        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($q) use ($search) {
                // Search in name column (JSON)
                $locale = app()->getLocale();
                $q->whereRaw("name->>'$locale' ILIKE ?", ["%{$search}%"])
                  ->orWhere('name', 'ilike', "%{$search}%"); // Fallback check entire JSON string
            });
        }

        // 🟢 Filtro por categoría de TOUR (tour_type_id)
        if ($typeId = $request->input('category')) {
            $query->where('tour_type_id', (int) $typeId);
        }

        $tours = $query
            ->leftJoin('tour_type_tour_order as o', function ($join) {
                $join->on('o.product_id', '=', 'tours.product_id')
                    ->on('o.tour_type_id', '=', 'tours.tour_type_id');
            })
            ->orderBy('tours.tour_type_id')
            ->orderByRaw('CASE WHEN o.position IS NULL THEN 1 ELSE 0 END')
            ->orderBy('o.position')
            ->orderBy('tours.name')
            ->paginate(12)
            ->withQueryString()
            ->through(function ($tour) use ($loc, $fb) {
                $tr = $this->pickTranslation($tour->translations, $loc, $fb);
                $tour->translated_name = $tr->name ?? $tour->name;
                return $tour;
            });

        // =========================
        // CATEGORÍAS PARA EL SELECT
        // =========================
        $categories = TourType::query()
            ->where('is_active', true)
            ->get()
            ->map(function (TourType $type) use ($loc, $fb) {
                $tr = $type->translations
                    ->firstWhere('locale', $loc)
                    ?? $type->translations->firstWhere('locale', $fb);

                $type->translated_name = $tr->name ?? $type->name;
                return $type;
            })
            ->sortBy('translated_name')
            ->values();

        return view('public.products.index', [
            'tours'      => $tours,
            'categories' => $categories,
        ]);
    }

    /* ===========================
       HELPERS PRIVADOS
       ===========================*/

    private function loadTypeMeta(string $loc, string $fb): Collection
    {
        $cacheKey = "home_type_meta:{$loc}";
        return Cache::remember($cacheKey, 3600, function () use ($loc, $fb) {
            return TourType::active()
                ->withTranslation()
                ->get()
                ->map(function ($type) use ($loc, $fb) {
                    $tr = $this->pickTranslation($type->translations, $loc, $fb);
                    return [
                        'id'          => $type->tour_type_id,
                        'title'       => $tr->name ?? '',
                        'duration'    => $tr->duration ?? '',
                        'description' => $tr->description ?? '',
                        'cover_url'   => $type->cover_url,
                    ];
                })
                ->sortBy('title')
                ->keyBy('id');
        });
    }

    private function loadActiveToursWithTranslations(string $loc, string $fb): Collection
    {
        $cacheKey = "home_active_tours:{$loc}";
        return Cache::remember($cacheKey, 3600, function () use ($loc, $fb) {
            return Product::query()
                ->with([
                    'tourType:tour_type_id',
                    'tourType.translations',
                    'coverImage',
                    // 👇 Aquí también cargamos translations de la categoría
                    'prices' => function ($q) {
                        $q->where('is_active', true)
                            ->whereHas('category', fn($cq) => $cq->where('is_active', true))
                            ->with(['category.translations'])
                            ->orderBy('category_id');
                    }
                ])
                ->leftJoin('tour_type_tour_order as o', function ($join) {
                    $join->on('o.product_id', '=', 'tours.product_id')
                        ->on('o.tour_type_id', '=', 'tours.tour_type_id');
                })
                ->where('tours.is_active', true)
                ->orderBy('tours.tour_type_id')
                ->orderByRaw('CASE WHEN o.position IS NULL THEN 1 ELSE 0 END')
                ->orderBy('o.position')
                ->orderBy('tours.name')
                ->get([
                    'tours.product_id',
                    'tours.name',
                    'tours.slug',
                    'tours.tour_type_id',
                    'tours.length',
                    'tours.max_capacity',
                    'tours.overview', // Needed for optimization
                ])
                ->map(function ($tour) use ($loc, $fb) {
                    $tr = $this->pickTranslation($tour->translations, $loc, $fb);
                    $tour->translated_name     = $tr->name ?? $tour->name;
                    $tour->translated_overview = $tr->overview ?? $tour->overview;
                    $tour->tour_type_id_group  = optional($tour->tourType)->tour_type_id ?? 'uncategorized';

                    // Filtrar precios activos con categorías activas
                    $activePrices = $tour->prices->filter(function ($price) {
                        return $price->is_active &&
                            $price->category &&
                            $price->category->is_active;
                    });

                    // Precio mínimo para mostrar en listados
                    $tour->min_price = $activePrices->min('price') ?? 0;

                    // Legacy: buscar adult/kid por slug para compatibilidad
                    $adultPrice = $activePrices->first(function ($p) {
                        $slug = $p->category->slug ?? '';
                        return in_array($slug, ['adult', 'adulto', 'adults']);
                    });

                    $kidPrice = $activePrices->first(function ($p) {
                        $slug = $p->category->slug ?? '';
                        return in_array($slug, ['kid', 'nino', 'child', 'kids', 'children']);
                    });

                    $tour->setAttribute('preview_adult_price', $adultPrice ? (float)$adultPrice->price : $tour->min_price);
                    $tour->setAttribute('preview_kid_price',   $kidPrice   ? (float)$kidPrice->price   : null);

                    return $tour;
                });
        });
    }

    private function computeTourBlocks(Product $tour): array
    {
        $visibleScheduleIds = $tour->schedules->pluck('schedule_id')->map(fn($sid) => (int)$sid)->all();

        $blockedRows = TourExcludedDate::where('product_id', $tour->product_id)
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
            ->orderByRaw('sort_order IS NULL, sort_order ASC');

        return $full
            ? $base->get(['id', 'pickup_time', 'map_url'])
            : $base->get(['id', 'pickup_time']);
    }

    private function pickTranslation($translations, string $locale, string $fallback)
    {
        $collection = $translations ?? collect();
        return $collection->firstWhere('locale', $locale)
            ?: $collection->firstWhere('locale', $fallback);
    }

    /**
     * Prepara los datos necesarios para el formulario de reserva (precios, categorías, traducciones)
     * Refactorizado desde las vistas travelers.blade.php y fields.blade.php
     */
    private function prepareReservationData(Product $tour): array
    {
        // 1. Configuración de ventana de reserva
        $maxFutureDays = (int) setting('booking.max_future_days', config('booking.max_days_advance', 730));

        // 2. Obtener precios activos y categorías
        $allPrices = $tour->prices()
            ->where('is_active', true)
            ->whereHas('category', fn($q) => $q->where('is_active', true))
            ->with('category.translations')
            ->orderBy('category_id')
            ->get();

        // 3. Preparar indicadores de precios para el calendario
        $priceIndicators = [];
        foreach ($allPrices as $price) {
            if ($price->valid_from && $price->valid_until) {
                $priceIndicators[] = [
                    'from' => $price->valid_from->format('Y-m-d'),
                    'until' => $price->valid_until->format('Y-m-d'),
                    'price' => (float) $price->price,
                ];
            }
        }

        // Calcular niveles de precios (lower/higher)
        $allPricesValues = collect($priceIndicators)->pluck('price')->filter(fn($p) => $p > 0);
        if ($allPricesValues->count() > 0) {
            $avgPrice = $allPricesValues->average();

            $priceIndicators = collect($priceIndicators)->map(function ($indicator) use ($avgPrice) {
                if ($indicator['price'] <= 0) {
                    $indicator['level'] = 'normal';
                    return $indicator;
                }

                $diff = (($indicator['price'] - $avgPrice) / $avgPrice) * 100;

                if ($diff < -10) {
                    $indicator['level'] = 'lower'; // 10%+ más barato
                } elseif ($diff > 10) {
                    $indicator['level'] = 'higher'; // 10%+ más caro
                } else {
                    $indicator['level'] = 'normal';
                }

                return $indicator;
            })->toArray();
        } else {
            $priceIndicators = [];
        }

        // 4. Preparar datos de categorías para JS (travelers.blade.php logic)
        $groupedCategories = $allPrices->groupBy('category_id');
        $maxPersonsGlobal = (int) config('booking.max_persons_per_booking', 12);
        $minAdultsGlobal  = (int) config('booking.min_adults_per_booking', 0);
        $maxKidsGlobal    = PHP_INT_MAX;

        $categoriesData = [];
        $loc = app()->getLocale();
        $fb  = config('app.fallback_locale', 'es');

        foreach ($groupedCategories as $catId => $prices) {
            $firstPrice = $prices->first();
            $category   = $firstPrice->category;
            $slug       = $category->slug ?? strtolower($category->name ?? '');

            // Traducción del nombre de la categoría (usando helper robusto del modelo)
            $catName = $category->getTranslatedName($loc);

            // Reglas de precios crudas desde DB
            $priceRules = $prices->map(function ($price) {
                return [
                    'price'       => (float) $price->price,
                    'min'         => (int) $price->min_quantity,
                    'max'         => (int) $price->max_quantity,
                    'valid_from'  => $price->valid_from ? $price->valid_from->format('Y-m-d') : null,
                    'valid_until' => $price->valid_until ? $price->valid_until->format('Y-m-d') : null,
                    'is_default'  => is_null($price->valid_from) && is_null($price->valid_until),
                ];
            })->values();

            // Aplicar restricciones globales a CADA regla
            $priceRules = $priceRules->map(function (array $rule) use (
                $slug,
                $minAdultsGlobal,
                $maxKidsGlobal,
                $maxPersonsGlobal
            ) {
                if (in_array($slug, ['adult', 'adulto', 'adults'], true)) {
                    $rule['min'] = max($rule['min'], $minAdultsGlobal);
                } elseif (in_array($slug, ['kid', 'nino', 'child', 'kids', 'children'], true)) {
                    $rule['max'] = min($rule['max'], $maxKidsGlobal);
                }

                // Nunca más que el máximo global por reserva
                $rule['max'] = min($rule['max'], $maxPersonsGlobal);

                // Evitar casos raros min > max
                if ($rule['max'] < $rule['min']) {
                    $rule['max'] = $rule['min'];
                }

                return $rule;
            });

            // Elegir regla default DESPUÉS de ajustar los límites
            $defaultRule = $priceRules->firstWhere('is_default', true) ?? $priceRules->first();

            $min = $defaultRule['min'];
            $max = $defaultRule['max'];

            // Inicial por categoría (ej: 2 adultos)
            if (in_array($slug, ['adult', 'adulto', 'adults'], true)) {
                $min = max($min, $minAdultsGlobal);
            } elseif (in_array($slug, ['kid', 'nino', 'child', 'kids', 'children'], true)) {
                $max = min($max, $maxKidsGlobal);
            }

            $max     = min($max, $maxPersonsGlobal);
            $initial = $min; // Use the actual minimum from the category instead of hardcoded value

            // Texto de rango de edad
            $ageMin       = $category->age_from;
            $ageMax       = $category->age_to;
            $ageRangeText = null;
            if ($ageMin !== null && $ageMax !== null) {
                $ageRangeText = __('m_bookings.travelers.age_between', ['min' => $ageMin, 'max' => $ageMax]);
            } elseif ($ageMin !== null) {
                $ageRangeText = __('m_bookings.travelers.age_from', ['min' => $ageMin]);
            } elseif ($ageMax !== null) {
                $ageRangeText = __('m_bookings.travelers.age_to', ['max' => $ageMax]);
            }

            $categoriesData[] = [
                'id'        => (int) $catId,
                'name'      => $catName,
                'slug'      => $slug,
                'price'     => (float) $defaultRule['price'],
                'min'       => $min,
                'max'       => $max,
                'initial'   => $initial,
                'age_text'  => $ageRangeText,
                'rules'     => $priceRules,
            ];
        }


        // 5. Calcular reglas de fechas (Cutoff & Lead Days)
        $tz = config('app.timezone', 'America/Costa_Rica');
        $gCutoff = (string) setting('booking.cutoff_hour', config('booking.cutoff_hour', '18:00'));
        $gLead = (int) setting('booking.lead_days', (int) config('booking.lead_days', 1));

        $calc = function (string $cutoff, int $lead) use ($tz) {
            $now = Carbon::now($tz);
            [$hh, $mm] = array_pad(explode(':', $cutoff, 2), 2, '00');
            $cutoffToday = Carbon::create($now->year, $now->month, $now->day, (int)$hh, (int)$mm, 0, $tz);
            $passed = $now->gte($cutoffToday);
            $days = max(0, (int)$lead) + ($passed ? 1 : 0);
            return [
                'cutoff' => sprintf('%02d:%02d', (int)$hh, (int)$mm),
                'lead_days' => (int)$lead,
                'after_cutoff' => $passed,
                'min' => $now->copy()->addDays($days)->toDateString(),
            ];
        };

        $tCutoff = $tour->cutoff_hour ?: $gCutoff;
        $tLead = is_null($tour->lead_days) ? $gLead : (int) $tour->lead_days;
        $tourRule = $calc($tCutoff, $tLead);

        $scheduleRules = [];
        foreach ($tour->schedules->sortBy('start_time') as $s) {
            $pCut = optional($s->pivot)->cutoff_hour;
            $pLd = optional($s->pivot)->lead_days;
            $sCut = $pCut ?: $tCutoff;
            $sLd = is_null($pLd) ? $tLead : (int)$pLd;
            $scheduleRules[$s->schedule_id] = $calc($sCut, $sLd);
        }

        $mins = array_map(fn($r) => $r['min'], $scheduleRules);
        $mins[] = $tourRule['min'];
        $initialMin = min($mins);

        $rulesPayload = [
            'tz' => $tz,
            'tour' => $tourRule,
            'schedules' => $scheduleRules,
            'initialMin' => $initialMin,
        ];

        // 6. Traducciones para el calendario
        $calendarTranslations = [
            'price_lower' => __('m_tours.tour.pricing.price_lower'),
            'price_higher' => __('m_tours.tour.pricing.price_higher'),
            'price_normal' => __('m_tours.tour.pricing.price_normal'),
            'price_legend' => __('m_tours.tour.pricing.price_legend'),
        ];

        // 7. Textos i18n para viajeros
        $travI18n = [
            'title_warning' => __('m_bookings.travelers.title_warning'),
            'title_info' => __('m_bookings.travelers.title_info'),
            'title_error' => __('m_bookings.travelers.title_error'),
            'max_persons_reached' => __('m_bookings.travelers.max_persons_reached'),
            'max_category_reached' => __('m_bookings.travelers.max_category_reached'),
            'invalid_quantity' => __('m_bookings.travelers.invalid_quantity'),
            'price_not_available' => __('m_tours.tour.pricing.not_available_for_date') ?? 'No disponible para esta fecha',
            // Validaciones submit
            'min_category_required' => __('adminlte::adminlte.min_category_required'),
            'max_category_exceeded' => __('adminlte::adminlte.max_category_exceeded'),
            'max_persons_exceeded' => __('adminlte::adminlte.max_persons_exceeded'),
            'min_one_person' => __('adminlte::adminlte.min_one_person'),
        ];

        return [
            'maxFutureDays' => $maxFutureDays,
            'priceIndicators' => $priceIndicators,
            'categoriesData' => $categoriesData,
            'calendarTranslations' => $calendarTranslations,
            'travI18n' => $travI18n,
            'maxPersonsGlobal' => $maxPersonsGlobal,
            'rulesPayload' => $rulesPayload,
        ];
    }

    /* ===========================
       CONTACTO
       ===========================*/

    public function contact()
    {
        try {
            $localeMap = [
                'es' => 'es',
                'es-CR' => 'es',
                'en' => 'en',
                'en-US' => 'en',
                'en-GB' => 'en',
                'fr' => 'fr',
                'fr-FR' => 'fr',
                'pt' => 'pt',
                'pt-PT' => 'pt',
                'pt-BR' => 'pt-BR',
                'de' => 'de',
                'de-DE' => 'de',
                'it' => 'it',
                'nl' => 'nl',
                'ru' => 'ru',
                'ja' => 'ja',
                'zh' => 'zh-CN',
                'zh-TW' => 'zh-TW',
            ];

            $mapLang = $localeMap[app()->getLocale()] ?? 'en';
            
            // Si hay API key, podríamos usar Embed API, pero el usuario confirmó NO tener key.
            // Usamos legacy iframe con coordenadas, que suele ser más permisivo.
            // Formato: https://maps.google.com/maps?q=LAT,LNG&z=15&output=embed
            $lat = config('company.map.latitude', '10.455753');
            $lng = config('company.map.longitude', '-84.653104');
            
            // Ahora que tenemos CSP corregido, intentamos mostrar el negocio por nombre
            // para que aparezca la "Place Card" de Google.
            // Generamos el query dinámicamente desde la configuración.
            $companyName = config('company.name', 'Company Name');
            $city        = config('company.address.city', 'Costa Rica');
            
            $query = rawurlencode("{$companyName}, {$city}");
            
            $mapSrc = sprintf(
                "https://maps.google.com/maps?q=%s&hl=%s&z=16&output=embed",
                $query,
                $mapLang
            );

            // Time Trap Token (Anti-bot)
            $timeToken = encrypt(time());

            return view('public.contact', compact('mapLang', 'mapSrc', 'timeToken'));
        } catch (Throwable $e) {
            Log::error('contact.view.failed', ['error' => $e->getMessage()]);
            abort(500);
        }
    }

    public function sendContact(Request $request, DeepLTranslator $translator)
    {
        try {
            // 0) Time Trap Validation (Anti-bot)
            // ✅ Timing Validation implemented correctly here.
            // Bots usually submit immediately. Humans take at least 3-5 seconds.
            if ($request->has('_t')) {
                try {
                    $timestamp = decrypt($request->input('_t'));
                    if (time() - $timestamp < 3) {
                        // Too fast (< 3s), likely a bot. Return fake success.
                        return back()->with('success', __('adminlte::adminlte.contact_spam_success'));
                    }
                } catch (\Exception $e) {
                    // Invalid token. Treat as bot.
                    return back()->with('success', __('adminlte::adminlte.contact_spam_success'));
                }
            } else {
                // Missing token. Likely a direct POST bot.
                // We allow it to proceed to validation for legacy/cached page support OR fail it.
            }

            // 1) Validación inicial, incluyendo Turnstile check presence
            $validated = $request->validate([
                'name'    => 'bail|required|string|min:2|max:100',
                'email'   => 'bail|required|email',
                'subject' => 'bail|required|string|min:3|max:150',
                'message' => 'bail|required|string|min:5|max:1000',
                'website' => 'nullable|string|max:50',
                // Turnstile genera este campo automáticamente (si está activo en frontend)
                'cf-turnstile-response' => 'nullable|string',
            ]);

            // 2) Honeypot: simulamos éxito pero no hacemos nada
            // ✅ Honeypot is functioning correctly.
            if (!empty($validated['website'])) {
                return back()->with(
                    'success',
                    __('adminlte::adminlte.contact_spam_success')
                );
            }

            // 3) Verificar Turnstile con Cloudflare
            // ✅ Server-Side Validation: explicitly verifying with Cloudflare API
            $secret = config('services.turnstile.secret_key');

            if ($secret) {
                if (empty($request->input('cf-turnstile-response'))) {
                    return back()
                        ->withInput($request->except('website'))
                        ->withErrors([
                            'cf-turnstile-response' => __('adminlte::adminlte.bot_detection_failed'),
                        ]);
                }

                $verifyResponse = \Illuminate\Support\Facades\Http::asForm()->post(
                    'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                    [
                        'secret'   => $secret,
                        'response' => $request->input('cf-turnstile-response'),
                        'remoteip' => $request->ip(),
                    ]
                );

                if (!$verifyResponse->ok() || !$verifyResponse->json('success')) {
                    Log::warning('contact.turnstile.failed', [
                        'ip'      => $request->ip(),
                        'payload' => $verifyResponse->json(),
                    ]);

                    return back()
                        ->withInput($request->except('website'))
                        ->withErrors([
                            'cf-turnstile-response' => __('adminlte::adminlte.bot_detection_failed'),
                        ]);
                }
            }

            // 4) Detección de Idioma del Mensaje (Smart Detection)
            $userLocale = app()->getLocale();
            try {
                // Detectamos el idioma del contenido del mensaje
                $detected = $translator->detect($validated['message']);
                if ($detected && in_array($detected, ['es', 'en', 'fr', 'pt', 'de'])) {
                    $userLocale = $detected;
                }
            } catch (\Throwable $e) {
                // Fallback silencioso al locale de la app si falla Detección
                Log::warning('contact.lang_detect.failed', ['msg' => $e->getMessage()]);
            }

            // 5) Enviar correo
            $recipient = env('MAIL_TO_CONTACT', config('mail.from.address', 'info@greenvacationscr.com'));

            Mail::to($recipient)->queue(
                new ContactMessage($validated + ['locale' => $userLocale])
            );

            return back()->with(
                'success',
                __('adminlte::adminlte.contact_success')
            );
        } catch (Throwable $e) {
            Log::error('contact.send.failed', [
                'ip'    => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'email' => __('adminlte::adminlte.contact_error'),
            ]);
        }
    }
    
    /**
     * Products by category (dynamic routes)
     */
    public function productsByCategory(Request $request)
    {
        $category = $request->route('category');
        $loc = app()->getLocale();
        $fb  = config('app.fallback_locale', 'es');
        
        // Get category config
        $categoryConfig = \App\Helpers\ProductCategoryHelper::getCategoryConfig($category);
        if (!$categoryConfig) {
            abort(404);
        }
        
        // Query products
        $query = Product::query()
            ->active()
            ->with([
                'productType',
                'coverImage',
                'prices.category.translations',
            ]);
        
        // Filter by search
        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($q) use ($search, $loc) {
                $q->whereRaw("name->>'$loc' ILIKE ?", ["%{$search}%"])
                  ->orWhere('name', 'ilike', "%{$search}%");
            });
        }
        
        // Filter by subcategory if provided
        if ($subcategory = $request->input('subcategory')) {
            $query->where('subcategory', $subcategory);
        }
        
        $products = $query
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString()
            ->through(function ($product) use ($loc, $fb) {
                $tr = $this->pickTranslation($product->translations, $loc, $fb);
                $product->translated_name = $tr->name ?? $product->name;
                return $product;
            });
        
        return view('public.products.index', [
            'products' => $products,
            'category' => $category,
            'categoryConfig' => $categoryConfig,
        ]);
    }
    
    /**
     * Products by subcategory (landing pages)
     */
    public function productsBySubcategory(Request $request, string $subcategory)
    {
        $category = $request->route('category');
        $loc = app()->getLocale();
        $fb  = config('app.fallback_locale', 'es');
        
        // Validate category and subcategory exist
        if (!\App\Helpers\ProductCategoryHelper::subcategoryExists($category, $subcategory)) {
            abort(404);
        }
        
        $categoryConfig = \App\Helpers\ProductCategoryHelper::getCategoryConfig($category);
        $subcategoryConfig = \App\Helpers\ProductCategoryHelper::getSubcategoryConfig($category, $subcategory);
        
        // Query products
        $products = Product::query()
            ->active()
            ->with([
                'productType',
                'coverImage',
                'prices.category.translations',
            ])
            ->where('subcategory', $subcategory)
            ->orderBy('name')
            ->paginate(12)
            ->through(function ($product) use ($loc, $fb) {
                $tr = $this->pickTranslation($product->translations, $loc, $fb);
                $product->translated_name = $tr->name ?? $product->name;
                return $product;
            });
        
        // Use same view as category, but with subcategory data
        return view('public.products.index', [
            'products' => $products,
            'category' => $category,
            'subcategory' => $subcategory,
            'categoryConfig' => $categoryConfig,
            'subcategoryConfig' => $subcategoryConfig,
        ]);
    }
}
