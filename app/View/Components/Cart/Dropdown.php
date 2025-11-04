<?php

namespace App\View\Components\Cart;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use App\Models\Cart;

class Dropdown extends Component
{
    public string $variant;
    public ?string $id;

    /** @var \App\Models\Cart|null */
    public ?Cart $headerCart = null;

    public int $headerCount = 0;
    public float $headerTotal = 0.0;

    /** @var \Illuminate\Support\Collection|null */
    public $sessionItems = null;

    public function __construct(string $variant = 'desktop', ?string $id = null)
    {
        $this->variant = $variant;
        $this->id = $id;

        // === AUTENTICADO: usa SIEMPRE el carrito ACTIVO más reciente, igual que /cart/count ===
        if (Auth::check()) {
            $cart = Cart::with([
                    // Solo ítems activos para que coincida con la API de count
                    'items' => fn ($q) => $q->where('is_active', true),
                    'items.tour',
                    'items.schedule',
                    'items.language',
                    'items.hotel',
                ])
                ->where('user_id', Auth::id())
                ->where('is_active', true)
                ->orderByDesc('cart_id')
                ->first();

            // Si expiró, lo ignoramos visualmente
            if ($cart && method_exists($cart, 'isExpired') && $cart->isExpired()) {
                $cart = null;
            }

            if ($cart) {
                $this->headerCart  = $cart;
                $this->headerCount = $cart->items->count();
                $this->headerTotal = (float) $cart->items->sum(function ($i) {
                    // Total desde snapshot de categorías (price * quantity)
                    return collect($i->categories ?? [])->sum(
                        fn($cat) => ((float)($cat['price'] ?? 0)) * ((int)($cat['quantity'] ?? 0))
                    );
                });
            }
            return;
        }

        // === INVITADO: carrito en sesión ===
        $sessionItems = collect(session('cart.items', []));
        if ($sessionItems->isNotEmpty()) {
            $this->sessionItems = $sessionItems;
            $this->headerCount  = $sessionItems->count();
            $this->headerTotal  = (float) $sessionItems->sum(function ($i) {
                // Esperamos categories también en sesión
                $cats = $i['categories'] ?? [];
                return collect($cats)->sum(
                    fn($cat) => ((float)($cat['price'] ?? 0)) * ((int)($cat['quantity'] ?? 0))
                );
            });
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.cart.dropdown', [
            'headerCart'    => $this->headerCart,
            'headerCount'   => $this->headerCount,
            'headerTotal'   => $this->headerTotal,
            'variant'       => $this->variant,
            'id'            => $this->id,
            'sessionItems'  => $this->sessionItems,
        ]);
    }
}
