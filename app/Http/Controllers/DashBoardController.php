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
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


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

        // Prefix (2 letters) and internal "locale"
        $prefix   = $language;                 // es, en, pt, ...
        $internal = $prefix === 'pt' ? 'pt' : $prefix; // map if needed

        // Save BOTH values
        session([
            'locale'        => $internal,  // used by views, etc.
            'locale_prefix' => $prefix,    // used by SetLocale when no /{locale} in URL
        ]);

        app()->setLocale($internal);

        // Return to where user was, WITHOUT adding prefix to non-localized routes
        $prev = (string) ($request->headers->get('referer') ?: url()->previous() ?: $request->fullUrl());
        $parsed = parse_url($prev) ?: [];
        $path   = $parsed['path'] ?? '/';
        $query  = isset($parsed['query']) ? ('?'.$parsed['query']) : '';

        // Remove locale prefix if exists
        $segments = array_values(array_filter(explode('/', $path)));
        $pathNoLocale = $path;
        if (!empty($segments) && in_array($segments[0], $supported, true)) {
            $pathNoLocale = '/' . implode('/', array_slice($segments, 1));
            if ($pathNoLocale === '/') { $pathNoLocale = ''; }
        }

        // Routes without localization (don't add /es, /en, ...)
        $unlocalized = [
            'login','register','password','password-reset','email','verify',
            'auth','two-factor-challenge','two-factor','unlock-account','account',
            'admin', // admin is not localized
            'profile','my-bookings','my-cart',
        ];

        $first = ltrim($pathNoLocale, '/');
        $first = $first !== '' ? strtok($first, '/') : '';

        if ($first !== '' && in_array($first, $unlocalized, true)) {
            // return as-is, without prefix
            return redirect()->to(($pathNoLocale === '' ? '/' : $pathNoLocale) . $query);
        }

        // If public root → redirect to language home
        if ($pathNoLocale === '' || $pathNoLocale === '/') {
            return redirect()->to('/' . $prefix);
        }

        // Localized public routes → prepend chosen language prefix
        return redirect()->to('/' . $prefix . $pathNoLocale . $query);
    }


    private function isAdminUrl(string $url): bool
    {
        $parsed = parse_url($url);
        $path   = $parsed['path'] ?? '/';

        $adminPrefixes = [
            '/admin',
            '/profile',
            '/my-bookings',
            '/my-cart',
        ];

        foreach ($adminPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function replaceLocaleInUrl(string $url, string $newLocale): string
    {
        $locales = array_keys(config('routes.locales', []));
        $parsed  = parse_url($url);
        $path    = $parsed['path'] ?? '/';
        $query   = isset($parsed['query']) ? '?' . $parsed['query'] : '';

        foreach ($locales as $locale) {
            if (str_starts_with($path, "/{$locale}/")) {
                $path = substr($path, strlen("/{$locale}"));
                break;
            } elseif ($path === "/{$locale}") {
                $path = '/';
                break;
            }
        }

        $newPath = "/{$newLocale}" . $path;

        return rtrim(config('app.url'), '/') . $newPath . $query;
    }

    public function dashboard(): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role_id, [1, 2], true)) {
            return redirect()
                ->route('login')
                ->with('error', __('adminlte::adminlte.access_denied'));
        }

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

        $upcomingBookings = Booking::with(['user', 'detail.tour'])
            ->whereHas('detail', fn ($query) => $query->where('tour_date', '>=', today()))
            ->orderBy('booking_date')
            ->take(5)
            ->get();

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
            'upcomingBookings'
        ));
    }
}
