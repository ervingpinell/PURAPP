<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\Amenity;
use App\Models\AmenityTranslation;
use App\Services\Contracts\TranslatorInterface;

class AmenityController extends Controller
{
    public function index()
    {
        $amenities = Amenity::orderBy('name')->get();
        return view('admin.tours.amenities.index', compact('amenities'));
    }

    public function store(Request $request, TranslatorInterface $translator)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:amenities,name',
        ]);

        DB::transaction(function () use ($request, $translator) {
            $name = $request->string('name')->trim();

            $amenity = Amenity::create([
                'name'      => $name,
                'is_active' => true,
            ]);

            // Traducciones automáticas (ES, EN, FR, PT, DE)
            $nameTr = $translator->translateAll($name);

            foreach (['es', 'en', 'fr', 'pt', 'de'] as $lang) {
                AmenityTranslation::create([
                    'amenity_id' => $amenity->amenity_id,
                    'locale'     => $lang,
                    'name'       => $nameTr[$lang] ?? $name,
                ]);
            }
        });

        return redirect()
            ->route('admin.tours.amenities.index')
            ->with('success', 'Amenidad creada correctamente.')
            ->with('alert_type', 'creado');
    }

    public function update(Request $request, Amenity $amenity)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('amenities', 'name')->ignore($amenity->amenity_id, 'amenity_id'),
            ],
        ]);

        $amenity->update([
            'name' => $request->string('name')->trim(),
        ]);

        return redirect()
            ->route('admin.tours.amenities.index')
            ->with('success', 'Amenidad actualizada correctamente.')
            ->with('alert_type', 'actualizado');
    }

    public function destroy(Amenity $amenity)
    {
        $amenity->update([
            'is_active' => ! $amenity->is_active,
        ]);

        $accion = $amenity->is_active ? 'activado' : 'desactivado';

        return redirect()
            ->route('admin.tours.amenities.index')
            ->with('success', "Amenidad {$accion} correctamente.")
            ->with('alert_type', $accion);
    }
}
