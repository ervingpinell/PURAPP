<?php

namespace App\Http\Controllers\Admin\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\{Cart, CartItem, Tour, TourLanguage, HotelList, MeetingPoint, PromoCode};
use App\Services\Bookings\{
    BookingCapacityService,
    BookingPricingService,
    BookingValidationService
};

class CartController extends Controller
{
    public function __construct(
        private BookingCapacityService $capacity,
        private BookingPricingService $pricing,
        private BookingValidationService $validation
    ) {}

    public function index(Request $request)
    {
        $user       = Auth::user();
        $languages  = TourLanguage::all();
        $hotels     = HotelList::where('is_active', true)->orderBy('name')->get();
        $adminPromo = session('admin_cart_promo') ?: [];

        $cart = $user->cart()
            ->where('is_active', true)
            ->orderByDesc('cart_id')
            ->first();

        if (!$cart || $cart->isExpired()) {
            if ($cart) $this->expireCart($cart);
            $empty        = new \stdClass();
            $empty->items = collect();
            return view('admin.carts.cart', [
                'cart'      => $empty,
                'languages' => $languages,
                'hotels'    => $hotels,
                'adminPromo'=> $adminPromo,
            ]);
        }

        $itemsQuery = CartItem::with(['tour.prices.category', 'schedule', 'language', 'hotel', 'meetingPoint'])
            ->where('cart_id', $cart->cart_id);

        if ($request->filled('status')) {
            $itemsQuery->where('is_active', (bool)$request->status);
        }

        $cart->items = $itemsQuery->get();

        return view('admin.carts.cart', compact('cart', 'languages', 'hotels', 'adminPromo'));
    }

    public function store(Request $request)
    {
        // Normalizar pickup: meeting point anula hotel/otro
        $in = $request->all();
        $in['is_other_hotel'] = (bool)($in['is_other_hotel'] ?? false);
        if (!empty($in['selected_meeting_point'])) {
            $in['is_other_hotel'] = false;
            $in['other_hotel_name'] = null;
            $in['hotel_id'] = null;
        } elseif (!empty($in['other_hotel_name'])) {
            $in['is_other_hotel'] = true;
            $in['hotel_id'] = null;
        } else {
            $raw = $in['hotel_id'] ?? null;
            if ($raw === 'other' || $raw === '__custom__' || (isset($raw) && !ctype_digit((string)$raw))) {
                if ($raw === 'other' || $raw === '__custom__') $in['is_other_hotel'] = true;
                $in['hotel_id'] = null;
            }
        }
        $request->replace($in);

        $request->validate([
            'tour_id'                => 'required|exists:tours,tour_id',
            'tour_date'              => 'required|date|after_or_equal:today',
            'schedule_id'            => 'required|exists:schedules,schedule_id',
            'tour_language_id'       => 'required|exists:tour_languages,tour_language_id',
            'categories'             => 'required|array|min:1',
            'categories.*'           => 'required|integer|min:0',
            'hotel_id'               => 'bail|nullable|integer|exists:hotels_list,hotel_id|exclude_if:is_other_hotel,1',
            'is_other_hotel'         => 'boolean',
            'other_hotel_name'       => 'nullable|string|max:255|required_if:is_other_hotel,1',
            'selected_meeting_point' => 'nullable|integer|exists:meeting_points,id',
        ]);

        $user = Auth::user();

        $cart = $user->cart()->where('is_active', true)->orderByDesc('cart_id')->first();
        if (!$cart || $cart->isExpired()) {
            if ($cart) $this->expireCart($cart);
            $cart = Cart::create(['user_id' => $user->user_id, 'is_active' => true])
                ->ensureExpiry((int) config('cart.expiry_minutes', 15));
        } else {
            $cart->ensureExpiry((int) config('cart.expiry_minutes', 15));
        }

        $tour = Tour::with(['schedules', 'prices.category'])->findOrFail($request->tour_id);

        // Validación modular de categorías
        $validationResult = $this->validation->validateQuantities($tour, $request->categories);
        if (!$validationResult['valid']) {
            $errorMsg = implode(' ', $validationResult['errors']);
            return back()->withInput()->withErrors(['categories' => $errorMsg]);
        }

        $schedule = $tour->schedules()
            ->where('schedules.schedule_id', $request->schedule_id)
            ->where('schedules.is_active', true)
            ->wherePivot('is_active', true)
            ->first();
        if (!$schedule) {
            return back()->withErrors(['schedule_id' => __('carts.messages.schedule_unavailable')]);
        }

        // Capacidad
        $totalPax = array_sum($request->categories);
        $remaining = $this->capacity->remainingCapacity(
            $tour,
            $schedule,
            $request->tour_date,
            excludeBookingId: null,
            countHolds: true
        );
        if ($totalPax > $remaining) {
            return back()->with('error', __('adminlte::adminlte.tourCapacityFull'));
        }

        // Snapshot
        $categoriesSnapshot = $this->pricing->buildCategoriesSnapshot($tour, $request->categories);
        if (empty($categoriesSnapshot)) {
            return back()->with('error', __('m_bookings.validation.no_active_categories'));
        }

        $mpId = $request->integer('selected_meeting_point') ?: null;
        $mp   = $mpId ? MeetingPoint::find($mpId) : null;

        // Legacy: adults/kids
        $adultCategory = collect($categoriesSnapshot)->firstWhere('category_slug', 'adult');
        $kidCategory   = collect($categoriesSnapshot)->firstWhere('category_slug', 'kid');

        CartItem::create([
            'cart_id'                    => $cart->cart_id,
            'tour_id'                    => (int)$request->tour_id,
            'tour_date'                  => $request->tour_date,
            'schedule_id'                => (int)$request->schedule_id,
            'tour_language_id'           => (int)$request->tour_language_id,
            'categories'                 => $categoriesSnapshot,

            // Legacy
            'adults_quantity'            => $adultCategory ? (int)$adultCategory['quantity'] : 0,
            'kids_quantity'              => $kidCategory ? (int)$kidCategory['quantity'] : 0,
            'adult_price'                => $adultCategory ? (float)$adultCategory['price'] : 0,
            'kid_price'                  => $kidCategory ? (float)$kidCategory['price'] : 0,

            // Pickup
            'hotel_id'                   => $request->boolean('is_other_hotel') ? null : $request->hotel_id,
            'is_other_hotel'             => $request->boolean('is_other_hotel'),
            'other_hotel_name'           => $request->boolean('is_other_hotel') ? $request->other_hotel_name : null,
            'meeting_point_id'           => $mp?->id,
            'meeting_point_name'         => $mp?->name,
            'meeting_point_pickup_time'  => $mp?->pickup_time,
            'meeting_point_description'  => $mp?->description,
            'meeting_point_map_url'      => $mp?->map_url,

            'is_active'                  => true,
        ]);

        $cart->refreshExpiry((int) config('cart.expiry_minutes', 15));

        return $request->ajax()
            ? response()->json(['message' => __('adminlte::adminlte.cartItemAdded')])
            : back()->with('success', __('adminlte::adminlte.cartItemAdded'));
    }

    public function update(Request $request, CartItem $item)
    {
        // Normalizar pickup
        $in = $request->all();
        $in['is_other_hotel'] = (bool)($in['is_other_hotel'] ?? false);
        if (!empty($in['meeting_point_id'])) {
            $in['is_other_hotel'] = false;
            $in['other_hotel_name'] = null;
            $in['hotel_id'] = null;
        } elseif (!empty($in['other_hotel_name'])) {
            $in['is_other_hotel'] = true;
            $in['hotel_id'] = null;
        } else {
            $raw = $in['hotel_id'] ?? null;
            if ($in['is_other_hotel'] === true || $raw === 'other' || $raw === '__custom__' || (isset($raw) && !ctype_digit((string)$raw))) {
                $in['hotel_id'] = null;
            }
        }
        $request->replace($in);

        $data = $request->validate([
            'tour_date'        => ['required', 'date', 'after_or_equal:today'],
            'categories'       => ['required', 'array', 'min:1'],
            'categories.*'     => ['required', 'integer', 'min:0'],
            'schedule_id'      => ['nullable', 'exists:schedules,schedule_id'],
            'tour_language_id' => ['required', 'exists:tour_languages,tour_language_id'],
            'is_active'        => ['nullable', 'boolean'],
            'hotel_id'         => ['bail', 'nullable', 'integer', 'exists:hotels_list,hotel_id', 'exclude_if:is_other_hotel,1'],
            'is_other_hotel'   => ['boolean'],
            'other_hotel_name' => ['nullable', 'string', 'max:255', 'required_if:is_other_hotel,1'],
            'meeting_point_id' => ['nullable', 'integer', 'exists:meeting_points,id'],
        ]);

        $tour = $item->tour->load('prices.category');

        // Validación por categorías
        $validationResult = $this->validation->validateQuantities($tour, $data['categories']);
        if (!$validationResult['valid']) {
            $errorMsg = implode(' ', $validationResult['errors']);
            return back()->withInput()->withErrors(['categories' => $errorMsg]);
        }

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

            // Capacidad (devolver pax actual del item)
            $cart = $item->cart;
            $snap = $this->capacity->capacitySnapshot(
                $tour,
                $schedule,
                $data['tour_date'],
                excludeBookingId: null,
                countHolds: true,
                excludeCartId: (int) $cart->cart_id
            );

            $currentPax = (int)$item->total_pax;
            $totalPax   = array_sum($data['categories']);
            $remaining  = $snap['available'] + $currentPax;

            if ($totalPax > $remaining) {
                return back()->with('error', __('carts.messages.capacity_full'));
            }
        }

        // Nuevo snapshot
        $categoriesSnapshot = $this->pricing->buildCategoriesSnapshot($tour, $data['categories']);

        // Legacy
        $adultCategory = collect($categoriesSnapshot)->firstWhere('category_slug', 'adult');
        $kidCategory   = collect($categoriesSnapshot)->firstWhere('category_slug', 'kid');

        $item->fill([
            'tour_date'        => $data['tour_date'],
            'categories'       => $categoriesSnapshot,
            'adults_quantity'  => $adultCategory ? (int)$adultCategory['quantity'] : 0,
            'kids_quantity'    => $kidCategory ? (int)$kidCategory['quantity'] : 0,
            'adult_price'      => $adultCategory ? (float)$adultCategory['price'] : 0,
            'kid_price'        => $kidCategory ? (float)$kidCategory['price'] : 0,
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
            $mp   = $mpId ? MeetingPoint::find($mpId) : null;

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
            ->with(['user', 'items.tour.prices.category', 'items.language', 'items.schedule', 'items.meetingPoint'])
            ->withCount('items')
            ->whereHas('user', function ($q) use ($request) {
                if ($request->filled('email')) {
                    $q->where('email', 'ilike', '%' . $request->email . '%');
                }
            })
            ->whereHas('items');

        if (in_array($status, ['0', '1'], true)) {
            $query->where('is_active', (bool)$status);
        }

        $carts = $query->orderByDesc('updated_at')->get();

        foreach ($carts as $cart) {
            $cart->total_usd = $cart->items->sum(fn($item) => (float)$item->subtotal);
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

        if ($codeInput === '' || $codeInput === strtoupper($current)) {
            $request->session()->forget('admin_cart_promo');
            return back()->with('success', __('carts.messages.code_removed'));
        }

        $user = Auth::user();
        $cart = $user->cart()->where('is_active', true)->with('items.tour.prices.category')->first();

        if (!$cart || !$cart->items->count()) {
            return back()->with('error', __('carts.messages.cart_empty'));
        }
        if ($cart->isExpired()) {
            $this->expireCart($cart);
            return back()->with('error', __('carts.messages.cart_expired'));
        }

        $clean = PromoCode::normalize($codeInput);
        $promo = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$clean])->first();

        if (!$promo)                     return back()->with('error', __('carts.messages.invalid_code'));
        if (!$promo->isValidToday())     return back()->with('error', __('carts.messages.code_expired_or_not_yet'));
        if (!$promo->hasRemainingUses()) return back()->with('error', __('carts.messages.code_limit_reached'));
        if ($promo->is_used && ($promo->usage_limit === 1)) {
            return back()->with('error', __('carts.messages.code_already_used'));
        }

        // Subtotal del carrito (modelo CartItem->subtotal)
        $subtotal = (float) $cart->items->sum(fn($item) => (float)$item->subtotal);

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
