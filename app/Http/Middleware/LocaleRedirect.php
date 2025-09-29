<?php

namespace App\Http\Middleware;

use Closure;

class LocaleRedirect
{
    public function handle($request, Closure $next)
    {
        $supported = (array) config('app.supported_locales', ['es','en','fr','de','pt']);
        $default   = config('app.locale', 'es');

        // Redirección permanente para GET/HEAD, temporal para otros verbos
        $method      = $request->getMethod();
        $isPermanent = in_array($method, ['GET','HEAD'], true);
        $status      = $isPermanent ? 301 : 307;

        $path     = ltrim($request->path(), '/');         // sin slash inicial
        $segments = $path === '' ? [] : explode('/', $path);
        $first    = $segments[0] ?? '';
        $second   = $segments[1] ?? '';

        // Prefijos/paths que NO deben llevar locale (auth/admin/APIs/assets…)
        $noLocalePrefixes = [
            // Auth & Fortify
            'login','logout','register',
            'password','forgot-password','reset-password',
            'verify-email','email','verification',
            'two-factor-challenge','two-factor-recovery-codes',
            'user', // ej: user/two-factor-qr-code, user/two-factor-recovery-codes

            // Admin & APIs & debug
            'admin','api','webhook','hooks','sanctum','oauth',
            '_ignition','telescope','horizon','debugbar',

            // Assets / públicos
            'vendor','storage','build','assets','public',
            'css','js','img','images','fonts','webfonts','svg',
            'favicon.ico','robots.txt','sitemap.xml','mix-manifest.json',
        ];

        // Helper: redirigir preservando query string
        $redirectTo = function (string $toPath) use ($request, $status) {
            $url = url($toPath);
            if ($qs = $request->getQueryString()) { $url .= '?' . $qs; }
            return redirect()->to($url)->setStatusCode($status);
        };

        // REGLA A: si viene /{locale}/{backend} (p. ej. /es/login) → des-localiza
        if ($first !== '' && in_array($first, $supported, true) && $second !== '' && in_array($second, $noLocalePrefixes, true)) {
            // quita el primer segmento (locale) y deja /{backend...}
            $rest = implode('/', array_slice($segments, 1));
            return $redirectTo($rest);
        }

        // REGLA B: si la URL empieza con un backend (p. ej. /login), NO localices
        if ($first !== '' && (in_array($first, $noLocalePrefixes, true) || in_array($path, $noLocalePrefixes, true))) {
            return $next($request);
        }

        // REGLA C: ?lang=xx / ?locale=xx → /xx[/rest]
        $qLang = $request->query('lang') ?? $request->query('locale');
        if ($qLang) {
            $candidate = strtolower(substr(str_replace('-', '_', $qLang), 0, 2));
            if (in_array($candidate, $supported, true)) {
                // si ya había locale, reemplázalo
                $rest = $path;
                if ($first !== '' && in_array($first, $supported, true)) {
                    $rest = ltrim(substr($path, strlen($first)), '/'); // quita locale actual
                }
                $target = $candidate . ($rest !== '' ? "/{$rest}" : '');
                if ($target !== $path) {
                    return $redirectTo($target);
                }
            }
        }

        // REGLA D: raíz → /{default}
        if ($path === '') {
            return $redirectTo($default);
        }

        // REGLA E: si no comienza con un locale soportado → /{default}/path
        if (!in_array($first, $supported, true)) {
            return $redirectTo($default . '/' . $path);
        }

        // Ya hay locale válido → continuar
        return $next($request);
    }
}
