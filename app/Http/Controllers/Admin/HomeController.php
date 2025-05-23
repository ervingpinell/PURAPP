<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Cliente;
use App\Models\Tour;

class HomeController extends Controller
{
    public function index(){
    $totalReservas = Reserva::count();
    $totalClientes = Cliente::count();
    $totalTours = Tour::count();

    return view('admin.dashboardMain', compact('totalClientes', 'totalReservas', 'totalTours'));
}
}
