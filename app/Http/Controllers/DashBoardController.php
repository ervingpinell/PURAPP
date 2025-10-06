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

    session(['locale' => $language]);
    app()->setLocale($language);

    $prev = url()->previous();                       // p.ej. http://127.0.0.1:8000/es/tours
    $path = parse_url($prev, PHP_URL_PATH) ?? '/';   // p.ej. /es/tours

    // Quita locale actual si viene con prefijo
    $segments = array_values(array_filter(explode('/', $path)));
    $pathNoLocale = $path;

    if (!empty($segments) && in_array($segments[0], $supported, true)) {
        // /{locale}/resto -> /resto
        $pathNoLocale = '/' . implode('/', array_slice($segments, 1));
        if ($pathNoLocale === '/') { $pathNoLocale = ''; } // home
    }

    // Rutas que NO están localizadas
    $unlocalized = [
        'login', 'register', 'password', 'account', 'unlock-account',
        'email', 'auth', 'admin' // admin tampoco lo localizas
    ];

    $first = ltrim($pathNoLocale, '/');
    $first = strtok($first, '/'); // primer segmento

    // Si es una ruta no localizada -> volver sin prefijo
    if ($first !== false && in_array($first, $unlocalized, true)) {
        return redirect($pathNoLocale === '' ? '/' : $pathNoLocale);
    }

    // Si es raíz
    if ($pathNoLocale === '' || $pathNoLocale === '/') {
        return redirect('/' . $language);
    }

    // Resto: aplicar nuevo prefijo de idioma
    return redirect('/' . $language . $pathNoLocale);
}

    private function isAdminUrl(string $url): bool
    {
        $parsed = parse_url($url);
        $path   = $parsed['path'] ?? '/';

        $adminPrefixes = [
            '/admin',
            '/profile',
            '/my-reservations',
            '/my-cart',
            '/mi-carrito',
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
