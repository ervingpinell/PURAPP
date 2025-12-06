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
    public function __construct()
    {
        $this->middleware(['can:view-meeting-points'])->only('index');
        $this->middleware(['can:create-meeting-points'])->only(['store']);
        $this->middleware(['can:edit-meeting-points'])->only(['update']);
        $this->middleware(['can:publish-meeting-points'])->only(['toggle']);
        $this->middleware(['can:delete-meeting-points'])->only(['destroy']);
    }
    public function index()
    {
        // Eager-load de translations para evitar N+1
        $points = MeetingPoint::with('translations')
            ->orderByRaw('sort_order IS NULL, sort_order ASC')
            ->orderBy('name', 'asc')
            ->get();

        // Locales disponibles para la UI de edición de traducción
        $locales = ['es', 'en', 'fr', 'pt', 'de'];

        return view('admin.meetingpoints.index', compact('points', 'locales'));
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

        // 🔹 Solo en CREATE: Genera/actualiza traducciones con DeepL
        $this->syncTranslations($mp, $translator);

        return redirect()->route('admin.meetingpoints.index')
            ->with('success', 'Punto creado y traducciones generadas correctamente.');
    }

    public function update(Request $request, MeetingPoint $meetingpoint)
    {
        $fallback = config('app.fallback_locale', 'es');

        // Validación flexible:
        // - Campos "base" opcionales (por si solo editas traducción)
        // - Locale + t_name obligatorios para la traducción
        $data = $request->validate([
            'name'         => 'sometimes|nullable|string|max:1000|unique:meeting_points,name,' . $meetingpoint->id,
            'pickup_time'  => 'sometimes|nullable|string|max:20',
            'description'  => 'sometimes|nullable|string|max:1000',
            'map_url'      => 'sometimes|nullable|url|max:255',
            'sort_order'   => 'sometimes|nullable|integer|min:0',
            'is_active'    => 'sometimes|boolean',

            'locale'       => 'required|in:es,en,fr,pt,de',
            't_name'       => 'required|string|max:1000',
            't_description' => 'nullable|string|max:1000',
        ]);

        // 🔸 Actualiza SI LLEGAN campos no lingüísticos / base (no obliga a enviarlos)
        $meetingpoint->fill([
            'pickup_time' => array_key_exists('pickup_time', $data) ? $data['pickup_time'] : $meetingpoint->pickup_time,
            'map_url'     => array_key_exists('map_url', $data)     ? $data['map_url']     : $meetingpoint->map_url,
            'sort_order'  => array_key_exists('sort_order', $data)  ? $data['sort_order']  : $meetingpoint->sort_order,
        ]);

        if (array_key_exists('is_active', $data)) {
            $meetingpoint->is_active = (bool) $data['is_active'];
        }

        // Si quieren cambiar explícitamente el nombre/descr base, también se respeta
        if (array_key_exists('name', $data)) {
            $meetingpoint->name = $data['name'];
        }
        if (array_key_exists('description', $data)) {
            $meetingpoint->description = $data['description'];
        }

        $meetingpoint->save();

        // 🔹 Siempre guardamos SOLO la traducción del locale enviado (sin DeepL)
        MeetingPointTranslation::updateOrCreate(
            ['meeting_point_id' => $meetingpoint->id, 'locale' => $data['locale']],
            [
                'name'        => $data['t_name'],
                'description' => $data['t_description'] ?? null,
            ]
        );

        // 🔸 Si el locale es el fallback, sincronizamos con el modelo base (si no fue enviado name/description base)
        if ($data['locale'] === $fallback) {
            $meetingpoint->update([
                'name'        => $data['t_name'],
                'description' => $data['t_description'] ?? $meetingpoint->description,
            ]);
        }

        return back()->with('success', 'Cambios guardados. Traducción actualizada para: ' . $data['locale'] . ' (sin DeepL).');
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
     * (SOLO CREATE) Genera/actualiza traducciones DeepL para los campos traducibles.
     */
    private function syncTranslations(MeetingPoint $mp, TranslatorInterface $translator): void
    {
        $fields = ['name', 'description'];

        // Pedimos todas las traducciones de cada campo
        $packs = [];
        foreach ($fields as $f) {
            $packs[$f] = $translator->translateAll((string) $mp->{$f}); // devuelve ['es','en','fr','pt','de']
        }

        foreach (['es', 'en', 'fr', 'pt', 'de'] as $loc) {
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
