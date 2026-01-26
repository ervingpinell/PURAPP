<?php

namespace App\Http\Controllers\Admin\Reviews;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use App\Models\Review;
use App\Models\ReviewProvider;
use App\Models\Booking; // <-- import para chequear columnas y relación

/**
 * ReviewAdminController
 *
 * Handles reviewadmin operations.
 */
class ReviewAdminController extends Controller
{
    use AuthorizesRequests;

    /**
     * Listado + filtros.
     */
    // app/Http/Controllers/Admin/Reviews/ReviewAdminController.php

    public function index(Request $request, \App\Services\Reviews\ReviewAggregator $aggregator)
    {
        $this->authorize('viewAny', Review::class);

        $reviewsTable  = (new Review())->getTable();
        $bookingsTable = (new \App\Models\Booking())->getTable();

        $hasReviewBkId    = Schema::hasColumn($reviewsTable, 'booking_id');
        $hasBookingRefCol = Schema::hasColumn($bookingsTable, 'booking_reference');

        // Get all active providers for filter dropdown
        $providers = ReviewProvider::where('is_active', true)
            ->orderByRaw("CASE WHEN slug = 'local' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get(['slug', 'name']);

        // Get selected provider from request (default: local)
        $selectedProvider = $request->get('provider', 'local');

        // === EXTERNAL PROVIDER: Fetch from API via aggregator ===
        if ($selectedProvider !== 'local') {
            try {
                $externalReviews = $aggregator->aggregate([
                    'provider' => $selectedProvider,
                    'limit' => 100,
                ]);

                // Convert to collection and add metadata
                $reviews = collect($externalReviews)->map(function ($review) use ($selectedProvider) {
                    return (object) array_merge($review, [
                        'provider' => $selectedProvider,
                        'is_external' => true,
                        'created_at' => $review['date'] ?? now(),
                    ]);
                });

                // Manual pagination for external reviews
                $perPage = 25;
                $currentPage = $request->get('page', 1);
                $reviews = new \Illuminate\Pagination\LengthAwarePaginator(
                    $reviews->forPage($currentPage, $perPage),
                    $reviews->count(),
                    $perPage,
                    $currentPage,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            } catch (\Throwable $e) {
                \Log::error('Failed to fetch external reviews', [
                    'provider' => $selectedProvider,
                    'error' => $e->getMessage(),
                ]);
                $reviews = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 25);
            }

            return view('admin.reviews.index', compact('reviews', 'providers', 'selectedProvider'));
        }

        // === LOCAL PROVIDER: Query database ===
        // Eager-load dinámico para evitar N+1
        $with = ['user:user_id,first_name,last_name,email'];
        if ($hasReviewBkId) {
            $with['booking'] = function ($q) use ($hasBookingRefCol) {
                $cols = ['booking_id'];
                if ($hasBookingRefCol) $cols[] = 'booking_reference';
                $q->select($cols);
            };
        }

        $q = Review::query()
            ->where('provider', 'local')
            ->with($with)
            ->withCount('replies')                 // cuántas respuestas
            ->withMax('replies', 'created_at');    // fecha de última respuesta

        // Filtros (para locales)
        if ($st  = $request->get('status'))  $q->where('status', $st);
        if ($tid = $request->get('product_id')) $q->where('product_id', (int) $tid);
        if ($stars = $request->get('stars')) $q->where('rating', (int) $stars);

        // Búsqueda libre (incluye booking_reference si existe)
        if ($qstr = trim((string) $request->get('q'))) {
            $q->where(function ($w) use ($qstr, $hasReviewBkId, $hasBookingRefCol) {
                $w->where('title', 'ilike', "%{$qstr}%")
                    ->orWhere('body', 'ilike', "%{$qstr}%")
                    ->orWhere('author_name', 'ilike', "%{$qstr}%");

                if ($hasReviewBkId && $hasBookingRefCol) {
                    $w->orWhereHas('booking', function ($bq) use ($qstr) {
                        $bq->where('booking_reference', 'ilike', "%{$qstr}%");
                    });
                }
            });
        }

        // Filtro: respondido (yes/no)
        if ($request->filled('responded')) {
            $resp = $request->get('responded');
            if ($resp === 'yes')      $q->has('replies');
            elseif ($resp === 'no')   $q->doesntHave('replies');
        }

        $reviews = $q->orderByDesc('id')->paginate(25)->withQueryString();

        return view('admin.reviews.index', compact('reviews', 'providers', 'selectedProvider'));
    }


    /**
     * Form crear.
     */
    public function create()
    {
        $this->authorize('create', Review::class);

        $review = new Review();
        // Defaults amigables
        $review->provider  = 'local';
        $review->rating    = 5;
        $review->status    = 'pending';
        $review->is_public = false;

        return view('admin.reviews.form', compact('review'));
    }

    /**
     * Guardar nueva (por defecto NO pública y en pending).
     */
    public function store(Request $request)
    {
        $this->authorize('create', Review::class);

        $validated = $request->validate([
            'product_id'     => ['required', 'integer', 'min:1'],
            'rating'      => ['required', 'integer', 'min:1', 'max:5'],
            'title'       => ['nullable', 'string', 'max:150'],
            'body'        => ['required', 'string', 'min:5', 'max:5000'],
            'author_name' => ['nullable', 'string', 'max:100'],
            'language'    => ['nullable', 'string', 'max:10'],
            'status'      => ['nullable', Rule::in(['pending', 'published', 'hidden', 'flagged'])],
            'is_public'   => ['nullable', 'boolean'],
            'booking_ref' => ['nullable', 'string'],
            'user_email'  => ['nullable', 'email'],
        ]);

        $data = [
            'provider'    => 'local',
            'product_id'     => (int) $validated['product_id'],
            'rating'      => (int) $validated['rating'],
            'title'       => $validated['title'] ?? null,
            'body'        => $validated['body'],
            'author_name' => $validated['author_name'] ?? null,
        ];

        // 1. Save manual inputs
        if (!empty($validated['booking_ref'])) {
            $data['manual_booking_ref'] = trim($validated['booking_ref']);
        }
        if (!empty($validated['user_email'])) {
            $data['author_email'] = trim($validated['user_email']);
        }

        // 2. Attempt to link real IDs if match found (case-insensitive)
        if (!empty($data['manual_booking_ref'])) {
            $bk = Booking::whereRaw('LOWER(booking_reference) = ?', [strtolower($data['manual_booking_ref'])])->first();
            if ($bk) $data['booking_id'] = $bk->booking_id;
        }

        if (!empty($data['author_email'])) {
            $u = \App\Models\User::whereRaw('LOWER(email) = ?', [strtolower($data['author_email'])])->first();
            if ($u) $data['user_id'] = $u->user_id;
        }

        if (Schema::hasColumn('reviews', 'language'))  $data['language']  = $validated['language'] ?? null;
        if (Schema::hasColumn('reviews', 'status'))    $data['status']    = 'pending';
        if (Schema::hasColumn('reviews', 'is_public')) $data['is_public'] = false;
        if (Schema::hasColumn('reviews', 'indexable')) $data['indexable'] = true;

        $review = Review::create($data);

        $this->touchReviewsRevision();

        return redirect()
            ->route('admin.reviews.index')
            ->with('ok', __('reviews.admin.messages.created'));
    }

    /**
     * Form editar.
     */
    public function edit(Review $review)
    {
        $this->authorize('update', $review);

        return view('admin.reviews.form', compact('review'));
    }

    /**
     * Actualizar (product_id suele venir disabled en el form).
     */
    public function update(Request $request, Review $review)
    {
        $this->authorize('update', $review);

        $validated = $request->validate([
            'rating'      => ['required', 'integer', 'min:1', 'max:5'],
            'title'       => ['nullable', 'string', 'max:150'],
            'body'        => ['required', 'string', 'min:5', 'max:5000'],
            'author_name' => ['nullable', 'string', 'max:100'],
            'language'    => ['nullable', 'string', 'max:10'],
            'status'      => ['nullable', Rule::in(['pending', 'published', 'hidden', 'flagged'])],
            'is_public'   => ['nullable', 'boolean'],
            'booking_ref' => ['nullable', 'string'],
            'user_email'  => ['nullable', 'email'],
        ]);

        $payload = [
            'rating'      => (int) $validated['rating'],
            'title'       => $validated['title'] ?? null,
            'body'        => $validated['body'],
            'author_name' => $validated['author_name'] ?? null,
        ];

        // 1. Save and Link Booking Ref
        if (array_key_exists('booking_ref', $validated)) {
             $val = trim($validated['booking_ref'] ?? '');
             $payload['manual_booking_ref'] = $val ?: null;

             if ($val) {
                 $bk = Booking::whereRaw('LOWER(booking_reference) = ?', [strtolower($val)])->first();
                 $payload['booking_id'] = $bk?->booking_id; // Link if found, otherwise null is implicit? No, explicit null on not found ok?
                 // Note: If no match, we just save manual_booking_ref. booking_id can be cleared or kept?
                 // Usually if manual ref changes, we should re-resolve booking_id. If not found, booking_id should be null.
                 $payload['booking_id'] = $bk ? $bk->booking_id : null;
             } else {
                 $payload['manual_booking_ref'] = null;
                 $payload['booking_id'] = null;
             }
        }

        // 2. Save and Link Email
        if (array_key_exists('user_email', $validated)) {
            $val = trim($validated['user_email'] ?? '');
            $payload['author_email'] = $val ?: null;

            if ($val) {
                $u = \App\Models\User::whereRaw('LOWER(email) = ?', [strtolower($val)])->first();
                $payload['user_id'] = $u ? $u->user_id : null;
            } else {
                $payload['author_email'] = null;
                $payload['user_id'] = null;
            }
        }

        if (Schema::hasColumn($review->getTable(), 'language')) $payload['language'] = $validated['language'] ?? $review->language;
        if (Schema::hasColumn($review->getTable(), 'status'))   $payload['status']   = $validated['status'] ?? $review->status ?? 'pending';
        if (Schema::hasColumn($review->getTable(), 'is_public')) {
            $payload['is_public'] = array_key_exists('is_public', $validated)
                ? (bool) $validated['is_public']
                : (bool) $review->is_public;

            if (($payload['status'] ?? $review->status) !== 'published') {
                $payload['is_public'] = false;
            }
        }

        $review->update($payload);

        $this->touchReviewsRevision();

        return redirect()
            ->route('admin.reviews.index')
            ->with('ok', __('reviews.admin.messages.updated'));
    }

    /**
     * Eliminar.
     */
    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);

        $review->delete();

        $this->touchReviewsRevision();

        return back()->with('ok', __('reviews.admin.messages.deleted'));
    }

    /**
     * Publicar (status=published, is_public=true).
     */
    public function publish(Review $review)
    {
        $this->authorize('update', $review);

        $min = (int) config('reviews.min_public_rating', 1);
        if ($min > 1 && (int) $review->rating < $min) {
            return back()->with('error', __('reviews.admin.messages.publish_min_rating', [
                'rating' => $review->rating,
                'min'    => $min,
            ]));
        }

        if (Schema::hasColumn($review->getTable(), 'status'))   $review->status   = 'published';
        if (Schema::hasColumn($review->getTable(), 'is_public')) $review->is_public = true;

        $review->save();
        $this->touchReviewsRevision();

        return back()->with('ok', __('reviews.admin.messages.published'));
    }


    /**
     * Ocultar (status=hidden, is_public=false).
     */
    public function hide(Review $review)
    {
        $this->authorize('update', $review);

        if (Schema::hasColumn($review->getTable(), 'status'))   $review->status   = 'hidden';
        if (Schema::hasColumn($review->getTable(), 'is_public')) $review->is_public = false;

        $review->save();

        $this->touchReviewsRevision();

        return back()->with('ok', __('reviews.admin.messages.hidden'));
    }

    /**
     * Flag/Marcar sospechosa (status=flagged, is_public=false).
     */
    public function flag(Review $review, Request $request)
    {
        $this->authorize('update', $review);

        if (Schema::hasColumn($review->getTable(), 'status'))   $review->status   = 'flagged';
        if (Schema::hasColumn($review->getTable(), 'is_public')) $review->is_public = false;
        if (Schema::hasColumn($review->getTable(), 'flag_reason')) {
            $reason = (string) $request->input('reason', '');
            $review->flag_reason = $reason !== '' ? $reason : null;
        }

        $review->save();

        $this->touchReviewsRevision();

        return back()->with('ok', __('reviews.admin.messages.flagged'));
    }

    /**
     * Acciones masivas: publish | hide | flag | delete
     */
    public function bulk(Request $request)
    {
        $this->authorize('update', Review::class);

        $request->validate([
            'action' => ['required', Rule::in(['publish', 'hide', 'flag', 'delete'])],
            'ids'    => ['required', 'array', 'min:1'],
            'ids.*'  => ['integer'],
        ]);

        $ids    = array_map('intval', (array) $request->input('ids', []));
        $action = (string) $request->input('action');

        $items = Review::whereIn('id', $ids)->where('provider', 'local')->get();
        $count = 0;

        foreach ($items as $r) {
            $this->authorize('update', $r);

            switch ($action) {
                case 'publish':
                    if (Schema::hasColumn($r->getTable(), 'status'))    $r->status    = 'published';
                    if (Schema::hasColumn($r->getTable(), 'is_public')) $r->is_public = true;
                    $r->save();
                    $count++;
                    break;

                case 'hide':
                    if (Schema::hasColumn($r->getTable(), 'status'))    $r->status    = 'hidden';
                    if (Schema::hasColumn($r->getTable(), 'is_public')) $r->is_public = false;
                    $r->save();
                    $count++;
                    break;

                case 'flag':
                    if (Schema::hasColumn($r->getTable(), 'status'))    $r->status    = 'flagged';
                    if (Schema::hasColumn($r->getTable(), 'is_public')) $r->is_public = false;
                    $r->save();
                    $count++;
                    break;

                case 'delete':
                    $this->authorize('delete', $r);
                    $r->delete();
                    $count++;
                    break;
            }
        }

        $this->touchReviewsRevision();

        $msg = match ($action) {
            'publish' => __('reviews.admin.messages.bulk_published', ['n' => $count]),
            'hide'    => __('reviews.admin.messages.bulk_hidden',    ['n' => $count]),
            'flag'    => __('reviews.admin.messages.bulk_flagged',   ['n' => $count]),
            'delete'  => __('reviews.admin.messages.bulk_deleted',   ['n' => $count]),
        };

        return back()->with('ok', $msg);
    }

    /**
     * Sube el "revisión" global para invalidar cachés de reviews.
     */
    private function touchReviewsRevision(): void
    {
        try {
            if (! Cache::has('reviews.rev')) {
                Cache::put('reviews.rev', 1, now()->addYears(5));
            } else {
                Cache::increment('reviews.rev');
            }
        } catch (\Throwable $e) {
            $v = (int) Cache::get('reviews.rev', 0) + 1;
            Cache::put('reviews.rev', $v, now()->addYears(5));
        }
    }
}
