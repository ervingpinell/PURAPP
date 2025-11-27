<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\HotelList;
use App\Models\MeetingPoint;
use App\Models\PromoCode;
use App\Models\Tour;
use App\Models\Schedule;
use App\Services\Bookings\BookingCapacityService;
use App\Services\Bookings\BookingValidationService;
use App\Services\Bookings\BookingPricingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function __construct(
        private BookingCapacityService   $capacity,
        private BookingValidationService $validation,
        private BookingPricingService    $pricing,
    ) {}

    /* ====================== Cart view (auth only) ====================== */
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
                'items.tour.translations',
                'items.tour.prices.category',
                'items.schedule',
                'items.language',
                'items.hotel',
                'items.meetingPoint.translations',
            ])
            ->first();

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

        // Log de datos recibidos para debug
        Log::info('Cart Store - Data received:', [
            'hotel_id' => $request->hotel_id,
            'meeting_point_id' => $request->meeting_point_id,
            'is_other_hotel' => $request->is_other_hotel,
            'other_hotel_name' => $request->other_hotel_name,
            'all_data' => $request->all()
        ]);

        $this->normalizeHotelInput($request);

        // Validar estructura básica
        $request->validate([
            'tour_id'          => 'required|exists:tours,tour_id',
            'tour_date'        => 'required|date|after_or_equal:today',
            'schedule_id'      => 'required|exists:schedules,schedule_id',
            'tour_language_id' => 'required|exists:tour_languages,tour_language_id',
            'categories'       => 'required|array|min:1',
            'categories.*'     => 'required|integer|min:0',
            'hotel_id'         => 'nullable|integer|exists:hotels_list,hotel_id',
            'is_other_hotel'   => 'nullable|boolean',
            'other_hotel_name' => 'nullable|string|max:255|required_if:is_other_hotel,1',
            'meeting_point_id' => 'nullable|integer|exists:meeting_points,id',
        ]);

        $user = $request->user();
        $tour = Tour::with(['schedules', 'prices.category'])->findOrFail((int) $request->tour_id);

        // Validación por categorías
        $validationResult = $this->validation->validateQuantities($tour, $request->categories);
        if (!$validationResult['valid']) {
            $errorMsg = implode(' ', $validationResult['errors']);
            return $this->backOrJsonError($request, $errorMsg);
        }

        $schedule = $this->findValidScheduleOrFail($tour, (int) $request->schedule_id);
        $tourDate = $request->tour_date;

        // Capacidad
        $totalPax = $this->totalFromCategories($request->categories);

        if ($this->capacity->isDateBlocked($tour, $schedule, $tourDate)) {
            return $this->backOrJsonError($request, __('carts.messages.capacity_full'));
        }

        $remaining = $this->capacity->remainingCapacity($tour, $schedule, $tourDate);
        if ($totalPax > $remaining) {
            $msg = $remaining <= 0
                ? __('carts.messages.capacity_full')
                : __('carts.messages.limited_seats_available', [
                    'available' => $remaining,
                    'tour'      => $tour->getTranslatedName(),
                    'date'      => $this->fmtDateEn($tourDate),
                ]);
            return $this->backOrJsonError($request, $msg);
        }

        // Snapshot de categorías con precios
        $categoriesSnapshot = $this->pricing->buildCategoriesSnapshot($tour, $request->categories, $tourDate);

        if (empty($categoriesSnapshot)) {
            return $this->backOrJsonError($request, __('m_bookings.validation.no_active_categories'));
        }

        // Obtener o crear carrito
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
            ])->ensureExpiry((int) config('cart.expiration_minutes', 15));
        }

        // Pickup - CORREGIDO: usar meeting_point_id directamente
        [$hotelId, $isOther, $other, $mpId] = $this->resolvePickupForStore($request);

        Log::info('Cart Store - Resolved pickup:', [
            'hotelId' => $hotelId,
            'isOther' => $isOther,
            'other' => $other,
            'mpId' => $mpId,
        ]);

        // Crear item
        $cartItem = CartItem::create([
            'cart_id'          => $cart->cart_id,
            'tour_id'          => (int) $tour->tour_id,
            'tour_date'        => $tourDate,
            'schedule_id'      => (int) $request->schedule_id,
            'tour_language_id' => (int) $request->tour_language_id,
            'categories'       => $categoriesSnapshot,
            'hotel_id'         => $hotelId,
            'is_other_hotel'   => $isOther,
            'other_hotel_name' => $other,
            'meeting_point_id' => $mpId,
            'is_active'        => true,
        ]);

        Log::info('Cart Item created:', [
            'item_id' => $cartItem->item_id,
            'meeting_point_id' => $cartItem->meeting_point_id,
            'hotel_id' => $cartItem->hotel_id,
        ]);

        $successMessage = __('carts.messages.item_added');

        return $request->ajax()
            ? response()->json(['ok' => true, 'message' => $successMessage, 'count' => $this->countRaw($request)])
            : back()->with('success', $successMessage);
    }

    /* ====================== Update ====================== */
    public function update(Request $request, $itemId)
    {
        abort_unless(Auth::check(), 403);

        // 1) Validación (categories pasa a nullable)
        $request->validate([
            'tour_date'        => 'required|date|after_or_equal:today',
            'schedule_id'      => 'required|exists:schedules,schedule_id',
            'tour_language_id' => 'required|exists:tour_languages,tour_language_id',
            'categories'       => 'nullable|array',
            'categories.*'     => 'nullable|integer|min:0',
            'hotel_id'         => 'nullable|integer|exists:hotels_list,hotel_id',
            'is_other_hotel'   => 'boolean',
            'other_hotel_name' => 'nullable|string|max:255|required_if:is_other_hotel,1',
            'meeting_point_id' => 'nullable|integer|exists:meeting_points,id',
        ]);

        // 2) Cart activo
        $cart = $this->activeCartOf($request->user(), withTourSchedules: true);
        if (!$cart || $cart->isExpired()) {
            return back()->with('error', __('carts.messages.cart_expired'));
        }

        // 3) Item
        $item = $cart->items()->where('item_id', $itemId)->first();
        if (!$item) {
            return back()->with('error', __('carts.messages.item_not_found'));
        }

        // 4) Entidades base
        $tour     = $item->tour->load('prices.category');
        $schedule = $this->findValidScheduleOrFail($tour, (int) $request->schedule_id);
        $tourDate = $request->tour_date;

        // 5) Resolver categorías efectivas
        $requestedCategories = $request->input('categories');
        if (is_null($requestedCategories)) {
            $requestedCategories = $this->snapshotToQuantities((array) ($item->categories ?? []));
        }
        $requestedCategories = array_filter(
            array_map('intval', (array) $requestedCategories),
            fn($q) => $q > 0
        );

        // Validación de negocio por categorías
        $validationResult = $this->validation->validateQuantities($tour, $requestedCategories);
        if (!$validationResult['valid']) {
            $errorMsg = implode(' ', $validationResult['errors']);
            return back()->with('error', $errorMsg);
        }

        // 6) Bloqueos
        if ($this->capacity->isDateBlocked($tour, $schedule, $tourDate)) {
            return back()->with('error', __('carts.messages.date_no_longer_available', [
                'date' => $this->fmtDateEn($tourDate),
                'min' => 1
            ]));
        }

        // 7) Capacidad
        $snap = $this->capacity->capacitySnapshot(
            $tour,
            $schedule,
            $tourDate,
            excludeBookingId: null,
            countHolds: true,
            excludeCartId: (int) $cart->cart_id
        );

        $currentPax = $this->totalFromCategories((array) ($item->categories ?? []));
        $requested  = $this->totalFromCategories($requestedCategories);
        $remaining  = $snap['available'] + $currentPax;

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

        // 8) Pickup - CORREGIDO
        [$hotelId, $isOther, $other, $mpId] = $this->resolvePickupForUpdate($request);

        // 9) Recalcular snapshot
        $categoriesSnapshot = $this->pricing->buildCategoriesSnapshot($tour, $requestedCategories, $tourDate);

        if (empty($categoriesSnapshot)) {
            return back()->with('error', __('m_bookings.validation.no_active_categories'));
        }

        // 10) Guardar cambios
        $item->fill([
            'tour_date'        => $tourDate,
            'schedule_id'      => (int) $schedule->schedule_id,
            'tour_language_id' => (int) $request->tour_language_id,
            'categories'       => $categoriesSnapshot,
            'meeting_point_id' => $mpId,
            'is_other_hotel'   => $isOther,
            'other_hotel_name' => $other,
            'hotel_id'         => $isOther || $mpId ? null : $hotelId,
        ])->save();

        return back()->with('success', __('carts.messages.item_updated'));
    }

    /* ====================== Remove item ====================== */
    public function destroy(Request $request, $itemId)
    {
        abort_unless(Auth::check(), 403);

        $cart = $this->activeCartOf(Auth::user());
        if ($cart) {
            $cart->items()->where('item_id', $itemId)->delete();

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
        if (!Auth::check()) {
            return response()->json([
                'count'   => 0,
                'expired' => false,
            ]);
        }

        $cart = $this->activeCartOf($request->user());

        if (!$cart) {
            return response()->json([
                'count'   => 0,
                'expired' => false,
            ]);
        }

        if (!$cart->items()->count()) {
            $cart->forceExpire();
            return response()->json([
                'count'   => 0,
                'expired' => true,
            ]);
        }

        if ($cart->isExpired()) {
            $this->expireCart($cart);
            return response()->json([
                'count'   => 0,
                'expired' => true,
            ]);
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

        $cart   = $this->activeCartOf($request->user());
        $extend = (int) config('cart.extend_minutes', 10);
        $maxExt = (int) config('cart.max_extensions', 1);

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
            ->with('items.tour.prices.category')
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

    /* ====================== API: Get Categories por Tour (AJAX) ====================== */
    public function getCategories(Tour $tour)
    {
        $locale = app()->getLocale();

        $categories = $tour->prices()
            ->where('tour_prices.is_active', true)
            ->with('category')
            ->orderBy('category_id')
            ->get()
            ->map(function ($price) use ($locale) {
                $cat  = $price->category;
                $slug = $cat->slug ?? '';

                $name = method_exists($cat, 'getTranslatedName')
                    ? ($cat->getTranslatedName($locale) ?: ($cat->name ?? null))
                    : ($cat->name ?? null);

                if (!$name && $slug) {
                    foreach (
                        [
                            "customer_categories.labels.$slug",
                            "m_tours.customer_categories.labels.$slug",
                        ] as $key
                    ) {
                        $tr = __($key);
                        if ($tr !== $key) {
                            $name = $tr;
                            break;
                        }
                    }
                }

                if (!$name) {
                    $name = 'Category #' . (int)$price->category_id;
                }

                return [
                    'id'        => (int)   $price->category_id,
                    'slug'      => (string)$slug,
                    'name'      => (string)$name,
                    'price'     => (float) $price->price,
                    'min'       => (int)   $price->min_quantity,
                    'max'       => (int)   $price->max_quantity,
                    'is_active' => (bool)  $price->is_active,
                ];
            });

        return response()->json($categories);
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

    private function normalizeHotelInput(Request $request): void
    {
        $in = $request->all();
        $in['is_other_hotel'] = (bool)($in['is_other_hotel'] ?? false);

        // Si viene meeting_point_id, anula hotel
        if (!empty($in['meeting_point_id'])) {
            $in['is_other_hotel']  = false;
            $in['other_hotel_name'] = null;
            $in['hotel_id']         = null;
        } else {
            $rawHotel = $in['hotel_id'] ?? null;
            if ($rawHotel === 'other' || $rawHotel === '__custom__' || (isset($rawHotel) && !ctype_digit((string)$rawHotel))) {
                if ($rawHotel === 'other' || $rawHotel === '__custom__') $in['is_other_hotel'] = true;
                $in['hotel_id'] = null;
            }
            if (!empty($in['other_hotel_name'])) {
                $in['is_other_hotel'] = true;
                $in['hotel_id'] = null;
            }
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
        if ($withTourSchedules) $q->with(['items.tour.schedules', 'items.tour.prices.category']);
        return $q->first();
    }

    // CORREGIDO: Ahora busca meeting_point_id directamente
    private function resolvePickupForStore(Request $request): array
    {
        // Buscar meeting_point_id (no selected_meeting_point)
        $mpId = $request->integer('meeting_point_id') ?: null;
        if ($mpId && !MeetingPoint::whereKey($mpId)->exists()) $mpId = null;

        $isOther = $request->boolean('is_other_hotel');
        $hotelId = $isOther ? null : ($request->hotel_id ?: null);
        $other   = $isOther ? ($request->other_hotel_name ?: null) : null;

        if ($mpId) {
            $hotelId = null;
            $isOther = false;
            $other = null;
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
            $hotelId = null;
            $isOther = false;
            $other = null;
        } elseif ($isOther) {
            $mpId = null;
            $hotelId = null;
        } elseif ($hotelId) {
            $mpId = null;
            $isOther = false;
            $other = null;
        }

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
        return Carbon::parse($date)->format('d/M/Y');
    }

    private function cartSubtotal(Cart $cart): float
    {
        return (float) $cart->items->sum(function ($item) {
            return collect($item->categories ?? [])->sum(
                fn($cat) => ((float)($cat['price'] ?? 0)) * ((int)($cat['quantity'] ?? 0))
            );
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

    private function totalFromCategories(array $cats): int
    {
        return array_sum(array_map(fn($q) => (int) $q, $cats));
    }

    private function snapshotToQuantities(array $snapshot): array
    {
        $out = [];
        foreach ($snapshot as $cat) {
            $cid = (int)($cat['category_id'] ?? 0);
            $qty = (int)($cat['quantity'] ?? 0);
            if ($cid > 0 && $qty > 0) {
                $out[$cid] = $qty;
            }
        }
        return $out;
    }
}
