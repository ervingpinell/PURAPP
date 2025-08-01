<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromoCode;

class PromoCodeController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'total' => 'required|numeric|min:0',
        ]);

        $code = strtoupper(trim($request->code));
        $total = $request->total;

        $promo = PromoCode::where('code', $code)->first();

        if (! $promo) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code not found.',
            ], 404);
        }

        if ($promo->is_used) {
            return response()->json([
                'success' => false,
                'message' => 'This promo code has already been used.',
            ], 400);
        }

        $newTotal = $promo->applyDiscount($total);

        return response()->json([
            'success' => true,
            'new_total' => round($newTotal, 2),
            'discount_applied' => round($total - $newTotal, 2),
        ]);
    }
}
