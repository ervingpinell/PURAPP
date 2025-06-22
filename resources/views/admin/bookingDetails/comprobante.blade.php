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
            $tour       = $reserva->tour;
            $adultPrice = $tour->adult_price ?? 0;
            $kidPrice   = $tour->kid_price   ?? 0;
        @endphp

        <div class="dato"><strong>Código:</strong> {{ $reserva->booking_reference }}</div>
        <div class="dato">
            <strong>Cliente:</strong>
            {{ optional($reserva->user)->full_name ?? 'N/A' }}
            ({{ optional($reserva->user)->email ?? 'N/A' }})
        </div>
        <div class="dato"><strong>Tour:</strong> {{ $tour->name ?? 'N/A' }}</div>
        <div class="dato">
            <strong>Fecha Reserva:</strong>
            {{ \Carbon\Carbon::parse($reserva->booking_date)->format('d/m/Y') }}
        </div>
        <div class="dato"><strong>Estado:</strong> {{ ucfirst($reserva->status) }}</div>

        <div class="line-separator"></div>

        <div class="dato">
            <strong>Adultos (x{{ $reserva->adults_quantity }}):</strong>
            ${{ number_format($adultPrice, 2) }}
            = ${{ number_format($adultPrice * $reserva->adults_quantity, 2) }}
        </div>
        <div class="dato">
            <strong>Niños (x{{ $reserva->kids_quantity }}):</strong>
            ${{ number_format($kidPrice, 2) }}
            = ${{ number_format($kidPrice * $reserva->kids_quantity, 2) }}
        </div>

        <div class="line-separator"></div>

        <div class="dato total">
            TOTAL A PAGAR: ${{ number_format($reserva->total, 2) }}
        </div>
    </div>
</body>
</html>
