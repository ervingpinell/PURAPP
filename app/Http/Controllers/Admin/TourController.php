<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Tour;
use App\Http\Controllers\Controller;


class TourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tours = Tour::all();
        return view('admin.tours', compact('tours'));
    }



    public function store(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_adulto' => 'required|numeric|min:0',
            'precio_nino' => 'nullable|numeric|min:0',
            'duracion_horas' => 'required|numeric|min:0',
            'ubicacion' => 'required|string|max:255',
            'tipo_tour' => 'required|in:Half Day,Full Day',
            'idioma_disponible' => 'nullable|string|max:100',
        ]);

        // Crear cliente
        Tour::create($validated);
        return redirect()->back()->with('success', 'Tour agregado correctamente.');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tour = Tour::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_adulto' => 'required|numeric|min:0',
            'precio_nino' => 'nullable|numeric|min:0',
            'duracion_horas' => 'required|numeric|min:0',
            'ubicacion' => 'required|string|max:255',
            'tipo_tour' => 'required|in:Half Day,Full Day',
            'idioma_disponible' => 'nullable|string|max:100',
        ]);

        $tour->update($request->only(['nombre', 'descripcion', 'precio_adulto', 'precio_nino','duracion_horas', 'ubicacion', 'tipo_tour', 'idioma_disponible']));

        return redirect()->back()->with('success', 'Tour actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tour $tour)
    {
        $tour->delete();
        return redirect()->back()->with('success', 'Tour eliminado correctamente.');
    }
}
