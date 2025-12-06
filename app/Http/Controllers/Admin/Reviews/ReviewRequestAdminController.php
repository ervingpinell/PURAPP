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

class ReviewRequestAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['can:view-review-requests'])->only(['index', 'indexEligible', 'indexRequested']);
        $this->middleware(['can:create-review-requests'])->only(['send']);
        $this->middleware(['can:edit-review-requests'])->only(['resend', 'remind', 'expire']);
        $this->middleware(['can:delete-review-requests'])->only(['destroy']);
    }

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'eligible');
        return $tab === 'requested'
            ? $this->indexRequested($request)
            : $this->indexEligible($request);
    }

    /** --------- TAB: ELEGIBLES (reservas) --------- */
    private function indexEligible(Request $request)
    {
        $windowDays = max(1, (int) $request->integer('days', 60));
        $to   = now()->toDateString();
        $from = now()->subDays($windowDays)->toDateString();

        $dateCol = $this->bookingDateColumn() ?? 'created_at';

        $revTable = (new Review())->getTable();
        $rrTable  = (new ReviewRequest())->getTable();
        $bkTable  = (new Booking())->getTable();

        $hasUsed       = Schema::hasColumn($rrTable, 'used_at');
        $hasCancelled  = Schema::hasColumn($rrTable, 'cancelled_at');
        $hasExpires    = Schema::hasColumn($rrTable, 'expires_at');
        $hasStatus     = Schema::hasColumn($rrTable, 'status');
        $hasBkRef      = Schema::hasColumn($bkTable, 'booking_reference');

        $q = Booking::query()
            ->with([
                'tour:tour_id,name',
                'user:user_id,full_name,email',
            ]);

        // (opcional) selección explícita de columna de referencia si existe
        if ($hasBkRef) {
            $q->select(["$bkTable.*", "$bkTable.booking_reference"]);
        }

        $q->whereIn('status', ['confirmed', 'completed', 'CONFIRMED', 'COMPLETED'])
            ->whereDate($dateCol, '>=', $from)
            ->whereDate($dateCol, '<=', $to)

            // Sin review local previa (mismo user + tour)
            ->whereNotExists(function ($sub) use ($revTable, $bkTable) {
                $sub->select(DB::raw('1'))
                    ->from($revTable)
                    ->where("$revTable.provider", 'local')
                    ->whereColumn("$revTable.tour_id", "$bkTable.tour_id")
                    ->whereColumn("$revTable.user_id", "$bkTable.user_id");
            })

            // Sin solicitud vigente
            ->whereNotExists(function ($sub) use ($rrTable, $bkTable, $hasUsed, $hasCancelled, $hasExpires, $hasStatus) {
                $sub->select(DB::raw('1'))
                    ->from($rrTable)
                    ->whereColumn("$rrTable.booking_id", "$bkTable.booking_id");

                if ($hasUsed)      $sub->whereNull("$rrTable.used_at");
                if ($hasCancelled) $sub->whereNull("$rrTable.cancelled_at");

                if ($hasExpires) {
                    $sub->where(function ($x) use ($rrTable) {
                        $x->whereNull("$rrTable.expires_at")->orWhere("$rrTable.expires_at", '>', now());
                    });
                } elseif ($hasStatus) {
                    $sub->whereIn("$rrTable.status", ['sent', 'reminded']);
                }
            })

            // Búsqueda libre (incluye booking_reference si existe)
            ->when($request->filled('q'), function ($w) use ($request, $bkTable, $hasBkRef) {
                $qstr = trim((string) $request->get('q'));
                $w->where(function ($qq) use ($qstr, $bkTable, $hasBkRef) {
                    $qq->where("$bkTable.booking_id", (int) $qstr)
                        ->orWhere("$bkTable.customer_name", 'ilike', "%{$qstr}%")
                        ->orWhere("$bkTable.customer_email", 'ilike', "%{$qstr}%");

                    if ($hasBkRef) {
                        $qq->orWhere("$bkTable.booking_reference", 'ilike', "%{$qstr}%");
                    }
                });
            })

            ->when($request->filled('tour_id'), fn($w) => $w->where('tour_id', (int) $request->tour_id))
            ->orderByDesc($dateCol);

        $bookings = $q->paginate(25)->withQueryString();

        return view('admin.reviews.requests.index', [
            'tab'        => 'eligible',
            'bookings'   => $bookings,
            'daysWindow' => $windowDays,
            'dateCol'    => $dateCol,
            'from'       => $from,
            'to'         => $to,
        ]);
    }

    /** --------- TAB: SOLICITADAS (review_requests) --------- */
    private function indexRequested(Request $request)
    {
        $rrTable = (new ReviewRequest())->getTable();
        $bkTable = (new Booking())->getTable();

        $hasUsed       = Schema::hasColumn($rrTable, 'used_at');
        $hasCancelled  = Schema::hasColumn($rrTable, 'cancelled_at');
        $hasExpires    = Schema::hasColumn($rrTable, 'expires_at');
        $hasStatus     = Schema::hasColumn($rrTable, 'status');
        $hasSentAt     = Schema::hasColumn($rrTable, 'sent_at');
        $hasBkRef      = Schema::hasColumn($bkTable, 'booking_reference');

        $q = ReviewRequest::query()
            ->with([
                // incluye booking_reference si existe
                'booking:booking_id,tour_id' . ($hasBkRef ? ',booking_reference' : ''),
                'tour:tour_id,name',
                'user:user_id,full_name,email',
            ]);

        // Filtros de estado
        if ($status = $request->get('status')) {
            $status = strtolower($status);
            $q->where(function ($w) use ($status, $rrTable, $hasUsed, $hasCancelled, $hasExpires, $hasStatus) {
                if ($status === 'active') {
                    if ($hasUsed)      $w->whereNull("$rrTable.used_at");
                    if ($hasCancelled) $w->whereNull("$rrTable.cancelled_at");
                    if ($hasExpires)   $w->where(function ($x) use ($rrTable) {
                        $x->whereNull("$rrTable.expires_at")->orWhere("$rrTable.expires_at", '>', now());
                    });
                } elseif (in_array($status, ['sent', 'reminded'], true)) {
                    if ($hasStatus) $w->where("$rrTable.status", $status);
                } elseif ($status === 'used' && $hasUsed) {
                    $w->whereNotNull("$rrTable.used_at");
                } elseif ($status === 'expired' && $hasExpires) {
                    $w->whereNotNull("$rrTable.expires_at")->where("$rrTable.expires_at", '<=', now());
                } elseif ($status === 'cancelled' && $hasCancelled) {
                    $w->whereNotNull("$rrTable.cancelled_at");
                }
            });
        }

        // Rango fechas
        $from = $request->get('from');
        $to   = $request->get('to');
        $dateCol = $hasSentAt ? 'sent_at' : 'created_at';

        if ($from) $q->whereDate($dateCol, '>=', $from);
        if ($to)   $q->whereDate($dateCol, '<=', $to);

        // Búsqueda (incluye booking.booking_reference si existe)
        $q->when($request->filled('q'), function ($w) use ($request, $rrTable, $hasBkRef) {
            $qstr = trim((string) $request->get('q'));
            $w->where(function ($qq) use ($qstr, $rrTable, $hasBkRef) {
                $qq->where("$rrTable.booking_id", (int) $qstr)
                    ->orWhere("$rrTable.email", 'ilike', "%{$qstr}%");

                if ($hasBkRef) {
                    $qq->orWhereHas('booking', function ($bq) use ($qstr) {
                        $bq->where('booking_reference', 'ilike', "%{$qstr}%");
                    });
                }
            });
        });

        // Filtrar por tour
        $q->when($request->filled('tour_id'), fn($w) => $w->where('tour_id', (int) request('tour_id')));

        $requests = $q->orderByDesc($dateCol)->paginate(25)->withQueryString();

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

        $rr = ReviewRequest::create([
            'booking_id' => $booking->getKey(),
            'user_id'    => $booking->user_id,
            'tour_id'    => $booking->tour_id,
            'email'      => $email,
            'token'      => $token,
            'sent_at'    => Schema::hasColumn((new ReviewRequest())->getTable(), 'sent_at') ? now() : null,
            'expires_at' => Schema::hasColumn((new ReviewRequest())->getTable(), 'expires_at') ? now()->addDays($expDays) : null,
            'status'     => Schema::hasColumn((new ReviewRequest())->getTable(), 'status') ? 'sent' : null,
        ]);

        if ($rr->email) {
            Mail::to($rr->email)->queue(new ReviewRequestLink($rr));
        }

        return back()->with('ok', __('reviews.requests.send_ok'));
    }

    public function resend(ReviewRequest $rr)
    {
        $table = $rr->getTable();

        if (Schema::hasColumn($table, 'used_at') && $rr->used_at) {
            return back()->withErrors(__('reviews.requests.errors.used'));
        }
        if (Schema::hasColumn($table, 'expires_at') && $rr->expires_at && $rr->expires_at->isPast()) {
            return back()->withErrors(__('reviews.requests.errors.expired'));
        }

        if ($rr->email) {
            Mail::to($rr->email)->queue(new ReviewRequestLink($rr));
        }

        if (Schema::hasColumn($table, 'sent_at'))  $rr->sent_at = now();
        if (Schema::hasColumn($table, 'status'))   $rr->status  = 'reminded';
        $rr->save();

        return back()->with('ok', __('reviews.requests.resend_ok'));
    }

    public function remind(ReviewRequest $rr)
    {
        $table = $rr->getTable();

        if (Schema::hasColumn($table, 'used_at') && $rr->used_at) {
            return back()->withErrors(__('reviews.requests.errors.used'));
        }
        if (Schema::hasColumn($table, 'expires_at') && $rr->expires_at && $rr->expires_at->isPast()) {
            return back()->withErrors(__('reviews.requests.errors.expired'));
        }

        if ($rr->email) {
            Mail::to($rr->email)->queue(new ReviewRequestLink($rr));
        }

        if (Schema::hasColumn($table, 'reminded_at')) $rr->reminded_at = now();
        if (Schema::hasColumn($table, 'status'))      $rr->status      = 'reminded';
        $rr->save();

        return back()->with('ok', __('reviews.requests.remind_ok'));
    }

    public function expire(ReviewRequest $rr)
    {
        $table = $rr->getTable();

        if (Schema::hasColumn($table, 'expires_at')) {
            $rr->expires_at = now();
        }
        if (Schema::hasColumn($table, 'status')) {
            $rr->status = 'expired';
        }
        $rr->save();

        return back()->with('ok', __('reviews.requests.expire_ok'));
    }

    public function destroy(ReviewRequest $rr)
    {
        $rr->delete();
        return back()->with('ok', __('reviews.requests.deleted'));
    }

    private function bookingDateColumn(): ?string
    {
        $bkTable = (new Booking())->getTable();
        foreach (
            [
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
            ] as $c
        ) {
            if (Schema::hasColumn($bkTable, $c)) return $c;
        }
        return null;
    }
}
