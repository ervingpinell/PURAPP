<?php

namespace App\Http\Controllers\Admin\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use App\Models\TourLanguage;
use App\Models\HotelList;

class CartController extends Controller
{
    // Mostrar el carrito del usuario
    public function index(Request $request)
    {
        $user = Auth::user();
        $languages = TourLanguage::all();
        $hotels    = HotelList::where('is_active', true)->orderBy('name')->get();

        // Obtener el carrito activo del usuario
        $cart = $user->cart()->where('is_active', true)->first();

        // Si no hay carrito, devolvemos view con un objeto vacío que tenga items como colección
        if (! $cart) {
            $emptyCart = new \stdClass;
            $emptyCart->items = collect();

            return view('admin.Cart.cart', [
                'cart'      => $emptyCart,
                'languages' => $languages,
                'hotels'    => $hotels,
            ]);
        }

        // Si existe carrito, cargamos sus ítems
        $itemsQuery = CartItem::with([
            'tour',
            'language',
            'hotel',
        ])->where('cart_id', $cart->cart_id);

        // Filtro opcional por estado
        if ($request->filled('estado')) {
            $itemsQuery->where('is_active', $request->estado);
        }

        // Asignamos la colección de items al carrito
        $cart->items = $itemsQuery->get();

        return view('admin.Cart.cart', compact('cart', 'languages', 'hotels'));
    }


    // Agregar un ítem al carrito
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
            'kids_quantity' => 'nullable|integer|min:0|max:2',
        ]);

        $user = Auth::user();
        $cart = $user->cart()->where('is_active', true)->first();

        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $user->user_id,
                'is_active' => true,
            ]);
        }

        CartItem::create([
            'cart_id'          => $cart->cart_id,
            'tour_id'          => $request->tour_id,
            'tour_date'        => $request->tour_date,
            'tour_schedule_id' => $request->tour_schedule_id,
            'tour_language_id' => $request->tour_language_id,
            
            'hotel_id'         => $request->is_other_hotel
                                ? null
                                : $request->hotel_id,
            'is_other_hotel'   => $request->is_other_hotel ?? false,
            'other_hotel_name' => $request->is_other_hotel
                                ? $request->other_hotel_name
                                : null,

            'adults_quantity'  => $request->adults_quantity,
            'kids_quantity'    => $request->kids_quantity ?? 0,
        ]);


        return $request->ajax()
            ? response()->json(['message' => 'Tour agregado al carrito.'])
            : back()->with('success', 'Tour agregado al carrito.');
    }

    // Actualizar un ítem desde modal PATCH
    public function update(Request $request, CartItem $item)
    {
        $data = $request->validate([
            'tour_date'        => 'required|date',
            'adults_quantity'  => 'required|integer|min:1',
            'kids_quantity'    => 'nullable|integer|min:0|max:2',
            'is_active'        => 'nullable|boolean',
            'hotel_id'         => 'nullable|exists:hotels_list,hotel_id',
            'is_other_hotel'   => 'required|boolean',
            'other_hotel_name' => 'nullable|string|max:255',
        ]);

        $item->update([
            'tour_date'        => $data['tour_date'],
            'adults_quantity'  => $data['adults_quantity'],
            'kids_quantity'    => $data['kids_quantity'] ?? 0,
            'is_active'        => $data['is_active'] ?? false,
            
            'hotel_id'         => $data['is_other_hotel']
                                ? null
                                : $data['hotel_id'],
            'is_other_hotel'   => $data['is_other_hotel'],
            'other_hotel_name' => $data['is_other_hotel']
                                ? $data['other_hotel_name']
                                : null,
        ]);


        return back()->with('success','Ítem actualizado correctamente.');
    }


    // Actualizar desde formulario POST (botón Guardar del modal)
    public function updateFromPost(Request $request, CartItem $item)
    {
        $validated = $request->validate([
            'tour_date' => 'required|date',
            'adults_quantity' => 'required|integer|min:1',
            'kids_quantity' => 'nullable|integer|min:0|max:2',
        ]);

        if (!$request->has('is_active')) {
            $item->delete();
            return back()->with('success', 'Ítem eliminado del carrito correctamente.');
        }

        $item->update([
            'tour_date' => $validated['tour_date'],
            'adults_quantity' => $validated['adults_quantity'],
            'kids_quantity' => $validated['kids_quantity'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'Ítem actualizado correctamente.');
    }

    // Eliminar un ítem
    public function destroy(CartItem $item)
    {
        $item->delete();
        return redirect()->back()->with('success', 'Ítem eliminado del carrito.');
    }

    // Vista de todos los carritos para el admin
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
