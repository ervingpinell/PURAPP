@php
    $alt = $brandName . ' logo';
    $bg = '#f5f6f8'; $card = '#ffffff'; $text = '#222'; $muted = '#6b7280'; $btn = '#10b981'; $btnText = '#fff';

    // Logo (normaliza slashes y embebe como CID si existe el archivo)
    $logoRel = isset($logoRelPath) ? str_replace('\\', '/', $logoRelPath) : 'images/logoCompanyWhite.png';
    $logoFs  = public_path($logoRel);
    $logoUrl = asset($logoRel);
    $imgSrc  = (is_file($logoFs) && isset($message)) ? $message->embed($logoFs) : $logoUrl;

    // Contact defaults (por si el Mailable no los envía)
    $contact = $contact ?? [
        'site'  => 'https://greenvacationscr.com',
        'email' => 'info@greenvacationscr.com',
        'phone' => '+506 2479 1471',
    ];

    // Greeting + textos
    $greeting = __('reviews.emails.request.greeting', [
        'name' => $userName ?: __('reviews.emails.traveler')
    ]);

    $tourLabel = trim($tourName . ($activityDateText ? " ({$activityDateText})" : ''));
    $intro = __('reviews.emails.request.intro', ['tour' => $tourLabel]);
    $ask   = __('reviews.emails.request.ask');
    $cta   = __('reviews.emails.request.cta');

    // Preheader opcional
    $pre = $preheader ?? $tourLabel;

    // Línea de contacto con enlaces (HTML)
    $contactHtml = __('reviews.emails.contact_line', [
        'email' => '<a href="mailto:'.e($contact['email']).'" style="color:'.$muted.'">'.e($contact['email']).'</a>',
        'phone' => '<a href="tel:'.preg_replace("/\s+/", "", e($contact['phone'])).'" style="color:'.$muted.'">'.e($contact['phone']).'</a>',
        'url'   => '<a href="'.e($contact['site']).'" style="color:'.$muted.'">'.e($contact['site']).'</a>',
    ]);
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ $brandName }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <span style="display:none!important;visibility:hidden;opacity:0;color:transparent;height:0;width:0;">
        {{ $pre }}
    </span>
</head>
<body style="margin:0;padding:0;background:{{ $bg }};font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:{{ $bg }};padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;background:{{ $card }};border-radius:12px;overflow:hidden;">
                <tr>
                    <td align="center" style="padding:24px;">
                        <img src="{{ $imgSrc }}" alt="{{ $alt }}" width="160" style="display:block;max-width:160px;height:auto;border:0;">
                    </td>
                </tr>

                {{-- Body --}}
                <tr>
                    <td style="padding:0 24px 8px 24px;color:{{ $text }};font-size:16px;line-height:1.5;">
                        <p style="margin:0 0 12px 0;">{{ $greeting }}</p>
                        <p style="margin:0 0 12px 0;">{{ $intro }}</p>
                        <p style="margin:0 0 12px 0;">{{ $ask }}</p>
                    </td>
                </tr>

                {{-- CTA Button --}}
                <tr>
                    <td align="center" style="padding:8px 24px 16px 24px;">
                        <a href="{{ $ctaUrl }}"
                           style="display:inline-block;background:{{ $btn }};color:{{ $btnText }};text-decoration:none;padding:12px 20px;border-radius:8px;font-weight:bold;">
                            {{ $cta }}
                        </a>
                    </td>
                </tr>

                {{-- Link fallback --}}
                <tr>
                    <td style="padding:0 24px 16px 24px;color:{{ $muted }};font-size:12px;line-height:1.5;">
                        <p style="margin:0;">
                            {{ __('reviews.emails.request.fallback') }}<br>
                            <a href="{{ $ctaUrl }}" style="color:{{ $muted }};word-break:break-all;">{{ $ctaUrl }}</a>
                        </p>
                    </td>
                </tr>

                {{-- Footer info --}}
                <tr>
                    <td style="padding:0 24px 24px 24px;color:{{ $muted }};font-size:12px;line-height:1.5;">
                        @if($expiresAtText)
                            <p style="margin:0 0 8px 0;">{{ __('reviews.emails.request.expires', ['date' => $expiresAtText]) }}</p>
                        @endif
                        <p style="margin:0 0 8px 0;">{{ __('reviews.emails.request.footer') }}</p>
                        <p style="margin:0;">{!! $contactHtml !!}</p>
                    </td>
                </tr>

                {{-- Footer brand --}}
                <tr>
                    <td align="center" style="padding:16px;color:{{ $muted }};font-size:11px;">
                        © {{ date('Y') }} {{ $brandName }} — {{ __('reviews.emails.reply.rights_reserved') }}
                    </td>
                </tr>
            </table>

            <div style="height:24px;"></div>
        </td>
    </tr>
</table>
</body>
</html>
