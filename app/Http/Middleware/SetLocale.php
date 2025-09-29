<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        $supported = (array) config('app.supported_locales', ['es','en','fr','de','pt']);

        // Preferencia: 1) segmento {locale}, 2) sesión, 3) navegador, 4) default app
        $routeLocale    = $request->route('locale');
        $sessionLocale  = Session::get('locale');
        $browserPref    = $request->getPreferredLanguage($supported);
        $candidate      = $routeLocale ?: $sessionLocale ?: $browserPref ?: config('app.locale', 'es');

        // Normaliza a 'es','en','fr','de','pt'
        if (is_string($candidate)) {
            $candidate = strtolower(substr(str_replace('-', '_', trim($candidate)), 0, 2));
        }
        if (! in_array($candidate, $supported, true)) {
            $candidate = config('app.locale', 'es');
        }

        // Guarda en sesión (para cuando estés en /admin sin locale en URL)
        Session::put('locale', $candidate);

        // Aplica a Laravel + Carbon + rutas
        App::setLocale($candidate);
        Carbon::setLocale($candidate);

        // => Hace que TODAS las rutas con {locale} lo reciban por defecto
        URL::defaults(['locale' => $candidate]);

        // Opcional: locales del sistema (fecha/moneda)
        $this->applyPhpSetLocale($candidate);

        return $next($request);
    }

    private function applyPhpSetLocale(string $locale): void
    {
        $map = [
            'es' => ['es_ES.UTF-8','es_ES','es'],
            'en' => ['en_US.UTF-8','en_US','en'],
            'fr' => ['fr_FR.UTF-8','fr_FR','fr'],
            'de' => ['de_DE.UTF-8','de_DE','de'],
            'pt' => ['pt_PT.UTF-8','pt_PT','pt'],
        ];
        $targets = $map[$locale] ?? [$locale . '.UTF-8', $locale];
        @setlocale(LC_TIME, ...$targets);
        @setlocale(LC_MONETARY, ...$targets);
        @setlocale(LC_NUMERIC, ...$targets);
    }
}
