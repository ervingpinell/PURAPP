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
        'full_name'    => ['required', 'string', 'max:255'],
        'email'        => [
            'required',
            'email',
            'max:255',
            // Validamos contra el email actual real, no contra pending_email
            Rule::unique('users', 'email')->ignore($user->getKey(), $user->getKeyName()),
        ],
        'country_code' => ['nullable', 'string', 'max:10'],
        'phone'        => ['nullable', 'string', 'max:25'],
    ])->validateWithBag('updateProfileInformation');

    $oldEmail = $user->email;
    $newEmail = mb_strtolower(trim($input['email']));

    // 2) Actualizar datos básicos del perfil (SIN tocar email todavía)
    $user->forceFill([
        'full_name'    => $input['full_name'],
        'country_code' => $input['country_code'] ?? $user->country_code,
        'phone'        => $input['phone'] ?? $user->phone,
    ])->save();

    // 3) Si el email no cambió, no hacemos nada extra
    if ($newEmail === $oldEmail) {
        return;
    }

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
