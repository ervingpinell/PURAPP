<?php

namespace App\Http\Controllers\Admin\Bookings;

use App\Http\Controllers\Controller;
use App\Models\{
    Booking,
    BookingDetail,
    Schedule,
    Tour,
    TourLanguage,
    User,
    HotelList,
    PromoCode,
    MeetingPoint,
    Cart,
    Payment
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Auth, Mail};
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
use App\Services\LoggerHelper;

/**
 * BookingController
 *
 * Handles booking management and display.
 */
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
            'user',
            'tour',
            'payments',
            'detail.schedule',
            'detail.hotel',
            'detail.tourLanguage',
            'detail.meetingPoint',
            'redemption.promoCode',
            'promoCodeLegacy',
        ]);

        // Handle view parameter: 'active' (default) or 'trash'
        $view = $request->get('view', 'active');

        if ($view === 'trash') {
            // Show only trashed bookings
            $query->onlyTrashed();
        }
        // else: default behavior shows only active (non-trashed) bookings

        // Status tab filtering (only when not in trash)
        if ($view !== 'trash') {
            $statusTab = $request->get('status', 'general');

            if ($statusTab !== 'general') {
                // Map tab names to status values
                $statusMap = [
                    'active' => 'confirmed',
                    'cancelled' => 'cancelled',
                    'pending' => 'pending',
                ];

                if (isset($statusMap[$statusTab])) {
                    $query->where('status', $statusMap[$statusTab]);
                }
            }
            // 'general' shows all statuses (no filter applied)
        }

        if ($request->filled('reference'))         $query->where('booking_reference', 'ilike', '%' . $request->reference . '%');
        // NOTE: status filter from advanced filters is handled by tabs, so we don't apply it here
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

    public function show(Booking $booking)
    {
        // Cargamos todas las relaciones necesarias para mostrar detalles completos
        $booking->loadMissing([
            'user',
            'tour',
            'tourLanguage',
            'hotel',
            'payments',
            'detail.tour',
            'detail.hotel',
            'detail.schedule',
            'detail.tourLanguage',
            'detail.meetingPoint',
            'detail.meetingPoint.translations',
            'redemption.promoCode',
        ]);

        return view('admin.bookings.show', compact('booking'));
    }

    /** Form create (admin) - Simplified version */
    public function create()
    {
        // Load ALL necessary data upfront - simple and clean
        $tours = Tour::with([
            'schedules' => fn($q) => $q->orderBy('start_time'),
            'languages' => fn($q) => $q->orderBy('name'),
            'prices' => fn($q) => $q->where('is_active', true)
                ->with('category.translations')
                ->orderBy('category_id'),
            'taxes' => fn($q) => $q->where('is_active', true)
        ])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Case-insensitive check for 'customer' role using relationship
        $customers = User::whereHas('roles', fn($q) => $q->whereRaw('LOWER(name) = ?', ['customer']))
            ->orderBy('full_name')
            ->get();

        $hotels = HotelList::where('is_active', true)->orderBy('name')->get();
        $meetingPoints = MeetingPoint::where('is_active', true)->orderBy('name')->get();

        return view('admin.bookings.create-simple', compact('tours', 'customers', 'hotels', 'meetingPoints'));
    }

    /** Form edit (admin) */
    public function edit(Booking $booking)
    {
        $booking->load([
            'detail.schedule',
            'detail.tourLanguage',
            'detail.hotel',
            'detail.meetingPoint',
            'user',
            'tour',
            'redemption.promoCode',
        ]);

        // Load tours with all relationships like create-simple
        $tours = Tour::with([
            'schedules' => fn($q) => $q->orderBy('start_time'),
            'languages' => fn($q) => $q->orderBy('name'),
            'prices' => fn($q) => $q->where('is_active', true)
                ->with('category.translations')
                ->orderBy('category_id'),
            'taxes' => fn($q) => $q->where('is_active', true)
        ])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $users         = User::where('is_active', true)->orderBy('full_name')->get();
        $hotels        = HotelList::where('is_active', true)->orderBy('name')->get();
        $meetingPoints = MeetingPoint::where('is_active', true)->orderBy('name')->get();

        // Parse category quantities from booking details
        $categoryQuantitiesById = [];
        $rawCategories = $booking->detail?->categories;
        $categoriesSnapshot = null; // Initialize to prevent undefined variable

        if (is_string($rawCategories)) {
            try {
                $categoriesSnapshot = json_decode($rawCategories, true);
            } catch (\Throwable $e) {
                $categoriesSnapshot = null;
            }
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

        $bookingLimits = $this->buildBookingLimits();
        $limitsPerTour = app(BookingValidationService::class)->getLimitsForTour($booking->tour);

        return view('admin.bookings.edit', compact(
            'booking',
            'tours',
            'users',
            'hotels',
            'meetingPoints',
            'categoryQuantitiesById',
            'bookingLimits',
            'limitsPerTour'
        ));
    }

    public function store(Request $request)
    {
        \Log::info('BookingController@store called', [
            'all_input' => $request->all(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
        ]);
        // Normalizar pickup / hotel / meeting point
        $in = $request->all();
        $in['is_other_hotel'] = (bool)($in['is_other_hotel'] ?? false);

        if (!empty($in['meeting_point_id'])) {
            // Si hay punto de encuentro, anulamos hotel/other_hotel
            $in['is_other_hotel']   = false;
            $in['other_hotel_name'] = null;
            $in['hotel_id']         = null;
        } elseif (!empty($in['other_hotel_name'])) {
            // Si hay "otro hotel", anulamos hotel_id normal
            $in['is_other_hotel'] = true;
            $in['hotel_id']       = null;
        }

        $request->replace($in);
        $request->merge($in);

        // Validación
        try {
            $validated = $request->validate([
                'user_id'           => 'required|exists:users,user_id',
                'tour_id'           => 'required|exists:tours,tour_id',
                'schedule_id'       => 'required|exists:schedules,schedule_id',
                'tour_language_id'  => 'required|exists:tour_languages,tour_language_id',
                'tour_date'         => 'required|date|after_or_equal:today',
                'booking_date'      => 'nullable|date',
                'categories'        => 'required|array|min:1',
                'categories.*'      => 'integer|min:0',
                'status'            => 'nullable|in:pending,confirmed,cancelled',
                'hotel_id'          => 'nullable|exists:hotels_list,hotel_id',
                'is_other_hotel'    => 'nullable|boolean',
                'other_hotel_name'  => 'nullable|required_if:is_other_hotel,true|string|max:255',
                'meeting_point_id'  => 'nullable|exists:meeting_points,id',
                'pickup_time'       => 'nullable|date_format:H:i',
                'notes'             => 'nullable|string|max:1000',
                'promo_code'        => 'nullable|string|max:100',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            throw $e; // Re-throw to let Laravel handle the redirect
        }

        \Log::info('Validation passed', ['validated' => $validated]);

        // Check if force capacity is requested
        $forceCapacity = $request->has('force_capacity') && $request->input('force_capacity') == '1';

        // Tope global de personas por reserva (REMOVIDO para admins) -> AHORA CON CONFIRMACIÓN
        $totalPax = array_sum($validated['categories'] ?? []);
        $maxTotal = (int) config('booking.max_persons_per_booking', 12);

        if ($totalPax > $maxTotal && !$forceCapacity) {
            \Log::warning('Capacity limit exceeded', [
                'total_pax' => $totalPax,
                'max_total' => $maxTotal,
                'force_capacity' => $forceCapacity
            ]);

            return back()->withInput()->with('capacity_error', [
                'available' => $maxTotal,
                'requested' => $totalPax,
                'message'   => __('m_bookings.bookings.validation.max_persons_confirm', [
                    'max' => $maxTotal,
                    'requested' => $totalPax
                ]) ?? "Global limit exceeded (Max: {$maxTotal}, Requested: {$totalPax}). Do you want to force this booking?"
            ]);
        }

        // =======================
        // CREACIÓN DE LA RESERVA
        // =======================
        try {
            // Cargar tour + precios
            $tour = Tour::with('prices.category')->findOrFail((int)$validated['tour_id']);

            // Verificar horario activo
            $schedule = $tour->schedules()
                ->where('schedules.schedule_id', $validated['schedule_id'])
                ->where('schedules.is_active', true)
                ->wherePivot('is_active', true)
                ->first();

            if (!$schedule) {
                return back()->withInput()->withErrors([
                    'schedule_id' => __('carts.messages.schedule_unavailable')
                ]);
            }

            // Check if force capacity is requested (moved up)
            // $forceCapacity already defined above

            // Validar cantidades por categoría (min/max, etc.)
            // Pasamos $forceCapacity para saltar límites SOLO si el usuario ya confirmó
            $validationResult = $this->validation->validateQuantities($tour, $validated['categories'], $forceCapacity);

            if (!$validationResult['valid']) {
                $errorMsg = implode(' ', $validationResult['errors']);

                // Si no estamos forzando, pedimos confirmación
                if (!$forceCapacity) {
                    return back()->withInput()->with('capacity_error', [
                        'available' => 'N/A',
                        'requested' => 'N/A',
                        'message'   => __('m_bookings.bookings.validation.limits_exceeded_confirm', ['errors' => $errorMsg])
                            ?? "Limits exceeded: $errorMsg. Do you want to force this booking?"
                    ]);
                }

                // Si ya forzamos y sigue fallando (ej. 0 pax), error real
                return back()->withInput()->withErrors(['categories' => $errorMsg]);
            }

            // Validar capacidad restante
            // Si forceCapacity es true, saltamos esta validación manual porque el BookingCreator
            // se encargará de ignorarla o validarla según el flag validateCapacity.
            // Sin embargo, para dar feedback inmediato antes de llamar al servicio:
            if (!$forceCapacity) {
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
                        'date'      => \Carbon\Carbon::parse($validated['tour_date'])->translatedFormat('M d, Y'),
                        'time'      => \Carbon\Carbon::parse($schedule->start_time)->format('g:i A'),
                        'requested' => $totalPax,
                        'available' => $remaining,
                        'max'       => $this->capacity->resolveMaxCapacity($tour, $schedule, $validated['tour_date']),
                    ]);

                    // Return back with special session flag to show confirmation modal
                    return back()->withInput()->with('capacity_error', [
                        'available' => $remaining,
                        'requested' => $totalPax,
                        'message'   => __('m_bookings.bookings.validation.capacity_exceeded_confirm', [
                            'available' => $remaining,
                            'requested' => $totalPax
                        ]) ?? "Capacity exceeded (Available: {$remaining}, Requested: {$totalPax}). Do you want to force this booking?"
                    ]);
                }
            }

            // Payload para BookingCreator
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
                'pickup_time'       => $validated['pickup_time'] ?? null,
                'hotel_id'          => !empty($validated['is_other_hotel']) ? null : ($validated['hotel_id'] ?? null),
                'is_other_hotel'    => (bool)($validated['is_other_hotel'] ?? false),
                'other_hotel_name'  => $validated['other_hotel_name'] ?? null,
                'notes'             => $validated['notes'] ?? null,
            ];

            \Log::info('Calling BookingCreator with payload', ['payload' => $payload]);

            try {
                // Crear booking usando tu servicio
                // Si forceCapacity es true, pasamos validateCapacity: false
                $booking = $this->creator->create(
                    $payload,
                    validateCapacity: !$forceCapacity,
                    countHolds: true
                );
            } catch (\RuntimeException $e) {
                // Check if it's a capacity error
                $msg = $e->getMessage();
                $json = json_decode($msg, true);

                if (json_last_error() === JSON_ERROR_NONE && isset($json['type']) && $json['type'] === 'capacity') {
                    // Capacity error detected
                    \Log::warning('Capacity error caught', ['json' => $json]);

                    // Return back with special session flag to show confirmation modal
                    return back()->withInput()->with('capacity_error', [
                        'available' => $json['available'],
                        'requested' => $json['requested'],
                        'message'   => __('m_bookings.bookings.validation.capacity_exceeded_confirm', [
                            'available' => $json['available'],
                            'requested' => $json['requested']
                        ]) ?? "Capacity exceeded (Available: {$json['available']}, Requested: {$json['requested']}). Do you want to force this booking?"
                    ]);
                }

                throw $e; // Re-throw other runtime exceptions
            }

            LoggerHelper::mutated('BookingController', 'store', 'Booking', $booking->booking_id);
        } catch (\Throwable $e) {
            LoggerHelper::exception('BookingController', 'store', 'Booking', null, $e);

            return back()->withInput()->with('error', __('m_bookings.bookings.errors.create'));
        }

        // =======================
        // ENVÍO DE CORREO (NO BLOQUEANTE)
        // =======================
        // Send customer email
        try {
            $this->dispatchMail(
                new \App\Mail\BookingCreatedMail($booking),
                optional($booking->user)->email,
                $booking
            );
        } catch (\Throwable $e) {
            LoggerHelper::exception('BookingController', 'store_email', 'Booking', $booking->booking_id, $e);
        }

        // Send admin notification email (separate, without password setup)
        $adminEmails = $this->notifyEmails();
        if (!empty($adminEmails)) {
            try {
                $this->dispatchMail(
                    new \App\Mail\BookingCreatedAdminMail($booking),
                    $adminEmails,
                    $booking
                );
            } catch (\Throwable $e) {
                LoggerHelper::exception('BookingController', 'store_admin_email', 'Booking', $booking->booking_id, $e);
            }
        }

        return redirect()->route('admin.bookings.show', $booking->booking_id)
            ->with('success', __('m_bookings.bookings.success.created'));
    }

    /** Crear desde carrito (admin) */
    public function storeFromCart(Request $request)
    {
        $user = Auth::user();

        // 👉 CORREGIDO: Leer notas desde input O sesión
        $notes = trim((string) ($request->input('notes') ?? session('admin_cart_notes', '')));

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
                    'date'      => \Carbon\Carbon::parse($tourDate)->format('d/M/Y'),
                ]));
            }
        }

        // Cupón
        $promoCodeValue   = session('admin_cart_promo.code') ?: $request->input('promo_code');
        $promoCodeToApply = null;

        if ($promoCodeValue) {
            $clean = \App\Models\PromoCode::normalize($promoCodeValue);
            $promoCodeToApply = \App\Models\PromoCode::whereRaw("TRIM(REPLACE(code, ' ', '')) = ?", [$clean])
                ->lockForUpdate()
                ->first();

            if ($promoCodeToApply && method_exists($promoCodeToApply, 'isValidToday') && !$promoCodeToApply->isValidToday())   $promoCodeToApply = null;
            if ($promoCodeToApply && method_exists($promoCodeToApply, 'hasRemainingUses') && !$promoCodeToApply->hasRemainingUses()) $promoCodeToApply = null;
        }

        // Crear bookings
        $created = [];
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
                // 👉 CORREGIDO: Usar notas capturadas
                'notes'             => $notes !== '' ? $notes : null,
                'exclude_cart_id'   => (int)$cart->cart_id,
            ];

            $booking = $this->creator->create($payload, validateCapacity: true, countHolds: true);

            if (!$promoApplied && $promoCodeToApply) {
                $promoCodeToApply->redeemForBooking($booking->booking_id, $cart->user_id);
                $promoApplied = true;
            }

            $created[] = $booking;
        }

        DB::transaction(function () use ($cart) {
            $cart->items()->delete();
            $cart->forceFill(['is_active' => false, 'expires_at' => now()])->save();
        });
        session()->forget('admin_cart_promo');
        // 👉 NUEVO: Limpiar notas de sesión después de crear bookings
        session()->forget('admin_cart_notes');

        // ===== Correos por booking (cliente + admins) =====
        $notify = $this->notifyEmails();
        $shouldSendDirect = app()->isLocal() && config('queue.default', env('QUEUE_CONNECTION')) === 'sync';

        foreach ($created as $booking) {
            try {
                $userMail = optional($booking->user)->email;

                // Send customer email
                if ($userMail) {
                    $customerMail = (new \App\Mail\BookingCreatedMail($booking))
                        ->onQueue('mail')
                        ->afterCommit();

                    $shouldSendDirect
                        ? \Mail::to($userMail)->send($customerMail)
                        : \Mail::to($userMail)->queue($customerMail);
                }

                // Send admin email (separate, without password setup)
                if (!empty($notify)) {
                    $adminMail = (new \App\Mail\BookingCreatedAdminMail($booking))
                        ->onQueue('mail')
                        ->afterCommit();

                    $shouldSendDirect
                        ? \Mail::to($notify)->send($adminMail)
                        : \Mail::to($notify)->queue($adminMail);
                }
            } catch (\Throwable $e) {
                LoggerHelper::exception('BookingController', 'storeFromCart', 'Booking', $booking->booking_id, $e);
            }
        }

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
            'booking_date'      => 'nullable|date',
            'categories'        => 'required|array|min:1',
            'categories.*'      => 'required|integer|min:0',
            'hotel_id'          => 'nullable|integer|exists:hotels_list,hotel_id|exclude_if:is_other_hotel,1',
            'is_other_hotel'    => 'nullable|boolean',
            'other_hotel_name'  => 'nullable|string|max:255|required_if:is_other_hotel,1',
            'status'            => 'required|in:pending,confirmed,cancelled',
            'meeting_point_id'  => 'nullable|integer|exists:meeting_points,id',
            'pickup_time'       => 'nullable|date_format:H:i',
            'notes'             => 'nullable|string|max:1000',
            'promo_code'        => 'nullable|string|max:100',
        ]);

        // Tope global pax
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

            $validationResult = $this->validation->validateQuantities($newTour, $validated['categories']);
            if (!$validationResult['valid']) {
                DB::rollBack();
                $errorMsg = implode(' ', $validationResult['errors']);
                return back()->withInput()
                    ->with('showEditModal', $booking->booking_id)
                    ->withErrors(['categories' => $errorMsg]);
            }

            // Capacidad (excluye booking actual)
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

            // Snapshot + subtotal (with date-based pricing)
            $categoriesSnapshot = $this->pricing->buildCategoriesSnapshot($newTour, $validated['categories'], $validated['tour_date']);

            // Calculate totals (subtotal, taxes)
            $totals = $this->pricing->calculateTotals($categoriesSnapshot);
            $detailSubtotal = $totals['subtotal'];
            $taxesTotal     = $totals['tax_amount'];
            $taxesBreakdown = $totals['taxes_breakdown'];

            // Promo
            $promo = null;
            $promoInput = trim((string)($validated['promo_code'] ?? ''));
            if ($promoInput !== '') {
                $clean = PromoCode::normalize($promoInput);
                $promo = PromoCode::whereRaw("TRIM(REPLACE(code,' ','')) = ?", [$clean])
                    ->lockForUpdate()
                    ->first();

                if ($promo && method_exists($promo, 'isValidToday') && !$promo->isValidToday())   $promo = null;
                if ($promo && method_exists($promo, 'hasRemainingUses') && !$promo->hasRemainingUses()) $promo = null;
            }

            // Total final: PRIMERO sumar impuestos, LUEGO aplicar promo
            $totalWithTaxes = $detailSubtotal + $taxesTotal;
            $total = $this->pricing->applyPromo($totalWithTaxes, $promo);

            // Cabecera
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
                'taxes_breakdown'   => $taxesBreakdown,
                'taxes_total'       => $taxesTotal,
                'hotel_id'          => !empty($validated['is_other_hotel']) ? null : ($validated['hotel_id'] ?? null),
                'is_other_hotel'    => (bool)($validated['is_other_hotel'] ?? false),
                'other_hotel_name'  => $validated['other_hotel_name'] ?? null,
                'meeting_point_id'  => $validated['meeting_point_id'] ?? null,
                'pickup_time'       => $validated['pickup_time'] ?? null,
            ]);

            // Redención y conteos (igual que tenías)
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
            LoggerHelper::mutated('BookingController', 'update', 'Booking', $booking->booking_id);

            // Email (cliente + admins)
            $this->dispatchMail(new \App\Mail\BookingUpdatedMail($booking), optional($booking->user)->email);

            return redirect()->route('admin.bookings.index')
                ->with('success', __('m_bookings.bookings.success.updated'));
        } catch (\Throwable $e) {
            DB::rollBack();
            LoggerHelper::exception('BookingController', 'update', 'Booking', $booking->booking_id, $e);
            return back()->withInput()
                ->with('showEditModal', $booking->booking_id)
                ->with('error', __('m_bookings.bookings.errors.update'));
        }
    }


    /** Cambiar status con validación de capacidad */
    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
            'pickup_time' => 'nullable|date_format:H:i',
            'pickup_type' => 'nullable|in:hotel,meeting_point',
            'hotel_id' => 'nullable|exists:hotels_list,hotel_id',
            'other_hotel_name' => 'nullable|string|max:255',
            'meeting_point_id' => 'nullable|exists:meeting_points,meeting_point_id',
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
                        'date'      => \Carbon\Carbon::parse($detail->tour_date)->format('M d, Y'),
                        'time'      => \Carbon\Carbon::parse($schedule->start_time)->format('g:i A'),
                        'requested' => $requested,
                        'available' => $snap['available'],
                        'max'       => $snap['max'],
                    ]));
                }

                // Update pickup location
                if ($request->filled('pickup_type')) {
                    if ($request->pickup_type === 'hotel') {
                        $detail->meeting_point_id = null;
                        $detail->meeting_point_name = null;

                        if ($request->filled('hotel_id')) {
                            $detail->hotel_id = $request->hotel_id;
                            $detail->is_other_hotel = false;
                            $detail->other_hotel_name = null;
                        } elseif ($request->filled('other_hotel_name')) {
                            $detail->hotel_id = null;
                            $detail->is_other_hotel = true;
                            $detail->other_hotel_name = $request->other_hotel_name;
                        }
                    } elseif ($request->pickup_type === 'meeting_point') {
                        $detail->hotel_id = null;
                        $detail->is_other_hotel = false;
                        $detail->other_hotel_name = null;

                        if ($request->filled('meeting_point_id')) {
                            $detail->meeting_point_id = $request->meeting_point_id;
                        }
                    }
                }

                // Update pickup time if provided
                if ($request->filled('pickup_time')) {
                    $detail->pickup_time = $request->pickup_time;
                }

                $detail->save();
            }

            $booking->status = $new;
            $booking->save();

            LoggerHelper::mutated('BookingController', 'updateStatus', 'Booking', $booking->booking_id, ['old_status' => $old, 'new_status' => $new]);

            // Email por estatus
            $mailable = match ($new) {
                'confirmed' => new \App\Mail\BookingConfirmedMail($booking),
                'cancelled' => new \App\Mail\BookingCancelledMail($booking),
                default     => new \App\Mail\BookingUpdatedMail($booking),
            };
            $this->dispatchMail($mailable, optional($booking->user)->email);

            $messageKey = match ($new) {
                'confirmed' => 'status_confirmed',
                'cancelled' => 'status_cancelled',
                'pending'   => 'status_pending',
                default     => 'status_updated'
            };

            return back()->with('success', __("m_bookings.bookings.success.{$messageKey}"));
        } catch (\Throwable $e) {
            LoggerHelper::exception('BookingController', 'updateStatus', 'Booking', $booking->booking_id, $e);
            return back()->with('error', __('m_bookings.bookings.errors.status_update_failed'));
        }
    }

    /** Soft Delete (default delete action) */
    public function destroy(Booking $booking)
    {
        try {
            $id = $booking->booking_id;
            $ref = $booking->booking_reference;

            // Soft delete the booking
            $booking->deleted_by = auth()->id();
            $booking->save();
            $booking->delete(); // This triggers soft delete
            LoggerHelper::mutated('BookingController', 'destroy', 'Booking', $id);

            return redirect()->route('admin.bookings.index')
                ->with('success', __('m_bookings.bookings.success.deleted'));
        } catch (\Throwable $e) {
            LoggerHelper::exception('BookingController', 'destroy', 'Booking', $booking->booking_id, $e);
            return back()->with('error', __('m_bookings.bookings.errors.delete'));
        }
    }

    /** Restore soft-deleted booking */
    public function restore($id)
    {
        try {
            $booking = Booking::withTrashed()->findOrFail($id);

            if (!$booking->trashed()) {
                return back()->with('info', __('Booking is not deleted.'));
            }

            $booking->deleted_by = null;
            $booking->save();
            $booking->restore();

            LoggerHelper::mutated('BookingController', 'restore', 'Booking', $booking->booking_id);

            return redirect()->route('admin.bookings.index', ['view' => 'active'])
                ->with('success', __('m_bookings.bookings.trash.booking_restored'));
        } catch (\Throwable $e) {
            LoggerHelper::exception('BookingController', 'restore', 'Booking', $id, $e);
            return back()->with('error', __('Failed to restore booking.'));
        }
    }

    /** Force Delete (permanent deletion) - Future: Admin only with permission */
    public function forceDelete($id)
    {
        try {
            $booking = Booking::withTrashed()->findOrFail($id);
            $bookingId = $booking->booking_id;
            $ref = $booking->booking_reference;

            DB::beginTransaction();

            // IMPORTANT: Save booking snapshot in payment metadata BEFORE deleting
            // This preserves audit trail information
            $payments = Payment::where('booking_id', $bookingId)->get();
            foreach ($payments as $payment) {
                $currentMetadata = $payment->metadata ?? [];

                // Add deleted booking snapshot
                $currentMetadata['deleted_booking_snapshot'] = [
                    'booking_id' => $booking->booking_id,
                    'booking_reference' => $booking->booking_reference,
                    'status' => $booking->status,
                    'total' => $booking->total,
                    'booking_date' => $booking->booking_date?->format('Y-m-d H:i:s'),
                    'user' => [
                        'user_id' => $booking->user?->user_id,
                        'name' => $booking->user?->name,
                        'email' => $booking->user?->email,
                    ],
                    'tour' => [
                        'tour_id' => $booking->tour?->tour_id,
                        'name' => $booking->tour?->name,
                    ],
                    'detail' => $booking->detail ? [
                        'tour_date' => $booking->detail->tour_date?->format('Y-m-d'),
                        'total_pax' => $booking->detail->total_pax,
                        'language' => $booking->detail->tourLanguage?->name,
                    ] : null,
                    'deleted_at' => now()->format('Y-m-d H:i:s'),
                    'deleted_by' => auth()->id(),
                ];

                $payment->metadata = $currentMetadata;
                $payment->save();
            }

            // Revoke promo code if exists
            if ($booking->redemption) {
                $booking->redemption->promoCode?->revokeRedemptionForBooking($booking->booking_id);
            }

            // Delete detail
            $booking->detail()->forceDelete();

            // NOTE: Payments are NOT deleted (foreign key is SET NULL)
            // This preserves payment records for audit trail and accounting
            // Payments will have booking_id = NULL but metadata contains snapshot

            // Permanently delete booking
            $booking->forceDelete();

            DB::commit();

            LoggerHelper::mutated('BookingController', 'forceDelete', 'Booking', $bookingId, ['context' => 'PERMANENTLY deleted, payments preserved']);

            // Redirect back to trash tab
            return redirect()->route('admin.bookings.index', ['view' => 'trash'])
                ->with('success', __('m_bookings.bookings.trash.booking_force_deleted'));
        } catch (\Throwable $e) {
            DB::rollBack();
            LoggerHelper::exception('BookingController', 'forceDelete', 'Booking', $id, $e);
            return back()->with('error', __('m_bookings.bookings.trash.force_delete_failed'));
        }
    }

    /** PDF del recibo */
    public function generateReceipt(Booking $booking)
    {
        $booking->load([
            'user',
            'tour',
            'detail.schedule',
            'detail.hotel',
            'detail.tourLanguage',
            'detail.meetingPoint',
            'redemption.promoCode',
            'promoCodeLegacy',
            'tour.prices.category',
        ]);

        $categoryNamesById = $booking->tour?->prices
            ? $booking->tour->prices->mapWithKeys(function ($p) {
                $locale = app()->getLocale();
                $name = method_exists($p->category, 'getTranslatedName')
                    ? ($p->category->getTranslatedName($locale) ?: $p->category->name)
                    : ($p->category->name ?? null);
                return [$p->category_id => $name];
            })->toArray()
            : [];

        $pdf = Pdf::loadView('admin.bookings.receipt', compact('booking', 'categoryNamesById'));

        return $pdf->stream("receipt-{$booking->booking_reference}.pdf");
    }

    /** Export PDF resumen */
    public function exportPdf(Request $request)
    {
        $query = Booking::with([
            'user',
            'tour',
            'detail.schedule',
            'detail.hotel',
            'detail.tourLanguage',
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
        $filters = $request->all();
        $export  = new BookingsExport($filters);
        $file    = BookingsExport::generateFileName($filters);

        return Excel::download($export, $file);
    }

    /** AJAX: verificar cupón */
    public function verifyPromoCode(Request $request)
    {
        $codeRaw  = (string)$request->input('code', '');
        $code     = PromoCode::normalize($codeRaw);
        $subtotal = (float)$request->input('subtotal', 0);

        if (!$code || $subtotal <= 0) {
            return response()->json(['valid' => false, 'message' => __('m_bookings.promo.invalid_data')]);
        }

        $promo = PromoCode::whereRaw("TRIM(REPLACE(code,' ','')) = ?", [$code])->first();
        if (!$promo) {
            return response()->json(['valid' => false, 'message' => __('m_bookings.promo.not_found')]);
        }
        if (method_exists($promo, 'isValidToday') && !$promo->isValidToday()) {
            return response()->json(['valid' => false, 'message' => __('m_bookings.promo.expired_or_not_yet')]);
        }
        if (method_exists($promo, 'hasRemainingUses') && !$promo->hasRemainingUses()) {
            return response()->json(['valid' => false, 'message' => __('m_bookings.promo.usage_limit_reached')]);
        }

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
            'message'          => __('m_bookings.promo.valid'),
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
    public function getCategories(Request $request, Tour $tour)
    {
        $locale = app()->getLocale();
        $tourDate = $request->input('tour_date');

        \Log::info('BookingController@getCategories called', [
            'tour_id' => $tour->tour_id,
            'tour_date' => $tourDate
        ]);

        $query = $tour->prices()
            ->where('tour_prices.is_active', true)
            ->with('category')
            ->orderBy('category_id');

        // Filter by date range if tour_date provided
        if ($tourDate) {
            $query->where(function ($q) use ($tourDate) {
                $q->where(function ($sub) use ($tourDate) {
                    // Prices with date range
                    $sub->whereNotNull('valid_from')
                        ->whereNotNull('valid_until')
                        ->whereDate('valid_from', '<=', $tourDate)
                        ->whereDate('valid_until', '>=', $tourDate);
                })->orWhere(function ($sub) {
                    // Default prices (no date range)
                    $sub->whereNull('valid_from')
                        ->whereNull('valid_until');
                });
            });

            // Prioritize seasonal prices
            $query->orderByRaw('CASE WHEN valid_from IS NOT NULL OR valid_until IS NOT NULL THEN 0 ELSE 1 END');
        }

        $allPrices = $query->get();

        // Deduplicate: if we have multiple prices for the same category (e.g. seasonal + default),
        // the query order (seasonal first) + this loop will keep the first one encountered.
        $uniquePrices = [];
        foreach ($allPrices as $p) {
            if (!isset($uniquePrices[$p->category_id])) {
                $uniquePrices[$p->category_id] = $p;
            }
        }

        $categories = collect($uniquePrices)
            ->map(function ($price) use ($locale) {
                $cat  = $price->category;
                $slug = $cat->slug ?? '';

                // Resolver nombre con fallbacks
                $name = method_exists($cat, 'getTranslatedName')
                    ? ($cat->getTranslatedName($locale) ?: ($cat->name ?? null))
                    : ($cat->name ?? null);

                if (!$name && $slug) {
                    foreach (
                        [
                            "customer_categories.labels.$slug",
                            "m_tours.customer_categories.labels.$slug",
                        ] as $key
                    ) {
                        $tr = __($key);
                        if ($tr !== $key) {
                            $name = $tr;
                            break;
                        }
                    }
                }

                if (!$name) {
                    $name = 'Category #' . (int)$price->category_id;
                }

                return [
                    'id'           => (int)$price->category_id,
                    'slug'         => (string)$slug,
                    'name'         => (string)$name,
                    'price'        => (float)$price->price,
                    'min'          => (int)$price->min_quantity,
                    'max'          => (int)$price->max_quantity,
                    'is_active'    => (bool)$price->is_active,
                    'season_label' => $price->season_label, // Include season label
                ];
            })
            ->values()
            ->all();

        return response()->json($categories);
    }


    /** Límites globales de reserva y reglas de fechas */
    private function buildBookingLimits(): array
    {
        return [
            'max_persons_total'         => (int)  config('booking.max_persons_per_booking', 12),
            'min_adults'                => (int)  config('booking.min_adults_per_booking', 0),
            'max_kids'                  => PHP_INT_MAX,
            'min_days_advance'          => (int)  config('booking.min_days_advance', 1),
            'max_days_advance'          => (int)  config('booking.max_days_advance', 365),
            'payment_timeout_minutes'   => (int)  config('booking.payment_timeout_minutes', 30),
            'allow_cancellation'        => (bool) config('booking.allow_cancellation', true),
            'cancellation_hours_before' => (int)  config('booking.cancellation_hours_before', 24),
            'allow_modification'        => (bool) config('booking.allow_modification', true),
            'modification_hours_before' => (int)  config('booking.modification_hours_before', 48),
        ];
    }

    /**
     * Destinatarios de notificación (admins), desde .env:
     * BOOKING_NOTIFY y/o MAIL_NOTIFICATIONS (coma-separado).
     */
    private function notifyEmails(): array
    {
        return collect([env('BOOKING_NOTIFY'), env('MAIL_NOTIFICATIONS')])
            ->filter()
            ->flatMap(fn($v) => array_map('trim', explode(',', $v)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Enviar SIEMPRE por Microsoft Graph (sin failover SMTP).
     * - To: cliente (si existe y es válido)
     * - BCC: admins de BOOKING_NOTIFY / MAIL_NOTIFICATIONS
     * - Lanza excepción para que la cola reintente si falla Graph.
     */
    private function dispatchMail(\Illuminate\Mail\Mailable $mailable, ?string $userMail, ?\App\Models\Booking $booking = null): void
    {
        try {
            // Lista de admins
            $notify = collect([env('BOOKING_NOTIFY'), env('MAIL_NOTIFICATIONS')])
                ->filter()
                ->flatMap(fn($v) => array_map('trim', explode(',', $v)))
                ->filter()
                ->unique()
                ->values()
                ->all();

            // Elegir MAIL_MAILER desde el .env (smtp)
            $mailer = \Mail::mailer(); // usa MAIL_MAILER del .env

            // Resolver destinatario del cliente
            $primaryTo = filter_var($userMail, FILTER_VALIDATE_EMAIL) ? $userMail : null;
            if (!$primaryTo && $booking) {
                $primaryTo = optional($booking->user)->email;
                if (!filter_var($primaryTo, FILTER_VALIDATE_EMAIL)) {
                    $primaryTo = null;
                }
            }

            // Si no hay email del cliente → fallback admins
            if (!$primaryTo && empty($notify)) {
                \Log::warning("dispatchMail: no 'to' address and no admins to notify");
                return;
            }

            if ($primaryTo) {
                $pending = $mailer->to($primaryTo);
                if (!empty($notify)) $pending->bcc($notify);

                // Enviar DIRECTO (NO queue)
                $pending->send($mailable);
            } else {
                // Fallback: solo admins
                $mailer->to($notify[0])
                    ->bcc(array_slice($notify, 1))
                    ->send($mailable);
            }
        } catch (\Throwable $e) {
            \Log::error("dispatchMail FAILED: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'to'    => $userMail,
            ]);
        }
    }
    /**
     * Generate payment link for a booking
     */
    public function generatePaymentLink(Booking $booking)
    {
        try {
            // Load payments relationship if not already loaded
            if (!$booking->relationLoaded('payments')) {
                $booking->load('payments');
            }

            // Check if booking is already paid
            $isPaid = false;
            if ($booking->payments->isNotEmpty()) {
                $latestPayment = $booking->payments->sortByDesc('created_at')->first();
                $isPaid = $latestPayment && $latestPayment->status === 'completed';
            }

            if ($isPaid) {
                return response()->json([
                    'success' => false,
                    'message' => __('m_bookings.bookings.errors.payment_already_completed') ?? 'This booking has already been paid.'
                ], 400);
            }

            // Use the new token-based URL
            $url = $booking->getPaymentUrl();

            return response()->json([
                'success' => true,
                'url' => $url
            ]);
        } catch (\Exception $e) {
            \Log::error('Error generating payment link: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating link'
            ], 500);
        }
    }
    /**
     * Regenerate payment link for a booking
     */
    public function regeneratePaymentLink(Booking $booking)
    {
        try {
            // Load payments relationship if not already loaded
            if (!$booking->relationLoaded('payments')) {
                $booking->load('payments');
            }

            // Check if booking is already paid
            $isPaid = false;
            if ($booking->payments->isNotEmpty()) {
                $latestPayment = $booking->payments->sortByDesc('created_at')->first();
                $isPaid = $latestPayment && $latestPayment->status === 'completed';
            }

            if ($isPaid) {
                return response()->json([
                    'success' => false,
                    'message' => __('m_bookings.bookings.errors.payment_already_completed') ?? 'This booking has already been paid.'
                ], 400);
            }

            // Regenerate payment token (invalidates old link)
            $newToken = $booking->regeneratePaymentToken();

            LoggerHelper::mutated('BookingController', 'regeneratePaymentLink', 'Booking', $booking->booking_id, [
                'token_preview' => substr($newToken, 0, 8) . '...'
            ]);

            $url = $booking->getPaymentUrl();

            return response()->json([
                'success' => true,
                'url' => $url,
                'message' => __('m_bookings.bookings.payment_link_regenerated') ?? 'Payment link regenerated successfully'
            ]);
        } catch (\Exception $e) {
            LoggerHelper::exception('BookingController', 'regeneratePaymentLink', 'Booking', $booking->booking_id, $e);
            return response()->json([
                'success' => false,
                'message' => 'Error regenerating link'
            ], 500);
        }
    }
    /**
     * Validate capacity for a potential booking (AJAX)
     */
    public function validateCapacity(Request $request)
    {
        try {
            // Basic validation of inputs needed for capacity check
            $validated = $request->validate([
                'tour_id' => 'required|integer|exists:tours,tour_id',
                'tour_date' => 'required|date',
                'schedule_id' => 'required|integer|exists:schedules,schedule_id',
                'categories' => 'required|array',
                'categories.*' => 'integer|min:0',
            ]);

            $totalPax = array_sum($validated['categories'] ?? []);
            $maxTotal = (int) config('booking.max_persons_per_booking', 12);

            // 1. Check Global Limit
            if ($totalPax > $maxTotal) {
                return response()->json([
                    'success' => false,
                    'type' => 'global_limit',
                    'available' => $maxTotal,
                    'requested' => $totalPax,
                    'message' => __('m_bookings.bookings.validation.max_persons_confirm', [
                        'max' => $maxTotal,
                        'requested' => $totalPax
                    ]) ?? "Global limit exceeded (Max: {$maxTotal}, Requested: {$totalPax}). Do you want to force this booking?"
                ]);
            }

            // 2. Check Tour Capacity
            $tour = Tour::findOrFail((int)$validated['tour_id']);
            $schedule = Schedule::findOrFail((int)$validated['schedule_id']);

            $remaining = $this->capacity->remainingCapacity(
                $tour,
                $schedule,
                $validated['tour_date'],
                excludeBookingId: null,
                countHolds: true
            );

            if ($totalPax > $remaining) {
                return response()->json([
                    'success' => false,
                    'type' => 'tour_capacity',
                    'available' => $remaining,
                    'requested' => $totalPax,
                    'message' => __('m_bookings.bookings.validation.capacity_exceeded_confirm', [
                        'available' => $remaining,
                        'requested' => $totalPax
                    ]) ?? "Capacity exceeded (Available: {$remaining}, Requested: {$totalPax}). Do you want to force this booking?"
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            LoggerHelper::exception('BookingController', 'validateCapacity', 'Booking', null, $e);
            return response()->json([
                'success' => false,
                'message' => 'Error validating capacity'
            ], 500);
        }
    }

    /**
     * Display unpaid bookings dashboard
     */
    public function unpaidIndex(Request $request)
    {
        $query = Booking::with(['user', 'tour', 'details'])
            ->where('is_paid', false)
            ->where('status', 'pending')
            ->whereNotNull('pending_expires_at');

        // Filter by expiration status
        if ($request->filled('expiry_filter')) {
            $now = now();
            switch ($request->expiry_filter) {
                case 'expired':
                    $query->where('pending_expires_at', '<', $now);
                    break;
                case 'expiring_soon': // Less than 2 hours
                    $query->where('pending_expires_at', '>', $now)
                        ->where('pending_expires_at', '<=', $now->copy()->addHours(2));
                    break;
                case 'active':
                    $query->where('pending_expires_at', '>', $now->copy()->addHours(2));
                    break;
            }
        }

        // Filter by pay-later
        if ($request->filled('is_pay_later')) {
            $query->where('is_pay_later', $request->is_pay_later === '1');
        }

        // Sort by expiration (soonest first)
        $bookings = $query->orderBy('pending_expires_at', 'asc')->paginate(20);

        return view('admin.bookings.unpaid', compact('bookings'));
    }

    /**
     * Extend booking expiration time
     */
    public function extendBooking(Request $request, Booking $booking)
    {
        $request->validate([
            'extend_hours' => ['required', 'integer', 'min:1', 'max:72']
        ]);

        if ($booking->is_paid) {
            return back()->with('error', __('Booking is already paid'));
        }

        $hours = (int) $request->extend_hours;
        $oldExpiry = $booking->pending_expires_at;
        $newExpiry = ($oldExpiry && $oldExpiry > now())
            ? $oldExpiry->copy()->addHours($hours)
            : now()->addHours($hours);

        $booking->pending_expires_at = $newExpiry;
        $booking->extension_count = ($booking->extension_count ?? 0) + 1;
        $booking->save();

        LoggerHelper::mutated('BookingController', 'extendBooking', 'Booking', $booking->booking_id, [
            'hours' => $hours,
            'old_expiry' => $oldExpiry,
            'new_expiry' => $newExpiry,
            'extension_count' => $booking->extension_count
        ]);

        // Send notification email to customer
        try {
            // TODO: Create ExtendedBookingMail
            // Mail::to($booking->user->email)->send(new ExtendedBookingMail($booking, $hours));
        } catch (\Exception $e) {
            LoggerHelper::exception('BookingController', 'extendBooking', 'Booking', $booking->booking_id, $e);
        }

        return back()->with('success', __('Booking extended by :hours hours', ['hours' => $hours]));
    }

    /**
     * Cancel unpaid booking
     */
    public function cancelUnpaid(Booking $booking)
    {
        if ($booking->is_paid) {
            return back()->with('error', __('Cannot cancel paid booking'));
        }

        if ($booking->status !== 'pending') {
            return back()->with('error', __('Only pending bookings can be cancelled'));
        }

        $booking->status = 'cancelled';
        $note = "\n\n[ADMIN-CANCELLED] Unpaid booking cancelled manually by admin on " . now()->format('Y-m-d H:i:s');
        $booking->notes = ($booking->notes ?? '') . $note;
        $booking->save();

        LoggerHelper::mutated('BookingController', 'cancelUnpaid', 'Booking', $booking->booking_id);

        // Send cancellation email
        try {
            Mail::to($booking->user->email)
                ->send(new \App\Mail\BookingCancelledExpiry($booking));
        } catch (\Exception $e) {
            LoggerHelper::exception('BookingController', 'cancelUnpaid', 'Booking', $booking->booking_id, $e);
        }

        return back()->with('success', __('Booking cancelled successfully'));
    }
}
