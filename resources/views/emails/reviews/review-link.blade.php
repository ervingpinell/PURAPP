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
    $mailLocale = $isSpanish ? 'es' : 'en';

    // === 2) Congelar locale de traducciones durante este render ===
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
    // Si tienes el modelo $tour disponible, úsalo; si no, conserva $tourName tal cual.
    if (!empty($tour) && method_exists($tour, 'getTranslatedName')) {
        // si el tour tiene traducciones, respeta el mailLocale (es/en)
        $tourNameResolved = $tour->getTranslatedName($mailLocale);
    } else {
        // fallback a lo que venga por variable
        $tourNameResolved = $tourName ?? '';
    }

    // Adjuntar fecha de actividad si viene
    $tourLabel = trim($tourNameResolved . (!empty($activityDateText) ? " ({$activityDateText})" : ''));

    // === 5) Textos del email (en el locale ya fijado) ===
    $greeting = __('reviews.emails.request.greeting', [
        'name' => $userName ?: __('reviews.emails.traveler')
    ]);

    $intro = __('reviews.emails.request.intro', ['tour' => $tourLabel]);
    $ask   = __('reviews.emails.request.ask');
    $cta   = __('reviews.emails.request.cta');

    $pre   = $preheader ?? $tourLabel; // preheader
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
      {{ __('reviews.emails.request.fallback') }}<br>
      <a href="{{ $ctaUrl }}" style="color:#6b7280;word-break:break-all;">{{ $ctaUrl }}</a>
    </p>
  </div>
</div>

<div class="section-card" style="margin-top:8px;">
  @if(!empty($expiresAtText))
    <p style="margin:0 0 8px 0;font-size:13px;color:#374151;">
      {{ __('reviews.emails.request.expires', ['date' => $expiresAtText]) }}
    </p>
  @endif

  <p style="margin:0 0 8px 0;font-size:13px;color:#374151;">
    {{ __('reviews.emails.request.footer') }}
  </p>

  <div style="font-size:13px;color:#6b7280;">
    {!! __('reviews.emails.contact_line', [
        'email' => '<a href="mailto:'.e($contact['email']).'" style="color:#6b7280">'.e($contact['email']).'</a>',
        'phone' => '<a href="tel:'.preg_replace("/\s+/", "", e($contact['phone'])).'" style="color:#6b7280">'.e($contact['phone']).'</a>',
        'url'   => '<a href="'.e($contact['site']).'" style="color:#6b7280">'.e($contact['site']).'</a>',
    ]) !!}
  </div>
</div>

@php
  // Restaurar locale original después de componer el correo
  app()->setLocale($oldLocale);
@endphp
@endsection
