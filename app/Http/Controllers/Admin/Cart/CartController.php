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
use App\Models\PromoCode;
use App\Models\MeetingPoint;
use App\Models\TourExcludedDate;

class CartController extends Controller
{
    /**
     * Display admin cart view for authenticated admin.
     */
    public function index(Request $request)
    {
        $user      = Auth::user();
        $languages = TourLanguage::all();
        $hotels    = HotelList::where('is_active', true)->orderBy('name')->get();

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

        $itemsQuery = CartItem::with([
                'tour', 'schedule', 'language', 'hotel', 'meetingPoint',
            ])
            ->where('cart_id', $cart->cart_id);

        if ($request->filled('status')) {
            $itemsQuery->where('is_active', (bool) $request->status);
        }

        $cart->items = $itemsQuery->get();

        return view('admin.carts.cart', compact('cart', 'languages', 'hotels'));
    }

    /**
     * Update a cart item (admin).
     */
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

            $isBlocked = TourExcludedDate::where('tour_id', $tour->tour_id)
                ->where(function ($q) use ($scheduleId) {
                    $q->whereNull('schedule_id')->orWhere('schedule_id', $scheduleId);
                })
                ->where('start_date', '<=', $data['tour_date'])
                ->where(function ($q) use ($data) {
                    $q->where('end_date', '>=', $data['tour_date'])->orWhereNull('end_date');
                })
                ->exists();

            if ($isBlocked) {
                return back()->with('error', __('carts.messages.date_blocked'));
            }

            $reserved = DB::table('booking_details')
                ->where('tour_id', $tour->tour_id)
                ->where('schedule_id', $scheduleId)
                ->where('tour_date', $data['tour_date'])
                ->sum(DB::raw('adults_quantity + kids_quantity'));

            $requested = (int)$data['adults_quantity'] + (int)($data['kids_quantity'] ?? 0);
            if ($reserved + $requested > $schedule->max_capacity) {
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
            $cart->isExpired() ? $this->expireCart($cart) : $cart->refreshExpiry(15);
        }

        return back()->with('success', __('carts.messages.item_updated'));
    }

    /**
     * Delete a single cart item.
     */
    public function destroy(CartItem $item)
    {
        $cart = $item->cart;
        $item->delete();

        if ($cart && $cart->is_active && !$cart->isExpired()) {
            $cart->refreshExpiry(15);
        }

        return back()->with('success', __('carts.messages.cart_item_deleted'));
    }

    /**
     * Delete a full cart and its items.
     */
    public function destroyCart(Cart $cart)
    {
        DB::transaction(function () use ($cart) {
            $cart->items()->delete();
            $cart->delete();
        });

        return back()->with('success', __('carts.messages.cart_deleted'));
    }

    /**
     * List all carts in admin.
     */
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

    /**
     * Toggle active/inactive.
     */
    public function toggleActive(Cart $cart)
    {
        $cart->update(['is_active' => !$cart->is_active]);
        return back()->with('success', __('carts.messages.cart_status_updated'));
    }

    /**
     * Apply promo code to admin cart.
     */
    public function applyPromoAdmin(Request $request)
    {
        $request->validate(['code' => ['required','string','max:50']]);

        $user = Auth::user();
        $cart = $user->cart()
            ->where('is_active', true)
            ->orderByDesc('cart_id')
            ->with('items.tour')
            ->first();

        if (!$cart || !$cart->items->count()) {
            return response()->json(['ok' => false, 'message' => __('carts.messages.cart_empty')], 422);
        }

        $code  = strtoupper(trim($request->code));
        $promo = PromoCode::whereRaw('UPPER(code) = ?', [$code])->first();

        if (!$promo) {
            return response()->json(['ok' => false, 'message' => __('carts.messages.invalid_code')], 422);
        }

        if ($promo->is_used) {
            return response()->json(['ok' => false, 'message' => __('carts.messages.code_already_used')], 422);
        }

        $subtotal = $this->adminCartSubtotal($cart);
        $discountFixed    = max(0.0, (float)($promo->discount_amount ?? 0));
        $discountPerc     = max(0.0, (float)($promo->discount_percent ?? 0));
        $discountFromPerc = round($subtotal * ($discountPerc / 100), 2);
        $adjustment = round($discountFixed + $discountFromPerc, 2);

        $operation  = $promo->operation === 'add' ? 'add' : 'subtract';
        $sign       = $operation === 'add' ? +1 : -1;
        $newTotal   = max(0, round($subtotal + ($sign * $adjustment), 2));

        session([
            'admin_cart_promo' => [
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

        if ($cart && $cart->is_active && !$cart->isExpired()) {
            $cart->refreshExpiry(15);
        }

        $message = $operation === 'add'
            ? __('carts.messages.code_applied') . ' (' . __('surcharge') . ')'
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

    /**
     * Remove promo from session.
     */
    public function removePromoAdmin(Request $request)
    {
        $request->session()->forget('admin_cart_promo');
        return response()->json(['ok' => true, 'message' => __('carts.messages.code_removed')]);
    }

    /**
     * Compute subtotal for admin.
     */
    private function adminCartSubtotal(Cart $cart): float
    {
        return (float)$cart->items->sum(function ($it) {
            $ap = (float)($it->tour->adult_price ?? 0);
            $kp = (float)($it->tour->kid_price   ?? 0);
            $aq = (int)($it->adults_quantity ?? 0);
            $kq = (int)($it->kids_quantity   ?? 0);
            return ($ap * $aq) + ($kp * $kq);
        });
    }

    /**
     * Expire and clear cart internally.
     */
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
