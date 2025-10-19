<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Throwable;

// === WebP (Intervention Image) ===
use Intervention\Image\Laravel\Facades\Image as Img;
use Intervention\Image\Encoders\WebpEncoder;

class TourImageController extends Controller
{
    /** List images for a tour. */
    public function index(Tour $tour)
    {
        try {
            $tour->load(['images', 'coverImage']);
            $maxImagesPerTour = (int) config('tours.max_images_per_tour', 20);

            return view('admin.tours.images', [
                'tour' => $tour,
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
    public function store(Request $request, Tour $tour)
    {
        try {
            $maxImageKb        = (int) config('tours.max_image_kb', 30720); // 30MB/file
            $maxImagesPerTour  = (int) config('tours.max_images_per_tour', 20);
            $webpCfg           = (array) config('tours.webp', []);
            $quality           = (int) ($webpCfg['quality'] ?? 82);
            $maxSide           = (int) ($webpCfg['max_side'] ?? 2560);

            $validated = $request->validate([
                'files'   => ['required', 'array'],
                'files.*' => ['image', 'mimes:jpeg,jpg,png,webp', "max:{$maxImageKb}"],
            ]);

            $currentCount = (int) $tour->images()->count();
            $remainingCap = max(0, $maxImagesPerTour - $currentCount);
            $uploadedFiles = $request->file('files', []);

            if ($remainingCap <= 0) {
                return back()->with('swal', [
                    'icon'  => 'warning',
                    'title' => __('m_tours.image.limit_reached_title'),
                    'text'  => __('m_tours.image.limit_reached_text'),
                ]);
            }

            if (!is_array($uploadedFiles) || empty($uploadedFiles)) {
                return back()->with('swal', [
                    'icon'  => 'info',
                    'title' => __('m_tours.image.notice'),
                    'text'  => __('m_tours.image.upload_none'),
                ]);
            }

            $truncatedForLimit = false;
            if (count($uploadedFiles) > $remainingCap) {
                $uploadedFiles     = array_slice($uploadedFiles, 0, $remainingCap);
                $truncatedForLimit = true;
            }

            $storageFolder   = "tours/{$tour->tour_id}/gallery";
            $nextPosition    = (int) ($tour->images()->max('position') ?? 0);
            $createdCount    = 0;
            $newImageRecords = [];

            foreach ($uploadedFiles as $uploaded) {
                $inputExt = strtolower((string) $uploaded->getClientOriginalExtension());
                if (!in_array($inputExt, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                    Log::warning('Skipped file due to invalid extension', [
                        'tour_id' => $tour->tour_id,
                        'file'    => $uploaded->getClientOriginalName(),
                        'ext'     => $inputExt,
                        'user_id' => optional(Auth::user())->id,
                    ]);
                    continue;
                }

                $basename = pathinfo($uploaded->getClientOriginalName(), PATHINFO_FILENAME);
                $basename = Str::slug($basename) ?: 'image';
                $stamp    = now()->format('YmdHis').'-'.Str::random(6);

                $finalPath = null;

                // Intentar convertir a WebP; si falla, guardar original
                try {
                    // Leer y orientar
                    $img = Img::read($uploaded->getRealPath())->orient();

                    // Limitar lado mayor
                    if ($maxSide > 0) {
                        $img->scaleDown($maxSide);
                    }

                    // Codificar a WebP - CORRECCIÓN AQUÍ
                    $webpName = "{$basename}-{$stamp}.webp";
                    $webpPath = "{$storageFolder}/{$webpName}";

                    // La forma correcta de codificar y guardar
                    $encoded = $img->encode(new WebpEncoder(quality: $quality));
                    Storage::disk('public')->put($webpPath, (string) $encoded);

                    $finalPath = $webpPath;

                    Log::info('Image converted to WebP successfully', [
                        'tour_id' => $tour->tour_id,
                        'original_file' => $uploaded->getClientOriginalName(),
                        'webp_path' => $webpPath,
                    ]);

                } catch (\Throwable $e) {
                    // Fallback: guardar el archivo tal cual (sin conversión)
                    Log::error('WebP encode failed, falling back to original', [
                        'tour_id' => $tour->tour_id,
                        'file'    => $uploaded->getClientOriginalName(),
                        'error'   => $e->getMessage(),
                        'trace'   => $e->getTraceAsString(),
                    ]);

                    $origName = "{$basename}-{$stamp}.{$inputExt}";
                    Storage::disk('public')->putFileAs($storageFolder, $uploaded, $origName);
                    $finalPath = "{$storageFolder}/{$origName}";
                }

                $image = TourImage::create([
                    'tour_id'  => $tour->tour_id,
                    'path'     => $finalPath,
                    'caption'  => null,
                    'position' => ++$nextPosition,
                    'is_cover' => false,
                ]);

                $newImageRecords[] = $image;
                $createdCount++;
            }

            if (!$tour->coverImage && isset($newImageRecords[0])) {
                $newImageRecords[0]->update(['is_cover' => true]);
            }

            $feedback = $createdCount > 0
                ? __('m_tours.image.upload_success')
                : __('m_tours.image.upload_none');

            if ($truncatedForLimit) {
                $feedback .= ' '.__('m_tours.image.upload_truncated');
            }

            return back()->with('swal', [
                'icon'  => $createdCount > 0 ? 'success' : 'info',
                'title' => $createdCount > 0 ? __('m_tours.image.done') : __('m_tours.image.notice'),
                'text'  => $feedback,
            ]);

        } catch (ValidationException $e) {
            Log::warning('Image upload validation failed', [
                'tour_id' => $tour->tour_id ?? null,
                'user_id' => optional(Auth::user())->id,
                'errors'  => $e->errors(),
            ]);
            $firstMsg = collect($e->errors())->flatten()->first() ?? __('m_tours.image.errors.validation');
            return back()->with('swal', [
                'icon'  => 'warning',
                'title' => __('m_tours.image.ui.warning_title'),
                'text'  => $firstMsg,
            ])->withInput();

        } catch (\Throwable $e) {
            Log::error('Image upload failed', [
                'tour_id' => $tour->tour_id ?? null,
                'user_id' => optional(Auth::user())->id,
                'error'   => $e->getMessage(),
            ]);
            return back()->with('swal', [
                'icon'  => 'error',
                'title' => __('m_tours.image.ui.error_title'),
                'text'  => __('m_tours.image.errors.upload_generic'),
            ])->withInput();
        }
    }


    /** Update an image (caption only for now). */
    public function update(Request $request, Tour $tour, TourImage $image)
    {
        abort_unless($image->tour_id === $tour->tour_id, 404);

        try {
            $data = $request->validate([
                'caption' => ['nullable', 'string', 'max:200'],
            ]);

            $image->update($data);

            return back()->with('swal', [
                'icon'  => 'success',
                'title' => __('m_tours.image.saved'),
                'text'  => __('m_tours.image.caption_updated'),
            ]);
        } catch (ValidationException $e) {
            Log::warning('Image update validation failed', [
                'image_id' => $image->id,
                'tour_id'  => $tour->tour_id,
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
                'tour_id'  => $tour->tour_id,
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
    public function destroy(Tour $tour, TourImage $image)
    {
        abort_unless($image->tour_id === $tour->tour_id, 404);

        try {
            $wasCover = (bool) $image->is_cover;

            if ($image->path && Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            $image->delete();

            if ($wasCover) {
                $nextCover = $tour->images()->orderBy('position')->first();
                if ($nextCover) {
                    $nextCover->update(['is_cover' => true]);
                }
            }

            return back()->with('swal', [
                'icon'  => 'success',
                'title' => __('m_tours.image.deleted'),
                'text'  => __('m_tours.image.image_removed'),
            ]);
        } catch (Throwable $e) {
            Log::error('Image delete failed', [
                'image_id' => $image->id ?? null,
                'tour_id'  => $tour->tour_id ?? null,
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
    public function reorder(Request $request, Tour $tour)
    {
        try {
            $orderIds = $request->input('order', []);
            if (!is_array($orderIds) || empty($orderIds)) {
                return $this->reorderResponse($request, false, __('m_tours.image.invalid_order'));
            }

            $validIds = $tour->images()->pluck('id')->all();
            $newOrder = array_values(array_intersect($orderIds, $validIds));
            if (empty($newOrder)) {
                return $this->reorderResponse($request, false, __('m_tours.image.nothing_to_reorder'));
            }

            DB::transaction(function () use ($newOrder) {
                foreach ($newOrder as $pos => $id) {
                    TourImage::where('id', $id)->update(['position' => $pos + 1]);
                }
            });

            return $this->reorderResponse($request, true, __('m_tours.image.order_saved'));
        } catch (Throwable $e) {
            Log::error('Image reorder failed', [
                'tour_id' => $tour->tour_id ?? null,
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
    public function setCover(Tour $tour, TourImage $image)
    {
        abort_unless($image->tour_id === $tour->tour_id, 404);

        try {
            DB::transaction(function () use ($tour, $image) {
                TourImage::where('tour_id', $tour->tour_id)->update(['is_cover' => false]);
                $image->update(['is_cover' => true]);
            });

            return back()->with('swal', [
                'icon'  => 'success',
                'title' => __('m_tours.image.cover_updated_title'),
                'text'  => __('m_tours.image.cover_updated_text'),
            ]);
        } catch (Throwable $e) {
            Log::error('Image setCover failed', [
                'image_id' => $image->id ?? null,
                'tour_id'  => $tour->tour_id ?? null,
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

            $tours = Tour::select('tour_id', 'name', 'viator_code')
                ->with(['coverImage:id,tour_id,path,is_cover'])
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($sub) use ($q) {
                        $sub->where('name', 'ILIKE', "%{$q}%")
                            ->orWhere('viator_code', 'ILIKE', "%{$q}%");
                        if (is_numeric($q)) {
                            $sub->orWhere('tour_id', (int) $q);
                        }
                    });
                })
                ->orderBy('name')
                ->paginate(24)
                ->withQueryString();

            // Compute cover_url
            $tours->getCollection()->transform(function ($tour) {
                $tour->cover_url = optional($tour->coverImage)->url ?? asset('images/volcano.png');
                return $tour;
            });

            return view('admin.tours.images.pick', [
                'items'         => $tours,
                'q'             => $q,
                'idField'       => 'tour_id',
                'nameField'     => 'name',
                'coverAccessor' => 'cover_url',
                'manageRoute'   => 'admin.tours.images.index',
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
                ->filter(fn ($path) => in_array(pathinfo($path, PATHINFO_EXTENSION), $allowed, true))
                ->sort(fn ($a, $b) => strnatcasecmp($a, $b))
                ->first();

            return $first ? asset('storage/'.$first) : asset('images/volcano.png');
        } catch (Throwable $e) {
            Log::warning('coverFromStorage failed; returning default', [
                'tour_id' => $tourId,
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
    public function bulkDestroy(Request $request, Tour $tour)
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
        $images = TourImage::query()
            ->where('tour_id', $tour->getKey())
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
            TourImage::whereIn('id', $images->pluck('id'))->delete();
        });

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
    public function destroyAll(Request $request, Tour $tour)
    {
        $images = TourImage::query()
            ->where('tour_id', $tour->getKey())
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
            TourImage::where('tour_id', $tour->getKey())->delete();
        });

        return back()->with('swal', [
            'icon'  => 'success',
            'title' => __('m_tours.image.deleted'),
            'text'  => __('Se eliminaron todas las imágenes del tour.'),
        ]);
    }
}
