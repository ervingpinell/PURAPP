<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\ProductType; // Fixed missing import
use Illuminate\Http\Request;
// Removed unused Storage import

/**
 * ProductTypeCoverPickerController
 *
 * Handles product type cover picker operations.
 */
class ProductTypeCoverPickerController extends Controller
{
    /**
     * Listado de categorías (Half/Full/…) reutilizando el MISMO blade genérico (admin.products.images.pick).
     * Enviamos $items, $idField, $nameField, $coverAccessor y $manageRoute.
     */
    public function pick(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        // Cargar traducciones para poder buscar por nombre
        $locale = app()->getLocale();

        // Cargar traducciones para poder buscar por nombre (ProductType uses Spatie)
        $types = ProductType::query()
            ->select('product_type_id', 'name', 'cover_path', 'is_active') // Keep cover_path for fallback if needed
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

        // Mapear cover_url para el blade using Spatie with fallback
        $types->transform(function ($t) {
            // Priority: 1. Spatie Media 'cover' 2. Old cover_path 3. Placeholder
            if ($t->hasMedia('cover')) {
                $t->cover_url = $t->getFirstMediaUrl('cover');
            } elseif ($t->cover_path) {
                $t->cover_url = asset('storage/' . ltrim($t->cover_path, '/'));
            } else {
                $t->cover_url = asset('images/volcano.png');
            }
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
            'requiredPermission' => 'edit_cover-product-images',
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
    public function edit(ProductType $productType) // Corrected variable name for consistency, though route binding uses {productType}
    {
        // Spatie with fallback
        if ($productType->hasMedia('cover')) {
            $coverUrl = $productType->getFirstMediaUrl('cover');
        } elseif ($productType->cover_path) {
            $coverUrl = asset('storage/' . ltrim($productType->cover_path, '/'));
        } else {
            $coverUrl = asset('images/volcano.png');
        }
            
        // Pass productType to view
        return view('admin.producttypes.edit-cover', compact('productType', 'coverUrl'));
    }

    /**
     * Procesa la subida del cover (PUT).
     */
    public function updateCover(Request $request, ProductType $productType)
    {
        $maxSizeKb = (int) config('products.max_image_kb', 30720); // 30MB por defecto

        $request->validate([
            'cover' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', "max:{$maxSizeKb}"],
        ]);

        try {
            // Spatie handles upload, conversion (if configured), and storage
            $productType->addMediaFromRequest('cover')
                        ->toMediaCollection('cover');

            // Optimizar y guardar imagen con WebP is handled by Spatie if configured, 
            // or we accept standard upload. The user said "use spatie tambien".
            
            // We do NOT update 'cover_path' anymore as Spatie manages the path.
            // But for backward compat, if other parts of the app use cover_path directly 
            // without accessing cover_url, they might break. 
            // However, the task is to refactor to Spatie.
            // I will clear cover_path to indicate it's now managed by Spatie if desired, 
            // or leave it as stale data. 
            // Better to clear it so fallbacks don't confusingly show old image if Spatie fails?
            // Actually, if we add media, Spatie works.
            $productType->update(['cover_path' => null]); // Optional clean up

            \App\Services\LoggerHelper::mutated(
                'ProductTypeCoverPickerController', // updated controller name in text
                'updateCover',
                'product_type', // updated from product_type
                $productType->product_type_id,
                [
                    'media_action' => 'check_spatie_media_table',
                    'user_id'    => optional($request->user())->getAuthIdentifier(),
                ]
            );

            return back()->with('swal', [
                'icon'  => 'success',
                'title' => 'Cover actualizado',
                'text'  => 'La imagen de portada de la categoría se guardó correctamente (Spatie).',
            ]);

        } catch (\Exception $e) {
             return back()->with('swal', [
                'icon'  => 'error',
                'title' => 'Error',
                'text'  => 'Error al subir imagen: ' . $e->getMessage(),
            ]);
        }
    }
}
