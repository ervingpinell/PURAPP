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
        // Atributos y mensajes localizados
        $attributes = [
            'code'        => __('m_config.promocode.fields.code'),
            'discount'    => __('m_config.promocode.fields.discount'),
            'type'        => __('m_config.promocode.fields.type'),
            'valid_from'  => __('m_config.promocode.fields.valid_from'),
            'valid_until' => __('m_config.promocode.fields.valid_until'),
            'usage_limit' => __('m_config.promocode.fields.usage_limit'),
        ];

        $messages = [
            'required'                    => __('validation.required', ['attribute' => ':attribute']),
            'string'                      => __('validation.string',   ['attribute' => ':attribute']),
            'unique'                      => __('validation.unique',   ['attribute' => ':attribute']),
            'numeric'                     => __('validation.numeric',  ['attribute' => ':attribute']),
            'integer'                     => __('validation.integer',  ['attribute' => ':attribute']),
            'min.numeric'                 => __('validation.min.numeric', ['attribute' => ':attribute', 'min' => 0]),
            'min.integer'                 => __('validation.min.numeric', ['attribute' => ':attribute', 'min' => 1]),
            'in'                          => __('validation.in', ['attribute' => ':attribute']),
            'date'                        => __('validation.date', ['attribute' => ':attribute']),
            'valid_until.after_or_equal'  => __('validation.after_or_equal', ['attribute' => ':attribute', 'date' => __('m_config.promocode.fields.valid_from')]),
        ];

        $rules = [
            'code'        => 'required|string|unique:promo_codes,code',
            'discount'    => 'required|numeric|min:0',
            'type'        => 'required|in:percent,amount',
            'valid_from'  => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'usage_limit' => 'nullable|integer|min:1', // null = ilimitado
        ];

        $request->validate($rules, $messages, $attributes);

        // Validación adicional de % > 100
        if ($request->type === 'percent' && (float) $request->discount > 100) {
            return back()
                ->withErrors(['discount' => __('m_config.promocode.messages.percent_over_100')])
                ->withInput();
        }

        // Normaliza código (usa método del modelo si existe; si no, fallback)
        if (method_exists(PromoCode::class, 'normalize')) {
            $normalizedCode = PromoCode::normalize($request->code);
        } else {
            $normalizedCode = strtoupper(trim(preg_replace('/\s+/', '', (string) $request->code)));
        }

        // Unicidad ignorando espacios y mayúsculas
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

        // Vigencia
        $promo->valid_from  = $request->input('valid_from');
        $promo->valid_until = $request->input('valid_until');

        // Usos
        $promo->usage_limit = $request->filled('usage_limit') ? (int) $request->usage_limit : null; // null = ilimitado
        $promo->usage_count = 0;

        $promo->save();

        return redirect()
            ->route('admin.promoCode.index')
            ->with('success', 'm_config.promocode.messages.created_success');
    }

    public function destroy(PromoCode $promo)
    {
        $promo->delete();

        return redirect()
            ->route('admin.promoCode.index')
            ->with('success', 'm_config.promocode.messages.deleted_success');
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
                'total' => 'total',
            ]);

            // Normaliza búsqueda
            if (method_exists(PromoCode::class, 'normalize')) {
                $code = PromoCode::normalize($request->input('code', ''));
            } else {
                $code = strtoupper(trim(preg_replace('/\s+/', '', (string) $request->input('code', ''))));
            }

            $total   = (float) $request->input('total', 0);
            $preview = $request->boolean('preview', true);

            $promo = PromoCode::whereRaw("UPPER(TRIM(REPLACE(code,' ',''))) = ?", [$code])->first();

            // Reglas de validez personalizadas (asumiendo helpers en el modelo)
            if (!$promo || (method_exists($promo, 'isValidToday') && ! $promo->isValidToday()) ||
                (method_exists($promo, 'hasRemainingUses') && ! $promo->hasRemainingUses())) {
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
                'remaining_uses'   => property_exists($promo, 'remaining_uses') ? $promo->remaining_uses : null,
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
                'message' => $e->getMessage(),
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
