<?php

namespace App\Http\Controllers;

use App\Models\Tour;

class HomeController extends Controller
{
    /**
     * Muestra la página principal con los tours activos.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtener tours activos con su tipo
        $tours = Tour::with('tourType')->where('is_active', true)->get();

        // Retornar la vista 'index' y pasarle los tours
        return view('public.index', compact('tours'));
    }
}
