<?php

namespace App\Mail\Concerns;

use Illuminate\Support\Str;

trait EmbedsBrandAssets
{
    /**
     * Inserta el logo como CID y devuelve su Content-ID (para usarlo en <img src="cid:...">).
     * Si falla, devuelve null.
     */
    protected function embedLogoCid(): ?string
    {
        $path = $this->resolveLogoPath();
        if (!$path || !is_file($path)) {
            return null;
        }

        $cid = null;

        // Usamos el objeto Symfony Email para embeber el archivo
        $this->withSymfonyMessage(function (\Symfony\Component\Mime\Email $message) use ($path, &$cid) {
            // El segundo parámetro es un nombre sugerido para la parte MIME
            $cid = $message->embedFromPath($path, 'brand-logo');
        });

        // Symfony devuelve algo tipo: 'cid:xxxxx' o solo '<xxxxx>' según la versión.
        // Normalizamos a 'xxxxx' sin prefijos/ángulos.
        if ($cid) {
            $cid = (string)$cid;
            $cid = Str::of($cid)->replace(['cid:', '<', '>'], '')->value();
            return $cid ?: null;
        }

        return null;
    }

    /**
     * URL pública de fallback para el logo (útil en previews en navegador).
     */
    protected function logoFallbackUrl(): string
    {
        $rel = $this->configuredLogoRelative();
        $rel = ltrim($rel, '/');
        $base = rtrim(config('app.url'), '/');

        return $base . '/' . $rel;
    }

    /**
     * Resuelve la ruta absoluta en disco del logo a embeber.
     * Prioriza APP_LOGO, luego COMPANY_LOGO, y por defecto 'images/logoCompanyWhite.png'.
     */
    protected function resolveLogoPath(): ?string
    {
        $rel = $this->configuredLogoRelative();

        // Si viene absoluta, úsala tal cual
        if (Str::startsWith($rel, ['/','C:\\','D:\\']) || preg_match('~^[A-Za-z]:\\\\~', $rel)) {
            return realpath($rel) ?: $rel;
        }

        // Normal: archivo dentro de /public
        $path = public_path($rel);
        return $path;
    }

    /**
     * Obtiene la ruta **relativa** (respecto a /public) configurada para el logo.
     */
    protected function configuredLogoRelative(): string
    {
        // Orden: APP_LOGO -> COMPANY_LOGO -> default
        return (string) (env('APP_LOGO')
            ?: env('COMPANY_LOGO')
            ?: 'images/logoCompanyWhite.png');
    }
}
