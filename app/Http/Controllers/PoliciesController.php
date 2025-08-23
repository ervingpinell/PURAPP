<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Policy;

class PoliciesController extends Controller
{
    public function index()
    {
        $policies = Policy::query()
            ->active()
            ->effectiveOn()
            ->with([
                'translations',
                'activeSections' => fn($q) => $q->orderBy('sort_order'),
                'activeSections.translations',
            ])
            ->orderByDesc('effective_from')
            ->get();

        return view('policies.index', compact('policies'));
    }

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
