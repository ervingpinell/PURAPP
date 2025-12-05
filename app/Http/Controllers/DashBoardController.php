<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\Tour;
use App\Models\TourLanguage;
use App\Models\TourType;
use App\Models\User;
use App\Services\Bookings\BookingCapacityService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashBoardController extends Controller
{
    public function index(): View
    {
        return view('index');
    }

    public function switchLanguage(Request $request, string $language)
    {
        $supported = array_keys(config('routes.locales', ['es' => []]));
        $default   = config('routes.default_locale', 'es');

        if (! in_array($language, $supported, true)) {
            $language = $default;
        }

        $prefix   = $language;
        $internal = $prefix === 'pt' ? 'pt' : $prefix;

        session([
            'locale'        => $internal,
            'locale_prefix' => $prefix,
        ]);

        app()->setLocale($internal);

        $prev   = (string) ($request->headers->get('referer') ?: url()->previous() ?: $request->fullUrl());
        $parsed = parse_url($prev) ?: [];
        $path   = $parsed['path'] ?? '/';
        $query  = isset($parsed['query']) ? ('?' . $parsed['query']) : '';

        $segments     = array_values(array_filter(explode('/', $path)));
        $pathNoLocale = $path;
        if (!empty($segments) && in_array($segments[0], $supported, true)) {
            $pathNoLocale = '/' . implode('/', array_slice($segments, 1));
            if ($pathNoLocale === '/') {
                $pathNoLocale = '';
            }
        }

        $unlocalized = [
            'login',
            'register',
            'password',
            'password-reset',
            'email',
            'verify',
            'auth',
            'two-factor-challenge',
            'two-factor',
            'unlock-account',
            'account',
            'admin',
            'profile',
            'my-bookings',
            'my-cart',
            'checkout',
            'payment',
            'dashboard',
            'pay',
        ];

        $first = ltrim($pathNoLocale, '/');
        $first = $first !== '' ? strtok($first, '/') : '';

        if ($first !== '' && in_array($first, $unlocalized, true)) {
            return redirect()->to(($pathNoLocale === '' ? '/' : $pathNoLocale) . $query);
        }

        if ($pathNoLocale === '' || $pathNoLocale === '/') {
            return redirect()->to('/' . $prefix);
        }

        return redirect()->to('/' . $prefix . $pathNoLocale . $query);
    }

    public function dashboard(BookingCapacityService $capacity): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role_id, [1, 2], true)) {
            return redirect()
                ->route('login')
                ->with('error', __('adminlte::adminlte.access_denied'));
        }

        // ===== Métricas básicas =====
        $totalUsers          = User::count();
        $totalTours          = Tour::count();
        $totalRoles          = Role::count();
        $totalTourTypes      = TourType::count();
        $totalLanguages      = TourLanguage::count();
        $totalSchedules      = Schedule::count();
        $totalAmenities      = Amenity::count();
        $totalItineraryItems = ItineraryItem::count();
        $totalBookings       = Booking::count();

        $itineraries = Itinerary::with('items')->get();

        // Próximas reservas (mañana)
        $tz        = config('app.timezone', 'UTC');
        $tomorrow  = Carbon::now($tz)->addDay()->toDateString();
        $tomorrowC = Carbon::now($tz)->addDay();

        $upcomingBookings = Booking::with(['user', 'detail.tour'])
            ->whereHas('detail', fn($q) => $q->whereDate('tour_date', $tomorrow))
            ->orderBy('booking_date')
            ->get();

        // ===== Alertas de capacidad (próximos 30 días) =====
        $start = Carbon::now($tz)->toDateString();
        $end   = Carbon::now($tz)->addDays(30)->toDateString();

        // ✅ Traemos TODO: booking completo + detail con categories
        $details = BookingDetail::with([
            'booking', // ✅ Traer toda la info del booking
            'tour:tour_id,name',
            'schedule:schedule_id,start_time',
        ])
            ->whereHas('booking', fn($q) => $q->whereIn('status', ['confirmed', 'paid']))
            ->whereDate('tour_date', '>=', $start)
            ->whereDate('tour_date', '<=', $end)
            ->get(); // ✅ Traer TODO, no solo campos específicos

        // Pre-caches de Tour y Schedule para evitar N+1
        $tourCache     = Tour::whereIn('tour_id', $details->pluck('tour_id')->unique())->get()->keyBy('tour_id');
        $scheduleCache = Schedule::whereIn('schedule_id', $details->pluck('schedule_id')->unique())->get()->keyBy('schedule_id');

        // ✅ LOG temporal para debug
        Log::info('[DASHBOARD] Processing capacity alerts', [
            'total_details' => $details->count(),
            'date_range' => [$start, $end],
        ]);

        // Agrupación por llave (tour|schedule|date) y suma de pax
        $buckets = [];
        foreach ($details as $d) {
            $date = Carbon::parse($d->tour_date, $tz)->toDateString();
            $key  = $d->tour_id . '|' . $d->schedule_id . '|' . $date;

            if (!isset($buckets[$key])) {
                $buckets[$key] = [
                    'tour_id'     => (int) $d->tour_id,
                    'schedule_id' => (int) $d->schedule_id,
                    'date'        => $date,
                    'tour_name'   => optional($d->tour)->name ?? '—',
                    'used'        => 0,
                    'detail_ids'  => [], // ✅ Para debug
                ];
            }

            // ✅ SUMAR categorías del JSON con mejor manejo
            $cats = $d->categories;

            // Si es string, decodificar
            if (is_string($cats)) {
                $cats = json_decode($cats, true);
            }

            // ✅ LOG temporal para ver qué se está sumando
            $paxThisDetail = 0;

            if (is_array($cats) && !empty($cats)) {
                foreach ($cats as $cat) {
                    $qty = (int) ($cat['quantity'] ?? 0);
                    $paxThisDetail += $qty;
                }
            }

            $buckets[$key]['used'] += $paxThisDetail;
            $buckets[$key]['detail_ids'][] = $d->booking_detail_id; // ✅ Para debug

            // ✅ LOG cada detalle procesado (temporal, remover después)
            Log::debug('[DASHBOARD] Detail processed', [
                'booking_detail_id' => $d->booking_detail_id,
                'booking_id' => $d->booking_id,
                'key' => $key,
                'categories_raw' => $d->categories,
                'pax_added' => $paxThisDetail,
                'bucket_total' => $buckets[$key]['used'],
            ]);
        }

        // ✅ LOG final de buckets
        Log::info('[DASHBOARD] Capacity buckets summary', [
            'total_buckets' => count($buckets),
            'buckets' => array_map(fn($b) => [
                'key' => $b['tour_id'] . '|' . $b['schedule_id'] . '|' . $b['date'],
                'tour' => $b['tour_name'],
                'used' => $b['used'],
                'detail_count' => count($b['detail_ids']),
            ], $buckets),
        ]);

        // Construir alertas consultando la CAPACIDAD EFECTIVA
        $alerts = collect();
        foreach ($buckets as $g) {
            $tour     = $tourCache->get($g['tour_id']);
            $schedule = $scheduleCache->get($g['schedule_id']);
            if (!$tour || !$schedule) {
                continue;
            }

            // Snapshot con lógica centralizada
            $snap = $capacity->capacitySnapshot($tour, $schedule, $g['date']);

            // Usados confirmados + capacidad máxima vigente
            $used = (int) $g['used'];
            $max  = (int) $snap['max'];

            // Si está bloqueado, disponibilidad 0
            $remaining = $snap['blocked'] ? 0 : max(0, $max - $used - (int) $snap['held']);
            $pct = $max > 0 ? (int) floor(($used * 100) / $max) : 0;

            // ✅ FILTRO: Solo alertas con ocupación >= 50%
            if ($pct < 50 && !$snap['blocked']) {
                continue;
            }

            $type = $remaining === 0
                ? 'sold_out'
                : (($remaining <= 3 || $pct >= 80) ? 'near_capacity' : 'info');

            $alerts->push([
                'key'         => (string) ($g['tour_id'] . '|' . $g['schedule_id'] . '|' . $g['date']),
                'tour_id'     => (int) $g['tour_id'],
                'schedule_id' => (int) $g['schedule_id'],
                'date'        => (string) $g['date'],
                'tour'        => (string) $g['tour_name'],
                'used'        => $used,
                'max'         => $max,
                'remaining'   => $remaining,
                'pct'         => $pct,
                'type'        => $type,
            ]);
        }

        // Orden por fecha y nombre de tour
        $alerts = $alerts->sortBy([
            ['date', 'asc'],
            ['tour', 'asc'],
        ])->values();

        $criticalCount = $alerts->whereIn('type', ['near_capacity', 'sold_out'])->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalTours',
            'totalRoles',
            'totalTourTypes',
            'totalLanguages',
            'totalSchedules',
            'totalAmenities',
            'totalItineraryItems',
            'totalBookings',
            'itineraries',
            'upcomingBookings',
            'tomorrowC',
        ))->with('capAlerts', $alerts)
            ->with('capCritical', $criticalCount);
    }
}
