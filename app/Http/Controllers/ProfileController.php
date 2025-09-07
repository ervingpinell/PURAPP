<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Muestra la página de edición de perfil.
     * Prepara de forma segura los datos de 2FA para la vista admin.
     */
    public function edit()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // ===== 2FA (usa los helpers del modelo si existen) =====
        $has2FA         = method_exists($user, 'twoFactorEnabled')       ? $user->twoFactorEnabled()        : !empty($user->two_factor_secret);
        $is2FAConfirmed = method_exists($user, 'twoFactorConfirmed')     ? $user->twoFactorConfirmed()      : !empty($user->two_factor_confirmed_at);
        $qrSvg          = method_exists($user, 'safeTwoFactorQrCodeSvg') ? $user->safeTwoFactorQrCodeSvg()  : null;
        $recoveryCodes  = method_exists($user, 'safeRecoveryCodes')      ? $user->safeRecoveryCodes()       : [];

        // Admin (roles 1,2) vs público
        if (in_array((int) $user->role_id, [1, 2], true)) {
            return view('admin.profile.profile', [
                'user'            => $user,
                'has2FA'          => $has2FA,
                'is2FAConfirmed'  => $is2FAConfirmed,
                'qrSvg'           => $qrSvg,
                'recoveryCodes'   => $recoveryCodes,
            ]);
        }

        // Vista pública (sin UI de 2FA)
        return view('profile.edit', compact('user'));
    }

    /**
     * Actualiza los datos del perfil (admin o público).
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $rules = [
            'full_name'    => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:users,email,' . $user->user_id . ',user_id'],
            'country_code' => ['nullable', 'string', 'max:8', 'regex:/^\+?\d{1,4}$/', 'required_with:phone'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'password'     => [
                'nullable', 'string', 'min:8',
                'regex:/[0-9]/',
                'regex:/[.\x{00A1}!@#$%^&*()_+\-]/u', // incluye "¡"
                'confirmed',
            ],
        ];

        $validated = $request->validate($rules);

        $user->full_name = $validated['full_name'];
        $user->email     = $validated['email'];

        // Normalización de teléfono (quita el código país duplicado si viene pegado al número)
        if ($request->hasAny(['country_code', 'phone'])) {
            $ccDigits    = preg_replace('/\D+/', '', (string) $request->country_code);
            $phoneDigits = preg_replace('/\D+/', '', (string) $request->phone);

            $startsWith = fn (string $haystack, string $needle): bool =>
                $needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;

            $national = $phoneDigits !== ''
                ? (($ccDigits && $startsWith($phoneDigits, $ccDigits))
                    ? substr($phoneDigits, strlen($ccDigits))
                    : $phoneDigits)
                : null;

            $user->country_code = $request->country_code;
            $user->phone        = $national;
        }

        // Password opcional
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Redirección según contexto (admin vs público)
        if (in_array((int) $user->role_id, [1, 2], true)) {
            return redirect()
                ->route('admin.profile.edit')
                ->with('success', __('adminlte::adminlte.profile_updated_successfully'));
        }

        return redirect()
            ->route('profile.edit')
            ->with('success', __('adminlte::adminlte.profile_updated_successfully'));
    }

    /* ============================================================
     | Wrappers para rutas admin/públicas (evitan cambiar rutas)
     * ============================================================*/

    /** GET /admin/profile -> si no tienes vista show, redirige al edit admin */
    public function adminShow()
    {
        return redirect()->route('admin.profile.edit');
    }

    /** GET /admin/profile/edit -> usa la misma lógica de edit() */
    public function adminEdit()
    {
        // Opcional: reforzar que sea admin
        $user = Auth::user();
        if (!$user) return redirect()->route('login');
        if (!in_array((int) $user->role_id, [1,2], true)) abort(403);

        return $this->edit();
    }

    /** POST /admin/profile/edit -> delega al update() */
    public function adminUpdate(Request $request)
    {
        return $this->update($request);
    }

    /** GET /profile -> si no tienes show pública, redirige al edit público */
    public function show()
    {
        return redirect()->route('profile.edit');
    }
}
