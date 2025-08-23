<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\Role;
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use App\Models\TourType;
use App\Models\Amenity;
use App\Models\TourLanguage;
use App\Models\ItineraryItem;
use App\Models\Itinerary;
use App\Models\Booking;

class DashBoardController extends Controller
{
    public function index()
    {
        return view('index');
    }

public function switchLanguage(string $language)
{
    $map = [
        'es' => 'es',
        'es_CR' => 'es',
        'pt' => 'pt',
        'pt_BR' => 'pt',
        'en' => 'en',
        'fr' => 'fr',
        'de' => 'de',
    ];

    if (isset($map[$language])) {
        $locale = $map[$language];
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }

    $referer = url()->previous() ?: route('home');


    $path = parse_url($referer, PHP_URL_PATH) ?? '';
    if (str_starts_with($path, '/login')) {
        return redirect()->route('login');
    }

    return redirect()->to($referer);
}



    public function dashboard()
    {
        if (!in_array(Auth::user()->role_id, [1, 2])) {
            return redirect()->route('login')->with('error', __('adminlte::adminlte.access_denied'));
        }

        $totalUsuarios   = User::count();
        $totalTours      = Tour::count();
        $roles           = Role::count();
        $tourTypes       = TourType::count();
        $totalIdiomas    = TourLanguage::count();
        $totalHorarios   = Schedule::count();
        $totalAmenities  = Amenity::count();
        $totalItinerarios = ItineraryItem::count();
        $totalReservas   = Booking::count();

        $itineraries = Itinerary::with('items')->get();

        $upcomingBookings = Booking::with(['user','detail.tour'])
            ->whereHas('detail', fn($q) => $q->where('tour_date', '>=', today()))
            ->orderBy('booking_date')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsuarios',
            'totalTours',
            'tourTypes',
            'totalIdiomas',
            'totalHorarios',
            'totalAmenities',
            'totalItinerarios',
            'totalReservas',
            'itineraries',
            'upcomingBookings'
        ));
    }
}
