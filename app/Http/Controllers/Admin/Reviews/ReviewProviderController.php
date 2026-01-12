<?php

namespace App\Http\Controllers\Admin\Reviews;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reviews\UpsertProviderRequest;
use App\Models\ReviewProvider;
use App\Models\Review;
use App\Services\Reviews\ReviewAggregator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * ReviewProviderController
 *
 * Handles reviewprovider operations.
 */
class ReviewProviderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-review-providers'])->only(['index']);
        $this->middleware(['can:create-review-providers'])->only(['create', 'store']);
        $this->middleware(['can:edit-review-providers'])->only(['edit', 'update', 'flushCache', 'test']);
        $this->middleware(['can:publish-review-providers'])->only(['toggle']);
        $this->middleware(['can:delete-review-providers'])->only(['destroy']);
    }

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

        // Build settings from simplified form fields or advanced JSON
        $settings = $this->buildSettings($data);

        $data['settings']  = $settings;
        $data['indexable'] = (bool) ($data['indexable'] ?? false);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        ReviewProvider::create($data);

        Cache::flush();

        // Redirige al listado
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

            // Build settings from simplified form fields or advanced JSON
            $settings = $this->buildSettings($data, $provider->settings ?? []);
            $provider->settings = $settings;
        }

        $provider->save();

        Cache::flush();

        // Redirige al listado
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
            return back()->with('error', 'Fallo al probar el proveedor: ' . $e->getMessage());
        }

        // Soporta array o Collection
        $n = is_countable($rows) ? count($rows) : (method_exists($rows, 'count') ? $rows->count() : 0);

        return back()->with('ok', __('reviews.providers.messages.test_fetched', [
            'n' => $n,
        ]));
    }

    /**
     * Build settings array from simplified form fields or advanced JSON
     */
    protected function buildSettings(array $data, array $existing = []): array
    {
        // If advanced JSON is provided, use it
        if (!empty($data['settings_json'])) {
            $decoded = json_decode($data['settings_json'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            // If JSON is invalid, fall back to existing
            return $existing;
        }

        // For external providers, keep existing settings (managed via .env)
        // Only allow editing via advanced JSON
        return $existing;
    }
}
