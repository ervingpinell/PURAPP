<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse as Contract;

class PasswordResetLinkSentResponse implements Contract
{
    public function toResponse($request)
    {
        // Mensaje de éxito con tus traducciones
        return back()->with('status', __('adminlte::auth.passwords.link_sent'));
    }
}
