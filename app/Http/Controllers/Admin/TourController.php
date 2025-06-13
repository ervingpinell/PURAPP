<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\Category;
use App\Models\TourLanguage;

class TourController extends Controller
{
    public function index()
    {
        $tours = Tour::with(['category', 'language'])->get();
        $categories = Category::all();
        $languages = TourLanguage::all();

        return view('admin.tours', compact('tours', 'categories', 'languages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'adult_price' => 'required|numeric|min:0',
            'kid_price' => 'nullable|numeric|min:0',
            'length' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'tour_language_id' => 'required|exists:tour_languages,tour_language_id',
        ]);

        $validated['is_active'] = true; // por defecto
        Tour::create($validated);

        return redirect()->back()->with('success', 'Tour agregado correctamente.');
    }

    public function update(Request $request, string $id)
    {
        $tour = Tour::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'adult_price' => 'required|numeric|min:0',
            'kid_price' => 'nullable|numeric|min:0',
            'length' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'tour_language_id' => 'required|exists:tour_languages,tour_language_id',
        ]);

        $tour->update($validated);

        return redirect()->back()->with('success', 'Tour actualizado correctamente.');
    }

    public function destroy(Tour $tour)
    {
        $tour->delete();
        return redirect()->back()->with('success', 'Tour eliminado correctamente.');
    }
}
