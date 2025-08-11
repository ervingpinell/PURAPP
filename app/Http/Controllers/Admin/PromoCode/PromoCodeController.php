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
            'code' => 'required|string|unique:promo_codes,code',
            'discount' => 'required|numeric|min:0',
            'type' => 'required|in:percent,amount',
        ]);

        $promo = new PromoCode();
        $promo->code = strtoupper(trim($request->code));

        if ($request->type === 'percent') {
            $promo->discount_percent = $request->discount;
            $promo->discount_amount = null;
        } else {
            $promo->discount_amount = $request->discount;
            $promo->discount_percent = null;
        }

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
        $code = strtoupper(trim($request->input('code', '')));
        $preview = $request->boolean('preview', true); // por defecto solo validar
        $total = (float) $request->input('total', 0);

        $promo = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$code])
            ->where('is_used', false)
            ->first();

        if (!$promo) {
            return response()->json([
                'valid'   => false,
                'success' => false,
                'message' => 'Código inválido o ya usado.',
            ], 404);
        }

        $resp = [
            'valid'   => true,
            'success' => true,
            'message' => 'Código válido.',
        ];

        // si quieres mostrar el tipo de descuento al usuario
        if ($promo->discount_percent !== null) {
            $resp['discount_percent'] = (float) $promo->discount_percent;
        }
        if ($promo->discount_amount !== null) {
            $resp['discount_amount'] = (float) $promo->discount_amount;
        }

        // sólo si te mandan total y no es preview, calcula montos
        if (!$preview && $total > 0) {
            $discount = 0;
            if ($promo->discount_percent !== null) {
                $discount = $total * ($promo->discount_percent / 100);
            } elseif ($promo->discount_amount !== null) {
                $discount = $promo->discount_amount;
            }
            $discount = min($discount, $total);
            $resp['discount_applied'] = $discount;
            $resp['new_total'] = $total - $discount;
        }

        return response()->json($resp);
    }




}
