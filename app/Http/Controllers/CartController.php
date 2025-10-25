<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Tour;
use App\Models\HotelList;
use App\Models\MeetingPoint;
use App\Models\TourExcludedDate;
use App\Models\PromoCode;
use App\Services\Bookings\BookingCapacityService;

class CartController extends Controller
{
    public function __construct(
        private BookingCapacityService $capacity
    ) {}

    /** Vista del carrito (solo autenticados) */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $cart = $user->cart()
            ->where('is_active', true)
            ->orderByDesc('cart_id')
            ->with([
                'items.tour.schedules',
                'items.tour.languages',
                'items.schedule',
                'items.language',
                'items.hotel',
                'items.meetingPoint',
            ])
            ->first();

        if (!$cart || $cart->isExpired()) {
            if ($cart) {
                $this->expireCart($cart);
            }
            $cart = Cart::create([
                'user_id'   => $user->user_id,
                'is_active' => true,
            ])->refreshExpiry((int) config('cart.expiry_minutes', 15));
        } else {
            $cart->refreshExpiry((int) config('cart.expiry_minutes', 15));
        }

        $hotels = HotelList::where('is_active', true)->orderBy('name')->get();
        $expiresAtIso = optional($cart->expires_at)->toIso8601String();

        return view('public.cart', [
            'cart'         => $cart,
            'client'       => $user,
            'hotels'       => $hotels,
            'expiresAtIso' => $expiresAtIso,
        ]);
    }

    /** Agregar ítem (solo autenticados) */
public function store(Request $request)
{
    abort_unless(Auth::check(), 403);

    $in = $request->all();
    $in['is_other_hotel'] = (bool)($in['is_other_hotel'] ?? false);

    if (array_key_exists('hotel_id', $in)) {
        $raw = $in['hotel_id'];
        if ($raw === 'other' || $raw === '__custom__' || (isset($raw) && !ctype_digit((string)$raw))) {
            if ($raw === 'other' || $raw === '__custom__') $in['is_other_hotel'] = true;
            $in['hotel_id'] = null;
        }
    } else {
        $in['hotel_id'] = null;
    }
    if ($in['is_other_hotel'] && !empty($in['other_hotel_name'])) {
        $in['hotel_id'] = null;
    }
    $request->replace($in);

    $request->validate([
        'tour_id'                 => 'required|exists:tours,tour_id',
        'tour_date'               => 'required|date|after_or_equal:today',
        'schedule_id'             => 'required|exists:schedules,schedule_id',
        'tour_language_id'        => 'required|exists:tour_languages,tour_language_id',
        'hotel_id'                => 'bail|nullable|integer|exists:hotels_list,hotel_id|exclude_if:is_other_hotel,1',
        'is_other_hotel'          => 'boolean',
        'other_hotel_name'        => 'nullable|string|max:255|required_if:is_other_hotel,1',
        'adults_quantity'         => 'required|integer|min:1',
        'kids_quantity'           => 'nullable|integer|min:0|max:2',
        'selected_meeting_point'  => 'nullable|integer|exists:meeting_points,id',
    ]);

    $user   = $request->user();
    $adults = (int)$request->adults_quantity;
    $kids   = (int)($request->kids_quantity ?? 0);
    $tour   = \App\Models\Tour::with('schedules')->findOrFail($request->tour_id);
    $expiry = (int) config('cart.expiry_minutes', 15);

    $cart = $user->cart()->where('is_active', true)->orderByDesc('cart_id')->first();
    if (!$cart || $cart->isExpired()) {
        if ($cart) $this->expireCart($cart);
        $cart = \App\Models\Cart::create(['user_id' => $user->user_id, 'is_active' => true]);
    }
    $cart->refreshExpiry($expiry);

    $schedule = $tour->schedules()
        ->where('schedules.schedule_id', $request->schedule_id)
        ->where('schedules.is_active', true)
        ->wherePivot('is_active', true)
        ->first();

    if (!$schedule) {
        return back()->withErrors(['schedule_id' => __('carts.messages.schedule_unavailable')]);
    }

    // Capacidad real (bloqueos + confirmados + pendientes + holds de carritos)
    $remaining = $this->capacity->remainingCapacity($tour, $schedule, $request->tour_date, excludeBookingId: null, countHolds: true);
    $requested = $adults + $kids;

    if ($requested > $remaining) {
        return back()->with('error', __('carts.messages.slot_full'));
    }

    $mpId = $request->integer('selected_meeting_point') ?: null;
    $mp   = $mpId ? \App\Models\MeetingPoint::find($mpId) : null;

    \App\Models\CartItem::create([
        'cart_id'          => $cart->cart_id,
        'tour_id'          => $tour->tour_id,
        'tour_date'        => $request->tour_date,
        'schedule_id'      => $request->schedule_id,
        'tour_language_id' => $request->tour_language_id,
        'hotel_id'         => $request->boolean('is_other_hotel') ? null : $request->hotel_id,
        'is_other_hotel'   => $request->boolean('is_other_hotel'),
        'other_hotel_name' => $request->boolean('is_other_hotel') ? $request->other_hotel_name : null,
        'adults_quantity'  => $adults,
        'kids_quantity'    => $kids,
        'is_active'        => true,
        'meeting_point_id'          => $mp?->id,
        'meeting_point_name'        => $mp?->name,
        'meeting_point_pickup_time' => $mp?->pickup_time,
        'meeting_point_description' => $mp?->description,
        'meeting_point_map_url'     => $mp?->map_url,
    ]);

    $cart->refreshExpiry($expiry);

    $successMessage = __('carts.messages.item_added') . ' ' .
        __('carts.messages.cart_expires_in', ['minutes' => $expiry]);

    return $request->ajax()
        ? response()->json([
            'message' => $successMessage,
            'count'   => $this->countRaw($request),
        ])
        : back()->with('success', $successMessage);
}


    /** Eliminar ítem */
    public function destroy(Request $request, $itemId)
    {
        abort_unless(Auth::check(), 403);

        $user = Auth::user();
        $cart = $user->cart()
            ->where('is_active', true)
            ->orderByDesc('cart_id')
            ->first();

        if ($cart) {
            $cart->items()->where('item_id', $itemId)->delete();
            if (!$cart->isExpired()) {
                $cart->refreshExpiry((int) config('cart.expiry_minutes', 15));
            }
        }

        return back()->with('success', __('carts.messages.item_removed'));
    }

    /** Count (header AJAX) */
    public function count(Request $request)
    {
        abort_unless(Auth::check(), 403);

        $user = $request->user();
        $cart = $user->cart()
            ->where('is_active', true)
            ->orderByDesc('cart_id')
            ->first();

        if (!$cart) {
            return response()->json(['count' => 0, 'expired' => false]);
        }

        if ($cart->isExpired()) {
            $this->expireCart($cart);
            return response()->json(['count' => 0, 'expired' => true]);
        }

        $count = $cart->items()->where('is_active', true)->count();

        return response()->json([
            'count'     => (int)$count,
            'expired'   => false,
            'remaining' => $cart->remainingSeconds(),
        ]);
    }

    /** Asientos confirmados (visores de disponibilidad) */
    public function getReserved(Request $request)
    {
        $request->validate([
            'tour_id'     => ['required','integer'],
            'schedule_id' => ['required','integer'],
            'tour_date'   => ['required','date'],
        ]);

        $reserved = DB::table('booking_details')
            ->join('bookings', 'booking_details.booking_id', '=', 'bookings.booking_id')
            ->where('booking_details.tour_id', $request->tour_id)
            ->where('booking_details.schedule_id', $request->schedule_id)
            ->where('booking_details.tour_date', $request->tour_date)
            ->where('bookings.status', 'confirmed')
            ->sum(DB::raw('COALESCE(adults_quantity,0) + COALESCE(kids_quantity,0)'));

        return response()->json(['reserved' => (int)$reserved]);
    }

    /** Expirar carrito (AJAX) */
    public function expire(Request $request)
    {
        abort_unless(Auth::check(), 403);

        $cart = $request->user()->cart()
            ->where('is_active', true)
            ->orderByDesc('cart_id')
            ->first();

        if (!$cart) {
            return response()->json(['ok' => true, 'message' => __('carts.messages.no_active_cart')], 200);
        }

        if ($cart->isExpired() || now()->greaterThanOrEqualTo($cart->expires_at)) {
            $this->expireCart($cart);
            return response()->json(['ok' => true, 'expired' => true]);
        }

        return response()->json([
            'ok'        => true,
            'expired'   => false,
            'remaining' => $cart->remainingSeconds(),
        ]);
    }

    /** Renovar expiración (AJAX) */
    public function refreshExpiry(Request $request)
    {
        abort_unless(Auth::check(), 403);

        $cart = $request->user()->cart()
            ->where('is_active', true)
            ->orderByDesc('cart_id')
            ->first();

        if (!$cart || $cart->isExpired()) {
            if ($cart) $this->expireCart($cart);
            $cart = Cart::create(['user_id' => $request->user()->user_id, 'is_active' => true]);
        }

        $cart->refreshExpiry((int) config('cart.expiry_minutes', 15));

        return response()->json([
            'ok'         => true,
            'expires_at' => $cart->expires_at->toIso8601String(),
            'remaining'  => $cart->remainingSeconds(),
            'message'    => __('carts.messages.cart_refreshed'),
        ]);
    }

    /** Aplicar promoción al carrito (público) */
    public function applyPromo(Request $request)
    {
        $request->validate(['code' => ['required','string','max:50']]);

        $user = $request->user();
        if (!$user) {
            return response()->json(['ok' => false, 'message' => __('carts.messages.no_active_cart')], 401);
        }

        $cart = $user->cart()
            ->where('is_active', true)
            ->orderByDesc('cart_id')
            ->with('items.tour')
            ->first();

        if (!$cart || !$cart->items->count()) {
            return response()->json(['ok' => false, 'message' => __('carts.messages.cart_empty')], 422);
        }
        if ($cart->isExpired()) {
            $this->expireCart($cart);
            return response()->json(['ok' => false, 'message' => __('carts.messages.cart_expired')], 422);
        }

        $code  = strtoupper(trim($request->code));
        $promo = PromoCode::whereRaw('UPPER(code) = ?', [$code])->first();

        if (!$promo) {
            return response()->json(['ok' => false, 'message' => __('carts.messages.invalid_code')], 422);
        }
        if (!$promo->isValidToday()) {
            return response()->json(['ok' => false, 'message' => __('carts.messages.code_expired_or_not_yet')], 422);
        }
        if (!$promo->hasRemainingUses()) {
            return response()->json(['ok' => false, 'message' => __('carts.messages.code_limit_reached')], 422);
        }
        if ($promo->is_used) {
            return response()->json(['ok' => false, 'message' => __('carts.messages.code_already_used')], 422);
        }

        // Subtotal del carrito
        $subtotal = (float)$cart->items->sum(function ($it) {
            $ap = (float)($it->tour->adult_price ?? 0);
            $kp = (float)($it->tour->kid_price   ?? 0);
            $aq = (int)($it->adults_quantity ?? 0);
            $kq = (int)($it->kids_quantity   ?? 0);
            return ($ap * $aq) + ($kp * $kq);
        });

        $discountFixed    = max(0.0, (float)($promo->discount_amount ?? 0));
        $discountPerc     = max(0.0, (float)($promo->discount_percent ?? 0));
        $discountFromPerc = round($subtotal * ($discountPerc / 100), 2);
        $adjustment       = round($discountFixed + $discountFromPerc, 2);

        $operation  = $promo->operation === 'add' ? 'add' : 'subtract';
        $sign       = $operation === 'add' ? +1 : -1;
        $newTotal   = max(0, round($subtotal + ($sign * $adjustment), 2));

        // Guardar en sesión pública
        session([
            'public_cart_promo' => [
                'code'       => $promo->code,
                'operation'  => $operation,
                'amount'     => $discountFixed,
                'percent'    => $discountPerc,
                'adjustment' => $adjustment,
                'subtotal'   => $subtotal,
                'new_total'  => $newTotal,
                'applied_at' => now()->toISOString(),
            ]
        ]);

        // Refresca expiración del carrito
        if ($cart && $cart->is_active && !$cart->isExpired()) {
            $cart->refreshExpiry((int) config('cart.expiry_minutes', 15));
        }

        $message = $operation === 'add'
            ? __('carts.messages.code_applied').' ('.__('surcharge').')'
            : __('carts.messages.code_applied');

        return response()->json([
            'ok'        => true,
            'message'   => $message,
            'code'      => $promo->code,
            'operation' => $operation,
            'adjustment'=> number_format($adjustment, 2),
            'subtotal'  => number_format($subtotal, 2),
            'new_total' => number_format($newTotal, 2),
        ]);
    }

    /** Quitar promoción del carrito (público) */
    public function removePromo(Request $request)
    {
        $request->session()->forget('public_cart_promo');
        return response()->json(['ok' => true, 'message' => __('carts.messages.code_removed')]);
    }

    /** Interno: expira y limpia */
    private function expireCart(?Cart $cart): void
    {
        if (!$cart) return;

        DB::transaction(function () use ($cart) {
            $cart->items()->delete();
            $cart->forceFill([
                'is_active'  => false,
                'expires_at' => now(),
            ])->save();
        });
    }

    /** Interno: conteo rápido */
    protected function countRaw(Request $request): int
    {
        $cart = Cart::withCount('items')
            ->where('user_id', Auth::id())
            ->where('is_active', true)
            ->orderByDesc('cart_id')
            ->first();
        return (int) ($cart->items_count ?? 0);
    }
}
