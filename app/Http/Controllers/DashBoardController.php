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

    /**
     * Normalize and switch the app locale, then return to the previous page.
     */
    public function switchLanguage(string $language): RedirectResponse
    {
        $normalized = str_replace('-', '_', strtolower($language));

        $localeMap = [
            'es'    => 'es',
            'es_cr' => 'es',
            'pt'    => 'pt',
            'pt_br' => 'pt',
            'en'    => 'en',
            'fr'    => 'fr',
            'de'    => 'de',
        ];

        if (isset($localeMap[$normalized])) {
            $targetLocale = $localeMap[$normalized];
            session(['locale' => $targetLocale]);
            app()->setLocale($targetLocale);
        }

        $previousUrl = url()->previous() ?: route('home');
        $path        = parse_url($previousUrl, PHP_URL_PATH) ?? '';

        // Avoid redirecting back into /login
        if (str_starts_with($path, '/login')) {
            return redirect()->route('login');
        }

        return redirect()->to($previousUrl);
    }

    /**
     * Admin dashboard (roles 1 or 2).
     */
    public function dashboard(): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role_id, [1, 2], true)) {
            return redirect()
                ->route('login')
                ->with('error', __('adminlte::adminlte.access_denied'));
        }

        // KPI counters
        $totalUsers          = User::count();
        $totalTours          = Tour::count();
        $totalRoles          = Role::count();
        $totalTourTypes      = TourType::count();
        $totalLanguages      = TourLanguage::count();
        $totalSchedules      = Schedule::count();
        $totalAmenities      = Amenity::count();
        $totalItineraryItems = ItineraryItem::count();
        $totalBookings       = Booking::count();

        // Extra data
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
