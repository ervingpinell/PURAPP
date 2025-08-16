<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Policy;

class PoliciesController extends Controller
{
    /** GET /politicas — listado público (todas las categorías activas y vigentes) */
    public function index()
    {
        $policies = Policy::query()
            ->active()
            ->effectiveOn() // hoy por defecto
            ->with([
                'translations',
                'activeSections' => fn($q) => $q->orderBy('sort_order'),
                'activeSections.translations',
            ])
            ->orderByDesc('effective_from')
            ->get();

        return view('policies.index', compact('policies'));
    }

    /** GET /politicas/{policy} — detalle público de una categoría */
    public function show(Policy $policy)
    {
        $policy->loadMissing([
            'translations',
            'activeSections' => fn($q) => $q->orderBy('sort_order'),
            'activeSections.translations',
        ]);

        return view('policies.show', compact('policy'));
    }
}
