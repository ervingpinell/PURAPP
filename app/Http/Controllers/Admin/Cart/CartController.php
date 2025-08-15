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

        $tour     = \App\Models\Tour::findOrFail($request->tour_id);
        $schedule = \App\Models\Schedule::findOrFail($request->schedule_id);

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


public function allCarts(Request $request)
{
    $estado = $request->filled('estado') ? (int) $request->estado : null;

    $carritos = Cart::with([
            'user',
            'items' => function ($q) use ($estado) {
                if (!is_null($estado)) $q->where('is_active', $estado);
            },
            'items.tour',           // precios vienen del tour
            'items.tour.schedules', // para el select de horarios
            'items.language',
            'items.schedule',
        ])
        ->whereHas('user', function ($q) use ($request) {
            if ($request->filled('correo')) {
                // Si usas MySQL, cambia 'ilike' por 'like'
                $q->where('email', 'ilike', '%'.$request->correo.'%');
            }
        })
        ->whereHas('items', function ($q) use ($estado) {
            if (!is_null($estado)) $q->where('is_active', $estado);
        })
        ->get();

    // ✅ Total por carrito en USD usando precios del Tour
    $carritos->transform(function ($cart) {
        $total = $cart->items->sum(function ($it) {
            $ap = (float) (($it->tour->adult_price ?? 0));
            $kp = (float) (($it->tour->kid_price   ?? 0));
            $aq = (int)   ($it->adults_quantity    ?? 0);
            $kq = (int)   ($it->kids_quantity      ?? 0);
            return ($ap * $aq) + ($kp * $kq);
        });
        $cart->total_usd   = $total;
        $cart->items_count = $cart->items->count();
        return $cart;
    });

    return view('admin.Cart.general', compact('carritos'));
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
