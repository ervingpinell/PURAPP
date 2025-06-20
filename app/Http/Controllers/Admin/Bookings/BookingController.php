<?php

namespace App\Http\Controllers\Admin\Bookings;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Tour;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::with('user')->get();
        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,user_id',
            'tour_id' => 'required|integer|exists:tours,tour_id',
            'booking_date' => 'required|date',
            'status' => 'required|string',
            'language' => 'required|string',
            'adults_quantity' => 'required|integer|min:1',
            'kids_quantity' => 'required|integer|min:0',
        ]);

        $tour = Tour::findOrFail($validated['tour_id']);

        $validated['total'] = ($tour->adult_price * $validated['adults_quantity']) +
                              ($tour->kid_price * $validated['kids_quantity']);

        $validated['booking_reference'] = strtoupper(Str::random(10));
        $validated['is_active'] = true;

        Booking::create($validated);

        return redirect()->back()->with('success', 'Booking successfully created.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'adults_quantity' => 'required|integer|min:1',
            'kids_quantity' => 'required|integer|min:0',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $booking = Booking::findOrFail($id);
        $tour = $booking->tour;

        $validated['total'] = ($tour->adult_price * $validated['adults_quantity']) +
                              ($tour->kid_price * $validated['kids_quantity']);

        $booking->update($validated);

        return redirect()->back()->with('success', 'Booking updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return redirect()->back()->with('success', 'Booking deleted successfully.');
    }

    /**
     * Generate PDF summary.
     */
    public function generatePDF()
    {
        $bookings = Booking::with('user')->get();
        $pdf = Pdf::loadView('admin.bookings.summary_pdf', compact('bookings'));
        return $pdf->download('booking_report.pdf');
    }

    /**
     * Generate PDF receipt.
     */
    public function generateReceipt(Booking $booking)
    {
        try {
            $pdf = Pdf::loadView('admin.bookings.receipt', compact('booking'));
            $clientName = preg_replace('/[^A-Za-z0-9_]/', '_', $booking->user->full_name ?? 'Client');
            $code = $booking->booking_reference ?? $booking->booking_id;
            $fileName = "Receipt_{$clientName}_GV-{$code}.pdf";

            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'PDF generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Store booking from cart.
     */
    public function storeFromCart()
    {
        $user = Auth::user();
        $cart = $user->cart()->with('items')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('admin.cart.index')->with('error', 'Tu carrito está vacío.');
        }

        foreach ($cart->items as $item) {
            Booking::create([
                'user_id' => $user->user_id,
                'booking_reference' => strtoupper(Str::random(10)),
                'booking_date' => now(),
                'status' => 'Pending',
                'total' => ($item->adults_quantity * $item->adult_price) + ($item->kids_quantity * $item->kid_price),
                'is_active' => true,
            ]);
        }

        // ✅ Elimina completamente los ítems del carrito
        $cart->items()->delete();

        return redirect()->route('admin.cart.index')->with('success', 'Solicitud de reserva enviada correctamente.');
    }

}
