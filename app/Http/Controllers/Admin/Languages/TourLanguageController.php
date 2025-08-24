<?php

namespace App\Http\Controllers\Admin\Languages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourLanguage;
use Illuminate\Validation\Rule;

class TourLanguageController extends Controller
{
    public function index()
    {
        $languages = TourLanguage::orderBy('name')->get();
        return view('admin.languages.index', compact('languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                'unique:tour_languages,name',
            ],
        ], [
            'name.required' => 'El nombre del idioma es obligatorio.',
            'name.unique'   => 'Ya existe un idioma con ese nombre.',
        ]);

        TourLanguage::create([
            'name'      => $request->string('name')->trim(),
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.languages.index')
            ->with('success', 'Idioma creado exitosamente.')
            ->with('alert_type', 'creado');
    }

    public function update(Request $request, TourLanguage $language)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('tour_languages', 'name')
                    ->ignore($language->getKey(), 'tour_language_id'),
            ],
        ], [
            'name.required' => 'El nombre del idioma es obligatorio.',
            'name.unique'   => 'Ya existe un idioma con ese nombre.',
        ]);

        $language->update([
            'name' => $request->string('name')->trim(),
        ]);

        return redirect()
            ->route('admin.languages.index')
            ->with('success', 'Idioma actualizado correctamente.')
            ->with('alert_type', 'actualizado');
    }

    public function toggle(TourLanguage $language)
    {
        $language->update([
            'is_active' => ! $language->is_active,
        ]);

        $accion = $language->is_active ? 'activado' : 'desactivado';

        return redirect()
            ->route('admin.languages.index')
            ->with('success', "Idioma {$accion} correctamente.")
            ->with('alert_type', $accion);
    }

    /**
     * (Opcional) Borrado real – dejado comentado.
     * Si algún día quieres eliminar físicamente:
     */
    /*
    public function destroy(TourLanguage $language)
    {
        $name = $language->name;
        $language->delete();

        return redirect()
            ->route('admin.languages.index')
            ->with('success', "Idioma «{$name}» eliminado.")
            ->with('alert_type', 'eliminado');
    }
    */
}
