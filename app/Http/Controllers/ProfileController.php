<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        return in_array($user->role_id, [1, 2])
            ? view('admin.profile.profile', compact('user'))
            : view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'full_name'    => ['required','string','max:255'],
            'email'        => ['required','email','unique:users,email,'.$user->user_id.',user_id'],
            'country_code' => ['nullable','string','max:8','regex:/^\+?\d{1,4}$/','required_with:phone'],
            'phone'        => ['nullable','string','max:30'],
            'password'     => [
                'nullable','string','min:8',
                'regex:/[0-9]/',
                'regex:/[.:!@#$%^&*()_+\-]/',
                'confirmed',
            ],
        ];

        $validated = $request->validate($rules);

        $user->full_name = $validated['full_name'];
        $user->email     = $validated['email'];

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

            $user->country_code = $request->country_code;
            $user->phone        = $national;
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->back()->with('success', __('adminlte::adminlte.profile_updated_successfully'));
    }
}
