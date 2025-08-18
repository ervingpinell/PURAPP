<?php

namespace App\Http\Controllers\Admin\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\TourLanguage;
use App\Models\HotelList;
use App\Models\PromoCode;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($request->routeIs('public.cart.index')) {
            $cart = $user->cart()->where('is_active', true)
                ->with('items.tour', 'items.schedule', 'items.language', 'items.hotel')
                ->first();

            return view('public.cart', ['cart' => $cart, 'client' => $user]);
        }

        $languages = TourLanguage::all();
        $hotels    = HotelList::where('is_active', true)->orderBy('name')->get();

        $cart = $user->cart()->where('is_active', true)->first();

        if (!$cart) {
            $emptyCart = new \stdClass;
            $emptyCart->items = collect();

            return view('admin.Cart.cart', compact('languages', 'hotels') + ['cart' => $emptyCart]);
        }

        $itemsQuery = CartItem::with(['tour','schedule','language','hotel'])
            ->where('cart_id', $cart->cart_id);

        if ($request->filled('estado')) {
            $itemsQuery->where('is_active', $request->estado);
        }

        $cart->items = $itemsQuery->get();

        return view('admin.Cart.cart', compact('cart', 'languages', 'hotels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tour_id'          => 'required|exists:tours,tour_id',
            'tour_date'        => 'required|date',
            'schedule_id'      => 'required|exists:schedules,schedule_id',
            'tour_language_id' => 'required|exists:tour_languages,tour_language_id',
            'hotel_id'         => 'nullable|integer|exists:hotels_list,hotel_id',
            'is_other_hotel'   => 'boolean',
            'other_hotel_name' => 'nullable|string|max:255',
            'adults_quantity'  => 'required|integer|min:1',
            'kids_quantity'    => 'nullable|integer|min:0|max:2',
        ]);

        $user = Auth::user();
        $cart = $user->cart()->where('is_active', true)->first();

        if (!$cart) {
            $cart = Cart::create(['user_id' => $user->user_id, 'is_active' => true]);
        }

 $tour = \App\Models\Tour::findOrFail($request->tour_id);

// Valida que el horario pertenece al tour y que está activo global + pivote
$schedule = $tour->schedules()
    ->where('schedules.schedule_id', $request->schedule_id)
    ->where('schedules.is_active', true)
    ->wherePivot('is_active', true)
    ->first();

if (!$schedule) {
    return back()->withErrors([
        'schedule_id' => 'El horario seleccionado no está disponible para este tour (inactivo o no asignado).'
    ]);
}


        $isBlocked = \App\Models\TourExcludedDate::where('tour_id', $tour->tour_id)
            ->where('schedule_id', $request->schedule_id)
            ->where('start_date', '<=', $request->tour_date)
            ->where(function ($q) use ($request) {
                $q->where('end_date', '>=', $request->tour_date)->orWhereNull('end_date');
            })->exists();

        if ($isBlocked) {
            return back()->with('error', __('adminlte::adminlte.blocked_date_for_tour', [
                'date' => $request->tour_date,
                'tour' => $tour->name,
            ]));
        }

        $reserved = DB::table('booking_details')
            ->where('tour_id', $request->tour_id)
            ->where('schedule_id', $request->schedule_id)
            ->where('tour_date', $request->tour_date)
            ->sum(DB::raw('adults_quantity + kids_quantity'));

        $requested = $request->adults_quantity + ($request->kids_quantity ?? 0);

        if ($reserved + $requested > $schedule->max_capacity) {
            return back()->with('error', __('adminlte::adminlte.tourCapacityFull'));
        }

        CartItem::create([
            'cart_id'          => $cart->cart_id,
            'tour_id'          => $request->tour_id,
            'tour_date'        => $request->tour_date,
            'schedule_id'      => $request->schedule_id,
            'tour_language_id' => $request->tour_language_id,
            'hotel_id'         => $request->is_other_hotel ? null : $request->hotel_id,
            'is_other_hotel'   => $request->is_other_hotel ?? false,
            'other_hotel_name' => $request->is_other_hotel ? $request->other_hotel_name : null,
            'adults_quantity'  => $request->adults_quantity,
            'kids_quantity'    => $request->kids_quantity ?? 0,
        ]);

        return $request->ajax()
            ? response()->json(['message' => __('adminlte::adminlte.cartItemAdded')])
            : back()->with('success', __('adminlte::adminlte.cartItemAdded'));
    }

    public function getReserved(Request $request)
    {
        $reserved = DB::table('booking_details')
            ->where('tour_id', $request->tour_id)
            ->where('schedule_id', $request->schedule_id)
            ->where('tour_date', $request->tour_date)
            ->sum(DB::raw('adults_quantity + kids_quantity'));

        return response()->json(['reserved' => $reserved]);
    }

    public function update(Request $request, CartItem $item)
    {
        $data = $request->validate([
            'tour_date'        => 'required|date',
            'adults_quantity'  => 'required|integer|min:1',
            'kids_quantity'    => 'nullable|integer|min:0|max:2',
            'schedule_id'      => 'nullable|exists:schedules,schedule_id',
            'is_active'        => 'nullable|boolean',
            'hotel_id'         => 'nullable|exists:hotels_list,hotel_id',
            'is_other_hotel'   => 'required|boolean',
            'other_hotel_name' => 'nullable|string|max:255',
        ]);

        $item->update([
            'tour_date'        => $data['tour_date'],
            'adults_quantity'  => $data['adults_quantity'],
            'kids_quantity'    => $data['kids_quantity'] ?? 0,
            'schedule_id'      => $data['schedule_id'] ?? $item->schedule_id,
            'is_active'        => $data['is_active'] ?? false,
            'hotel_id'         => $data['is_other_hotel'] ? null : $data['hotel_id'],
            'is_other_hotel'   => $data['is_other_hotel'],
            'other_hotel_name' => $data['is_other_hotel'] ? $data['other_hotel_name'] : null,
        ]);

        return back()->with('success', __('adminlte::adminlte.itemUpdated'));
    }

    public function updateFromPost(Request $request, CartItem $item)
    {
        $validated = $request->validate([
            'tour_date'       => ['required','date'],
            'adults_quantity' => ['required','integer','min:1'],
            'kids_quantity'   => ['nullable','integer','min:0','max:2'],
            'schedule_id'     => ['sometimes','nullable','exists:schedules,schedule_id'],
        ]);

        if (!$request->has('is_active')) {
            $item->delete();
            return back()->with('success', __('adminlte::adminlte.cartItemDeleted'));
        }

        $item->update([
            'tour_date'       => $validated['tour_date'],
            'adults_quantity' => (int) ($validated['adults_quantity']),
            'kids_quantity'   => array_key_exists('kids_quantity', $validated) ? (int) $validated['kids_quantity'] : 0,
            'schedule_id'     => array_key_exists('schedule_id', $validated) ? $validated['schedule_id'] : $item->schedule_id,
            'is_active'       => true,
        ]);

        return back()->with('success', __('adminlte::adminlte.itemUpdated'));
    }

    public function destroy(CartItem $item)
    {
        $item->delete();
        return back()->with('success', __('adminlte::adminlte.cartItemDeleted'));
    }

    public function destroyCart(\App\Models\Cart $cart)
{
    // Si quieres, valida permisos aquí (ej: Gate::authorize('delete', $cart);)

    \DB::transaction(function () use ($cart) {
        // Borra primero los ítems (por si no tienes ON DELETE CASCADE)
        $cart->items()->delete();
        $cart->delete();
    });

    return back()->with('success', __('adminlte::adminlte.cartDeleted') ?? 'Carrito eliminado correctamente.');
}
private function adminCartSubtotal(\App\Models\Cart $cart): float
{
    return (float) $cart->items->sum(function ($it) {
        $ap = (float)($it->tour->adult_price ?? 0);
        $kp = (float)($it->tour->kid_price   ?? 0);
        $aq = (int)($it->adults_quantity ?? 0);
        $kq = (int)($it->kids_quantity   ?? 0);
        return ($ap * $aq) + ($kp * $kq);
    });
}

/**
 * Aplica cupón en el carrito ADMIN (AJAX)
 * - Valida que exista
 * - Que no esté marcado como usado (one-shot)
 * - Calcula descuento por monto fijo o % (si ambos existen, se suman)
 * - Guarda en sesión 'admin_cart_promo'
 */
public function applyPromoAdmin(Request $request)
{
    $request->validate(['code' => ['required','string','max:50']]);

    $user = Auth::user();
    $cart = $user->cart()->where('is_active', true)
        ->with('items.tour')->first();

    if (!$cart || !$cart->items->count()) {
        return response()->json(['ok' => false, 'message' => 'No hay ítems en el carrito.'], 422);
    }

    $code = strtoupper(trim($request->code));
    $promo = PromoCode::whereRaw('UPPER(code) = ?', [$code])->first();

    if (!$promo) {
        return response()->json(['ok' => false, 'message' => 'Código inválido.'], 422);
    }
    if ($promo->is_used) {
        return response()->json(['ok' => false, 'message' => 'Este código ya fue utilizado.'], 422);
    }

    $subtotal = $this->adminCartSubtotal($cart);

    // Calcula descuento (se suman amount + percent si ambos existen)
    $discountFixed = max(0.0, (float)($promo->discount_amount ?? 0));
    $discountPerc  = max(0.0, (float)($promo->discount_percent ?? 0));
    $discountFromPerc = round($subtotal * ($discountPerc / 100), 2);

    $discount = min($subtotal, round($discountFixed + $discountFromPerc, 2));
    $newTotal = max(0, round($subtotal - $discount, 2));

    // Guarda en sesión separada para ADMIN
    session([
        'admin_cart_promo' => [
            'code'      => $promo->code,
            'amount'    => $discountFixed,
            'percent'   => $discountPerc,
            'discount'  => $discount,
            'subtotal'  => $subtotal,
            'new_total' => $newTotal,
            'applied_at'=> now()->toISOString(),
        ]
    ]);

    // Texto amigable
    $parts = [];
    if ($discountFixed > 0) $parts[] = '$'.number_format($discountFixed, 2);
    if ($discountPerc  > 0) $parts[] = $discountPerc.'%';
    $label = implode(' + ', $parts);

    return response()->json([
        'ok'        => true,
        'message'   => 'Código aplicado.',
        'code'      => $promo->code,
        'label'     => $label ?: 'Descuento',
        'discount'  => number_format($discount, 2),
        'subtotal'  => number_format($subtotal, 2),
        'new_total' => number_format($newTotal, 2),
    ]);
}

/** Quita cupón en ADMIN */
public function removePromoAdmin(Request $request)
{
    $request->session()->forget('admin_cart_promo');
    return response()->json(['ok' => true, 'message' => 'Cupón eliminado.']);
}


public function allCarts(Request $request)
{
    $estado = $request->query('estado'); // '1', '0' o null

    $query = \App\Models\Cart::query()
        ->with([
            'user',
            'items.tour',
            'items.language',
            'items.schedule',
        ])
        ->withCount('items') // para $cart->items_count en el blade
        ->whereHas('user', function ($q) use ($request) {
            if ($request->filled('correo')) {
                // Si usas MySQL cambia 'ilike' por 'like'
                $q->where('email', 'ilike', '%' . $request->correo . '%');
            }
        })
        ->whereHas('items'); // sólo carritos con ítems

    // ✅ Usa has() (no filled()) y filtra por estado del CARRITO
    if ($request->has('estado') && in_array($estado, ['0','1'], true)) {
        $query->where('is_active', (bool)$estado);
    }

    $carritos = $query->orderByDesc('updated_at')->get();

    // ✅ Calcula total en USD por carrito con precios del tour
    foreach ($carritos as $cart) {
        $cart->total_usd = $cart->items->sum(function ($it) {
            $ap = (float)($it->tour->adult_price ?? 0);
            $kp = (float)($it->tour->kid_price   ?? 0);
            $aq = (int)($it->adults_quantity ?? 0);
            $kq = (int)($it->kids_quantity   ?? 0);
            return ($ap * $aq) + ($kp * $kq);
        });
    }

    return view('admin.Cart.general', compact('carritos'));
}

public function toggleActive(\App\Models\Cart $cart){
    $cart->update(['is_active' => !$cart->is_active]);
    return back()->with('success', 'Estado del carrito actualizado correctamente.');
}

    public function count()
    {
        if (!auth()->check()) {
            return response()->json(['count' => 0]);
        }
        $cart  = auth()->user()->cart;
        $count = $cart ? $cart->items()->where('is_active', true)->count() : 0;
        return response()->json(['count' => $count]);
    }
}
