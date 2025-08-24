<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use App\Models\HotelList;
use App\Models\Tour;
use App\Models\TourExcludedDate;
use App\Models\TourType;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    public function index()
    {
        $currentLocale  = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'es');

        // Tour type metadata with locale-aware fallbacks
        $typeMeta = TourType::active()
            ->with('translations')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($type) use ($currentLocale, $fallbackLocale) {
                $translation = $this->pickTranslation($type->translations, $currentLocale, $fallbackLocale);

                return [
                    $type->tour_type_id => [
                        'id'          => $type->tour_type_id,
                        'title'       => $translation->name ?? $type->name,
                        'duration'    => $translation->duration ?? $type->duration ?? '',
                        'description' => $translation->description ?? $type->description ?? '',
                    ],
                ];
            });

        // Active tours with locale-aware name/overview and grouped by type
        $tours = Tour::with(['tourType.translations', 'itinerary.items', 'translations'])
            ->where('is_active', true)
            ->get()
            ->map(function ($tour) use ($currentLocale, $fallbackLocale) {
                $translation = $this->pickTranslation($tour->translations, $currentLocale, $fallbackLocale);

                $tour->translated_name     = $translation->name ?? $tour->name;
                $tour->translated_overview = $translation->overview ?? $tour->overview;
                $tour->tour_type_id_group  = optional($tour->tourType)->tour_type_id ?? 'uncategorized';

                return $tour;
            });

        $toursByType = $tours
            ->sortBy('tour_type_id_group', SORT_NATURAL | SORT_FLAG_CASE)
            ->groupBy(fn ($tour) => $tour->tour_type_id_group);

        // Viator carousel products
        $viatorTours = Tour::with('translations')
            ->whereNotNull('viator_code')
            ->inRandomOrder()
            ->limit(6)
            ->get(['tour_id', 'viator_code', 'name']);

        $carouselProductCodes = $viatorTours->map(function ($tour) use ($currentLocale, $fallbackLocale) {
            $translation = $this->pickTranslation($tour->translations, $currentLocale, $fallbackLocale);

            return [
                'id'   => $tour->tour_id,
                'code' => $tour->viator_code,
                'name' => $translation->name ?? $tour->name ?? '',
            ];
        })->values();

        return view('public.home', compact('toursByType', 'typeMeta', 'carouselProductCodes'));
    }

    public function showTour(int $id)
    {
        $currentLocale  = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'es');

        $tour = Tour::with([
            'tourType.translations',
            'schedules' => function ($scheduleQuery) {
                $scheduleQuery->where('schedules.is_active', true)
                    ->wherePivot('is_active', true)
                    ->orderBy('schedules.start_time');
            },
            'languages' => function ($languageQuery) {
                $languageQuery->wherePivot('is_active', true)
                    ->where('tour_languages.is_active', true)
                    ->orderBy('name');
            },
            'itinerary.items.translations',
            'itinerary.translations',
            'amenities.translations',
            'excludedAmenities.translations',
            'translations',
        ])->findOrFail($id);

        // Tour main translation
        $tourTr = $this->pickTranslation($tour->translations, $currentLocale, $fallbackLocale);
        $tour->translated_name     = $tourTr->name     ?? $tour->name;
        $tour->translated_overview = $tourTr->overview ?? $tour->overview;

        // Itinerary and items
        if ($tour->itinerary) {
            $itTr = $this->pickTranslation($tour->itinerary->translations, $currentLocale, $fallbackLocale);
            $tour->itinerary->translated_name        = $itTr->name        ?? $tour->itinerary->name;
            $tour->itinerary->translated_description = $itTr->description ?? $tour->itinerary->description;

            foreach ($tour->itinerary->items as $item) {
                $itemTr = $this->pickTranslation($item->translations, $currentLocale, $fallbackLocale);
                $item->translated_title       = $itemTr->title       ?? $item->title;
                $item->translated_description = $itemTr->description ?? $item->description;
            }
        }

        // Amenities (included)
        foreach ($tour->amenities as $amenity) {
            $amenityTr = $this->pickTranslation($amenity->translations, $currentLocale, $fallbackLocale);
            $amenity->translated_name = $amenityTr->name ?? $amenity->name;
        }

        // Amenities (excluded)
        foreach ($tour->excludedAmenities as $amenity) {
            $amenityTr = $this->pickTranslation($amenity->translations, $currentLocale, $fallbackLocale);
            $amenity->translated_name = $amenityTr->name ?? $amenity->name;
        }

        // Date/schedule blocks (only for visible schedules)
        $visibleScheduleIds = $tour->schedules->pluck('schedule_id')->map(fn ($sid) => (int) $sid)->all();

        $blockedRows = TourExcludedDate::query()
            ->where('tour_id', $tour->tour_id)
            ->where(function ($query) use ($visibleScheduleIds) {
                $query->whereNull('schedule_id');
                if (!empty($visibleScheduleIds)) {
                    $query->orWhereIn('schedule_id', $visibleScheduleIds);
                }
            })
            ->get(['schedule_id', 'start_date', 'end_date']);

        // Global blocks (no schedule)
        $blockedGeneral = [];
        foreach ($blockedRows->whereNull('schedule_id') as $row) {
            $startDate = Carbon::parse($row->start_date)->toDateString();
            $endDate   = $row->end_date ? Carbon::parse($row->end_date)->toDateString() : $startDate;

            foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
                $blockedGeneral[] = $date->toDateString();
            }
        }
        $blockedGeneral = array_values(array_unique($blockedGeneral));

        // Schedule-specific blocks
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

        // Dates fully blocked (all visible schedules blocked on that day)
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
            $fullyBlockedDates = array_values(array_unique($fullyBlockedDates));
        }

        $hotels       = HotelList::orderBy('name')->get();
        $cancelPolicy = $tour->cancel_policy ?? null;
        $refundPolicy = $tour->refund_policy ?? null;

        return view('public.tour-show', compact(
            'tour',
            'hotels',
            'cancelPolicy',
            'refundPolicy',
            'blockedGeneral',
            'blockedBySchedule',
            'fullyBlockedDates'
        ));
    }

    public function contact()
    {
        // Map app locale to Google Maps `hl` param
        $localeMap = [
            'es' => 'es', 'es-CR' => 'es',
            'en' => 'en', 'en-US' => 'en', 'en-GB' => 'en',
            'fr' => 'fr', 'fr-FR' => 'fr',
            'pt' => 'pt', 'pt-PT' => 'pt', 'pt-BR' => 'pt-BR',
            'de' => 'de', 'de-DE' => 'de',
            'it' => 'it', 'nl' => 'nl', 'ru' => 'ru', 'ja' => 'ja',
            'zh' => 'zh-CN', 'zh-CN' => 'zh-CN', 'zh-TW' => 'zh-TW',
        ];

        $mapLang     = $localeMap[app()->getLocale()] ?? 'en';
        $placeName   = 'Agencia de Viajes Green Vacation';
        $placeAddr   = 'La Fortuna, San Carlos, Costa Rica';
        $centerLat   = 10.4556623;
        $centerLng   = -84.6532029;
        $encodedQuery = rawurlencode("{$placeName}, {$placeAddr}");

        $mapSrc = "https://maps.google.com/maps?hl={$mapLang}&gl=CR&q={$encodedQuery}"
                . "&ll={$centerLat},{$centerLng}&z=16&iwloc=near&output=embed";

        return view('public.contact', compact('mapLang', 'mapSrc'));
    }

    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'bail|required|string|min:2|max:100',
            'email'   => 'bail|required|email',
            'subject' => 'bail|required|string|min:3|max:150',
            'message' => 'bail|required|string|min:5|max:1000',
            'website' => 'nullable|string|max:50', // honeypot
        ]);

        // Honeypot: pretend success if filled
        if (!empty(data_get($validated, 'website'))) {
            return back()->with(
                'success',
                __('adminlte::adminlte.message_sent_spam_caught') ?? 'Your message has been sent.'
            );
        }

        try {
            $recipient = config('mail.to.contact', config('mail.from.address', 'info@greenvacationscr.com'));
            Mail::to($recipient)->send(new ContactMessage($validated));

            return back()->with(
                'success',
                __('adminlte::adminlte.contact_success') ?? 'Your message has been sent successfully. We will contact you soon.'
            );
        } catch (\Throwable $e) {
            Log::error('Contact form send error: '.$e->getMessage(), [
                'ip' => $request->ip(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'email' => __('adminlte::adminlte.contact_error')
                        ?? 'An error occurred while sending your message. Please try again in a few minutes.',
                ]);
        }
    }

    /**
     * Pick a translation for a locale, falling back when necessary.
     *
     * @param \Illuminate\Support\Collection|null $translations
     */
    private function pickTranslation($translations, string $locale, string $fallback)
    {
        $collection = $translations ?? collect();
        return $collection->firstWhere('locale', $locale)
            ?: $collection->firstWhere('locale', $fallback);
    }
}
