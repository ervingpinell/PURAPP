<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Tour;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;


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
        $idiomasDisponibles = ['es', 'en', 'fr'];

        if (in_array($idioma, $idiomasDisponibles)) {
            session(['locale' => $idioma]);
            App::setLocale($idioma); // lo aplica inmediatamente
        }

        return redirect()->back(); // redirige a la misma página
    }

    public function dashboard()
    {
        if (Auth::user()->id_role != 1 && Auth::user()->id_role != 2) {
            return redirect()->route('login')->with('error', 'Acceso denegado.');
        }

        $totalReservas = Reserva::count();
        $totalUsuarios = User::count();
        $totalTours = Tour::count();
        $roles = Role::count();

        return view('admin.dashboardMain', compact('totalReservas', 'totalTours', 'totalUsuarios', 'roles'));
    }
}