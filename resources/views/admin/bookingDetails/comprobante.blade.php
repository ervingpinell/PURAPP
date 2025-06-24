<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante de Reserva - Green Vacations</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size:13px; color:#333; background:#f9f9f9; padding:30px; }
        .comprobante-container { background:#fff; padding:25px; border:1px solid #2c6e49; border-radius:8px; max-width:700px; margin:auto; box-shadow:0 3px 10px rgba(44,110,73,0.15); }
        h2 { text-align:center; color:#2c6e49; margin-bottom:20px; }
        .dato { margin-bottom:8px; }
        .dato strong { color:#1e4d2b; }
        .line-separator { border-top:1px dashed #2c6e49; margin:15px 0; }
        .total { font-weight:bold; color:#1e4d2b; font-size:16px; }
    </style>
</head>
<body>
    <div class="comprobante-container">
        <h2>🎫 Comprobante de Reserva</h2>

        @php
            $tour    = $reserva->tour;
            $detail  = $reserva->detail;
            $aQty    = $detail->adults_quantity;
            $kQty    = $detail->kids_quantity;
            $aPrice  = $tour->adult_price   ?? 0;
            $kPrice  = $tour->kid_price     ?? 0;
            $hotel   = $detail->is_other_hotel
                        ? $detail->other_hotel_name
                        : optional($detail->hotel)->name ?? '—';
        @endphp

        <div class="dato"><strong>Código:</strong> {{ $reserva->booking_reference }}</div>
        <div class="dato"><strong>Cliente:</strong> {{ optional($reserva->user)->full_name }} ({{ optional($reserva->user)->email }})</div>
        <div class="dato"><strong>Tour:</strong> {{ $tour->name }}</div>
        <div class="dato"><strong>Fecha Reserva:</strong> {{ \Carbon\Carbon::parse($reserva->booking_date)->format('d/m/Y') }}</div>
        <div class="dato"><strong>Hotel:</strong> {{ $hotel }}</div>
        <div class="dato"><strong>Estado:</strong> {{ ucfirst($reserva->status) }}</div>

        <div class="line-separator"></div>

        <div class="dato"><strong>Adultos (x{{ $aQty }}):</strong> ${{ number_format($aPrice,2) }} = ${{ number_format($aPrice * $aQty,2) }}</div>
        <div class="dato"><strong>Niños (x{{ $kQty }}):</strong> ${{ number_format($kPrice,2) }} = ${{ number_format($kPrice * $kQty,2) }}</div>
        <div class="dato"><strong>Personas:</strong> {{ $aQty + $kQty }}</div>

        <div class="line-separator"></div>

        <div class="dato total">TOTAL A PAGAR: ${{ number_format($reserva->total,2) }}</div>
    </div>
</body>
</html>
