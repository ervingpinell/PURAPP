<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourLanguage;

class TourLanguageController extends Controller
{
    public function index()
    {
        $languages = TourLanguage::all();
        return view('admin.languages', compact('languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        TourLanguage::create([
            'name' => $request->name,
            'is_active' => true
        ]);

        return redirect()->route('admin.languages.index')
            ->with('success', 'Idioma creado exitosamente.')
            ->with('alert_type', 'creado');
    }

    public function update(Request $request, $id)
    {
        $language = TourLanguage::findOrFail($id);
        $language->update([
            'name' => $request->name
        ]);

        return redirect()->route('admin.languages.index')
            ->with('success', 'Idioma actualizado correctamente.')
            ->with('alert_type', 'actualizado');
    }

    public function destroy($id)
    {
        $language = TourLanguage::findOrFail($id);
        $language->is_active = !$language->is_active;
        $language->save();

        $accion = $language->is_active ? 'activado' : 'desactivado';

        return redirect()->route('admin.languages.index')
            ->with('success', 'Idioma ' . $accion . ' correctamente.')
            ->with('alert_type', $accion);
    }
}
