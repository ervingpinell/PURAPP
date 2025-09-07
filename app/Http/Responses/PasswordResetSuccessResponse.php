<?php

namespace App\Http\Responses;

class PasswordResetSuccessResponse
{
    public function toResponse($request)
    {
        // Al completar el reset, vuelve al login con tu flash traducido
        return redirect()
            ->route('login')
            ->with('status', __('adminlte::auth.passwords.reset'));
    }
}
