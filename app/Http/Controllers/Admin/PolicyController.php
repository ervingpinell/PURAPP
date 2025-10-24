<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function index()
    {
        $policies = Policy::query()
            ->with(['translations', 'sections'])
            ->withCount('sections')
            ->orderByDesc('effective_from')
            ->get();

        return view('admin.policies.index', compact('policies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'slug'           => ['nullable', 'string', 'max:255', 'unique:policies,slug'],
            'content'        => ['required', 'string'],
            'is_active'      => ['sometimes', 'boolean'],
            'effective_from' => ['nullable', 'date'],
            'effective_to'   => ['nullable', 'date', 'after_or_equal:effective_from'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        Policy::create($data);

        return back()->with('success', 'm_config.policies.created');
    }

    public function update(Request $request, $policyId)
    {
        // ✅ Buscar explícitamente por policy_id (ignora el getRouteKeyName del modelo)
        $policy = Policy::where('policy_id', $policyId)->firstOrFail();

        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'slug'           => ['nullable', 'string', 'max:255', 'unique:policies,slug,' . $policy->policy_id . ',policy_id'],
            'content'        => ['required', 'string'],
            'is_active'      => ['sometimes', 'boolean'],
            'effective_from' => ['nullable', 'date'],
            'effective_to'   => ['nullable', 'date', 'after_or_equal:effective_from'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $policy->update($data);

        return back()->with('success', 'm_config.policies.updated');
    }

    public function toggle($policyId)
    {
        // ✅ Buscar explícitamente por policy_id
        $policy = Policy::where('policy_id', $policyId)->firstOrFail();

        $policy->update([
            'is_active' => !$policy->is_active,
        ]);

        return back()->with(
            'success',
            $policy->is_active
                ? 'm_config.policies.category_activated'
                : 'm_config.policies.category_deactivated'
        );
    }

    public function destroy($policyId)
    {
        // ✅ Buscar explícitamente por policy_id
        $policy = Policy::where('policy_id', $policyId)->firstOrFail();
        $policy->delete();

        return back()->with('success', 'm_config.policies.deleted');
    }
}
