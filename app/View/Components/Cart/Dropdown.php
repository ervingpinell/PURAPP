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

            // Si no hay carrito o expiró, no mostramos nada (la API también devolvería 0)
            if ($cart && method_exists($cart, 'isExpired') && $cart->isExpired()) {
                $cart = null;
            }

            if ($cart) {
                $this->headerCart  = $cart;
                $this->headerCount = $cart->items->count();
                $this->headerTotal = (float) $cart->items->sum(function ($i) {
                    $adult = ($i->tour->adult_price ?? 0) * (int)($i->adults_quantity ?? 0);
                    $kid   = ($i->tour->kid_price   ?? 0) * (int)($i->kids_quantity   ?? 0);
                    return $adult + $kid;
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
                $adultPrice = (float)($i['adult_price'] ?? 0);
                $kidPrice   = (float)($i['kid_price'] ?? 0);
                $adultsQty  = (int)  ($i['adults_quantity'] ?? 0);
                $kidsQty    = (int)  ($i['kids_quantity'] ?? 0);
                return ($adultPrice * $adultsQty) + ($kidPrice * $kidsQty);
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
