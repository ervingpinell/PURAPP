<?php

namespace App\Http\Controllers\Admin\Bookings;

use App\Http\Controllers\Controller;
use App\Models\{
    Booking, BookingDetail, Schedule, Tour, TourLanguage, User, HotelList, PromoCode, MeetingPoint
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    DB, Log, Auth, Schema
};
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingsExport;
use App\Services\Bookings\{
    BookingCreator, BookingCapacityService, BookingPricingService
};
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

        if ($request->filled('reference'))           $query->where('booking_reference', 'like', '%' . $request->reference . '%');
        if ($request->filled('status'))              $query->where('status', $request->status);
        if ($request->filled('booking_date_from'))   $query->whereDate('booking_date', '>=', $request->booking_date_from);
        if ($request->filled('booking_date_to'))     $query->whereDate('booking_date', '<=', $request->booking_date_to);

        if ($request->filled('tour_date_from') || $request->filled('tour_date_to')) {
            $query->whereHas('detail', function ($q) use ($request) {
                if ($request->filled('tour_date_from')) $q->whereDate('tour_date', '>=', $request->tour_date_from);
                if ($request->filled('tour_date_to'))   $q->whereDate('tour_date', '<=', $request->tour_date_to);
            });
        }

        if ($request->filled('tour_id'))     $query->where('tour_id', $request->tour_id);
        if ($request->filled('schedule_id')) $query->whereHas('detail', fn($q) => $q->where('schedule_id', $request->schedule_id));

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

public function store(Request $request)
{
    $validated = $request->validate([
        'user_id'           => 'required|exists:users,user_id',
        'tour_id'           => 'required|exists:tours,tour_id',
        'schedule_id'       => 'required|exists:schedules,schedule_id',
        'tour_language_id'  => 'required|exists:tour_languages,tour_language_id',
        'tour_date'         => 'required|date',
        'booking_date'      => 'nullable|date',
        'hotel_id'          => 'nullable|exists:hotels_list,hotel_id',
        'is_other_hotel'    => 'nullable|boolean',
        'other_hotel_name'  => 'nullable|string|max:255|required_if:is_other_hotel,1',
        'adults_quantity'   => 'required|integer|min:1',
        'kids_quantity'     => 'nullable|integer|min:0',
        'status'            => 'required|in:pending,confirmed,cancelled',
        'meeting_point_id'  => 'nullable|exists:meeting_points,id',
        'notes'             => 'nullable|string|max:1000',
        'promo_code'        => 'nullable|string|max:100',
    ]);

    try {
        $payload = [
            'user_id'           => (int)$validated['user_id'],
            'tour_id'           => (int)$validated['tour_id'],
            'schedule_id'       => (int)$validated['schedule_id'],
            'tour_language_id'  => (int)$validated['tour_language_id'],
            'tour_date'         => $validated['tour_date'],
            'booking_date'      => $validated['booking_date'] ?? now(),
            'adults_quantity'   => (int)$validated['adults_quantity'],
            'kids_quantity'     => (int)($validated['kids_quantity'] ?? 0),
            'status'            => $validated['status'],
            'promo_code'        => $validated['promo_code'] ?? null,
            'meeting_point_id'  => $validated['meeting_point_id'] ?? null,
            'hotel_id'          => !empty($validated['is_other_hotel']) ? null : ($validated['hotel_id'] ?? null),
            'is_other_hotel'    => (bool)($validated['is_other_hotel'] ?? false),
            'other_hotel_name'  => $validated['other_hotel_name'] ?? null,
            'notes'             => $validated['notes'] ?? null,
        ];

        // Crea con validación de capacidad (lanza RuntimeException si no hay cupo)
        $booking = $this->creator->create($payload, validateCapacity: true);

        return redirect()->route('admin.bookings.index')
            ->with('success', __('m_bookings.bookings.success.created'));

    } catch (\RuntimeException $e) {
        // Si viene nuestra excepción de capacidad, extrae los datos
        $data = json_decode($e->getMessage(), true);
        if (json_last_error() === JSON_ERROR_NONE && ($data['type'] ?? null) === 'capacity') {
            $tour       = \App\Models\Tour::find((int)$validated['tour_id']);
            $schedule   = \App\Models\Schedule::find((int)$validated['schedule_id']);
            $date       = \Carbon\Carbon::parse($validated['tour_date']);

            $friendly = __('m_bookings.bookings.errors.insufficient_capacity', [
                'tour'      => optional($tour)->name ?? 'Tour',
                'date'      => $date->translatedFormat('M d, Y'),
                'time'      => \Carbon\Carbon::parse(optional($schedule)->start_time)->format('g:i A'),
                'requested' => (int)$data['requested'],
                'available' => (int)$data['available'], // disponibles reales
                'max'       => (int)$data['max'],       // capacidad total
            ]);

            return back()->withInput()->withErrors(['capacity' => $friendly]);
        }

        // Otros errores lógicos
        return back()->withInput()->with('error', $e->getMessage());
    } catch (\Throwable $e) {
        \Log::error('Admin booking store error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return back()->withInput()->with('error', __('m_bookings.bookings.errors.create'));
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
        $promoCodeValue   = session('admin_cart_promo.code') ?: $request->input('promo_code');
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
// Dentro de App\Http\Controllers\Admin\Bookings\BookingController

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
        $tourDate = \Carbon\Carbon::parse($validated['tour_date']);
        if ($tourDate->lt(\Carbon\Carbon::today())) {
            DB::rollBack();
            return back()->withInput()
                ->with('showEditModal', $booking->booking_id)
                ->withErrors(['tour_date' => __('m_bookings.bookings.validation.past_date')]);
        }

        $newTour     = \App\Models\Tour::findOrFail($validated['tour_id']);
        $newSchedule = \App\Models\Schedule::findOrFail($validated['schedule_id']);

        // ===== Capacidad para cualquier estado, pero especialmente si pasa a confirmed
        $adults    = (int)$validated['adults_quantity'];
        $kids      = (int)$validated['kids_quantity'];
        $requested = $adults + $kids;

        // Calculamos SIEMPRE igual para consistencia
        $max       = (int) app(\App\Services\Bookings\BookingCapacityService::class)->resolveMaxCapacity($newSchedule, $newTour);
        $confirmed = (int) app(\App\Services\Bookings\BookingCapacityService::class)->confirmedPaxFor(
            $validated['tour_date'], (int)$newSchedule->schedule_id, (int)$booking->booking_id, (int)$newTour->tour_id
        );
        $available = max(0, $max - $confirmed);

        if ($requested > $available) {
            DB::rollBack();
            return back()->withInput()
                ->with('showEditModal', $booking->booking_id)
                ->withErrors([
                    'capacity' => __('m_bookings.bookings.errors.insufficient_capacity', [
                        'tour'      => $newTour->name,
                        'date'      => $tourDate->translatedFormat('M d, Y'),
                        'time'      => \Carbon\Carbon::parse($newSchedule->start_time)->format('g:i A'),
                        'requested' => $requested,
                        'available' => $available, // <- disponibles reales
                        'max'       => $max,       // <- capacidad total
                    ])
                ]);
        }

        // ======== PRECIOS (snapshots) ========
        $detail = $booking->detail()->lockForUpdate()->first();
        if (!$detail) {
            DB::rollBack();
            return back()->with('error', __('m_bookings.bookings.errors.detail_not_found'));
        }

        $tourChanged = (int)$validated['tour_id'] !== (int)$booking->tour_id;
        $adultUnit = $tourChanged ? (float)$newTour->adult_price : (float)($detail->adult_price ?? $newTour->adult_price);
        $kidUnit   = $tourChanged ? (float)$newTour->kid_price   : (float)($detail->kid_price   ?? $newTour->kid_price);

        $detailSubtotal = round($adultUnit * $adults + $kidUnit * $kids, 2);

        // ======== PROMO (vacío = quitar) ========
        $promo = null;
        $promoCodeInput = trim((string)($validated['promo_code'] ?? ''));
        if ($promoCodeInput !== '') {
            $clean = \App\Models\PromoCode::normalize($promoCodeInput);
            $promo = \App\Models\PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$clean])
                ->lockForUpdate()
                ->first();

            if ($promo && method_exists($promo,'isValidToday') && !$promo->isValidToday())          $promo = null;
            if ($promo && method_exists($promo,'hasRemainingUses') && !$promo->hasRemainingUses()) $promo = null;
        }

        // Total con promo
        $total = app(\App\Services\Bookings\BookingPricingService::class)->applyPromo($detailSubtotal, $promo);

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

        // ======== PIVOT: promo_code_redemptions (historial + snapshots) ========
        $currentRedemption = $booking->redemption()->lockForUpdate()->first();

        if ($promo) {
            $appliedAmount = 0.0;
            if ($promo->discount_percent)      $appliedAmount = round($detailSubtotal * ($promo->discount_percent/100), 2);
            elseif ($promo->discount_amount)   $appliedAmount = (float)$promo->discount_amount;

            if ($currentRedemption) {
                $wasPromoId = (int)$currentRedemption->promo_code_id;
                $currentRedemption->update([
                    'promo_code_id'      => (int)$promo->promo_code_id,
                    'applied_amount'     => $appliedAmount,
                    'operation_snapshot' => $promo->operation ?? 'subtract',
                    'percent_snapshot'   => $promo->discount_percent,
                    'amount_snapshot'    => $promo->discount_amount,
                    'used_at'            => now(),
                    'user_id'            => (int)$validated['user_id'],
                ]);

                if ($wasPromoId !== (int)$promo->promo_code_id) {
                    if ($prev = \App\Models\PromoCode::lockForUpdate()->find($wasPromoId)) {
                        $prev->usage_count = max(0, (int)$prev->usage_count - 1);
                        if (is_null($prev->usage_limit) || $prev->usage_count < $prev->usage_limit) {
                            $prev->is_used = false;
                            $prev->used_by_booking_id = null;
                        }
                        $prev->save();
                    }
                    $promo->usage_count = (int)$promo->usage_count + 1;
                    if (!is_null($promo->usage_limit) && $promo->usage_count >= $promo->usage_limit) {
                        $promo->is_used = true;
                        $promo->used_at = now();
                    }
                    $promo->save();
                }
            } else {
                $booking->redemption()->create([
                    'promo_code_id'      => (int)$promo->promo_code_id,
                    'applied_amount'     => $appliedAmount,
                    'operation_snapshot' => $promo->operation ?? 'subtract',
                    'percent_snapshot'   => $promo->discount_percent,
                    'amount_snapshot'    => $promo->discount_amount,
                    'used_at'            => now(),
                    'user_id'            => (int)$validated['user_id'],
                ]);

                $promo->usage_count = (int)$promo->usage_count + 1;
                if (!is_null($promo->usage_limit) && $promo->usage_count >= $promo->usage_limit) {
                    $promo->is_used = true;
                    $promo->used_at = now();
                }
                $promo->save();
            }
        } else {
            if ($currentRedemption) {
                $code = \App\Models\PromoCode::lockForUpdate()->find($currentRedemption->promo_code_id);
                $currentRedemption->delete();

                if ($code) {
                    $code->usage_count = max(0, (int)$code->usage_count - 1);
                    if (is_null($code->usage_limit) || $code->usage_count < $code->usage_limit) {
                        $code->is_used = false;
                        $code->used_by_booking_id = null;
                    }
                    $code->save();
                }
            }
        }

        DB::commit();
        \Log::info("Booking updated successfully: #{$booking->booking_id} by user ID: " . auth()->id());

        return redirect()->route('admin.bookings.index')
            ->with('success', __('m_bookings.bookings.success.updated'));

    } catch (\Throwable $e) {
        DB::rollBack();
        \Log::error("Error updating booking #{$booking->booking_id}: " . $e->getMessage());
        \Log::error($e->getTraceAsString());
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
            // Revierte uso de cupón si procede
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
            Log::error("Delete error #{$booking->booking_id}: ".$e->getMessage());
            return back()->with('error', __('m_bookings.bookings.errors.delete'));
        }
    }

    /** PDF del recibo */
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

    /** Export PDF resumen */
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

        return Pdf::loadView('admin.bookings.pdf-summary', compact('bookings','totalAdults','totalKids','totalPersons'))
            ->download('bookings-report-'.now()->format('Y-m-d').'.pdf');
    }

    /** Export Excel */
    public function exportExcel(Request $request)
    {
        return Excel::download(new BookingsExport($request->all()), 'bookings-'.now()->format('Y-m-d').'.xlsx');
    }

    /** AJAX verificar cupón (para el modal edit) */
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

    $discountAmount  = 0.0;
    $discountPercent = null;

    if (!is_null($promo->discount_percent)) {
        $discountPercent = (float)$promo->discount_percent;
        $discountAmount  = round($subtotal * ($discountPercent/100), 2);
    } elseif (!is_null($promo->discount_amount)) {
        $discountAmount = (float)$promo->discount_amount;
    }

    $operation = ($promo->operation === 'add') ? 'add' : 'subtract';

    return response()->json([
        'valid'            => true,
        'message'          => 'Código válido',
        'operation'        => $operation,
        'discount_amount'  => $discountAmount,
        'discount_percent' => $discountPercent,
    ]);
}


    /* ========= Helpers simples (si los necesitas) ========= */

    private function subtotal(Tour $tour, int $adults, int $kids): float
    {
        return round(($tour->adult_price * $adults) + ($tour->kid_price * $kids), 2);
    }

    private function applyPromoCalc(float $base, ?PromoCode $promo): float
    {
        if (!$promo) return $base;
        $disc = 0.0;
        if (!is_null($promo->discount_percent)) $disc = round($base * ((float)$promo->discount_percent/100), 2);
        elseif (!is_null($promo->discount_amount)) $disc = (float)$promo->discount_amount;
        return $promo->operation === 'add' ? round($base + $disc, 2) : max(0, round($base - $disc, 2));
    }
}
