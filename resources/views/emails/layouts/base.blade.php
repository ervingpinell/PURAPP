{{-- resources/views/emails/layouts/base.blade.php --}}
<!DOCTYPE html>
<html lang="{{ $mailLocale ?? app()->getLocale() }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

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
    html,
    body {
      margin: 0;
      padding: 0;
      height: 100%;
    }

    body {
      width: 100% !important;
      background-color: #ffffff !important;
      /* Blanco para Outlook */
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
      display: block;
    }

    table {
      border-collapse: collapse !important;
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
    }

    /* ===== Wrapper & container ===== */
    .email-wrapper {
      width: 100%;
      background-color: #ffffff;
      padding: 0;
      margin: 0;
    }

    .email-container {
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
      background-color: #ffffff;
    }

    /* ===== Header ===== */
    .email-header {
      background-color: #256d1b;
      padding: 20px;
      text-align: center;
    }

    .email-header img {
      display: block;
      margin: 0 auto;
      max-width: 180px;
      height: auto;
    }

    /* ===== Body ===== */
    .email-body {
      padding: 30px 20px;
      font-size: 14px;
      line-height: 1.6;
      color: #333333;
    }

    .section-card {
      background-color: #f9fafb;
      border: 1px solid #e5e7eb;
      padding: 15px;
      margin-bottom: 15px;
    }

    .section-title {
      font-size: 16px;
      font-weight: 700;
      color: #111827;
      margin: 0 0 10px 0;
    }

    /* Payment Status Badge */
    .payment-status {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      margin-top: 5px;
    }

    .payment-status.pending {
      background-color: #fef3c7;
      color: #92400e;
      border: 1px solid #fbbf24;
    }

    .payment-status.paid {
      background-color: #d1fae5;
      color: #065f46;
      border: 1px solid #10b981;
    }

    /* ===== Tables ===== */
    table.data-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      font-size: 13px;
    }

    table.data-table th {
      background-color: #f3f4f6;
      color: #374151;
      font-size: 13px;
      font-weight: 600;
      padding: 10px 8px;
      text-align: left;
      border-bottom: 2px solid #e5e7eb;
    }

    table.data-table td {
      padding: 10px 8px;
      font-size: 13px;
      text-align: left;
      border-bottom: 1px solid #f1f5f9;
    }

    /* ===== Totals ===== */
    .totals-inline {
      padding: 15px 0;
      border-top: 2px solid #e5e7eb;
      margin-top: 10px;
    }

    .totals-inline .row {
      margin: 8px 0;
      display: table;
      width: 100%;
    }

    .totals-inline .label {
      font-weight: 600;
      color: #374151;
      display: table-cell;
    }

    .totals-inline .amount {
      font-weight: 700;
      text-align: right;
      display: table-cell;
    }

    .totals-inline .total {
      margin-top: 10px;
      padding-top: 10px;
      border-top: 2px solid #e5e7eb;
      font-size: 16px;
    }

    .totals-inline .total .label,
    .totals-inline .total .amount {
      font-weight: 800;
      color: #111827;
    }

    /* ===== Footer ===== */
    .email-footer {
      background-color: #f9fafb;
      padding: 20px;
      text-align: center;
      border-top: 2px solid #e5e7eb;
      color: #6b7280;
      font-size: 12px;
    }

    .email-footer a {
      color: #059669;
      text-decoration: none;
    }

    .email-footer a:hover {
      text-decoration: underline;
    }

    .email-copyright {
      margin-top: 15px;
      font-size: 11px;
      color: #9ca3af;
    }

    /* ===== Outlook specific fixes ===== */
    /* Prevent Outlook from adding extra spacing */
    table td {
      border-collapse: collapse;
    }

    /* Force Outlook to provide a "view in browser" menu link */
    #outlook a {
      padding: 0;
    }
  </style>

  <!--[if mso]>
  <style type="text/css">
    body, table, td {
      font-family: Arial, sans-serif !important;
    }
    .email-container {
      width: 600px !important;
    }
  </style>
  <![endif]-->
</head>

@php
// Locale efectivo del correo
$mailLocale = $mailLocale ?? app()->getLocale();

// Valores base
$appUrl = rtrim($appUrl ?? config('app.url'), '/');
$brand = $brand ?? $company ?? config('mail.from.name', config('app.name', 'Green Vacations CR'));
$supportEmail = env('MAIL_TO_CONTACT', 'info@greenvacationscr.com');
$phone = env('COMPANY_PHONE', '+506 2479 1471');

// Logo: simple y directo desde la URL pública
$logoUrl = env('COMPANY_LOGO_URL', $appUrl . '/images/logo.png');
@endphp

<body style="margin:0; padding:0; background-color:#ffffff;">
  <!-- Wrapper table for Outlook -->
  <table role="presentation" class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td align="center" style="padding:0;">
        <!-- Container table -->
        <table role="presentation" class="email-container" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; width:100%;">

          <!-- HEADER -->
          <tr>
            <td class="email-header" style="background-color:#256d1b; padding:20px; text-align:center;">
              <img
                src="{{ $logoUrl }}"
                alt="{{ $brand }}"
                width="180"
                style="max-width:180px; height:auto; display:block; margin:0 auto;">
            </td>
          </tr>

          <!-- BODY -->
          <tr>
            <td class="email-body" style="padding:30px 20px; font-size:14px; line-height:1.6; color:#333333;">
              @yield('content')
            </td>
          </tr>

          <!-- FOOTER -->
          <tr>
            <td class="email-footer" style="background-color:#f9fafb; padding:20px; text-align:center; border-top:2px solid #e5e7eb; color:#6b7280; font-size:12px;">
              <p style="margin:0 0 10px 0;">
                📧 <a href="mailto:{{ $supportEmail }}" style="color:#059669; text-decoration:none;">{{ $supportEmail }}</a> &nbsp; | &nbsp;
                📞 <a href="tel:{{ $phone }}" style="color:#059669; text-decoration:none;">{{ $phone }}</a> &nbsp; | &nbsp;
                🌐 <a href="{{ $appUrl }}" style="color:#059669; text-decoration:none;">greenvacationscr.com</a>
              </p>
              <div class="email-copyright" style="margin-top:15px; font-size:11px; color:#9ca3af;">
                © {{ date('Y') }} {{ $brand }}.
                {{ str_starts_with(strtolower($mailLocale), 'es') ? 'Todos los derechos reservados.' : 'All rights reserved.' }}
              </div>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>

</html>