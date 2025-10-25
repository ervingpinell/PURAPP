<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Cart;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\PromoCode;
use App\Models\Tour;
use App\Support\BookingRules;
use App\Mail\BookingCreatedMail;

use App\Services\Bookings\{BookingCreator, BookingCapacityService};

class BookingController extends Controller
{
    public function __construct(
        private BookingCreator $creator,
        private BookingCapacityService $capacity
    ) {}

    /** Checkout desde carrito (público) */
public function storeFromCart(Request $request)
{
    $user = Auth::user();

    $key = 'booking:cart:' . $user->user_id;
    if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 1)) {
        return redirect()->route('public.carts.index')
            ->with('error', __('carts.messages.cart_being_processed'));
    }
    \Illuminate\Support\Facades\RateLimiter::hit($key, 10);

    $createdBookings = DB::transaction(function () use ($user, $request) {
        $cart = \App\Models\Cart::with(['items.tour','items.schedule'])
            ->where('user_id', $user->user_id)
            ->where('is_active', true)
            ->orderByDesc('cart_id')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            throw \Illuminate\Validation\ValidationException::withMessages(['cart' => __('carts.messages.cart_empty')]);
        }
        if ($cart->isExpired()) {
            throw \Illuminate\Validation\ValidationException::withMessages(['cart' => __('carts.messages.cart_expired')]);
        }

        // Validaciones por grupo (tour+fecha+horario)
        $groups = $cart->items->groupBy(fn($i) => $i->tour_id.'_'.$i->tour_date.'_'.$i->schedule_id);
        foreach ($groups as $items) {
            $tz      = config('app.timezone', 'America/Costa_Rica');
            $minDate = \App\Support\BookingRules::earliestBookableDate();

            $first      = $items->first();
            $tour       = $first->tour;
            $tourDate   = $first->tour_date;
            $scheduleId = $first->schedule_id;

            $dt = \Carbon\Carbon::parse($tourDate, $tz)->startOfDay();
            if ($dt->lt($minDate)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'tour_date' => __("m_bookings.messages.date_no_longer_available", [
                        'date' => $tourDate, 'min' => $minDate->toDateString(),
                    ]),
                ]);
            }

            $schedule = $tour->schedules()
                ->where('schedules.schedule_id', $scheduleId)
                ->where('schedules.is_active', true)
                ->wherePivot('is_active', true)
                ->first();

            if (!$schedule) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'schedule_id' => __("carts.messages.schedule_unavailable"),
                ]);
            }

            // Capacidad real (bloqueos + confirmados + pendientes + holds de carritos)
            $remaining = $this->capacity->remainingCapacity($tour, $schedule, $tourDate, excludeBookingId: null, countHolds: true);
            $requested = $items->sum(fn($i) => (int)$i->adults_quantity + (int)$i->kids_quantity);

            if ($requested > $remaining) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'capacity' => __("m_bookings.messages.limited_seats_available", [
                        'available' => $remaining, 'tour' => $tour->name, 'date' => $tourDate
                    ]),
                ]);
            }
        }

        // Promo (primera reserva la consume)
        $promoCodeValue   = $request->input('promo_code') ?? $request->session()->get('public_cart_promo.code');
        $promoCodeToApply = null;
        if ($promoCodeValue) {
            $clean = strtoupper(trim(preg_replace('/\s+/', '', $promoCodeValue)));
            $promoCodeToApply = \App\Models\PromoCode::whereRaw("UPPER(TRIM(REPLACE(code, ' ', ''))) = ?", [$clean])
                ->where('is_used', false)
                ->first();
        }

        $created = [];
        $promoApplied = false;

        foreach ($cart->items as $item) {
            $payload = [
                'user_id'           => $user->user_id,
                'tour_id'           => $item->tour_id,
                'schedule_id'       => $item->schedule_id,
                'tour_language_id'  => $item->tour_language_id,
                'tour_date'         => $item->tour_date,
                'booking_date'      => now(),
                'hotel_id'          => $item->is_other_hotel ? null : $item->hotel_id,
                'is_other_hotel'    => (bool)$item->is_other_hotel,
                'other_hotel_name'  => $item->is_other_hotel ? $item->other_hotel_name : null,
                'adults_quantity'   => (int)$item->adults_quantity,
                'kids_quantity'     => (int)$item->kids_quantity,
                'status'            => 'pending', // público arranca en pending
                'meeting_point_id'  => $item->meeting_point_id,
                'notes'             => null,
                'promo_code'        => $promoApplied ? null : ($promoCodeToApply?->code),
            ];

            $booking = $this->creator->create($payload, validateCapacity: false);

            if (!$promoApplied && $promoCodeToApply) {
                $promoCodeToApply->redeemForBooking($booking->booking_id, $user->user_id);
                $promoApplied = true;
            }

            $created[] = $booking;
        }

        $cart->items()->delete();
        $cart->update(['is_active' => false]);
        $request->session()->forget('public_cart_promo');

        return $created;
    });

    // Correos (igual que lo tenías)
    $createdBookings = collect($createdBookings);
    $details = \App\Models\BookingDetail::with(['tour','schedule','hotel','tourLanguage','booking.user'])
        ->whereIn('booking_id', $createdBookings->pluck('booking_id'))
        ->get();

    if ($details->isNotEmpty()) {
        $byLang = $details->groupBy('tour_language_id');
        foreach ($byLang as $langId => $langDetails) {
            $firstBooking = $createdBookings->firstWhere('booking_id', $langDetails->first()->booking_id);
            \Mail::to($user->email)->queue(new \App\Mail\BookingCreatedMail($firstBooking, $langDetails));
        }
    }

    return redirect()->route('my-bookings')
        ->with('success', __('m_bookings.messages.bookings_created_from_cart'));
}

    /** Mis reservas (público) */
    public function myBookings()
    {
        $bookings = Booking::with(['user','tour','detail.hotel','detail.meetingPoint'])
            ->where('user_id', Auth::id())
            ->orderByDesc('booking_date')
            ->get();

        return view('customer.bookings.index', compact('bookings'));
    }

    /** Recibo PDF (público) */
    public function downloadReceiptPdf(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $booking->load(['detail.schedule','user']);

        $detail       = $booking->detail;
        $totalAdults  = (int)$detail->adults_quantity;
        $totalKids    = (int)$detail->kids_quantity;
        $totalPersons = $totalAdults + $totalKids;

        $client = preg_replace('/[^A-Za-z0-9_]/', '_', $booking->user->full_name ?? 'Client');
        $code   = $booking->booking_reference;

        $pdf = Pdf::loadView('admin.bookings.receipt', compact('booking','totalAdults','totalKids','totalPersons'));

        return $pdf->download("Receipt_{$client}_{$code}.pdf");
    }
}
