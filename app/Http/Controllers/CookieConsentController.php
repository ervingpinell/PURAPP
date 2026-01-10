<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Response;

/**
 * CookieConsentController
 *
 * Manages cookie consent preferences.
 */
class CookieConsentController extends Controller
{
    public function accept(Request $request): Response
    {
        $request->session()->put('cookies.accepted', true);
        $request->session()->put('cookies.accepted_at', now()->toIso8601String());

        Cookie::queue(cookie(
            name: 'gv_cookie_consent',
            value: '1',
            minutes: 60 * 24 * 365,
            path: '/',
            domain: config('session.domain'),
            secure: (bool) config('session.secure', false),
            httpOnly: true,  // ✅ Proteger contra XSS
            raw: false,
            sameSite: config('session.same_site', 'lax')  // ✅ Alinear con sesión (none para Alignet)
        ));

        return response()->noContent();
    }


    public function reject(Request $request): Response
    {
        $request->session()->forget('cookies.accepted');
        $request->session()->put('cookies.rejected_at', now()->toIso8601String());

        Cookie::queue(cookie(
            name: 'gv_cookie_consent',
            value: '0',
            minutes: 60 * 24 * 365,
            path: '/',
            domain: config('session.domain'),
            secure: (bool) config('session.secure', false),
            httpOnly: true,  // ✅ Proteger contra XSS
            raw: false,
            sameSite: config('session.same_site', 'lax')  // ✅ Alinear con sesión (none para Alignet)
        ));

        return response()->noContent();
    }
}
