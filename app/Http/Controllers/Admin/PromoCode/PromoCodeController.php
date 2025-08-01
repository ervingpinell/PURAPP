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
        $code = strtoupper(trim($request->input('code')));
        $total = floatval($request->input('total'));

        $promo = PromoCode::where('code', $code)
            ->where('is_used', false) 
            ->first();

        if (! $promo) {
            return response()->json([
                'success' => false,
                'message' => 'Código inválido o ya utilizado.',
            ], 404);
        }

        // Calcular descuento
        $discount = 0;

        if ($promo->discount_percent !== null) {
            $discount = $total * ($promo->discount_percent / 100);
        } elseif ($promo->discount_amount !== null) {
            $discount = $promo->discount_amount;
        }

        $discount = min($discount, $total); 
        $newTotal = $total - $discount;

        return response()->json([
            'success' => true,
            'discount_applied' => $discount,
            'new_total' => $newTotal,
            'message' => 'Código válido.',
        ]);
    }


}
