<?php

namespace App\Http\Controllers\Admin\Bookings;

use App\Http\Controllers\Controller;
use App\Models\{
    Booking, BookingDetail, Schedule, Tour, TourLanguage, User, HotelList, PromoCode, MeetingPoint, Cart
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Auth};
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingsExport;
use App\Services\Bookings\{
    BookingCreator,
    BookingCapacityService,
    BookingPricingService,
    BookingValidationService
};

class BookingController extends Controller
{
    public function __construct(
        private BookingCreator $creator,
        private BookingCapacityService $capacity,
        private BookingPricingService $pricing,
        private BookingValidationService $validation
    ) {}

    /** Listado con filtros */
    public function index(Request $request)
    {
        $query = Booking::with([
            'user', 'tour',
            'detail.schedule', 'detail.hotel', 'detail.tourLanguage', 'detail.meetingPoint',
            'redemption.promoCode',
            'promoCodeLegacy',
        ]);

        if ($request->filled('reference'))         $query->where('booking_reference', 'ilike', '%' . $request->reference . '%');
        if ($request->filled('status'))            $query->where('status', $request->status);
        if ($request->filled('booking_date_from')) $query->whereDate('booking_date', '>=', $request->booking_date_from);
        if ($request->filled('booking_date_to'))   $query->whereDate('booking_date', '<=', $request->booking_date_to);

        if ($request->filled('tour_date_from') || $request->filled('tour_date_to')) {
            $query->whereHas('detail', function ($q) use ($request) {
                if ($request->filled('tour_date_from')) $q->whereDate('tour_date', '>=', $request->tour_date_from);
                if ($request->filled('tour_date_to'))   $q->whereDate('tour_date', '<=', $request->tour_date_to);
            });
        }

        if ($request->filled('tour_id'))           $query->where('tour_id', $request->tour_id);
        if ($request->filled('schedule_id'))       $query->whereHas('detail', fn($q) => $q->where('schedule_id', $request->schedule_id));
        if ($request->filled('tour_language_id'))  $query->where('tour_language_id', $request->tour_language_id);

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

    /** Form create (admin) */
    public function create()
    {
        $tours = Tour::with(['prices.category', 'schedules', 'languages'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $users         = User::where('is_active', true)->orderBy('full_name')->get();
        $hotels        = HotelList::where('is_active', true)->orderBy('name')->get();
        $meetingPoints = MeetingPoint::where('is_active', true)->orderBy('name')->get();

        // Límites globales — usados por el JS para min/max fechas y reglas de pax
        $bookingLimits = $this->buildBookingLimits();

        return view('admin.bookings.create', compact('tours', 'users', 'hotels', 'meetingPoints', 'bookingLimits'));
    }

    /** Form edit (admin) */
    public function edit(Booking $booking)
    {
        $booking->load([
            'detail.schedule', 'detail.tourLanguage', 'detail.hotel', 'detail.meetingPoint',
            'user', 'tour.prices.category', 'tour.schedules', 'tour.languages',
            'redemption.promoCode',
        ]);

        $tours         = Tour::with(['prices.category', 'schedules', 'languages'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $users         = User::where('is_active', true)->orderBy('full_name')->get();
        $hotels        = HotelList::where('is_active', true)->orderBy('name')->get();
        $meetingPoints = MeetingPoint::where('is_active', true)->orderBy('name')->get();

        // === Parseo de snapshot de categorías -> mapa id => qty
        $categoryQuantitiesById = [];
        $categoriesSnapshot     = null;
        $rawCategories          = $booking->detail?->categories;

        if (is_string($rawCategories)) {
            try { $categoriesSnapshot = json_decode($rawCategories, true); } catch (\Throwable $e) { $categoriesSnapshot = null; }
        } elseif (is_array($rawCategories)) {
            $categoriesSnapshot = $rawCategories;
        }

        if (is_array($categoriesSnapshot)) {
            if (isset($categoriesSnapshot[0]) && is_array($categoriesSnapshot[0])) {
                foreach ($categoriesSnapshot as $item) {
                    $cid = (string)($item['category_id'] ?? $item['id'] ?? '');
                    if ($cid !== '') $categoryQuantitiesById[$cid] = (int)($item['quantity'] ?? 0);
                }
            } else {
                foreach ($categoriesSnapshot as $cid => $info) {
                    $categoryQuantitiesById[(string)$cid] = (int)($info['quantity'] ?? 0);
                }
            }
        }

        // Totales iniciales
        $initSubtotal = is_array($categoriesSnapshot) && method_exists($this->pricing, 'calculateSubtotal')
            ? (float) $this->pricing->calculateSubtotal($categoriesSnapshot)
            : (float) ($booking->detail->total ?? 0);

        $redemption   = $booking->redemption;
        $opSnapshot   = $redemption?->operation_snapshot ?: ($redemption?->promoCode?->operation ?? null);
        $applied      = (float) ($redemption->applied_amount ?? 0);
        $initDiscount = 0.0;
        $initTotal    = $initSubtotal;

        if ($opSnapshot === 'subtract' && $applied > 0) {
            $initDiscount = $applied;
            $initTotal    = max(0, $initSubtotal - $applied);
        } elseif ($opSnapshot === 'add' && $applied > 0) {
            $initTotal    = $initSubtotal + $applied;
        } else {
            $initTotal    = (float) ($booking->total ?? $initSubtotal);
        }

        $initPersons   = array_sum(array_map('intval', $categoryQuantitiesById));
        $bookingLimits = $this->buildBookingLimits();

        // Limites por tour (slugs adult/kid y min/max por categoría) — el front lo usa si lo pasas
        $limitsPerTour = app(BookingValidationService::class)->getLimitsForTour($booking->tour);

        return view('admin.bookings.edit', compact(
            'booking',
            'tours',
            'users',
            'hotels',
            'meetingPoints',
            'categoryQuantitiesById',
            'initSubtotal',
            'initDiscount',
            'initTotal',
            'initPersons',
            'bookingLimits',
            'limitsPerTour'
        ));
    }

    /** Crear una reserva (admin) */
    public function store(Request $request)
    {
        // Normalizar pickup: si hay meeting_point, anula hotel/otro
        $in = $request->all();
        $in['is_other_hotel'] = (bool)($in['is_other_hotel'] ?? false);
        if (!empty($in['meeting_point_id'])) {
            $in['is_other_hotel']  = false;
            $in['other_hotel_name'] = null;
            $in['hotel_id']         = null;
        } elseif (!empty($in['other_hotel_name'])) {
            $in['is_other_hotel'] = true;
            $in['hotel_id']       = null;
        }
        $request->replace($in);

        $validated = $request->validate([
            'user_id'           => 'required|exists:users,user_id',
            'tour_id'           => 'required|exists:tours,tour_id',
            'schedule_id'       => 'required|exists:schedules,schedule_id',
            'tour_language_id'  => 'required|exists:tour_languages,tour_language_id',
            'tour_date'         => 'required|date|after_or_equal:today',
            'booking_date'      => 'nullable|date',
            'categories'        => 'required|array|min:1',
            'categories.*'      => 'required|integer|min:0',
            'hotel_id'          => 'nullable|integer|exists:hotels_list,hotel_id|exclude_if:is_other_hotel,1',
            'is_other_hotel'    => 'nullable|boolean',
            'other_hotel_name'  => 'nullable|string|max:255|required_if:is_other_hotel,1',
            'status'            => 'required|in:pending,confirmed,cancelled',
            'meeting_point_id'  => 'nullable|integer|exists:meeting_points,id',
            'notes'             => 'nullable|string|max:1000',
            'promo_code'        => 'nullable|string|max:100',
        ]);

        // Tope global de pax por reserva (igual que en edit)
        $totalPax = array_sum($validated['categories'] ?? []);
        $maxTotal = (int) config('booking.max_persons_per_booking', 12);
        if ($totalPax > $maxTotal) {
            return back()->withInput()->withErrors([
                'categories' => __('m_bookings.bookings.validation.max_persons_total', ['max' => $maxTotal])
            ]);
        }

        try {
            $tour = Tour::with('prices.category')->findOrFail((int)$validated['tour_id']);

            $schedule = $tour->schedules()
                ->where('schedules.schedule_id', $validated['schedule_id'])
                ->where('schedules.is_active', true)
                ->wherePivot('is_active', true)
                ->first();

            if (!$schedule) {
                return back()->withInput()->withErrors(['schedule_id' => __('carts.messages.schedule_unavailable')]);
            }

            // Validación modular de cantidades/por categorías
            $validationResult = $this->validation->validateQuantities($tour, $validated['categories']);
            if (!$validationResult['valid']) {
                $errorMsg = implode(' ', $validationResult['errors']);
                return back()->withInput()->withErrors(['categories' => $errorMsg]);
            }

            // Capacidad viva
            $remaining = $this->capacity->remainingCapacity(
                $tour,
                $schedule,
                $validated['tour_date'],
                excludeBookingId: null,
                countHolds: true
            );
            if ($totalPax > $remaining) {
                $friendly = __('m_bookings.bookings.errors.insufficient_capacity', [
                    'tour'      => $tour->name,
                    'date'      => Carbon::parse($validated['tour_date'])->translatedFormat('M d, Y'),
                    'time'      => Carbon::parse($schedule->start_time)->format('g:i A'),
                    'requested' => $totalPax,
                    'available' => $remaining,
                    'max'       => $this->capacity->resolveMaxCapacity($tour, $schedule, $validated['tour_date']),
                ]);
                return back()->withInput()->withErrors(['capacity' => $friendly]);
            }

            // Payload -> service
            $payload = [
                'user_id'           => (int)$validated['user_id'],
                'tour_id'           => (int)$validated['tour_id'],
                'schedule_id'       => (int)$validated['schedule_id'],
                'tour_language_id'  => (int)$validated['tour_language_id'],
                'tour_date'         => $validated['tour_date'],
                'booking_date'      => $validated['booking_date'] ?? now(),
                'categories'        => $validated['categories'],
                'status'            => $validated['status'],
                'promo_code'        => $validated['promo_code'] ?? null,
                'meeting_point_id'  => $validated['meeting_point_id'] ?? null,
                'hotel_id'          => !empty($validated['is_other_hotel']) ? null : ($validated['hotel_id'] ?? null),
                'is_other_hotel'    => (bool)($validated['is_other_hotel'] ?? false),
                'other_hotel_name'  => $validated['other_hotel_name'] ?? null,
                'notes'             => $validated['notes'] ?? null,
            ];

            $this->creator->create($payload, validateCapacity: true, countHolds: true);

            return redirect()->route('admin.bookings.index')
                ->with('success', __('m_bookings.bookings.success.created'));

        } catch (\Throwable $e) {
            Log::error('Admin booking store error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', __('m_bookings.bookings.errors.create'));
        }
    }

    /** Crear desde carrito (admin) */
    public function storeFromCart(Request $request)
    {
        $user = Auth::user();

        $cart = $user->cart()
            ->where('is_active', true)
            ->with(['items' => function ($q) {
                $q->with(['tour.prices.category', 'schedule', 'language', 'hotel', 'meetingPoint']);
            }])
            ->first();

        if (!$cart || !$cart->items->count()) {
            return back()->with('error', __('carts.messages.cart_empty'));
        }
        if ($cart->isExpired()) {
            DB::transaction(function () use ($cart) {
                $cart->items()->delete();
                $cart->forceFill(['is_active' => false, 'expires_at' => now()])->save();
            });
            return back()->with('error', __('carts.messages.cart_expired'));
        }

        // Cupón desde admin session o input
        $promoCodeValue   = session('admin_cart_promo.code') ?: $request->input('promo_code');
        $promoCodeToApply = null;

        if ($promoCodeValue) {
            $clean = PromoCode::normalize($promoCodeValue);
            $promoCodeToApply = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$clean])
                ->lockForUpdate()
                ->first();

            if ($promoCodeToApply && method_exists($promoCodeToApply, 'isValidToday') && !$promoCodeToApply->isValidToday())   $promoCodeToApply = null;
            if ($promoCodeToApply && method_exists($promoCodeToApply, 'hasRemainingUses') && !$promoCodeToApply->hasRemainingUses()) $promoCodeToApply = null;
        }

        // Prevalidación por grupo (tour+fecha+horario)
        $groups = $cart->items->groupBy(fn($i) => $i->tour_id . '_' . $i->tour_date . '_' . $i->schedule_id);

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

            $totalPax = $items->sum(fn($item) => (int)$item->total_pax);

            $remaining = $this->capacity->remainingCapacity(
                $tour,
                $schedule,
                $tourDate,
                excludeBookingId: null,
                countHolds: true,
                excludeCartId: (int)$cart->cart_id
            );

            if ($totalPax > $remaining) {
                return back()->with('error', __('m_bookings.messages.limited_seats_available', [
                    'available' => $remaining,
                    'tour'      => $tour->name,
                    'date'      => Carbon::parse($tourDate)->format('d/M/Y'),
                ]));
            }
        }

        // Crear bookings (aplica promo una vez)
        $promoApplied = false;

        foreach ($cart->items as $item) {
            // Construir cantidades por categoría desde snapshot
            $quantities = [];
            foreach ((array)($item->categories ?? []) as $cat) {
                $cid = (int)($cat['category_id'] ?? 0);
                $qty = (int)($cat['quantity'] ?? 0);
                if ($cid > 0 && $qty > 0) $quantities[$cid] = $qty;
            }

            $payload = [
                'user_id'           => (int)$cart->user_id,
                'tour_id'           => (int)$item->tour_id,
                'schedule_id'       => (int)$item->schedule_id,
                'tour_language_id'  => (int)$item->tour_language_id,
                'tour_date'         => $item->tour_date,
                'booking_date'      => now(),
                'categories'        => $quantities,
                'status'            => 'pending',
                'promo_code'        => $promoApplied ? null : ($promoCodeToApply?->code),
                'meeting_point_id'  => $item->meeting_point_id ?: null,
                'hotel_id'          => $item->is_other_hotel ? null : ($item->hotel_id ?: null),
                'is_other_hotel'    => (bool)$item->is_other_hotel,
                'other_hotel_name'  => $item->is_other_hotel ? ($item->other_hotel_name ?? null) : null,
                'notes'             => null,
                'exclude_cart_id'   => (int)$cart->cart_id,
            ];

            $booking = $this->creator->create($payload, validateCapacity: true, countHolds: true);

            if (!$promoApplied && $promoCodeToApply) {
                $promoCodeToApply->redeemForBooking($booking->booking_id, $cart->user_id);
                $promoApplied = true;
            }
        }

        DB::transaction(function () use ($cart) {
            $cart->items()->delete();
            $cart->forceFill(['is_active' => false, 'expires_at' => now()])->save();
        });
        session()->forget('admin_cart_promo');

        return redirect()->route('admin.bookings.index')
            ->with('success', __('m_bookings.bookings.success.created'));
    }

    /** Update (admin) */
   public function update(Request $request, Booking $booking)
{
    // Normalizar pickup
    $in = $request->all();
    $in['is_other_hotel'] = (bool)($in['is_other_hotel'] ?? false);
    if (!empty($in['meeting_point_id'])) {
        $in['is_other_hotel']   = false;
        $in['other_hotel_name'] = null;
        $in['hotel_id']         = null;
    } elseif (!empty($in['other_hotel_name'])) {
        $in['is_other_hotel'] = true;
        $in['hotel_id']       = null;
    }
    $request->replace($in);

    $validated = $request->validate([
        'user_id'           => 'required|exists:users,user_id',
        'tour_id'           => 'required|exists:tours,tour_id',
        'schedule_id'       => 'required|exists:schedules,schedule_id',
        'tour_language_id'  => 'required|exists:tour_languages,tour_language_id',
        'tour_date'         => 'required|date',
        // ✔ Hacer opcional booking_date; si viene null usamos el valor actual
        'booking_date'      => 'nullable|date',
        'categories'        => 'required|array|min:1',
        'categories.*'      => 'required|integer|min:0',
        'hotel_id'          => 'nullable|integer|exists:hotels_list,hotel_id|exclude_if:is_other_hotel,1',
        'is_other_hotel'    => 'nullable|boolean',
        'other_hotel_name'  => 'nullable|string|max:255|required_if:is_other_hotel,1',
        'status'            => 'required|in:pending,confirmed,cancelled',
        'meeting_point_id'  => 'nullable|integer|exists:meeting_points,id',
        'notes'             => 'nullable|string|max:1000',
        'promo_code'        => 'nullable|string|max:100',
    ]);

    // Tope global de pax
    $totalPax = array_sum($validated['categories'] ?? []);
    $maxTotal = (int) config('booking.max_persons_per_booking', 12);
    if ($totalPax > $maxTotal) {
        return back()->withInput()
            ->with('showEditModal', $booking->booking_id)
            ->withErrors([
                'categories' => __('m_bookings.bookings.validation.max_persons_total', ['max' => $maxTotal])
            ]);
    }

    DB::beginTransaction();
    try {
        $tourDate = Carbon::parse($validated['tour_date']);
        if ($tourDate->lt(Carbon::today())) {
            DB::rollBack();
            return back()->withInput()
                ->with('showEditModal', $booking->booking_id)
                ->withErrors(['tour_date' => __('m_bookings.bookings.validation.past_date')]);
        }

        $newTour = Tour::with('prices.category')->findOrFail($validated['tour_id']);
        $newSchedule = $newTour->schedules()
            ->where('schedules.schedule_id', $validated['schedule_id'])
            ->where('schedules.is_active', true)
            ->wherePivot('is_active', true)
            ->first();

        if (!$newSchedule) {
            DB::rollBack();
            return back()->withInput()
                ->with('showEditModal', $booking->booking_id)
                ->withErrors(['schedule_id' => __('carts.messages.schedule_unavailable')]);
        }

        // Validación modular
        $validationResult = $this->validation->validateQuantities($newTour, $validated['categories']);
        if (!$validationResult['valid']) {
            DB::rollBack();
            $errorMsg = implode(' ', $validationResult['errors']);
            return back()->withInput()
                ->with('showEditModal', $booking->booking_id)
                ->withErrors(['categories' => $errorMsg]);
        }

        // Capacidad excluyendo el booking actual
        $snap = $this->capacity->capacitySnapshot(
            $newTour,
            $newSchedule,
            $validated['tour_date'],
            excludeBookingId: (int)$booking->booking_id,
            countHolds: true
        );

        if ($totalPax > $snap['available']) {
            DB::rollBack();
            return back()->withInput()
                ->with('showEditModal', $booking->booking_id)
                ->withErrors([
                    'capacity' => __('m_bookings.bookings.errors.insufficient_capacity', [
                        'tour'      => $newTour->name,
                        'date'      => $tourDate->translatedFormat('M d, Y'),
                        'time'      => Carbon::parse($newSchedule->start_time)->format('g:i A'),
                        'requested' => $totalPax,
                        'available' => $snap['available'],
                        'max'       => $snap['max'],
                    ])
                ]);
        }

        // Snapshot + subtotal
        $categoriesSnapshot = $this->pricing->buildCategoriesSnapshot($newTour, $validated['categories']);
        $detailSubtotal     = $this->pricing->calculateSubtotal($categoriesSnapshot);

        // Promo
        $promo = null;
        $promoInput = trim((string)($validated['promo_code'] ?? ''));
        if ($promoInput !== '') {
            $clean = PromoCode::normalize($promoInput);
            $promo = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$clean])
                ->lockForUpdate()
                ->first();

            if ($promo && method_exists($promo, 'isValidToday') && !$promo->isValidToday())   $promo = null;
            if ($promo && method_exists($promo, 'hasRemainingUses') && !$promo->hasRemainingUses()) $promo = null;
        }

        $total = $this->pricing->applyPromo($detailSubtotal, $promo);

        // Cabecera (✔ mantener booking_date actual si no se envía)
        $booking->update([
            'user_id'          => (int)$validated['user_id'],
            'tour_id'          => (int)$validated['tour_id'],
            'tour_language_id' => (int)$validated['tour_language_id'],
            'booking_date'     => $validated['booking_date'] ?? $booking->booking_date,
            'status'           => $validated['status'],
            'total'            => $total,
            'notes'            => $validated['notes'] ?? null,
        ]);

        // Detalle
        $detail = $booking->detail()->lockForUpdate()->first();
        if (!$detail) {
            DB::rollBack();
            return back()->with('error', __('m_bookings.bookings.errors.detail_not_found'));
        }

        $detail->update([
            'tour_id'           => (int)$validated['tour_id'],
            'schedule_id'       => (int)$validated['schedule_id'],
            'tour_date'         => $validated['tour_date'],
            'tour_language_id'  => (int)$validated['tour_language_id'],
            'categories'        => $categoriesSnapshot,
            'total'             => $detailSubtotal,
            'hotel_id'          => !empty($validated['is_other_hotel']) ? null : ($validated['hotel_id'] ?? null),
            'is_other_hotel'    => (bool)($validated['is_other_hotel'] ?? false),
            'other_hotel_name'  => $validated['other_hotel_name'] ?? null,
            'meeting_point_id'  => $validated['meeting_point_id'] ?? null,
        ]);

        // Redención y conteos
        $currentRedemption = $booking->redemption()->lockForUpdate()->first();
        if ($promo) {
            $appliedAmount = 0.0;
            if ($promo->discount_percent)    $appliedAmount = round($detailSubtotal * ($promo->discount_percent / 100), 2);
            elseif ($promo->discount_amount) $appliedAmount = (float)$promo->discount_amount;

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
                    if ($prev = PromoCode::lockForUpdate()->find($wasPromoId)) {
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
                $code = PromoCode::lockForUpdate()->find($currentRedemption->promo_code_id);
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
        Log::info("Booking updated successfully: #{$booking->booking_id} by user ID: " . auth()->id());

        return redirect()->route('admin.bookings.index')
            ->with('success', __('m_bookings.bookings.success.updated'));

    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error("Error updating booking #{$booking->booking_id}: " . $e->getMessage());
        Log::error($e->getTraceAsString());
        return back()->withInput()
            ->with('showEditModal', $booking->booking_id)
            ->with('error', __('m_bookings.bookings.errors.update'));
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

                $snap = $this->capacity->capacitySnapshot(
                    $tour,
                    $schedule,
                    $detail->tour_date,
                    excludeBookingId: (int)$booking->booking_id,
                    countHolds: true
                );

                $requested = (int)$detail->total_pax;

                if ($requested > $snap['available']) {
                    return back()->with('error', __('m_bookings.bookings.errors.insufficient_capacity', [
                        'tour'      => optional($tour)->name ?? 'Unknown Tour',
                        'date'      => Carbon::parse($detail->tour_date)->format('M d, Y'),
                        'time'      => Carbon::parse($schedule->start_time)->format('g:i A'),
                        'requested' => $requested,
                        'available' => $snap['available'],
                        'max'       => $snap['max'],
                    ]));
                }
            }

            $booking->status = $new;
            $booking->save();

            Log::info("Booking #{$booking->booking_id} status changed from '{$old}' to '{$new}' by user ID: " . auth()->id());

            $messageKey = match ($new) {
                'confirmed' => 'status_confirmed',
                'cancelled' => 'status_cancelled',
                'pending'   => 'status_pending',
                default     => 'status_updated'
            };

            return back()->with('success', __("m_bookings.bookings.success.{$messageKey}"));

        } catch (\Throwable $e) {
            Log::error("Error updating booking status for booking #{$booking->booking_id}: " . $e->getMessage());
            return back()->with('error', __('m_bookings.bookings.errors.status_update_failed'));
        }
    }

    /** Delete */
    public function destroy(Booking $booking)
    {
        try {
            $id = $booking->booking_id;

            DB::beginTransaction();
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
            Log::error("Delete error #{$booking->booking_id}: " . $e->getMessage());
            return back()->with('error', __('m_bookings.bookings.errors.delete'));
        }
    }

    /** PDF del recibo */
    public function generateReceipt(Booking $booking)
    {
        $booking->load([
            'user', 'tour',
            'detail.schedule', 'detail.hotel', 'detail.tourLanguage', 'detail.meetingPoint',
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
            'user', 'tour',
            'detail.schedule', 'detail.hotel', 'detail.tourLanguage',
            'redemption.promoCode',
            'promoCodeLegacy',
        ]);

        if ($request->filled('reference'))         $query->where('booking_reference', 'ilike', '%' . $request->reference . '%');
        if ($request->filled('status'))            $query->where('status', $request->status);
        if ($request->filled('booking_date_from')) $query->whereDate('booking_date', '>=', $request->booking_date_from);
        if ($request->filled('booking_date_to'))   $query->whereDate('booking_date', '<=', $request->booking_date_to);

        $bookings = $query->orderBy('booking_date', 'desc')->get();

        $totalAdults  = $bookings->sum(fn($b) => (int) optional($b->detail)->adults_quantity);
        $totalKids    = $bookings->sum(fn($b) => (int) optional($b->detail)->kids_quantity);
        $totalPersons = $totalAdults + $totalKids;

        return Pdf::loadView('admin.bookings.pdf-summary', compact('bookings', 'totalAdults', 'totalKids', 'totalPersons'))
            ->download('bookings-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /** Export Excel */
    public function exportExcel(Request $request)
    {
        return Excel::download(new BookingsExport($request->all()), 'bookings-' . now()->format('Y-m-d') . '.xlsx');
    }

    /** AJAX: verificar cupón */
    public function verifyPromoCode(Request $request)
    {
        $codeRaw  = (string)$request->input('code', '');
        $code     = PromoCode::normalize($codeRaw);
        $subtotal = (float)$request->input('subtotal', 0);

        if (!$code || $subtotal <= 0) {
            return response()->json(['valid' => false, 'message' => 'Datos inválidos']);
        }

        $promo = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$code])->first();
        if (!$promo) return response()->json(['valid' => false, 'message' => 'Código no encontrado']);
        if (method_exists($promo, 'isValidToday') && !$promo->isValidToday())           return response()->json(['valid' => false, 'message' => 'Este código ha expirado o aún no es válido']);
        if (method_exists($promo, 'hasRemainingUses') && !$promo->hasRemainingUses())   return response()->json(['valid' => false, 'message' => 'Este código ha alcanzado su límite de usos']);

        $discountAmount  = 0.0;
        $discountPercent = null;

        if (!is_null($promo->discount_percent)) {
            $discountPercent = (float)$promo->discount_percent;
            $discountAmount  = round($subtotal * ($discountPercent / 100), 2);
        } elseif (!is_null($promo->discount_amount)) {
            $discountAmount = (float)$promo->discount_amount;
        }

        $operation = $promo->operation === 'add' ? 'add' : 'subtract';

        return response()->json([
            'valid'            => true,
            'message'          => 'Código válido',
            'operation'        => $operation,
            'discount_amount'  => $discountAmount,
            'discount_percent' => $discountPercent,
        ]);
    }

    /** API: schedules por tour (AJAX) */
    public function getSchedules(Tour $tour)
    {
        return response()->json(
            $tour->schedules()
                ->where('schedules.is_active', true)
                ->wherePivot('is_active', true)
                ->orderBy('start_time')
                ->get(['schedule_id', 'start_time', 'end_time'])
        );
    }

    /** API: languages por tour (AJAX) */
    public function getLanguages(Tour $tour)
    {
        return response()->json(
            $tour->languages()->get(['tour_language_id', 'name'])
        );
    }

    /** API: categorías/precios por tour (AJAX) */
    public function getCategories(Tour $tour)
    {
        $categories = $tour->prices()
            ->where('is_active', true)
            ->with('category')
            ->orderBy('category_id')
            ->get()
            ->map(function($price) {
                return [
                    'id'        => $price->category_id,
                    'name'      => $price->category->name,
                    'price'     => (float) $price->price,
                    'min'       => (int) $price->min_quantity,
                    'max'       => (int) $price->max_quantity,
                    'is_active' => (bool) $price->is_active
                ];
            });

        return response()->json($categories);
    }

    /** Límites globales de reserva y reglas de fechas */
    private function buildBookingLimits(): array
    {
        return [
            'max_persons_total'         => (int)  config('booking.max_persons_per_booking', 12),
            'min_adults'                => (int)  config('booking.min_adults_per_booking', 0),
            'max_kids'                  => (int)  config('booking.max_kids_per_booking', PHP_INT_MAX),
            'min_days_advance'          => (int)  config('booking.min_days_advance', 1),
            'max_days_advance'          => (int)  config('booking.max_days_advance', 365),
            'payment_timeout_minutes'   => (int)  config('booking.payment_timeout_minutes', 30),
            'allow_cancellation'        => (bool) config('booking.allow_cancellation', true),
            'cancellation_hours_before' => (int)  config('booking.cancellation_hours_before', 24),
            'allow_modification'        => (bool) config('booking.allow_modification', true),
            'modification_hours_before' => (int)  config('booking.modification_hours_before', 48),
        ];
    }
}
