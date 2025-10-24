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

class CartController extends Controller
{
    /* =========================================================
     | Session-based cart helpers (for guests)
     * ========================================================= */
    protected function getSessionItems(Request $request): array
    {
        return (array) $request->session()->get('cart.items', []);
    }

    protected function saveSessionItems(Request $request, array $items): void
    {
        $request->session()->put('cart.items', array_values($items));
    }

    /* =========================================================
     | Cart — Public view
     * ========================================================= */
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

        if (!$cart) {
            $cart = Cart::create([
                'user_id'   => $user->user_id,
                'is_active' => true,
            ])->refreshExpiry(15);
        } else {
            if ($cart->isExpired()) {
                $this->expireCart($cart);
                session()->flash('error', __('carts.messages.cart_expired'));
                $cart = Cart::create([
                    'user_id'   => $user->user_id,
                    'is_active' => true,
                ])->refreshExpiry(15);
            } elseif (!$cart->expires_at || $cart->remainingSeconds() <= 0) {
                $cart->refreshExpiry(15);
            }
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

    /* =========================================================
     | Add item
     * ========================================================= */
/* =========================================================
 | Add item
 * ========================================================= */
public function store(Request $request)
{
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

    $adults = (int)$request->adults_quantity;
    $kids   = (int)($request->kids_quantity ?? 0);
    $tour   = Tour::findOrFail($request->tour_id);
    $expiryMinutes = config('cart.expiry_minutes', 15);

    if (Auth::check()) {
        $user = Auth::user();

        $cart = $user->cart()
            ->where('is_active', true)
            ->orderByDesc('cart_id')
            ->first();

        if (!$cart) {
            $cart = Cart::create(['user_id' => $user->user_id, 'is_active' => true])->refreshExpiry($expiryMinutes);
        } elseif ($cart->isExpired()) {
            $this->expireCart($cart);
            $cart = Cart::create(['user_id' => $user->user_id, 'is_active' => true])->refreshExpiry($expiryMinutes);
        } else {
            $cart->refreshExpiry($expiryMinutes);
        }

        $schedule = $tour->schedules()
            ->where('schedules.schedule_id', $request->schedule_id)
            ->where('schedules.is_active', true)
            ->wherePivot('is_active', true)
            ->first();

        if (!$schedule) {
            return back()->withErrors(['schedule_id' => __('carts.messages.schedule_unavailable')]);
        }

        $isBlocked = TourExcludedDate::where('tour_id', $tour->tour_id)
            ->where(function ($q) use ($request) {
                $q->whereNull('schedule_id')->orWhere('schedule_id', $request->schedule_id);
            })
            ->where('start_date', '<=', $request->tour_date)
            ->where(function ($q) use ($request) {
                $q->where('end_date', '>=', $request->tour_date)->orWhereNull('end_date');
            })
            ->exists();

        if ($isBlocked) {
            return back()->with('error', __('carts.messages.date_blocked'));
        }

        $reserved = DB::table('booking_details')
            ->where('tour_id', $request->tour_id)
            ->where('schedule_id', $request->schedule_id)
            ->where('tour_date', $request->tour_date)
            ->sum(DB::raw('adults_quantity + kids_quantity'));

        $requested = $adults + $kids;
        if ($reserved + $requested > $schedule->max_capacity) {
            return back()->with('error', __('carts.messages.slot_full'));
        }

        $mpId = $request->integer('selected_meeting_point') ?: null;
        $mp   = $mpId ? MeetingPoint::find($mpId) : null;

        CartItem::create([
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

        $cart->refreshExpiry($expiryMinutes);

        $successMessage = __('carts.messages.item_added') . ' ' . __('carts.messages.cart_expires_in', ['minutes' => $expiryMinutes]);

        return $request->ajax()
            ? response()->json([
                'message' => $successMessage,
                'count'   => $this->countRaw($request),
            ])
            : back()->with('success', $successMessage);
    }

    // Guest cart (session)
    $items = $this->getSessionItems($request);
    $items[] = [
        'item_key'         => uniqid('it_', true),
        'tour_id'          => $tour->tour_id,
        'tour_name'        => method_exists($tour, 'getTranslatedName') ? $tour->getTranslatedName() : ($tour->name ?? 'Tour'),
        'tour_date'        => $request->tour_date,
        'schedule_id'      => $request->schedule_id,
        'tour_language_id' => $request->tour_language_id,
        'hotel_id'         => $request->boolean('is_other_hotel') ? null : $request->hotel_id,
        'is_other_hotel'   => $request->boolean('is_other_hotel'),
        'other_hotel_name' => $request->boolean('is_other_hotel') ? $request->other_hotel_name : null,
        'adults_quantity'  => $adults,
        'kids_quantity'    => $kids,
        'adult_price'      => (float)($tour->adult_price ?? 0),
        'kid_price'        => (float)($tour->kid_price   ?? 0),
    ];
    $this->saveSessionItems($request, $items);

    $successMessage = __('carts.messages.item_added') . ' ' . __('carts.messages.cart_expires_in', ['minutes' => $expiryMinutes]);

    return response()->json([
        'message' => $successMessage,
        'count'   => $this->countRaw($request),
    ]);
}

    /* =========================================================
     | Delete item
     * ========================================================= */
    public function destroy(Request $request, $itemIdOrKey)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $cart = $user->cart()
                ->where('is_active', true)
                ->orderByDesc('cart_id')
                ->first();

            if ($cart) {
                $cart->items()->where('item_id', $itemIdOrKey)->delete();
                if (!$cart->isExpired()) {
                    $cart->refreshExpiry(15);
                }
            }

            return back()->with('success', __('carts.messages.item_removed'));
        }

        $items = $this->getSessionItems($request);
        $items = array_values(array_filter($items, fn($it) => ($it['item_key'] ?? null) !== $itemIdOrKey));
        $this->saveSessionItems($request, $items);

        return back()->with('success', __('carts.messages.item_removed'));
    }

    /* =========================================================
     | Count (used by header AJAX)
     * ========================================================= */
    public function count(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['count' => $this->countRaw($request), 'expired' => false]);
        }

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
            'count'     => $count,
            'expired'   => false,
            'remaining' => $cart->remainingSeconds(),
        ]);
    }

    protected function countRaw(Request $request): int
    {
        if (Auth::check()) {
            $cart = Cart::withCount('items')
                ->where('user_id', Auth::id())
                ->where('is_active', true)
                ->orderByDesc('cart_id')
                ->first();
            return (int) ($cart->items_count ?? 0);
        }
        return count($this->getSessionItems($request));
    }

    /* =========================================================
     | Reserved seats (availability check)
     * ========================================================= */
    public function getReserved(Request $request)
    {
        $request->validate([
            'tour_id'     => ['required','integer'],
            'schedule_id' => ['required','integer'],
            'tour_date'   => ['required','date'],
        ]);

        $reserved = DB::table('booking_details')
            ->where('tour_id', $request->tour_id)
            ->where('schedule_id', $request->schedule_id)
            ->where('tour_date', $request->tour_date)
            ->sum(DB::raw('adults_quantity + kids_quantity'));

        return response()->json(['reserved' => $reserved]);
    }

    /* =========================================================
     | Expire cart manually (AJAX)
     * ========================================================= */
    public function expire(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['ok' => true, 'message' => __('carts.messages.no_active_cart')], 200);
        }

        $cart = $user->cart()
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

    /* =========================================================
     | Refresh expiration (AJAX)
     * ========================================================= */
    public function refreshExpiry(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['ok' => false, 'message' => __('carts.messages.no_active_cart')], 401);
        }

        $cart = $user->cart()
            ->where('is_active', true)
            ->orderByDesc('cart_id')
            ->first();

        if (!$cart) {
            $cart = Cart::create(['user_id' => $user->user_id, 'is_active' => true]);
        } elseif ($cart->isExpired()) {
            $this->expireCart($cart);
            $cart = Cart::create(['user_id' => $user->user_id, 'is_active' => true]);
        }

        $cart->refreshExpiry(config('cart.expiry_minutes', 15));

        return response()->json([
            'ok'         => true,
            'expires_at' => $cart->expires_at->toIso8601String(),
            'remaining'  => $cart->remainingSeconds(),
            'message'    => __('carts.messages.cart_refreshed'),
        ]);
    }

    /* =========================================================
     | Internal cleanup for expired carts
     * ========================================================= */
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
}
