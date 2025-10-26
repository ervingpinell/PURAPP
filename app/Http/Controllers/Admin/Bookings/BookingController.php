<?php

namespace App\Http\Controllers\Admin\Bookings;

use App\Http\Controllers\Controller;
use App\Models\{Booking, BookingDetail, Schedule, Tour, TourLanguage, User, HotelList, PromoCode, MeetingPoint};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Auth};
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingsExport;
use App\Services\Bookings\{BookingCreator, BookingCapacityService, BookingPricingService};
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function __construct(
        private BookingCreator $creator,
        private BookingCapacityService $capacity
    ) {}

    /** Listado con filtros */
    public function index(Request $request)
    {
        $query = Booking::with([
            'user','tour',
            'detail.schedule','detail.hotel','detail.tourLanguage','detail.meetingPoint',
            'redemption.promoCode',
            'promoCodeLegacy',
        ]);

        if ($request->filled('reference'))     $query->where('booking_reference', 'like', '%' . $request->reference . '%');
        if ($request->filled('status'))        $query->where('status', $request->status);
        if ($request->filled('booking_date_from')) $query->whereDate('booking_date', '>=', $request->booking_date_from);
        if ($request->filled('booking_date_to'))   $query->whereDate('booking_date', '<=', $request->booking_date_to);

        if ($request->filled('tour_date_from') || $request->filled('tour_date_to')) {
            $query->whereHas('detail', function ($q) use ($request) {
                if ($request->filled('tour_date_from')) $q->whereDate('tour_date', '>=', $request->tour_date_from);
                if ($request->filled('tour_date_to'))   $q->whereDate('tour_date', '<=', $request->tour_date_to);
            });
        }

        if ($request->filled('tour_id')) {
            $query->where('tour_id', $request->tour_id);
        }
        if ($request->filled('schedule_id')) {
            $query->whereHas('detail', fn($q) => $q->where('schedule_id', $request->schedule_id));
        }

        $bookings = $query->orderBy('booking_date', 'desc')->paginate(15);

        $tours         = Tour::orderBy('name')->get(['tour_id', 'name']);
        $schedules     = Schedule::orderBy('start_time')->get(['schedule_id', 'start_time', 'end_time']);
        $hotels        = HotelList::where('is_active', true)->orderBy('name')->get(['hotel_id', 'name']);
        $meetingPoints = MeetingPoint::where('is_active', true)
            ->orderByRaw('sort_order IS NULL, sort_order ASC')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.bookings.index', compact('bookings', 'tours', 'schedules', 'hotels', 'meetingPoints'));
    }

    /** Crear booking (admin) */
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
        } catch (ValidationException $ve) {
            return back()->withErrors($ve->validator)->withInput()->with('openModal','register');
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
            if ($payload['status'] === 'confirmed') {
                $tour     = Tour::findOrFail($payload['tour_id']);
                $schedule = Schedule::findOrFail($payload['schedule_id']);

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

        } catch (ValidationException $ve) {
            return back()
                ->withErrors($ve->validator ?? $ve->errors())
                ->withInput()
                ->with('openModal','register');

        } catch (\Throwable $e) {
            report($e);
            // Detalle explícito en logs y un error más específico al usuario si estás en local
            Log::error('Booking store failed: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()
                ->withErrors(['general' => app()->isLocal() ? $e->getMessage() : __('m_bookings.messages.unexpected_error')])
                ->withInput()
                ->with('openModal','register');
        }
    }

    /** Crear desde carrito */
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

        // Cupón desde sesión/admin o input
        $promoCodeValue = session('admin_cart_promo.code') ?: $request->input('promo_code');
        $promoCodeToApply = null;
        if ($promoCodeValue) {
            $clean = PromoCode::normalize($promoCodeValue);
            $promoCodeToApply = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$clean])->first();
            if ($promoCodeToApply && method_exists($promoCodeToApply,'isValidToday') && !$promoCodeToApply->isValidToday())      $promoCodeToApply = null;
            if ($promoCodeToApply && method_exists($promoCodeToApply,'hasRemainingUses') && !$promoCodeToApply->hasRemainingUses()) $promoCodeToApply = null;
        }

        // Validación de capacidad por grupo
        $groups = $cart->items->groupBy(fn($i) => $i->tour_id.'_'.$i->tour_date.'_'.$i->schedule_id);

        foreach ($groups as $items) {
            $first      = $items->first();
            $tour       = $first->tour;
            $tourDate   = $first->tour_date;
            $scheduleId = (int)$first->schedule_id;

            $schedule = $tour->schedules()
                ->where('schedules.schedule_id', $scheduleId)
                ->where('schedules.is_active', true)
                ->wherePivot('is_active', true)
                ->first();

            if (!$schedule) {
                return back()->with('error', __('carts.messages.schedule_unavailable'));
            }

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

        // Crear 1 booking por ítem
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
                'status'            => 'pending',
                'promo_code'        => $promoApplied ? null : ($promoCodeToApply?->code),
                'meeting_point_id'  => $it->meeting_point_id ?: null,
                'hotel_id'          => $it->is_other_hotel ? null : ($it->hotel_id ?: null),
                'is_other_hotel'    => (bool)$it->is_other_hotel,
                'other_hotel_name'  => $it->is_other_hotel ? ($it->other_hotel_name ?? null) : null,
                'notes'             => null,
            ];

            $booking = $this->creator->create($payload, validateCapacity: true);

            if (!$promoApplied && $promoCodeToApply) {
                // incrementa uso + crea pivot
                $promoCodeToApply->redeemForBooking($booking->booking_id, $cart->user_id);
                $promoApplied = true;
            }
        }

        DB::transaction(function() use ($cart) {
            $cart->items()->delete();
            $cart->forceFill(['is_active'=>false,'expires_at'=>now()])->save();
        });
        session()->forget('admin_cart_promo');

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', __('m_bookings.bookings.success.created'));
    }

    /** Update (admin) */
    public function update(Request $request, Booking $booking)
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
            'kids_quantity'     => 'required|integer|min:0',
            'status'            => 'required|in:pending,confirmed,cancelled',
            'meeting_point_id'  => 'nullable|exists:meeting_points,id',
            'notes'             => 'nullable|string|max:1000',
            'promo_code'        => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            // Fecha válida
            $tourDate = Carbon::parse($validated['tour_date']);
            if ($tourDate->lt(Carbon::today())) {
                DB::rollBack();
                return back()->withInput()
                    ->with('showEditModal', $booking->booking_id)
                    ->withErrors(['tour_date' => __('m_bookings.bookings.validation.past_date')]);
            }

            $newTour     = Tour::findOrFail($validated['tour_id']);
            $newSchedule = Schedule::findOrFail($validated['schedule_id']);

            // Capacidad si va a confirmed
            if ($validated['status'] === 'confirmed') {
                $remaining = $this->capacity->remainingCapacity(
                    $newTour, $newSchedule, $validated['tour_date'],
                    excludeBookingId: (int)$booking->booking_id, countHolds: true
                );
                $requested = (int)$validated['adults_quantity'] + (int)$validated['kids_quantity'];
                if ($requested > $remaining) {
                    DB::rollBack();
                    return back()->withInput()
                        ->with('showEditModal', $booking->booking_id)
                        ->withErrors([
                            'capacity' => __('m_bookings.bookings.errors.insufficient_capacity', [
                                'tour'      => $newTour->name,
                                'date'      => Carbon::parse($validated['tour_date'])->format('M d, Y'),
                                'time'      => Carbon::parse($newSchedule->start_time)->format('g:i A'),
                                'requested' => $requested,
                                'available' => $remaining,
                                'max'       => $this->capacity->resolveMaxCapacity($newSchedule, $newTour),
                            ])
                        ]);
                }
            }

            // ======== PRECIOS (snapshot) ========
            $detail = $booking->detail()->lockForUpdate()->first();
            $tourChanged = (int)$validated['tour_id'] !== (int)$booking->tour_id;

            $adultUnit = $tourChanged ? (float)$newTour->adult_price : (float)($detail->adult_price ?? $newTour->adult_price);
            $kidUnit   = $tourChanged ? (float)$newTour->kid_price   : (float)($detail->kid_price   ?? $newTour->kid_price);

            $adults = (int)$validated['adults_quantity'];
            $kids   = (int)$validated['kids_quantity'];

            $detailSubtotal = round($adultUnit * $adults + $kidUnit * $kids, 2);

            // ======== PROMO (vacío = quitar) ========
            $promo = null;
            $promoCodeInput = trim((string)($validated['promo_code'] ?? ''));
            if ($promoCodeInput !== '') {
                $clean = PromoCode::normalize($promoCodeInput);
                $promo = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$clean])->first();

                if ($promo && method_exists($promo,'isValidToday') && !$promo->isValidToday())          $promo = null;
                if ($promo && method_exists($promo,'hasRemainingUses') && !$promo->hasRemainingUses()) $promo = null;
            }

            // Total con promo
            $total = app(BookingPricingService::class)->applyPromo($detailSubtotal, $promo);

            // ======== CABECERA ========
            $booking->update([
                'user_id'          => (int)$validated['user_id'],
                'tour_id'          => (int)$validated['tour_id'],
                'tour_language_id' => (int)$validated['tour_language_id'],
                'booking_date'     => $validated['booking_date'],
                'status'           => $validated['status'],
                'total'            => $total,
                'notes'            => $validated['notes'] ?? null,
            ]);

            // ======== DETALLE (subtotal = snapshot sin promo) ========
            $detail->update([
                'tour_id'           => (int)$validated['tour_id'],
                'schedule_id'       => (int)$validated['schedule_id'],
                'tour_date'         => $validated['tour_date'],
                'tour_language_id'  => (int)$validated['tour_language_id'],
                'adults_quantity'   => $adults,
                'kids_quantity'     => $kids,
                'adult_price'       => $adultUnit,
                'kid_price'         => $kidUnit,
                'total'             => $detailSubtotal,
                'hotel_id'          => !empty($validated['is_other_hotel']) ? null : ($validated['hotel_id'] ?? null),
                'is_other_hotel'    => (bool)($validated['is_other_hotel'] ?? false),
                'other_hotel_name'  => $validated['other_hotel_name'] ?? null,
                'meeting_point_id'  => $validated['meeting_point_id'] ?? null,
            ]);

            // ======== PIVOT: promo_code_redemptions + usos ========
            $currentRedemption = $booking->redemption()->first();

            if ($promo) {
                // Si hay pivot y es el mismo código, solo refrescamos used_at
                if ($currentRedemption && (int)$currentRedemption->promo_code_id === (int)$promo->getKey()) {
                    $currentRedemption->update(['used_at' => now()]);
                } else {
                    // Cambió o no había: revertir anterior y aplicar nuevo con contadores
                    if ($currentRedemption) {
                        $currentRedemption->promoCode?->revokeRedemptionForBooking($booking->booking_id);
                    }
                    $promo->redeemForBooking($booking->booking_id, $booking->user_id);
                }
            } else {
                // quitar promo si existía (restituye uso)
                if ($currentRedemption) {
                    $currentRedemption->promoCode?->revokeRedemptionForBooking($booking->booking_id);
                }
            }

            DB::commit();
            Log::info("Booking updated successfully: #{$booking->booking_id} by user ID: " . auth()->id());

            return redirect()->route('admin.bookings.index')
                ->with('success', __('m_bookings.bookings.success.updated'));

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Error updating booking #{$booking->booking_id}: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->withInput()
                ->with('showEditModal', $booking->booking_id)
                ->with('error', app()->isLocal() ? $e->getMessage() : __('m_bookings.bookings.errors.update'));
        }
    }

    /** Cambiar status con validación de capacidad */
    public function updateStatus(Request $request, Booking $booking)
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
                $schedule = Schedule::find((int)$detail->schedule_id);
                if (!$schedule) {
                    return back()->with('error', __('m_bookings.bookings.errors.schedule_not_found'));
                }

                $remaining = $this->capacity->remainingCapacity($tour, $schedule, $detail->tour_date, excludeBookingId: (int)$booking->booking_id, countHolds: true);
                $requested = (int)$detail->adults_quantity + (int)$detail->kids_quantity;

                if ($requested > $remaining) {
                    return back()->with('error', __('m_bookings.bookings.errors.insufficient_capacity', [
                        'tour'      => optional($tour)->name ?? 'Unknown Tour',
                        'date'      => Carbon::parse($detail->tour_date)->format('M d, Y'),
                        'time'      => Carbon::parse($schedule->start_time)->format('g:i A'),
                        'requested' => $requested,
                        'available' => $remaining,
                        'max'       => $this->capacity->resolveMaxCapacity($schedule, $tour),
                    ]));
                }
            }

            $booking->status = $new;
            $booking->save();

            Log::info("Booking #{$booking->booking_id} status changed from '{$old}' to '{$new}' by user ID: " . auth()->id());

            $messageKey = match($new) {
                'confirmed' => 'status_confirmed',
                'cancelled' => 'status_cancelled',
                'pending'   => 'status_pending',
                default     => 'status_updated'
            };

            return back()->with('success', __("m_bookings.bookings.success.{$messageKey}"));

        } catch (\Throwable $e) {
            Log::error("Error updating booking status for booking #{$booking->booking_id}: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            return back()->with('error', __('m_bookings.bookings.errors.status_update_failed'));
        }
    }

    /** Delete */
    public function destroy(Booking $booking)
    {
        try {
            $id = $booking->booking_id;

            DB::beginTransaction();
            // Si quieres, revierte uso de cupón aquí también
            if ($booking->redemption) {
                $booking->redemption->promoCode?->revokeRedemptionForBooking($booking->booking_id);
            }
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

    /** PDF */
    public function generateReceipt(Booking $booking)
    {
        $booking->load([
            'user','tour',
            'detail.schedule','detail.hotel','detail.tourLanguage','detail.meetingPoint',
            'redemption.promoCode',
            'promoCodeLegacy',
        ]);

        $pdf = Pdf::loadView('admin.bookings.receipt', compact('booking'));

        return $pdf->download("receipt-{$booking->booking_reference}.pdf");
    }

    /** Export PDF */
    public function exportPdf(Request $request)
    {
        $query = Booking::with([
            'user','tour',
            'detail.schedule','detail.hotel','detail.tourLanguage',
            'redemption.promoCode',
            'promoCodeLegacy',
        ]);
        if ($request->filled('reference'))        $query->where('booking_reference', 'like', '%' . $request->reference . '%');
        if ($request->filled('status'))           $query->where('status', $request->status);
        if ($request->filled('booking_date_from'))$query->whereDate('booking_date', '>=', $request->booking_date_from);
        if ($request->filled('booking_date_to'))  $query->whereDate('booking_date', '<=', $request->booking_date_to);

        $bookings = $query->orderBy('booking_date', 'desc')->get();

        $totalAdults  = $bookings->sum(fn($b) => (int) optional($b->detail)->adults_quantity);
        $totalKids    = $bookings->sum(fn($b) => (int) optional($b->detail)->kids_quantity);
        $totalPersons = $totalAdults + $totalKids;

        $pdf = Pdf::loadView('admin.bookings.pdf-summary', compact('bookings', 'totalAdults', 'totalKids', 'totalPersons'));

        return $pdf->download('bookings-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /** Export Excel */
    public function exportExcel(Request $request)
    {
        return Excel::download(new BookingsExport($request->all()), 'bookings-' . now()->format('Y-m-d') . '.xlsx');
    }

    /** AJAX verificar cupón */
    public function verifyPromoCode(Request $request)
    {
        $codeRaw  = (string)$request->input('code','');
        $code     = PromoCode::normalize($codeRaw);
        $subtotal = (float)$request->input('subtotal', 0);

        if (!$code || $subtotal <= 0) {
            return response()->json(['valid'=>false,'message'=>'Datos inválidos']);
        }

        $promo = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$code])->first();
        if (!$promo) {
            return response()->json(['valid'=>false,'message'=>'Código no encontrado']);
        }
        if (method_exists($promo,'isValidToday') && !$promo->isValidToday()) {
            return response()->json(['valid'=>false,'message'=>'Este código ha expirado o aún no es válido']);
        }
        if (method_exists($promo,'hasRemainingUses') && !$promo->hasRemainingUses()) {
            return response()->json(['valid'=>false,'message'=>'Este código ha alcanzado su límite de usos']);
        }

        $discountAmount = 0.0;
        if ($promo->discount_percent)      $discountAmount = round($subtotal * ($promo->discount_percent/100), 2);
        elseif ($promo->discount_amount)   $discountAmount = (float)$promo->discount_amount;

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
