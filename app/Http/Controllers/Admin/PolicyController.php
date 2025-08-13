<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Policy;
use App\Models\PolicyTranslation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PolicyController extends Controller
{
    public function index()
    {
        $policies = Policy::with('translations')->latest()->get();
        return view('admin.policies.index', compact('policies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'           => ['required','string','max:255'],
            'name'           => ['required','string','max:255'],
            'effective_from' => ['nullable','date'],
            'effective_to'   => ['nullable','date','after_or_equal:effective_from'],
            'is_default'     => ['nullable','boolean'],

            // Texto fuente para ES si decides crear desde admin (opcional)
            'title'          => ['nullable','string','max:255'],
            'content'        => ['nullable','string'],
        ]);

        return DB::transaction(function () use ($request) {
            $policy = Policy::create([
                'type'           => $request->string('type')->trim(),
                'name'           => $request->string('name')->trim(),
                'effective_from' => $request->input('effective_from'),
                'effective_to'   => $request->input('effective_to'),
                'is_default'     => $request->boolean('is_default'),
                'is_active'      => true,
            ]);

            // Única default (global; si prefieres por tipo, agrega ->where('type',$policy->type))
            if ($policy->is_default) {
                Policy::where('policy_id', '!=', $policy->policy_id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            // Si se envía contenido ES al crear desde admin, lo guardamos
            if ($request->filled('title') || $request->filled('content')) {
                PolicyTranslation::updateOrCreate(
                    ['policy_id' => $policy->policy_id, 'locale' => 'es'],
                    [
                        'title'   => $request->input('title', ''),
                        'content' => $request->input('content', ''),
                    ]
                );
            }

            return redirect()->route('admin.policies.index')
                ->with('success', 'Política creada correctamente.');
        });
    }

    public function update(Request $request, Policy $policy)
    {
        $request->validate([
            'type'           => ['required','string','max:255'],
            'name'           => ['required','string','max:255'],
            'effective_from' => ['nullable','date'],
            'effective_to'   => ['nullable','date','after_or_equal:effective_from'],
            'is_default'     => ['nullable','boolean'],
        ]);

        return DB::transaction(function () use ($request, $policy) {
            $policy->update([
                'type'           => $request->string('type')->trim(),
                'name'           => $request->string('name')->trim(),
                'effective_from' => $request->input('effective_from'),
                'effective_to'   => $request->input('effective_to'),
                'is_default'     => $request->boolean('is_default'),
            ]);

            if ($policy->is_default) {
                Policy::where('policy_id', '!=', $policy->policy_id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            return redirect()->route('admin.policies.index')
                ->with('success', 'Política actualizada correctamente.');
        });
    }

    public function toggleStatus(Policy $policy)
    {
        $policy->is_active = !$policy->is_active;
        $policy->save();

        return redirect()->route('admin.policies.index')
            ->with('success', 'Estado de la política actualizado.');
    }

    public function destroy(Policy $policy)
    {
        $policy->delete();

        return redirect()->route('admin.policies.index')
            ->with('success', 'Política eliminada correctamente.');
    }

    /**
     * (Opcional) Público — ahora sin slug.
     * Puedes mostrar por ID o por (type + default). Aquí muestro por ID.
     */
    public function showPublic(int $policyId)
    {
        $policy = Policy::with('translations')
            ->where('policy_id', $policyId)
            ->where('is_active', true)
            ->firstOrFail();

        return view('policies.show', compact('policy'));
    }
}
