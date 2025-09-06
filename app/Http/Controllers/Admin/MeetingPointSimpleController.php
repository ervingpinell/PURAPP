<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeetingPoint;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class MeetingPointSimpleController extends Controller
{
    public function index()
    {
        $points = \App\Models\MeetingPoint::orderByRaw('sort_order IS NULL, sort_order ASC')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.meetingpoints.index', compact('points'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        // 👉 NO exigimos is_active. Lo normalizamos después.
        $data = $request->validate(
            [
                'name'        => 'required|string|max:255|unique:meeting_points,name',
                'pickup_time' => 'nullable|string|max:20',
                'address'     => 'nullable|string|max:255',
                'map_url'     => 'nullable|url|max:255',
                'sort_order'  => 'nullable|integer|min:0',
                'is_active'   => 'sometimes|boolean',
            ],
            [],
            [ 'is_active' => 'Activo' ] // etiqueta bonita en errores
        );

        $data['is_active']  = (bool) ($data['is_active'] ?? 0);
        $data['sort_order'] = $data['sort_order'] ?? ((\App\Models\MeetingPoint::max('sort_order') ?? 0) + 1);

        \App\Models\MeetingPoint::create($data);

        return redirect()->route('admin.meetingpoints.index')
            ->with('success', 'Punto creado correctamente.');
    }

    public function update(Request $request, \App\Models\MeetingPoint $meetingpoint): \Illuminate\Http\RedirectResponse
    {
        // 👉 Igual que store: no exigimos is_active.
        $data = $request->validate(
            [
                'name'        => 'required|string|max:255|unique:meeting_points,name,' . $meetingpoint->id,
                'pickup_time' => 'nullable|string|max:20',
                'address'     => 'nullable|string|max:255',
                'map_url'     => 'nullable|url|max:255',
                'sort_order'  => 'nullable|integer|min:0',
                'is_active'   => 'sometimes|boolean',
            ],
            [],
            [ 'is_active' => 'Activo' ]
        );

        // Si no vino is_active en el form, dejamos el actual
        $data['is_active']  = array_key_exists('is_active', $data)
            ? (bool) $data['is_active']
            : $meetingpoint->is_active;

        $meetingpoint->update($data);

        return back()->with('success', 'Cambios guardados.');
    }

    public function toggle(\App\Models\MeetingPoint $meetingpoint): \Illuminate\Http\RedirectResponse
    {
        $meetingpoint->is_active = ! $meetingpoint->is_active;
        $meetingpoint->save();

        return back()->with('success', $meetingpoint->is_active ? 'Activado.' : 'Desactivado.');
    }

    public function destroy(\App\Models\MeetingPoint $meetingpoint): \Illuminate\Http\RedirectResponse
    {
        $meetingpoint->delete();
        return back()->with('success', 'Eliminado correctamente.');
    }

}
