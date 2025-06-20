<?php

namespace App\Http\Controllers\Admin\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use App\Models\TourLanguage;


class CartController extends Controller
{
    // Muestra el contenido del carrito
    public function index(Request $request)
    {
        $user = Auth::user();
        $languages = TourLanguage::all();

        $cart = $user->cart()->with('user')->first();

        $itemsQuery = CartItem::with(['tour', 'language'])
            ->where('cart_id', $cart->cart_id);

        // Si viene un filtro de estado
        if ($request->filled('estado')) {
            $itemsQuery->where('is_active', $request->estado);
        }

        $cart->items = $itemsQuery->get();

        return view('admin.Cart.cart', compact('cart','languages'));
    }



    // Agrega un tour al carrito
    public function store(Request $request)
    {
        $request->validate([
            'tour_id' => 'required|exists:tours,tour_id',
            'tour_date' => 'required|date',
            'tour_schedule_id' => 'nullable|exists:tour_schedules,tour_schedule_id',
            'tour_language_id' => 'required|exists:tour_languages,tour_language_id',
            'hotel_id' => 'nullable|exists:hotels_list,hotel_id',
            'is_other_hotel' => 'boolean',
            'other_hotel_name' => 'nullable|string|max:255',
            'adults_quantity' => 'required|integer|min:1',
            'kids_quantity' => 'nullable|integer|min:0',
            'adult_price' => 'required|numeric|min:0',
            'kid_price' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        $cart = $user->cart ?? Cart::create([
            'user_id' => $user->user_id,
            'is_active' => true
        ]);

        CartItem::create([
            'cart_id' => $cart->cart_id,
            'tour_id' => $request->tour_id,
            'tour_date' => $request->tour_date,
            'tour_schedule_id' => $request->tour_schedule_id,
            'tour_language_id' => $request->tour_language_id,
            'hotel_id' => $request->hotel_id,
            'is_other_hotel' => $request->is_other_hotel ?? false,
            'other_hotel_name' => $request->other_hotel_name,
            'adults_quantity' => $request->adults_quantity,
            'kids_quantity' => $request->kids_quantity ?? 0,
            'adult_price' => $request->adult_price,
            'kid_price' => $request->kid_price,
            'is_active' => true,
        ]);

        // Si es petición AJAX, retorna JSON
        if ($request->ajax()) {
            return response()->json(['message' => 'Tour agregado al carrito.']);
        }

        return back()->with('success', 'Tour agregado al carrito.');

    }


    // Actualiza cantidad del item en carrito
    public function update(Request $request, CartItem $item)
    {
        $validated = $request->validate([
            'tour_date' => 'required|date',
            'adults_quantity' => 'required|integer|min:1',
            'kids_quantity' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $item->update([
            'tour_date' => $validated['tour_date'],
            'adults_quantity' => $validated['adults_quantity'],
            'kids_quantity' => $validated['kids_quantity'] ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Ítem actualizado correctamente.');
    }


    // Remueve item del carrito
    public function destroy(CartItem $item)
    {
        $item->delete();

        return redirect()->back()->with('success', 'Ítem eliminado del carrito.');
    }

    public function allCarts(Request $request)
    {
        $carritos = Cart::with(['user', 'items' => function ($query) use ($request) {
            if ($request->filled('estado')) {
                $query->where('is_active', $request->estado);
            }
        }, 'items.tour', 'items.language'])
        ->whereHas('user', function ($query) use ($request) {
            if ($request->filled('correo')) {
                $query->where('email', 'ilike', '%' . $request->correo . '%');
            }
        })
        ->whereHas('items') // solo carritos con ítems
        ->get();

        return view('admin.Cart.general', compact('carritos'));
    }

    public function updateFromPost(Request $request, CartItem $item)
    {
        $validated = $request->validate([
            'tour_date' => 'required|date',
            'adults_quantity' => 'required|integer|min:1',
            'kids_quantity' => 'nullable|integer|min:0',
        ]);

        // Si no está activo (checkbox desmarcado), se elimina
        if (!$request->has('is_active')) {
            $item->delete();
            return back()->with('success', 'Ítem eliminado del carrito correctamente.');
        }

        // Si está activo, se actualiza normalmente
        $item->update([
            'tour_date' => $validated['tour_date'],
            'adults_quantity' => $validated['adults_quantity'],
            'kids_quantity' => $validated['kids_quantity'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'Ítem actualizado correctamente.');
    }




    


}