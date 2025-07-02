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

class BookingController extends Controller
{
    /** Listado de reservas */
    public function index(Request $request)
    {
        $sort      = $request->get('sort', 'booking_id');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['booking_id','booking_date','total','status','user'];
        if (! in_array($sort, $allowedSorts)) {
            $sort = 'booking_id';
        }

        // Cargamos user + detalle (con tour y hotel) + tour
        $query = Booking::with([
                'user',
                'detail.tour',      // para nombre del tour
                'detail.hotel',     // para nombre del hotel
            ])
            ->join('users','bookings.user_id','users.user_id')
            ->select('bookings.*');

        if (Auth::user()->role_id === 3) {
            $query->where('bookings.user_id', Auth::id());
        }
        $reference = $request->get('reference');
        if ($reference) {
            // buscamos coincidencias parciales (ILIKE en Postgres)
            $query->where('booking_reference', 'ILIKE', "%{$reference}%");
        }

        $bookings = $query
            ->orderBy($sort === 'user' ? 'users.full_name' : $sort, $direction)
            ->paginate(10)
            ->appends([
                'sort'      => $sort,
                'direction' => $direction,
                'reference' => $reference,
            ]);
        $bookings = $query
            ->orderBy($sort === 'user' ? 'users.full_name' : $sort, $direction)
            ->paginate(10)
            ->appends(compact('sort','direction'));

        // Pasamos también la lista de hoteles activos para el modal de edición
        $hotels = \App\Models\HotelList::where('is_active', true)->orderBy('name')->get();

        return view('admin.bookings.index', compact('bookings','sort','direction','hotels'));
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
            'hotel_id'         => 'nullable|exists:hotels_list,hotel_id',
            'is_other_hotel'   => 'required|boolean',
            'other_hotel_name' => 'nullable|string|max:255',
        ]);

        // 1. Validación de cupo
        $tour      = Tour::findOrFail($v['tour_id']);
        $tourDate  = $v['tour_date'];
        $max       = $tour->max_capacity;

        // suma de reservas existentes en esa fecha
        $reserved  = BookingDetail::where('tour_id', $tour->tour_id)
            ->where('tour_date', $tourDate)
            ->sum(DB::raw('adults_quantity + kids_quantity'));

        // plazas solicitadas ahora
        $requested = $v['adults_quantity'] + $v['kids_quantity'];

        if ($reserved + $requested > $max) {
            $available = $max - $reserved;
            return back()
                ->withErrors([
                    'capacity' => "Sólo quedan {$available} plazas disponibles para '{$tour->name}' el día {$tourDate}."
                ])
                ->withInput();
        }

        // 2. Si pasa, calculamos total y guardamos
        $total = ($tour->adult_price * $v['adults_quantity'])
            + ($tour->kid_price   * $v['kids_quantity']);

        $booking = Booking::create([
            'user_id'           => $v['user_id'],
            'tour_id'           => $tour->tour_id,
            'tour_language_id'  => $v['tour_language_id'],
            'booking_reference' => strtoupper(Str::random(10)),
            'booking_date'      => $v['booking_date'],
            'status'            => $v['status'],
            'total'             => $total,
            'is_active'         => true,
        ]);

        BookingDetail::create([
            'booking_id'       => $booking->booking_id,
            'tour_id'          => $tour->tour_id,
            'tour_language_id' => $v['tour_language_id'],
            'tour_date'        => $tourDate,
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

        return redirect()
            ->route('admin.reservas.index')
            ->with('success', 'Reserva creada correctamente.');
    }


    /** Mostrar formulario de edición de una reserva vía AJAX */
    public function edit($reservaId, Request $request)
    {
        $booking  = Booking::with(['details','user','tour'])->findOrFail($reservaId);
        $statuses = [
            'pending'   => 'Pending',
            'confirmed' => 'Confirmed',
            'cancelled' => 'Cancelled',
        ];

        if ($request->ajax()) {
            return view('admin.bookings.partials.edit-form', compact('booking','statuses'));
        }

        return redirect()->route('admin.reservas.index');
    }

    /** Actualizar reserva existente */
    public function update(Request $request, $id)
    {
        // 1. Validación básica de inputs…
        $r = $request->validate([
            'adults_quantity'  => 'required|integer|min:1',
            'kids_quantity'    => 'required|integer|min:0',
            'status'           => 'required|string|in:pending,confirmed,cancelled',
            'notes'            => 'nullable|string',
            'hotel_id'         => 'nullable|exists:hotels_list,hotel_id',
            'is_other_hotel'   => 'required|boolean',
            'other_hotel_name' => 'nullable|string|max:255',
        ]);

        $booking = Booking::findOrFail($id);
        $detail  = $booking->details()->firstOrFail();

        // 2. Cálculo de plazas solicitadas y cupo máximo
        $requested = $r['adults_quantity'] + $r['kids_quantity'];
        $max       = $booking->tour->max_capacity;  // de tu nuevo campo en tours

        // 3. Cuántas plazas ya están reservadas en esa fecha, excluyendo esta reserva
        $reserved = BookingDetail::where('tour_id', $booking->tour_id)
            ->where('tour_date', $detail->tour_date)
            ->where('booking_id', '<>', $booking->booking_id)
            ->sum(DB::raw('adults_quantity + kids_quantity'));

        // 4. Validar antes de actualizar
        if ($reserved + $requested > $max) {
            return back()
                ->withErrors([
                    'capacity' => "Sólo quedan " . ($max - $reserved) . " plazas disponibles para este tour."
                ])
                ->withInput()
                ->with('showEditModal', $booking->booking_id);
        }

        // … si pasa la validación, recalcula totales y guarda …
        $newTotal = ($detail->adult_price * $r['adults_quantity'])
                + ($detail->kid_price   * $r['kids_quantity']);

        $booking->update([
            'status' => $r['status'],
            'notes'  => $r['notes'] ?? null,
            'total'  => $newTotal,
        ]);

        $detail->update([
            'adults_quantity'  => $r['adults_quantity'],
            'kids_quantity'    => $r['kids_quantity'],
            'total'            => $newTotal,
            'hotel_id'         => $r['is_other_hotel'] ? null : $r['hotel_id'],
            'is_other_hotel'   => $r['is_other_hotel'],
            'other_hotel_name' => $r['is_other_hotel'] ? $r['other_hotel_name'] : null,
        ]);

        return redirect()
            ->route('admin.reservas.index')
            ->with('success', 'Reserva actualizada correctamente.');
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
        $detalle      = $reserva->detail;
        $totalAdults  = $detalle->adults_quantity;
        $totalKids    = $detalle->kids_quantity;
        $totalPersons = $totalAdults + $totalKids;

        $client = preg_replace('/[^A-Za-z0-9_]/', '_', $reserva->user->full_name ?? 'Client');
        $code   = $reserva->booking_reference;

        $pdf = Pdf::loadView('admin.bookingDetails.comprobante', compact('reserva','totalAdults','totalKids','totalPersons'));
        return $pdf->download("Receipt_{$client}_{$code}.pdf");
    }

    /** Generar PDF resumen de todas las reservas */
    public function generarPDF()
    {
        $reservas = Booking::with(['user','tour','detail'])->orderBy('booking_id')->get();

        $totalAdults  = $reservas->sum(fn($b) => $b->detail->adults_quantity);
        $totalKids    = $reservas->sum(fn($b) => $b->detail->kids_quantity);
        $totalPersons = $totalAdults + $totalKids;

        $pdf = Pdf::loadView('admin.bookingDetails.pdf_resumen', compact('reservas','totalAdults','totalKids','totalPersons'));
        return $pdf->download('booking_report.pdf');
    }

    /** Crear reservas desde el carrito del usuario */
    public function storeFromCart(Request $request)
    {
        $user = Auth::user();
        $cart = Cart::with('items.tour')->where('user_id', $user->user_id)->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()
                ->route('admin.cart.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        // 1. Agrupar por tour y fecha
        $groups = $cart->items
            ->groupBy(fn($item) => $item->tour_id . '_' . $item->tour_date);

        foreach ($groups as $key => $items) {
            /** @var \App\Models\CartItem $first */
            $first     = $items->first();
            $tour      = $first->tour;
            $tourDate  = $first->tour_date;
            $max       = $tour->max_capacity;                              // cupo del tour
            $reserved  = BookingDetail::where('tour_id', $tour->tour_id)   // ya reservadas
                ->where('tour_date', $tourDate)
                ->sum(DB::raw('adults_quantity + kids_quantity'));
            $requested = $items->sum(fn($i) =>                             // solicitadas en carrito
                $i->adults_quantity + $i->kids_quantity
            );

            if ($reserved + $requested > $max) {
                $available = $max - $reserved;
                return redirect()
                    ->route('admin.cart.index')
                    ->with('error', "Para '{$tour->name}' el día {$tourDate} sólo quedan {$available} plazas.");
            }
        }

        // 2. Si todo OK, creamos el Booking principal
        $totalBooking = $cart->items->sum(fn($item) =>
            ($item->tour->adult_price * $item->adults_quantity)
        + ($item->tour->kid_price   * $item->kids_quantity)
        );

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

        // 3. Creamos cada detalle
        foreach ($cart->items as $item) {
            BookingDetail::create([
                'booking_id'       => $booking->booking_id,
                'tour_id'          => $item->tour_id,
                'tour_schedule_id' => $item->tour_schedule_id,
                'tour_language_id' => $item->tour_language_id,
                'tour_date'        => $item->tour_date,
                'hotel_id'         => $item->is_other_hotel
                                        ? null
                                        : $item->hotel_id,
                'is_other_hotel'   => $item->is_other_hotel,
                'other_hotel_name' => $item->other_hotel_name,
                'adults_quantity'  => $item->adults_quantity,
                'kids_quantity'    => $item->kids_quantity,
                'adult_price'      => $item->tour->adult_price,
                'kid_price'        => $item->tour->kid_price,
                'total'            => ($item->tour->adult_price * $item->adults_quantity)
                                + ($item->tour->kid_price   * $item->kids_quantity),
                'is_active'        => true,
            ]);
        }

        // 4. Vaciar el carrito
        $cart->items()->delete();

        return redirect()
            ->route('admin.cart.index')
            ->with('success', 'Reservas generadas correctamente desde el carrito.');
    }




    /** Vista del calendario */
    public function calendar()
    {
        return view('admin.bookings.calendar');
    }

    /** Datos para FullCalendar */
    public function calendarData(Request $request)
{
    $query = BookingDetail::with(['booking.user', 'tour', 'tourSchedule']);

    if ($request->filled('from')) {
        $query->where('tour_date', '>=', $request->input('from'));
    }
    if ($request->filled('to')) {
        $query->where('tour_date', '<=', $request->input('to'));
    }

    $events = $query->get()->map(function ($d) {
        // Usa horario del detalle o del tourSchedule
        $schedule = $d->tourSchedule;
        $startTime = $schedule ? $schedule->start_time : '08:00:00';
        $endTime   = $schedule ? $schedule->end_time   : '10:00:00';

        return [
            'id'     => $d->booking->booking_id,
            'title'  => "{$d->booking->user->full_name} – {$d->tour->name}",
            'start'  => "{$d->tour_date->toDateString()}T{$startTime}",
            'end'    => "{$d->tour_date->toDateString()}T{$endTime}",
            'status' => $d->booking->status,
            'adults' => $d->adults_quantity,
            'kids'   => $d->kids_quantity,
            'total'  => $d->total,
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

}
