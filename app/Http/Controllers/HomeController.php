<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\HotelList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\TourType;
use App\Models\TourExcludedDate;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use App\Mail\ContactMessage;


class HomeController extends Controller
{
    public function index()
    {
        $locale   = app()->getLocale();
        $fallback = config('app.fallback_locale', 'es');

        // 1) Tipos de tour con traducciones -> meta para UI
        $typeMeta = TourType::active()
            ->with('translations') // si no hay traducciones aún, la colección vendrá vacía
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($type) use ($locale, $fallback) {
                $tr = ($type->translations ?? collect())->firstWhere('locale', $locale)
                    ?: ($type->translations ?? collect())->firstWhere('locale', $fallback);

                return [
                    $type->tour_type_id => [
                        'id'          => $type->tour_type_id,
                        'title'       => $tr->name ?? $type->name,                 // fallback al original (ES)
                        'duration'    => $tr->duration ?? $type->duration ?? '',
                        'description' => $tr->description ?? $type->description ?? '',
                    ],
                ];
            });

        // 2) Tours con traducciones y tipo (con fallback al original)
        $tours = Tour::with(['tourType.translations', 'itinerary.items', 'translations'])
            ->where('is_active', true)
            ->get()
            ->map(function ($tour) use ($locale, $fallback) {
                $tTr = ($tour->translations ?? collect())->firstWhere('locale', $locale)
                    ?: ($tour->translations ?? collect())->firstWhere('locale', $fallback);

                $tour->translated_name     = $tTr->name ?? $tour->name;
                $tour->translated_overview = $tTr->overview ?? $tour->overview;

                $tour->tour_type_id_group  = optional($tour->tourType)->tour_type_id ?? 'sin_categoria';

                return $tour;
            });

        // 3) Agrupar por id de tipo
        $toursByType = $tours
            ->sortBy('tour_type_id_group', SORT_NATURAL | SORT_FLAG_CASE)
            ->groupBy(fn ($t) => $t->tour_type_id_group);

        // 4) Carrusel Viator — hoy usa nombre original (ES); mañana usará traducción si existe
        $viatorTours = Tour::with('translations')
            ->whereNotNull('viator_code')
            ->inRandomOrder()
            ->limit(6)
            ->get(['tour_id', 'viator_code', 'name']);

        $carouselProductCodes = $viatorTours->map(function ($t) use ($locale, $fallback) {
            $tr = ($t->translations ?? collect())->firstWhere('locale', $locale)
                ?: ($t->translations ?? collect())->firstWhere('locale', $fallback);

            return [
                'id'   => $t->tour_id,
                'code' => $t->viator_code,
                'name' => $tr->name ?? $t->name ?? '',   // nunca null
            ];
        })->values();

        return view('public.home', compact('toursByType', 'typeMeta', 'carouselProductCodes'));
    }


    public function showTour($id)
    {
        $locale   = app()->getLocale();
        $fallback = config('app.fallback_locale', 'es');

        $tour = Tour::with([
            'tourType.translations',
            // horarios visibles: global + pivote
            'schedules' => function ($q) {
                $q->where('schedules.is_active', true)
                  ->wherePivot('is_active', true)
                  ->orderBy('schedules.start_time');
            },
            // idiomas activos
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

        // Traducciones
        $t = ($tour->translations ?? collect())->firstWhere('locale', $locale)
           ?: ($tour->translations ?? collect())->firstWhere('locale', $fallback);

        $tour->translated_name     = $t->name     ?? $tour->name;
        $tour->translated_overview = $t->overview ?? $tour->overview;

        if ($tour->itinerary) {
            $it = ($tour->itinerary->translations ?? collect())->firstWhere('locale', $locale)
               ?: ($tour->itinerary->translations ?? collect())->firstWhere('locale', $fallback);

            $tour->itinerary->translated_name        = $it->name        ?? $tour->itinerary->name;
            $tour->itinerary->translated_description = $it->description ?? $tour->itinerary->description;

            foreach ($tour->itinerary->items as $item) {
                $itT = ($item->translations ?? collect())->firstWhere('locale', $locale)
                    ?: ($item->translations ?? collect())->firstWhere('locale', $fallback);
                $item->translated_title       = $itT->title       ?? $item->title;
                $item->translated_description = $itT->description ?? $item->description;
            }
        }

        foreach ($tour->amenities as $a) {
            $ta = ($a->translations ?? collect())->firstWhere('locale', $locale)
                ?: ($a->translations ?? collect())->firstWhere('locale', $fallback);
            $a->translated_name = $ta->name ?? $a->name;
        }

        foreach ($tour->excludedAmenities as $e) {
            $te = ($e->translations ?? collect())->firstWhere('locale', $locale)
                ?: ($e->translations ?? collect())->firstWhere('locale', $fallback);
            $e->translated_name = $te->name ?? $e->name;
        }

        // =========================
        // Bloqueos de fechas/horarios
        // =========================
        $visibleScheduleIds = $tour->schedules->pluck('schedule_id')->map(fn($v)=>(int)$v)->all();

        // Trae SOLO bloqueos globales o de los horarios visibles
        $blocked = TourExcludedDate::query()
            ->where('tour_id', $tour->tour_id)
            ->where(function ($q) use ($visibleScheduleIds) {
                $q->whereNull('schedule_id');
                if (!empty($visibleScheduleIds)) {
                    $q->orWhereIn('schedule_id', $visibleScheduleIds);
                }
            })
            ->get(['schedule_id','start_date','end_date']);

        // Globales (sin horario)
        $blockedGeneral = [];
        foreach ($blocked->whereNull('schedule_id') as $row) {
            $start = Carbon::parse($row->start_date)->toImmutable();
            $end   = $row->end_date ? Carbon::parse($row->end_date)->toImmutable() : $start;
            for ($d=$start; $d->lte($end); $d=$d->addDay()) {
                $blockedGeneral[] = $d->toDateString();
            }
        }
        $blockedGeneral = array_values(array_unique($blockedGeneral));

        // Por horario (solo visibles)
        $blockedBySchedule = [];
        foreach ($blocked->whereNotNull('schedule_id') as $row) {
            $sid   = (string) $row->schedule_id;
            $start = Carbon::parse($row->start_date)->toImmutable();
            $end   = $row->end_date ? Carbon::parse($row->end_date)->toImmutable() : $start;
            for ($d=$start; $d->lte($end); $d=$d->addDay()) {
                $blockedBySchedule[$sid][] = $d->toDateString();
            }
        }
        foreach ($blockedBySchedule as $sid => $dates) {
            $blockedBySchedule[$sid] = array_values(array_unique($dates));
        }

        // Días totalmente bloqueados (todos los horarios visibles bloqueados ese día)
        $fullyBlockedDates = [];
        if (!empty($visibleScheduleIds)) {
            $total = count($visibleScheduleIds);
            // cuenta por día
            $countByDate = [];

            // globales suman "total" directamente (todos los horarios)
            foreach ($blockedGeneral as $date) {
                $countByDate[$date] = ($countByDate[$date] ?? 0) + $total;
            }
            // específicos suman de a 1
            foreach ($blockedBySchedule as $sid => $dates) {
                foreach ($dates as $date) {
                    $countByDate[$date] = ($countByDate[$date] ?? 0) + 1;
                }
            }

            foreach ($countByDate as $date => $cnt) {
                if ($cnt >= $total) {
                    $fullyBlockedDates[] = $date;
                }
            }
            $fullyBlockedDates = array_values(array_unique($fullyBlockedDates));
        }

        // Datos extra para la vista
        $hotels = HotelList::orderBy('name')->get();
        $cancel = $tour->cancel_policy ?? null;
        $refund = $tour->refund_policy ?? null;
        return view('public.tour-show', compact(
            'tour',
            'hotels',
            'cancel',
            'refund',
            'blockedGeneral',
            'blockedBySchedule',
            'fullyBlockedDates'

        ));
    }




    public function contact()
    {
        return view('public.contact');
    }

public function sendContact(Request $request)
{
    // Validación (un poco más estricta y con bail)
    $validated = $request->validate([
        'name'    => 'bail|required|string|min:2|max:100',
        'email'   => 'bail|required|email',
        'subject' => 'bail|required|string|min:3|max:150',
        'message' => 'bail|required|string|min:5|max:1000',
        'website' => 'nullable|string|max:50', // honeypot
    ]);

    // Honeypot: si tiene contenido, tratar como enviado (sin mandar nada)
    if (!empty(data_get($validated, 'website'))) {
        return back()->with('success', __('adminlte::adminlte.message_sent_spam_caught')
            ?? 'Tu mensaje ha sido enviado.');
    }

    try {
        $to = config('mail.to.contact', config('mail.from.address', 'info@greenvacationscr.com'));

        Mail::to($to)->send(new ContactMessage($validated));
        // Si usas cola:
        // Mail::to($to)->queue(new ContactMessage($validated));

        return back()->with('success', __('adminlte::adminlte.contact_success')
            ?? 'Tu mensaje ha sido enviado con éxito. Pronto te contactaremos.');
    } catch (\Throwable $e) {
        // En producción puedes evitar el trace completo para reducir PII
        Log::error('Error enviando contacto: '.$e->getMessage(), [
            'ip' => $request->ip(),
            // 'trace' => $e->getTraceAsString(), // opcional en local
        ]);

        return back()
            ->withInput()
            ->withErrors(['email' => __('adminlte::adminlte.contact_error')
                ?? 'Ocurrió un error al enviar tu mensaje. Intenta de nuevo en unos minutos.']);
    }
}

}
