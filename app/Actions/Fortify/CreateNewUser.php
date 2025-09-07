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
                'full_name'    => ['required', 'string', 'max:255'],
                'email'        => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email'],
                'country_code' => ['required', 'string', 'max:10'],
                'phone'        => ['required', 'string', function ($attr, $value, $fail) {
                    $digits = preg_replace('/\D+/', '', (string) $value);
                    if (strlen($digits) < 6 || strlen($digits) > 20) {
                        // Mensaje genérico de Laravel con atributo localizado
                        $fail(__('validation.regex', ['attribute' => __('adminlte::validation.attributes.phone')]));
                    }
                }],
                'password'     => ['required', 'confirmed', 'min:8', "regex:$passwordRegex"],
            ],
            [
                // Tus claves
                'full_name.required' => __('adminlte::validation.required_full_name'),
                'email.required'     => __('adminlte::validation.required_email'),
                'password.required'  => __('adminlte::validation.required_password'),

                'password.confirmed' => __('adminlte::validation.custom.password.confirmed'),
                'password.min'       => __('adminlte::validation.custom.password.min'),
                'password.regex'     => __('adminlte::validation.custom.password.regex'),
                'email.unique'       => __('adminlte::validation.custom.email.unique'),
            ]
        )->validate();

        return User::create([
            'full_name'    => trim($input['full_name']),
            'email'        => mb_strtolower(trim($input['email'])),
            'country_code' => $input['country_code'], // mutator normaliza a +NNNN
            'phone'        => $input['phone'],        // mutator deja solo dígitos
            'password'     => Hash::make($input['password']),
            'status'       => true,
            'role_id'      => 3,   // cliente
            'is_locked'    => false,
        ]);
    }
}
