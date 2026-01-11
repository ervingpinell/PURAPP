{{-- resources/views/emails/layouts/base.blade.php --}}
<!DOCTYPE html>
<html lang="{{ $mailLocale ?? app()->getLocale() }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  @php
  // Locale efectivo del correo
  $mailLocale = $mailLocale ?? app()->getLocale();

  // Valores base
  $appUrl = rtrim($appUrl ?? config('app.url'), '/');
  $displayUrl = preg_replace('(^https?://)', '', $appUrl);
  $brand = $brand ?? $company ?? config('mail.from.name', config('app.name'));
  $supportEmail = config('mail.reply_to.address');
  $phone = config('app.company_phone');

  // Logo: embedded as base64 for reliable display in all email clients
  $logoUrl = \App\Helpers\EmailHelper::getEmbeddedLogo();
  $logoDimensions = \App\Helpers\EmailHelper::getLogoEmailDimensions();
  @endphp

  <title>{{ $viewTitle ?? $brand }}</title>

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
      font-family: 'Segoe UI';
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
      background-color: #0f2419;
      /* --primary-header */
      padding: 20px;
      text-align: center;
    }

    .email-header img {
      display: block;
      margin: 0 auto;
      width: 240px;
      max-width: 240px;
      height: auto;
      max-height: 140px;
      object-fit: contain;
    }

    /* ===== Body ===== */
    .email-body {
      padding: 30px 20px;
      font-size: 15px;
      /* Tamaño estándar legible */
      line-height: 1.7;
      /* Mayor espaciado entre líneas */
      color: #374151;
      /* Color más oscuro para mejor legibilidad */
      text-align: left;
      /* Alineación izquierda para párrafos largos */
    }

    .email-body h1 {
      font-size: 22px;
      /* Título principal legible pero no muy grande */
      font-weight: 700;
      margin: 0 0 20px 0;
      color: #111827;
      text-align: left;
      /* Títulos alineados a la izquierda */
      line-height: 1.3;
    }

    .email-body h2 {
      font-size: 18px;
      font-weight: 600;
      margin: 20px 0 12px 0;
      color: #1f2937;
      text-align: left;
    }

    .email-body p {
      margin: 0 0 16px 0;
      font-size: 15px;
      line-height: 1.7;
      color: #374151;
    }

    .email-body strong {
      font-weight: 600;
      color: #1f2937;
    }

    /* ===== Tables ===== */
    .email-body table {
      width: 100%;
      border-collapse: collapse;
      margin: 10px 0;
      font-family: 'Segoe UI', sans-serif;
    }

    .email-body table th,
    .email-body table td {
      padding: 8px 12px;
      text-align: left;
      border-bottom: 1px solid #e5e7eb;
      font-family: 'Segoe UI', sans-serif;
    }

    .email-body table th {
      background-color: #f9fafb;
      font-weight: 600;
      color: #374151;
      font-size: 14px;
    }

    .email-body table td {
      color: #333333;
      font-size: 14px;
    }

    .email-body table tr:last-child td {
      border-bottom: none;
    }

    /* Data table specific */
    .data-table {
      width: 100%;
      border-collapse: collapse;
      font-family: 'Segoe UI', sans-serif;
    }

    .data-table th,
    .data-table td {
      padding: 10px 12px;
      text-align: left;
      border-bottom: 1px solid #e5e7eb;
      font-family: 'Segoe UI', sans-serif;
      font-size: 14px;
    }

    .data-table th {
      background-color: #f9fafb;
      font-weight: 600;
      color: #374151;
      text-align: center;
    }

    .data-table td {
      color: #333333;
      text-align: center;
    }

    .data-table tfoot td {
      font-weight: 600;
      background-color: #f9fafb;
    }

    /* Section cards */
    .section-card {
      margin-bottom: 16px;
      padding: 16px;
      background-color: #ffffff;
      border: 1px solid #e5e7eb;
      border-radius: 4px;
      font-family: 'Segoe UI', sans-serif;
    }

    .section-title {
      font-weight: 700;
      font-size: 16px;
      color: #111827;
      margin-bottom: 8px;
      font-family: 'Segoe UI', sans-serif;
    }

    /* ===== Totales de reservas ===== */
    .totals-inline {
      margin: 20px 0;
      padding: 16px;
      background-color: #f9fafb;
      border-radius: 4px;
      font-family: 'Segoe UI', sans-serif;
    }

    .totals-inline .row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 0;
      font-size: 15px;
      color: #374151;
      border-bottom: 1px solid #e5e7eb;
    }

    .totals-inline .row:last-child {
      border-bottom: none;
    }

    .totals-inline .row.total {
      margin-top: 8px;
      padding-top: 12px;
      border-top: 2px solid #111827;
      font-weight: 700;
      font-size: 16px;
      color: #111827;
    }

    .totals-inline .label {
      font-weight: 500;
      color: #374151;
    }

    .totals-inline .amount {
      font-weight: 600;
      color: #111827;
    }

    .totals-inline .row.total .amount {
      font-weight: 700;
      font-size: 17px;
    }

    .totals-inline .muted {
      font-size: 13px;
      color: #6b7280;
      font-style: italic;
      margin-left: 8px;
    }

    /* ===== Footer ===== */
    .email-footer {
      background-color: #f9fafb;
      padding: 20px;
      text-align: center;
      border-top: 2px solid #e5e7eb;
      color: #6b7280;
      font-size: 13px !important;
      /* Tamaño base del footer */
      line-height: 1.6;
      /* Mejor espaciado */
      font-family: 'Segoe UI', sans-serif;
    }

    .email-footer a {
      color: #3869d4;
      /* --primary-color */
      text-decoration: none;
    }

    .email-footer a:hover {
      text-decoration: underline;
    }

    /* Links específicos del footer - controlar tamaño aquí */
    .email-footer-link {
      color: #3869d4 !important;
      text-decoration: none !important;
      font-size: 13px !important;
      /* Aumentado de 12px a 13px para mejor legibilidad */
      font-weight: 500;
      /* Peso medio para destacar */
    }

    .email-copyright {
      margin-top: 12px;
      font-size: 12px;
      color: #9ca3af;
      line-height: 1.5;
    }
  </style>
</head>

<body style="margin:0; padding:0; background-color:#ffffff;">
  <!-- Wrapper table for Outlook -->
  <table role="presentation" class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td align="center" style="padding:0;">
        <!-- Container table -->
        <table role="presentation" class="email-container" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; width:100%;">

          <tr>
            <td class="email-header" style="background-color:#0f2419; padding:20px; text-align:center;">
              <img src="{{ $logoUrl }}"
                alt="{{ $brand }}"
                width="{{ $logoDimensions['width'] }}"
                height="{{ $logoDimensions['height'] }}"
                border="0"
                style="display:block; margin:0 auto; width:{{ $logoDimensions['width'] }}px; height:{{ $logoDimensions['height'] }}px; max-width:100%; border:0; outline:none; text-decoration:none;">
            </td>
          </tr>

          <!-- BODY -->
          <tr>
            <td class="email-body" style="background-color:#ffffff; padding:30px 20px; color:#333333; font-family:'Segoe UI',sans-serif; font-size:16px; line-height:1.6;">
              @yield('content')
            </td>
          </tr>

          <!-- FOOTER -->
          <tr>
            <td class="email-footer">
              <p style="margin:0 0 10px 0; font-size:13px; text-align:center;"><a href="mailto:{{ $supportEmail }}" class="email-footer-link" style="color:#3869d4; text-decoration:none; font-size:13px; font-weight:500;">{{ $supportEmail }}</a>
                &nbsp; | &nbsp;
                📞 <a href="tel:{{ $phone }}" class="email-footer-link" style="color:#3869d4; text-decoration:none; font-size:13px; font-weight:500;">{{ $phone }}</a>
                &nbsp; | &nbsp;
                🌐 <a href="{{ $appUrl }}" class="email-footer-link" style="color:#3869d4; text-decoration:none; font-size:13px; font-weight:500;">{{ $displayUrl }}</a>
              </p>

              <div class="email-copyright" style="margin-top:15px; font-size:12px; color:#9ca3af;">
                &copy; {{ date('Y') }} {{ $brand }}.
                {{ str_starts_with(strtolower($mailLocale), 'es') ? 'Todos los derechos reservados.' : 'All rights reserved.' }}
              </div>
            </td>
          </tr>

        </table>
        <!-- End Container -->
      </td>
    </tr>
  </table>
  <!-- End Wrapper -->
</body>

</html>