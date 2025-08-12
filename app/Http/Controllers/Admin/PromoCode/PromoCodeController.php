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
        // Validación básica
        $request->validate([
            'code'     => 'required|string|unique:promo_codes,code',
            'discount' => 'required|numeric|min:0',
            'type'     => 'required|in:percent,amount',
        ]);

        // Reglas adicionales: porcentaje <= 100 si aplica
        if ($request->type === 'percent' && (float)$request->discount > 100) {
            return back()
                ->withErrors(['discount' => 'El porcentaje no puede ser mayor a 100.'])
                ->withInput();
        }

        // Normaliza el código: quita espacios internos y convierte a mayúsculas
        $normalizedCode = strtoupper(trim(preg_replace('/\s+/', '', $request->code)));

        // Unicidad case/space-insensitive a nivel app (además del unique simple)
        $exists = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$normalizedCode])->exists();
        if ($exists) {
            return back()
                ->withErrors(['code' => 'Este código (ignorando espacios y mayúsculas) ya existe.'])
                ->withInput();
        }

        $promo = new PromoCode();
        // Guarda normalizado (para evitar variaciones con espacios)
        $promo->code = $normalizedCode;

        if ($request->type === 'percent') {
            $promo->discount_percent = (float) $request->discount;
            $promo->discount_amount  = null;
        } else {
            $promo->discount_amount  = (float) $request->discount;
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
        try {
            $request->validate([
                'code'    => 'required|string',
                'total'   => 'nullable|numeric|min:0',
                'preview' => 'nullable|boolean',
            ]);

            $code    = strtoupper(trim(preg_replace('/\s+/', '', $request->input('code', ''))));
            $total   = (float) $request->input('total', 0);
            $preview = $request->boolean('preview', true);

            $promo = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$code])
                ->where('is_used', false)
                ->first();

            if (!$promo) {
                return response()->json([
                    'success' => false,
                    'valid'   => false,
                    'message' => 'Código inválido o ya usado.',
                ], 200); // <- SIEMPRE 200
            }

            $resp = [
                'success'          => true,
                'valid'            => true,
                'message'          => 'Código válido.',
                'code'             => $promo->code,
                'type'             => $promo->discount_percent !== null ? 'percent' : 'amount',
                'discount_percent' => $promo->discount_percent !== null ? (float) $promo->discount_percent : null,
                'discount_amount'  => $promo->discount_amount  !== null ? (float) $promo->discount_amount  : null,
                'preview'          => $preview,
            ];

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
            // Evita redirecciones 422 que el front interpreta como “error del sistema”
            return response()->json([
                'success' => false,
                'valid'   => false,
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 200);
        } catch (\Throwable $e) {
            // Falla controlada
            return response()->json([
                'success' => false,
                'valid'   => false,
                'message' => 'Error del servidor, inténtalo de nuevo.',
            ], 200);
        }
    }

}
