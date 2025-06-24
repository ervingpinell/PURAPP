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

        $query = Booking::with(['user','detail'])
            ->join('users','bookings.user_id','users.user_id')
            ->select('bookings.*');

        // Solo los clientes (role_id = 3) ven sus propias reservas
        if (Auth::user()->role_id === 3) {
            $query->where('bookings.user_id', Auth::id());
        }

        $bookings = $query
            ->orderBy($sort === 'user' ? 'users.full_name' : $sort, $direction)
            ->paginate(10)
            ->appends(compact('sort','direction'));

        return view('admin.bookings.index', compact('bookings','sort','direction'));
    }

    /** Crear reserva desde formulario manual */
    public function store(Request $request)
    {
        $v = $request->validate([
            'user_id'          => 'required|exists:users,user_id',
            'tour_id'          => 'required|exists:tours,tour_id',
            'booking_date'     => 'required|date',
            'status'           => 'required|in:pending,confirmed,cancelled',
            'tour_language_id' => 'required|exists:tour_languages,tour_language_id',
            'adults_quantity'  => 'required|integer|min:1',
            'kids_quantity'    => 'required|integer|min:0|max:2',
        ]);

        $tour = Tour::findOrFail($v['tour_id']);
        $total = ($tour->adult_price * $v['adults_quantity']) + ($tour->kid_price * $v['kids_quantity']);

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
            'tour_date'        => $v['booking_date'],
            'adults_quantity'  => $v['adults_quantity'],
            'kids_quantity'    => $v['kids_quantity'],
            'adult_price'      => $tour->adult_price,
            'kid_price'        => $tour->kid_price,
            'total'            => $total,
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
        $r = $request->validate([
            'adults_quantity' => 'required|integer|min:1',
            'kids_quantity'   => 'required|integer|min:0',
            'status'          => 'required|string|in:pending,confirmed,cancelled',
            'notes'           => 'nullable|string',
        ]);

        $booking = Booking::findOrFail($id);
        $detail  = $booking->details()->firstOrFail();

        $newTotal = ($detail->adult_price * $r['adults_quantity']) + ($detail->kid_price * $r['kids_quantity']);

        $booking->update([
            'status' => $r['status'],
            'notes'  => $r['notes'] ?? null,
            'total'  => $newTotal,
        ]);

        $detail->update([
            'adults_quantity' => $r['adults_quantity'],
            'kids_quantity'   => $r['kids_quantity'],
            'total'           => $newTotal,
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
    public function storeFromCart()
    {
        $user = Auth::user();
        $cart = Cart::with('items.tour')->where('user_id',$user->user_id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('admin.cart.index')->with('error','Tu carrito está vacío.');
        }

        // Validación de cupo (máx. 12 personas por tour y fecha)
        foreach ($cart->items as $item) {
            $reservadas = BookingDetail::where('tour_id',$item->tour_id)
                ->where('tour_date',$item->tour_date)
                ->sum(DB::raw('adults_quantity+kids_quantity'));

            $solicitadas = $item->adults_quantity + $item->kids_quantity;

            if (($reservadas + $solicitadas) > 12) {
                $disponibles = 12 - $reservadas;
                return redirect()->route('admin.cart.index')
                    ->with('error',"No hay suficientes espacios para '{$item->tour->name}' el día {$item->tour_date}. Disponibles: {$disponibles}.");
            }
        }

        $total_booking = $cart->items->sum(fn($item) =>
            ($item->tour->adult_price * $item->adults_quantity)
          + ($item->tour->kid_price   * $item->kids_quantity)
        );

        $booking = Booking::create([
            'user_id'           => $user->user_id,
            'booking_reference' => strtoupper(Str::random(10)),
            'booking_date'      => now(),
            'status'            => 'pending',
            'total'             => $total_booking,
            'is_active'         => true,
        ]);

        foreach ($cart->items as $item) {
            BookingDetail::create([
                'booking_id'       => $booking->booking_id,
                'tour_id'          => $item->tour_id,
                'tour_schedule_id' => $item->tour_schedule_id,
                'tour_language_id' => $item->tour_language_id,
                'tour_date'        => $item->tour_date,
                'hotel_id'         => $item->hotel_id,
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

        $cart->items()->delete();

        return redirect()->route('admin.cart.index')->with('success','Reservas generadas correctamente desde el carrito.');
    }

    /** Vista del calendario */
    public function calendar()
    {
        return view('admin.bookings.calendar');
    }

    /** Datos para FullCalendar */
    public function calendarData(Request $request)
    {
        $query = BookingDetail::with(['booking.user','booking.tour']);

        if ($request->filled('from')) {
            $query->where('tour_date','>=',$request->input('from'));
        }
        if ($request->filled('to')) {
            $query->where('tour_date','<=',$request->input('to'));
        }

        $events = $query->get()->map(fn($d) => [
            'id'      => $d->booking->booking_id,
            'title'   => "{$d->booking->user->full_name} – {$d->booking->tour->name}",
            'start'   => $d->tour_date->toDateString(),
            'status'  => $d->booking->status,
            'adults'  => $d->adults_quantity,
            'kids'    => $d->kids_quantity,
            'total'   => $d->total,
        ]);

        return response()->json($events);
    }
}
