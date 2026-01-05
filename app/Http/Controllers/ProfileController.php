<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\EmailChangeVerificationNotification;
use App\Models\User;

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

        // Extraer first_name y last_name del full_name para edición
        $nameParts = explode(' ', $user->full_name, 2);
        $firstName = $nameParts[0] ?? '';
        $lastName  = $nameParts[1] ?? '';

        // Admin (tenga permiso 'access-admin') vs público
        if ($user->hasRole('super-admin') || $user->can('access-admin')) {
            return view('admin.profile.profile', [
                'user'            => $user,
                'firstName'       => $firstName,
                'lastName'        => $lastName,
                'has2FA'          => $has2FA,
                'is2FAConfirmed'  => $is2FAConfirmed,
                'qrSvg'           => $qrSvg,
                'recoveryCodes'   => $recoveryCodes,
            ]);
        }

        // Vista pública (sin UI de 2FA)
        return view('profile.edit', [
            'user'      => $user,
            'firstName' => $firstName,
            'lastName'  => $lastName,
        ]);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $rules = [
            'first_name'   => ['required', 'string', 'max:100'],
            'last_name'    => ['required', 'string', 'max:100'],
            'email'        => [
                'required',
                'email',
                'max:255',
                // Unicidad contra el email actual (no toca pending_email)
                'unique:users,email,' . $user->user_id . ',user_id',
            ],
            'address'      => ['required', 'string', 'max:255'],
            'city'         => ['required', 'string', 'max:100'],
            'state'        => ['required', 'string', 'max:100'],
            'zip'          => ['required', 'string', 'max:20'],
            'country'      => ['required', 'string', 'size:2'], // ISO code
            'country_code' => ['nullable', 'string', 'max:8', 'regex:/^\+?\d{1,4}$/', 'required_with:phone'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'password'     => [
                'nullable',
                'string',
                'min:8',
                'regex:/[0-9]/',
                'regex:/[.\x{00A1}!@#$%^&*()_+\-]/u', // incluye "¡"
                'confirmed',
            ],
        ];

        $validated = $request->validate($rules);

        $oldEmail = $user->email;
        $newEmail = mb_strtolower(trim($validated['email']));

        // Datos básicos - construir full_name desde first_name + last_name
        $user->full_name = trim($validated['first_name']) . ' ' . trim($validated['last_name']);
        $user->address   = trim($validated['address']);
        $user->city      = trim($validated['city']);
        $user->state     = trim($validated['state']);
        $user->zip       = trim($validated['zip']);
        $user->country   = trim($validated['country']);

        // Normalización de teléfono
        if ($request->hasAny(['country_code', 'phone'])) {
            $ccDigits    = preg_replace('/\D+/', '', (string) $request->country_code);
            $phoneDigits = preg_replace('/\D+/', '', (string) $request->phone);

            $startsWith = fn(string $haystack, string $needle): bool =>
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

        // Guardamos cambios "normales"
        $user->save();

        $emailMessageKey = 'adminlte::adminlte.profile_updated_successfully';

        // Si el email cambió, NO lo actualizamos directo, usamos pending_email
        if ($newEmail !== mb_strtolower(trim($oldEmail))) {
            $token = bin2hex(random_bytes(32));

            $user->forceFill([
                'pending_email'             => $newEmail,
                'pending_email_token'       => $token,
                'pending_email_created_at'  => now(),
            ])->save();

            if (method_exists($user, 'sendEmailChangeVerificationNotification')) {
                // Pasamos el locale actual de la petición (es/en)
                $user->sendEmailChangeVerificationNotification($token, app()->getLocale());
            }

            $emailMessageKey = 'adminlte::adminlte.profile_updated_email_change_pending';
        }

        // Redirección según contexto (admin vs público)
        if ($user->hasRole('super-admin') || $user->can('access-admin')) {
            return redirect()
                ->route('admin.profile.edit')
                ->with('success', __($emailMessageKey));
        }

        return redirect()
            ->route('profile.edit')
            ->with('success', __($emailMessageKey));
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
        if (! ($user->hasRole('super-admin') || $user->can('access-admin'))) abort(403);

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
