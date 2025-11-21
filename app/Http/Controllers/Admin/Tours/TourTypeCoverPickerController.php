<?php

namespace App\Http\Controllers\Admin\Tours;

use App\Http\Controllers\Controller;
use App\Models\TourType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TourTypeCoverPickerController extends Controller
{
    /**
     * Listado de categorías (Half/Full/…) reutilizando el MISMO blade genérico (admin.tours.images.pick).
     * Enviamos $items, $idField, $nameField, $coverAccessor y $manageRoute.
     */
    public function pick(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        // Cargar traducciones para poder buscar por nombre
        $types = TourType::with(['translations' => fn($query) => $query->where('locale', app()->getLocale())])
            ->select('tour_type_id', 'cover_path', 'is_active')
            ->when($q !== '', function ($qr) use ($q) {
                // Buscar en traducciones
                $qr->whereHas('translations', function ($transQuery) use ($q) {
                    $transQuery->where('name', 'ILIKE', "%{$q}%");
                });

                // O por ID si es numérico
                if (is_numeric($q)) {
                    $qr->orWhere('tour_type_id', (int) $q);
                }
            })
            ->get()
            ->sortBy('name'); // Ordenar por nombre usando el accessor

        // Mapear cover_url para el blade
        $types->transform(function ($t) {
            $t->cover_url = $t->cover_path
                ? asset('storage/' . ltrim($t->cover_path, '/'))
                : asset('images/volcano.png');
            return $t;
        });

        // Paginar manualmente después de ordenar
        $perPage = 24;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $currentItems = $types->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedTypes = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $types->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
        $paginatedTypes->appends($request->query());

        // Reutiliza tu blade genérico (usa $items/$manageRoute)
        return view('admin.tours.images.pick', [
            'items'         => $paginatedTypes,
            'q'             => $q,
            'idField'       => 'tour_type_id',
            'nameField'     => 'name',
            'coverAccessor' => 'cover_url',
            // IMPORTANTE: apuntamos al edit de este mismo controlador
            'manageRoute'   => 'admin.types.images.edit',
            'i18n' => [
                'title'              => 'Categorias',
                'heading'            => 'Categorías',
                'choose'             => 'Elige una categoría para gestionar su cover',
                'search_placeholder' => 'Buscar categoría...',
                'search_button'      => 'Buscar',
                'no_results'         => 'No hay categorías.',
                'cover_alt'          => 'Cover de la categoría',
                'manage'             => 'Manage Images',
            ],
        ]);
    }

    /**
     * Formulario para subir/actualizar el cover de la categoría.
     */
    public function edit(TourType $tourType)
    {
        $coverUrl = $tourType->cover_url; // accessor del modelo
        return view('admin.tourtypes.edit-cover', compact('tourType', 'coverUrl'));
    }

    /**
     * Procesa la subida del cover (PUT).
     */
    public function updateCover(Request $request, TourType $tourType)
    {
        $maxSizeKb = (int) config('tours.max_image_kb', 30720); // 30MB por defecto

        $request->validate([
            'cover' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', "max:{$maxSizeKb}"],
        ]);

        $id  = (int) $tourType->tour_type_id;
        $dir = "types/{$id}";

        // Asegurar carpeta
        Storage::disk('public')->makeDirectory($dir);

        // (Opcional) limpiar anteriores
        foreach (Storage::disk('public')->files($dir) as $f) {
            Storage::disk('public')->delete($f);
        }

        // Guardar nuevo
        $ext      = strtolower($request->file('cover')->getClientOriginalExtension());
        $filename = 'cover-' . now()->format('YmdHis') . '.' . $ext;

        Storage::disk('public')->putFileAs($dir, $request->file('cover'), $filename);

        // Persistir ruta relativa
        $tourType->update(['cover_path' => $dir . '/' . $filename]);

        return back()->with('swal', [
            'icon'  => 'success',
            'title' => 'Cover actualizado',
            'text'  => 'La imagen de portada de la categoría se guardó correctamente.',
        ]);
    }
}
