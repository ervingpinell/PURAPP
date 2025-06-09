<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Tour;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    public function index()
    {
        
        if (Auth::user()->id_role != 1 && Auth::user()->id_role != 2){
            return redirect()->route('home')->with('error', 'Acceso denegado.');
        }

        $totalReservas = Reserva::count();
        $totalUsuarios = User::count();
        $totalTours = Tour::count();
        $roles = Role::count(); 

        return view('admin.dashboardMain', compact('totalReservas', 'totalTours', 'totalUsuarios', 'roles'));
    }
}
