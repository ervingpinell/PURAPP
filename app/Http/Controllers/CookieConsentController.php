<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Response;

class CookieConsentController extends Controller
{
public function accept(Request $request): Response
{
    $request->session()->put('cookies.accepted', true);

    Cookie::queue(cookie(
        name:     'gv_cookie_consent',
        value:    '1',
        minutes:  60 * 24 * 365,
        path:     '/',
        domain:   config('session.domain'),
        secure:   (bool) config('session.secure', false),
        httpOnly: false,
        raw:      false,
        sameSite: config('session.same_site', 'lax')
    ));

    return response()->noContent();
}


    public function reject(Request $request): Response
    {
        $request->session()->forget('cookies.accepted');

        Cookie::queue(cookie(
            name:     'gv_cookie_consent',
            value:    '0',
            minutes:  60 * 24 * 365,
            path:     '/',
            domain:   config('session.domain'),
            secure:   (bool) config('session.secure', false),
            httpOnly: false,
            raw:      false,
            sameSite: config('session.same_site', 'lax')
        ));

        return response()->noContent();
    }
}
