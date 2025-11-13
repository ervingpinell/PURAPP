<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class EmailChangeController extends Controller
{

    public function confirm(Request $request, User $user, string $token)
    {
        // 1) Validar token
        if (! $user->pending_email || ! hash_equals((string) $user->pending_email_token, (string) $token)) {
            abort(403, 'Invalid email change token.');
        }

        // 2) Validar expiración (ej: 2 horas)
        $createdAt = $user->pending_email_created_at
            ? Carbon::parse($user->pending_email_created_at)
            : null;

        if ($createdAt && $createdAt->lt(now()->subHours(2))) {
            // Limpiamos los campos pendientes si expiró
            $user->forceFill([
                'pending_email'            => null,
                'pending_email_token'      => null,
                'pending_email_created_at' => null,
            ])->save();

            return redirect()->route('login')->withErrors([
                'email' => __('auth.email_change_link_expired'),
            ]);
        }

        // 3) Aplicar cambio definitivo
        $user->forceFill([
            'email'                     => $user->pending_email,
            'pending_email'             => null,
            'pending_email_token'       => null,
            'pending_email_created_at'  => null,
            // Este clic cuenta como verificación del nuevo correo
            'email_verified_at'         => now(),
        ])->save();

        // 4) Autologin del usuario (por si no estaba logeado en este navegador)
        Auth::login($user);

        // 5) Redirigir al perfil con mensaje de éxito
        $successMessage = __('auth.email_change_confirmed');

        if (in_array((int) $user->role_id, [1, 2], true)) {
            // Perfil admin
            return redirect()
                ->route('admin.profile.edit')
                ->with('success', $successMessage);
        }

        // Perfil público
        return redirect()
            ->route('profile.edit')
            ->with('success', $successMessage);
    }
}
