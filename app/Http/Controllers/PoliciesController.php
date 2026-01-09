<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\Request;

/**
 * PoliciesController
 *
 * Displays policy pages.
 */
class PoliciesController extends Controller
{
    /**
     * Lista de políticas activas y vigentes hoy.
     */
    public function index()
    {
        $policies = Policy::query()
            ->active()
            ->effectiveOn()
            ->with([
                'activeSections' => fn ($q) => $q->orderBy('sort_order'),
                'activeSections.translations',
            ])
            ->orderByDesc('effective_from')
            ->get();

        return view('policies.index', compact('policies'));
    }

    /**
     * Vista pública/detalle de una política (usa slug automáticamente).
     */
    public function show(Policy $policy)
    {
        $policy->loadMissing([
            'activeSections' => fn ($q) => $q->orderBy('sort_order'),
            'activeSections.translations',
        ]);

        return view('policies.show', compact('policy'));
    }


public function showById(Policy $policy)
{
    $policy->loadMissing('translations'); // opcional, por consistencia
    return redirect()->to(
        localized_route('policies.show', ['policy' => $policy->slug]),
        301
    );
}
}
