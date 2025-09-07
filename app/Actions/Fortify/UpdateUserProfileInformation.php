<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function update($user, array $input): void
    {
        Validator::make(
            $input,
            [
                'full_name'    => ['required', 'string', 'max:255'],
                'email'        => [
                    'required', 'string', 'email:rfc,dns', 'max:255',
                    Rule::unique('users', 'email')->ignore($user->getKey(), $user->getKeyName()),
                ],
                'country_code' => ['nullable', 'string', 'max:10'],
                'phone'        => ['nullable', 'string', function ($attr, $value, $fail) {
                    if ($value === null || $value === '') return;
                    $digits = preg_replace('/\D+/', '', (string) $value);
                    if ($digits !== '' && (strlen($digits) < 6 || strlen($digits) > 20)) {
                        $fail(__('validation.regex', ['attribute' => __('adminlte::validation.attributes.phone')]));
                    }
                }],
            ],
            [
                'email.unique' => __('adminlte::validation.custom.email.unique'),
            ]
        )->validate();

        $oldEmail = $user->email;

        $user->forceFill([
            'full_name'    => trim($input['full_name']),
            'email'        => mb_strtolower(trim($input['email'])),
            'country_code' => $input['country_code'] ?? $user->country_code,
            'phone'        => $input['phone'] ?? $user->phone,
        ])->save();

        if ($oldEmail !== $user->email) {
            $user->email_verified_at = null;
            $user->save();
            if (method_exists($user, 'sendEmailVerificationNotification')) {
                $user->sendEmailVerificationNotification();
            }
        }
    }
}
