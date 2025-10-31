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

    /* ====================== Cart view (auth only) ====================== */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Fetch the most recent active cart with relations
        $cart = $user->cart()
            ->where('is_active', true)
            ->orderByDesc('cart_id')
            ->with([
                'items.tour.schedules',
                'items.tour.languages',
                'items.tour.translations',
                'items.schedule',
                'items.language',
                'items.hotel',
                'items.meetingPoint.translations',
            ])
            ->first();

        // If it exists but is empty, expire it and hide timer
        if ($cart && !$cart->items()->count()) {
            $cart->forceExpire();
            $cart = null;
        }

        return view('public.cart', [
            'cart'           => $cart,
            'client'         => $user,
            'hotels'         => HotelList::where('is_active', true)->orderBy('name')->get(),
            'meetingPoints'  => MeetingPoint::where('is_active', true)
                ->with('translations')
                ->orderByRaw('sort_order IS NULL, sort_order ASC')
                ->orderBy('name', 'asc')
                ->get(),
            'expiresAtIso'   => optional($cart?->expires_at)->toIso8601String(),
            'extendUsed'     => (int) ($cart?->extended_count ?? 0),
            'extendMax'      => (int) config('cart.max_extensions', 1),
        ]);
    }

    /* ====================== Add item ====================== */
    public function store(Request $request)
    {
        abort_unless(Auth::check(), 403);

        $this->normalizeHotelInput($request);

        // Business rules:
        // - At least 2 adults
        // - At most 2 kids
        // - Total pax (adults + kids) <= 12
        $request->validate([
            'tour_id'                => 'required|exists:tours,tour_id',
            'tour_date'              => 'required|date|after_or_equal:today',
            'schedule_id'            => 'required|exists:schedules,schedule_id',
            'tour_language_id'       => 'required|exists:tour_languages,tour_language_id',
            'hotel_id'               => 'bail|nullable|integer|exists:hotels_list,hotel_id|exclude_if:is_other_hotel,1',
            'is_other_hotel'         => 'boolean',
            'other_hotel_name'       => 'nullable|string|max:255|required_if:is_other_hotel,1',
            'adults_quantity'        => 'required|integer|min:2|max:12',
            'kids_quantity'          => 'nullable|integer|min:0|max:2',
            'selected_meeting_point' => 'nullable|integer|exists:meeting_points,id',
        ]);

        $user     = $request->user();
        $adults   = (int) $request->adults_quantity;
        $kids     = (int) ($request->kids_quantity ?? 0);
        $totalPax = $adults + $kids;

        // Enforce total pax <= 12
        if ($totalPax > 12) {
            return $this->backOrJsonError($request, __('Máximo 12 personas por reserva (adultos + niños).'));
        }

        $tour     = \App\Models\Tour::with('schedules')->findOrFail((int) $request->tour_id);
        $schedule = $this->findValidScheduleOrFail($tour, (int) $request->schedule_id);
        $tourDate = $request->tour_date;

        // Blocked date / capacity
        if ($this->capacity->isDateBlocked($tour, $schedule, $tourDate)) {
            return $this->backOrJsonError($request, __('carts.messages.capacity_full'));
        }

        $requested = $totalPax;
        $remaining = $this->capacity->remainingCapacity($tour, $schedule, $tourDate, excludeBookingId: null, countHolds: true);

        if ($requested > $remaining) {
            $msg = $remaining <= 0
                ? __('carts.messages.capacity_full')
                : __('carts.messages.limited_seats_available', [
                    'available' => $remaining,
                    'tour'      => $tour->getTranslatedName(),
                    'date'      => $this->fmtDateEn($tourDate),
                ]);
            return $this->backOrJsonError($request, $msg);
        }

        // Pickup resolution
        [$hotelId, $isOther, $other, $mpId] = $this->resolvePickupForStore($request);

        // Create cart ONLY if needed when adding the first item
        $cart = Cart::where('user_id', $user->user_id)
            ->where('is_active', true)
            ->latest('cart_id')
            ->first();

        if (!$cart || $cart->isExpired()) {
            if ($cart && $cart->isExpired()) {
                $cart->forceExpire();
            }
            $cart = Cart::create([
                'user_id'   => $user->user_id,
                'is_active' => true,
            ])->ensureExpiry((int) config('cart.expiry_minutes', 15));
        }

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

        $successMessage = __('carts.messages.item_added');

        return $request->ajax()
            ? response()->json(['ok' => true, 'message' => $successMessage, 'count' => $this->countRaw($request)])
            : back()->with('success', $successMessage);
    }

    /* ====================== Remove item ====================== */
    public function destroy(Request $request, $itemId)
    {
        abort_unless(Auth::check(), 403);

        $cart = $this->activeCartOf(Auth::user());
        if ($cart) {
            $cart->items()->where('item_id', $itemId)->delete();

            // If there are no items left, expire the cart (avoid empty timers)
            if ($cart->items()->count() === 0) {
                $cart->forceExpire();
                return back()->with('success', __('carts.messages.cart_deleted'));
            }
        }

        return back()->with('success', __('carts.messages.item_removed'));
    }

    /* ====================== Quick count (AJAX) ====================== */
    public function count(Request $request)
    {
        abort_unless(Auth::check(), 403);

        $cart = $this->activeCartOf($request->user());
        if (!$cart) return response()->json(['count' => 0, 'expired' => false]);

        // If empty, expire immediately and answer 0
        if (!$cart->items()->count()) {
            $cart->forceExpire();
            return response()->json(['count' => 0, 'expired' => true]);
        }

        // If expired, force expiration
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

    /* ====================== Expire cart (AJAX) ====================== */
    public function expire(Request $request)
    {
        abort_unless(Auth::check(), 403);

        $cart = $this->activeCartOf($request->user());
        if (!$cart) return response()->json(['ok' => true, 'already' => 'no_cart']);

        $cart->forceExpire();
        return response()->json(['ok' => true, 'message' => __('carts.messages.cart_expired')]);
    }

    /* ====================== Refresh/Extend expiry (AJAX) ====================== */
    public function refreshExpiry(Request $request)
    {
        abort_unless(Auth::check(), 403);

        // Important: DO NOT create a cart if none exists
        $cart   = $this->activeCartOf($request->user());
        $extend = (int) config('cart.extend_minutes', 10);
        $maxExt = (int) config('cart.max_extensions', 1);

        // If there is no cart or it is empty, return 410 and ensure it is not active
        if (!$cart || !$cart->items()->count()) {
            if ($cart) $cart->forceExpire();
            return response()->json([
                'ok'      => false,
                'expired' => true,
                'message' => __('carts.messages.cart_empty'),
            ], 410);
        }

        if ($cart->isExpired()) {
            $this->expireCart($cart);
            return response()->json(['ok' => false, 'expired' => true, 'message' => __('carts.messages.cart_expired')], 422);
        }

        if (!$cart->canExtend()) {
            return response()->json([
                'ok'              => false,
                'message'         => __('carts.messages.max_extensions_reached', ['max' => $maxExt]),
                'expires_at'      => optional($cart->expires_at)->toIso8601String(),
                'remaining'       => $cart->remainingSeconds(),
                'extended_count'  => (int) $cart->extended_count,
                'max_extensions'  => $maxExt,
            ], 422);
        }

        $cart->extendOnce($extend);

        return response()->json([
            'ok'              => true,
            'expires_at'      => $cart->expires_at->toIso8601String(),
            'remaining'       => $cart->remainingSeconds(),
            'message'         => __('carts.messages.cart_refreshed'),
            'extended_count'  => (int) $cart->extended_count,
            'max_extensions'  => $maxExt,
        ]);
    }

    /* ====================== Promo (public) ====================== */
    public function applyPromo(Request $request)
    {
        abort_unless(Auth::check(), 403);

        $request->validate(['code' => 'required|string|max:100']);
        $user = $request->user();

        $cart = $user->cart()
            ->where('is_active', true)
            ->with('items.tour')
            ->first();

        if (!$cart || !$cart->items->count()) {
            return response()->json(['ok' => false, 'message' => __('carts.messages.cart_empty')], 422);
        }
        if ($cart->isExpired()) {
            $this->expireCart($cart);
            return response()->json(['ok' => false, 'message' => __('carts.messages.cart_expired')], 422);
        }

        $clean = PromoCode::normalize($request->code);
        $promo = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$clean])->first();

        if (!$promo)                     return response()->json(['ok' => false, 'message' => __('carts.messages.invalid_code')], 422);
        if (!$promo->isValidToday())     return response()->json(['ok' => false, 'message' => __('carts.messages.code_expired_or_not_yet')], 422);
        if (!$promo->hasRemainingUses()) return response()->json(['ok' => false, 'message' => __('carts.messages.code_limit_reached')], 422);
        if ($promo->is_used && ($promo->usage_limit === 1)) {
            return response()->json(['ok' => false, 'message' => __('carts.messages.code_already_used')], 422);
        }

        $subtotal = $this->cartSubtotal($cart);

        $discountFixed    = max(0.0, (float)($promo->discount_amount  ?? 0));
        $discountPerc     = max(0.0, (float)($promo->discount_percent ?? 0));
        $discountFromPerc = round($subtotal * ($discountPerc / 100), 2);
        $adjustment       = round($discountFixed + $discountFromPerc, 2);
        $operation        = $promo->operation === 'add' ? 'add' : 'subtract';

        // Persist promo in public session
        session([
            'public_cart_promo' => [
                'code'       => $promo->code,
                'operation'  => $operation,
                'amount'     => $discountFixed,
                'percent'    => $discountPerc,
                'adjustment' => $adjustment,
                'applied_at' => now()->toIso8601String(),
            ]
        ]);

        $newTotal = $this->cartTotalWithSessionPromo($cart);

        return response()->json([
            'ok'        => true,
            'code'      => $promo->code,
            'message'   => __('carts.messages.code_applied'),
            'new_total' => $newTotal,
        ]);
    }

    public function removePromo(Request $request)
    {
        abort_unless(Auth::check(), 403);

        $request->session()->forget('public_cart_promo');

        $cart = $this->activeCartOf($request->user());
        $newTotal = $cart ? $this->cartSubtotal($cart) : 0.0;

        return response()->json([
            'ok'        => true,
            'message'   => __('carts.messages.code_removed'),
            'new_total' => $newTotal,
        ]);
    }

    /* ====================== Internals ====================== */
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

    public function update(Request $request, $itemId)
    {
        abort_unless(Auth::check(), 403);

        // Business rules identical to store()
        $request->validate([
            'tour_date'        => 'required|date|after_or_equal:today',
            'schedule_id'      => 'required|exists:schedules,schedule_id',
            'tour_language_id' => 'required|exists:tour_languages,tour_language_id',
            'adults_quantity'  => 'required|integer|min:2|max:12',
            'kids_quantity'    => 'nullable|integer|min:0|max:2',
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

        // Enforce total pax <= 12
        if ($requested > 12) {
            return back()->with('error', __('Máximo 12 personas en total (adultos + niños).'));
        }

        if ($this->capacity->isDateBlocked($tour, $schedule, $tourDate)) {
            return back()->with('error', __('carts.messages.date_no_longer_available', [
                'date' => $this->fmtDateEn($tourDate), 'min' => 1
            ]));
        }

        // remaining = max - confirmed - held + (current item pax)
        $max        = $this->capacity->resolveMaxCapacity($schedule, $tour);
        $confirmed  = $this->capacity->confirmedPaxFor($tourDate, (int)$schedule->schedule_id, null, (int)$tour->tour_id);
        $held       = $this->capacity->heldPaxInActiveCarts($tourDate, (int)$schedule->schedule_id, (int)$tour->tour_id);
        $currentPax = (int)$item->adults_quantity + (int)$item->kids_quantity;
        $remaining  = max(0, (int)$max - (int)$confirmed - (int)$held + $currentPax);

        if ($requested > $remaining) {
            $msg = $remaining <= 0
                ? __('carts.messages.slot_full')
                : __('carts.messages.limited_seats_available', [
                    'available' => $remaining,
                    'tour'      => $tour->getTranslatedName(),
                    'date'      => $this->fmtDateEn($tourDate),
                ]);
            return back()->with('error', $msg);
        }

        // Pickup resolution
        [$hotelId, $isOther, $other, $mpId] = $this->resolvePickupForUpdate($request);

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

        return back()->with('success', __('carts.messages.item_updated'));
    }

    /* ---------------- helpers ---------------- */
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
            abort(403, __('carts.messages.schedule_unavailable'));
        }

        return $schedule;
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

        if ($mpId) { $hotelId = null; $isOther = false; $other = null; }

        return [$hotelId, $isOther, $other, $mpId];
    }

    private function resolvePickupForUpdate(Request $request): array
    {
        $mpId = $request->integer('meeting_point_id') ?: null;
        if ($mpId && !MeetingPoint::whereKey($mpId)->exists()) $mpId = null;

        $isOther = $request->boolean('is_other_hotel');
        $hotelId = $isOther ? null : ($request->hotel_id ?: null);
        $other   = $isOther ? ($request->other_hotel_name ?: null) : null;

        if ($mpId)       { $hotelId = null; $isOther = false; $other = null; }
        elseif ($isOther){ $mpId = null; $hotelId = null; }
        elseif ($hotelId){ $mpId = null; $isOther = false; $other = null; }

        return [$hotelId, $isOther, $other, $mpId];
    }

    private function backOrJsonError(Request $request, string $message)
    {
        return $request->ajax()
            ? response()->json(['ok' => false, 'message' => $message], 422)
            : back()->withInput()->with('error', $message);
    }

    private function fmtDateEn(string|\DateTimeInterface $date): string
    {
        // Example: 30/Oct/2025
        return Carbon::parse($date)->format('d/M/Y');
    }

    private function cartSubtotal(Cart $cart): float
    {
        return (float) $cart->items->sum(function ($it) {
            $ap = (float)($it->tour->adult_price ?? 0);
            $kp = (float)($it->tour->kid_price   ?? 0);
            $aq = (int)($it->adults_quantity ?? 0);
            $kq = (int)($it->kids_quantity   ?? 0);
            return ($ap * $aq) + ($kp * $kq);
        });
    }

    private function cartTotalWithSessionPromo(Cart $cart): float
    {
        $total = $this->cartSubtotal($cart);
        $promoSession = session('public_cart_promo');
        if ($promoSession) {
            $op = ($promoSession['operation'] ?? 'subtract') === 'add' ? 1 : -1;
            $total = max(0, round($total + $op * (float)($promoSession['adjustment'] ?? 0), 2));
        }
        return $total;
    }
}
