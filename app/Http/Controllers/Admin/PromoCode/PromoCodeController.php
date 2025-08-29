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
        // Mensajes/labels traducidos para la validación
        $attributes = [
            'code'     => __('m_config.promocode.fields.code'),
            'discount' => __('m_config.promocode.fields.discount'),
            'type'     => __('m_config.promocode.fields.type'),
        ];

        $messages = [
            'required'        => __('validation.required', ['attribute' => ':attribute']),
            'numeric'         => __('validation.numeric',  ['attribute' => ':attribute']),
            'min.numeric'     => __('validation.min.numeric', ['attribute' => ':attribute', 'min' => 0]),
            'in'              => __('validation.in', ['attribute' => ':attribute']),
            'unique'          => __('validation.unique', ['attribute' => ':attribute']),
        ];

        $request->validate([
            'code'     => 'required|string|unique:promo_codes,code',
            'discount' => 'required|numeric|min:0',
            'type'     => 'required|in:percent,amount',
        ], $messages, $attributes);

        if ($request->type === 'percent' && (float) $request->discount > 100) {
            return back()
                ->withErrors(['discount' => __('m_config.promocode.messages.percent_over_100')])
                ->withInput();
        }

        // Normaliza código para evitar duplicados con espacios/mayúsculas
        $normalizedCode = strtoupper(trim(preg_replace('/\s+/', '', $request->code)));

        $exists = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$normalizedCode])->exists();
        if ($exists) {
            return back()
                ->withErrors(['code' => __('m_config.promocode.messages.code_exists_normalized')])
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

        $promo->save();

        return redirect()
            ->route('admin.promoCode.index')
            ->with('success', __('m_config.promocode.messages.created_success'));
    }

    public function destroy(PromoCode $promo)
    {
        $promo->delete();

        return redirect()
            ->route('admin.promoCode.index')
            ->with('success', __('m_config.promocode.messages.deleted_success'));
    }

    public function apply(Request $request)
    {
        try {
            $request->validate([
                'code'    => 'required|string',
                'total'   => 'nullable|numeric|min:0',
                'preview' => 'nullable|boolean',
            ], [], [
                'code'  => __('m_config.promocode.fields.code'),
                'total' => 'total', // opcional: podrías añadir clave si lo muestras en UI
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
                    'message' => __('m_config.promocode.messages.invalid_or_used'),
                ], 200);
            }

            $resp = [
                'success'          => true,
                'valid'            => true,
                'message'          => __('m_config.promocode.messages.valid'),
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
            return response()->json([
                'success' => false,
                'valid'   => false,
                'message' => $e->getMessage(), // Laravel ya localiza estos mensajes
                'errors'  => $e->errors(),
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'valid'   => false,
                'message' => __('m_config.promocode.messages.server_error'),
            ], 200);
        }
    }
}
