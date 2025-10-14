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
        $routesLocales      = (array) config('routes.locales', []);   // ['es'=>..,'en'=>..,'pt'=>..]
        $supportedPrefixes  = array_keys($routesLocales);             // ['es','en','fr','de','pt']
        $defaultPrefix      = (string) config('routes.default_locale', 'es');

        // 1) Prefijo en URL
        $segments  = $request->segments();
        $urlPrefix = (!empty($segments[0]) && in_array($segments[0], $supportedPrefixes, true))
            ? $segments[0]
            : null;

        $isTranslationPath = $this->isTranslationEditPath($request->path());

        // 2) Resolver prefijo (orden: URL > ?lang > ?locale > sesión > navegador > default)
        if ($urlPrefix) {
            $prefix = $urlPrefix;
            Session::put('locale_prefix', $prefix);
        } elseif ($candidate = $request->query('lang')) {
            $prefix = $this->toPrefix($candidate, $supportedPrefixes, $defaultPrefix);
            Session::put('locale_prefix', $prefix);
        } elseif (($candidate = $request->query('locale')) && !$isTranslationPath) {
            $prefix = $this->toPrefix($candidate, $supportedPrefixes, $defaultPrefix);
            Session::put('locale_prefix', $prefix);
        } elseif (Session::has('locale_prefix')) {
            $prefix = (string) Session::get('locale_prefix');
            if (!in_array($prefix, $supportedPrefixes, true)) {
                $prefix = $defaultPrefix;
                Session::put('locale_prefix', $prefix);
            }
        } else {
            $pref   = $request->getPreferredLanguage($supportedPrefixes);
            $prefix = $pref ?: $defaultPrefix;
            Session::put('locale_prefix', $prefix);
        }

        // 3) Mapeo prefijo -> locale interno de Laravel (siempre 'pt', NO pt_BR)
        $internal = $this->toInternalLocale($prefix); // 'pt'

        Session::put('locale', $internal);
        App::setLocale($internal);
        Carbon::setLocale($internal);

        // 4) setlocale de PHP para formatos: forzamos targets de Brasil si es 'pt'
        $this->applyPhpSetLocale($internal);

        return $next($request);
    }

    /** Normaliza entrada variada -> prefijo soportado */
    private function toPrefix(string $raw, array $supportedPrefixes, string $fallback): string
    {
        $s = strtolower(str_replace('-', '_', trim($raw)));
        $map = [
            'es' => 'es', 'es_cr' => 'es', 'es_es' => 'es',
            'en' => 'en', 'en_us' => 'en', 'en_gb' => 'en',
            'fr' => 'fr', 'fr_fr' => 'fr',
            'de' => 'de', 'de_de' => 'de',
            'pt' => 'pt', 'pt_br' => 'pt', 'pt-pt' => 'pt', 'pt_pt' => 'pt', 'pt-br' => 'pt',
        ];
        $prefix = $map[$s] ?? substr($s, 0, 2);
        return in_array($prefix, $supportedPrefixes, true) ? $prefix : $fallback;
    }

    /** Prefijo -> locale interno (siempre 'pt' para simplificar) */
    private function toInternalLocale(string $prefix): string
    {
        return $prefix === 'pt' ? 'pt' : $prefix;
    }

    private function isTranslationEditPath(string $path): bool
    {
        return str_contains($path, 'admin/translations/');
    }

    private function applyPhpSetLocale(string $internal): void
    {
        // aunque App::setLocale('pt'), usamos targets de Brasil para formatos
        $map = [
            'es' => ['es_ES.UTF-8', 'es_ES', 'es'],
            'en' => ['en_US.UTF-8', 'en_US', 'en'],
            'fr' => ['fr_FR.UTF-8', 'fr_FR', 'fr'],
            'de' => ['de_DE.UTF-8', 'de_DE', 'de'],
            'pt' => ['pt_BR.UTF-8', 'pt_BR', 'pt'],
        ];

        $targets = $map[$internal] ?? [$internal . '.UTF-8', $internal];
        @setlocale(LC_TIME, ...$targets);
        @setlocale(LC_MONETARY, ...$targets);
        @setlocale(LC_NUMERIC, ...$targets);
    }
}
