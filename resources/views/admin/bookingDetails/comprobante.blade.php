<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <title>{{ __('receipt.title') }}</title>
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
    <h2>{{ mb_strtoupper(__('receipt.title'), 'UTF-8') }}</h2>
    <h3>{{ config('app.name', __('receipt.company')) }}</h3>

    @php
      use Carbon\Carbon;
      Carbon::setLocale(app()->getLocale());

      $tour   = $reserva->tour;
      $detail = $reserva->detail;
      $aQty   = (int) $detail->adults_quantity;
      $kQty   = (int) $detail->kids_quantity;
      $aPrice = $tour->adult_price ?? 0;
      $kPrice = $tour->kid_price ?? 0;

      $hotel  = $detail->is_other_hotel
                  ? $detail->other_hotel_name
                  : optional($detail->hotel)->name ?? '—';

      // NUEVO: Meeting Point (solo nombre)
      $meetingPointName = optional($detail->meetingPoint)->name ?? '—';
      $meetingPointLabel = \Illuminate\Support\Facades\Lang::has('receipt.meeting_point')
        ? __('receipt.meeting_point')
        : 'Meeting Point';

      $horario = $detail->schedule
        ? Carbon::parse($detail->schedule->start_time)->isoFormat('LT') . ' – ' . Carbon::parse($detail->schedule->end_time)->isoFormat('LT')
        : __('receipt.no_schedule');

      $bookingDate = Carbon::parse($reserva->booking_date)->isoFormat('L');
      $tourDate    = Carbon::parse($detail->tour_date)->isoFormat('L');

      $subtotal = ($aPrice * $aQty) + ($kPrice * $kQty);
      $descuento = 0;

      if ($reserva->promoCode) {
          if ($reserva->promoCode->discount_percent) {
              $descuento = $subtotal * ($reserva->promoCode->discount_percent / 100);
          } elseif ($reserva->promoCode->discount_amount) {
              $descuento = $reserva->promoCode->discount_amount;
          }
      }

      // Traducir estado si existe clave, si no, ucfirst simple
      $statusKey = 'receipt.statuses.' . strtolower((string)$reserva->status);
      $statusT = trans()->has($statusKey) ? __($statusKey) : ucfirst((string)$reserva->status);
    @endphp

    <div class="datos-grid">
      <div class="dato">
        <strong>{{ __('receipt.code') }}</strong>
        <span>{{ $reserva->booking_reference }}</span>
      </div>
      <div class="dato">
        <strong>{{ __('receipt.client') }}</strong>
        <span>{{ optional($reserva->user)->full_name }}</span>
        <small>({{ optional($reserva->user)->email }})</small>
      </div>
      <div class="dato">
        <strong>{{ __('receipt.tour') }}</strong>
        <span>{{ $tour->name }}</span>
      </div>
      <div class="dato">
        <strong>{{ __('receipt.booking_date') }}</strong>
        <span>{{ $bookingDate }}</span>
      </div>
      <div class="dato">
        <strong>{{ __('receipt.tour_date') }}</strong>
        <span>{{ $tourDate }}</span>
      </div>
      <div class="dato">
        <strong>{{ __('receipt.schedule') }}</strong>
        <span>{{ $horario }}</span>
      </div>
      <div class="dato">
        <strong>{{ __('receipt.hotel') }}</strong>
        <span>{{ $hotel }}</span>
      </div>
      {{-- NUEVO: Meeting Point --}}
      <div class="dato">
        <strong>{{ $meetingPointLabel }}</strong>
        <span>{{ $meetingPointName }}</span>
      </div>
      <div class="dato">
        <strong>{{ __('receipt.status') }}</strong>
        <span>{{ $statusT }}</span>
      </div>
    </div>

    <div class="line-separator"></div>

    <div class="datos-grid">
      <div class="dato">
        <strong>{{ str_replace(':count', (string)$aQty, __('receipt.adults_x')) }}</strong>
        <span>${{ number_format($aPrice * $aQty, 2) }}</span>
      </div>
      <div class="dato">
        <strong>{{ str_replace(':count', (string)$kQty, __('receipt.kids_x')) }}</strong>
        <span>${{ number_format($kPrice * $kQty, 2) }}</span>
      </div>
      <div class="dato">
        <strong>{{ __('receipt.people') }}</strong>
        <span>{{ $aQty + $kQty }}</span>
      </div>
    </div>

    <div class="total-section">
      <div style="text-align: right;">
        <div>
          <strong>{{ __('receipt.subtotal') }}:</strong> ${{ number_format($subtotal, 2) }}
        </div>

        @if($descuento > 0)
          <div style="color: green;">
            <strong>{{ __('receipt.discount') }} ({{ $reserva->promoCode->code }}):</strong>
            -${{ number_format($descuento, 2) }}
          </div>
        @endif

        <div class="total mt-2">
          {{ __('receipt.total') }}: ${{ number_format($reserva->total, 2) }}
        </div>
      </div>
    </div>

    <div class="qr-container">
      @php
        $data = urlencode($reserva->booking_reference);
        $urlQr = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={$data}";
        $png = @file_get_contents($urlQr);
        $base64 = $png ? base64_encode($png) : null;
      @endphp

      @if($base64)
        <img src="data:image/png;base64,{{ $base64 }}" alt="{{ __('receipt.qr_alt') }}" style="width:120px; height:120px;">
      @endif
      <p class="qr-label">{{ __('receipt.qr_scan') }}</p>
      <p class="qr-label">{{ __('receipt.thanks', ['company' => config('app.name', __('receipt.company'))]) }}</p>
    </div>
  </div>
</body>
</html>
