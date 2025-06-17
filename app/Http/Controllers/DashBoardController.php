<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\Role;
use App\Models\User;
use App\Models\TourSchedule;
use Illuminate\Support\Facades\Auth;
use App\Models\TourType;
use App\Models\Amenity;
use App\Models\TourLanguage;
use App\Models\ItineraryItem;
use App\Models\Itinerary;


class DashBoardController extends Controller
{
    // Página pública principal
    public function index()
    {
        return view('index');
    }

    // Cambiar idioma (sesión)
    public function switchLanguage($language)
    {
        if (in_array($language, ['es', 'en', 'fr'])) {
            session(['locale' => $language]);
        }

        $referer = url()->previous();

        if (str_contains($referer, '/login')) {
            return redirect()->route('login');
        }

        return redirect($referer);
    }

    // Dashboard para admin y roles autorizados
    public function dashboard()
    {
        if (!in_array(Auth::user()->role_id, [1, 2])) {
            return redirect()->route('login')->with('error', 'Acceso denegado.');
        }

 $totalUsuarios = User::count();
    $totalTours = Tour::count();
    $roles = Role::count();
    $tourTypes = TourType::count();
    $totalIdiomas = TourLanguage::count();
    $totalHorarios = TourSchedule::count();
    $totalAmenities = Amenity::count();
    $totalItinerarios = ItineraryItem::count(); // ← Agrega esto
    $itineraries = Itinerary::with('items')->get();
    
    return view('admin.dashboard', compact(
        'totalUsuarios',
        'totalTours',
        'tourTypes',
        'totalIdiomas',
        'totalHorarios',
        'totalAmenities',
        'totalItinerarios',
        'itineraries',
    ));
    }
}
