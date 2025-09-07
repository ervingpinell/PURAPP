<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\Request;

class PoliciesController extends Controller
{
    /**
     * Lista de políticas activas y vigentes hoy.
     */
    public function index()
    {
        $policies = Policy::query()
            ->active()
            ->effectiveOn() // asumiendo que filtra por hoy
            ->with([
                // Ya no necesitamos 'translations' de la política
                'activeSections' => fn ($q) => $q->orderBy('sort_order'),
                'activeSections.translations', // se mantienen traducciones de secciones, si aplica
            ])
            ->orderByDesc('effective_from')
            ->get();

        return view('policies.index', compact('policies'));
    }

    /**
     * Vista pública/detalle de una política.
     */
    public function show(Policy $policy)
    {
        $policy->loadMissing([
            // Sin 'translations' de la política
            'activeSections' => fn ($q) => $q->orderBy('sort_order'),
            'activeSections.translations',
        ]);

        return view('policies.show', compact('policy'));
    }

    /**
     * Crear nueva política (admin).
     * Ruta sugerida: POST admin.policies.store
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'content'        => ['required', 'string'], // ahora se guarda aquí
            'is_active'      => ['sometimes', 'boolean'],
            'effective_from' => ['nullable', 'date'],
            'effective_to'   => ['nullable', 'date', 'after_or_equal:effective_from'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        Policy::create($data);

        return back()->with('success', 'm_config.policies.created');
    }

    /**
     * Actualizar política (admin).
     * Ruta sugerida: PUT/PATCH admin.policies.update
     */
    public function update(Request $request, Policy $policy)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'content'        => ['required', 'string'],
            'is_active'      => ['sometimes', 'boolean'],
            'effective_from' => ['nullable', 'date'],
            'effective_to'   => ['nullable', 'date', 'after_or_equal:effective_from'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $policy->update($data);

        return back()->with('success', 'm_config.policies.updated');
    }

    /**
     * Activar / Desactivar (admin).
     * Ruta sugerida: POST admin.policies.toggle
     */
    public function toggle(Policy $policy)
    {
        $policy->update([
            'is_active' => ! $policy->is_active,
        ]);

        return back()->with(
            'success',
            $policy->is_active
                ? 'm_config.policies.activated'
                : 'm_config.policies.deactivated'
        );
    }

    /**
     * Eliminar política (admin).
     * Ruta sugerida: DELETE admin.policies.destroy
     */
    public function destroy(Policy $policy)
    {
        $policy->delete();

        return back()->with('success', 'm_config.policies.deleted');
    }
}
