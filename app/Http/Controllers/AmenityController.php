<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Amenity;

class AmenityController extends Controller
{
    public function index()
    {
        $amenities = Amenity::where('is_active', true)->get();
        return view('amenities.index', compact('amenities'));
    }

    public function create()
    {
        return view('amenities.create');
    }

    public function store(Request $request)
    {
            $request->validate([
            'name' => 'required|string|max:255'
        ]);

        Amenity::create($request->only('name') + ['is_active' => true]);

        return redirect()->back()->with('success', 'Amenidad creada');
    }

    public function edit(Amenity $amenity)
    {
        return view('amenities.edit', compact('amenity'));
    }

    public function update(Request $request, Amenity $amenity)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $amenity->update($request->only('name'));

        return redirect()->route('amenities.index')->with('success', 'Amenidad actualizada correctamente.');
    }

    public function destroy(Amenity $amenity)
    {
        $amenity->is_active = false;
        $amenity->save();

        return redirect()->route('amenities.index')->with('success', 'Amenidad desactivada correctamente.');
    }
}
