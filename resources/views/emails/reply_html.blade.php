@php
  $alt = $brandName . ' logo';
  $bg = '#f5f6f8'; $card = '#ffffff'; $text = '#222'; $muted = '#6b7280';

  // Normaliza y embebe como CID si existe el archivo
  $logoRel = isset($logoRelPath) ? str_replace('\\','/',$logoRelPath) : 'images/logoCompanyWhite.png';
  $logoFs  = public_path($logoRel);
  $logoUrl = asset($logoRel);
  $imgSrc  = (is_file($logoFs) && isset($message)) ? $message->embed($logoFs) : $logoUrl;

  // Intro con “sobre <strong>Tour</strong>” si aplica (traducción con HTML)
  $introHtml = __('reviews.emails.reply.intro', [
      'extra' => $tourName
          ? __('reviews.emails.reply.about_html', ['tour' => e($tourName)])
          : '',
  ]);
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <title>{{ $brandName }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
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

        <tr>
          <td style="padding:0 24px 8px 24px;color:{{ $text }};font-size:16px;line-height:1.5;">
            <p style="margin:0 0 12px 0;">
              {{ __('reviews.emails.reply.greeting', ['name' => $customerName ?: __('reviews.emails.traveler')]) }}
            </p>

            <p style="margin:0 0 12px 0;">{!! $introHtml !!}</p>

            <blockquote style="margin:0 0 16px 0;padding:12px 16px;border-left:4px solid #6b7280;background:#f9fafb;">
              {{ $body }}
            </blockquote>

            <p style="margin:0 0 12px 0;">{{ __('reviews.emails.reply.closing') }}</p>

            <p style="margin:16px 0 0 0;">{!! __('reviews.emails.reply.sign', ['admin' => e($adminName)]) !!}</p>
          </td>
        </tr>

        <tr>
          <td style="padding:0 24px 24px 24px;color:{{ $muted }};font-size:12px;line-height:1.5;">
            <p style="margin:0 0 4px 0;">
              <strong>{{ $brandName }}</strong><br>
              {!! __('reviews.emails.contact_line', [
                    'email' => '<a href="mailto:'.e($contact['email']).'" style="color:'.$muted.'">'.e($contact['email']).'</a>',
                    'phone' => '<a href="tel:'.preg_replace("/\s+/", "", e($contact['phone'])).'" style="color:'.$muted.'">'.e($contact['phone']).'</a>',
                    'url'   => '<a href="'.e($contact['site']).'" style="color:'.$muted.'">'.e($contact['site']).'</a>',
              ]) !!}
            </p>
          </td>
        </tr>

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
