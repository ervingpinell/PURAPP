<?php

namespace App\Http\Controllers\Admin\PromoCode;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromoCode;

class PromoCodeController extends Controller
{
    public function index()
    {
        $promoCodes = PromoCode::latest()->get();
        return view('admin.promoCode.index', compact('promoCodes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'         => 'required|string|unique:promo_codes,code',
            'discount'     => 'required|numeric|min:0',
            'type'         => 'required|in:percent,amount',
            'valid_from'   => 'nullable|date',
            'valid_until'  => 'nullable|date|after_or_equal:valid_from',
            'usage_limit'  => 'nullable|integer|min:1', // null = ilimitado
        ]);

        if ($request->type === 'percent' && (float)$request->discount > 100) {
            return back()
                ->withErrors(['discount' => 'El porcentaje no puede ser mayor a 100.'])
                ->withInput();
        }

        $normalizedCode = PromoCode::normalize($request->code);

        // Unicidad ignorando espacios y mayúsculas
        $exists = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$normalizedCode])->exists();
        if ($exists) {
            return back()
                ->withErrors(['code' => 'Este código (ignorando espacios y mayúsculas) ya existe.'])
                ->withInput();
        }

        $promo = new PromoCode();
        $promo->code = $normalizedCode;

        if ($request->type === 'percent') {
            $promo->discount_percent = (float) $request->discount;
            $promo->discount_amount  = null;
        } else {
            $promo->discount_amount  = (float) $request->discount;
            $promo->discount_percent = null;
        }

        // Vigencia (pueden ser null)
        $promo->valid_from  = $request->input('valid_from');
        $promo->valid_until = $request->input('valid_until');

        // Límite de usos (null = ilimitado)
        $promo->usage_limit = $request->filled('usage_limit') ? (int)$request->usage_limit : null;
        $promo->usage_count = 0;

        $promo->save();

        return redirect()->route('admin.promoCode.index')->with('success', 'Promo code created successfully.');
    }

    public function destroy(PromoCode $promo)
    {
        $promo->delete();
        return redirect()->route('admin.promoCode.index')->with('success', 'Promo code deleted successfully.');
    }

    public function apply(Request $request)
    {
        try {
            $request->validate([
                'code'    => 'required|string',
                'total'   => 'nullable|numeric|min:0',   
                'preview' => 'nullable|boolean',
                'items'           => 'nullable|array',
                'items.*.total'   => 'required_with:items|numeric|min:0',
            ]);

            $code    = \App\Models\PromoCode::normalize($request->input('code', ''));
            $preview = $request->boolean('preview', true);

            $promo = \App\Models\PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$code])->first();

            if (!$promo || ! $promo->isValidToday() || ! $promo->hasRemainingUses()) {
                return response()->json([
                    'success' => false,
                    'valid'   => false,
                    'message' => 'Código inválido, fuera de vigencia o sin usos disponibles.',
                ], 200);
            }

            // Respuesta base
            $resp = [
                'success'          => true,
                'valid'            => true,
                'message'          => 'Código válido.',
                'code'             => $promo->code,
                'type'             => $promo->discount_percent !== null ? 'percent' : 'amount',
                'discount_percent' => $promo->discount_percent !== null ? (float) $promo->discount_percent : null,
                'discount_amount'  => $promo->discount_amount  !== null ? (float) $promo->discount_amount  : null,
                'preview'          => $preview,
                'remaining_uses'   => $promo->remaining_uses, // null = ilimitado
            ];

            // Si mandan items → devolvemos desglose por ítem
            $items = collect($request->input('items', []))
                ->map(fn($it) => [
                    'total' => (float) ($it['total'] ?? 0),
                ]);

            if ($items->isNotEmpty()) {
                $isPercent   = $promo->discount_percent !== null;
                $percent     = (float) $promo->discount_percent;
                $amount      = (float) $promo->discount_amount;
                $unlimited   = is_null($promo->usage_limit);
                $remaining   = $unlimited ? PHP_INT_MAX : max(0, (int)$promo->usage_limit - (int)$promo->usage_count);

                // Estrategia: aplicar a ítems de mayor subtotal primero
                $indexed = $items->values()->map(function ($it, $i) {
                    return ['index' => $i, 'base_total' => (float)$it['total']];
                });

                $sorted = $indexed->sortByDesc('base_total')->values();
                $applyCount = min($remaining, $sorted->count());

                $applyIndexes = $sorted->take($applyCount)->pluck('index')->all();

                $perItem = [];
                $cartDiscountTotal = 0.0;
                $cartNewTotal      = 0.0;

                foreach ($items as $i => $it) {
                    $base = (float)$it['total'];
                    $applied = in_array($i, $applyIndexes, true);

                    $disc = 0.0;
                    if ($applied) {
                        if ($isPercent) {
                            $disc = round($base * ($percent / 100), 2);
                        } else {
                            $disc = min($amount, $base);
                        }
                    }
                    $newT = round(max($base - $disc, 0), 2);

                    $cartDiscountTotal += $disc;
                    $cartNewTotal      += $newT;

                    $perItem[] = [
                        'index'            => $i,
                        'base_total'       => round($base, 2),
                        'applied'          => $applied,
                        'discount_applied' => round($disc, 2),
                        'new_total'        => $newT,
                    ];
                }

                $resp['items_result']        = $perItem;
                $resp['cart_discount_total'] = round($cartDiscountTotal, 2);
                $resp['cart_new_total']      = round($cartNewTotal, 2);
                $resp['applied_items']       = count($applyIndexes);
                $resp['remaining_uses_after_preview'] = $unlimited
                    ? null
                    : max(0, (int)$remaining - (int)count($applyIndexes));

                return response()->json($resp, 200);
            }

            // Legacy: si no mandan items, seguimos con "total"
            $total = (float) $request->input('total', 0);
            if ($total > 0) {
                $discount = 0.0;
                if ($promo->discount_percent !== null) {
                    $discount = round($total * ($promo->discount_percent / 100), 2);
                } elseif ($promo->discount_amount !== null) {
                    $discount = (float) $promo->discount_amount;
                }
                $discount = min($discount, $total);

                $resp['discount_applied'] = $discount;
                $resp['new_total']        = round($total - $discount, 2);
            }

            return response()->json($resp, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'valid'   => false,
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'valid'   => false,
                'message' => 'Error del servidor, inténtalo de nuevo.',
            ], 200);
        }
    }

}
