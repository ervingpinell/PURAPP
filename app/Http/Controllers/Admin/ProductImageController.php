<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Rules\MaxVideoDuration;

// === WebP (Intervention Image) ===
use Intervention\Image\Laravel\Facades\Image as Img;
use Intervention\Image\Encoders\WebpEncoder;
use App\Services\LoggerHelper;

/**
 * TourImageController
 *
 * Handles tourimage operations.
 */
class ProductImageController extends Controller
{
    /** List images for a tour. */
    public function index(Product $product)
    {
        try {
            $product->load(['images']);
            $maxImagesPerTour = (int) config('tours.max_images_per_tour', 20);

            return view('admin.products.images', [
                'product' => $product,
                'max'  => $maxImagesPerTour,
            ]);
        } catch (Throwable $e) {
            Log::error('Image index failed', [
                'user_id' => optional(Auth::user())->id,
                'error'   => $e->getMessage(),
            ]);

            return back()->with('swal', [
                'icon'  => 'error',
                'title' => __('m_tours.image.ui.error_title'),
                'text'  => __('m_tours.image.errors.load_list'),
            ]);
        }
    }

    /** Upload images for a tour (respecting per-tour limit) and convert to WebP. */
    public function store(Request $request, Product $product)
    {
        try {
            $maxImageKb        = (int) config('tours.max_image_kb', 30720); // 30MB/file
            $maxImagesPerTour  = (int) config('tours.max_images_per_tour', 20);
            $webpCfg           = (array) config('tours.webp', []);
            $quality           = (int) ($webpCfg['quality'] ?? 82);
            $maxSide           = (int) ($webpCfg['max_side'] ?? 2560);

            // 1. Validar que exista el array 'files', pero NO validar contenido aquí (para partial uploads)
            $request->validate([
                'files'   => ['required', 'array'],
            ]);

            $uploadedFiles = $request->file('files', []);
            if (!is_array($uploadedFiles) || empty($uploadedFiles)) {
                return back()->with('swal', [
                    'icon'  => 'info',
                    'title' => __('m_tours.image.notice'),
                    'text'  => __('m_tours.image.upload_none'),
                ]);
            }

            // 2. Verificar cupo global
            $currentCount = (int) $product->images()->count();
            $remainingCap = max(0, $maxImagesPerTour - $currentCount);

            if ($remainingCap <= 0) {
                return back()->with('swal', [
                    'icon'  => 'warning',
                    'title' => __('m_tours.image.limit_reached_title'),
                    'text'  => __('m_tours.image.limit_reached_text'),
                ]);
            }

            // 3. Truncar si exceden el cupo restante
            $truncatedForLimit = false;
            if (count($uploadedFiles) > $remainingCap) {
                $uploadedFiles     = array_slice($uploadedFiles, 0, $remainingCap);
                $truncatedForLimit = true;
            }

            // 3. Validar cada archivo individualmente
            $validFiles   = [];
            $invalidFiles = [];

            foreach ($uploadedFiles as $file) {
                // Validar tipo MIME (incluyendo HEIC/HEIF con múltiples variantes)
                $allowedMimes = [
                    'image/jpeg',
                    'image/jpg',
                    'image/png',
                    'image/webp',
                    'image/heic',
                    'image/heif',
                    'image/heic-sequence',
                    'image/heif-sequence',
                    // Algunos navegadores/sistemas usan estos MIME types para HEIC
                    'application/octet-stream', // Fallback común para HEIC
                ];


                $fileMime = $file->getMimeType();
                $fileExt  = strtolower($file->getClientOriginalExtension());
                
                // Rechazar HEIC/HEIF explícitamente con mensaje útil
                if (in_array($fileExt, ['heic', 'heif'])) {
                    $invalidFiles[] = [
                        'name'   => $file->getClientOriginalName(),
                        'reason' => 'HEIC no soportado. Por favor convierte la imagen a JPG o PNG antes de subirla.',
                    ];
                    continue;
                }
                
                // Aceptar si el MIME type es válido O si la extensión es válida
                $isValidType = in_array($fileMime, $allowedMimes) || 
                               in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp']);

                if (!$isValidType) {
                    $invalidFiles[] = [
                        'name'   => $file->getClientOriginalName(),
                        'reason' => __('m_tours.image.errors.invalid_type'),
                    ];
                    continue;
                }


                // Validar tamaño
                $fileSizeKb = (int) ($file->getSize() / 1024);
                if ($fileSizeKb > $maxImageKb) {
                    $invalidFiles[] = [
                        'name'   => $file->getClientOriginalName(),
                        'reason' => __('m_tours.image.errors.too_large', ['max' => $maxImageKb]),
                    ];
                    continue;
                }

                // Validar que sea imagen real (solo para formatos soportados por getimagesize)
                // HEIC/HEIF no son soportados por getimagesize(), así que los omitimos
                if (!in_array($fileExt, ['heic', 'heif'])) {
                    try {
                        $imageInfo = @getimagesize($file->getRealPath());
                        if (!$imageInfo) {
                            $invalidFiles[] = [
                                'name'   => $file->getClientOriginalName(),
                                'reason' => __('m_tours.image.errors.not_image'),
                            ];
                            continue;
                        }
                    } catch (\Throwable $e) {
                        $invalidFiles[] = [
                            'name'   => $file->getClientOriginalName(),
                            'reason' => __('m_tours.image.errors.not_image'),
                        ];
                        continue;
                    }
                }

                $validFiles[] = $file;
            }

            $storageFolder   = "tours/{$product->product_id}/gallery";
            $nextPosition    = (int) ($product->images()->max('position') ?? 0);
            $createdCount    = 0;
            $newImageRecords = [];
            
            // Copiar invalidFiles a skippedFiles para mostrar en el mensaje
            $skippedFiles = [];
            foreach ($invalidFiles as $invalidFile) {
                $skippedFiles[] = "{$invalidFile['name']} ({$invalidFile['reason']})";
            }

            // 4. Procesar uno a uno
            foreach ($validFiles as $uploaded) { // Iterate over validFiles
                $fileName = $uploaded->getClientOriginalName();

                // 4.1. Validar archivo individual (removed, now done before loop)
                // The previous validation block is removed here.

                $inputExt = strtolower((string) $uploaded->getClientOriginalExtension());
                $basename = pathinfo($fileName, PATHINFO_FILENAME);
                $basename = Str::slug($basename) ?: 'image';
                $stamp    = now()->format('YmdHis') . '-' . Str::random(6);

                $finalPath = null;

                // 4.2. Intentar convertir a WebP
                try {
                    $img = Img::read($uploaded->getRealPath())->orient();
                    if ($maxSide > 0) {
                        $img->scaleDown($maxSide);
                    }

                    $webpName = "{$basename}-{$stamp}.webp";
                    $webpPath = "{$storageFolder}/{$webpName}";

                    $encoded = $img->encode(new WebpEncoder(quality: $quality));
                    Storage::disk('public')->put($webpPath, (string) $encoded);

                    $finalPath = $webpPath;
                } catch (\Throwable $e) {
                    // Fallback
                    Log::error('WebP encode failed, falling back to original', [
                        'product_id' => $product->product_id,
                        'file'    => $fileName,
                        'error'   => $e->getMessage(),
                    ]);

                    $origName = "{$basename}-{$stamp}.{$inputExt}";
                    Storage::disk('public')->putFileAs($storageFolder, $uploaded, $origName);
                    $finalPath = "{$storageFolder}/{$origName}";
                }

                // 4.3. Crear registro
                $image = ProductImage::create([
                    'product_id'  => $product->product_id,
                    'path'     => $finalPath,
                    'caption'  => null,
                    'position' => ++$nextPosition,
                    'is_cover' => false,
                ]);

                $newImageRecords[] = $image;
                $createdCount++;
            }

            // 5. Asignar portada si no había
            if (!$product->coverImage && isset($newImageRecords[0])) {
                $newImageRecords[0]->update(['is_cover' => true]);
            }


            LoggerHelper::mutated('TourImageController', 'store', 'Tour', $product->product_id, ['count' => $createdCount]);

            // 6. Construir mensaje de respuesta con SweetAlert
            
            // Caso A: Todos los archivos fueron rechazados
            if ($createdCount === 0 && !empty($skippedFiles)) {
                $errorList = '<ul style="text-align: left; margin: 10px 0;">';
                foreach ($skippedFiles as $skipped) {
                    $errorList .= "<li style='margin: 5px 0;'>{$skipped}</li>";
                }
                $errorList .= '</ul>';

                return back()->with('swal', [
                    'icon'  => 'error',
                    'title' => 'No se subieron imágenes',
                    'html'  => '<div style="text-align: center;">Los siguientes archivos fueron rechazados:</div>' . $errorList,
                ]);
            }

            // Caso B: Algunos archivos se subieron, otros fueron rechazados
            if ($createdCount > 0 && !empty($skippedFiles)) {
                $errorList = '<ul style="text-align: left; margin: 10px 0;">';
                foreach ($skippedFiles as $skipped) {
                    $errorList .= "<li style='margin: 5px 0;'>{$skipped}</li>";
                }
                $errorList .= '</ul>';

                return back()->with('swal', [
                    'icon'  => 'warning',
                    'title' => "✓ Se subieron {$createdCount} imagen(es)",
                    'html'  => '<div style="text-align: center;">Pero los siguientes archivos fueron rechazados:</div>' . $errorList,
                ]);
            }

            // Caso C: Todos los archivos se subieron correctamente
            $message = $createdCount === 1
                ? '¡Imagen subida correctamente!'
                : "¡Se subieron {$createdCount} imágenes correctamente!";

            if ($truncatedForLimit) {
                $message .= ' (Se alcanzó el límite máximo de imágenes)';
            }


            return back()->with('swal', [
                'icon'  => 'success',
                'title' => '¡Listo!',
                'text'  => $message,
            ]);

        } catch (ValidationException $e) {
            // Este catch atrapa fallos del $request->validate initial
            return back()->with('swal', [
                'icon'  => 'warning',
                'title' => __('Error de validación'),
                'text'  => collect($e->errors())->flatten()->first() ?? 'Datos inválidos',
            ])->withInput();
        } catch (\Throwable $e) {
            Log::error('Image upload failed (critical)', [
                'product_id' => $product->product_id ?? null,
                'error'   => $e->getMessage(),
            ]);
            return back()->with('swal', [
                'icon'  => 'error',
                'title' => __('Error del servidor'),
                'text'  => __('Ocurrió un error inesperado al procesar las imágenes.'),
            ])->withInput();
        }
    }


    /** Update an image (caption only for now). */
    public function update(Request $request, Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->product_id, 404);

        try {
            $data = $request->validate([
                'caption' => ['nullable', 'string', 'max:200'],
            ]);

            $image->update($data);

            LoggerHelper::mutated('TourImageController', 'update', 'TourImage', $image->id);

            return back()->with('swal', [
                'icon'  => 'success',
                'title' => __('m_tours.image.saved'),
                'text'  => __('m_tours.image.caption_updated'),
            ]);
        } catch (ValidationException $e) {
            Log::warning('Image update validation failed', [
                'image_id' => $image->id,
                'product_id'  => $product->product_id,
                'user_id'  => optional(Auth::user())->id,
                'errors'   => $e->errors(),
            ]);

            $firstMsg = collect($e->errors())->flatten()->first() ?? __('m_tours.image.errors.validation');
            return back()->with('swal', [
                'icon'  => 'warning',
                'title' => __('m_tours.image.ui.warning_title'),
                'text'  => $firstMsg,
            ])->withInput();
        } catch (Throwable $e) {
            Log::error('Image update failed', [
                'image_id' => $image->id,
                'product_id'  => $product->product_id,
                'user_id'  => optional(Auth::user())->id,
                'error'    => $e->getMessage(),
            ]);

            return back()->with('swal', [
                'icon'  => 'error',
                'title' => __('m_tours.image.ui.error_title'),
                'text'  => __('m_tours.image.errors.update_caption'),
            ]);
        }
    }

    /** Delete an image. If it was cover, set next image as cover. */
    public function destroy(Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->product_id, 404);

        try {
            $wasCover = (bool) $image->is_cover;

            if ($image->path && Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            $image->delete();

            if ($wasCover) {
                $nextCover = $product->images()->orderBy('position')->first();
                if ($nextCover) {
                    $nextCover->update(['is_cover' => true]);
                }
            }

            LoggerHelper::mutated('TourImageController', 'destroy', 'TourImage', $image->id);

            return back()->with('swal', [
                'icon'  => 'success',
                'title' => __('m_tours.image.deleted'),
                'text'  => __('m_tours.image.image_removed'),
            ]);
        } catch (Throwable $e) {
            Log::error('Image delete failed', [
                'image_id' => $image->id ?? null,
                'product_id'  => $product->product_id ?? null,
                'user_id'  => optional(Auth::user())->id,
                'error'    => $e->getMessage(),
            ]);

            return back()->with('swal', [
                'icon'  => 'error',
                'title' => __('m_tours.image.ui.error_title'),
                'text'  => __('m_tours.image.errors.delete'),
            ]);
        }
    }

    /** Reorder images (expects `order` = [imageId1, imageId2, ...]). */
    public function reorder(Request $request, Product $product)
    {
        try {
            $orderIds = $request->input('order', []);
            if (!is_array($orderIds) || empty($orderIds)) {
                return $this->reorderResponse($request, false, __('m_tours.image.invalid_order'));
            }

            $validIds = $product->images()->pluck('id')->all();
            $newOrder = array_values(array_intersect($orderIds, $validIds));
            if (empty($newOrder)) {
                return $this->reorderResponse($request, false, __('m_tours.image.nothing_to_reorder'));
            }

            DB::transaction(function () use ($newOrder) {
                foreach ($newOrder as $pos => $id) {
                    ProductImage::where('id', $id)->update(['position' => $pos + 1]);
                }
            });

            LoggerHelper::mutated('TourImageController', 'reorder', 'Tour', $product->product_id);

            return $this->reorderResponse($request, true, __('m_tours.image.order_saved'));
        } catch (Throwable $e) {
            Log::error('Image reorder failed', [
                'product_id' => $product->product_id ?? null,
                'user_id' => optional(Auth::user())->id,
                'error'   => $e->getMessage(),
            ]);

            return $this->reorderResponse($request, false, __('m_tours.image.errors.reorder'));
        }
    }

    protected function reorderResponse(Request $request, bool $ok, string $message)
    {
        if ($request->wantsJson()) {
            return response()->json(['ok' => $ok, 'message' => $message], $ok ? 200 : 422);
        }

        return back()->with('swal', [
            'icon'  => $ok ? 'success' : 'warning',
            'title' => $ok ? __('m_tours.image.done') : __('m_tours.image.notice'),
            'text'  => $message,
        ]);
    }

    /** Set an image as cover. */
    public function setCover(Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->product_id, 404);

        try {
            DB::transaction(function () use ($product, $image) {
                ProductImage::where('product_id', $product->product_id)->update(['is_cover' => false]);
                $image->update(['is_cover' => true]);
            });

            LoggerHelper::mutated('TourImageController', 'setCover', 'TourImage', $image->id);

            return back()->with('swal', [
                'icon'  => 'success',
                'title' => __('m_tours.image.cover_updated_title'),
                'text'  => __('m_tours.image.cover_updated_text'),
            ]);
        } catch (Throwable $e) {
            Log::error('Image setCover failed', [
                'image_id' => $image->id ?? null,
                'product_id'  => $product->product_id ?? null,
                'user_id'  => optional(Auth::user())->id,
                'error'    => $e->getMessage(),
            ]);

            return back()->with('swal', [
                'icon'  => 'error',
                'title' => __('m_tours.image.ui.error_title'),
                'text'  => __('m_tours.image.errors.set_cover'),
            ]);
        }
    }

    /** Picker: list tours to manage their images. */
    public function pick(Request $request)
    {
        try {
            $q = trim((string) $request->query('q', ''));
            $locale = app()->getLocale();

            $tours = Product::select('product_id', 'name')
                ->with(['coverImage:id,product_id,path,is_cover'])
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($sub) use ($q) {
                        $sub->where('name', 'ILIKE', "%{$q}%");
                        if (is_numeric($q)) {
                            $sub->orWhere('product_id', (int) $q);
                        }
                    });
                })
                ->orderByRaw("name->>'$locale' ASC")
                ->paginate(24)
                ->withQueryString();

            // Compute cover_url
            $tours->getCollection()->transform(function ($product) {
                $product->cover_url = optional($product->coverImage)->url ?? asset('images/volcano.png');
                return $product;
            });

            return view('admin.products.images.pick', [
                'items'         => $tours,
                'q'             => $q,
                'idField'       => 'product_id',
                'nameField'     => 'name',
                'coverAccessor' => 'cover_url',
                'manageRoute'   => 'admin.products.images.index',
                'requiredPermission' => 'view-tour-images',
                'i18n'          => [
                    'title'   => __('m_tours.image.ui.page_title_pick'),
                    'heading' => __('m_tours.image.ui.page_heading'),
                    'choose'  => __('m_tours.image.ui.choose_tour'),
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('Image pick failed', [
                'user_id' => optional(Auth::user())->id,
                'error'   => $e->getMessage(),
            ]);

            return back()->with('swal', [
                'icon'  => 'error',
                'title' => __('m_tours.image.ui.error_title'),
                'text'  => __('m_tours.image.errors.load_list'),
            ]);
        }
    }

    /** Helper: find a "cover-like" image from storage if no DB cover is set. */
    protected function coverFromStorage(int $tourId): string
    {
        try {
            $folder = "tours/{$tourId}/gallery";
            if (!Storage::disk('public')->exists($folder)) {
                return asset('images/volcano.png');
            }

            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'JPG', 'JPEG', 'PNG', 'WEBP'];

            $first = collect(Storage::disk('public')->files($folder))
                ->filter(fn($path) => in_array(pathinfo($path, PATHINFO_EXTENSION), $allowed, true))
                ->sort(fn($a, $b) => strnatcasecmp($a, $b))
                ->first();

            return $first ? asset('storage/' . $first) : asset('images/volcano.png');
        } catch (Throwable $e) {
            Log::warning('coverFromStorage failed; returning default', [
                'product_id' => $tourId,
                'error'   => $e->getMessage(),
            ]);
            return asset('images/volcano.png');
        }
    }

    /* =========================================================
     *           👇👇  MÉTODOS NUEVOS A PARTIR DE AQUÍ  👇👇
     * ========================================================= */

    /**
     * Convierte /storage/... a ruta relativa del disco 'public'
     * p.ej. /storage/tours/1/gallery/img.webp -> tours/1/gallery/img.webp
     */
    private function pathFromUrl(?string $url): ?string
    {
        if (!$url) return null;
        $path = parse_url($url, PHP_URL_PATH) ?? $url;
        return ltrim(str_replace('/storage/', '', $path), '/');
    }

    /**
     * DELETE admin.tours.images.bulk-destroy
     * Recibe "ids" como string "1,2,3" o como array ["1","2","3"]
     */
    public function bulkDestroy(Request $request, Product $product)
    {
        $ids = $request->input('ids');

        // Permitir array o string
        if (is_string($ids)) {
            $ids = collect(explode(',', $ids))->map('trim')->filter()->values();
        } else {
            $ids = collect((array)$ids)->map('trim')->filter()->values();
        }

        if ($ids->isEmpty()) {
            return back()->with('swal', [
                'icon'  => 'warning',
                'title' => __('m_tours.image.notice'),
                'text'  => __('No hay imágenes seleccionadas.'),
            ]);
        }

        // Solo imágenes del tour actual
        $images = ProductImage::query()
            ->where('product_id', $product->getKey())
            ->whereIn('id', $ids->all())
            ->get();

        if ($images->isEmpty()) {
            return back()->with('swal', [
                'icon'  => 'info',
                'title' => __('m_tours.image.notice'),
                'text'  => __('No se encontraron imágenes válidas para eliminar.'),
            ]);
        }

        DB::transaction(function () use ($images) {
            // Borrar archivos físicos
            $paths = $images->map(function ($img) {
                // Si tu modelo ya guarda path relativo, usa $img->path directamente
                return $img->path ?: $this->pathFromUrl($img->url ?? null);
            })->filter()->values()->all();

            if (!empty($paths)) {
                Storage::disk('public')->delete($paths);
            }

            // Borrar registros
            ProductImage::whereIn('id', $images->pluck('id'))->delete();
        });

        LoggerHelper::mutated('TourImageController', 'bulkDestroy', 'Tour', $product->product_id, ['count' => $images->count()]);

        return back()->with('swal', [
            'icon'  => 'success',
            'title' => __('m_tours.image.deleted'),
            'text'  => __('Se eliminaron :n imágenes seleccionadas.', ['n' => $images->count()]),
        ]);
    }

    /**
     * DELETE admin.tours.images.destroyAll
     * Elimina TODAS las imágenes del tour.
     */
    public function destroyAll(Request $request, Product $product)
    {
        $images = ProductImage::query()
            ->where('product_id', $product->getKey())
            ->get();

        if ($images->isEmpty()) {
            return back()->with('swal', [
                'icon'  => 'info',
                'title' => __('m_tours.image.notice'),
                'text'  => __('Este tour no tiene imágenes para eliminar.'),
            ]);
        }

        DB::transaction(function () use ($images, $tour) {
            // Borrar archivos físicos
            $paths = $images->map(function ($img) {
                return $img->path ?: $this->pathFromUrl($img->url ?? null);
            })->filter()->values()->all();

            if (!empty($paths)) {
                Storage::disk('public')->delete($paths);
            }

            // Borrar registros
            ProductImage::where('product_id', $product->getKey())->delete();
        });

        LoggerHelper::mutated('TourImageController', 'destroyAll', 'Tour', $product->product_id);

        return back()->with('swal', [
            'icon'  => 'success',
            'title' => __('m_tours.image.deleted'),
            'text'  => __('Se eliminaron todas las imágenes del tour.'),
        ]);
    }

    /**
     * Upload videos using Spatie Media Library
     */
    public function storeVideo(Request $request, Product $product)
    {
        try {
            $maxVideosPerProduct = 3;

            $request->validate([
                'files' => ['required', 'array'],
                'files.*' => [
                    'file',
                    'mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo',
                    'max:409600', // 400MB
                    new MaxVideoDuration(120), // 2 minutes
                ],
            ]);

            $uploadedFiles = $request->file('files', []);
            if (empty($uploadedFiles)) {
                return back()->with('swal', [
                    'icon' => 'info',
                    'title' => 'Sin archivos',
                    'text' => 'No se seleccionaron videos.',
                ]);
            }

            $currentCount = $product->getMedia('videos')->count();
            $remainingCap = max(0, $maxVideosPerProduct - $currentCount);

            if ($remainingCap <= 0) {
                return back()->with('swal', [
                    'icon' => 'warning',
                    'title' => 'Límite alcanzado',
                    'text' => "Máximo {$maxVideosPerProduct} videos por producto",
                ]);
            }

            if (count($uploadedFiles) > $remainingCap) {
                $uploadedFiles = array_slice($uploadedFiles, 0, $remainingCap);
            }

            $createdCount = 0;
            foreach ($uploadedFiles as $video) {
                try {
                    $product->addMedia($video)->toMediaCollection('videos');
                    $createdCount++;
                } catch (\Throwable $e) {
                    Log::error('Video upload failed', [
                        'product_id' => $product->product_id,
                        'file' => $video->getClientOriginalName(),
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return back()->with('swal', [
                'icon' => 'success',
                'title' => '¡Listo!',
                'text' => "Se subieron {$createdCount} videos correctamente.",
            ]);

        } catch (\Throwable $e) {
            Log::error('Video upload failed', [
                'product_id' => $product->product_id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('swal', [
                'icon' => 'error',
                'title' => 'Error al subir videos',
                'text' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete a video from product
     */
    public function destroyVideo(Product $product, $mediaId)
    {
        try {
            $media = $product->getMedia('videos')->where('id', $mediaId)->first();
            
            if (!$media) {
                return back()->with('swal', [
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => 'Video no encontrado.',
                ]);
            }

            $media->delete();

            return back()->with('swal', [
                'icon' => 'success',
                'title' => '¡Eliminado!',
                'text' => 'Video eliminado correctamente.',
            ]);

        } catch (\Throwable $e) {
            Log::error('Video deletion failed', [
                'product_id' => $product->product_id,
                'media_id' => $mediaId,
                'error' => $e->getMessage(),
            ]);

            return back()->with('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el video.',
            ]);
        }
    }
}
