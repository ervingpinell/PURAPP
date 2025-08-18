<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\User;

class VerifyEmailController extends Controller
{
    // GET /email/verify (requiere auth)
    public function notice(Request $request)
    {
        if ($request->user() && $request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('home'));
        }
        return view('auth.verify-email');
    }

    // GET /email/verify/{id}/{hash} (NO requiere auth; usa 'signed' y throttle)
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // 'signed' ya validó la firma; validamos el hash de email
        $expected = sha1($user->getEmailForVerification());
        if (! hash_equals((string) $hash, $expected)) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => __('El enlace de verificación es inválido o ha expirado.')]);
        }

        if (! $user->hasVerifiedEmail()) {
            if (method_exists($user, 'markEmailAsVerified')) {
                if ($user->markEmailAsVerified()) {
                    event(new Verified($user));
                }
            } else {
                $user->forceFill(['email_verified_at' => now()])->save();
                event(new Verified($user));
            }
        }

        return redirect()
            ->route('login')
            ->with('status', __('adminlte::auth.verify.verified_success')
                ?: 'Tu correo ha sido verificado. Ya puedes iniciar sesión.');
    }

    // POST /email/verification-notification (requiere auth)
    public function resend(Request $request)
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->hasVerifiedEmail()) {
            return back()->with('status', __('adminlte::auth.verify.already_verified') ?: 'Tu correo ya está verificado.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', __('adminlte::auth.verify.link_sent') ?: 'Te enviamos un nuevo enlace de verificación.');
    }
}
