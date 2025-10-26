<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\HotelList;
use App\Models\MeetingPoint;
use App\Models\PromoCode;
use App\Services\Bookings\BookingCapacityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function __construct(private BookingCapacityService $capacity) {}

    /* ======================================================
     *  Cart view (auth only)
     * ====================================================== */
    public function index(Request $request)
    {
        if (!Auth::check()) return redirect()->route('login');

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
                // 👇 Eager-load translations para que el Blade vea el nombre/descr localizados
                'items.meetingPoint.translations',
            ])
            ->first();

        if (!$cart || $cart->isExpired()) {
            if ($cart) $this->expireCart($cart);
            $cart = Cart::create(['user_id' => $user->user_id, 'is_active' => true])
                ->refreshExpiry((int) config('cart.expiry_minutes', 15));
        } else {
            $cart->refreshExpiry((int) config('cart.expiry_minutes', 15));
        }

        return view('public.cart', [
            'cart'           => $cart,
            'client'         => $user,
            'hotels'         => HotelList::where('is_active', true)->orderBy('name')->get(),
            // 👇 También la lista del selector con translations
            'meetingPoints'  => MeetingPoint::where('is_active', true)
                ->with('translations')
                ->orderByRaw('sort_order IS NULL, sort_order ASC')
                ->orderBy('name', 'asc')
                ->get(),
            'expiresAtIso'   => optional($cart->expires_at)->toIso8601String(),
        ]);
    }

    /* ======================================================
     *  Add item
     * ====================================================== */
    public function store(Request $request)
    {
        abort_unless(Auth::check(), 403);

        $this->normalizeHotelInput($request);

        $request->validate([
            'tour_id'                => 'required|exists:tours,tour_id',
            'tour_date'              => 'required|date|after_or_equal:today',
            'schedule_id'            => 'required|exists:schedules,schedule_id',
            'tour_language_id'       => 'required|exists:tour_languages,tour_language_id',
            'hotel_id'               => 'bail|nullable|integer|exists:hotels_list,hotel_id|exclude_if:is_other_hotel,1',
            'is_other_hotel'         => 'boolean',
            'other_hotel_name'       => 'nullable|string|max:255|required_if:is_other_hotel,1',
            'adults_quantity'        => 'required|integer|min:1',
            'kids_quantity'          => 'nullable|integer|min:0|max:12',
            'selected_meeting_point' => 'nullable|integer|exists:meeting_points,id',
        ]);

        $user     = $request->user();
        $adults   = (int) $request->adults_quantity;
        $kids     = (int) ($request->kids_quantity ?? 0);
        $tourDate = $request->tour_date;
        $expiry   = (int) config('cart.expiry_minutes', 15);

        $tour     = \App\Models\Tour::with('schedules')->findOrFail((int) $request->tour_id);
        $schedule = $this->findValidScheduleOrFail($tour, (int) $request->schedule_id);

        if ($this->capacity->isDateBlocked($tour, $schedule, $tourDate)) {
            return $this->backOrJsonError($request, __('carts.messages.capacity_full'));
        }

        $requested = $adults + $kids;
        $remaining = $this->capacity->remainingCapacity($tour, $schedule, $tourDate, excludeBookingId: null, countHolds: true);

        if ($requested > $remaining) {
            $msg = $remaining <= 0
                ? __('carts.messages.capacity_full')
                : __('carts.messages.limited_seats_available', [
                    'available' => $remaining,
                    'tour'      => $tour->name,
                    'date'      => Carbon::parse($tourDate)->format('d/m/Y'),
                ]);
            return $this->backOrJsonError($request, $msg);
        }

        // Pickup resolution (mutually exclusive)
        [$hotelId, $isOther, $other, $mpId] = $this->resolvePickupForStore($request);

        $cart = $this->activeCartOrCreate($user->user_id)->refreshExpiry($expiry);

        CartItem::create([
            'cart_id'          => $cart->cart_id,
            'tour_id'          => (int) $tour->tour_id,
            'tour_date'        => $tourDate,
            'schedule_id'      => (int) $request->schedule_id,
            'tour_language_id' => (int) $request->tour_language_id,
            'hotel_id'         => $hotelId,
            'is_other_hotel'   => $isOther,
            'other_hotel_name' => $other,
            'adults_quantity'  => $adults,
            'kids_quantity'    => $kids,
            'is_active'        => true,
            'meeting_point_id' => $mpId,
        ]);

        $cart->refreshExpiry($expiry);

        $successMessage = __('carts.messages.item_added') . ' ' .
            __('carts.messages.cart_expires_in', ['minutes' => $expiry]);

        return $request->ajax()
            ? response()->json(['ok' => true, 'message' => $successMessage, 'count' => $this->countRaw($request)])
            : back()->with('success', $successMessage);
    }

    /* ======================================================
     *  Remove item
     * ====================================================== */
    public function destroy(Request $request, $itemId)
    {
        abort_unless(Auth::check(), 403);

        $cart = $this->activeCartOf(Auth::user());
        if ($cart) {
            $cart->items()->where('item_id', $itemId)->delete();
            if (!$cart->isExpired()) $cart->refreshExpiry((int) config('cart.expiry_minutes', 15));
        }

        return back()->with('success', __('carts.messages.item_removed'));
    }

    /* ======================================================
     *  Quick count (AJAX)
     * ====================================================== */
    public function count(Request $request)
    {
        abort_unless(Auth::check(), 403);

        $cart = $this->activeCartOf($request->user());
        if (!$cart) return response()->json(['count' => 0, 'expired' => false]);

        if ($cart->isExpired()) {
            $this->expireCart($cart);
            return response()->json(['count' => 0, 'expired' => true]);
        }

        return response()->json([
            'count'     => (int) $cart->items()->where('is_active', true)->count(),
            'expired'   => false,
            'remaining' => $cart->remainingSeconds(),
        ]);
    }

    /* ======================================================
     *  Expire cart (AJAX)
     * ====================================================== */
    public function expire(Request $request)
    {
        abort_unless(Auth::check(), 403);

        $cart = $this->activeCartOf($request->user());
        if (!$cart) return response()->json(['ok' => true, 'message' => __('carts.messages.no_active_cart')]);

        if ($cart->isExpired() || now()->greaterThanOrEqualTo($cart->expires_at)) {
            $this->expireCart($cart);
            return response()->json(['ok' => true, 'expired' => true]);
        }

        return response()->json(['ok' => true, 'expired' => false, 'remaining' => $cart->remainingSeconds()]);
    }

    /* ======================================================
     *  Refresh expiry (AJAX)
     * ====================================================== */
    public function refreshExpiry(Request $request)
    {
        abort_unless(Auth::check(), 403);

        $cart = $this->activeCartOrCreate($request->user()->user_id);
        $cart->refreshExpiry((int) config('cart.expiry_minutes', 15));

        return response()->json([
            'ok'         => true,
            'expires_at' => $cart->expires_at->toIso8601String(),
            'remaining'  => $cart->remainingSeconds(),
            'message'    => __('carts.messages.cart_refreshed'),
        ]);
    }

    /* ======================================================
     *  Apply / remove promo
     * ====================================================== */
    public function applyPromo(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'max:50']]);

        $user = $request->user();
        if (!$user) return response()->json(['ok' => false, 'message' => __('carts.messages.no_active_cart')], 401);

        $cart = $user->cart()->where('is_active', true)->orderByDesc('cart_id')->with('items.tour')->first();
        if (!$cart || !$cart->items->count()) return response()->json(['ok' => false, 'message' => __('carts.messages.cart_empty')], 422);
        if ($cart->isExpired()) {
            $this->expireCart($cart);
            return response()->json(['ok' => false, 'message' => __('carts.messages.cart_expired')], 422);
        }

        $code  = strtoupper(trim($request->code));
        $promo = PromoCode::whereRaw('UPPER(code) = ?', [$code])->first();

        if (!$promo)                         return response()->json(['ok' => false, 'message' => __('carts.messages.invalid_code')], 422);
        if (!$promo->isValidToday())         return response()->json(['ok' => false, 'message' => __('carts.messages.code_expired_or_not_yet')], 422);
        if (!$promo->hasRemainingUses())     return response()->json(['ok' => false, 'message' => __('carts.messages.code_limit_reached')], 422);
        if ($promo->is_used)                 return response()->json(['ok' => false, 'message' => __('carts.messages.code_already_used')], 422);

        $subtotal = (float) $cart->items->sum(function ($it) {
            $ap = (float)($it->tour->adult_price ?? 0);
            $kp = (float)($it->tour->kid_price ?? 0);
            return ($ap * $it->adults_quantity) + ($kp * $it->kids_quantity);
        });

        $fixed    = max(0.0, (float)($promo->discount_amount ?? 0));
        $perc     = max(0.0, (float)($promo->discount_percent ?? 0));
        $fromPerc = round($subtotal * ($perc / 100), 2);
        $adj      = round($fixed + $fromPerc, 2);

        $op     = $promo->operation === 'add' ? 'add' : 'subtract';
        $sign   = $op === 'add' ? +1 : -1;
        $total  = max(0, round($subtotal + ($sign * $adj), 2));

        session([
            'public_cart_promo' => [
                'code'       => $promo->code,
                'operation'  => $op,
                'amount'     => $fixed,
                'percent'    => $perc,
                'adjustment' => $adj,
                'subtotal'   => $subtotal,
                'new_total'  => $total,
                'applied_at' => now()->toISOString(),
            ]
        ]);

        $cart->refreshExpiry((int) config('cart.expiry_minutes', 15));

        $message = $op === 'add'
            ? __('carts.messages.code_applied') . ' (' . __('surcharge') . ')'
            : __('carts.messages.code_applied');

        return response()->json([
            'ok'        => true,
            'message'   => $message,
            'code'      => $promo->code,
            'operation' => $op,
            'adjustment'=> number_format($adj, 2),
            'subtotal'  => number_format($subtotal, 2),
            'new_total' => number_format($total, 2),
        ]);
    }

    public function removePromo(Request $request)
    {
        $request->session()->forget('public_cart_promo');
        return response()->json(['ok' => true, 'message' => __('carts.messages.code_removed')]);
    }

    /* ================= Internals ================ */

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

    protected function countRaw(Request $request): int
    {
        $cart = Cart::withCount('items')
            ->where('user_id', Auth::id())
            ->where('is_active', true)
            ->orderByDesc('cart_id')
            ->first();

        return (int) ($cart->items_count ?? 0);
    }

    /* ======================================================
     *  Update item (cart edit)
     * ====================================================== */
    public function update(Request $request, $itemId)
    {
        abort_unless(Auth::check(), 403);

        $request->validate([
            'tour_date'        => 'required|date|after_or_equal:today',
            'schedule_id'      => 'required|exists:schedules,schedule_id',
            'tour_language_id' => 'required|exists:tour_languages,tour_language_id',
            'adults_quantity'  => 'required|integer|min:1',
            'kids_quantity'    => 'nullable|integer|min:0|max:12',
            'hotel_id'         => 'nullable|integer|exists:hotels_list,hotel_id',
            'is_other_hotel'   => 'boolean',
            'other_hotel_name' => 'nullable|string|max:255|required_if:is_other_hotel,1',
            'meeting_point_id' => 'nullable|integer|exists:meeting_points,id',
        ]);

        $cart = $this->activeCartOf($request->user(), withTourSchedules: true);
        if (!$cart || $cart->isExpired()) return back()->with('error', __('carts.messages.cart_expired'));

        $item = $cart->items()->where('item_id', $itemId)->first();
        if (!$item) return back()->with('error', __('carts.messages.item_not_found'));

        $tour     = $item->tour;
        $schedule = $this->findValidScheduleOrFail($tour, (int) $request->schedule_id);

        $tourDate  = $request->tour_date;
        $adults    = (int)$request->adults_quantity;
        $kids      = (int)($request->kids_quantity ?? 0);
        $requested = $adults + $kids;

        if ($this->capacity->isDateBlocked($tour, $schedule, $tourDate)) {
            return back()->with('error', __('carts.messages.date_no_longer_available', [
                'date' => Carbon::parse($tourDate)->format('d/m/Y'), 'min' => 1
            ]));
        }

        $max       = $this->capacity->resolveMaxCapacity($schedule, $tour);
        $confirmed = $this->capacity->confirmedPaxFor($tourDate, (int)$schedule->schedule_id, null, (int)$tour->tour_id);
        $held      = $this->capacity->heldPaxInActiveCarts($tourDate, (int)$schedule->schedule_id, (int)$tour->tour_id);
        $remaining = max(0, (int)$max - (int)$confirmed - (int)$held + ($item->adults_quantity + $item->kids_quantity));

        if ($requested > $remaining) {
            $msg = $remaining <= 0
                ? __('carts.messages.slot_full')
                : __('carts.messages.limited_seats_available', [
                    'available' => $remaining,
                    'tour'      => $tour->name,
                    'date'      => Carbon::parse($tourDate)->format('d/m/Y'),
                ]);
            return back()->with('error', $msg);
        }

        // Pickup resolution (mutually exclusive)
        [$hotelId, $isOther, $other, $mpId] = $this->resolvePickupForUpdate($request);

        // Persist
        $item->fill([
            'tour_date'        => $tourDate,
            'schedule_id'      => (int)$schedule->schedule_id,
            'tour_language_id' => (int)$request->tour_language_id,
            'adults_quantity'  => $adults,
            'kids_quantity'    => $kids,
            'meeting_point_id' => $mpId,
            'is_other_hotel'   => $isOther,
            'other_hotel_name' => $other,
            'hotel_id'         => $isOther || $mpId ? null : $hotelId,
        ])->save();

        $cart->refreshExpiry((int) config('cart.expiry_minutes', 15));

        return back()->with('success', __('carts.messages.item_updated'));
    }

    /* ================= Helpers ================= */

    private function normalizeHotelInput(Request $request): void
    {
        $in = $request->all();
        $in['is_other_hotel'] = (bool)($in['is_other_hotel'] ?? false);

        $rawHotel = $in['hotel_id'] ?? null;
        if ($rawHotel === 'other' || $rawHotel === '__custom__' || (isset($rawHotel) && !ctype_digit((string)$rawHotel))) {
            if ($rawHotel === 'other' || $rawHotel === '__custom__') $in['is_other_hotel'] = true;
            $in['hotel_id'] = null;
        }
        if (!empty($in['other_hotel_name'])) {
            $in['is_other_hotel'] = true;
            $in['hotel_id'] = null;
        }
        $request->replace($in);
    }

    private function findValidScheduleOrFail($tour, int $scheduleId)
    {
        $schedule = $tour->schedules()
            ->where('schedules.schedule_id', $scheduleId)
            ->where('schedules.is_active', true)
            ->wherePivot('is_active', true)
            ->first();

        if (!$schedule) {
            // Mantén el mismo flujo de error hacia atrás
            abort(403, __('carts.messages.schedule_unavailable'));
        }

        return $schedule;
    }

    private function activeCartOrCreate(int $userId): Cart
    {
        $cart = Cart::where('user_id', $userId)->where('is_active', true)->orderByDesc('cart_id')->first();
        if (!$cart || $cart->isExpired()) {
            if ($cart) $this->expireCart($cart);
            $cart = Cart::create(['user_id' => $userId, 'is_active' => true]);
        }
        return $cart;
    }

    private function activeCartOf($user, bool $withTourSchedules = false): ?Cart
    {
        $q = $user->cart()->where('is_active', true)->orderByDesc('cart_id');
        if ($withTourSchedules) $q->with(['items.tour.schedules']);
        return $q->first();
    }

    private function resolvePickupForStore(Request $request): array
    {
        $mpId = $request->integer('selected_meeting_point') ?: null;
        if ($mpId && !MeetingPoint::whereKey($mpId)->exists()) $mpId = null;

        $isOther = $request->boolean('is_other_hotel');
        $hotelId = $isOther ? null : ($request->hotel_id ?: null);
        $other   = $isOther ? ($request->other_hotel_name ?: null) : null;

        if ($mpId) { // MP wipes hotel/custom
            $hotelId = null; $isOther = false; $other = null;
        }

        return [$hotelId, $isOther, $other, $mpId];
    }

    private function resolvePickupForUpdate(Request $request): array
    {
        $mpId = $request->integer('meeting_point_id') ?: null;
        if ($mpId && !MeetingPoint::whereKey($mpId)->exists()) $mpId = null;

        $isOther = $request->boolean('is_other_hotel');
        $hotelId = $isOther ? null : ($request->hotel_id ?: null);
        $other   = $isOther ? ($request->other_hotel_name ?: null) : null;

        if ($mpId) {
            $hotelId = null; $isOther = false; $other = null;
        } elseif ($isOther) {
            $mpId = null; $hotelId = null;
        } elseif ($hotelId) {
            $mpId = null; $isOther = false; $other = null;
        }

        return [$hotelId, $isOther, $other, $mpId];
    }

    private function backOrJsonError(Request $request, string $message)
    {
        return $request->ajax()
            ? response()->json(['ok' => false, 'message' => $message], 422)
            : back()->withInput()->with('error', $message);
    }
}
