<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\TourLanguage;

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
        $totalCategorias = Category::count();
        $totalIdiomas = TourLanguage::count();

        return view('admin.dashboard', compact(
            'totalTours',
            'totalUsuarios',
            'roles',
            'totalCategorias',
            'totalIdiomas'
        ));
    }
}
