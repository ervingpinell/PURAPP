<?php

namespace App\Http\Controllers\Admin\Reviews;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reviews\UpsertProviderRequest;
use App\Models\ReviewProvider;
use App\Models\Review;
use App\Services\Reviews\ReviewAggregator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReviewProviderController extends Controller
{
    public function index(Request $r)
    {
        $providers = ReviewProvider::query()
            // Local primero
            ->orderByRaw("CASE WHEN slug = 'local' THEN 0 ELSE 1 END")
            ->when($r->filled('q'), function ($query) use ($r) {
                $qstr = trim((string) $r->get('q'));
                $query->where(function ($w) use ($qstr) {
                    // Usa LIKE para compatibilidad amplia (si usas Postgres puedes cambiar a ILIKE)
                    $w->where('name', 'like', "%{$qstr}%")
                      ->orWhere('slug', 'like', "%{$qstr}%")
                      ->orWhere('driver', 'like', "%{$qstr}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.providers.index', compact('providers'));
    }

    public function create()
    {
        // No permitimos crear 'local' desde aquí; ese se asegura en AppServiceProvider.
        return view('admin.reviews.providers.form', ['provider' => new ReviewProvider()]);
    }

public function store(UpsertProviderRequest $req)
{
    $data = $req->validated();

    // Bloqueo explícito: no se puede crear un proveedor con slug 'local'
    if (isset($data['slug']) && $data['slug'] === ReviewProvider::LOCAL_SLUG) {
        return back()->with('error', "El proveedor 'local' es de sistema y ya existe.")->withInput();
    }

    // Driver fijo para proveedores creados por el admin: http_json
    $data['driver'] = 'http_json';

    // Normaliza settings (acepta array o JSON string)
    $settings = $data['settings'] ?? [];
    if (is_string($settings)) {
        $decoded  = json_decode($settings, true);
        $settings = is_array($decoded) ? $decoded : [];
    }

    $data['settings']  = $settings;
    $data['indexable'] = (bool) ($data['indexable'] ?? false);
    $data['is_active'] = (bool) ($data['is_active'] ?? true);

    ReviewProvider::create($data);

    Cache::flush();

    // ⬅️ Redirige al listado
    return redirect()
        ->route('admin.review-providers.index')
        ->with('ok', __('reviews.providers.messages.created'));
}

    public function edit(ReviewProvider $provider)
    {
        return view('admin.reviews.providers.form', ['provider' => $provider]);
    }


public function update(UpsertProviderRequest $req, ReviewProvider $provider)
{
    $data = $req->validated();

    // Campos comunes y seguros
    $provider->name          = $data['name'];
    $provider->indexable     = (bool) ($data['indexable'] ?? $provider->indexable);
    $provider->is_active     = (bool) ($data['is_active']   ?? $provider->is_active);
    if (isset($data['cache_ttl_sec'])) {
        $provider->cache_ttl_sec = (int) $data['cache_ttl_sec'];
    }

    // === BLOQUE: proveedor LOCAL (o de sistema) ===
    if (($provider->is_system ?? false) || $provider->slug === ReviewProvider::LOCAL_SLUG) {
        // Fuerza atributos inmutables para "local"
        $provider->slug      = ReviewProvider::LOCAL_SLUG;
        $provider->driver    = 'local';
        $provider->is_system = true;
        $provider->is_active = true;

        // Ajuste de min_stars desde el formulario (sin exponer/usar JSON)
        $settings = is_array($provider->settings) ? $provider->settings : [];
        $min      = isset($data['min_stars']) ? (int) $data['min_stars'] : (int) ($settings['min_stars'] ?? 0);
        $settings['min_stars'] = max(0, min(5, $min));
        $provider->settings    = $settings;

        // Ignoramos cualquier "settings" genérico que llegue del request para "local"
    }
    // === BLOQUE: proveedor EXTERNO (http_json) ===
    else {
        // Driver externo
        $provider->driver = 'http_json';

        // Guardar settings del request (ya normalizado por el FormRequest)
        if (array_key_exists('settings', $data) && is_array($data['settings'])) {
            // El mutator del modelo cifra automáticamente claves sensibles (api_key, etc.)
            $provider->settings = $data['settings'];
        }
    }

    $provider->save();

    Cache::flush();

    // ⬅️ Redirige al listado
    return redirect()
        ->route('admin.review-providers.index')
        ->with('ok', __('reviews.providers.messages.updated'));
}


    /**
     * Elimina un proveedor de reseñas.
     * - No elimina proveedores de sistema ni el slug 'local'.
     * - Si tiene reseñas asociadas, bloquea la eliminación.
     */
    public function destroy(Request $request, ReviewProvider $provider)
    {
        $this->authorize('delete', $provider);

        if (($provider->is_system ?? false) || $provider->slug === 'local') {
            return back()->with('error', 'Este proveedor es de sistema y no puede eliminarse.');
        }

        $count = Review::where('provider', $provider->slug)->count();
        if ($count > 0) {
            return back()->with(
                'error',
                "No se puede eliminar: hay {$count} reseñas asociadas a este proveedor. Desactívalo primero o elimina/mueve esas reseñas."
            );
        }

        $provider->delete();

        return back()->with('ok', __('reviews.providers.messages.deleted'));
    }

    /**
     * Activar / desactivar proveedor.
     * No permite desactivar el proveedor local/sistema.
     */
    public function toggle(ReviewProvider $provider)
    {
        if (($provider->is_system ?? false) || $provider->slug === 'local') {
            return back()->with('error', 'El proveedor local/sistema no puede desactivarse.');
        }

        $provider->is_active = ! $provider->is_active;
        $provider->save();

        Cache::flush();

        return back()->with('ok', __('reviews.providers.messages.status_updated'));
    }

    /**
     * Flush completo del cache (útil al cambiar settings).
     */
    public function flushCache(ReviewProvider $provider)
    {
        Cache::flush();

        return back()->with('ok', __('reviews.providers.messages.cache_flushed'));
    }

    /**
     * Probar el fetch de un proveedor (no persiste nada).
     */
    public function test(ReviewProvider $provider, ReviewAggregator $agg)
    {
        try {
            $rows = $agg->aggregate(['provider' => $provider->slug, 'limit' => 3]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Fallo al probar el proveedor: '.$e->getMessage());
        }

        // Soporta array o Collection
        $n = is_countable($rows) ? count($rows) : (method_exists($rows, 'count') ? $rows->count() : 0);

        return back()->with('ok', __('reviews.providers.messages.test_fetched', [
            'n' => $n,
        ]));
    }
}
