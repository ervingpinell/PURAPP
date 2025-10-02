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
        $locales   = config('routes.locales', []); // ['es' => ..., 'en' => ...]
        $supported = array_keys($locales);
        $default   = config('routes.default_locale', 'es');

        // 1) Detectar locale desde el prefijo de la ruta
        $segments    = $request->segments();
        $routeLocale = null;
        if (!empty($segments[0]) && in_array($segments[0], $supported, true)) {
            $routeLocale = $segments[0];
        }

        // Determina si estamos en páginas de traducciones
        $isTranslationPath = $this->isTranslationEditPath($request->path());

        // 2) Si hay locale en la URL (prefijo), usarlo
        if ($routeLocale) {
            $locale = $routeLocale;
            Session::put('locale', $locale);
        }
        // 3) Si hay query param ?lang= (SOLO para UI)
        elseif ($candidate = $request->query('lang')) {
            $candidate = $this->normalizeLocale($candidate);
            if (in_array($candidate, $supported, true)) {
                $locale = $candidate;
                Session::put('locale', $locale);
            }
        }
        // 4) Si hay query param ?locale= y NO estamos en pantallas de traducción, trátalo como alias de ?lang
        elseif (($candidate = $request->query('locale')) && !$isTranslationPath) {
            $candidate = $this->normalizeLocale($candidate);
            if (in_array($candidate, $supported, true)) {
                $locale = $candidate;
                Session::put('locale', $locale);
            }
        }
        // 5) Si hay locale en sesión (UI)
        elseif (Session::has('locale')) {
            $locale = Session::get('locale');
        }
        // 6) Detectar del navegador
        else {
            $pref   = $request->getPreferredLanguage($supported);
            $locale = $pref ?: $default;
            Session::put('locale', $locale);
        }

        // Aplicar a framework y helpers de fecha/número
        App::setLocale($locale);
        Carbon::setLocale($locale);
        $this->applyPhpSetLocale($locale);

        return $next($request);
    }

    private function normalizeLocale(string $locale): string
    {
        $locale = str_replace('-', '_', strtolower(trim($locale)));

        // Mapeo de variantes
        $map = [
            'es_cr' => 'es',
            'es_es' => 'es',
            'pt_br' => 'pt',
            'pt_pt' => 'pt',
            'en_us' => 'en',
            'en_gb' => 'en',
            'fr_fr' => 'fr',
            'de_de' => 'de',
        ];

        return $map[$locale] ?? substr($locale, 0, 2);
    }

    private function isTranslationEditPath(string $path): bool
    {
        // Ajusta si cambias tu prefijo/rutas
        return str_contains($path, 'admin/translations/');
    }

    private function applyPhpSetLocale(string $locale): void
    {
        $map = [
            'es' => ['es_ES.UTF-8', 'es_ES', 'es'],
            'en' => ['en_US.UTF-8', 'en_US', 'en'],
            'fr' => ['fr_FR.UTF-8', 'fr_FR', 'fr'],
            'de' => ['de_DE.UTF-8', 'de_DE', 'de'],
            'pt' => ['pt_BR.UTF-8', 'pt_BR', 'pt'],
        ];

        $targets = $map[$locale] ?? [$locale . '.UTF-8', $locale];
        @setlocale(LC_TIME, ...$targets);
        @setlocale(LC_MONETARY, ...$targets);
        @setlocale(LC_NUMERIC, ...$targets);
    }
}
