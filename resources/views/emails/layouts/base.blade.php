<!DOCTYPE html>
<html lang="{{ $locale ?? app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $subject ?? config('app.name') }}</title>
  <style>
    /* ===== Reset & Base ===== */
    body {
      margin: 0;
      padding: 0;
      background: #f4f5f7;
      font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      color: #1f2937;
      -webkit-font-smoothing: antialiased;
    }
    .email-wrapper { width: 100%; background: #f4f5f7; padding: 32px 0; }
    .email-container {
      max-width: 640px; margin: 0 auto; background: #fff; border-radius: 12px;
      overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    /* ===== Header ===== */
    .email-header { background: linear-gradient(135deg, #059669, #10b981); padding: 36px 20px; text-align: center; }
    .email-header img { max-width: 180px; height: auto; }

    /* ===== Body ===== */
    .email-body { padding: 36px 40px; font-size: 15px; line-height: 1.6; }
    .section-card {
      background: #f9fafb; border-radius: 8px; padding: 16px 20px; margin-bottom: 18px; border: 1px solid #e5e7eb;
    }
    .section-title { font-size: 17px; font-weight: 600; color: #111827; margin-bottom: 6px; }

    .title { font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 8px; padding: 0 20px; }

    /* ===== Tables ===== */
    table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table.data-table th {
      background: #f3f4f6; color: #374151; font-size: 14px;
      padding: 10px 8px; border-bottom: 2px solid #e5e7eb; text-align: center;
    }
    table.data-table td { padding: 10px 8px; border-bottom: 1px solid #f1f5f9; text-align: center; font-size: 14px; }

    /* ===== Totales en línea ===== */
    .totals-inline { padding: 8px 20px 0; }
    .totals-inline .row { margin: 6px 0; }
    .totals-inline .label { font-weight: 500; color: #111827; }
    .totals-inline .amount { font-weight: 600; }
    .totals-inline .muted { color: #6b7280; font-weight: 500; }
    .totals-inline .total { margin-top: 6px; font-weight: 800; }

    /* ===== Footer ===== */
    .email-footer { background: #f9fafb; padding: 24px 30px; text-align: center; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 13px; }
    .email-footer a { color: #059669; text-decoration: none; margin: 0 8px; }
    .email-footer a:hover { text-decoration: underline; }
    .email-copyright { margin-top: 16px; font-size: 12px; color: #9ca3af; }

    @media only screen and (max-width:600px) {
      .email-body { padding: 24px; }
      .section-card { padding: 14px 16px; }
      .title { padding: 0 16px; }
      .totals-inline { padding: 8px 16px 0; }
    }
  </style>
</head>

@php
  // Valores base
  $appUrl   = rtrim($appUrl ?? config('app.url'), '/');
  $brand    = $company ?? config('mail.from.name', config('app.name', 'Green Vacations CR'));
  $supportEmail = env('MAIL_TO_CONTACT', 'info@greenvacationscr.com');
  $phone = env('COMPANY_PHONE', '+506 2479 1471');
  $currentLocale = ($locale ?? app()->getLocale());

  // Blindajes de logo
  $logoCid         = $logoCid         ?? null;
  $appLogoFallback = $appLogoFallback
    ?? (env('COMPANY_LOGO_URL')
      ?: (file_exists(public_path('images/logo-email.png'))
          ? asset('images/logo-email.png')
          : asset(ltrim($appLogo ?? 'images/logoCompanyWhite.png', '/'))));
@endphp

<body>
  <div class="email-wrapper">
    <div class="email-container">
      {{-- HEADER --}}
      <div class="email-header">
        <img
          src="{{ $logoCid ? ('cid:' . $logoCid) : $appLogoFallback }}"
          alt="{{ $brand }}"
        />
      </div>

      {{-- BODY --}}
      <div class="email-body">
        @yield('content')
      </div>

      {{-- FOOTER --}}
      <div class="email-footer">
        <p>
          📧 <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a> &nbsp; | &nbsp;
          📞 <a href="tel:{{ $phone }}">{{ $phone }}</a> &nbsp; | &nbsp;
          🌐 <a href="{{ $appUrl }}">greenvacationscr.com</a>
        </p>
        <div class="email-copyright">
          © {{ date('Y') }} {{ $brand }}.
          {{ str_starts_with(strtolower($currentLocale), 'es') ? 'Todos los derechos reservados.' : 'All rights reserved.' }}
        </div>
      </div>
    </div>
  </div>
</body>
</html>
