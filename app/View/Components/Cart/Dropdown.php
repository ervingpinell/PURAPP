<?php

namespace App\View\Components\Cart;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use App\Models\Cart;
use App\Models\CustomerCategory;

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

    /** @var array<int,string> */
    public array $categoryNamesById = [];

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

                // 👇 Construir mapa de nombres traducidos por category_id a partir de snapshots del carrito
                $this->categoryNamesById = $this->buildCategoryNamesMap(
                    $cart->items->pluck('categories')->filter()->all()
                );
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

            // 👇 Construir mapa también para invitados
            $this->categoryNamesById = $this->buildCategoryNamesMap(
                $sessionItems->pluck('categories')->filter()->all()
            );
        }
    }

    /**
     * Recibe un arreglo (posiblemente mixto) de "categories" (snapshots por ítem)
     * y devuelve [category_id => nombre_traducido].
     *
     * @param array<int, mixed> $categoriesSnapshots
     * @return array<int, string>
     */
    private function buildCategoryNamesMap(array $categoriesSnapshots): array
    {
        $ids = collect();

        foreach ($categoriesSnapshots as $cats) {
            if (empty($cats)) continue;

            // Soporta array indexado y asociativo
            if (is_array($cats)) {
                // Si NO es indexado, normalizamos a values()
                $iterable = isset($cats[0]) ? $cats : array_values($cats);
                foreach ($iterable as $c) {
                    if (is_array($c) && !empty($c['category_id'])) {
                        $ids->push((int) $c['category_id']);
                    } elseif (is_array($c) && !empty($c['id'])) {
                        $ids->push((int) $c['id']);
                    }
                }
            }
        }

        $ids = $ids->unique()->values();
        if ($ids->isEmpty()) return [];

        $loc = app()->getLocale();
        $fb  = config('app.fallback_locale', 'es');

        // Carga UNA VEZ las categorías + traducciones
        $rows = CustomerCategory::whereIn('category_id', $ids)
            ->with('translations')
            ->get();

        $map = [];
        foreach ($rows as $cat) {
            // Si tienes helper en el modelo:
            if (method_exists($cat, 'getTranslatedName')) {
                $map[(int)$cat->category_id] = (string) $cat->getTranslatedName($loc);
                continue;
            }

            // Fallback manual con translations:
            $trLoc = optional($cat->translations)->firstWhere('locale', $loc);
            $trFb  = optional($cat->translations)->firstWhere('locale', $fb);

            $name = $trLoc->name
                ?? $trFb->name
                ?? ($cat->display_name ?? $cat->name ?? '');

            if (!$name && !empty($cat->slug)) {
                $name = str($cat->slug)->replace(['_','-'],' ')->title();
            }

            $map[(int)$cat->category_id] = (string) $name;
        }

        return $map;
    }

    public function render(): View|Closure|string
    {
        return view('components.cart.dropdown', [
            'headerCart'        => $this->headerCart,
            'headerCount'       => $this->headerCount,
            'headerTotal'       => $this->headerTotal,
            'variant'           => $this->variant,
            'id'                => $this->id,
            'sessionItems'      => $this->sessionItems,
            'categoryNamesById' => $this->categoryNamesById, // 👈 pasamos el mapa a la vista
        ]);
    }
}
