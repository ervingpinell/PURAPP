<?php

namespace App\Http\Controllers\Admin\Bookings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Booking;
use App\Models\Tour;
use App\Models\BookingDetail;

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

        $bookings = Booking::with(['user','detail'])
            ->join('users','bookings.user_id','users.user_id')
            ->select('bookings.*')
            ->orderBy($sort == 'user' ? 'users.full_name' : $sort, $direction)
            ->paginate(10)
            ->appends(compact('sort','direction'));

        return view('admin.bookings.index', compact('bookings','sort','direction'));
    }


    /** Crear reserva desde formulario */
    public function store(Request $request)
    {
        $v = $request->validate([
            'user_id'         => 'required|exists:users,user_id',
            'tour_id'         => 'required|exists:tours,tour_id',
            'booking_date'    => 'required|date',
            'status'            => 'required|in:pending,confirmed,cancelled',
            'tour_language_id' => 'required|exists:tour_languages,tour_language_id',
            'adults_quantity'   => 'required|integer|min:1',
            'kids_quantity' => 'required|integer|min:0|max:2',
        ]);

        $tour = Tour::findOrFail($v['tour_id']);

        $v['total']             = ($tour->adult_price * $v['adults_quantity'])
                                + ($tour->kid_price   * $v['kids_quantity']);
        $v['booking_reference'] = strtoupper(Str::random(10));
        $v['is_active']         = true;

        // 1) Creamos la cabecera
        $booking = Booking::create([
            'user_id'           => $v['user_id'],
            'tour_id'           => $v['tour_id'],
            'tour_language_id'  => $v['tour_language_id'],
            'booking_reference' => strtoupper(Str::random(10)),
            'booking_date'      => $v['booking_date'],
            'status'            => $v['status'],
            'total'             => ($tour->adult_price * $request->input('adults_quantity'))
                                + ($tour->kid_price   * $request->input('kids_quantity')),
            'is_active'         => true,
        ]);

        // 2) Creamos el detalle
        BookingDetail::create([
            'booking_id'       => $booking->booking_id,
            'tour_id'          => $tour->tour_id,
            'tour_date'        => $v['booking_date'],
            'tour_language_id' => $v['tour_language_id'],
            'adults_quantity'  => $request->input('adults_quantity'),
            'kids_quantity'    => $request->input('kids_quantity'),
            'adult_price'      => $tour->adult_price,
            'kid_price'        => $tour->kid_price,
            'total'            => $booking->total,
            'is_active'        => true,
        ]);


        return redirect()
        ->route('admin.reservas.index')
        ->with('success', 'Reserva actualizada correctamente.');
    }

    /** Actualizar reserva existente */
    public function update(Request $request, $id)
    {
        $r = $request->validate([
            'adults_quantity' => 'required|integer|min:1',
            'kids_quantity'   => 'required|integer|min:0',
            'status'          => 'required|string',
            'notes'           => 'nullable|string',
        ]);

        // 1) Cabecera
        $booking = Booking::findOrFail($id);
        $booking->update([
            'status' => $r['status'],
            'notes'  => $r['notes'] ?? null,
        ]);

        // 2) Detalle
        $detail = $booking->details()->firstOrFail();
        $newTotal = ($detail->adult_price * $r['adults_quantity'])
                + ($detail->kid_price   * $r['kids_quantity']);

        $detail->update([
            'adults_quantity' => $r['adults_quantity'],
            'kids_quantity'   => $r['kids_quantity'],
            'total'           => $newTotal,
        ]);

        // 3) Si quieres mantener el total en la cabecera:
        $booking->update(['total' => $newTotal]);

        return redirect()
        ->route('admin.reservas.index')
        ->with('success', 'Reserva actualizada correctamente.');
    }



    /** Eliminar reserva */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return redirect()->back()->with('success', 'Reserva eliminada correctamente.');
    }

    /** Generar comprobante individual */
    public function generarComprobante(Booking $reserva)
    {
        // Recuperamos el detalle
        $detalle      = $reserva->detail;
        $totalAdults  = $detalle->adults_quantity;
        $totalKids    = $detalle->kids_quantity;
        $totalPersons = $totalAdults + $totalKids;

        $client = preg_replace('/[^A-Za-z0-9_]/', '_', $reserva->user->full_name ?? 'Client');
        $code   = $reserva->booking_reference;

        $pdf = Pdf::loadView(
            'admin.bookingDetails.comprobante',
            compact('reserva','totalAdults','totalKids','totalPersons')
        );

        return $pdf->download("Receipt_{$client}_{$code}.pdf");
    }

    /** Generar PDF resumen de todas las reservas */
    public function generarPDF()
    {
        $reservas = Booking::with(['user','tour','detail'])
                           ->orderBy('booking_id')
                           ->get();

        // Totales globales
        $totalAdults   = $reservas->sum(fn($b) => $b->detail->adults_quantity);
        $totalKids     = $reservas->sum(fn($b) => $b->detail->kids_quantity);
        $totalPersons  = $totalAdults + $totalKids;

        $pdf = Pdf::loadView(
            'admin.bookingDetails.pdf_resumen',
            compact('reservas','totalAdults','totalKids','totalPersons')
        );

        return $pdf->download('booking_report.pdf');
    }


    /** Crear reservas a partir del carrito del usuario */
    public function storeFromCart()
    {
        $user = Auth::user();
        $cart = $user->cart()->with('items')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('admin.cart.index')
                             ->with('error', 'Tu carrito está vacío.');
        }

        foreach ($cart->items as $item) {
            $tour = $item->tour; // asume relación en CartItem

            $booking = Booking::create([
                'user_id'           => $user->user_id,
                'tour_id'           => $tour->tour_id,
                'tour_language_id'  => $item->tour_language_id,    // <- ID, no todo el objeto
                'booking_reference' => strtoupper(Str::random(10)),
                'booking_date'      => now(),
                'status'            => 'pending',
                'total'             => ($tour->adult_price * $item->adults_quantity)
                                    + ($tour->kid_price   * $item->kids_quantity),
                'is_active'         => true,
            ]);

            //creacion del detalle
            BookingDetail::create([
                'booking_id'       => $booking->booking_id,
                'tour_id'          => $tour->tour_id,
                'tour_schedule_id' => null,  // o el correspondiente
                'tour_date'        => $item->tour_date,   // si lo guardas en cart_item
                'tour_language_id' => $item->tour_language_id,
                'adults_quantity'  => $item->adults_quantity,
                'kids_quantity'    => $item->kids_quantity,
                'adult_price'      => $tour->adult_price,
                'kid_price'        => $tour->kid_price,
                'total'            => ($tour->adult_price * $item->adults_quantity)
                                    + ($tour->kid_price   * $item->kids_quantity),
                'is_active'        => true,
            ]);
        }

        $cart->items()->delete();

        return redirect()->route('admin.cart.index')
                         ->with('success', 'Reservas generadas desde el carrito.');
    }

    //Calendario
    /** Renderiza la vista del calendario */
    public function calendar()
    {
        return view('admin.bookings.calendar');
    }

    public function calendarData(Request $request)
    {
        $query = BookingDetail::with(['booking.user','booking.tour']);

        if ($request->filled('from')) {
            $query->where('tour_date', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->where('tour_date', '<=', $request->input('to'));
        }

        $events = $query->get()->map(fn(BookingDetail $d) => [
            'id'     => $d->booking->booking_id,
            'title'  => "{$d->booking->user->full_name} – {$d->booking->tour->name}",
            'start'  => $d->tour_date->toDateString(),
            'status' => $d->booking->status,
            'extendedProps' => [
                'adults' => $d->adults_quantity,
                'kids'   => $d->kids_quantity,
                'total'  => $d->total,
            ],
        ]);

        return response()->json($events);
    }

    /**
 * Mostrar formulario de edición de una reserva.
 */
    public function edit($reservaId, Request $request)
    {
        $booking  = Booking::with('detail','user','tour')->findOrFail($reservaId);
        $statuses = ['pending' => 'Pending', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled'];

        // Solo para peticiones AJAX devolvemos la vista parcial
        if ($request->ajax()) {
            return view('admin.bookings.partials.edit-form', compact('booking','statuses'));
        }

        // Si alguien entra directo por URL, rediriges
        return redirect()->route('admin.reservas.index');
    }


}
