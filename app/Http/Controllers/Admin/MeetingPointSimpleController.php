<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeetingPoint;
use Illuminate\Http\Request;

// Imports para traducciones
use App\Services\Contracts\TranslatorInterface;
use App\Models\MeetingPointTranslation;

class MeetingPointSimpleController extends Controller
{
    public function index()
    {
        // Eager-load de translations para evitar N+1
        $points = MeetingPoint::with('translations')
            ->orderByRaw('sort_order IS NULL, sort_order ASC')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.meetingpoints.index', compact('points'));
    }

    public function store(Request $request, TranslatorInterface $translator)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:1000|unique:meeting_points,name',
            'pickup_time' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
            'map_url'     => 'nullable|url|max:255',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'sometimes|boolean',
        ]);

        $data['is_active']  = (bool) ($data['is_active'] ?? 0);
        $data['sort_order'] = $data['sort_order'] ?? ((MeetingPoint::max('sort_order') ?? 0) + 1);

        $mp = MeetingPoint::create($data);

        // Genera/actualiza traducciones con DeepL
        $this->syncTranslations($mp, $translator);

        return redirect()->route('admin.meetingpoints.index')
            ->with('success', 'Punto creado y traducciones generadas correctamente.');
    }

    public function update(Request $request, MeetingPoint $meetingpoint, TranslatorInterface $translator)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:1000|unique:meeting_points,name,' . $meetingpoint->id,
            'pickup_time' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
            'map_url'     => 'nullable|url|max:255',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'sometimes|boolean',
        ]);

        $data['is_active'] = array_key_exists('is_active', $data)
            ? (bool) $data['is_active']
            : $meetingpoint->is_active;

        $meetingpoint->update($data);

        // Recalcular traducciones
        $this->syncTranslations($meetingpoint, $translator);

        return back()->with('success', 'Cambios guardados y traducciones sincronizadas.');
    }

    public function toggle(MeetingPoint $meetingpoint)
    {
        $meetingpoint->is_active = ! $meetingpoint->is_active;
        $meetingpoint->save();

        return back()->with('success', $meetingpoint->is_active ? 'Activado.' : 'Desactivado.');
    }

    public function destroy(MeetingPoint $meetingpoint)
    {
        $meetingpoint->delete();
        return back()->with('success', 'Eliminado correctamente.');
    }

    /**
     * Genera/actualiza traducciones DeepL para los campos traducibles del MeetingPoint.
     */
    private function syncTranslations(MeetingPoint $mp, TranslatorInterface $translator): void
    {
        // Campos a traducir
        $fields = ['name', 'description'];

        // Pedimos todas las traducciones de cada campo
        $packs = [];
        foreach ($fields as $f) {
            $packs[$f] = $translator->translateAll((string) $mp->{$f}); // ['es','en','fr','pt','de']
        }

        // Persistimos por locale (claves cortas)
        foreach (['es','en','fr','pt','de'] as $loc) {
            MeetingPointTranslation::updateOrCreate(
                ['meeting_point_id' => $mp->id, 'locale' => $loc],
                [
                    'name'        => $packs['name'][$loc] ?? $mp->name,
                    'description' => $packs['description'][$loc] ?? $mp->description,
                ]
            );
        }
    }
}
