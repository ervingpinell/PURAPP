@extends('emails.layouts.base')

@section('content')
@php
    use Illuminate\Support\Str;

    /**
     * === 1) Detectar idioma del tour (spanish vs. other) ===
     * Priorizamos:
     *  - $tourLanguageCode (ej. 'es', 'en')
     *  - $tourLanguageName  (ej. 'Español', 'English')
     *  - relaciones si vienen: $detail->tourLanguage->name / $booking->tourLanguage->name
     */
    $tourLanguageCode = $tourLanguageCode
        ?? null;

    $tourLanguageName = $tourLanguageName
        ?? ($detail->tourLanguage->name ?? null)
        ?? ($booking->tourLanguage->name ?? null);

    $isSpanish = false;

    if (!empty($tourLanguageCode)) {
        $isSpanish = Str::startsWith(Str::lower($tourLanguageCode), 'es');
    } elseif (!empty($tourLanguageName)) {
        $nameLower = Str::lower($tourLanguageName);
        // heurística simple: 'español', 'spanish', 'es-xx'
        $isSpanish = (Str::contains($nameLower, 'espa') || Str::contains($nameLower, 'spani'));
    }

    // Si no se pudo inferir nada, caemos a lo que venga en $mailLocale o app()->getLocale()
    $mailLocale = $isSpanish
        ? 'es'
        : (str_starts_with(strtolower($mailLocale ?? app()->getLocale()), 'es') ? 'es' : 'en');

    // === 2) Congelar locale de traducciones durante este render (por si algo externo lo usa) ===
    $oldLocale = app()->getLocale();
    app()->setLocale($mailLocale);

    // === 3) Branding / contacto ===
    $company = $brandName
        ?? ($company ?? config('mail.from.name', config('app.name', 'Green Vacations CR')));

    $contact = [
        'site'  => rtrim(env('COMPANY_SITE', config('app.url')), '/'),
        'email' => env('MAIL_TO_CONTACT', config('mail.from.address')),
        'phone' => env('COMPANY_PHONE', '+506 2479 1471'),
    ];

    // === 4) Nombre del tour usando traducciones ===
    if (!empty($tour) && method_exists($tour, 'getTranslatedName')) {
        // si el tour tiene traducciones, respeta el mailLocale (es/en)
        $tourNameResolved = $tour->getTranslatedName($mailLocale);
    } else {
        // fallback a lo que venga por variable
        $tourNameResolved = $tourName ?? '';
    }

    // Adjuntar fecha de actividad si viene (texto ya formateado fuera)
    $tourLabel = trim($tourNameResolved . (!empty($activityDateText) ? " ({$activityDateText})" : ''));

    // === 5) Textos del email en ES / EN (sin usar __()) ===
    $nameForGreeting = $userName ?: ($mailLocale === 'es' ? 'viajero' : 'traveler');

    if ($mailLocale === 'es') {
        $greeting = "Hola {$nameForGreeting},";
        $intro    = "¡Pura vida! 🙌 Gracias por elegirnos. Nos encantaría saber cómo te fue en {$tourLabel}.";
        $ask      = "¿Nos regalas 1–2 minutos para dejar tu reseña? ¡Nos ayuda muchísimo!";
        $cta      = "Dejar mi reseña";

        $fallbackLabel = "Si el botón no funciona, copia y pega este enlace en tu navegador:";
        $expiresLabel  = "Este enlace estará activo hasta: :date.";
        $footerText    = "Gracias por apoyar el turismo local. ¡Esperamos verte de nuevo pronto! 🌿";

        $contactLineTemplate = "Si necesitas ayuda, contáctanos en :email o :phone. Visítanos en :url.";
    } else {
        $greeting = "Hi {$nameForGreeting},";
        $intro    = "Pura vida! 🙌 Thanks for choosing us. We’d love to know how it went on {$tourLabel}.";
        $ask      = "Could you spare 1–2 minutes to leave your review? It truly helps a lot.";
        $cta      = "Leave my review";

        $fallbackLabel = "If the button does not work, copy and paste this link in your browser:";
        $expiresLabel  = "This link will be active until: :date.";
        $footerText    = "Thanks for supporting local tourism. We hope to see you again soon! 🌿";

        $contactLineTemplate = "If you need help, contact us at :email or :phone. Visit us at :url.";
    }

    // Preheader (puedes ajustar si quieres algo más descriptivo)
    $pre = $preheader ?? $tourLabel;

    // Texto de expiración (si viene la fecha ya formateada)
    $expiresText = null;
    if (!empty($expiresAtText)) {
        $expiresText = str_replace(':date', $expiresAtText, $expiresLabel);
    }

    // Línea de contacto con HTML
    $emailHtml = '<a href="mailto:'.e($contact['email']).'" style="color:#6b7280">'.e($contact['email']).'</a>';
    $phoneHtml = '<a href="tel:'.preg_replace("/\s+/", "", e($contact['phone'])).'" style="color:#6b7280">'.e($contact['phone']).'</a>';
    $urlHtml   = '<a href="'.e($contact['site']).'" style="color:#6b7280">'.e($contact['site']).'</a>';

    $contactLineHtml = strtr($contactLineTemplate, [
        ':email' => $emailHtml,
        ':phone' => $phoneHtml,
        ':url'   => $urlHtml,
    ]);
@endphp

{{-- Preheader oculto --}}
<span style="display:none!important;visibility:hidden;opacity:0;color:transparent;height:0;width:0;">
  {{ $pre }}
</span>

<div class="section-card">
  <div class="section-title" style="margin-bottom:8px;">{{ $company }}</div>

  <div style="font-size:16px;line-height:1.6;color:#111827;">
    <p style="margin:0 0 12px;">{{ $greeting }}</p>
    <p style="margin:0 0 12px;">{{ $intro }}</p>
    <p style="margin:0 0 12px;">{{ $ask }}</p>
  </div>

  <div style="text-align:center;margin:6px 0 10px;">
    <a href="{{ $ctaUrl }}"
       style="display:inline-block;background:#10b981;color:#fff;text-decoration:none;padding:12px 20px;border-radius:8px;font-weight:700;">
      {{ $cta }}
    </a>
  </div>

  <div style="font-size:12px;color:#6b7280;line-height:1.5;">
    <p style="margin:0;">
      {{ $fallbackLabel }}<br>
      <a href="{{ $ctaUrl }}" style="color:#6b7280;word-break:break-all;">{{ $ctaUrl }}</a>
    </p>
  </div>
</div>

<div class="section-card" style="margin-top:8px;">
  @if($expiresText)
    <p style="margin:0 0 8px 0;font-size:13px;color:#374151;">
      {{ $expiresText }}
    </p>
  @endif

  <p style="margin:0 0 8px 0;font-size:13px;color:#374151;">
    {{ $footerText }}
  </p>

  <div style="font-size:13px;color:#6b7280;">
    {!! $contactLineHtml !!}
  </div>
</div>

@php
  // Restaurar locale original después de componer el correo
  app()->setLocale($oldLocale);
@endphp
@endsection
