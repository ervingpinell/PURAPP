<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserRegisterController extends Controller
{
    // Listado con filtros
    public function index(Request $request)
    {
        $roles = Role::all();
        $query = User::with('role');

        if ($request->filled('rol'))    $query->where('role_id', $request->rol);
        if ($request->filled('email'))  $query->where('email', 'like', '%'.$request->email.'%');
        if ($request->filled('estado')) $query->where('status', $request->estado);

        $users = $query->get();
        return view('admin.users.users', compact('users', 'roles'));
    }

    // (no se usa) formulario
    public function create()
    {
        $roles = Role::all();
        return view('auth.register', compact('roles'));
    }

    // Crear usuario
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

            // Normaliza entrada de teléfono (solo quita prefijo si coincide con country_code)
            $ccDigits    = preg_replace('/\D+/', '', (string) $request->country_code); // "506"
            $phoneDigits = preg_replace('/\D+/', '', (string) $request->phone);        // "89002232" o "50689002232"

            $startsWith = function (string $haystack, string $needle): bool {
                if ($needle === '') return false;
                return strncmp($haystack, $needle, strlen($needle)) === 0;
            };

            if ($ccDigits && $startsWith($phoneDigits, $ccDigits)) {
                $national = substr($phoneDigits, strlen($ccDigits));
            } else {
                $national = $phoneDigits ?: null;
            }

            User::create([
                'full_name'    => $request->full_name,
                'email'        => $request->email,
                'password'     => Hash::make($request->password),
                'role_id'      => $request->role_id,
                'status'       => true,
                'country_code' => $request->country_code, // mutator del modelo lo normaliza a "+506"
                'phone'        => $national,              // mutator del modelo deja solo dígitos o null
            ]);

            return redirect()
                ->route('admin.users.index')
                ->with('success', __('adminlte::adminlte.user_registered_successfully'))
                ->with('alert_type', 'creado');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Mantener modal abierto si el único error fue password
            if ($e->validator->errors()->count() === 1 && $e->validator->errors()->has('password')) {
                return redirect()->back()
                    ->withErrors($e->validator)
                    ->with('error_password', $e->validator->errors()->first('password'))
                    ->withInput()
                    ->with('show_register_modal', true);
            }
            throw $e;
        }
    }

    public function edit($id)
    {
        return redirect()->route('admin.users.index');
    }
    public function unlock($id)
{
    $user = User::findOrFail($id);
    $user->is_locked = false;
    $user->save();

    return back()->with('success', 'Usuario desbloqueado.');
}

    // Actualizar usuario
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

        $user->full_name = $request->full_name;
        $user->email     = $request->email;
        $user->role_id   = $request->role_id;

        // Si vienen campos de teléfono, procesarlos (permitimos limpiar a null)
        if ($request->hasAny(['country_code','phone'])) {
            $ccDigits    = preg_replace('/\D+/', '', (string) $request->country_code);
            $phoneDigits = preg_replace('/\D+/', '', (string) $request->phone);

            $startsWith = function (string $haystack, string $needle): bool {
                if ($needle === '') return false;
                return strncmp($haystack, $needle, strlen($needle)) === 0;
            };

            if ($phoneDigits !== '') {
                $national = ($ccDigits && $startsWith($phoneDigits, $ccDigits))
                    ? substr($phoneDigits, strlen($ccDigits))
                    : $phoneDigits;
            } else {
                $national = null;
            }

            $user->country_code = $request->country_code; // mutator normaliza
            $user->phone        = $national;              // mutator deja solo dígitos o null
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

    // Activar/desactivar
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->status = !$user->status;
        $user->save();

        $mensaje = $user->status
            ? ['tipo' => 'activado',     'texto' => __('adminlte::adminlte.user_reactivated_successfully')]
            : ['tipo' => 'desactivado',  'texto' => __('adminlte::adminlte.user_deactivated_successfully')];

        return redirect()
            ->route('admin.users.index')
            ->with('alert_type', $mensaje['tipo'])
            ->with('success', $mensaje['texto']);
    }
}
