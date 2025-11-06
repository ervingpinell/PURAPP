<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Services\Policies\PolicySnapshotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PublicCheckoutController extends Controller
{
    private function findActiveCartForUser(?int $userId): ?Cart
    {
        if (!$userId) {
            return null;
        }

        return Cart::query()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->with([
                'items.tour',
                'items.schedule',
                'items.language',
                'items.hotel',
                'items.meetingPoint',
            ])
            ->latest('updated_at')
            ->first();
    }

    public function show(Request $request, PolicySnapshotService $svc)
    {
        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login');
        }

        $cart = $this->findActiveCartForUser($userId);
        $itemsCount = $cart ? $cart->items()->count() : 0;

        if (!$cart || $itemsCount === 0) {
            return redirect()
                ->route('public.carts.index')
                ->with('error', __('adminlte::adminlte.emptyCart'));
        }

        $pack = $svc->make();

        return view('public.checkout', [
            'cart'           => $cart,
            'termsVersion'   => $pack['versions']['terms']   ?? 'v1.0',
            'privacyVersion' => $pack['versions']['privacy'] ?? 'v1.0',
            'policies'       => $pack['snapshot']            ?? [],
        ]);
    }

    public function process(Request $request, PolicySnapshotService $svc)
    {
        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login');
        }

        $cart = $this->findActiveCartForUser($userId);
        $itemsCount = $cart ? $cart->items()->count() : 0;

        if (!$cart || $itemsCount === 0) {
            return redirect()
                ->route('public.carts.index')
                ->with('error', __('adminlte::adminlte.emptyCart'));
        }

        $request->validate([
            'accept_terms' => ['required', 'accepted'],
            'scroll_ok'    => ['required','in:1'],
        ], [
            'accept_terms.required' => __('Debes aceptar los Términos y Políticas para continuar'),
            'accept_terms.accepted' => __('Debes aceptar los Términos y Políticas para continuar'),
        ]);

        $pack = $svc->make();

        $cart->forceFill([
            'terms_accepted_at' => now(),
            'terms_version'     => $pack['versions']['terms']   ?? 'v1.0',
            'privacy_version'   => $pack['versions']['privacy'] ?? 'v1.0',
            'terms_ip'          => $request->ip(),
            'policies_snapshot' => $pack['snapshot'] ?? [],
            'policies_sha256'   => $pack['sha256']   ?? null,
        ])->save();

        DB::table('terms_acceptances')->insert([
            'user_id'           => $userId,
            'cart_ref'          => $cart->cart_id ?? $cart->id ?? null,
            'booking_ref'       => null,
            'accepted_at'       => now(),
            'terms_version'     => $cart->terms_version,
            'privacy_version'   => $cart->privacy_version,
            'policies_snapshot' => json_encode($pack['snapshot'] ?? []),
            'policies_sha256'   => $pack['sha256'] ?? null,
            'ip_address'        => $request->ip(),
            'user_agent'        => (string) $request->userAgent(),
            'locale'            => app()->getLocale(),
            'timezone'          => config('app.timezone'),
            'consent_source'    => 'checkout',
            'referrer'          => $request->headers->get('referer'),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // Continúa con la creación de la reserva
        return app(\App\Http\Controllers\Bookings\BookingController::class)->storeFromCart($request);
    }
}
