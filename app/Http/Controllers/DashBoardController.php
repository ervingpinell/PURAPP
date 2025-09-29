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

    /**
     * Cambia el primer segmento (locale) de la URL actual y redirige.
     * No usa sesión.
     */
    public function switchLanguage(Request $request, string $language): RedirectResponse
    {
        $supported = (array) config('app.supported_locales', ['es','en','fr','de','pt']);
        $lang = strtolower(substr(str_replace('-', '_', $language), 0, 2));
        if (!in_array($lang, $supported, true)) {
            $lang = config('app.locale', 'es');
        }

        $segments = $request->segments(); // sin slash
        if (count($segments) === 0) {
            return redirect()->to(url("/{$lang}"));
        }

        // Sustituye el primer segmento por el nuevo locale
        $segments[0] = $lang;
        $newUrl = url(implode('/', $segments));
        if ($query = $request->getQueryString()) {
            $newUrl .= '?'.$query;
        }
        return redirect()->to($newUrl);
    }

    /**
     * Admin dashboard (roles 1 o 2).
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
