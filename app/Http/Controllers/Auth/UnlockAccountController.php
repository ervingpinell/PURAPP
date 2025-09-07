<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AccountLockedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;

class UnlockAccountController extends Controller
{
    /**
     * Formulario para solicitar reenvío del enlace de desbloqueo (opcional).
     */
    public function form()
    {
        return view('auth.unlock-request'); // crea esta vista si usas el flujo self-service
    }

    /**
     * Envía el enlace de desbloqueo si el email existe y está bloqueado (opcional).
     */
    public function send(Request $request)
    {
        $request->validate(['email' => ['required','email']]);

        $user = User::where('email', mb_strtolower(trim($request->email)))->first();

        if ($user && !empty($user->is_locked)) {
            $url = URL::temporarySignedRoute(
                'unlock.process',
                now()->addMinutes(60),
                ['user' => $user->getKey(), 'hash' => sha1($user->email)]
            );

            try { $user->notify(new AccountLockedNotification($url)); } catch (\Throwable $e) {}
        }

        return back()->with('status', __('auth.unlock.sent_if_exists'));
    }

    /**
     * Procesa el enlace firmado y desbloquea.
     */
    public function process(Request $request, User $user, string $hash)
    {
        // La ruta ya trae middleware('signed'); verificamos hash de email
        if (! hash_equals($hash, sha1($user->email))) {
            abort(403, 'Invalid unlock link');
        }

        // 1) Desbloquear
        $user->is_locked = false;
        $user->save();

        // 2) Limpiar contador de fallos persistente
        Cache::forget('auth:fail:'.$user->getKey());

        // 3) Limpiar throttles (por si luego vuelves a activar alguno)
        $emailLc = mb_strtolower(trim($user->email));
        RateLimiter::clear($emailLc.'|'.$request->ip());
        RateLimiter::clear('verify:'.$user->getKey());

        return redirect()->route('login')
            ->with('status', __('auth.account.unlocked') ?: 'Tu cuenta ha sido desbloqueada. Ya puedes iniciar sesión.');
    }
}
