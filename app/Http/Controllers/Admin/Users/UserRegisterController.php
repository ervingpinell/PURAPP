<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use App\Services\LoggerHelper;

/**
 * UserRegisterController
 *
 * Handles userregister operations.
 */
class UserRegisterController extends Controller
{
    /**
     * Lista de usuarios con filtros por rol, estado y email.
     */
    public function index(Request $request)
    {
        // Usar roles de Spatie con filtro de jerarquía
        $currentUser = auth()->user();
        $rolesQ = \Spatie\Permission\Models\Role::query();

        if ($currentUser->hasRole('super-admin')) {
            // Super admin ve todo menos super-admin
            $rolesQ->where('name', '!=', 'super-admin');
        } elseif ($currentUser->hasRole('admin')) {
            // Admin no ve super-admin ni admin
            $rolesQ->whereNotIn('name', ['super-admin', 'admin']);
        } else {
            // Fallback
            $rolesQ->whereNotIn('name', ['super-admin', 'admin']);
        }
        $roles = $rolesQ->get();

        $query = User::query();

        // Excluir usuarios que sean super-admin
        $query->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'super-admin');
        });

        if ($request->filled('rol')) {
            // Filter by Spatie role
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('id', $request->rol);
            });
        }
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
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
                'first_name'   => ['required', 'string', 'max:50'],
                'last_name'    => ['required', 'string', 'max:50'],
                'email'        => ['required', 'string', 'email', 'max:200', 'unique:users,email'],
                'password'     => ['required', 'string', 'min:8', 'confirmed'],
                'role_id'      => ['required', 'exists:roles,id'],
                'country_code' => ['nullable', 'string', 'max:8', 'regex:/^\+?\d{1,4}$/', 'required_with:phone'],
                'phone'        => ['nullable', 'string', 'max:30'],
                'address'      => ['nullable', 'string', 'max:255'],
                'city'         => ['nullable', 'string', 'max:100'],
                'state'        => ['nullable', 'string', 'max:100'],
                'zip'          => ['nullable', 'string', 'max:20'],
                'country'      => ['nullable', 'string', 'size:2'],
            ]);

            // Validación de jerarquía de roles
            $roleToCheck = \Spatie\Permission\Models\Role::find($request->role_id);
            $currentUser = $request->user();

            if ($roleToCheck) {
                // Nadie puede asignar super-admin desde la UI
                if ($roleToCheck->name === 'super-admin') {
                    return back()->with('error', 'No se puede asignar el rol de Super Admin.')->withInput();
                }
                // Si intenta asignar Admin y no es Super Admin
                if ($roleToCheck->name === 'admin' && !$currentUser->hasRole('super-admin')) {
                    return back()->with('error', 'No tienes permiso para asignar el rol de Admin.')->withInput();
                }
            }

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

            $user = User::create([
                'first_name'   => trim($request->first_name),
                'last_name'    => trim($request->last_name),
                'email'        => mb_strtolower(trim($request->email)),
                'password'     => Hash::make($request->password),
                'status'       => true,
                'country_code' => $request->country_code,
                'phone'        => $national,
                'is_locked'    => false,
                'address'      => $request->address,
                'city'         => $request->city,
                'state'        => $request->state,
                'zip'          => $request->zip,
                'country'      => $request->country,
            ]);

            // Asignar rol usando Spatie
            $role = \Spatie\Permission\Models\Role::findOrFail($request->role_id);
            $user->syncRoles([$role->name]);

            LoggerHelper::mutated('UserRegisterController', 'store', 'User', $user->user_id);

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
        $currentUser = $request->user();

        // Protección: No puedes editar tu propio rol (solo super-admin puede)
        if (!$currentUser->hasRole('super-admin') && $currentUser->user_id == $user->user_id) {
            $currentRoleId = $user->getRoleNames()->first();
            $newRole = \Spatie\Permission\Models\Role::find($request->role_id);
            if ($newRole && $currentRoleId != $newRole->name) {
                return redirect()->back()->with('error', 'No puedes cambiar tu propio rol.');
            }
        }

        $request->validate([
            'first_name'   => ['required', 'string', 'max:50'],
            'last_name'    => ['required', 'string', 'max:50'],
            'email'        => ['required', 'string', 'email', 'max:200', 'unique:users,email,' . $id . ',user_id'],
            'password'     => [
                'nullable',
                'string',
                'min:8',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*(),.?":{}|<>_\-+=]/',
                'confirmed',
            ],
            'role_id'      => ['required', 'exists:roles,id'],
            'country_code' => ['nullable', 'string', 'max:8', 'regex:/^\+?\d{1,4}$/', 'required_with:phone'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'address'      => ['nullable', 'string', 'max:255'],
            'city'         => ['nullable', 'string', 'max:100'],
            'state'        => ['nullable', 'string', 'max:100'],
            'zip'          => ['nullable', 'string', 'max:20'],
            'country'      => ['nullable', 'string', 'size:2'],
        ]);

        // Validación de jerarquía de roles
        $roleToCheck = \Spatie\Permission\Models\Role::find($request->role_id);
        if ($roleToCheck) {
            // Nadie puede asignar super-admin
            if ($roleToCheck->name === 'super-admin') {
                return back()->with('error', 'No se puede asignar el rol de Super Admin.');
            }
            // Si intenta asignar Admin y no es Super Admin
            if ($roleToCheck->name === 'admin' && !$currentUser->hasRole('super-admin')) {
                return back()->with('error', 'No tienes permiso para asignar el rol de Admin.');
            }
        }

        $user->first_name = trim($request->first_name);
        $user->last_name  = trim($request->last_name);
        $user->email      = mb_strtolower(trim($request->email));
        $user->address    = $request->address;
        $user->city       = $request->city;
        $user->state      = $request->state;
        $user->zip        = $request->zip;
        $user->country    = $request->country;

        // Usar Spatie para asignar rol en lugar de role_id
        $role = \Spatie\Permission\Models\Role::findOrFail($request->role_id);
        $user->syncRoles([$role->name]);

        if ($request->hasAny(['country_code', 'phone'])) {
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

        LoggerHelper::mutated('UserRegisterController', 'update', 'User', $user->user_id);

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('adminlte::adminlte.user_updated_successfully'))
            ->with('alert_type', 'actualizado');
    }

    /**
     * Soft delete usuario.
     */
    public function destroy($id)
    {
        $this->authorize('soft-delete-users');

        $user = User::findOrFail($id);

        // Prevent self-deletion
        if (auth()->id() == $user->user_id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // Prevent deleting super-admin
        if ($user->hasRole('super-admin')) {
            return back()->with('error', 'No se puede eliminar un Super Admin.');
        }

        $user->delete(); // Soft delete

        LoggerHelper::mutated('UserRegisterController', 'destroy', 'User', $user->user_id);

        return redirect()
            ->route('admin.users.index')
            ->with('alert_type', 'eliminado')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * BLOQUEAR manualmente (botón).
     */
    public function lock(Request $request, User $user)
    {
        $user->is_locked = true;
        $user->save();

        LoggerHelper::mutated('UserRegisterController', 'lock', 'User', $user->user_id);

        // Limpia contadores de negocio por si venía con intentos
        RateLimiter::clear('login-fails:' . $user->getKey());

        return back()->with('success', __('adminlte::adminlte.user_locked_successfully') ?? 'Usuario bloqueado.');
    }

    /**
     * DESBLOQUEAR manualmente (botón).
     */
    public function unlock(Request $request, User $user)
    {
        $user->is_locked = false;
        $user->save();

        LoggerHelper::mutated('UserRegisterController', 'unlock', 'User', $user->user_id);

        // Limpia contadores de fallos (negocio) y throttles (email|ip)
        RateLimiter::clear('login-fails:' . $user->getKey());

        $emailLc = mb_strtolower(trim($user->email));
        RateLimiter::clear($emailLc . '|' . $request->ip());

        if ($lastKey = Cache::pull('last_login_key:' . $user->getKey())) {
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
            LoggerHelper::mutated('UserRegisterController', 'markVerified', 'User', $user->user_id);
        }

        return back()->with('success', __('m_users.user_marked_verified') ?? 'Usuario marcado como verificado.');
    }

    /**
     * Desactivar 2FA para un usuario.
     */
    public function disable2FA(User $user)
    {
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        LoggerHelper::mutated('UserRegisterController', 'disable2FA', 'User', $user->user_id);

        return back()->with('success', '2FA desactivado exitosamente para ' . $user->full_name);
    }

    /**
     * Ver usuarios eliminados (soft deleted).
     */
    public function trashed()
    {
        $this->authorize('hard-delete-users');

        $users = User::onlyTrashed()->get();
        $roles = \Spatie\Permission\Models\Role::all();

        return view('admin.users.trashed', compact('users', 'roles'));
    }

    /**
     * Restaurar usuario eliminado.
     */
    public function restore($id)
    {
        $this->authorize('hard-delete-users');

        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        LoggerHelper::mutated('UserRegisterController', 'restore', 'User', $user->user_id);

        return redirect()
            ->route('admin.users.trashed')
            ->with('success', 'Usuario restaurado exitosamente.');
    }

    /**
     * Eliminar usuario permanentemente (hard delete).
     */
    public function forceDelete($id)
    {
        $this->authorize('hard-delete-users');

        $user = User::withTrashed()->findOrFail($id);

        // Prevent force deleting super-admin
        if ($user->hasRole('super-admin')) {
            return back()->with('error', 'No se puede eliminar permanentemente un Super Admin.');
        }

        $userName = $user->full_name;
        $user->forceDelete();

        LoggerHelper::mutated('UserRegisterController', 'forceDelete', 'User', $id);

        return redirect()
            ->route('admin.users.trashed')
            ->with('success', "Usuario {$userName} eliminado permanentemente.");
    }
}
