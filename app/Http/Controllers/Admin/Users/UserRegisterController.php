<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;

class UserRegisterController extends Controller
{
    /**
     * Lista de usuarios con filtros por rol, estado y email.
     */
    public function index(Request $request)
    {
        $roles = Role::all();
        $query = User::with('role');

        if ($request->filled('rol')) {
            $query->where('role_id', $request->rol);
        }
        if ($request->filled('email')) {
            $query->where('email', 'like', '%'.$request->email.'%');
        }
        if ($request->filled('estado')) {
            $query->where('status', (bool) $request->estado);
        }

        $users = $query->get();

        return view('admin.users.users', compact('users', 'roles'));
    }

    /**
     * (Opcional) Si usas un formulario externo para crear.
     */
    public function create()
    {
        $roles = Role::all();
        return view('auth.register', compact('roles'));
    }

    /**
     * Registrar usuario (desde modal del panel).
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'full_name'    => ['required','string','max:100'],
                'email'        => ['required','string','email','max:200','unique:users,email'],
                'password'     => ['required','string','min:8','confirmed'],
                'role_id'      => ['required','exists:roles,role_id'],
                'country_code' => ['nullable','string','max:8','regex:/^\+?\d{1,4}$/','required_with:phone'],
                'phone'        => ['nullable','string','max:30'],
            ]);

            // Normalización de teléfono: si viene +NN... pegado al número, quítalo
            $ccDigits    = preg_replace('/\D+/', '', (string) $request->country_code);
            $phoneDigits = preg_replace('/\D+/', '', (string) $request->phone);

            $startsWith = static function (string $haystack, string $needle): bool {
                if ($needle === '') return false;
                return strncmp($haystack, $needle, strlen($needle)) === 0;
            };

            $national = null;
            if ($phoneDigits !== '') {
                $national = ($ccDigits && $startsWith($phoneDigits, $ccDigits))
                    ? substr($phoneDigits, strlen($ccDigits))
                    : $phoneDigits;
            }

            User::create([
                'full_name'    => trim($request->full_name),
                'email'        => mb_strtolower(trim($request->email)),
                'password'     => Hash::make($request->password),
                'role_id'      => (int) $request->role_id,
                'status'       => true,
                'country_code' => $request->country_code,
                'phone'        => $national,
                'is_locked'    => false,
            ]);

            return redirect()
                ->route('admin.users.index')
                ->with('success', __('adminlte::adminlte.user_registered_successfully'))
                ->with('alert_type', 'creado');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si solo falló password, reabrir modal de registro
            if ($e->validator->errors()->count() === 1 && $e->validator->errors()->has('password')) {
                return back()
                    ->withErrors($e->validator)
                    ->with('error_password', $e->validator->errors()->first('password'))
                    ->withInput()
                    ->with('show_register_modal', true);
            }
            throw $e;
        }
    }

    /**
     * Redirige a index; edición se maneja vía modal.
     */
    public function edit($id)
    {
        return redirect()->route('admin.users.index');
    }

    /**
     * Actualiza usuario (desde modal).
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'full_name'    => ['required','string','max:100'],
            'email'        => ['required','string','email','max:200','unique:users,email,'.$id.',user_id'],
            'password'     => [
                'nullable','string','min:8',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*(),.?":{}|<>_\-+=]/',
                'confirmed',
            ],
            'role_id'      => ['required','exists:roles,role_id'],
            'country_code' => ['nullable','string','max:8','regex:/^\+?\d{1,4}$/','required_with:phone'],
            'phone'        => ['nullable','string','max:30'],
        ]);

        $user->full_name = trim($request->full_name);
        $user->email     = mb_strtolower(trim($request->email));
        $user->role_id   = (int) $request->role_id;

        if ($request->hasAny(['country_code','phone'])) {
            $ccDigits    = preg_replace('/\D+/', '', (string) $request->country_code);
            $phoneDigits = preg_replace('/\D+/', '', (string) $request->phone);

            $startsWith = static function (string $haystack, string $needle): bool {
                if ($needle === '') return false;
                return strncmp($haystack, $needle, strlen($needle)) === 0;
            };

            $national = null;
            if ($phoneDigits !== '') {
                $national = ($ccDigits && $startsWith($phoneDigits, $ccDigits))
                    ? substr($phoneDigits, strlen($ccDigits))
                    : $phoneDigits;
            }

            $user->country_code = $request->country_code;
            $user->phone        = $national;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('adminlte::adminlte.user_updated_successfully'))
            ->with('alert_type', 'actualizado');
    }

    /**
     * Activa/Inactiva usuario (toggle).
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->status = ! (bool) $user->status;
        $user->save();

        $mensaje = $user->status
            ? ['tipo' => 'activado',     'texto' => __('adminlte::adminlte.user_reactivated_successfully')]
            : ['tipo' => 'desactivado',  'texto' => __('adminlte::adminlte.user_deactivated_successfully')];

        return redirect()
            ->route('admin.users.index')
            ->with('alert_type', $mensaje['tipo'])
            ->with('success', $mensaje['texto']);
    }

    /**
     * BLOQUEAR manualmente (botón).
     */
    public function lock(Request $request, User $user)
    {
        $user->is_locked = true;
        $user->save();

        // Limpia contadores de negocio por si venía con intentos
        RateLimiter::clear('login-fails:'.$user->getKey());

        return back()->with('success', __('adminlte::adminlte.user_locked_successfully') ?? 'Usuario bloqueado.');
    }

    /**
     * DESBLOQUEAR manualmente (botón).
     */
    public function unlock(Request $request, User $user)
    {
        $user->is_locked = false;
        $user->save();

        // Limpia contadores de fallos (negocio) y throttles (email|ip)
        RateLimiter::clear('login-fails:'.$user->getKey());

        $emailLc = mb_strtolower(trim($user->email));
        RateLimiter::clear($emailLc.'|'.$request->ip());

        if ($lastKey = Cache::pull('last_login_key:'.$user->getKey())) {
            RateLimiter::clear($lastKey);
        }

        return back()->with('success', __('adminlte::adminlte.user_unlocked_successfully') ?? 'Usuario desbloqueado.');
    }

    /**
     * Marcar como verificado (opcional) si no lo está.
     */
    public function markVerified(User $user)
    {
        if (empty($user->email_verified_at)) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return back()->with('success', __('m_users.user_marked_verified') ?? 'Usuario marcado como verificado.');
    }
}
