<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    public function update($user, array $input): void
    {
        $passwordRegex = '/^(?=.*\d)(?=.*[.\x{00A1}!@#$%^&*()_+\-]).{8,}$/u';

        Validator::make(
            $input,
            [
                'current_password' => ['required', 'current_password'],
                'password'         => ['required', 'confirmed', 'min:8', "regex:$passwordRegex"],
            ],
            [
                'password.confirmed' => __('adminlte::validation.custom.password.confirmed'),
                'password.min'       => __('adminlte::validation.custom.password.min'),
                'password.regex'     => __('adminlte::validation.custom.password.regex'),
            ]
        )->validate();

        $user->forceFill(['password' => Hash::make($input['password'])])->save();
    }
}
