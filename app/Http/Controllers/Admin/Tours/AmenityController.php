<?php

    namespace App\Http\Controllers\Admin\Tours;
use App\Services\TranslationService;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Amenity;
    use Illuminate\Validation\Rule;

    class AmenityController extends Controller
    {
        public function index()
        {
            $amenities = Amenity::orderBy('name')->get();
            return view('admin.tours.amenities.index', compact('amenities'));
        }

public function store(Request $request, TranslationService $translator)
{
    $request->validate([
        'name' => 'required|string|max:255|unique:amenities,name',
    ]);

    $amenity = Amenity::create([
        'name' => $request->name,
        'is_active' => true,
    ]);

    // ✅ Traducción automática al crear
    foreach (['en', 'pt', 'fr', 'de'] as $lang) {
        \App\Models\AmenityTranslation::create([
            'amenity_id' => $amenity->amenity_id,
            'locale'     => $lang,
            'name'       => $translator->translate($request->name, $lang),
        ]);
    }

    return redirect()->route('admin.tours.amenities.index')
        ->with('success', 'Amenidad creada correctamente.')
        ->with('alert_type', 'creado');
}

        public function update(Request $request, $id)
        {
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('amenities', 'name')->ignore($id),
                ],
            ]);

            $amenity = Amenity::findOrFail($id);
            $amenity->update([
                'name' => $request->name,
            ]);

            return redirect()->route('admin.tours.amenities.index')
                ->with('success', 'Amenidad actualizada correctamente.')
                ->with('alert_type', 'actualizado');
        }

        public function destroy($id)
        {
            $amenity = Amenity::findOrFail($id);
            $amenity->is_active = !$amenity->is_active;
            $amenity->save();

            $accion = $amenity->is_active ? 'activado' : 'desactivado';

            return redirect()->route('admin.tours.amenities.index')
                ->with('success', "Amenidad {$accion} correctamente.")
                ->with('alert_type', $accion);
        }
    }
