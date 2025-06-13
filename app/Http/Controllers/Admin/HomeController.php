<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use App\Models\Category;
use App\Models\TourLanguage;



class HomeController extends Controller
{
    public function __construct()
    {
        // Esto aplica el idioma en cada request para este controlador
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }
    }

    public function index()
    {
        return view('index'); // la vista principal
    }

public function cambiarIdioma($idioma)
{
    if (in_array($idioma, ['es', 'en', 'fr'])) {
        session(['locale' => $idioma]);
    }

    // 🔁 Redirige de forma más segura (sin cache)
    $referer = url()->previous();
    if (str_contains($referer, '/login')) {
        return redirect()->route('login'); // fuerza recarga del login
    }

    return redirect($referer);
}

    public function dashboard()
    {
        if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2) {
            return redirect()->route('login')->with('error', 'Acceso denegado.');
        }

        $totalUsuarios = User::count();
        $totalTours = Tour::count();
        $roles = Role::count();
        $totalCategorias = Category::count();
        $totalIdiomas = TourLanguage::count();

        return view('admin.dashboardMain', compact(
            'totalTours',
            'totalUsuarios',
            'roles',
            'totalCategorias',
            'totalIdiomas'
        ));
    }

}