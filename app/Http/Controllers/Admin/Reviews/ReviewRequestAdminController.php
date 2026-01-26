<?php

namespace App\Http\Controllers\Admin\Reviews;

use App\Http\Controllers\Controller;
use App\Mail\ReviewRequestLink;
use App\Models\Booking;
use App\Models\Review;
use App\Models\ReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * ReviewRequestAdminController
 *
 * Handles reviewrequestadmin operations.
 */
class ReviewRequestAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-review-requests'])->only(['index', 'indexEligible', 'indexRequested']);
        $this->middleware(['can:create-review-requests'])->only(['send', 'discard']);
        $this->middleware(['can:edit-review-requests'])->only(['resend', 'remind', 'expire', 'restore', 'skip']);
        $this->middleware(['can:delete-review-requests'])->only(['destroy', 'destroyPerm']);
    }

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'eligible');
        if ($tab === 'requested') return $this->indexRequested($request);
        if ($tab === 'trash')     return $this->indexTrash($request);
        return $this->indexEligible($request);
    }

    /** --------- TAB: ELEGIBLES (reservas) --------- */
    private function indexEligible(Request $request)
    {
        // Allow manual date range or default to window
        $to   = $request->get('to') ?: now()->toDateString();
        $from = $request->get('from') ?: now()->subDays(60)->toDateString();

        $reviewTable = (new Review())->getTable();
        $reviewRequestTable = (new ReviewRequest())->getTable();
        $bookingTable = (new Booking())->getTable();

        // Allow user to select date column, or auto-detect
        $reqDateCol = $request->get('date_col');
        
        // If request has a date_col, verify it exists. Else auto-detect.
        if ($reqDateCol && Schema::hasColumn($bookingTable, $reqDateCol)) {
            $dateCol = $reqDateCol;
        } else {
            // Fallback to auto-detection or created_at
            $dateCol = $this->detectBookingDateColumn($bookingTable) ?? 'created_at';
        }

        $hasUsed       = Schema::hasColumn($reviewRequestTable, 'used_at');
        $hasCancelled  = Schema::hasColumn($reviewRequestTable, 'cancelled_at');
        $hasExpires    = Schema::hasColumn($reviewRequestTable, 'expires_at');
        $hasStatus     = Schema::hasColumn($reviewRequestTable, 'status');
        $hasBookingRef = Schema::hasColumn($bookingTable, 'booking_reference');

        $query = Booking::query()
            ->with([
                'tour:product_id,name',
                'tour.translations',
                'user:user_id,first_name,last_name,email',
                'detail.tourLanguage:tour_language_id,name',
            ]);

        // (opcional) selección explícita de columna de referencia si existe
        if ($hasBookingRef) {
            $query->select(["$bookingTable.*", "$bookingTable.booking_reference"]);
        }

        $query->whereIn('status', ['confirmed', 'completed', 'CONFIRMED', 'COMPLETED'])
            ->whereDate($dateCol, '>=', $from)
            ->whereDate($dateCol, '<=', $to)

            // Sin review local previa (mismo user + tour)
            ->whereNotExists(function ($subQuery) use ($reviewTable, $bookingTable) {
                $subQuery->select(DB::raw('1'))
                    ->from($reviewTable)
                    ->where("$reviewTable.provider", 'local')
                    ->whereColumn("$reviewTable.product_id", "$bookingTable.product_id")
                    ->whereColumn("$reviewTable.user_id", "$bookingTable.user_id");
            })

            // Sin solicitud vigente
            ->whereNotExists(function ($subQuery) use ($reviewRequestTable, $bookingTable, $hasUsed, $hasCancelled, $hasExpires, $hasStatus) {
                $subQuery->select(DB::raw('1'))
                    ->from($reviewRequestTable)
                    ->whereColumn("$reviewRequestTable.booking_id", "$bookingTable.booking_id");

                if ($hasUsed)      $subQuery->whereNull("$reviewRequestTable.used_at");
                if ($hasCancelled) $subQuery->whereNull("$reviewRequestTable.cancelled_at");

                if ($hasExpires) {
                    $subQuery->where(function ($sub) use ($reviewRequestTable) {
                        $sub->whereNull("$reviewRequestTable.expires_at")->orWhere("$reviewRequestTable.expires_at", '>', now());
                    });
                } elseif ($hasStatus) {
                    $subQuery->whereIn("$reviewRequestTable.status", ['sent', 'reminded']);
                }
            })

            // Búsqueda libre (incluye booking_reference si existe)
            ->when($request->filled('q'), function ($sub) use ($request, $bookingTable, $hasBookingRef) {
                $searchString = trim((string) $request->get('q'));
                $sub->where(function ($qq) use ($searchString, $bookingTable, $hasBookingRef) {
                    $qq->where("$bookingTable.booking_id", (int) $searchString)
                        ->orWhere("$bookingTable.customer_name", 'ilike', "%{$searchString}%")
                        ->orWhere("$bookingTable.customer_email", 'ilike', "%{$searchString}%");

                    if ($hasBookingRef) {
                        $qq->orWhere("$bookingTable.booking_reference", 'ilike', "%{$searchString}%");
                    }
                });
            })
        
            // Strict Filter: Tour Date must be in the past (or today)
            // Helps prevent future tours from showing up even if filtered by creation date.
            ->whereHas('detail', function ($sub) {
                $sub->where('tour_date', '<=', now());
            })

            ->when($request->filled('product_id'), fn($sub) => $sub->where('product_id', (int) $request->product_id))
            ->orderByDesc($dateCol);

        $bookings = $query->paginate(25)->withQueryString();

        return view('admin.reviews.requests.index', [
            'tab'        => 'eligible',
            'bookings'   => $bookings,
            'dateCol'    => $dateCol,
            'from'       => $from,
            'to'         => $to,
        ]);
    }

    /** --------- TAB: SOLICITADAS (review_requests) --------- */
    private function indexRequested(Request $request)
    {
        $reviewRequestTable = (new ReviewRequest())->getTable();
        $bookingTable = (new Booking())->getTable();

        $hasUsed       = Schema::hasColumn($reviewRequestTable, 'used_at');
        $hasCancelled  = Schema::hasColumn($reviewRequestTable, 'cancelled_at');
        $hasExpires    = Schema::hasColumn($reviewRequestTable, 'expires_at');
        $hasStatus     = Schema::hasColumn($reviewRequestTable, 'status');
        $hasSentAt     = Schema::hasColumn($reviewRequestTable, 'sent_at');
        $hasBookingRef = Schema::hasColumn($bookingTable, 'booking_reference');

        $query = ReviewRequest::query()
            ->with([
                // incluye booking_reference si existe
                'booking:booking_id,product_id' . ($hasBookingRef ? ',booking_reference' : ''),
                'tour:product_id,name',
                'user:user_id,first_name,last_name,email',
            ]);

        // Filtros de estado
        if ($status = $request->get('status')) {
            $status = strtolower($status);
            $query->where(function ($sub) use ($status, $reviewRequestTable, $hasUsed, $hasCancelled, $hasExpires, $hasStatus) {
                if ($status === 'active') {
                    if ($hasUsed)      $sub->whereNull("$reviewRequestTable.used_at");
                    if ($hasCancelled) $sub->whereNull("$reviewRequestTable.cancelled_at");
                    if ($hasExpires)   $sub->where(function ($x) use ($reviewRequestTable) {
                        $x->whereNull("$reviewRequestTable.expires_at")->orWhere("$reviewRequestTable.expires_at", '>', now());
                    });
                } elseif (in_array($status, ['sent', 'reminded', 'skipped'], true)) {
                    if ($hasStatus) $sub->where("$reviewRequestTable.status", $status);
                } elseif ($status === 'used' && $hasUsed) {
                    $sub->whereNotNull("$reviewRequestTable.used_at");
                } elseif ($status === 'expired' && $hasExpires) {
                    $sub->whereNotNull("$reviewRequestTable.expires_at")->where("$reviewRequestTable.expires_at", '<=', now());
                } elseif ($status === 'cancelled' && $hasCancelled) {
                    $sub->whereNotNull("$reviewRequestTable.cancelled_at");
                }
            });
        } elseif ($hasStatus) {
             // Default: Exclude skipped (hide from default view)
             $query->where("$reviewRequestTable.status", '!=', 'skipped');

             // HIDE COMPLETED (Used) from default view to avoid clutter
             if ($hasUsed) {
                 $query->whereNull("$reviewRequestTable.used_at");
             }
        }

        // Rango fechas
        $from = $request->get('from');
        $to   = $request->get('to');
        $dateCol = $hasSentAt ? 'sent_at' : 'created_at';

        if ($from) $query->whereDate($dateCol, '>=', $from);
        if ($to)   $query->whereDate($dateCol, '<=', $to);

        // Búsqueda (incluye booking.booking_reference si existe)
        $query->when($request->filled('q'), function ($sub) use ($request, $reviewRequestTable, $hasBookingRef) {
            $searchString = trim((string) $request->get('q'));
            $sub->where(function ($qq) use ($searchString, $reviewRequestTable, $hasBookingRef) {
                $qq->where("$reviewRequestTable.booking_id", (int) $searchString)
                    ->orWhere("$reviewRequestTable.email", 'ilike', "%{$searchString}%");

                if ($hasBookingRef) {
                    $qq->orWhereHas('booking', function ($bq) use ($searchString) {
                        $bq->where('booking_reference', 'ilike', "%{$searchString}%");
                    });
                }
            });
        });

        // Filtrar por tour
        $query->when($request->filled('product_id'), fn($sub) => $sub->where('product_id', (int) request('product_id')));

        $requests = $query->orderByDesc($dateCol)->paginate(25)->withQueryString();

        return view('admin.reviews.requests.index', [
            'tab'      => 'requested',
            'requests' => $requests,
        ]);
    }

    /** Crear y enviar link */
    public function send(Request $request, Booking $booking)
    {
        $request->validate([
            'expires_in_days' => 'nullable|integer|min:1|max:120',
        ]);

        $expDays = (int) $request->input('expires_in_days', 30);
        $token   = Str::random(40);

        $email = optional($booking->user)->email
            ?: ($booking->customer_email ?? $booking->email ?? null);

        $reviewRequest = ReviewRequest::create([
            'booking_id' => $booking->getKey(),
            'user_id'    => $booking->user_id,
            'product_id'    => $booking->product_id,
            'email'      => $email,
            'token'      => $token,
            'sent_at'    => Schema::hasColumn((new ReviewRequest())->getTable(), 'sent_at') ? now() : null,
            'expires_at' => Schema::hasColumn((new ReviewRequest())->getTable(), 'expires_at') ? now()->addDays($expDays) : null,
            'status'     => Schema::hasColumn((new ReviewRequest())->getTable(), 'status') ? 'sent' : null,
        ]);

        if ($reviewRequest->email) {
            // Detect locale from tour language name in booking details
            $tourLanguageName = optional(optional($booking->detail)->tourLanguage)->name ?? '';
            $locale = $this->detectLocaleFromLanguageName($tourLanguageName);
            
            Mail::to($reviewRequest->email)->queue(new ReviewRequestLink($reviewRequest, $locale));
        }

        return back()->with('ok', __('reviews.requests.send_ok'));
    }

    public function resend(ReviewRequest $reviewRequest)
    {
        $table = $reviewRequest->getTable();

        if (Schema::hasColumn($table, 'used_at') && $reviewRequest->used_at) {
            return back()->withErrors(__('reviews.requests.errors.used'));
        }
        if (Schema::hasColumn($table, 'expires_at') && $reviewRequest->expires_at && $reviewRequest->expires_at->isPast()) {
            return back()->withErrors(__('reviews.requests.errors.expired'));
        }

        // Rate limit: 1 per minute
        if (Schema::hasColumn($table, 'sent_at') && $reviewRequest->sent_at && $reviewRequest->sent_at->gt(now()->subMinute())) {
            return back()->withErrors(__('reviews.requests.errors.wait_one_minute'));
        }

        // Load tour language from booking details for language detection
        $reviewRequest->load('booking.detail.tourLanguage', 'booking.tour.translations');

        if ($reviewRequest->email) {
            // Detect locale from tour language name in booking details
            $tourLanguageName = optional(optional(optional($reviewRequest->booking)->detail)->tourLanguage)->name ?? '';
            $locale = $this->detectLocaleFromLanguageName($tourLanguageName);
            
            Mail::to($reviewRequest->email)->queue(new ReviewRequestLink($reviewRequest, $locale));
        }

        if (Schema::hasColumn($table, 'sent_at'))  $reviewRequest->sent_at = now();
        if (Schema::hasColumn($table, 'status'))   $reviewRequest->status  = 'reminded';
        $reviewRequest->save();

        return back()->with('ok', __('reviews.requests.resend_ok'));
    }

    public function remind(ReviewRequest $reviewRequest)
    {
        $table = $reviewRequest->getTable();

        if (Schema::hasColumn($table, 'used_at') && $reviewRequest->used_at) {
            return back()->withErrors(__('reviews.requests.errors.used'));
        }
        if (Schema::hasColumn($table, 'expires_at') && $reviewRequest->expires_at && $reviewRequest->expires_at->isPast()) {
            return back()->withErrors(__('reviews.requests.errors.expired'));
        }

        // Load tour language from booking details for language detection
        $reviewRequest->load('booking.detail.tourLanguage', 'booking.tour.translations');

        if ($reviewRequest->email) {
            // Detect locale from tour language name in booking details
            $tourLanguageName = optional(optional(optional($reviewRequest->booking)->detail)->tourLanguage)->name ?? '';
            $locale = $this->detectLocaleFromLanguageName($tourLanguageName);
            
            Mail::to($reviewRequest->email)->queue(new ReviewRequestLink($reviewRequest, $locale));
        }

        if (Schema::hasColumn($table, 'reminded_at')) $reviewRequest->reminded_at = now();
        if (Schema::hasColumn($table, 'status'))      $reviewRequest->status      = 'reminded';
        $reviewRequest->save();

        return back()->with('ok', __('reviews.requests.remind_ok'));
    }

    public function expire(ReviewRequest $reviewRequest)
    {
        $table = $reviewRequest->getTable();

        if (Schema::hasColumn($table, 'expires_at')) {
            $reviewRequest->expires_at = now();
        }
        if (Schema::hasColumn($table, 'status')) {
            $reviewRequest->status = 'expired';
        }
        $reviewRequest->save();

        return back()->with('ok', __('reviews.requests.expire_ok'));
    }

    public function destroy(ReviewRequest $reviewRequest)
    {
        $reviewRequest->delete();
        return back()->with('ok', __('reviews.requests.deleted'));
    }

    private function getBookingDateColumns(string $table): array
    {
        return [
            'booking_date',
            'start_date',
            'tour_date',
            'service_date',
            'activity_date',
            'travel_date',
            'date',
            'experience_date',
            'scheduled_for',
            'start_at',
            'created_at',
        ];
    }

    private function detectBookingDateColumn(string $table): ?string
    {
        foreach ($this->getBookingDateColumns($table) as $c) {
            if (Schema::hasColumn($table, $c)) return $c;
        }
        return null;
    }

    /**
     * Detect email locale from tour language name
     * 
     * @param string $languageName
     * @return string 'es' or 'en'
     */
    private function detectLocaleFromLanguageName(string $languageName): string
    {
        $languageName = strtolower(trim($languageName));
        
        // Check if language name contains Spanish indicators
        if (str_contains($languageName, 'español') || 
            str_contains($languageName, 'spanish') ||
            $languageName === 'es') {
            return 'es';
        }
        
        // Default to English for all other languages
        return 'en';
    }
    public function discard(Booking $booking)
    {
        $email = optional($booking->user)->email 
            ?: ($booking->customer_email ?? $booking->email ?? 'no-email@example.com');

        ReviewRequest::create([
            'booking_id' => $booking->getKey(),
            'user_id'    => $booking->user_id,
            'product_id'    => $booking->product_id,
            'email'      => $email,
            'token'      => Str::random(40),
            'sent_at'    => now(),
            'expires_at' => null, // Permanent (never "expires" so stays valid for blocking)
            'status'     => 'skipped',
        ]);

        return back()->with('ok', __('reviews.requests.discard_ok') ?? 'Review request discarded.');
    }

    /** Mover solicitud existente a Skipped (ocultar de tabla, mantiene validez) */
    /** Mover solicitud existente a Skipped (ocultar de tabla, mantiene validez) */
    public function skip(ReviewRequest $reviewRequest)
    {
        if (Schema::hasColumn($reviewRequest->getTable(), 'status')) {
            $reviewRequest->status = 'skipped';
        }
        if (Schema::hasColumn($reviewRequest->getTable(), 'expires_at')) {
            $reviewRequest->expires_at = null; // Nunca expira (para que siga bloqueando elegibles)
        }
        $reviewRequest->save();
        return back()->with('ok', __('reviews.requests.skipped_ok') ?? 'Request skipped.');
    }

    /** Restaurar de papelera */
    /** Restaurar de papelera */
    public function restore($id)
    {
        $reviewRequest = ReviewRequest::withTrashed()->findOrFail($id);
        $reviewRequest->restore();
        return back()->with('ok', __('reviews.requests.restored_ok') ?? 'Request restored.');
    }

    /** Eliminar permanentemente (solo desde trash) */
    /** Eliminar permanentemente (solo desde trash) */
    public function destroyPerm($id)
    {
        $reviewRequest = ReviewRequest::withTrashed()->findOrFail($id);
        $reviewRequest->forceDelete();
        return back()->with('ok', __('reviews.requests.deleted_perm') ?? 'Request deleted permanently.');
    }

    /** --------- TAB: TRASH (Papelera) --------- */
    private function indexTrash(Request $request)
    {
        $reviewRequestTable = (new ReviewRequest())->getTable();
        $bookingTable = (new Booking())->getTable();
        $hasBookingRef = Schema::hasColumn($bookingTable, 'booking_reference');
        $hasSentAt = Schema::hasColumn($reviewRequestTable, 'sent_at');

        $query = ReviewRequest::onlyTrashed()
            ->with([
                'booking:booking_id,product_id' . ($hasBookingRef ? ',booking_reference' : ''),
                'tour:product_id,name',
                'user:user_id,first_name,last_name,email',
            ]);

        // Search
        $query->when($request->filled('q'), function ($sub) use ($request, $reviewRequestTable, $hasBookingRef) {
            $searchString = trim((string) $request->get('q'));
            $sub->where(function ($qq) use ($searchString, $reviewRequestTable, $hasBookingRef) {
                $qq->where("$reviewRequestTable.booking_id", (int) $searchString)
                    ->orWhere("$reviewRequestTable.email", 'ilike', "%{$searchString}%");
                if ($hasBookingRef) {
                    $qq->orWhereHas('booking', function ($bq) use ($searchString) {
                        $bq->where('booking_reference', 'ilike', "%{$searchString}%");
                    });
                }
            });
        });

        $dateCol = $hasSentAt ? 'sent_at' : 'created_at';
        $requests = $query->orderByDesc($dateCol)->paginate(25)->withQueryString();

        return view('admin.reviews.requests.index', [
            'tab'      => 'trash',
            'requests' => $requests,
        ]);
    }
}
