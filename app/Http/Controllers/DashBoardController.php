<?php

namespace App\Http\Controllers;

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



class DashBoardController extends Controller
{


    public function index()
    {
        return view('index'); 
    }

public function switchLanguage($language)
{
    if (in_array($language, ['es', 'en', 'fr'])) {
        session(['locale' => $language]);
    }

    // 🔁 Redirige de forma más segura (sin cache)
    $referer = url()->previous();
    if (str_contains($referer, '/login')) {
        return redirect()->route('login'); 
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

        return view('admin.dashboard', compact(
            'totalTours',
            'totalUsuarios',
            'roles',
            'totalCategorias',
            'totalIdiomas'
        ));
    }

}