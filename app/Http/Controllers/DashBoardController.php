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

class DashBoardController extends Controller
{
    public function index(): View
    {
        return view('index');
    }

    public function switchLanguage(string $language): RedirectResponse
    {
        $locales     = array_keys(config('routes.locales', []));
        $normalized  = strtolower(str_replace(['-', '_'], '', $language));

        $localeMap = [
            'es'   => 'es',
            'escr' => 'es',
            'pt'   => 'pt',
            'ptbr' => 'pt',
            'ptpt' => 'pt',
            'en'   => 'en',
            'enus' => 'en',
            'engb' => 'en',
            'fr'   => 'fr',
            'frfr' => 'fr',
            'de'   => 'de',
            'dede' => 'de',
        ];

        $targetLocale = $localeMap[$normalized] ?? null;

        if ($targetLocale && in_array($targetLocale, $locales, true)) {
            $previous = url()->previous();

            // Detectar si estamos en la página de edición de traducción
            $isTranslationEdit = str_contains($previous, '/admin/translations/') &&
                                 str_contains($previous, '/edit');

            if ($isTranslationEdit) {
                // En página de edición: solo cambiar locale de interfaz
                session(['locale' => $targetLocale]);
                app()->setLocale($targetLocale);

                // Recargar la misma URL sin tocar el parámetro de edición
                return redirect($previous);
            }

            // Resto: cambiar UI locale y redirigir manteniendo contexto
            session(['locale' => $targetLocale]);
            app()->setLocale($targetLocale);

            $isAdmin = $this->isAdminUrl($previous);

            if ($isAdmin) {
                return redirect($previous);
            } else {
                $newUrl = $this->replaceLocaleInUrl($previous, $targetLocale);
                return redirect($newUrl);
            }
        }

        return back();
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
