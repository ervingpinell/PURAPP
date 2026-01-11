<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function update($user, array $input): void
    {
        /** @var \App\Models\User $user */

        // 1) Validar datos
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');


        // 4) Generar token de cambio de email
        $token = bin2hex(random_bytes(32));

        $user->forceFill([
            'pending_email'             => $newEmail,
            'pending_email_token'       => $token,
            'pending_email_created_at'  => now(),
            // OJO: NO tocamos aquí email ni email_verified_at
        ])->save();

        // 5) Enviar correo de confirmación de cambio de email
        if (method_exists($user, 'sendEmailChangeVerificationNotification')) {
            $user->sendEmailChangeVerificationNotification($token, app()->getLocale());
        }
    }
}
