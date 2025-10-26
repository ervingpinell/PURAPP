<?php

namespace App\Http\Controllers\Admin\Bookings;

use App\Http\Controllers\Controller;
use App\Models\{Booking, Schedule, Tour, HotelList, PromoCode, MeetingPoint};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Auth, Schema};
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingsExport;
use App\Services\Bookings\{BookingCreator, BookingCapacityService};

class BookingController extends Controller
{
    public function __construct(
        private BookingCreator $creator,
        private BookingCapacityService $capacity
    ) {}

    /* =========================
     * List with basic filters
     * ========================= */
    public function index(Request $r)
    {
        $q = Booking::with(['user','tour','detail.schedule','detail.hotel','detail.tourLanguage','detail.meetingPoint','promoCode']);

        if ($r->filled('reference'))      $q->where('booking_reference','like','%'.$r->reference.'%');
        if ($r->filled('status'))         $q->where('status',$r->status);
        if ($r->filled('booking_date_from')) $q->whereDate('booking_date','>=',$r->booking_date_from);
        if ($r->filled('booking_date_to'))   $q->whereDate('booking_date','<=',$r->booking_date_to);
        if ($r->filled('tour_id'))        $q->where('tour_id',$r->tour_id);
        if ($r->filled('schedule_id'))    $q->whereHas('detail',fn($x)=>$x->where('schedule_id',$r->schedule_id));
        if ($r->filled('tour_date_from') || $r->filled('tour_date_to')) {
            $q->whereHas('detail', function($x) use ($r){
                if ($r->filled('tour_date_from')) $x->whereDate('tour_date','>=',$r->tour_date_from);
                if ($r->filled('tour_date_to'))   $x->whereDate('tour_date','<=',$r->tour_date_to);
            });
        }

        return view('admin.bookings.index', [
            'bookings'      => $q->orderBy('booking_date','desc')->paginate(15),
            'tours'         => Tour::orderBy('name')->get(['tour_id','name']),
            'schedules'     => Schedule::orderBy('start_time')->get(['schedule_id','start_time','end_time']),
            'hotels'        => HotelList::where('is_active',true)->orderBy('name')->get(['hotel_id','name']),
            'meetingPoints' => MeetingPoint::where('is_active',true)->orderByRaw('sort_order IS NULL, sort_order ASC')->orderBy('name')->get(),
        ]);
    }

    /* =========================
     * Create (admin)
     * ========================= */
    public function store(Request $r)
    {
        $data = $r->validate([
            'user_id'           => 'required|integer|exists:users,user_id',
            'tour_language_id'  => 'required|integer|exists:tour_languages,tour_language_id',
            'tour_id'           => 'required|integer|exists:tours,tour_id',
            'schedule_id'       => 'required|integer|exists:schedules,schedule_id',
            'tour_date'         => 'required|date|after_or_equal:today',
            'adults_quantity'   => 'required|integer|min:1',
            'kids_quantity'     => 'nullable|integer|min:0|max:2',
            'pickup_mode'       => 'required|in:hotel,point',
            'hotel_id'          => 'nullable|integer|exists:hotels_list,hotel_id|exclude_if:is_other_hotel,1',
            'is_other_hotel'    => 'nullable|boolean',
            'other_hotel_name'  => 'nullable|string|max:255|required_if:is_other_hotel,1',
            'meeting_point_id'  => 'nullable|integer|exists:meeting_points,meeting_point_id',
            'promo_code'        => 'nullable|string|max:50',
            'status'            => 'nullable|in:pending,confirmed,cancelled',
        ]);

        // normalize pickup
        $isOther = (bool)($data['is_other_hotel'] ?? false);
        $hotelId = ($data['pickup_mode'] ?? 'hotel') === 'point' ? null : ($isOther ? null : ($data['hotel_id'] ?? null));
        $meeting = ($data['pickup_mode'] ?? 'hotel') === 'point' ? ($data['meeting_point_id'] ?? null) : null;

        $payload = [
            'user_id'           => (int)$data['user_id'],
            'tour_id'           => (int)$data['tour_id'],
            'schedule_id'       => (int)$data['schedule_id'],
            'tour_language_id'  => (int)$data['tour_language_id'],
            'tour_date'         => $data['tour_date'],
            'booking_date'      => now(),
            'adults_quantity'   => (int)$data['adults_quantity'],
            'kids_quantity'     => (int)($data['kids_quantity'] ?? 0),
            'status'            => $data['status'] ?? 'pending',
            'promo_code'        => $data['promo_code'] ?? null,
            'meeting_point_id'  => $meeting,
            'hotel_id'          => $hotelId,
            'is_other_hotel'    => ($data['pickup_mode'] ?? 'hotel') === 'hotel' ? $isOther : false,
            'other_hotel_name'  => $isOther ? ($data['other_hotel_name'] ?? null) : null,
            'notes'             => null,
        ];

        // if saving confirmed, check capacity
        if ($payload['status'] === 'confirmed') {
            $tour = Tour::findOrFail($payload['tour_id']);
            $sch  = Schedule::findOrFail($payload['schedule_id']);
            $requested = $payload['adults_quantity'] + $payload['kids_quantity'];
            $remaining = $this->capacity->remainingCapacity($tour, $sch, $payload['tour_date'], excludeBookingId:null, countHolds:true);
            if ($requested > $remaining) {
                return back()->withErrors(['capacity'=>__("m_bookings.messages.limited_seats_available",[
                    'available'=>$remaining,'tour'=>$tour->name,'date'=>$payload['tour_date']
                ])])->withInput()->with('openModal','register');
            }
        }

        $this->creator->create($payload, validateCapacity:false);

        return redirect()->route('admin.bookings.index')
            ->with('success', __('m_bookings.bookings.success.created'));
    }

    /* =========================
     * Update (admin)
     * ========================= */
    public function update(Request $r, Booking $booking)
    {
        $mpCol = Schema::hasColumn('meeting_points','meeting_point_id') ? 'meeting_point_id' : 'id';

        $v = $r->validate([
            'user_id'           => 'required|exists:users,user_id',
            'tour_id'           => 'required|exists:tours,tour_id',
            'schedule_id'       => 'required|exists:schedules,schedule_id',
            'tour_language_id'  => 'required|exists:tour_languages,tour_language_id',
            'tour_date'         => 'required|date',
            'booking_date'      => 'required|date',
            'hotel_id'          => 'nullable|exists:hotels_list,hotel_id',
            'is_other_hotel'    => 'nullable|boolean',
            'other_hotel_name'  => 'nullable|string|max:255|required_if:is_other_hotel,1',
            'adults_quantity'   => 'required|integer|min:1',
            'kids_quantity'     => 'required|integer|min:0|max:2',
            'status'            => 'required|in:pending,confirmed,cancelled',
            'meeting_point_id'  => "nullable|exists:meeting_points,{$mpCol}",
            'notes'             => 'nullable|string|max:1000',
            // promo flags from modal
            'promo_action'      => 'nullable|in:keep,apply,remove',
            'promo_code'        => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            // date + capacity
            if (\Carbon\Carbon::parse($v['tour_date'])->lt(\Carbon\Carbon::today())) {
                DB::rollBack();
                return back()->withInput()->with('showEditModal',$booking->booking_id)
                    ->withErrors(['tour_date'=>__('m_bookings.bookings.validation.past_date')]);
            }

            $tour = Tour::findOrFail($v['tour_id']);
            $sch  = Schedule::findOrFail($v['schedule_id']);
            if ($v['status']==='confirmed') {
                $req = $v['adults_quantity'] + $v['kids_quantity'];
                $rem = $this->capacity->remainingCapacity($tour, $sch, $v['tour_date'], excludeBookingId:(int)$booking->booking_id, countHolds:true);
                if ($req > $rem) {
                    DB::rollBack();
                    return back()->withInput()->with('showEditModal',$booking->booking_id)->withErrors([
                        'capacity' => __('m_bookings.bookings.errors.insufficient_capacity',[
                            'tour'=>$tour->name,
                            'date'=>\Carbon\Carbon::parse($v['tour_date'])->format('M d, Y'),
                            'time'=>\Carbon\Carbon::parse($sch->start_time)->format('g:i A'),
                            'requested'=>$req,'available'=>$rem,
                            'max'=>$this->capacity->resolveMaxCapacity($sch,$tour),
                        ])
                    ]);
                }
            }

            // pricing
            $base = $this->subtotal($tour, (int)$v['adults_quantity'], (int)$v['kids_quantity']);

            // promo resolution (never touches bookings.promo_code_id)
            $action     = $r->input('promo_action','keep'); // keep|apply|remove
            $inputCode  = trim((string)$r->input('promo_code',''));
            $current    = $booking->promoCode; // via redemption
            $candidate  = ($action==='apply' && $inputCode!=='') ? $this->findPromo($inputCode) : null;

            if ($action==='apply' && !$candidate) {
                DB::rollBack();
                return back()->withInput()->with('showEditModal',$booking->booking_id)
                    ->withErrors(['promo_code'=>__('m_config.promocode.messages.invalid_or_used')]);
            }
            if ($candidate) {
                if (method_exists($candidate,'isValidToday') && !$candidate->isValidToday()) {
                    DB::rollBack();
                    return back()->withInput()->with('showEditModal',$booking->booking_id)
                        ->withErrors(['promo_code'=>__('carts.messages.code_expired_or_not_yet')]);
                }
                if (method_exists($candidate,'hasRemainingUses') && !$candidate->hasRemainingUses()) {
                    DB::rollBack();
                    return back()->withInput()->with('showEditModal',$booking->booking_id)
                        ->withErrors(['promo_code'=>__('carts.messages.code_limit_reached')]);
                }
                if ($current && strtoupper($current->code) === strtoupper($candidate->code)) {
                    $action = 'keep'; // same coupon → ignore
                    $candidate = null;
                }
            }

            $effective = match($action) {
                'remove' => null,
                'apply'  => $candidate,
                default  => $current,
            };
            $total = $this->applyPromoCalc($base, $effective);

            // header
            $booking->update([
                'user_id'      => (int)$v['user_id'],
                'tour_id'      => (int)$v['tour_id'],
                'booking_date' => $v['booking_date'],
                'status'       => $v['status'],
                'total'        => $total,
                'notes'        => $v['notes'] ?? null,
            ]);

            // detail
            $booking->detail->update([
                'schedule_id'       => (int)$v['schedule_id'],
                'tour_date'         => $v['tour_date'],
                'tour_language_id'  => (int)$v['tour_language_id'],
                'hotel_id'          => !empty($v['is_other_hotel']) ? null : ($v['hotel_id'] ?? null),
                'is_other_hotel'    => (bool)($v['is_other_hotel'] ?? false),
                'other_hotel_name'  => $v['other_hotel_name'] ?? null,
                'adults_quantity'   => (int)$v['adults_quantity'],
                'kids_quantity'     => (int)$v['kids_quantity'],
                'meeting_point_id'  => $v['meeting_point_id'] ?? null,
            ]);

            // redemptions
            if ($action==='remove' && $current)   $this->releaseRedemption($booking, $current);
            if ($action==='apply'  && $candidate) $this->upsertRedemption($booking, $candidate);

            DB::commit();
            return redirect()->route('admin.bookings.index')
                ->with('success', __('m_bookings.bookings.success.updated'));

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Booking update failed', [
                'booking_id'=>$booking->booking_id,'user_id'=>auth()->id(),
                'message'=>$e->getMessage(),'payload'=>$r->all()
            ]);
            return back()->withInput()->with('showEditModal',$booking->booking_id)
                ->with('error', config('app.debug') ? 'Error: '.$e->getMessage() : __('m_bookings.bookings.errors.update'));
        }
    }

    /* =========================
     * Status only
     * ========================= */
    public function updateStatus(Request $r, Booking $booking)
    {
        $r->validate(['status'=>'required|in:pending,confirmed,cancelled']);

        try {
            $old = $booking->status; $new = $r->status;

            if ($new==='confirmed' && $old!=='confirmed') {
                $d = $booking->detail;
                if (!$d) return back()->with('error', __('m_bookings.bookings.errors.detail_not_found'));
                $tour = $booking->tour;
                $sch  = Schedule::find((int)$d->schedule_id) ?: null;
                if (!$sch) return back()->with('error', __('m_bookings.bookings.errors.schedule_not_found'));

                $req = (int)$d->adults_quantity + (int)$d->kids_quantity;
                $rem = $this->capacity->remainingCapacity($tour, $sch, $d->tour_date, excludeBookingId:(int)$booking->booking_id, countHolds:true);
                if ($req > $rem) {
                    return back()->with('error', __('m_bookings.bookings.errors.insufficient_capacity', [
                        'tour'=>$tour?->name,'date'=>\Carbon\Carbon::parse($d->tour_date)->format('M d, Y'),
                        'time'=>\Carbon\Carbon::parse($sch->start_time)->format('g:i A'),
                        'requested'=>$req,'available'=>$rem,
                        'max'=>$this->capacity->resolveMaxCapacity($sch,$tour),
                    ]));
                }
            }

            $booking->update(['status'=>$new]);
            Log::info("Booking #{$booking->booking_id} status: {$old} → {$new} by ".auth()->id());

            return back()->with('success', __("m_bookings.bookings.success.".match($new){
                'confirmed'=>'status_confirmed','cancelled'=>'status_cancelled','pending'=>'status_pending', default=>'status_updated'
            }));

        } catch (\Throwable $e) {
            Log::error("Status update error #{$booking->booking_id}: ".$e->getMessage());
            return back()->with('error', __('m_bookings.bookings.errors.status_update_failed'));
        }
    }

    /* =========================
     * Delete
     * ========================= */
    public function destroy(Booking $booking)
    {
        try {
            DB::transaction(function() use ($booking){
                $booking->detail()->delete();
                $booking->delete();
            });
            return redirect()->route('admin.bookings.index')
                ->with('success', __('m_bookings.bookings.success.deleted'));
        } catch (\Throwable $e) {
            Log::error("Delete error #{$booking->booking_id}: ".$e->getMessage());
            return back()->with('error', __('m_bookings.bookings.errors.delete'));
        }
    }

    /* =========================
     * PDFs / Excel
     * ========================= */
    public function generateReceipt(Booking $booking)
    {
        $booking->load(['user','tour','detail.schedule','detail.hotel','detail.tourLanguage','detail.meetingPoint','promoCode']);
        return Pdf::loadView('admin.bookings.receipt', compact('booking'))
            ->download("receipt-{$booking->booking_reference}.pdf");
    }

    public function exportPdf(Request $r)
    {
        $q = Booking::with(['user','tour','detail.schedule','detail.hotel','detail.tourLanguage','promoCode']);
        if ($r->filled('reference'))          $q->where('booking_reference','like','%'.$r->reference.'%');
        if ($r->filled('status'))             $q->where('status',$r->status);
        if ($r->filled('booking_date_from'))  $q->whereDate('booking_date','>=',$r->booking_date_from);
        if ($r->filled('booking_date_to'))    $q->whereDate('booking_date','<=',$r->booking_date_to);
        $bookings = $q->orderBy('booking_date','desc')->get();

        $totalAdults = $bookings->sum(fn($b)=>(int)optional($b->detail)->adults_quantity);
        $totalKids   = $bookings->sum(fn($b)=>(int)optional($b->detail)->kids_quantity);
        $totalPersons= $totalAdults + $totalKids;

        return Pdf::loadView('admin.bookings.pdf-summary', compact('bookings','totalAdults','totalKids','totalPersons'))
            ->download('bookings-report-'.now()->format('Y-m-d').'.pdf');
    }

    public function exportExcel(Request $r)
    {
        return Excel::download(new BookingsExport($r->all()), 'bookings-'.now()->format('Y-m-d').'.xlsx');
    }

    /* =========================
     * AJAX: verify promo
     * ========================= */
    public function verifyPromoCode(Request $r)
    {
        $code = PromoCode::normalize((string)$r->input('code',''));
        $subtotal = (float)$r->input('subtotal',0);

        if (!$code) return response()->json(['valid'=>false,'message'=>'Invalid data'], 422);

        $promo = PromoCode::where('code',$code)->first();
        if (!$promo) return response()->json(['valid'=>false,'message'=>'Code not found'], 404);
        if (method_exists($promo,'isValidToday') && !$promo->isValidToday())
            return response()->json(['valid'=>false,'message'=>'Code expired or not active'], 422);
        if (method_exists($promo,'hasRemainingUses') && !$promo->hasRemainingUses())
            return response()->json(['valid'=>false,'message'=>'Code usage limit reached'], 422);
        if (isset($promo->is_used) && $promo->is_used)
            return response()->json(['valid'=>false,'message'=>'Code already used'], 422);

        $resp = [
            'valid'            => true,
            'code'             => $promo->code,
            'operation'        => $promo->operation === 'add' ? 'add' : 'subtract',
            'discount_percent' => $promo->discount_percent !== null ? (float)$promo->discount_percent : null,
            'discount_amount'  => $promo->discount_amount  !== null ? (float)$promo->discount_amount  : null,
            'message'          => 'Valid code',
        ];

        if ($subtotal > 0) {
            $adj  = $promo->discount_percent !== null
                ? round($subtotal * ((float)$promo->discount_percent / 100), 2)
                : min((float)$promo->discount_amount, $subtotal);
            $sign = $promo->operation === 'add' ? +1 : -1;
            $resp['adjustment_amount'] = $adj;
            $resp['new_total'] = round(max($subtotal + ($sign * $adj), 0), 2);
        }

        return response()->json($resp);
    }

    /* ========= Helpers ========= */

    private function subtotal(Tour $tour, int $adults, int $kids): float
    {
        return round(($tour->adult_price * $adults) + ($tour->kid_price * $kids), 2);
    }

    private function findPromo(string $raw): ?PromoCode
    {
        $norm = PromoCode::normalize($raw) ?? strtoupper(trim(preg_replace('/\s+/', '', $raw)));
        return PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$norm])->first();
    }

    private function applyPromoCalc(float $base, ?PromoCode $promo): float
    {
        if (!$promo) return $base;
        $disc = 0.0;
        if (!is_null($promo->discount_percent)) $disc = round($base * ((float)$promo->discount_percent/100), 2);
        elseif (!is_null($promo->discount_amount)) $disc = (float)$promo->discount_amount;
        return $promo->operation === 'add' ? round($base + $disc, 2) : max(0, round($base - $disc, 2));
    }

    private function upsertRedemption(Booking $booking, PromoCode $promo): void
    {
        $pk = Schema::hasColumn('promo_codes','promo_code_id') ? 'promo_code_id' : 'id';
        if (class_exists(\App\Models\PromoCodeRedemption::class)) {
            \App\Models\PromoCodeRedemption::updateOrCreate(
                ['booking_id' => $booking->booking_id],
                ['promo_code_id' => (int)$promo->{$pk}, 'user_id' => $booking->user_id]
            );
        }
        if (property_exists($promo,'is_used')) $promo->is_used = true;
        if (property_exists($promo,'used_by_booking_id')) $promo->used_by_booking_id = $booking->booking_id;
        if (property_exists($promo,'used_by_user_id'))    $promo->used_by_user_id = $booking->user_id;
        if (property_exists($promo,'usage_count'))        $promo->usage_count = (int)$promo->usage_count + 1;
        $promo->save();
    }

    private function releaseRedemption(Booking $booking, PromoCode $promo): void
    {
        if (class_exists(\App\Models\PromoCodeRedemption::class)) {
            \App\Models\PromoCodeRedemption::where('booking_id',$booking->booking_id)->delete();
        }
        if (property_exists($promo,'is_used')) $promo->is_used = false;
        if (property_exists($promo,'used_by_booking_id')) $promo->used_by_booking_id = null;
        if (property_exists($promo,'used_by_user_id'))    $promo->used_by_user_id = null;
        if (property_exists($promo,'usage_count') && (int)$promo->usage_count > 0) $promo->usage_count--;
        $promo->save();
    }
}
