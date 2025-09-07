<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\FailedPasswordResetResponse as Contract;

class PasswordResetFailedResponse implements Contract
{
    protected string $status;

    public function __construct(string $status)
    {
        $this->status = $status;
    }

    public function toResponse($request)
    {
        $map = [
            'passwords.token'      => 'adminlte::auth.passwords.token',
            'passwords.user'       => 'adminlte::auth.passwords.user',
            'passwords.throttled'  => 'adminlte::auth.passwords.throttled',
        ];

        $key = $map[$this->status] ?? 'adminlte::validation.invalid_credentials';

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($key)]);
    }
}
