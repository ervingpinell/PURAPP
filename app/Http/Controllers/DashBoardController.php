<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Booking;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\Tour;
use App\Models\TourLanguage;
use App\Models\TourType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $query  = isset($parsed['query']) ? ('?'.$parsed['query']) : '';

        $segments     = array_values(array_filter(explode('/', $path)));
        $pathNoLocale = $path;
        if (!empty($segments) && in_array($segments[0], $supported, true)) {
            $pathNoLocale = '/' . implode('/', array_slice($segments, 1));
            if ($pathNoLocale === '/') { $pathNoLocale = ''; }
        }

        $unlocalized = [
            'login','register','password','password-reset','email','verify',
            'auth','two-factor-challenge','two-factor','unlock-account','account',
            'admin',
            'profile','my-bookings','my-cart',
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

    public function dashboard(): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role_id, [1, 2], true)) {
            return redirect()
                ->route('login')
                ->with('error', __('adminlte::adminlte.access_denied'));
        }

        // Métricas
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
            ->whereHas('detail', fn ($q) => $q->whereDate('tour_date', $tomorrow))
            ->orderBy('booking_date')
            ->get();

        // ========= Alertas de capacidad por FECHA (respetando overrides) =========
        $start = Carbon::now($tz)->toDateString();
        $end   = Carbon::now($tz)->addDays(30)->toDateString();

        // Sumatoria por (schedule_id, fecha) y capacidad efectiva (override o base)
        $rows = DB::table('booking_details as d')
            ->join('bookings as b', 'b.booking_id', '=', 'd.booking_id')
            ->join('schedules as s', 's.schedule_id', '=', 'd.schedule_id')
            ->leftJoin('tours as t', 't.tour_id', '=', 'd.tour_id')
            ->leftJoin('schedule_capacity_overrides as o', function ($j) {
                $j->on('o.schedule_id', '=', 'd.schedule_id')
                  ->on(DB::raw('o.date'), '=', DB::raw('DATE(d.tour_date)'));
            })
            ->whereDate('d.tour_date', '>=', $start)
            ->whereDate('d.tour_date', '<=', $end)
            ->whereIn('b.status', ['confirmed', 'paid'])
            ->groupBy('d.schedule_id', DB::raw('DATE(d.tour_date)'), 't.name', 's.max_capacity', 'o.max_capacity')
            ->select([
                'd.schedule_id',
                DB::raw('DATE(d.tour_date) as date'),
                't.name as tour',
                DB::raw('SUM(COALESCE(d.adults_quantity,0)+COALESCE(d.kids_quantity,0)) as used'),
                DB::raw('COALESCE(o.max_capacity, s.max_capacity) as eff_max'),
            ])
            ->get();

        $alerts = $rows->map(function ($r) {
            $max = (int) $r->eff_max;
            $used = (int) $r->used;
            $remaining = max(0, $max - $used);
            $pct = $max > 0 ? (int) floor(($used * 100) / $max) : 0;

            $type = $remaining === 0
                ? 'sold_out'
                : (($remaining <= 3 || $pct >= 80) ? 'near_capacity' : 'info');

            return [
                'key'         => (string) ($r->schedule_id . '-' . $r->date), // clave única p/ cache local
                'schedule_id' => (int) $r->schedule_id,
                'date'        => (string) $r->date,
                'tour'        => $r->tour ?? '—',
                'used'        => $used,
                'max'         => $max,
                'remaining'   => $remaining,
                'pct'         => $pct,
                'type'        => $type,
            ];
        })->sortBy(['date','tour'])->values();

        $criticalCount = $alerts->whereIn('type', ['near_capacity','sold_out'])->count();

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
