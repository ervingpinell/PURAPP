<?php

namespace App\Http\Controllers\Admin\Bookings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Booking;
use App\Models\Tour;
use App\Models\BookingDetail;
use App\Models\Cart;
use App\Models\HotelList;
use App\Models\Schedule;
use Illuminate\Support\Facades\Mail;
use App\Exports\BookingsExport;
use App\Mail\BookingCreatedMail;
use App\Mail\BookingUpdatedMail;
use App\Mail\BookingConfirmedMail;
use App\Mail\BookingCancelledMail;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\PromoCode;



class BookingController extends Controller
{
    /** Listado de reservas */
    /** Listado de reservas */public function index(Request $request)
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
        'detail.schedule'
    ])
    ->join('users', 'bookings.user_id', '=', 'users.user_id')
    ->select('bookings.*');

    // Solo clientes ven sus reservas
    if (Auth::user()->role_id === 3) {
        $query->where('bookings.user_id', Auth::id());
    }

    // Filtro: referencia
    if ($request->filled('reference')) {
        $query->where('bookings.booking_reference', 'ILIKE', "%{$request->reference}%");
    }

    // Filtro: estado
    if ($request->filled('status')) {
        $query->where('bookings.status', $request->status);
    }

    // Filtro: fecha de reserva
    if ($request->filled('booking_date_from')) {
        $query->whereDate('bookings.booking_date', '>=', $request->booking_date_from);
    }
    if ($request->filled('booking_date_to')) {
        $query->whereDate('bookings.booking_date', '<=', $request->booking_date_to);
    }

    // Filtros de detalles (fecha viaje, tour, horario)
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

    // Ordenamiento
    $bookings = $query
        ->orderBy($sort === 'user' ? 'users.full_name' : 'bookings.' . $sort, $direction)
        ->paginate(10)
        ->appends($request->all());

    // Datos para filtros
    $hotels    = HotelList::where('is_active', true)->orderBy('name')->get();
    $schedules = Schedule::orderBy('start_time')->get();
    $tours     = Tour::orderBy('name')->get();

    return view('admin.bookings.index', compact(
        'bookings', 'sort', 'direction', 'hotels', 'schedules', 'tours'
    ));
}


    /** Crear reserva desde formulario manual */
    public function store(Request $request)
    {
        $v = $request->validate([
            'user_id'          => 'required|exists:users,user_id',
            'tour_id'          => 'required|exists:tours,tour_id',
            'booking_date'     => 'required|date',
            'tour_date'        => 'required|date',
            'status'           => 'required|in:pending,confirmed,cancelled',
            'tour_language_id' => 'required|exists:tour_languages,tour_language_id',
            'adults_quantity'  => 'required|integer|min:1',
            'kids_quantity'    => 'required|integer|min:0|max:2',
            'schedule_id'      => 'required|exists:schedules,schedule_id',
            'hotel_id'         => 'nullable|exists:hotels_list,hotel_id',
            'is_other_hotel'   => 'required|boolean',
            'other_hotel_name' => 'nullable|string|max:255',
        ]);

        $tour = Tour::with('schedules')->findOrFail($v['tour_id']);
        $schedule = $tour->schedules()->where('schedules.schedule_id', $v['schedule_id'])->firstOrFail();

        // ✅ Valida horario
        if (! $tour->schedules()->where('schedules.schedule_id', $v['schedule_id'])->exists()) {
            return back()->withErrors(['schedule_id' => 'El horario no pertenece a este tour.']);
        }

        // ✅ Valida fecha bloqueada
        $isBlocked = \App\Models\TourExcludedDate::where('tour_id', $tour->tour_id)
            ->where(function ($query) use ($v) {
                $query->whereNull('schedule_id')
                    ->orWhere('schedule_id', $v['schedule_id']);
            })
            ->where('start_date', '<=', $v['tour_date'])
            ->where(function ($q) use ($v) {
                $q->where('end_date', '>=', $v['tour_date'])
                ->orWhereNull('end_date');
            })
            ->exists();


        if ($isBlocked) {
            return back()->withErrors(['tour_date' => 'La fecha seleccionada está bloqueada para este tour.'])->withInput();
        }

        // ✅ Valida cupo
        $reserved = BookingDetail::where('tour_id', $tour->tour_id)
            ->where('tour_date', $v['tour_date'])
            ->where('schedule_id', $v['schedule_id'])
            ->sum(DB::raw('adults_quantity + kids_quantity'));

        $requested = $v['adults_quantity'] + $v['kids_quantity'];

        if ($reserved + $requested > $schedule->max_capacity) {
            $available = $schedule->max_capacity - $reserved;
            return back()->withErrors(['capacity' => "Solo quedan {$available} plazas disponibles para este horario."])->withInput();
        }

        $total = ($tour->adult_price * $v['adults_quantity']) + ($tour->kid_price * $v['kids_quantity']);
        $promoCodeValue = $request->input('promo_code');
        $promoCode = null;

        if ($promoCodeValue) {
            $cleanCode = strtoupper(trim(preg_replace('/\s+/', '', $promoCodeValue)));
            $promoCode = PromoCode::whereRaw('UPPER(TRIM(REPLACE(code, \' \', \'\'))) = ?', [$cleanCode])
                                ->where('is_used', false)
                                ->first();
        }

        $booking = Booking::create([
            'user_id'           => $v['user_id'],
            'tour_id'           => $tour->tour_id,
            'tour_language_id'  => $v['tour_language_id'],
            'schedule_id'       => $v['schedule_id'],
            'booking_reference' => strtoupper(Str::random(10)),
            'booking_date'      => $v['booking_date'],
            'status'            => $v['status'],
            'total'             => $total,
            'promo_code_id'     => $promoCode?->promo_code_id,
            'is_active'         => true,
        ]);

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
        ]);

        if ($promoCode) {
            $promoCode->markAsUsed($booking->booking_id);
        }

        // ✅ Envía el correo de confirmación
        Mail::to($booking->user->email)->send(new BookingCreatedMail($booking));

        return redirect()->route('admin.reservas.index')->with('success', 'Reserva creada correctamente.');
    }


    /** Actualizar reserva existente */
    public function update(Request $request, $id)
    {
        $r = $request->validate([
            'adults_quantity'  => 'required|integer|min:1',
            'kids_quantity'    => 'required|integer|min:0|max:2',
            'status'           => 'required|in:pending,confirmed,cancelled',
            'notes'            => 'nullable|string',
            'schedule_id'      => 'required|exists:schedules,schedule_id',
            'hotel_id'         => 'nullable|exists:hotels_list,hotel_id',
            'is_other_hotel'   => 'required|boolean',
            'other_hotel_name' => 'nullable|string|max:255',
        ]);

        $booking = Booking::with(['tour', 'user'])->findOrFail($id);
        $detail  = $booking->details()->firstOrFail();

        // ✅ Validar que el horario pertenece al tour
        if (! $booking->tour->schedules()->where('schedules.schedule_id', $r['schedule_id'])->exists()) {
            return back()->withErrors(['schedule_id' => 'El horario no pertenece a este tour.']);
        }

        // ✅ Validar si la fecha está bloqueada
        $isBlocked = \App\Models\TourExcludedDate::where('tour_id', $booking->tour_id)
            ->where(function ($query) use ($r) {
                $query->whereNull('schedule_id')
                    ->orWhere('schedule_id', $r['schedule_id']);
            })
            ->where('start_date', '<=', $detail->tour_date)
            ->where(function ($q) use ($detail) {
                $q->where('end_date', '>=', $detail->tour_date)
                ->orWhereNull('end_date');
            })
            ->exists();

        if ($isBlocked) {
            return back()->withErrors(['tour_date' => 'La fecha está bloqueada para este tour y horario.'])
                ->withInput()
                ->with('showEditModal', $booking->booking_id);
        }

        // ✅ Validar capacidad
        $reserved = BookingDetail::where('tour_id', $booking->tour_id)
            ->where('tour_date', $detail->tour_date)
            ->where('schedule_id', $r['schedule_id'])
            ->where('booking_id', '<>', $booking->booking_id)
            ->sum(DB::raw('adults_quantity + kids_quantity'));

        $requested = $r['adults_quantity'] + $r['kids_quantity'];

        $schedule = $booking->tour->schedules()->where('schedules.schedule_id', $r['schedule_id'])->firstOrFail();

        if ($reserved + $requested > $schedule->max_capacity) {
            $available = $schedule->max_capacity - $reserved;
            return back()->withErrors(['capacity' => "Solo quedan {$available} plazas."])
                ->withInput()
                ->with('showEditModal', $booking->booking_id);
        }

        // ✅ Actualizar total
        $newTotal = ($detail->adult_price * $r['adults_quantity']) + ($detail->kid_price * $r['kids_quantity']);

        $booking->update([
            'status'      => $r['status'],
            'notes'       => $r['notes'] ?? null,
            'total'       => $newTotal,
            'schedule_id' => $r['schedule_id'],
        ]);

        $detail->update([
            'adults_quantity'  => $r['adults_quantity'],
            'kids_quantity'    => $r['kids_quantity'],
            'schedule_id'      => $r['schedule_id'],
            'total'            => $newTotal,
            'hotel_id'         => $r['is_other_hotel'] ? null : $r['hotel_id'],
            'is_other_hotel'   => $r['is_other_hotel'],
            'other_hotel_name' => $r['is_other_hotel'] ? $r['other_hotel_name'] : null,
        ]);

        // ✅ Enviar correo según status
        if ($r['status'] === 'cancelled') {
            Mail::to($booking->user->email)->send(new \App\Mail\BookingCancelledMail($booking));
        } elseif ($r['status'] === 'confirmed') {
            Mail::to($booking->user->email)->send(new \App\Mail\BookingConfirmedMail($booking));
        } else {
            Mail::to($booking->user->email)->send(new \App\Mail\BookingUpdatedMail($booking));
        }

        // ✅ Si es AJAX
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.reservas.index')->with('success', 'Reserva actualizada correctamente.');
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

        return view('admin.bookings.partials.edit-form', compact('booking', 'statuses'))->render();
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
        // Carga relaciones necesarias: detail con schedule y usuario
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
        // Asegúrate de cargar schedules dentro de detail
        $reservas = Booking::with(['user', 'tour', 'detail.schedule', 'promoCode'])
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
        $cart = Cart::with('items.tour')->where('user_id', $user->user_id)->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('admin.cart.index')->with('error', 'Tu carrito está vacío.');
        }

        // ✅ Agrupar por tour, fecha y horario
        $groups = $cart->items->groupBy(fn($item) =>
            $item->tour_id . '_' . $item->tour_date . '_' . $item->schedule_id
        );

        foreach ($groups as $key => $items) {
            $first      = $items->first();
            $tour       = $first->tour;
            $tourDate   = $first->tour_date;
            $scheduleId = $first->schedule_id;
            $schedule = $tour->schedules()->where('schedules.schedule_id', $scheduleId)->firstOrFail();

            // ✅ Validar fecha bloqueada
            $isBlocked = \App\Models\TourExcludedDate::where('tour_id', $tour->tour_id)
                ->where(function ($query) use ($scheduleId) {
                    $query->whereNull('schedule_id')
                        ->orWhere('schedule_id', $scheduleId);
                })
                ->where('start_date', '<=', $tourDate)
                ->where(function ($q) use ($tourDate) {
                    $q->where('end_date', '>=', $tourDate)
                    ->orWhereNull('end_date');
                })
                ->exists();

            if ($isBlocked) {
                return redirect()->route('admin.cart.index')
                    ->with('error', "La fecha {$tourDate} está bloqueada para '{$tour->name}'.");
            }

            // ✅ Validar cupo
            $reserved = BookingDetail::where('tour_id', $tour->tour_id)
                ->where('tour_date', $tourDate)
                ->where('schedule_id', $scheduleId)
                ->sum(DB::raw('adults_quantity + kids_quantity'));

            $requested = $items->sum(fn($i) =>
                $i->adults_quantity + $i->kids_quantity
            );

            if ($reserved + $requested > $schedule->max_capacity) {
                $available = $schedule->max_capacity - $reserved;
                return redirect()->route('admin.cart.index')
                    ->with('error', "Para '{$tour->name}' el día {$tourDate} solo quedan {$available} plazas para ese horario.");
            }
        }

        // ✅ Crear booking principal
        // ✅ Total sin descuento
        $totalBooking = $cart->items->sum(fn($item) =>
            ($item->tour->adult_price * $item->adults_quantity)
            + ($item->tour->kid_price * $item->kids_quantity)
        );

        // ✅ Aplicar código promocional si corresponde
        $promoCodeValue = $request->input('promo_code');
        $promoCode = null;
        $discountAmount = 0;

        if ($promoCodeValue) {
            $cleanCode = strtoupper(trim(preg_replace('/\s+/', '', $promoCodeValue)));
            $promoCode = PromoCode::whereRaw('UPPER(TRIM(REPLACE(code, \' \', \'\'))) = ?', [$cleanCode])
                                ->where('is_used', false)
                                ->first();


            if ($promoCode) {
                if ($promoCode->discount_amount) {
                    $discountAmount = $promoCode->discount_amount;
                } elseif ($promoCode->discount_percent) {
                    $discountAmount = $totalBooking * ($promoCode->discount_percent / 100);
                }

                $totalBooking = max($totalBooking - $discountAmount, 0);
            }
        }


        $firstItem = $cart->items->first();

        $booking = Booking::create([
            'user_id'           => $user->user_id,
            'tour_id'           => $firstItem->tour_id,
            'tour_language_id'  => $firstItem->tour_language_id,
            'booking_reference' => strtoupper(Str::random(10)),
            'booking_date'      => now(),
            'status'            => 'pending',
            'total'             => $totalBooking,
            'is_active'         => true,
        ]);

        // ✅ Crear detalles
        foreach ($cart->items as $item) {
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
                'adult_price'      => $item->tour->adult_price,
                'kid_price'        => $item->tour->kid_price,
                'total'            => ($item->tour->adult_price * $item->adults_quantity)
                                    + ($item->tour->kid_price * $item->kids_quantity),
                'is_active'        => true,
            ]);
        }

        // ✅ Marcar código como usado
        if ($promoCode) {
            $promoCode->markAsUsed($booking->booking_id);
        }

        // ✅ Vaciar carrito
        $cart->items()->delete();

        // ✅ Enviar correo de confirmación
        Mail::to($booking->user->email)
            ->send(new \App\Mail\BookingCreatedMail($booking));

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
    $query = BookingDetail::with(['booking.user', 'tour', 'schedule', 'hotel']);

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
            ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i:s')
            : '08:00:00';

        $endTime = $schedule && $schedule->end_time
            ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i:s')
            : '10:00:00';

        $adults = $detail->adults_quantity;
        $kids = $detail->kids_quantity;
        $paxText = $adults . ($kids > 0 ? "+{$kids}" : '');

        $tourName = $detail->tour->name ?? '';
        $shortName = preg_replace('/\((.*?)\)/', '', $tourName);
        $shortName = preg_replace('/\b(Tour|Combo|Experience|Adventure|Full Day|Half Day)\b/i', '', $shortName);
        $shortName = trim(\Illuminate\Support\Str::limit(trim($shortName), 25));

        return [
            'id' => $detail->booking->booking_id,
            'title' => '',
            'start' => "{$detail->tour_date->toDateString()}T{$startTime}",
            'end' => "{$detail->tour_date->toDateString()}T{$endTime}",
            'backgroundColor' => $detail->tour->color ?? '#5cb85c',
            'borderColor' => $detail->tour->color ?? '#5cb85c',
            'textColor' => '#000',
            'status' => $detail->booking->status,
            'hotel_name' => $detail->hotel->name ?? '—',
            'pax' => $paxText . ' pax',
            'short_tour_name' => $shortName,
            'booking_ref' => '#' . $detail->booking->booking_reference,
            'adults' => $adults,
            'kids' => $kids,
            'total' => $detail->total,
        ];
    });

    return response()->json($events);
}




    public function reservedCount(Request $request)
    {
        $data = $request->validate([
            'tour_id'   => 'required|exists:tours,tour_id',
            'tour_date' => 'required|date',
        ]);

        $reserved = BookingDetail::where('tour_id', $data['tour_id'])
            ->where('tour_date', $data['tour_date'])
            ->where('schedule_id', $data['schedule_id'])
            ->sum(DB::raw('adults_quantity + kids_quantity'));
        return response()->json(['reserved' => $reserved]);
    }

    /**
     * Show the authenticated customer’s reservations.
     */
    public function myReservations()
    {
        $bookings = Booking::with(['user','tour','detail.hotel'])
            ->where('user_id', Auth::id())
            ->orderByDesc('booking_date')
            ->get();

        return view('customer.reservations.index', compact('bookings'));
    }

    /** Mostrar comprobante (PDF o vista) */
    public function showReceipt(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        // Si ya tienes un método generarComprobante que devuelve PDF:
        return $this->generarComprobante($booking);

        // —o— si quieres mostrarlo en una vista Blade:
        // return view('customer.reservations.receipt', compact('booking'));
    }


public function generarExcel(Request $request)
{
    // Asegurar que todos los filtros estén definidos, aunque vengan vacíos
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

    // Esto garantiza que las claves existen aunque estén vacías
    $filters = array_merge([
        'reference' => null,
        'tour_id' => null,
        'status' => null,
        'tour_date_from' => null,
        'tour_date_to' => null,
        'booking_date_from' => null,
        'booking_date_to' => null,
        'schedule_id' => null,
    ], $filters);

    // Obtener las reservas filtradas
    $bookings = $this->filtrarReservas($filters)->get();

    // Generar nombre dinámico del archivo
    $nombre = BookingsExport::generarNombre($filters);

    // Pasar tanto filtros como bookings al exportador
    return Excel::download(
        new BookingsExport($filters, $bookings),
        BookingsExport::generarNombre($filters)

    );
}

public function filtrarReservas(array $filters)
{
    return \App\Models\Booking::with([
            'user',
            'detail.tour.tourType',
            'detail.hotel',
            'detail.schedule'
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
