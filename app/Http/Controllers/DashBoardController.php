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

    // ⬇️  Prefijo (2 letras) y "locale" interno
    $prefix   = $language;                 // es, en, pt, ...
    $internal = $prefix === 'pt' ? 'pt' : $prefix; // aquí mapeas si hiciera falta

    // ⬇️  ¡Clave! guarda AMBAS cosas
    session([
        'locale'        => $internal,  // usado por views, etc.
        'locale_prefix' => $prefix,    // usado por SetLocale cuando NO hay /{locale} en la URL
    ]);

    app()->setLocale($internal);

    // --- Volver a donde estaba el usuario, SIN meter prefijo en rutas no-localizadas ---
    $prev = (string) ($request->headers->get('referer') ?: url()->previous() ?: $request->fullUrl());
    $parsed = parse_url($prev) ?: [];
    $path   = $parsed['path'] ?? '/';
    $query  = isset($parsed['query']) ? ('?'.$parsed['query']) : '';

    // Quitar prefijo de locale si existe
    $segments = array_values(array_filter(explode('/', $path)));
    $pathNoLocale = $path;
    if (!empty($segments) && in_array($segments[0], $supported, true)) {
        $pathNoLocale = '/' . implode('/', array_slice($segments, 1));
        if ($pathNoLocale === '/') { $pathNoLocale = ''; }
    }

    // Rutas sin localización (no agregues /es, /en, ...)
    $unlocalized = [
        'login','register','password','password-reset','email','verify',
        'auth','two-factor-challenge','two-factor','unlock-account','account',
        'admin', // admin no está localizado
        'profile','my-reservations','my-cart','mi-carrito',
    ];

    $first = ltrim($pathNoLocale, '/');
    $first = $first !== '' ? strtok($first, '/') : '';

    if ($first !== '' && in_array($first, $unlocalized, true)) {
        // vuelve tal cual, sin prefijo
        return redirect()->to(($pathNoLocale === '' ? '/' : $pathNoLocale) . $query);
    }

    // Si es raíz pública → manda al home del idioma
    if ($pathNoLocale === '' || $pathNoLocale === '/') {
        return redirect()->to('/' . $prefix);
    }

    // Rutas públicas localizadas → anteponer prefijo del idioma elegido
    return redirect()->to('/' . $prefix . $pathNoLocale . $query);
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
