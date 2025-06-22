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
    public function index()
    {
        $bookings = Booking::with(['user', 'tour','detail'])
                      ->orderBy('booking_id')
                      ->get();

    return view('admin.bookings.index', compact('bookings'));
    }

    /** Crear reserva desde formulario */
    public function store(Request $request)
    {
        $v = $request->validate([
            'user_id'         => 'required|exists:users,user_id',
            'tour_id'         => 'required|exists:tours,tour_id',
            'booking_date'    => 'required|date',
            'status'          => 'required|string',
            'language'        => 'required|string',
            'adults_quantity' => 'required|integer|min:1',
            'kids_quantity'   => 'required|integer|min:0',
        ]);

        $tour = Tour::findOrFail($v['tour_id']);

        $v['total']             = ($tour->adult_price * $v['adults_quantity'])
                                + ($tour->kid_price   * $v['kids_quantity']);
        $v['booking_reference'] = strtoupper(Str::random(10));
        $v['is_active']         = true;

        // 1) Creamos la cabecera
        $booking = Booking::create($v);

        // 2) Creamos el detalle
        BookingDetail::create([
            'booking_id'       => $booking->booking_id,
            'tour_id'          => $tour->tour_id,
            'tour_schedule_id' => null,              // o la que toque
            'tour_date'        => now(),              // o tu fecha
            'tour_language_id' => 1,                  // o la que toque
            'adults_quantity'  => $v['adults_quantity'],
            'kids_quantity'    => $v['kids_quantity'],
            'adult_price'      => $tour->adult_price,
            'kid_price'        => $tour->kid_price,
            'total'            => $v['total'],
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
        // Cambia 'admin.bookings.receipt' por el path real:
        $pdf = Pdf::loadView('admin.bookingDetails.comprobante', compact('reserva'));

        $client = preg_replace('/[^A-Za-z0-9_]/', '_', $reserva->user->full_name ?? 'Client');
        $code   = $reserva->booking_reference;

        return $pdf->download("Receipt_{$client}_{$code}.pdf");
    }

    /** Generar PDF resumen de todas las reservas */
    public function generarPDF()
    {
        $reservas = Booking::with(['user','tour','detail'])->orderBy('booking_id')->get();
        $pdf = Pdf::loadView('admin.bookingDetails.pdf_resumen', compact('reservas'));
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

            Booking::create([
                'user_id'          => $user->user_id,
                'tour_id'          => $tour->tour_id,
                'booking_reference'=> strtoupper(Str::random(10)),
                'booking_date'     => now(),
                'status'           => 'pending',
                'language'         => $item->language,
                'adults_quantity'  => $item->adults_quantity,
                'kids_quantity'    => $item->kids_quantity,
                'total'            => ($tour->adult_price * $item->adults_quantity)
                                    + ($tour->kid_price   * $item->kids_quantity),
                'is_active'        => true,
            ]);
        }

        $cart->items()->delete();

        return redirect()->route('admin.cart.index')
                         ->with('success', 'Reservas generadas desde el carrito.');
    }
}
