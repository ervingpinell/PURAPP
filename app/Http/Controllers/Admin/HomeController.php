<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Tour;
use App\Models\Role;
use App\Models\User;

class HomeController extends Controller
{
    public function index(){
    $totalReservas = Reserva::count();
    $totalUsuarios = User::count();
    $totalTours = Tour::count();
    $roles = Role::count();
    
    return view('admin.dashboardMain', compact( 'totalReservas', 'totalTours','totalUsuarios'));
}
}
