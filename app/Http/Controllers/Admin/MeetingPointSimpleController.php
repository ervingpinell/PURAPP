<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeetingPoint;
use Illuminate\Http\Request;

// Imports para traducciones
use App\Services\Contracts\TranslatorInterface;
use App\Models\MeetingPointTranslation;
use App\Services\LoggerHelper;

/**
 * MeetingPointSimpleController
 *
 * Handles meetingpointsimple operations.
 */
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
            ->get();

        // Locales disponibles para la UI de edición de traducción
        $locales = ['es', 'en', 'fr', 'pt', 'de'];

        return view('admin.meetingpoints.index', compact('points', 'locales'));
    }

    public function store(Request $request, TranslatorInterface $translator)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:1000',
            'description'  => 'nullable|string|max:1000',
            'instructions' => 'nullable|string',
            'pickup_time'  => 'nullable|string|max:20',
            'map_url'      => 'nullable|url|max:255',
            'sort_order'   => 'nullable|integer|min:0',
            'is_active'    => 'sometimes|boolean',
        ]);

        $createData = [
            'pickup_time' => $data['pickup_time'] ?? null,
            'map_url'     => $data['map_url'] ?? null,
            'sort_order'  => $data['sort_order'] ?? ((MeetingPoint::max('sort_order') ?? 0) + 1),
            'is_active'   => (bool) ($data['is_active'] ?? 0),
        ];

        $mp = MeetingPoint::create($createData);

        // 🔹 Solo en CREATE: Genera/actualiza traducciones con DeepL usando el input 'name', 'description', 'instructions'
        $this->syncTranslations($mp, $translator, $data['name'], $data['description'] ?? null, $data['instructions'] ?? null);

        LoggerHelper::mutated('MeetingPointSimpleController', 'store', 'MeetingPoint', $mp->id);

        return redirect()->route('admin.meetingpoints.index')
            ->with('success', 'Punto creado y traducciones generadas correctamente.');
    }

    // ... (update method remains unchanged) ...

    /**
     * (SOLO CREATE) Genera/actualiza traducciones DeepL para los campos traducibles.
     */
    private function syncTranslations(MeetingPoint $mp, TranslatorInterface $translator, string $sourceName, ?string $sourceDesc, ?string $sourceInstr): void
    {
        $packs = [];
        $packs['name']         = $translator->translateAll($sourceName);
        $packs['description']  = $sourceDesc ? $translator->translateAll($sourceDesc) : [];
        $packs['instructions'] = $sourceInstr ? $translator->translateAll($sourceInstr) : [];

        foreach (['es', 'en', 'fr', 'pt', 'de'] as $loc) {
            MeetingPointTranslation::updateOrCreate(
                ['meeting_point_id' => $mp->id, 'locale' => $loc],
                [
                    'name'         => $packs['name'][$loc] ?? $sourceName,
                    'description'  => $packs['description'][$loc] ?? $sourceDesc,
                    'instructions' => $packs['instructions'][$loc] ?? $sourceInstr,
                ]
            );
        }
    }
}
