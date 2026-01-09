<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

/**
 * PublicEmailVerificationController
 *
 * Handles publicemailverification operations.
 */
class PublicEmailVerificationController extends Controller
{
    public function __invoke(Request $request, $id, $hash)
    {
        /** @var \App\Models\User $user */
        $user = User::findOrFail($id);

        // Valida hash (sha1 del email)
        if (! hash_equals((string) $hash, sha1((string) $user->email))) {
            abort(403, 'Invalid verification hash.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('status', __('auth.verify.already'));
        }

        $user->forceFill(['email_verified_at' => now()])->save();
        event(new Verified($user));

        return redirect()->route('login')->with('status', __('auth.verify.verified'));
    }
}
