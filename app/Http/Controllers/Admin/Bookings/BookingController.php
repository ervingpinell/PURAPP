<?php

namespace App\Http\Controllers\Admin\Bookings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Cart;
use App\Models\HotelList;
use App\Models\Schedule;
use App\Models\Tour;
use App\Models\PromoCode;
use App\Models\MeetingPoint;

use App\Support\BookingRules;

use App\Exports\BookingsExport;
use App\Mail\BookingCreatedMail;
use App\Mail\BookingUpdatedMail;
use App\Mail\BookingConfirmedMail;
use App\Mail\BookingCancelledMail;
use Maatwebsite\Excel\Facades\Excel;

use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $sort      = $request->get('sort', 'booking_id');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['booking_id', 'booking_date', 'total', 'status', 'user'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'booking_id';
        }

        $query = Booking::with([
            'user',
            'detail.tour.tourType',
            'detail.hotel',
            'detail.schedule',
            'detail.meetingPoint',
            'redemption.promoCode',
            'tour' => fn ($q) => $q->withTrashed()->with('schedules'),
        ])
            ->join('users', 'bookings.user_id', '=', 'users.user_id')
            ->select('bookings.*');

        if (Auth::user()->role_id === 3) {
            $query->where('bookings.user_id', Auth::id());
        }

        if ($request->filled('reference')) {
            $query->where('bookings.booking_reference', 'ILIKE', "%{$request->reference}%");
        }

        if ($request->filled('status')) {
            $query->where('bookings.status', $request->status);
        }

        if ($request->filled('booking_date_from')) {
            $query->whereDate('bookings.booking_date', '>=', $request->booking_date_from);
        }
        if ($request->filled('booking_date_to')) {
            $query->whereDate('bookings.booking_date', '<=', $request->booking_date_to);
        }

        if (
            $request->filled('tour_date_from') || $request->filled('tour_date_to') ||
            $request->filled('tour_id') || $request->filled('schedule_id')
        ) {
            $query->whereHas('detail', function ($q) use ($request) {
                if ($request->filled('tour_date_from')) {
                    $q->whereDate('tour_date', '>=', $request->tour_date_from);
                }
                if ($request->filled('tour_date_to')) {
                    $q->whereDate('tour_date', '<=', $request->tour_date_to);
                }
                if ($request->filled('tour_id')) {
                    $q->where('tour_id', $request->tour_id);
                }
                if ($request->filled('schedule_id')) {
                    $q->where('schedule_id', $request->schedule_id);
                }
            });
        }

        $bookings = $query
            ->orderBy($sort === 'user' ? 'users.full_name' : 'bookings.' . $sort, $direction)
            ->paginate(10)
            ->appends($request->all());

        $hotels    = HotelList::where('is_active', true)->orderBy('name')->get();
        $schedules = Schedule::orderBy('start_time')->get();
        $tours     = Tour::orderBy('name')->get();

        return view('admin.bookings.index', compact(
            'bookings',
            'sort',
            'direction',
            'hotels',
            'schedules',
            'tours'
        ));
    }


    /** Crear reserva desde formulario manual */
    public function store(Request $request)
    {
        // Normalización previa (hotel_id / is_other_hotel)
        $input = $request->all();
        $input['is_other_hotel'] = filter_var($input['is_other_hotel'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (($input['hotel_id'] ?? null) === 'other' || $input['is_other_hotel'] === true) {
            $input['hotel_id'] = null;
        } else {
            if (isset($input['hotel_id']) && !ctype_digit((string) $input['hotel_id'])) {
                $input['hotel_id'] = null;
            }
        }
        $request->replace($input);

        // 0) Validación inicial
        try {
            $tz      = config('app.timezone', 'America/Costa_Rica');
            $minDate = BookingRules::earliestBookableDate();

            $v = $request->validate([
                'user_id'          => 'required|exists:users,user_id',
                'tour_id'          => 'required|exists:tours,tour_id',
                'booking_date'     => 'required|date',
                'tour_date'        => ['required', 'date', function ($attr, $value, $fail) use ($tz, $minDate) {
                    $dt = Carbon::parse($value, $tz)->startOfDay();
                    if ($dt->lt($minDate)) {
                        $fail(__('adminlte::adminlte.no_past_dates') . ' (min: ' . $minDate->toDateString() . ')');
                    }
                }],
                'status'           => 'required|in:pending,confirmed,cancelled',
                'tour_language_id' => 'required|exists:tour_languages,tour_language_id',
                'adults_quantity'  => 'required|integer|min:1',
                'kids_quantity'    => 'required|integer|min:0|max:2',
                'schedule_id'      => 'required|exists:schedules,schedule_id',
                'hotel_id'         => 'bail|nullable|integer|exists:hotels_list,hotel_id',
                'is_other_hotel'   => 'required|boolean',
                'other_hotel_name' => 'nullable|string|max:255|required_if:is_other_hotel,1',
                'promo_code'       => 'nullable|string',
                'meeting_point_id' => 'nullable|integer|exists:meeting_points,id',
            ], [
                'other_hotel_name.required_if' => __('adminlte::adminlte.other_hotel_required'),
            ]);
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->with('openModal', 'register')
                ->withInput();
        }

        // 1) Validación de promo (si viene)
        if ($request->filled('promo_code')) {
            $cleanCode   = strtoupper(trim(preg_replace('/\s+/', '', $request->input('promo_code'))));
            $promoExists = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code, ' ', ''))) = ?", [$cleanCode])
                ->where('is_used', false)
                ->exists();

            if (!$promoExists) {
                return back()
                    ->withErrors(['promo_code' => __('adminlte::adminlte.promo_invalid_or_used')])
                    ->with('openModal', 'register')
                    ->withInput();
            }
        }

        // 2) Anti doble submit
        $key = sprintf(
            'booking:%s:%s:%s:%s',
            $v['user_id'], $v['tour_id'],
            Carbon::parse($v['tour_date'])->toDateString(),
            $v['schedule_id']
        );
        if (RateLimiter::tooManyAttempts($key, 1)) {
            return back()
                ->withErrors(['too_many' => __('adminlte::adminlte.dupe_submit_cart')])
                ->with('openModal', 'register')
                ->withInput();
        }
        RateLimiter::hit($key, 10);

        // 3) Transacción
        try {
            $booking = DB::transaction(function () use ($request, $v) {
                $tour = Tour::with('schedules')->findOrFail($v['tour_id']);

                $schedule = $tour->schedules()
                    ->where('schedules.schedule_id', $v['schedule_id'])
                    ->where('schedules.is_active', true)
                    ->wherePivot('is_active', true)
                    ->first();

                if (!$schedule) {
                    throw ValidationException::withMessages([
                        'schedule_id' => __('adminlte::adminlte.schedule_not_available'),
                    ]);
                }

                // Fecha bloqueada
                $isBlocked = \App\Models\TourExcludedDate::where('tour_id', $tour->tour_id)
                    ->where(function ($query) use ($v) {
                        $query->whereNull('schedule_id')->orWhere('schedule_id', $v['schedule_id']);
                    })
                    ->where('start_date', '<=', $v['tour_date'])
                    ->where(function ($q) use ($v) {
                        $q->where('end_date', '>=', $v['tour_date'])->orWhereNull('end_date');
                    })
                    ->exists();
                if ($isBlocked) {
                    throw ValidationException::withMessages([
                        'tour_date' => __('adminlte::adminlte.date_blocked'),
                    ]);
                }

                // Capacidad
                $reserved = BookingDetail::where('tour_id', $tour->tour_id)
                    ->where('tour_date', $v['tour_date'])
                    ->where('schedule_id', $v['schedule_id'])
                    ->sum(DB::raw('adults_quantity + kids_quantity'));

                $requested = $v['adults_quantity'] + $v['kids_quantity'];
                if ($reserved + $requested > $schedule->max_capacity) {
                    $available = $schedule->max_capacity - $reserved;
                    throw ValidationException::withMessages([
                        'capacity' => __('adminlte::adminlte.capacity_left', ['available' => $available]),
                    ]);
                }

                // ======= TOTAL + PROMO =======
                $baseTotal = ($tour->adult_price * $v['adults_quantity']) + ($tour->kid_price * $v['kids_quantity']);
                $total     = $baseTotal;

                $promoCode = null;
                if ($request->filled('promo_code')) {
                    $cleanCode = strtoupper(trim(preg_replace('/\s+/', '', $request->input('promo_code'))));
                    $promoCode = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code, ' ', ''))) = ?", [$cleanCode])
                        ->where('is_used', false)
                        ->first();
                }

                if ($promoCode) {
                    $op = $promoCode->operation === 'add' ? 'add' : 'subtract';
                    $delta = 0.0;

                    if ($promoCode->discount_amount) {
                        $delta = (float)$promoCode->discount_amount;
                    } elseif ($promoCode->discount_percent) {
                        $delta = round($baseTotal * ((float)$promoCode->discount_percent / 100), 2);
                    }

                    $total = $op === 'add'
                        ? round($baseTotal + $delta, 2)
                        : max(0, round($baseTotal - $delta, 2));
                }

                // Meeting Point snapshot
                $mp = !empty($v['meeting_point_id']) ? MeetingPoint::find($v['meeting_point_id']) : null;

                // ✅ Cabecera SIN schedule_id + snapshot
                $booking = Booking::create([
                    'user_id'           => $v['user_id'],
                    'tour_id'           => $tour->tour_id,
                    'tour_language_id'  => $v['tour_language_id'],
                    'booking_reference' => strtoupper(Str::random(10)),
                    'booking_date'      => $v['booking_date'],
                    'status'            => $v['status'],
                    'total'             => $total,
                    'is_active'         => true,
                    'tour_name_snapshot'=> $tour->name, // 👈 snapshot
                ]);

                // ✅ Detalle CON schedule_id + snapshot
                BookingDetail::create([
                    'booking_id'       => $booking->booking_id,
                    'tour_id'          => $tour->tour_id,
                    'tour_language_id' => $v['tour_language_id'],
                    'tour_date'        => $v['tour_date'],
                    'schedule_id'      => $v['schedule_id'],
                    'adults_quantity'  => $v['adults_quantity'],
                    'kids_quantity'    => $v['kids_quantity'],
                    'adult_price'      => $tour->adult_price,
                    'kid_price'        => $tour->kid_price,
                    'total'            => $total,
                    'hotel_id'         => $v['is_other_hotel'] ? null : $v['hotel_id'],
                    'is_other_hotel'   => $v['is_other_hotel'],
                    'other_hotel_name' => $v['is_other_hotel'] ? $v['other_hotel_name'] : null,
                    'is_active'        => true,

                    'meeting_point_id'          => $mp?->id,
                    'meeting_point_name'        => $mp?->name,
                    'meeting_point_pickup_time' => $mp?->pickup_time,
                    'meeting_point_description'     => $mp?->description,
                    'meeting_point_map_url'     => $mp?->map_url,

                    'tour_name_snapshot'        => $tour->name, // 👈 snapshot
                ]);

                if ($promoCode) {
                    $promoCode->redeemForBooking($booking->booking_id, optional($request->user())->user_id);
                }

                return $booking;
            });
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->with('openModal', 'register')
                ->withInput();
        }

        // 4) Mail
        $booking->load(['detail.hotel', 'tour', 'user', 'tourLanguage', 'detail.tourLanguage']);
        Mail::to($booking->user->email)->queue(new BookingCreatedMail($booking));

        return redirect()->route('admin.reservas.index')
            ->with('success', __('adminlte::adminlte.booking_created_success'));
    }

    /** Actualizar reserva existente */
    public function update(Request $request, $id)
    {
        // Normalización previa
        $input = $request->all();
        $input['is_other_hotel'] = filter_var($input['is_other_hotel'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (($input['hotel_id'] ?? null) === 'other' || $input['is_other_hotel'] === true) {
            $input['hotel_id'] = null;
        } else {
            if (isset($input['hotel_id']) && !ctype_digit((string) $input['hotel_id'])) {
                $input['hotel_id'] = null;
            }
        }
        $request->replace($input);

        // 0) Validación
        try {
            $tz      = config('app.timezone', 'America/Costa_Rica');
            $minDate = BookingRules::earliestBookableDate();

            $r = $request->validate([
                'user_id'          => 'required|exists:users,user_id',
                'tour_id'          => 'required|exists:tours,tour_id',
                'tour_language_id' => 'required|exists:tour_languages,tour_language_id',
                'booking_date'     => 'required|date',
                'tour_date'        => ['required', 'date', function ($attr, $value, $fail) use ($tz, $minDate) {
                    $dt = Carbon::parse($value, $tz)->startOfDay();
                    if ($dt->lt($minDate)) {
                        $fail(__('adminlte::adminlte.no_past_dates') . ' (min: ' . $minDate->toDateString() . ')');
                    }
                }],
                'schedule_id'      => 'required|exists:schedules,schedule_id',
                'adults_quantity'  => 'required|integer|min:1',
                'kids_quantity'    => 'required|integer|min:0|max:2',
                'status'           => 'required|in:pending,confirmed,cancelled',
                'notes'            => 'nullable|string',
                'hotel_id'         => 'bail|nullable|integer|exists:hotels_list,hotel_id',
                'is_other_hotel'   => 'required|boolean',
                'other_hotel_name' => 'nullable|string|max:255|required_if:is_other_hotel,1',
                'promo_code'       => 'nullable|string',
                'remove_promo'     => 'nullable|boolean',
                'meeting_point_id' => 'nullable|integer|exists:meeting_points,id',
            ], [
                'other_hotel_name.required_if' => __('adminlte::adminlte.other_hotel_required'),
            ]);
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->with('showEditModal', $id)
                ->withInput();
        }

        // 1) Datos actuales
        $booking      = Booking::with(['detail', 'tour', 'user'])->findOrFail($id);
        $detail       = $booking->detail()->firstOrFail();
        $currentPromo = PromoCode::where('used_by_booking_id', $booking->booking_id)->first();

        // 2) Tour/horario
        $tour = Tour::with('schedules')->findOrFail($r['tour_id']);
        $schedule = $tour->schedules()
            ->where('schedules.schedule_id', $r['schedule_id'])
            ->where('schedules.is_active', true)
            ->wherePivot('is_active', true)
            ->first();

        if (!$schedule) {
            return back()
                ->withErrors(['schedule_id' => __('adminlte::adminlte.schedule_not_available')])
                ->with('showEditModal', $booking->booking_id)
                ->withInput();
        }

        // 3) Fecha bloqueada
        $isBlocked = \App\Models\TourExcludedDate::where('tour_id', $tour->tour_id)
            ->where(function ($q) use ($r) {
                $q->whereNull('schedule_id')->orWhere('schedule_id', $r['schedule_id']);
            })
            ->where('start_date', '<=', $r['tour_date'])
            ->where(function ($q) use ($r) {
                $q->where('end_date', '>=', $r['tour_date'])->orWhereNull('end_date');
            })
            ->exists();

        if ($isBlocked) {
            return back()
                ->withErrors(['tour_date' => __('adminlte::adminlte.date_blocked')])
                ->with('showEditModal', $booking->booking_id)
                ->withInput();
        }

        // 4) Capacidad (excluye esta misma reserva)
        $reserved = BookingDetail::where('tour_id', $tour->tour_id)
            ->whereDate('tour_date', $r['tour_date'])
            ->where('schedule_id', $schedule->schedule_id)
            ->where('booking_id', '<>', $booking->booking_id)
            ->sum(DB::raw('adults_quantity + kids_quantity'));

        $requested = (int)$r['adults_quantity'] + (int)$r['kids_quantity'];
        if ($reserved + $requested > $schedule->max_capacity) {
            $available = max($schedule->max_capacity - $reserved, 0);
            return back()
                ->withErrors(['capacity' => __('adminlte::adminlte.capacity_left', ['available' => $available])])
                ->with('showEditModal', $booking->booking_id)
                ->withInput();
        }

        // 5) Totales / promo (respeta operation)
        $adultPrice = $tour->adult_price;
        $kidPrice   = $tour->kid_price;
        $baseTotal  = ($adultPrice * (int)$r['adults_quantity']) + ($kidPrice * (int)$r['kids_quantity']);

        $removePromo = $request->boolean('remove_promo');
        $inputCode   = $request->filled('promo_code')
            ? strtoupper(trim(preg_replace('/\s+/', '', $request->input('promo_code'))))
            : null;

        $currentCode   = $currentPromo ? strtoupper(trim(preg_replace('/\s+/', '', $currentPromo->code))) : null;
        $promoToAssign = null;
        $errorPromo    = null;

        if ($removePromo) {
            $promoToAssign = null;
        } else {
            if ($inputCode) {
                if ($currentCode && $currentCode === $inputCode) {
                    $promoToAssign = $currentPromo;
                } else {
                    $candidate = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code, ' ', ''))) = ?", [$inputCode])->first();
                    if (!$candidate) {
                        $errorPromo = __('adminlte::adminlte.promo_invalid');
                    } elseif ($candidate->is_used && (int)$candidate->used_by_booking_id !== (int)$booking->booking_id) {
                        $errorPromo = __('adminlte::adminlte.promo_already_used');
                    } else {
                        $promoToAssign = $candidate;
                    }
                }
            } else {
                $promoToAssign = $currentPromo;
            }
        }

        if ($errorPromo) {
            return back()
                ->withErrors(['promo_code' => $errorPromo])
                ->with('showEditModal', $booking->booking_id)
                ->withInput();
        }

        $newTotal = $baseTotal;
        if ($promoToAssign) {
            $op = $promoToAssign->operation === 'add' ? 'add' : 'subtract';
            $delta = 0.0;

            if ($promoToAssign->discount_amount) {
                $delta = (float)$promoToAssign->discount_amount;
            } elseif ($promoToAssign->discount_percent) {
                $delta = round($baseTotal * ((float)$promoToAssign->discount_percent / 100), 2);
            }

            $newTotal = $op === 'add'
                ? round($baseTotal + $delta, 2)
                : max(0, round($baseTotal - $delta, 2));
        }

        // 6) Guardar
        DB::transaction(function () use (
            $booking, $detail, $tour, $schedule, $r,
            $adultPrice, $kidPrice, $newTotal,
            $removePromo, $currentPromo, $promoToAssign
        ) {
            // Promo (compat)
            if ($removePromo) {
                if ($currentPromo) {
                    $currentPromo->is_used = false;
                    $currentPromo->used_by_booking_id = null;
                    $currentPromo->save();
                }
            } else {
                if ($currentPromo && (!$promoToAssign || $currentPromo->id !== $promoToAssign->id)) {
                    $currentPromo->is_used = false;
                    $currentPromo->used_by_booking_id = null;
                    $currentPromo->save();
                }
                if ($promoToAssign) {
                    $promoToAssign->is_used = true;
                    $promoToAssign->used_by_booking_id = $booking->booking_id;
                    $promoToAssign->save();
                }
            }

            // Meeting point snapshot
            $mpId = $r['meeting_point_id'] ?? null;
            $mp   = $mpId ? MeetingPoint::find($mpId) : null;

            // ✅ Cabecera SIN schedule_id + snapshot
            $booking->update([
                'user_id'            => $r['user_id'],
                'tour_id'            => $tour->tour_id,
                'tour_language_id'   => $r['tour_language_id'],
                'booking_date'       => $r['booking_date'],
                'status'             => $r['status'],
                'total'              => $newTotal,
                'notes'              => $r['notes'] ?? null,
                'tour_name_snapshot' => $tour->name, // 👈 refrescar snapshot
            ]);

            // ✅ Detalle CON schedule_id + snapshot
            $detail->update([
                'tour_id'              => $tour->tour_id,
                'tour_language_id'     => $r['tour_language_id'],
                'tour_date'            => $r['tour_date'],
                'schedule_id'          => $schedule->schedule_id,
                'adults_quantity'      => (int)$r['adults_quantity'],
                'kids_quantity'        => (int)$r['kids_quantity'],
                'adult_price'          => $adultPrice,
                'kid_price'            => $kidPrice,
                'total'                => $newTotal,
                'hotel_id'             => $r['is_other_hotel'] ? null : $r['hotel_id'],
                'is_other_hotel'       => (bool)$r['is_other_hotel'],
                'other_hotel_name'     => $r['is_other_hotel'] ? ($r['other_hotel_name'] ?? null) : null,

                'meeting_point_id'          => $mp?->id,
                'meeting_point_name'        => $mp?->name,
                'meeting_point_pickup_time' => $mp?->pickup_time,
                'meeting_point_description' => $mp?->description,
                'meeting_point_map_url'     => $mp?->map_url,

                'tour_name_snapshot'        => $tour->name, // 👈 refrescar snapshot
            ]);
        });

        // 7) Correos
        $booking->refresh()->load(['detail.hotel', 'tour', 'user', 'tourLanguage', 'detail.tourLanguage']);

        if ($r['status'] === 'cancelled') {
            Mail::to($booking->user->email)->queue(new BookingCancelledMail($booking));
        } elseif ($r['status'] === 'confirmed') {
            Mail::to($booking->user->email)->queue(new BookingConfirmedMail($booking));
        } else {
            Mail::to($booking->user->email)->queue(new BookingUpdatedMail($booking));
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('admin.reservas.index')
            ->with('success', __('adminlte::adminlte.booking_updated_success'));
    }

    public function edit($id)
    {
        $booking = Booking::with(['detail.tour.schedules', 'detail.hotel'])
            ->findOrFail($id);

        $statuses = [
            'pending'   => 'Pending',
            'confirmed' => 'Confirmed',
            'cancelled' => 'Cancelled',
        ];

        $tz       = config('app.timezone', 'America/Costa_Rica');
        $today    = Carbon::today($tz);
        $detailDt = $booking->detail?->tour_date;
        $isPast   = $detailDt ? Carbon::parse($detailDt, $tz)->lt($today) : false;

        return view('admin.bookings.partials.edit-form', compact('booking', 'statuses', 'isPast'))->render();
    }

    /** Eliminar reserva */
    public function destroy($id)
    {
        Booking::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Reserva eliminada correctamente.');
    }

    /** Generar comprobante individual */
    public function generarComprobante(Booking $reserva)
    {
        $reserva->load(['detail.schedule', 'user']);

        $detalle      = $reserva->detail;
        $totalAdults  = $detalle->adults_quantity;
        $totalKids    = $detalle->kids_quantity;
        $totalPersons = $totalAdults + $totalKids;

        $client = preg_replace('/[^A-Za-z0-9_]/', '_', $reserva->user->full_name ?? 'Client');
        $code   = $reserva->booking_reference;

        $pdf = Pdf::loadView(
            'admin.bookingDetails.comprobante',
            compact('reserva', 'totalAdults', 'totalKids', 'totalPersons')
        );

        return $pdf->download("Receipt_{$client}_{$code}.pdf");
    }

    /** Generar PDF resumen de todas las reservas */
    public function generarPDF()
    {
        $reservas = Booking::with(['user', 'tour', 'detail.schedule', 'detail.meetingPoint', 'promoCode','redemption.promoCode'])
            ->orderBy('booking_id')
            ->get();

        $totalAdults  = $reservas->sum(fn($b) => $b->detail->adults_quantity);
        $totalKids    = $reservas->sum(fn($b) => $b->detail->kids_quantity);
        $totalPersons = $totalAdults + $totalKids;

        $pdf = Pdf::loadView(
            'admin.bookingDetails.pdf_resumen',
            compact('reservas', 'totalAdults', 'totalKids', 'totalPersons')
        );

        return $pdf->download('booking_report.pdf');
    }

    /** Crear reservas desde el carrito del usuario */
    public function storeFromCart(Request $request)
    {
        $user = Auth::user();

        $key = 'booking:cart:' . $user->user_id;
        if (RateLimiter::tooManyAttempts($key, 1)) {
            return redirect()->route('admin.cart.index')
                ->withErrors(['too_many' => 'Estamos procesando tu carrito. Intenta de nuevo en unos segundos.']);
        }
        RateLimiter::hit($key, 10);

        $createdBookings = DB::transaction(function () use ($user, $request) {
            $cart = Cart::with('items.tour')->where('user_id', $user->user_id)->first();
            if (!$cart || $cart->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Tu carrito está vacío.',
                ]);
            }

            // Validaciones por grupo
            $groups = $cart->items->groupBy(fn($item) => $item->tour_id . '_' . $item->tour_date . '_' . $item->schedule_id);

            foreach ($groups as $items) {
                $tz         = config('app.timezone', 'America/Costa_Rica');
                $minDate    = BookingRules::earliestBookableDate();

                $first      = $items->first();
                $tour       = $first->tour;
                $tourDate   = $first->tour_date;
                $scheduleId = $first->schedule_id;

                $dt = Carbon::parse($tourDate, $tz)->startOfDay();
                if ($dt->lt($minDate)) {
                    throw ValidationException::withMessages([
                        'tour_date' => "La fecha {$tourDate} ya no está disponible para reserva (mínima: {$minDate->toDateString()}).",
                    ]);
                }

                $schedule = $tour->schedules()
                    ->where('schedules.schedule_id', $scheduleId)
                    ->where('schedules.is_active', true)
                    ->wherePivot('is_active', true)
                    ->first();

                if (!$schedule) {
                    throw ValidationException::withMessages([
                        'schedule_id' => "El horario seleccionado no está disponible para '{$tour->name}'.",
                    ]);
                }

                $isBlocked = \App\Models\TourExcludedDate::where('tour_id', $tour->tour_id)
                    ->where(function ($query) use ($scheduleId) {
                        $query->whereNull('schedule_id')->orWhere('schedule_id', $scheduleId);
                    })
                    ->where('start_date', '<=', $tourDate)
                    ->where(function ($q) use ($tourDate) {
                        $q->where('end_date', '>=', $tourDate)->orWhereNull('end_date');
                    })
                    ->exists();
                if ($isBlocked) {
                    throw ValidationException::withMessages([
                        'tour_date' => "La fecha {$tourDate} está bloqueada para '{$tour->name}'.",
                    ]);
                }

                $reserved = BookingDetail::where('tour_id', $tour->tour_id)
                    ->where('tour_date', $tourDate)
                    ->where('schedule_id', $scheduleId)
                    ->sum(DB::raw('adults_quantity + kids_quantity'));

                $requested = $items->sum(fn($i) => $i->adults_quantity + $i->kids_quantity);

                if ($reserved + $requested > $schedule->max_capacity) {
                    $available = $schedule->max_capacity - $reserved;
                    throw ValidationException::withMessages([
                        'capacity' => "Para '{$tour->name}' el día {$tourDate} solo quedan {$available} plazas para ese horario.",
                    ]);
                }
            }

            // Promo del carrito
            $promoCode = null;
            $promoCodeValue = $request->input('promo_code');
            if ($promoCodeValue) {
                $cleanCode = strtoupper(trim(preg_replace('/\s+/', '', $promoCodeValue)));
                $promoCode = PromoCode::whereRaw('UPPER(TRIM(REPLACE(code, \' \', \'\'))) = ?', [$cleanCode])
                    ->where('is_used', false)
                    ->first();
            }

            $created = [];
            $promoApplied = false; // solo al primer booking

            foreach ($cart->items as $item) {
                $tour       = $item->tour;
                $adultPrice = $tour->adult_price;
                $kidPrice   = $tour->kid_price;
                $baseTotal  = ($adultPrice * $item->adults_quantity) + ($kidPrice * $item->kids_quantity);

                $total = $baseTotal;

                if ($promoCode && !$promoApplied) {
                    $op = $promoCode->operation === 'add' ? 'add' : 'subtract';
                    $delta = 0.0;

                    if ($promoCode->discount_amount) {
                        $delta = (float)$promoCode->discount_amount;
                    } elseif ($promoCode->discount_percent) {
                        $delta = round($baseTotal * ((float)$promoCode->discount_percent / 100), 2);
                    }

                    $total = $op === 'add'
                        ? round($baseTotal + $delta, 2)
                        : max(0, round($baseTotal - $delta, 2));
                }

                // ✅ Cabecera SIN schedule_id + snapshot
                $booking = Booking::create([
                    'user_id'            => $user->user_id,
                    'tour_id'            => $item->tour_id,
                    'tour_language_id'   => $item->tour_language_id,
                    'booking_reference'  => strtoupper(Str::random(10)),
                    'booking_date'       => now(),
                    'status'             => 'pending',
                    'total'              => $total,
                    'is_active'          => true,
                    'tour_name_snapshot' => $tour->name, // 👈 snapshot
                ]);

                // ✅ Detalle CON schedule_id + snapshot
                BookingDetail::create([
                    'booking_id'       => $booking->booking_id,
                    'tour_id'          => $item->tour_id,
                    'schedule_id'      => $item->schedule_id,
                    'tour_language_id' => $item->tour_language_id,
                    'tour_date'        => $item->tour_date,
                    'hotel_id'         => $item->is_other_hotel ? null : $item->hotel_id,
                    'is_other_hotel'   => $item->is_other_hotel,
                    'other_hotel_name' => $item->other_hotel_name,
                    'adults_quantity'  => $item->adults_quantity,
                    'kids_quantity'    => $item->kids_quantity,
                    'adult_price'      => $adultPrice,
                    'kid_price'        => $kidPrice,
                    'total'            => $total,
                    'is_active'        => true,

                    'meeting_point_id'          => $item->meeting_point_id,
                    'meeting_point_name'        => $item->meeting_point_name,
                    'meeting_point_pickup_time' => $item->meeting_point_pickup_time,
                    'meeting_point_description' => $item->meeting_point_description,
                    'meeting_point_map_url'     => $item->meeting_point_map_url,

                    'tour_name_snapshot'        => $tour->name, // 👈 snapshot
                ]);

                if ($promoCode && !$promoApplied) {
                    $promoCode->redeemForBooking($booking->booking_id, optional($request->user())->user_id);
                    $promoApplied = true;
                }

                $created[] = $booking;
            }

            // Vaciar carrito
            $cart->items()->delete();

            return $created;
        });

        // Enviar correos (consolidado por idioma)
        $createdBookings = collect($createdBookings);

        $allDetails = BookingDetail::with(['tour','schedule','hotel','tourLanguage','booking.user'])
            ->whereIn('booking_id', $createdBookings->pluck('booking_id')->all())
            ->get();

        if ($allDetails->isNotEmpty()) {
            $byLang = $allDetails->groupBy('tour_language_id');

            foreach ($byLang as $langId => $langDetails) {
                $firstBookingForLang = $createdBookings->firstWhere('booking_id', $langDetails->first()->booking_id);
                Mail::to($user->email)->queue(
                    new BookingCreatedMail($firstBookingForLang, $langDetails)
                );
            }
        }

        return redirect()->route('admin.cart.index')
            ->with('success', 'Reservas generadas correctamente desde el carrito.');
    }

    /** Vista del calendario */
    public function calendar()
    {
        $tours = Tour::select('tour_id', 'name')->orderBy('name')->get();
        return view('admin.bookings.calendar', compact('tours'));
    }

    /** Datos para FullCalendar */
    public function calendarData(Request $request)
    {
        $query = BookingDetail::with(['booking.user', 'tour', 'schedule', 'hotel', 'meetingPoint']);

        if ($request->filled('from')) {
            $query->where('tour_date', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->where('tour_date', '<=', $request->input('to'));
        }

        if ($request->filled('tour_id')) {
            $query->where('tour_id', $request->input('tour_id'));
        }

        $events = $query->get()->map(function ($detail) {
            $schedule = $detail->schedule;

            $startTime = $schedule && $schedule->start_time
                ? Carbon::parse($schedule->start_time)->format('H:i:s')
                : '08:00:00';

            $endTime = $schedule && $schedule->end_time
                ? Carbon::parse($schedule->end_time)->format('H:i:s')
                : '10:00:00';

            $adults  = $detail->adults_quantity;
            $kids    = $detail->kids_quantity;
            $paxText = $adults . ($kids > 0 ? "+{$kids}" : '');

            $tourName  = $detail->tour->name ?? '';
            $shortName = preg_replace('/\((.*?)\)/', '', $tourName);
            $shortName = preg_replace('/\b(Tour|Combo|Experience|Adventure|Full Day|Half Day)\b/i', '', $shortName);
            $shortName = trim(\Illuminate\Support\Str::limit(trim($shortName), 25));

            return [
                'id'          => $detail->booking->booking_id,
                'title'       => '',
                'start'       => "{$detail->tour_date->toDateString()}T{$startTime}",
                'end'         => "{$detail->tour_date->toDateString()}T{$endTime}",
                'backgroundColor' => $detail->tour->color ?? '#5cb85c',
                'borderColor'     => $detail->tour->color ?? '#5cb85c',
                'textColor'       => '#000',
                'status'          => $detail->booking->status,
                'hotel_name'      => $detail->hotel->name ?? '—',
                'pax'             => $paxText . ' pax',
                'short_tour_name' => $shortName,
                'booking_ref'     => '#' . $detail->booking->booking_reference,
                'adults'          => $adults,
                'kids'            => $kids,
                'total'           => $detail->total,
                'meeting_point_name'        => $detail->meeting_point_name,
                'meeting_point_pickup_time' => $detail->meeting_point_pickup_time,
            ];
        });

        return response()->json($events);
    }

    public function reservedCount(Request $request)
    {
        $tz      = config('app.timezone', 'America/Costa_Rica');
        $minDate = BookingRules::earliestBookableDate();

        $data = $request->validate([
            'tour_id'     => 'required|exists:tours,tour_id',
            'schedule_id' => 'required|exists:schedules,schedule_id',
            'tour_date'   => ['required', 'date', function ($attr, $value, $fail) use ($tz, $minDate) {
                $dt = Carbon::parse($value, $tz)->startOfDay();
                if ($dt->lt($minDate)) {
                    $fail(__('adminlte::adminlte.no_past_dates') . ' (min: ' . $minDate->toDateString() . ')');
                }
            }],
        ]);

        $reserved = BookingDetail::where('tour_id', $data['tour_id'])
            ->where('tour_date', $data['tour_date'])
            ->where('schedule_id', $data['schedule_id'])
            ->sum(DB::raw('adults_quantity + kids_quantity'));

        return response()->json(['reserved' => $reserved]);
    }

    public function myReservations()
    {
        $bookings = Booking::with(['user', 'tour', 'detail.hotel', 'detail.meetingPoint'])
            ->where('user_id', Auth::id())
            ->orderByDesc('booking_date')
            ->get();

        return view('customer.reservations.index', compact('bookings'));
    }

    public function showReceipt(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);
        return $this->generarComprobante($booking);
    }

    public function generarExcel(Request $request)
    {
        $filters = $request->only([
            'reference',
            'tour_id',
            'status',
            'tour_date_from',
            'tour_date_to',
            'booking_date_from',
            'booking_date_to',
            'schedule_id',
        ]);

        $filters = array_merge([
            'reference'         => null,
            'tour_id'           => null,
            'status'            => null,
            'tour_date_from'    => null,
            'tour_date_to'      => null,
            'booking_date_from' => null,
            'booking_date_to'   => null,
            'schedule_id'       => null,
        ], $filters);

        $bookings = $this->filtrarReservas($filters)->get();

        return Excel::download(
            new BookingsExport($filters, $bookings),
            BookingsExport::generarNombre($filters)
        );
    }

    public function filtrarReservas(array $filters)
    {
        return Booking::with([
            'user',
            'detail.tour.tourType',
            'detail.hotel',
            'detail.schedule',
            'detail.meetingPoint',
        ])
            ->when($filters['reference'] ?? false, function ($q) use ($filters) {
                $q->where('booking_reference', 'ILIKE', '%' . $filters['reference'] . '%');
            })
            ->when($filters['status'] ?? false, function ($q) use ($filters) {
                $q->where('status', $filters['status']);
            })
            ->when($filters['booking_date_from'] ?? false, function ($q) use ($filters) {
                $q->whereDate('booking_date', '>=', $filters['booking_date_from']);
            })
            ->when($filters['booking_date_to'] ?? false, function ($q) use ($filters) {
                $q->whereDate('booking_date', '<=', $filters['booking_date_to']);
            })
            ->when(
                ($filters['tour_id'] ?? false) ||
                ($filters['schedule_id'] ?? false) ||
                ($filters['tour_date_from'] ?? false) ||
                ($filters['tour_date_to'] ?? false),
                function ($q) use ($filters) {
                    $q->whereHas('detail', function ($q) use ($filters) {
                        if ($filters['tour_id'] ?? false) {
                            $q->where('tour_id', $filters['tour_id']);
                        }
                        if ($filters['schedule_id'] ?? false) {
                            $q->where('schedule_id', $filters['schedule_id']);
                        }
                        if ($filters['tour_date_from'] ?? false) {
                            $q->whereDate('tour_date', '>=', $filters['tour_date_from']);
                        }
                        if ($filters['tour_date_to'] ?? false) {
                            $q->whereDate('tour_date', '<=', $filters['tour_date_to']);
                        }
                    });
                }
            );
    }
}
