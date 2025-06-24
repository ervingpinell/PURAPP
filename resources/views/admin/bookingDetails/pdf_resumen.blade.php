{{-- resources/views/admin/bookingDetails/pdf_resumen.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Reservas - Green Vacations</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size:12px; color:#333; background:#f9f9f9; padding:20px; }
        h2 { text-align:center; color:#2c6e49; margin-bottom:20px; }
        .reserva-section { background:#fff; border:1px solid #2c6e49; border-radius:6px; padding:15px; margin-bottom:30px; }
        .section-title { font-weight:bold; color:#2c6e49; margin-bottom:8px; }
        .dato { margin-bottom:6px; }
        .line-separator { border-top:1px dashed #2c6e49; margin:10px 0; }
        .total { font-weight:bold; color:#1e4d2b; }
        .resumen-general { background:#eaf5ed; border:1px solid #2c6e49; border-radius:6px; padding:15px; }
    </style>
</head>
<body>
    <h2>🌿 Reporte General de Reservas - Green Vacations Costa Rica 🌿</h2>

    @foreach($reservas as $reserva)
        @php
            $d       = $reserva->detail;
            $tour    = $reserva->tour;
            $aQty    = $d->adults_quantity;
            $kQty    = $d->kids_quantity;
            $aPrice  = $d->adult_price;
            $kPrice  = $d->kid_price;
        @endphp

        <div class="reserva-section">
            <div class="section-title">📌 Código: {{ $reserva->booking_reference }}</div>

            <div class="dato"><strong>Cliente:</strong>
                {{ optional($reserva->user)->full_name ?? 'N/A' }}
                ({{ optional($reserva->user)->email ?? 'N/A' }})
            </div>
            <div class="dato"><strong>Tour:</strong> {{ $tour->name ?? 'N/A' }}</div>
            <div class="dato"><strong>Fecha Reserva:</strong>
                {{ \Carbon\Carbon::parse($reserva->booking_date)->format('d/m/Y') }}
            </div>
            <div class="dato"><strong>Estado:</strong> {{ ucfirst($reserva->status) }}</div>

            <div class="line-separator"></div>

            <div class="dato">
                <strong>Adultos (x{{ $aQty }}):</strong>
                ${{ number_format($aPrice,2) }} = ${{ number_format($aPrice * $aQty,2) }}
            </div>
            <div class="dato">
                <strong>Niños (x{{ $kQty }}):</strong>
                ${{ number_format($kPrice,2) }} = ${{ number_format($kPrice * $kQty,2) }}
            </div>
            <div class="dato">
                <strong>Personas:</strong> {{ $aQty + $kQty }}
            </div>

            <div class="line-separator"></div>

            <div class="dato total">
                TOTAL: ${{ number_format($reserva->total,2) }}
            </div>
        </div>
    @endforeach

    <div class="line-separator"></div>

    <div class="resumen-general">
        <div class="dato"><strong>Total Adultos:</strong>   {{ $totalAdults  }}</div>
        <div class="dato"><strong>Total Niños:</strong>     {{ $totalKids    }}</div>
        <div class="dato"><strong>Total Personas:</strong>  {{ $totalPersons }}</div>
    </div>
</body>
</html>
