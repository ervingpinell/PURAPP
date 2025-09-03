<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TourImageController extends Controller
{
    /** List images for a tour. */
    public function index(Tour $tour)
    {
        $tour->load(['images', 'coverImage']);
        $max = config('tours.max_images_per_tour', 20);

        return view('admin.tours.images', compact('tour', 'max'));
    }

    /** Upload images for a tour (respecting per-tour limit). */
    public function store(Request $request, Tour $tour)
    {
        $maxSizeKb = (int) config('tours.max_image_kb', 30720); // 30MB default

        $request->validate([
            'files'   => ['required', 'array'],
            'files.*' => ['image', 'mimes:jpeg,jpg,png,webp', "max:{$maxSizeKb}"],
        ]);

        $max     = (int) config('tours.max_images_per_tour', 20);
        $current = (int) $tour->images()->count();
        $allow   = max(0, $max - $current);

        $files = $request->file('files', []);
        if ($allow <= 0) {
            return back()->with('swal', [
                'icon'  => 'warning',
                'title' => __('m_tours.image.limit_reached_title'),
                'text'  => __('m_tours.image.limit_reached_text'),
            ]);
        }


        // Only take up to the allowed amount
        if (count($files) > $allow) {
            $files = array_slice($files, 0, $allow);
            $truncated = true;
        } else {
            $truncated = false;
        }

        $folder      = "tours/{$tour->tour_id}/gallery";
        $nextPos     = (int) ($tour->images()->max('position') ?? 0);
        $created     = 0;
        $createdImgs = [];

        foreach ($files as $file) {
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                continue;
            }

            $base = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $base = Str::slug($base) ?: 'image';

            $filename = $base.'-'.now()->format('YmdHis').'-'.Str::random(6).'.'.$ext;
            Storage::disk('public')->putFileAs($folder, $file, $filename);

            $img = TourImage::create([
                'tour_id'  => $tour->tour_id,
                'path'     => $folder.'/'.$filename,
                'caption'  => null,
                'position' => ++$nextPos,
                'is_cover' => false,
            ]);

            $createdImgs[] = $img;
            $created++;
        }

        // If there is no cover yet, mark the first newly created as cover
        if (!$tour->coverImage && isset($createdImgs[0])) {
            $createdImgs[0]->update(['is_cover' => true]);
        }

        $msg = $created > 0
            ? __('m_tours.image.upload_success')
            : __('m_tours.image.upload_none');

        if ($truncated) {
            $msg .= ' '.__('m_tours.image.upload_truncated');
        }

        return back()->with('swal', [
            'icon'  => $created > 0 ? 'success' : 'info',
            'title' => $created > 0 ? __('m_tours.image.done') : __('m_tours.image.notice'),
            'text'  => $msg,
        ]);
    }

    /** Update an image (caption only for now). */
    public function update(Request $request, Tour $tour, TourImage $img)
    {
        abort_unless($img->tour_id === $tour->tour_id, 404);

        $data = $request->validate([
            'caption' => ['nullable', 'string', 'max:200'],
        ]);

        $img->update($data);

        return back()->with('swal', [
            'icon'  => 'success',
            'title' => __('m_tours.image.saved'),
            'text'  => __('m_tours.image.caption_updated'),
        ]);
    }

    /** Delete an image. If it was cover, set next image as cover. */
    public function destroy(Tour $tour, TourImage $img)
    {
        abort_unless($img->tour_id === $tour->tour_id, 404);

        $wasCover = (bool) $img->is_cover;

        if ($img->path && Storage::disk('public')->exists($img->path)) {
            Storage::disk('public')->delete($img->path);
        }

        $img->delete();

        if ($wasCover) {
            $next = $tour->images()->orderBy('position')->first();
            if ($next) {
                $next->update(['is_cover' => true]);
            }
        }

        return back()->with('swal', [
            'icon'  => 'success',
            'title' => __('m_tours.image.deleted'),
            'text'  => __('m_tours.image.image_removed'),
        ]);
    }

    /** Reorder images (expects `order` = [imageId1, imageId2, ...]). */
    public function reorder(Request $request, Tour $tour)
    {
        $order = $request->input('order', []);
        if (!is_array($order) || empty($order)) {
            return $this->reorderResponse($request, false, __('m_tours.image.invalid_order'));
        }

        // Only apply to images belonging to this tour
        $validIds = $tour->images()->pluck('id')->all();
        $newOrder = array_values(array_intersect($order, $validIds));
        if (empty($newOrder)) {
            return $this->reorderResponse($request, false, __('m_tours.image.nothing_to_reorder'));
        }

        DB::transaction(function () use ($newOrder) {
            foreach ($newOrder as $pos => $id) {
                TourImage::where('id', $id)->update(['position' => $pos + 1]);
            }
        });

        return $this->reorderResponse($request, true, __('m_tours.image.order_saved'));
    }

    protected function reorderResponse(Request $request, bool $ok, string $message)
    {
        if ($request->wantsJson()) {
            return response()->json(['ok' => $ok, 'message' => $message]);
        }

        return back()->with('swal', [
            'icon'  => $ok ? 'success' : 'warning',
            'title' => $ok ? __('m_tours.image.done') : __('m_tours.image.notice'),
            'text'  => $message,
        ]);
    }

    /** Set an image as cover. */
    public function setCover(Tour $tour, TourImage $img)
    {
        abort_unless($img->tour_id === $tour->tour_id, 404);

        DB::transaction(function () use ($tour, $img) {
            TourImage::where('tour_id', $tour->tour_id)->update(['is_cover' => false]);
            $img->update(['is_cover' => true]);
        });

        return back()->with('swal', [
            'icon'  => 'success',
            'title' => __('m_tours.image.cover_updated_title'),
            'text'  => __('m_tours.image.cover_updated_text'),
        ]);
    }

    /** Picker: list tours to manage their images. */
    public function pick(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $tours = \App\Models\Tour::select('tour_id', 'name', 'viator_code')
            ->with(['coverImage:id,tour_id,path,is_cover'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'ILIKE', "%{$q}%")
                    ->orWhere('viator_code', 'ILIKE', "%{$q}%");
                    if (is_numeric($q)) {
                        $qq->orWhere('tour_id', (int) $q);
                    }
                });
            })
            ->orderBy('name')
            ->paginate(24)
            ->withQueryString();

        // Asegurar cover_url
        $tours->getCollection()->transform(function ($t) {
            $t->cover_url = optional($t->coverImage)->url ?? asset('images/volcano.png');
            return $t;
        });

        return view('admin.tours.images.pick', [
            'items'         => $tours,
            'q'             => $q,
            'idField'       => 'tour_id',
            'nameField'     => 'name',
            'coverAccessor' => 'cover_url',
            'manageRoute'   => 'admin.tours.images.index',
            'i18n' => [
                'title'   => __('m_tours.image.ui.page_title_pick'),
                'heading' => __('m_tours.image.ui.page_heading'),
                'choose'  => __('m_tours.image.ui.choose_tour'),
            ],
        ]);
    }


    /** Helper: find a "cover-like" image from storage if no DB cover is set. */
    protected function coverFromStorage(int $tourId): string
    {
        $folder = "tours/{$tourId}/gallery";
        if (!Storage::disk('public')->exists($folder)) {
            return asset('images/volcano.png');
        }

        $allowed = ['jpg','jpeg','png','webp','JPG','JPEG','PNG','WEBP'];

        $first = collect(Storage::disk('public')->files($folder))
            ->filter(fn ($p) => in_array(pathinfo($p, PATHINFO_EXTENSION), $allowed, true))
            ->sort(fn ($a, $b) => strnatcasecmp($a, $b))
            ->first();

        return $first ? asset('storage/'.$first) : asset('images/volcano.png');
    }
}
