<?php

namespace App\Support;

use Illuminate\Http\Request;

class LocaleUrl
{
    public static function swap(Request $request, string $toLocale): string
    {
        $supported = (array) config('app.supported_locales', ['es','en','fr','de','pt']);
        if (! in_array($toLocale, $supported, true)) {
            $toLocale = config('app.locale', 'es');
        }

        $segments = $request->segments(); // array sin slashes
        if (count($segments) === 0) {
            return url($toLocale);
        }
        // sustituye el primer segmento por el nuevo locale
        $segments[0] = $toLocale;
        return url(implode('/', $segments)) . ($request->getQueryString() ? ('?'.$request->getQueryString()) : '');
    }
}
