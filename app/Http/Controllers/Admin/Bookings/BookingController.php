<?php

namespace App\Http\Controllers\Admin\Bookings;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Schedule;
use App\Models\Tour;
use App\Models\TourLanguage;
use App\Models\User;
use App\Models\HotelList;
use App\Models\PromoCode;
use App\Models\MeetingPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingsExport;
use App\Services\Bookings\BookingCreator;
use App\Services\Bookings\BookingCapacityService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;



class BookingController extends Controller
{
    public function __construct(
        private BookingCreator $creator,
        private BookingCapacityService $capacity
    ) {}

    /**
     * Display a listing of bookings with filters
     */
    public function index(Request $request)
    {
        $query = Booking::with([
            'user',
            'tour',
            'detail.schedule',
            'detail.hotel',
            'detail.tourLanguage',
            'detail.meetingPoint',
            'promoCode'
        ]);

        if ($request->filled('reference')) {
            $query->where('booking_reference', 'like', '%' . $request->reference . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('booking_date_from')) {
            $query->whereDate('booking_date', '>=', $request->booking_date_from);
        }
        if ($request->filled('booking_date_to')) {
            $query->whereDate('booking_date', '<=', $request->booking_date_to);
        }
        if ($request->filled('tour_date_from') || $request->filled('tour_date_to')) {
            $query->whereHas('detail', function ($q) use ($request) {
                if ($request->filled('tour_date_from')) {
                    $q->whereDate('tour_date', '>=', $request->tour_date_from);
                }
                if ($request->filled('tour_date_to')) {
                    $q->whereDate('tour_date', '<=', $request->tour_date_to);
                }
            });
        }
        if ($request->filled('tour_id')) {
            $query->where('tour_id', $request->tour_id);
        }
        if ($request->filled('schedule_id')) {
            $query->whereHas('detail', function ($q) use ($request) {
                $q->where('schedule_id', $request->schedule_id);
            });
        }

        $bookings = $query->orderBy('booking_date', 'desc')->paginate(15);

        $tours = Tour::orderBy('name')->get(['tour_id', 'name']);
        $schedules = Schedule::orderBy('start_time')->get(['schedule_id', 'start_time', 'end_time']);
        $hotels = HotelList::where('is_active', true)->orderBy('name')->get(['hotel_id', 'name']);
        $meetingPoints = MeetingPoint::where('is_active', true)
            ->orderByRaw('sort_order IS NULL, sort_order ASC')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.bookings.index', compact('bookings', 'tours', 'schedules', 'hotels', 'meetingPoints'));
    }

    /**
     * Store a newly created booking (admin)
     */
public function store(Request $request)
{
    $rules = [
        'user_id'           => ['required','integer','exists:users,user_id'],
        'tour_language_id'  => ['required','integer','exists:tour_languages,tour_language_id'],
        'tour_id'           => ['required','integer','exists:tours,tour_id'],
        'schedule_id'       => ['required','integer','exists:schedules,schedule_id'],
        'tour_date'         => ['required','date','after_or_equal:today'],
        'adults_quantity'   => ['required','integer','min:1'],
        'kids_quantity'     => ['nullable','integer','min:0','max:2'],
        'pickup_mode'       => ['required','in:hotel,point'],
        'hotel_id'          => ['nullable','integer','exists:hotels_list,hotel_id','exclude_if:is_other_hotel,1'],
        'is_other_hotel'    => ['nullable','boolean'],
        'other_hotel_name'  => ['nullable','string','max:255','required_if:is_other_hotel,1'],
        'meeting_point_id'  => ['nullable','integer','exists:meeting_points,id'],
        'promo_code'        => ['nullable','string','max:50'],
        'status'            => ['nullable','in:pending,confirmed,cancelled'],
    ];

    try {
        $data = $request->validate($rules);
    } catch (\Illuminate\Validation\ValidationException $ve) {
        return back()
            ->withErrors($ve->validator)
            ->withInput()
            ->with('openModal','register');
    }

    $isOtherHotel = (bool)($data['is_other_hotel'] ?? false);
    $hotelId      = $isOtherHotel ? null : ($data['hotel_id'] ?? null);

    if (($data['pickup_mode'] ?? 'hotel') === 'point') {
        $isOtherHotel = false;
        $hotelId = null;
    } else {
        $data['meeting_point_id'] = null;
    }

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
        'meeting_point_id'  => $data['meeting_point_id'] ?? null,
        'hotel_id'          => $hotelId,
        'is_other_hotel'    => $isOtherHotel,
        'other_hotel_name'  => $isOtherHotel ? ($data['other_hotel_name'] ?? null) : null,
        'notes'             => null,
    ];

    try {
        // Validar capacidad si guardan en "confirmed" (se cuentan confirmed+pending)
        if ($payload['status'] === 'confirmed') {
            $tour     = \App\Models\Tour::findOrFail($payload['tour_id']);
            $schedule = \App\Models\Schedule::findOrFail($payload['schedule_id']);

            $remaining = $this->capacity->remainingCapacity($tour, $schedule, $payload['tour_date'], excludeBookingId: null, countHolds: true);
            $requested = (int)$payload['adults_quantity'] + (int)$payload['kids_quantity'];

            if ($requested > $remaining) {
                return back()
                    ->withErrors(['capacity' => __("m_bookings.messages.limited_seats_available", [
                        'available' => $remaining,
                        'tour'      => optional($tour)->name,
                        'date'      => $payload['tour_date'],
                    ])])
                    ->withInput()
                    ->with('openModal','register');
            }
        }

        $this->creator->create($payload, validateCapacity: false);

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', __('m_bookings.bookings.success.created'));

    } catch (\Illuminate\Validation\ValidationException $ve) {
        return back()
            ->withErrors($ve->validator ?? $ve->errors())
            ->withInput()
            ->with('openModal','register');

    } catch (\Throwable $e) {
        report($e);
        return back()
            ->withErrors(['general' => __('m_bookings.messages.unexpected_error')])
            ->withInput()
            ->with('openModal','register');
    }
}


public function storeFromCart(Request $request)
{
    $user = Auth::user();

    $cart = $user->cart()
        ->where('is_active', true)
        ->with(['items' => function($q){
            $q->with(['tour','schedule','language','hotel','meetingPoint']);
        }])
        ->first();

    if (!$cart || !$cart->items->count()) {
        return back()->with('error', __('carts.messages.cart_empty'));
    }
    if ($cart->isExpired()) {
        DB::transaction(function() use ($cart){
            $cart->items()->delete();
            $cart->forceFill(['is_active'=>false,'expires_at'=>now()])->save();
        });
        return back()->with('error', __('carts.messages.cart_expired'));
    }

    // Cupón guardado por el admin en sesión o del hidden
    $promoCodeValue = session('admin_cart_promo.code') ?: $request->input('promo_code');
    $promoCodeToApply = null;
    if ($promoCodeValue) {
        $clean = strtoupper(trim(preg_replace('/\s+/', '', $promoCodeValue)));
        $promoCodeToApply = \App\Models\PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$clean])
            ->where('is_used', false)
            ->first();
    }

    // ===== Validación de capacidad por grupo (tour_id + tour_date + schedule_id)
    $groups = $cart->items->groupBy(fn($i) => $i->tour_id.'_'.$i->tour_date.'_'.$i->schedule_id);

    foreach ($groups as $items) {
        $first      = $items->first();
        $tour       = $first->tour;
        $tourDate   = $first->tour_date;
        $scheduleId = (int)$first->schedule_id;

        // Debe existir un schedule activo en el pivot del tour
        $schedule = $tour->schedules()
            ->where('schedules.schedule_id', $scheduleId)
            ->where('schedules.is_active', true)
            ->wherePivot('is_active', true)
            ->first();

        if (!$schedule) {
            return back()->with('error', __('carts.messages.schedule_unavailable'));
        }

        // Capacidad real: confirmed + pending + holds de carritos
        $remaining = $this->capacity->remainingCapacity($tour, $schedule, $tourDate, excludeBookingId: null, countHolds: true);
        $requested = $items->sum(fn($i) => (int)$i->adults_quantity + (int)$i->kids_quantity);

        if ($requested > $remaining) {
            return back()->with('error', __('m_bookings.messages.limited_seats_available', [
                'available' => $remaining,
                'tour'      => $tour->name,
                'date'      => $tourDate,
            ]));
        }
    }

    // ===== Crear 1 booking por ítem
    $created = [];
    $promoApplied = false;

    foreach ($cart->items as $it) {
        $payload = [
            'user_id'           => (int)$cart->user_id,
            'tour_id'           => (int)$it->tour_id,
            'schedule_id'       => (int)$it->schedule_id,
            'tour_language_id'  => (int)$it->tour_language_id,
            'tour_date'         => $it->tour_date,
            'booking_date'      => now(),
            'adults_quantity'   => (int)$it->adults_quantity,
            'kids_quantity'     => (int)$it->kids_quantity,
            'status'            => 'pending', // puedes cambiar a 'confirmed' si así lo decides
            'promo_code'        => $promoApplied ? null : ($promoCodeToApply?->code), // solo la primera
            'meeting_point_id'  => $it->meeting_point_id ?: null,
            'hotel_id'          => $it->is_other_hotel ? null : ($it->hotel_id ?: null),
            'is_other_hotel'    => (bool)$it->is_other_hotel,
            'other_hotel_name'  => $it->is_other_hotel ? ($it->other_hotel_name ?? null) : null,
            'notes'             => null,
        ];

        // Validación defensiva en el creador (el servicio cuenta confirmed+pending)
        $booking = $this->creator->create($payload, validateCapacity: true);
        $created[] = $booking->booking_id;

        // Marcar cupón usado tras aplicarlo a la primera reserva
        if (!$promoApplied && $promoCodeToApply) {
            $promoCodeToApply->redeemForBooking($booking->booking_id, $cart->user_id);
            $promoApplied = true;
        }
    }

    // Limpia carrito y sesión de promo (admin)
    DB::transaction(function() use ($cart) {
        $cart->items()->delete();
        $cart->forceFill(['is_active'=>false,'expires_at'=>now()])->save();
    });
    session()->forget('admin_cart_promo');

    return redirect()
        ->route('admin.bookings.index')
        ->with('success', __('m_bookings.bookings.success.created'));
}


    /**
     * Update the specified booking (admin)
     */
public function update(Request $request, \App\Models\Booking $booking)
{
    $validated = $request->validate([
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
        'meeting_point_id'  => 'nullable|exists:meeting_points,id',
        'notes'             => 'nullable|string|max:1000',
    ]);

    DB::beginTransaction();

    try {
        $tourDate = \Carbon\Carbon::parse($validated['tour_date']);
        if ($tourDate->lt(\Carbon\Carbon::today())) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('showEditModal', $booking->booking_id)
                ->withErrors(['tour_date' => __('m_bookings.bookings.validation.past_date')]);
        }

        $tour     = \App\Models\Tour::findOrFail($validated['tour_id']);
        $schedule = \App\Models\Schedule::findOrFail($validated['schedule_id']);

        // Si pasa a confirmado (o sigue confirmado), validar capacidad real
        if ($validated['status'] === 'confirmed' && $booking->status !== 'confirmed') {
            $remaining = $this->capacity->remainingCapacity($tour, $schedule, $validated['tour_date'], excludeBookingId: (int)$booking->booking_id, countHolds: true);
            $requested = (int)$validated['adults_quantity'] + (int)$validated['kids_quantity'];

            if ($requested > $remaining) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->with('showEditModal', $booking->booking_id)
                    ->withErrors([
                        'capacity' => __('m_bookings.bookings.errors.insufficient_capacity', [
                            'tour'      => $tour->name,
                            'date'      => \Carbon\Carbon::parse($validated['tour_date'])->format('M d, Y'),
                            'time'      => \Carbon\Carbon::parse($schedule->start_time)->format('g:i A'),
                            'requested' => $requested,
                            'available' => $remaining,
                            'max'       => $this->capacity->resolveMaxCapacity($schedule, $tour),
                        ])
                    ]);
            }
        }

        // Precios base
        $adultPrice = (float) $tour->adult_price;
        $kidPrice   = (float) $tour->kid_price;
        $baseTotal  = round($adultPrice * (int)$validated['adults_quantity'] + $kidPrice * (int)$validated['kids_quantity'], 2);
        $total      = $baseTotal;

        // Respetar promo asociada
        if ($booking->promo_code_id) {
            $promo = \App\Models\PromoCode::find($booking->promo_code_id);
            if ($promo) {
                $discount = 0.0;
                if ($promo->discount_percent) {
                    $discount = round($baseTotal * ($promo->discount_percent / 100), 2);
                } elseif ($promo->discount_amount) {
                    $discount = (float) $promo->discount_amount;
                }
                $total = $promo->operation === 'add'
                    ? round($baseTotal + $discount, 2)
                    : max(0, round($baseTotal - $discount, 2));
            }
        }

        // Cabecera
        $booking->update([
            'user_id'          => (int)$validated['user_id'],
            'tour_id'          => (int)$validated['tour_id'],
            'tour_language_id' => (int)$validated['tour_language_id'],
            'booking_date'     => $validated['booking_date'],
            'status'           => $validated['status'],
            'total'            => $total,
            'notes'            => $validated['notes'] ?? null,
        ]);

        // Detalle
        $booking->detail->update([
            'schedule_id'       => (int)$validated['schedule_id'],
            'tour_date'         => $validated['tour_date'],
            'hotel_id'          => !empty($validated['is_other_hotel']) ? null : ($validated['hotel_id'] ?? null),
            'is_other_hotel'    => (bool)($validated['is_other_hotel'] ?? false),
            'other_hotel_name'  => $validated['other_hotel_name'] ?? null,
            'adults_quantity'   => (int)$validated['adults_quantity'],
            'kids_quantity'     => (int)$validated['kids_quantity'],
            'meeting_point_id'  => $validated['meeting_point_id'] ?? null,
        ]);

        DB::commit();

        \Log::info("Booking updated successfully: #{$booking->booking_id} by user ID: " . auth()->id());

        return redirect()->route('admin.bookings.index')
            ->with('success', __('m_bookings.bookings.success.updated'));

    } catch (\Throwable $e) {
        DB::rollBack();
        \Log::error("Error updating booking #{$booking->booking_id}: " . $e->getMessage());
        \Log::error($e->getTraceAsString());

        return back()
            ->withInput()
            ->with('showEditModal', $booking->booking_id)
            ->with('error', __('m_bookings.bookings.errors.update'));
    }
}

    /**
     * Update booking status with capacity validation (admin)
     */
public function updateStatus(Request $request, \App\Models\Booking $booking)
{
    $request->validate([
        'status' => 'required|in:pending,confirmed,cancelled'
    ]);

    try {
        $old = $booking->status;
        $new = $request->status;

        if ($new === 'confirmed' && $old !== 'confirmed') {
            $detail = $booking->detail;
            if (!$detail) {
                return back()->with('error', __('m_bookings.bookings.errors.detail_not_found'));
            }

            $tour     = $booking->tour;
            $schedule = \App\Models\Schedule::find((int)$detail->schedule_id);
            if (!$schedule) {
                return back()->with('error', __('m_bookings.bookings.errors.schedule_not_found'));
            }

            $remaining = $this->capacity->remainingCapacity($tour, $schedule, $detail->tour_date, excludeBookingId: (int)$booking->booking_id, countHolds: true);
            $requested = (int)$detail->adults_quantity + (int)$detail->kids_quantity;

            if ($requested > $remaining) {
                return back()->with('error', __('m_bookings.bookings.errors.insufficient_capacity', [
                    'tour'      => optional($tour)->name ?? 'Unknown Tour',
                    'date'      => \Carbon\Carbon::parse($detail->tour_date)->format('M d, Y'),
                    'time'      => \Carbon\Carbon::parse($schedule->start_time)->format('g:i A'),
                    'requested' => $requested,
                    'available' => $remaining,
                    'max'       => $this->capacity->resolveMaxCapacity($schedule, $tour),
                ]));
            }
        }

        $booking->status = $new;
        $booking->save();

        \Log::info("Booking #{$booking->booking_id} status changed from '{$old}' to '{$new}' by user ID: " . auth()->id());

        $messageKey = match($new) {
            'confirmed' => 'status_confirmed',
            'cancelled' => 'status_cancelled',
            'pending'   => 'status_pending',
            default     => 'status_updated'
        };

        return back()->with('success', __("m_bookings.bookings.success.{$messageKey}"));

    } catch (\Throwable $e) {
        \Log::error("Error updating booking status for booking #{$booking->booking_id}: " . $e->getMessage());
        \Log::error($e->getTraceAsString());

        return back()->with('error', __('m_bookings.bookings.errors.status_update_failed'));
    }
}


    /**
     * Remove the specified booking (admin)
     */
    public function destroy(Booking $booking)
    {
        try {
            $id = $booking->booking_id;

            DB::beginTransaction();
            $booking->detail()->delete();
            $booking->delete();
            DB::commit();

            Log::info("Booking deleted successfully: #{$id} by user ID: " . auth()->id());

            return redirect()->route('admin.bookings.index')
                ->with('success', __('m_bookings.bookings.success.deleted'));

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Error deleting booking #{$booking->booking_id}: " . $e->getMessage());

            return back()->with('error', __('m_bookings.bookings.errors.delete'));
        }
    }

    /**
     * Generate receipt PDF (admin)
     */
    public function generateReceipt(Booking $booking)
    {
        $booking->load(['user','tour','detail.schedule','detail.hotel','detail.tourLanguage','detail.meetingPoint','promoCode']);

        $pdf = Pdf::loadView('admin.bookings.receipt', compact('booking'));

        return $pdf->download("receipt-{$booking->booking_reference}.pdf");
    }

    /**
     * Export bookings to PDF (admin)
     */
    public function exportPdf(Request $request)
    {
        $query = Booking::with(['user', 'tour', 'detail.schedule', 'detail.hotel', 'detail.tourLanguage', 'promoCode']);

        if ($request->filled('reference')) {
            $query->where('booking_reference', 'like', '%' . $request->reference . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('booking_date_from')) {
            $query->whereDate('booking_date', '>=', $request->booking_date_from);
        }
        if ($request->filled('booking_date_to')) {
            $query->whereDate('booking_date', '<=', $request->booking_date_to);
        }

        $bookings = $query->orderBy('booking_date', 'desc')->get();

        $totalAdults = $bookings->sum(fn($b) => (int) optional($b->detail)->adults_quantity);
        $totalKids   = $bookings->sum(fn($b) => (int) optional($b->detail)->kids_quantity);
        $totalPersons= $totalAdults + $totalKids;

        $pdf = Pdf::loadView('admin.bookings.pdf-summary', compact('bookings', 'totalAdults', 'totalKids', 'totalPersons'));

        return $pdf->download('bookings-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export bookings to Excel (admin)
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(new BookingsExport($request->all()), 'bookings-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Verify promo code via AJAX (admin)
     */
    public function verifyPromoCode(Request $request)
    {
        $codeRaw  = (string) $request->input('code', '');
        $code     = PromoCode::normalize($codeRaw);
        $subtotal = (float) $request->input('subtotal', 0);

        if (!$code || $subtotal <= 0) {
            return response()->json(['valid' => false, 'message' => 'Datos inválidos']);
        }

        $promo = PromoCode::where('code', $code)->first();
        if (!$promo) {
            return response()->json(['valid' => false, 'message' => 'Código no encontrado']);
        }
        if (!$promo->isValidToday()) {
            return response()->json(['valid' => false, 'message' => 'Este código ha expirado o aún no es válido']);
        }
        if (!$promo->hasRemainingUses()) {
            return response()->json(['valid' => false, 'message' => 'Este código ha alcanzado su límite de usos']);
        }

        $discountAmount = 0.0;
        if ($promo->discount_percent) {
            $discountAmount = round($subtotal * ($promo->discount_percent / 100), 2);
        } elseif ($promo->discount_amount) {
            $discountAmount = (float) $promo->discount_amount;
        }

        $operation = $promo->operation === 'add' ? 'add' : 'subtract';

        return response()->json([
            'valid'            => true,
            'code'             => $code,
            'discount_amount'  => $discountAmount,
            'discount_percent' => $promo->discount_percent,
            'operation'        => $operation,
            'message'          => 'Código válido'
        ]);
    }
}
