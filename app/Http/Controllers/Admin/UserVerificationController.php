<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserVerificationController extends Controller
{

    public function resend(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return back()->with('status', __('adminlte::auth.verify.already_verified') ?? 'El usuario ya tiene el correo verificado.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', __('adminlte::auth.verify.link_sent'));
    }
}
