<?php

namespace App\View\Components\Cart;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use App\Models\Cart;

class Dropdown extends Component
{
    /** @var 'mobile'|'desktop' */
    public string $variant;

    /** @var string|null */
    public ?string $id;

    // Datos que expondrá el componente a la vista
    public ?Cart $headerCart = null;
    public int $headerCount = 0;
    public float $headerTotal = 0.0;

    /**
     * Create a new component instance.
     */
    public function __construct(string $variant = 'desktop', ?string $id = null)
    {
        $this->variant = $variant;
        $this->id = $id;

        // Cargar datos del carrito (no hay lógica en el Blade del header)
        if (Auth::check()) {
            $cart = Cart::with(['items.tour','items.schedule','items.language','items.hotel'])
                ->where('user_id', Auth::id())
                ->first();

            if ($cart) {
                $this->headerCart  = $cart;
                $this->headerCount = $cart->items->count();
                $this->headerTotal = (float) $cart->items->sum(function ($i) {
                    $adult = ($i->tour->adult_price ?? 0) * ($i->adults_quantity ?? 0);
                    $kid   = ($i->tour->kid_price   ?? 0) * ($i->kids_quantity   ?? 0);
                    return $adult + $kid;
                });
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cart.dropdown', [
            'headerCart'  => $this->headerCart,
            'headerCount' => $this->headerCount,
            'headerTotal' => $this->headerTotal,
            'variant'     => $this->variant,
            'id'          => $this->id,
        ]);
    }
}
