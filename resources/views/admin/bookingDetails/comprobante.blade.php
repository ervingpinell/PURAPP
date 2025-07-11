<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Comprobante de Reserva</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Lora:wght@400;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --green-dark: #1A5229;
      --green-base: #2E8B57;
      --gray-light: #f0f2f5;
      --text-color: #333;
      --font-heading: 'Montserrat', sans-serif;
      --font-body: 'Lora', serif;
    }
    body {
      font-family: var(--font-body);
      font-size: 14px;
      background: var(--gray-light);
      margin: 0;
      padding: 30px;
      color: var(--text-color);
      line-height: 1.5;
    }
    .comprobante-container {
      max-width: 680px;
      margin: auto;
      background: #fff;
      border: none;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    h2 {
      margin: 0 0 25px;
      color: var(--green-dark);
      text-transform: uppercase;
      letter-spacing: 1.5px;
      font-size: 28px;
      text-align: center;
      font-family: var(--font-heading);
      font-weight: 700;
    }
    h3 {
      text-align: center;
      margin-top: -10px;
      color: var(--green-base);
      font-family: var(--font-heading);
      font-size: 16px;
      font-weight: 600;
      letter-spacing: 1px;
    }
    .datos-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px 30px;
    }
    .dato {
      display: flex;
      flex-direction: column;
      padding: 8px 0;
      border-bottom: 1px dashed #ddd;
    }
    .dato:last-of-type,
    .datos-grid > div:nth-last-child(2):nth-child(odd) {
      border-bottom: none;
    }
    .dato strong {
      color: var(--green-base);
      margin-bottom: 6px;
      font-size: 13px;
      font-family: var(--font-heading);
      font-weight: 600;
    }
    .dato span {
      font-size: 15px;
      color: var(--text-color);
    }
    .dato small {
      font-size: 12px;
      color: #777;
    }
    .line-separator {
      border-top: 1.5px solid var(--green-base);
      margin: 30px 0;
    }
    .total-section {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      padding-top: 10px;
    }
    .total {
      font-size: 22px;
      color: var(--green-dark);
      border-top: 1px dashed #ccc;
      padding-top: 10px;
      font-family: var(--font-heading);
      font-weight: 700;
    }
    .qr-container {
      text-align: center;
      margin-top: 35px;
      padding: 15px;
      background-color: #f6faf7;
      border-radius: 8px;
    }
    .qr-label {
      margin: 5px 0;
      font-size: 13px;
      color: #666;
      font-family: var(--font-body);
    }

    @media (max-width: 600px) {
      body { padding: 15px; }
      .comprobante-container { padding: 20px; }
      .datos-grid { grid-template-columns: 1fr; gap: 10px; }
      h2 { font-size: 24px; }
      .total { font-size: 18px; }
    }
    @media print {
      body { background: none; padding: 0; margin: 0; }
      .comprobante-container {
        box-shadow: none;
        border: 1px solid #ccc;
        border-radius: 0;
        margin: 1cm auto;
        max-width: initial;
      }
    }
  </style>
</head>
<body>
  <div class="comprobante-container">
    <h2>COMPROBANTE DE RESERVA</h2>
    <h3>GREEN VACATION CR</h3>

    @php
      $tour   = $reserva->tour;
      $detail = $reserva->detail;
      $aQty   = $detail->adults_quantity;
      $kQty   = $detail->kids_quantity;
      $aPrice = $tour->adult_price ?? 0;
      $kPrice = $tour->kid_price ?? 0;
      $hotel  = $detail->is_other_hotel
                  ? $detail->other_hotel_name
                  : optional($detail->hotel)->name ?? '—';
      $horario = $detail->schedule
        ? \Carbon\Carbon::parse($detail->schedule->start_time)->format('g:i A') . ' – ' .
          \Carbon\Carbon::parse($detail->schedule->end_time)->format('g:i A')
        : 'Sin horario';
    @endphp

    <div class="datos-grid">
      <div class="dato">
        <strong>Código</strong>
        <span>{{ $reserva->booking_reference }}</span>
      </div>
      <div class="dato">
        <strong>Cliente</strong>
        <span>{{ optional($reserva->user)->full_name }}</span>
        <small>({{ optional($reserva->user)->email }})</small>
      </div>
      <div class="dato">
        <strong>Tour</strong>
        <span>{{ $tour->name }}</span>
      </div>
      <div class="dato">
        <strong>Fecha de reserva</strong>
        <span>{{ \Carbon\Carbon::parse($reserva->booking_date)->format('d/m/Y') }}</span>
      </div>
      <div class="dato">
        <strong>Fecha de Tour</strong>
        <span>{{ \Carbon\Carbon::parse($detail->tour_date)->format('d/m/Y') }}</span>
      </div>
      <div class="dato">
        <strong>Horario</strong>
        <span>{{ $horario }}</span>
      </div>
      <div class="dato">
        <strong>Hotel</strong>
        <span>{{ $hotel }}</span>
      </div>
      <div class="dato">
        <strong>Estado</strong>
        <span>{{ ucfirst($reserva->status) }}</span>
      </div>
    </div>

    <div class="line-separator"></div>

    <div class="datos-grid">
      <div class="dato">
        <strong>Adultos (x{{ $aQty }})</strong>
        <span>${{ number_format($aPrice * $aQty, 2) }}</span>
      </div>
      <div class="dato">
        <strong>Niños (x{{ $kQty }})</strong>
        <span>${{ number_format($kPrice * $kQty, 2) }}</span>
      </div>
      <div class="dato">
        <strong>Personas</strong>
        <span>{{ $aQty + $kQty }}</span>
      </div>
    </div>

    <div class="total-section">
      <span class="total">TOTAL: ${{ number_format($reserva->total, 2) }}</span>
    </div>

    <div class="qr-container">
      @php
        $data = urlencode($reserva->booking_reference);
        $urlQr = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={$data}";
        $png = file_get_contents($urlQr);
        $base64 = base64_encode($png);
      @endphp

      <img src="data:image/png;base64,{{ $base64 }}" alt="QR Código de Reserva" style="width:120px; height:120px;">
      <p class="qr-label">Escanea para verificar tu reserva</p>
      <p class="qr-label">¡Gracias por reservar con Green Vacations Costa Rica!</p>
    </div>
  </div>
</body>
</html>
