<?php

namespace App\Http\Controllers\Admin\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Tour;
use App\Models\TourLanguage;
use App\Models\HotelList;
use App\Models\MeetingPoint;
use App\Models\PromoCode;
use App\Models\TourExcludedDate;
use App\Services\Bookings\BookingCapacityService;

class CartController extends Controller
{
    public function __construct(
        private BookingCapacityService $capacity
    ) {}

    public function index(Request $request)
    {
        $user      = Auth::user();
        $languages = TourLanguage::all();
        $hotels    = HotelList::where('is_active', true)->orderBy('name')->get();
        $adminPromo = session('admin_cart_promo') ?: [];

        $cart = $user->cart()
            ->where('is_active', true)
            ->orderByDesc('cart_id')
            ->first();

        if (!$cart) {
            $emptyCart        = new \stdClass();
            $emptyCart->items = collect();
            return view('admin.carts.cart', [
                'cart'      => $emptyCart,
                'languages' => $languages,
                'hotels'    => $hotels,
            ]);
        }

        $itemsQuery = CartItem::with(['tour','schedule','language','hotel','meetingPoint'])
            ->where('cart_id', $cart->cart_id);

        if ($request->filled('status')) {
            $itemsQuery->where('is_active', (bool)$request->status);
        }

        $cart->items = $itemsQuery->get();

        return view('admin.carts.cart', compact('cart','languages','hotels','adminPromo'));
    }

public function store(Request $request)
{
    $in = $request->all();
    $in['is_other_hotel'] = (bool)($in['is_other_hotel'] ?? false);

    $raw = $in['hotel_id'] ?? null;
    if ($raw === 'other' || $raw === '__custom__' || (isset($raw) && !ctype_digit((string)$raw))) {
        if ($raw === 'other' || $raw === '__custom__') $in['is_other_hotel'] = true;
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

    $user = Auth::user();

    $cart = $user->cart()->where('is_active', true)->orderByDesc('cart_id')->first();
    if (!$cart || $cart->isExpired()) {
        if ($cart) $this->expireCart($cart);
        $cart = \App\Models\Cart::create(['user_id' => $user->user_id, 'is_active' => true]);
    }
    $cart->refreshExpiry((int) config('cart.expiry_minutes', 15));

    $tour = \App\Models\Tour::with('schedules')->findOrFail($request->tour_id);
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
    $requested = (int)$request->adults_quantity + (int)($request->kids_quantity ?? 0);

    if ($requested > $remaining) {
        return back()->with('error', __('adminlte::adminlte.tourCapacityFull'));
    }

    $mpId = $request->integer('selected_meeting_point') ?: null;
    $mp   = $mpId ? \App\Models\MeetingPoint::find($mpId) : null;

    \App\Models\CartItem::create([
        'cart_id'          => $cart->cart_id,
        'tour_id'          => $request->tour_id,
        'tour_date'        => $request->tour_date,
        'schedule_id'      => $request->schedule_id,
        'tour_language_id' => $request->tour_language_id,
        'hotel_id'         => $request->boolean('is_other_hotel') ? null : $request->hotel_id,
        'is_other_hotel'   => $request->boolean('is_other_hotel'),
        'other_hotel_name' => $request->boolean('is_other_hotel') ? $request->other_hotel_name : null,
        'adults_quantity'  => (int)$request->adults_quantity,
        'kids_quantity'    => (int)($request->kids_quantity ?? 0),
        'is_active'        => true,
        'meeting_point_id'          => $mp?->id,
        'meeting_point_name'        => $mp?->name,
        'meeting_point_pickup_time' => $mp?->pickup_time,
        'meeting_point_description' => $mp?->description,
        'meeting_point_map_url'     => $mp?->map_url,
    ]);

    $cart->refreshExpiry((int) config('cart.expiry_minutes', 15));

    return $request->ajax()
        ? response()->json(['message' => __('adminlte::adminlte.cartItemAdded')])
        : back()->with('success', __('adminlte::adminlte.cartItemAdded'));
}


public function update(Request $request, CartItem $item)
{
    $in = $request->all();
    $in['is_other_hotel'] = (bool)($in['is_other_hotel'] ?? false);

    $raw = $in['hotel_id'] ?? null;
    if ($in['is_other_hotel'] === true || $raw === 'other' || $raw === '__custom__' || (isset($raw) && !ctype_digit((string)$raw))) {
        $in['hotel_id'] = null;
    }
    $request->replace($in);

    $data = $request->validate([
        'tour_date'        => ['required','date','after_or_equal:today'],
        'adults_quantity'  => ['required','integer','min:1'],
        'kids_quantity'    => ['nullable','integer','min:0','max:2'],
        'schedule_id'      => ['nullable','exists:schedules,schedule_id'],
        'tour_language_id' => ['required','exists:tour_languages,tour_language_id'],
        'is_active'        => ['nullable','boolean'],
        'hotel_id'         => ['bail','nullable','integer','exists:hotels_list,hotel_id','exclude_if:is_other_hotel,1'],
        'is_other_hotel'   => ['boolean'],
        'other_hotel_name' => ['nullable','string','max:255','required_if:is_other_hotel,1'],
        'meeting_point_id' => ['nullable','integer','exists:meeting_points,id'],
    ]);

    $tour       = $item->tour;
    $scheduleId = $data['schedule_id'] ?? $item->schedule_id;

    if ($scheduleId) {
        $schedule = $tour->schedules()
            ->where('schedules.schedule_id', $scheduleId)
            ->where('schedules.is_active', true)
            ->wherePivot('is_active', true)
            ->first();

        if (!$schedule) {
            return back()->withErrors(['schedule_id' => __('carts.messages.schedule_unavailable')]);
        }

        $remaining = $this->capacity->remainingCapacity($tour, $schedule, $data['tour_date'], excludeBookingId: null, countHolds: true);
        $requested = (int)$data['adults_quantity'] + (int)($data['kids_quantity'] ?? 0);

        if ($requested > $remaining) {
            return back()->with('error', __('carts.messages.capacity_full'));
        }
    }

    $item->fill([
        'tour_date'        => $data['tour_date'],
        'adults_quantity'  => (int)$data['adults_quantity'],
        'kids_quantity'    => (int)($data['kids_quantity'] ?? 0),
        'schedule_id'      => $data['schedule_id'] ?? $item->schedule_id,
        'tour_language_id' => (int)$data['tour_language_id'],
        'is_active'        => $request->boolean('is_active'),
    ]);

    if ($request->boolean('is_other_hotel')) {
        $item->fill([
            'is_other_hotel'   => true,
            'other_hotel_name' => $data['other_hotel_name'] ?? null,
            'hotel_id'         => null,
        ]);
    } else {
        $item->fill([
            'is_other_hotel'   => false,
            'other_hotel_name' => null,
            'hotel_id'         => $data['hotel_id'] ?? null,
        ]);
    }

    if (array_key_exists('meeting_point_id', $data)) {
        $mpId = $request->integer('meeting_point_id') ?: null;
        $mp   = $mpId ? \App\Models\MeetingPoint::find($mpId) : null;

        $item->meeting_point_id          = $mp?->id;
        $item->meeting_point_name        = $mp?->name;
        $item->meeting_point_pickup_time = $mp?->pickup_time;
        $item->meeting_point_description = $mp?->description;
        $item->meeting_point_map_url     = $mp?->map_url;
    }

    $item->save();

    $cart = $item->cart;
    if ($cart && $cart->is_active) {
        $cart->isExpired() ? $this->expireCart($cart) : $cart->refreshExpiry((int) config('cart.expiry_minutes', 15));
    }

    return back()->with('success', __('carts.messages.item_updated'));
}


    public function destroy(CartItem $item)
    {
        $cart = $item->cart;
        $item->delete();

        if ($cart && $cart->is_active && !$cart->isExpired()) {
            $cart->refreshExpiry((int) config('cart.expiry_minutes', 15));
        }

        return back()->with('success', __('carts.messages.cart_item_deleted'));
    }

    public function destroyCart(Cart $cart)
    {
        DB::transaction(function () use ($cart) {
            $cart->items()->delete();
            $cart->delete();
        });

        return back()->with('success', __('carts.messages.cart_deleted'));
    }

    public function allCarts(Request $request)
    {
        $status = $request->query('status');

        $query = Cart::query()
            ->with(['user','items.tour','items.language','items.schedule','items.meetingPoint'])
            ->withCount('items')
            ->whereHas('user', function ($q) use ($request) {
                if ($request->filled('email')) {
                    $q->where('email', 'ilike', '%' . $request->email . '%');
                }
            })
            ->whereHas('items');

        if (in_array($status, ['0','1'], true)) {
            $query->where('is_active', (bool)$status);
        }

        $carts = $query->orderByDesc('updated_at')->get();

        foreach ($carts as $cart) {
            $cart->total_usd = $cart->items->sum(function ($it) {
                $ap = (float)($it->tour->adult_price ?? 0);
                $kp = (float)($it->tour->kid_price   ?? 0);
                $aq = (int)($it->adults_quantity ?? 0);
                $kq = (int)($it->kids_quantity   ?? 0);
                return ($ap * $aq) + ($kp * $kq);
            });
        }

        return view('admin.carts.all', compact('carts'));
    }

    public function toggleActive(Cart $cart)
    {
        $cart->update(['is_active' => !$cart->is_active]);
        return back()->with('success', __('carts.messages.cart_status_updated'));
    }

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

public function applyPromoAdmin(Request $request)
{
    $codeInput = strtoupper(trim((string)$request->input('code', '')));
    $current   = (string) (session('admin_cart_promo.code') ?? '');

    // Si vacío o mismo => quitar
    if ($codeInput === '' || $codeInput === strtoupper($current)) {
        $request->session()->forget('admin_cart_promo');
        return back()->with('success', __('carts.messages.code_removed'));
    }

    $user = Auth::user();
    $cart = $user->cart()
        ->where('is_active', true)
        ->with('items.tour')
        ->first();

    if (!$cart || !$cart->items->count()) {
        return back()->with('error', __('carts.messages.cart_empty'));
    }
    if ($cart->isExpired()) {
        $this->expireCart($cart);
        return back()->with('error', __('carts.messages.cart_expired'));
    }

    // Normaliza y busca cupón (sin is_active/expiration_date columnas)
    $clean = PromoCode::normalize($codeInput);
    $promo = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$clean])->first();
    if (!$promo)                  return back()->with('error', __('carts.messages.invalid_code'));
    if (!$promo->isValidToday())  return back()->with('error', __('carts.messages.code_expired_or_not_yet'));
    if (!$promo->hasRemainingUses()) return back()->with('error', __('carts.messages.code_limit_reached'));
    if ($promo->is_used && ($promo->usage_limit === 1)) {
        // si tu lógica es un solo uso total, bloquea
        return back()->with('error', __('carts.messages.code_already_used'));
    }

    // Subtotal
    $subtotal = (float)$cart->items->sum(function ($it) {
        $ap = (float)($it->tour->adult_price ?? 0);
        $kp = (float)($it->tour->kid_price   ?? 0);
        $aq = (int)($it->adults_quantity ?? 0);
        $kq = (int)($it->kids_quantity   ?? 0);
        return ($ap * $aq) + ($kp * $kq);
    });

    $discountFixed    = max(0.0, (float)($promo->discount_amount   ?? 0));
    $discountPerc     = max(0.0, (float)($promo->discount_percent  ?? 0));
    $discountFromPerc = round($subtotal * ($discountPerc / 100), 2);
    $adjustment       = round($discountFixed + $discountFromPerc, 2);

    $operation = $promo->operation === 'add' ? 'add' : 'subtract';

    session([
        'admin_cart_promo' => [
            'code'       => $promo->code,
            'operation'  => $operation,
            'amount'     => $discountFixed,
            'percent'    => $discountPerc,
            'adjustment' => $adjustment,
            'applied_at' => now()->toISOString(),
        ]
    ]);

    if ($cart && $cart->is_active && !$cart->isExpired()) {
        $cart->refreshExpiry((int) config('cart.expiry_minutes', 15));
    }

    return back()->with('success', __('carts.messages.code_applied'));
}

public function removePromoAdmin(Request $request)
{
    $request->session()->forget('admin_cart_promo');
    return back()->with('success', __('carts.messages.code_removed'));
}



}
