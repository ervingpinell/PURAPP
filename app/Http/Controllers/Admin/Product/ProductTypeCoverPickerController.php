<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\TourType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * TourTypeCoverPickerController
 *
 * Handles tourtypecoverpicker operations.
 */
class ProductTypeCoverPickerController extends Controller
{
    /**
     * Listado de categorías (Half/Full/…) reutilizando el MISMO blade genérico (admin.tours.images.pick).
     * Enviamos $items, $idField, $nameField, $coverAccessor y $manageRoute.
     */
    public function pick(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        // Cargar traducciones para poder buscar por nombre
        $locale = app()->getLocale();

        // Cargar traducciones para poder buscar por nombre (ProductType uses Spatie)
        $types = \App\Models\ProductType::query()
            ->select('product_type_id as product_type_id', 'product_type_id', 'name', 'cover_path', 'is_active')
            ->when($q !== '', function ($qr) use ($q, $locale) {
                // Laravel JSON where syntax
                $qr->where("name->{$locale}", 'LIKE', "%{$q}%");

                // O por ID si es numérico
                if (is_numeric($q)) {
                    $qr->orWhere('product_type_id', (int) $q);
                }
            })
            ->get()
            ->sortBy('name'); // Ordenar por nombre (accessor returns string)

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
        return view('admin.products.images.pick', [
            'items'         => $paginatedTypes,
            'q'             => $q,
            'idField'       => 'product_type_id',
            'nameField'     => 'name',
            'coverAccessor' => 'cover_url',
            // IMPORTANTE: apuntamos al edit de este mismo controlador
            'manageRoute'   => 'admin.types.images.edit',
            'requiredPermission' => 'edit_cover-tour-images',
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
    public function edit(ProductType $tourType)
    {
        $coverUrl = $tourType->cover_url; // accessor defined in model? No, but we can compute it view side or dynamic prop. transform() above did it.
        // But edit() takes fresh model. ProductType has no getCoverUrlAttribute.
        // Let's manually compute it similar to pick()
        $coverUrl = $tourType->cover_path 
            ? asset('storage/' . ltrim($tourType->cover_path, '/')) 
            : asset('images/volcano.png');
            
        return view('admin.tourtypes.edit-cover', compact('tourType', 'coverUrl'));
    }

    /**
     * Procesa la subida del cover (PUT).
     */
    public function updateCover(Request $request, ProductType $tourType)
    {
        $maxSizeKb = (int) config('tours.max_image_kb', 30720); // 30MB por defecto

        $request->validate([
            'cover' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', "max:{$maxSizeKb}"],
        ]);

        $id  = (int) $tourType->product_type_id;
        $dir = "types/{$id}";

        // Asegurar carpeta
        Storage::disk('public')->makeDirectory($dir);

        // (Opcional) limpiar anteriores
        foreach (Storage::disk('public')->files($dir) as $f) {
            Storage::disk('public')->delete($f);
        }

        // Optimizar y guardar imagen con WebP
        $imageService = app(\App\Services\ImageOptimizationService::class);
        $paths = $imageService->optimizeAndSave($request->file('cover'), $dir);

        // Persistir ruta relativa (usamos la versión original, WebP se sirve automáticamente con <picture>)
        $tourType->update(['cover_path' => $paths['original']]);

        \App\Services\LoggerHelper::mutated(
            'TourTypeCoverPickerController',
            'updateCover',
            'tour_type',
            $id,
            [
                'cover_path' => $paths['original'],
                'webp_path' => $paths['webp'],
                'user_id'    => optional($request->user())->getAuthIdentifier(),
            ]
        );

        return back()->with('swal', [
            'icon'  => 'success',
            'title' => 'Cover actualizado',
            'text'  => 'La imagen de portada de la categoría se guardó y optimizó correctamente.',
        ]);
    }
}
