<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeetingPoint;
use Illuminate\Http\Request;

class MeetingPointSimpleController extends Controller
{
    public function index()
    {
        $points = MeetingPoint::orderByRaw('sort_order IS NULL, sort_order ASC')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.meetingpoints.index', compact('points'));
    }

    public function store(Request $request)
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

        MeetingPoint::create($data);

        return redirect()->route('admin.meetingpoints.index')
            ->with('success', 'Punto creado correctamente.');
    }

    public function update(Request $request, MeetingPoint $meetingpoint)
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

        return back()->with('success', 'Cambios guardados.');
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
}
