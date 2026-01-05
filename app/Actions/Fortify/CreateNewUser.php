<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        // Requisitos: ≥8, 1 número y 1 especial de (. ¡ ! @ # $ % ^ & * ( ) _ + -)
        $passwordRegex = '/^(?=.*\d)(?=.*[.\x{00A1}!@#$%^&*()_+\-]).{8,}$/u';

        Validator::make(
            $input,
            [
                'first_name'   => ['required', 'string', 'max:100'],
                'last_name'    => ['required', 'string', 'max:100'],
                'email'        => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email'],
                'country_code' => ['nullable', 'string', 'max:10'], // Make nullable as we use full country logic now
                'phone'        => ['required', 'string', function ($attr, $value, $fail) {
                    $digits = preg_replace('/\D+/', '', (string) $value);
                    if (strlen($digits) < 5 || strlen($digits) > 20) {
                        $fail(__('validation.regex', ['attribute' => __('adminlte::validation.attributes.phone')]));
                    }
                }],
                'address'      => ['required', 'string', 'max:255'],
                'city'         => ['required', 'string', 'max:100'],
                'state'        => ['required', 'string', 'max:100'],
                'zip'          => ['required', 'string', 'max:20'],
                'country'      => ['required', 'string', 'size:2'], // ISO
                'password'     => ['required', 'confirmed', 'min:8', "regex:$passwordRegex"],
            ],
            [
                'first_name.required' => __('adminlte::validation.required_first_name'),
                'last_name.required'  => __('adminlte::validation.required_last_name'),
                'email.required'     => __('adminlte::validation.required_email'),
                'password.required'  => __('adminlte::validation.required_password'),

                'address.required'   => __('Address is required'),
                'city.required'      => __('City is required'),
                'country.required'   => __('Country is required'),

                'password.confirmed' => __('adminlte::validation.custom.password.confirmed'),
                'password.min'       => __('adminlte::validation.custom.password.min'),
                'password.regex'     => __('adminlte::validation.custom.password.regex'),
                'email.unique'       => __('adminlte::validation.custom.email.unique'),
            ]
        )->validate();

        // ⬇️ Creamos el usuario y disparamos el evento Registered
        $fullName = trim($input['first_name']) . ' ' . trim($input['last_name']);

        // Normalización de teléfono: separar country_code y phone
        $countryCode = $input['country_code'] ?? null;
        $phoneField = $input['phone'];

        // Si el teléfono incluye el código de área al inicio, lo removemos
        if ($countryCode && $phoneField) {
            $ccDigits = preg_replace('/\D+/', '', (string) $countryCode);
            $phoneDigits = preg_replace('/\D+/', '', (string) $phoneField);

            // Si el número empieza con el código de área, lo removemos
            if ($ccDigits && str_starts_with($phoneDigits, $ccDigits)) {
                $phoneField = substr($phoneDigits, strlen($ccDigits));
            } else {
                $phoneField = $phoneDigits;
            }
        }

        $user = User::create([
            'full_name'    => $fullName,
            'email'        => mb_strtolower(trim($input['email'])),
            'country_code' => $countryCode,
            'phone'        => $phoneField,
            'address'      => trim($input['address']),
            'city'         => trim($input['city']),
            'state'        => trim($input['state']),
            'zip'          => trim($input['zip']),
            'country'      => trim($input['country']),
            'password'     => Hash::make($input['password']),
            'status'       => true,
            'is_locked'    => false,
        ]);

        $user->assignRole('customer');

        return $user;
    }
}
