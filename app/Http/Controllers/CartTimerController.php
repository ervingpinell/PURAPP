<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartTimerController extends Controller
{
    /** POST /public/carts/refresh-expiry */
    public function refreshExpiry(Request $request)
    {
        $user = Auth::user();
        $cart = $user?->cart()
            ->where('is_active', true)
            ->latest('cart_id')
            ->first();

        if (!$cart) {
            return response()->json([
                'ok' => false,
                'message' => __('carts.messages.cart_empty'),
            ], 404);
        }

        if ($cart->isExpired()) {
            $cart->forceExpire();
            return response()->json([
                'ok'      => false,
                'expired' => true,
                'message' => __('carts.messages.cart_expired'),
            ], 410);
        }

        if (!$cart->canExtend()) {
            // Importante: devolver 429 y la "reason" esperada por el JS
            return response()->json([
                'ok'   => false,
                'reason' => 'limit_reached',
                'used'   => (int) $cart->extended_count,
                'max'    => (int) $cart->maxExtensions(),
                'message'=> __('carts.messages.max_extensions_reached'),
            ], 429);
        }

        // Extiende y persiste
        $cart->extendOnce();

        // Marca en sesión para deshabilitar el botón desde el render
        if ($cart->extended_count >= 1) {
            session()->put('cart_extended_once', true);
        }

        return response()->json([
            'ok'             => true,
            'extended'       => true, // clave que espera el JS
            'expires_at'     => optional($cart->expires_at)->toIso8601String(),
            'used'           => (int) $cart->extended_count,   // total usadas
            'max'            => (int) $cart->maxExtensions(),  // máximo permitido
            'extend_minutes' => (int) $cart->extendMinutes(),
            'message'        => __('carts.messages.extend_success'),
        ]);
    }

    /** POST /public/carts/expire (cuando el tiempo llega a 0) */
    public function expire(Request $request)
    {
        $user = Auth::user();
        $cart = $user?->cart()
            ->where('is_active', true)
            ->latest('cart_id')
            ->first();

        if (!$cart) {
            return response()->json(['ok' => true, 'already' => 'no_cart']);
        }

        $cart->forceExpire();

        return response()->json([
            'ok' => true,
            'message' => __('carts.messages.cart_expired')
        ]);
    }
}
