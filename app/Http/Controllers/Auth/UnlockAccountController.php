<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;

class UnlockAccountController extends Controller
{
    // GET /unlock-account/{user}/{hash}  (ruta firmada)
    public function process(Request $request, User $user, string $hash)
    {
        if ($request->has('signature') && ! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired signed link');
        }

        if (! hash_equals($hash, sha1($user->email))) {
            abort(403, 'Invalid unlock link');
        }

        // 1) Quita flag
        $user->is_locked = false;
        $user->save();

        // 2) Limpia RateLimiter guardado con la clave original
        if ($lockKey = cache()->pull('lock_key:'.$user->id)) {
            RateLimiter::clear($lockKey);
        }

        // 3) Por si el IP cambió, limpia combinación común
        $altKey = mb_strtolower(trim($user->email)).'|'.$request->ip();
        RateLimiter::clear($altKey);

        // 4) Limpia throttle de reenvíos de verificación (si existe)
        RateLimiter::clear('verify:'.$user->id);

        return redirect()->route('login')->with('status', __('adminlte::auth.account.unlocked'));
    }
}
