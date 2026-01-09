<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

/**
 * BookingApiController
 *
 * Handles bookingapi operations.
 */
class BookingApiController extends Controller
{
    public function verifyPromo(Request $request)
    {
        $codeRaw  = (string)$request->query('code', '');
        $code     = PromoCode::normalize($codeRaw);
        $subtotal = (float)$request->query('subtotal', 0);

        if (!$code || $subtotal <= 0) {
            return response()->json(['valid' => false, 'message' => 'Datos inválidos'], 422);
        }

        $promo = PromoCode::whereRaw("TRIM(REPLACE(code,' ','')) = ?", [$code])->first();
        if (!$promo) return response()->json(['valid' => false, 'message' => 'Código no encontrado'], 404);
        if (method_exists($promo, 'isValidToday') && !$promo->isValidToday())
            return response()->json(['valid' => false, 'message' => 'Este código ha expirado o aún no es válido'], 422);
        if (method_exists($promo, 'hasRemainingUses') && !$promo->hasRemainingUses())
            return response()->json(['valid' => false, 'message' => 'Este código ha alcanzado su límite de usos'], 422);

        $discountAmount  = 0.0;
        $discountPercent = null;

        if (!is_null($promo->discount_percent)) {
            $discountPercent = (float)$promo->discount_percent;
            $discountAmount  = round($subtotal * ($discountPercent / 100), 2);
        } elseif (!is_null($promo->discount_amount)) {
            $discountAmount = (float)$promo->discount_amount;
        }

        return response()->json([
            'valid'            => true,
            'operation'        => $promo->operation === 'add' ? 'add' : 'subtract',
            'discount_amount'  => $discountAmount,
            'discount_percent' => $discountPercent,
            'message'          => 'Código válido',
        ]);
    }
}
