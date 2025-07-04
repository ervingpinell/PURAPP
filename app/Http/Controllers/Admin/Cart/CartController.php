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
    // ✅ Mostrar el carrito del usuario
    public function index(Request $request)
    {
        $user = Auth::user();
        $languages = TourLanguage::all();
        $hotels    = HotelList::where('is_active', true)->orderBy('name')->get();

        $cart = $user->cart()->where('is_active', true)->first();

        if (! $cart) {
            $emptyCart = new \stdClass;
            $emptyCart->items = collect();

            return view('admin.Cart.cart', [
                'cart'      => $emptyCart,
                'languages' => $languages,
                'hotels'    => $hotels,
            ]);
        }

        $itemsQuery = CartItem::with(['tour','schedule', 'language', 'hotel'])
            ->where('cart_id', $cart->cart_id);

        if ($request->filled('estado')) {
            $itemsQuery->where('is_active', $request->estado);
        }

        $cart->items = $itemsQuery->get();

        return view('admin.Cart.cart', compact('cart', 'languages', 'hotels'));
    }

    // ✅ Agregar ítem al carrito con validación de cupo
    public function store(Request $request)
    {
        $request->validate([
            'tour_id'          => 'required|exists:tours,tour_id',
            'tour_date'        => 'required|date',
            'schedule_id'      => 'required|exists:schedules,schedule_id',
            'tour_language_id' => 'required|exists:tour_languages,tour_language_id',
            'hotel_id'         => 'nullable|exists:hotels_list,hotel_id',
            'is_other_hotel'   => 'boolean',
            'other_hotel_name' => 'nullable|string|max:255',
            'adults_quantity'  => 'required|integer|min:1',
            'kids_quantity'    => 'nullable|integer|min:0|max:2',
        ]);

        $user = Auth::user();
        $cart = $user->cart()->where('is_active', true)->first();

        if (! $cart) {
            $cart = Cart::create([
                'user_id'   => $user->user_id,
                'is_active' => true,
            ]);
        }

        // ✅ Validar cupo antes de guardar
        $reserved = DB::table('booking_details')
            ->where('tour_id', $request->tour_id)
            ->where('schedule_id', $request->schedule_id)
            ->where('tour_date', $request->tour_date)
            ->sum(DB::raw('adults_quantity + kids_quantity'));

        $requested = $request->adults_quantity + ($request->kids_quantity ?? 0);

        $tour = \App\Models\Tour::findOrFail($request->tour_id);
        if ($reserved + $requested > $tour->max_capacity) {
            return back()->with('error', 'El cupo disponible para este horario está lleno.');
        }

        // ✅ Crear ítem en carrito
        CartItem::create([
            'cart_id'          => $cart->cart_id,
            'tour_id'          => $request->tour_id,
            'tour_date'        => $request->tour_date,
            'schedule_id' => $request->schedule_id,
            'tour_language_id' => $request->tour_language_id,
            'hotel_id'         => $request->is_other_hotel ? null : $request->hotel_id,
            'is_other_hotel'   => $request->is_other_hotel ?? false,
            'other_hotel_name' => $request->is_other_hotel ? $request->other_hotel_name : null,
            'adults_quantity'  => $request->adults_quantity,
            'kids_quantity'    => $request->kids_quantity ?? 0,
        ]);

        return $request->ajax()
            ? response()->json(['message' => 'Tour agregado al carrito.'])
            : back()->with('success', 'Tour agregado al carrito.');
    }

    // ✅ Validar cupo por AJAX (llamada desde JS)
    public function getReserved(Request $request)
    {
        $reserved = DB::table('booking_details')
            ->where('tour_id', $request->tour_id)
            ->where('schedule_id', $request->schedule_id)
            ->where('tour_date', $request->tour_date)
            ->sum(DB::raw('adults_quantity + kids_quantity'));

        return response()->json(['reserved' => $reserved]);
    }

    // ✅ Actualizar ítem desde modal
    public function update(Request $request, CartItem $item)
    {
        $data = $request->validate([
            'tour_date'        => 'required|date',
            'adults_quantity'  => 'required|integer|min:1',
            'kids_quantity'    => 'nullable|integer|min:0|max:2',
            'schedule_id' => 'nullable|exists:schedules,schedule_id',
            'is_active'        => 'nullable|boolean',
            'hotel_id'         => 'nullable|exists:hotels_list,hotel_id',
            'is_other_hotel'   => 'required|boolean',
            'other_hotel_name' => 'nullable|string|max:255',
        ]);

        $item->update([
            'tour_date'        => $data['tour_date'],
            'adults_quantity'  => $data['adults_quantity'],
            'kids_quantity'    => $data['kids_quantity'] ?? 0,
            'schedule_id' => $data['schedule_id'],
            'is_active'        => $data['is_active'] ?? false,
            'hotel_id'         => $data['is_other_hotel'] ? null : $data['hotel_id'],
            'is_other_hotel'   => $data['is_other_hotel'],
            'other_hotel_name' => $data['is_other_hotel'] ? $data['other_hotel_name'] : null,
        ]);

        return back()->with('success', 'Ítem actualizado correctamente.');
    }

    // ✅ Actualizar desde POST (botón Guardar)
    public function updateFromPost(Request $request, CartItem $item)
    {
        $validated = $request->validate([
            'tour_date'        => 'required|date',
            'adults_quantity'  => 'required|integer|min:1',
            'kids_quantity'    => 'nullable|integer|min:0|max:2',
            'schedule_id'      => 'nullable|exists:schedules,schedule_id',
        ]);

        if (!$request->has('is_active')) {
            $item->delete();
            return back()->with('success', 'Ítem eliminado del carrito correctamente.');
        }

        $item->update([
            'tour_date'       => $validated['tour_date'],
            'adults_quantity' => $validated['adults_quantity'],
            'kids_quantity'   => $validated['kids_quantity'] ?? 0,
            'schedule_id'      => $validated['schedule_id'],
            'is_active'       => true,
        ]);

        return back()->with('success', 'Ítem actualizado correctamente.');
    }

    // ✅ Eliminar ítem
    public function destroy(CartItem $item)
    {
        $item->delete();
        return back()->with('success', 'Ítem eliminado del carrito.');
    }

    // ✅ Vista de todos los carritos
    public function allCarts(Request $request)
    {
        $carritos = Cart::with([
            'user',
            'items' => function ($q) use ($request) {
                if ($request->filled('estado')) {
                    $q->where('is_active', $request->estado);
                }
            },
            'items.tour',
            'items.language',
            'items.schedule',
        ])
        ->whereHas('user', function ($q) use ($request) {
            if ($request->filled('correo')) {
                $q->where('email', 'ilike', '%' . $request->correo . '%');
            }
        })
        ->whereHas('items')
        ->get();

        return view('admin.Cart.general', compact('carritos'));
    }
}
