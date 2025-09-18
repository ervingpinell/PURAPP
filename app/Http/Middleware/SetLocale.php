<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        $supported = (array) config('app.supported_locales', ['es','en','fr','de','pt_BR']);

        // 1) locale desde route param /?lang= /?locale=
        $candidate = $request->route('locale')
            ?? $request->query('lang')
            ?? $request->query('locale');

        // normaliza guiones y mayúsculas (pt_BR, en, es, fr, de)
        if (is_string($candidate)) {
            $candidate = str_replace('-', '_', trim($candidate));
            if (strlen($candidate) === 5) {
                $candidate = substr($candidate, 0, 2) . '_' . strtoupper(substr($candidate, 3, 2));
            } else {
                $candidate = strtolower($candidate);
            }
        }

        if ($candidate && in_array($candidate, $supported, true)) {
            Session::put('locale', $candidate);
        } elseif (!Session::has('locale')) {
            // 2) primera vez: mejor lenguaje del navegador
            $pref = $request->getPreferredLanguage($supported);
            Session::put('locale', $pref ?: config('app.locale'));
        }

        $locale = Session::get('locale', config('app.locale'));

        // 3) aplica a Laravel + Carbon + setlocale PHP
        App::setLocale($locale);
        Carbon::setLocale($this->carbonLocale($locale));
        $this->applyPhpSetLocale($locale);

        return $next($request);
    }

    private function carbonLocale(string $locale): string
    {
        // Carbon usa es, en, fr, de, pt_BR…
        return $locale;
    }

    private function applyPhpSetLocale(string $locale): void
    {
        // mapea a locales del sistema (ajusta si tu servidor usa otros)
        $map = [
            'es'    => ['es_ES.UTF-8', 'es_ES', 'es'],
            'en'    => ['en_US.UTF-8', 'en_US', 'en'],
            'fr'    => ['fr_FR.UTF-8', 'fr_FR', 'fr'],
            'de'    => ['de_DE.UTF-8', 'de_DE', 'de'],
            'pt_BR' => ['pt_BR.UTF-8', 'pt_BR', 'pt_BR.utf8', 'pt'],
        ];

        $targets = $map[$locale] ?? [$locale . '.UTF-8', $locale];
        @setlocale(LC_TIME, ...$targets);
        @setlocale(LC_MONETARY, ...$targets);
        @setlocale(LC_NUMERIC, ...$targets);
    }
}
