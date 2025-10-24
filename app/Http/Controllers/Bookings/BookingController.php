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

class BookingController extends Controller
{
    /** Store bookings from user cart (public) */
    public function storeFromCart(Request $request)
    {
        $user = Auth::user();

        $key = 'booking:cart:' . $user->user_id;
        if (RateLimiter::tooManyAttempts($key, 1)) {
            return redirect()->route('public.carts.index')
                ->with('error', __('carts.messages.cart_being_processed'));
        }
        RateLimiter::hit($key, 10);

        $createdBookings = DB::transaction(function () use ($user, $request) {
            // 🔧 FIX: Buscar el carrito activo más reciente
            $cart = Cart::with(['items.tour', 'items.schedule'])
                ->where('user_id', $user->user_id)
                ->where('is_active', true)
                ->orderByDesc('cart_id')
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => __('carts.messages.cart_empty'),
                ]);
            }

            // 🔧 FIX: Verificar si el carrito está expirado
            if ($cart->isExpired()) {
                throw ValidationException::withMessages([
                    'cart' => __('carts.messages.cart_expired'),
                ]);
            }

            // Group items by tour/date/schedule for validation
            $groups = $cart->items->groupBy(fn ($item) => $item->tour_id . '_' . $item->tour_date . '_' . $item->schedule_id);

            foreach ($groups as $items) {
                $tz      = config('app.timezone', 'America/Costa_Rica');
                $minDate = BookingRules::earliestBookableDate();

                $first      = $items->first();
                $tour       = $first->tour;
                $tourDate   = $first->tour_date;
                $scheduleId = $first->schedule_id;

                $dt = Carbon::parse($tourDate, $tz)->startOfDay();
                if ($dt->lt($minDate)) {
                    throw ValidationException::withMessages([
                        'tour_date' => __("bookings.messages.date_no_longer_available", [
                            'date' => $tourDate,
                            'min'  => $minDate->toDateString(),
                        ]),
                    ]);
                }

                $schedule = $tour->schedules()
                    ->where('schedules.schedule_id', $scheduleId)
                    ->where('schedules.is_active', true)
                    ->wherePivot('is_active', true)
                    ->first();

                if (!$schedule) {
                    throw ValidationException::withMessages([
                        'schedule_id' => __("carts.messages.schedule_unavailable"),
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
                        'tour_date' => __("carts.messages.blocked_date_for_tour", [
                            'date' => $tourDate,
                            'tour' => $tour->name
                        ]),
                    ]);
                }

                $reserved = BookingDetail::where('tour_id', $tour->tour_id)
                    ->where('tour_date', $tourDate)
                    ->where('schedule_id', $scheduleId)
                    ->sum(DB::raw('adults_quantity + kids_quantity'));

                $requested = $items->sum(fn ($i) => $i->adults_quantity + $i->kids_quantity);

                if ($reserved + $requested > $schedule->max_capacity) {
                    $available = $schedule->max_capacity - $reserved;
                    throw ValidationException::withMessages([
                        'capacity' => __("bookings.messages.limited_seats_available", [
                            'available' => $available,
                            'tour'      => $tour->name,
                            'date'      => $tourDate,
                        ]),
                    ]);
                }
            }

            // Promo code (cart-level, applied to the first booking only)
            $promoCode = null;
            $promoCodeValue = $request->input('promo_code');
            if ($promoCodeValue) {
                $cleanCode = strtoupper(trim(preg_replace('/\s+/', '', $promoCodeValue)));
                $promoCode = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code, ' ', ''))) = ?", [$cleanCode])
                    ->where('is_used', false)
                    ->first();
            }

            $created = [];
            $promoApplied = false;

            foreach ($cart->items as $item) {
                $tour       = $item->tour;
                $adultPrice = $tour->adult_price;
                $kidPrice   = $tour->kid_price;
                $baseTotal  = ($adultPrice * $item->adults_quantity) + ($kidPrice * $item->kids_quantity);
                $total      = $baseTotal;

                if ($promoCode && !$promoApplied) {
                    $op = $promoCode->operation === 'add' ? 'add' : 'subtract';
                    $delta = 0.0;

                    if ($promoCode->discount_amount) {
                        $delta = (float) $promoCode->discount_amount;
                    } elseif ($promoCode->discount_percent) {
                        $delta = round($baseTotal * ((float) $promoCode->discount_percent / 100), 2);
                    }

                    $total = $op === 'add'
                        ? round($baseTotal + $delta, 2)
                        : max(0, round($baseTotal - $delta, 2));
                }

                // Header
                $booking = Booking::create([
                    'user_id'            => $user->user_id,
                    'tour_id'            => $item->tour_id,
                    'tour_language_id'   => $item->tour_language_id,
                    'booking_reference'  => strtoupper(Str::random(10)),
                    'booking_date'       => now(),
                    'status'             => 'pending',
                    'total'              => $total,
                    'is_active'          => true,
                    'tour_name_snapshot' => $tour->name,
                ]);

                // Detail
                BookingDetail::create([
                    'booking_id'       => $booking->booking_id,
                    'tour_id'          => $item->tour_id,
                    'schedule_id'      => $item->schedule_id,
                    'tour_language_id' => $item->tour_language_id,
                    'tour_date'        => $item->tour_date,
                    'hotel_id'         => $item->is_other_hotel ? null : $item->hotel_id,
                    'is_other_hotel'   => $item->is_other_hotel,
                    'other_hotel_name' => $item->is_other_hotel ? $item->other_hotel_name : null,
                    'adults_quantity'  => $item->adults_quantity,
                    'kids_quantity'    => $item->kids_quantity,
                    'adult_price'      => $adultPrice,
                    'kid_price'        => $kidPrice,
                    'total'            => $total,
                    'is_active'        => true,

                    'meeting_point_id'             => $item->meeting_point_id,
                    'meeting_point_name'           => $item->meeting_point_name,
                    'meeting_point_pickup_time'    => $item->meeting_point_pickup_time,
                    'meeting_point_description'    => $item->meeting_point_description,
                    'meeting_point_map_url'        => $item->meeting_point_map_url,

                    'tour_name_snapshot'           => $tour->name,
                ]);

                if ($promoCode && !$promoApplied) {
                    $promoCode->redeemForBooking($booking->booking_id, optional($request->user())->user_id);
                    $promoApplied = true;
                }

                $created[] = $booking;
            }

            // Empty cart and mark as inactive
            $cart->items()->delete();
            $cart->update(['is_active' => false]);

            return $created;
        });

        // Email confirmation (group items by language like before)
        $createdBookings = collect($createdBookings);

        $details = BookingDetail::with(['tour', 'schedule', 'hotel', 'tourLanguage', 'booking.user'])
            ->whereIn('booking_id', $createdBookings->pluck('booking_id'))
            ->get();

        if ($details->isNotEmpty()) {
            $byLang = $details->groupBy('tour_language_id');
            foreach ($byLang as $langId => $langDetails) {
                $firstBooking = $createdBookings->firstWhere('booking_id', $langDetails->first()->booking_id);
                Mail::to($user->email)->queue(new BookingCreatedMail($firstBooking, $langDetails));
            }
        }

        return redirect()->route('my-bookings')
            ->with('success', __('bookings.messages.bookings_created_from_cart'));
    }

    /** Show customer bookings */
    public function myBookings()
    {
        $bookings = Booking::with(['user', 'tour', 'detail.hotel', 'detail.meetingPoint'])
            ->where('user_id', Auth::id())
            ->orderByDesc('booking_date')
            ->get();

        return view('customer.bookings.index', compact('bookings'));
    }

    /** Download customer receipt PDF (public) */
    public function downloadReceiptPdf(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $booking->load(['detail.schedule', 'user']);

        $detail       = $booking->detail;
        $totalAdults  = $detail->adults_quantity;
        $totalKids    = $detail->kids_quantity;
        $totalPersons = $totalAdults + $totalKids;

        $client = preg_replace('/[^A-Za-z0-9_]/', '_', $booking->user->full_name ?? 'Client');
        $code   = $booking->booking_reference;

        $pdf = Pdf::loadView('admin.bookings.receipt', compact('booking', 'totalAdults', 'totalKids', 'totalPersons'));

        return $pdf->download("Receipt_{$client}_{$code}.pdf");
    }
}
