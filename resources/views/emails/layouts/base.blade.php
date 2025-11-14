{{-- resources/views/emails/layouts/base.blade.php --}}
<!DOCTYPE html>
<html lang="{{ $mailLocale ?? app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  @php
    // Marca / nombre de la app
    $brand = $company
      ?? config('mail.from.name', config('app.name', 'Green Vacations CR'));

    // Título que se muestra en la pestaña del cliente de correo
    $viewTitle = $title ?? $brand;
  @endphp

  <title>{{ $viewTitle }}</title>

  <style>
    /* ===== Reset & Email-safe base ===== */
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
    }

    body {
      width: 100% !important;
      background: #f4f5f7;
      font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      color: #1f2937;
      -webkit-font-smoothing: antialiased;
      -ms-text-size-adjust: 100%;
      -webkit-text-size-adjust: 100%;
    }

    img {
      border: 0;
      outline: none;
      text-decoration: none;
      -ms-interpolation-mode: bicubic;
    }

    table {
      border-collapse: collapse !important;
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
    }

    .root {
      --primary-color:#60a862;
      --primary-dark:#256d1b;
      --primary-header:#0f2419;
      --primary-red:#e74c3c;
      --text-dark:#333;
      --white:#fff;
    }

    /* ===== Wrapper & container ===== */
    .email-wrapper {
      width: 100%;
      background: #f4f5f7;
      padding: 20px 0;
    }

    .email-container {
      width: 100%;
      max-width: 680px;
      margin: 0 auto;
      background: #ffffff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    /* ===== Header ===== */
    .email-header {
      background: linear-gradient(135deg, #256d1b, #60a862);
      padding: 18px 16px;
      text-align: center;
    }

    .email-header img {
      display: block;
      margin: 0 auto;
      max-width: 190px;     /* límite horizontal */
      max-height: 70px;     /* límite vertical */
      height: auto;
      width: auto;
    }

    /* ===== Body ===== */
    .email-body {
      padding: 24px 28px;
      font-size: 14px;
      line-height: 1.5;
    }

    .section-card {
      background: #f9fafb;
      border-radius: 8px;
      padding: 14px 18px;
      margin-bottom: 16px;
      border: 1px solid #e5e7eb;
    }

    .section-title {
      font-size: 16px;
      font-weight: 600;
      color: #111827;
      margin-bottom: 4px;
    }

    .title {
      font-size: 17px;
      font-weight: 700;
      color: #111827;
      margin: 0 0 6px;
      padding: 0 12px;
    }

    /* ===== Tablas de datos genéricas ===== */
    table.data-table {
      width: 100%;
      border-collapse: collapse;
      border-spacing: 0;
      margin-top: 8px;
      font-size: 13px;
    }

    table.data-table th {
      background: #f3f4f6;
      color: #374151;
      font-size: 13px;
      padding: 8px 6px;
      text-align: left;
      border-bottom: 1px solid #e5e7eb;
    }

    table.data-table td {
      padding: 7px 6px;
      font-size: 13px;
      text-align: left;
      border: none;
    }

    /* Línea suave solo ENTRE filas (evita rayas fantasma al final) */
    table.data-table tr + tr td {
      border-top: 1px solid #f1f5f9;
    }

    /* ===== Totales en línea ===== */
    .totals-inline {
      padding: 6px 12px 0;
    }

    .totals-inline .row {
      margin: 4px 0;
    }

    .totals-inline .label {
      font-weight: 500;
      color: #111827;
    }

    .totals-inline .amount {
      font-weight: 600;
    }

    .totals-inline .muted {
      color: #6b7280;
      font-weight: 500;
    }

    .totals-inline .total {
      margin-top: 4px;
      font-weight: 800;
    }

    /* ===== Footer ===== */
    .email-footer {
      background: #f9fafb;
      padding: 18px 22px;
      text-align: center;
      border-top: 1px solid #e5e7eb;
      color: #6b7280;
      font-size: 12px;
    }

    .email-footer a {
      color: #059669;
      text-decoration: none;
      margin: 0 6px;
    }

    .email-footer a:hover {
      text-decoration: underline;
    }

    .email-copyright {
      margin-top: 10px;
      font-size: 11px;
      color: #9ca3af;
    }

    /* ===== Responsive ===== */
    @media only screen and (max-width: 600px) {
      .email-body {
        padding: 18px 18px;
      }

      .section-card {
        padding: 12px 14px;
      }

      .title {
        padding: 0 10px;
      }

      .totals-inline {
        padding: 6px 10px 0;
      }
    }
  </style>
</head>

@php
  // Locale efectivo del correo
  $mailLocale = $mailLocale ?? app()->getLocale();

  // Valores base
  $appUrl       = rtrim($appUrl ?? config('app.url'), '/');
  $brand        = $brand ?? $company ?? config('mail.from.name', config('app.name', 'Green Vacations CR'));
  $supportEmail = env('MAIL_TO_CONTACT', 'info@greenvacationscr.com');
  $phone        = env('COMPANY_PHONE', '+506 2479 1471');

  // Logo: simple y directo desde la URL pública
  $logoUrl = env('COMPANY_LOGO_URL', $appUrl . '/images/logo.png');
@endphp

<body class="root">
  <div class="email-wrapper">
    <div class="email-container">
      {{-- HEADER --}}
      <div class="email-header">
        <img
          src="{{ $logoUrl }}"
          alt="{{ $brand }}"
          width="190"
          style="max-width:190px; max-height:70px; height:auto; display:block; margin:0 auto;"
        >
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
          {{ str_starts_with(strtolower($mailLocale), 'es') ? 'Todos los derechos reservados.' : 'All rights reserved.' }}
        </div>
      </div>
    </div>
  </div>
</body>
</html>
