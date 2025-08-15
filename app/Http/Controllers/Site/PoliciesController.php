<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Contracts\View\View;

class PoliciesController extends Controller
{
    /** GET /politicas - Listado público */
    public function index(): View
    {
        $policies = Policy::query()
            ->active()
            ->effectiveOn() // hoy por defecto (scope del modelo)
            ->with([
                'translations',
                'activeSections' => fn($q) => $q->orderBy('sort_order'),
                'activeSections.translations',
            ])
            ->orderByDesc('effective_from')
            ->get();

        return view('policies.index', compact('policies'));
    }

    /** GET /politicas/{policy} - Detalle público */
    public function show(Policy $policy): View
    {
        $policy->loadMissing([
            'translations',
            'activeSections' => fn($q) => $q->orderBy('sort_order'),
            'activeSections.translations',
        ]);

        return view('policies.show', compact('policy'));
    }
}
