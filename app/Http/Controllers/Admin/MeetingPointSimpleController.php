<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeetingPoint;
use App\Models\User;
use Illuminate\Http\Request;

// Imports para traducciones
use App\Services\Contracts\TranslatorInterface;
use App\Models\MeetingPointTranslation;
use App\Services\LoggerHelper;

/**
 * MeetingPointSimpleController
 *
 * Handles meeting point CRUD operations with soft delete support.
 */
class MeetingPointSimpleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-meeting-points'])->only(['index']);
        $this->middleware(['can:create-meeting-points'])->only(['store']);
        $this->middleware(['can:edit-meeting-points'])->only(['update']);
        $this->middleware(['can:publish-meeting-points'])->only(['toggle']);
        $this->middleware(['can:delete-meeting-points'])->only(['destroy']);
        $this->middleware(['can:restore-meeting-points'])->only(['trash', 'restore']);
        $this->middleware(['can:force-delete-meeting-points'])->only(['forceDelete']);
    }

    /**
     * Display active meeting points
     */
    public function index()
    {
        // Eager-load translations to avoid N+1
        $points = MeetingPoint::with('translations')
            ->orderByRaw('sort_order IS NULL, sort_order ASC')
            ->get();

        // Count trashed items for badge
        $trashedCount = MeetingPoint::onlyTrashed()->count();

        // Available locales for translation UI
        $locales = ['es', 'en', 'fr', 'pt', 'de'];

        return view('admin.meetingpoints.index', compact('points', 'locales', 'trashedCount'));
    }

    /**
     * Display trashed meeting points
     */
    public function trash()
    {
        $trashedPoints = MeetingPoint::onlyTrashed()
            ->with(['translations', 'deletedBy'])
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('admin.meetingpoints.trash', compact('trashedPoints'));
    }

    /**
     * Store a new meeting point with auto-translations
     */
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

        // Generate translations with DeepL
        $this->syncTranslations(
            $mp,
            $translator,
            $data['name'],
            $data['description'] ?? null,
            $data['instructions'] ?? null
        );

        LoggerHelper::mutated('MeetingPointSimpleController', 'store', 'MeetingPoint', $mp->id);

        return redirect()->route('admin.meetingpoints.index')
            ->with('success', __('pickups.meeting_point.toasts.created_success'));
    }

    /**
     * Update meeting point and specific translation
     */
    public function update(Request $request, MeetingPoint $meetingpoint)
    {
        $data = $request->validate([
            'name'            => 'nullable|string|max:1000',
            'pickup_time'     => 'nullable|string|max:20',
            'map_url'         => 'nullable|url|max:255',
            'sort_order'      => 'nullable|integer|min:0',
            'is_active'       => 'sometimes|boolean',
            'locale'          => 'required|in:es,en,fr,pt,de',
            't_name'          => 'required|string|max:1000',
            't_description'   => 'nullable|string|max:1000',
            't_instructions'  => 'nullable|string',
        ]);

        // Update base fields if provided
        $updateData = [];
        if (isset($data['pickup_time'])) $updateData['pickup_time'] = $data['pickup_time'];
        if (isset($data['map_url'])) $updateData['map_url'] = $data['map_url'];
        if (isset($data['sort_order'])) $updateData['sort_order'] = $data['sort_order'];
        if (isset($data['is_active'])) $updateData['is_active'] = (bool) $data['is_active'];

        if (!empty($updateData)) {
            $meetingpoint->update($updateData);
        }

        // Update specific translation
        MeetingPointTranslation::updateOrCreate(
            ['meeting_point_id' => $meetingpoint->id, 'locale' => $data['locale']],
            [
                'name'         => $data['t_name'],
                'description'  => $data['t_description'] ?? null,
                'instructions' => $data['t_instructions'] ?? null,
            ]
        );

        LoggerHelper::mutated('MeetingPointSimpleController', 'update', 'MeetingPoint', $meetingpoint->id);

        return redirect()->route('admin.meetingpoints.index')
            ->with('success', __('pickups.meeting_point.toasts.updated_success'));
    }

    /**
     * Toggle active status
     */
    public function toggle(MeetingPoint $meetingpoint)
    {
        $meetingpoint->update(['is_active' => !$meetingpoint->is_active]);

        LoggerHelper::mutated('MeetingPointSimpleController', 'toggle', 'MeetingPoint', $meetingpoint->id);

        $message = $meetingpoint->is_active
            ? __('pickups.meeting_point.toasts.activated_success')
            : __('pickups.meeting_point.toasts.deactivated_success');

        return redirect()->route('admin.meetingpoints.index')
            ->with('success', $message);
    }

    /**
     * Soft delete meeting point
     */
    public function destroy(MeetingPoint $meetingpoint)
    {
        // Record who deleted it
        $meetingpoint->update(['deleted_by' => auth()->id()]);
        $meetingpoint->delete(); // Soft delete

        LoggerHelper::mutated(
            'MeetingPointSimpleController',
            'destroy',
            'MeetingPoint',
            $meetingpoint->id,
            ['deleted_by' => auth()->id()]
        );

        return redirect()->route('admin.meetingpoints.index')
            ->with('success', __('pickups.meeting_point.toasts.deleted_success'));
    }

    /**
     * Restore a soft-deleted meeting point
     */
    public function restore($id)
    {
        $meetingpoint = MeetingPoint::onlyTrashed()->findOrFail($id);

        $meetingpoint->update(['deleted_by' => null]);
        $meetingpoint->restore();

        LoggerHelper::mutated('MeetingPointSimpleController', 'restore', 'MeetingPoint', $id);

        return redirect()->route('admin.meetingpoints.trash')
            ->with('success', __('pickups.meeting_point.trash.restore_success'));
    }

    /**
     * Permanently delete a meeting point (hard delete)
     */
    public function forceDelete($id)
    {
        $meetingpoint = MeetingPoint::onlyTrashed()->findOrFail($id);

        LoggerHelper::mutated('MeetingPointSimpleController', 'forceDelete', 'MeetingPoint', $id);

        $meetingpoint->forceDelete();

        return redirect()->route('admin.meetingpoints.trash')
            ->with('success', __('pickups.meeting_point.trash.force_delete_success'));
    }

    /**
     * (SOLO CREATE) Genera/actualiza traducciones DeepL para los campos traducibles.
     */
    private function syncTranslations(
        MeetingPoint $mp,
        TranslatorInterface $translator,
        string $sourceName,
        ?string $sourceDesc,
        ?string $sourceInstr
    ): void {
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
