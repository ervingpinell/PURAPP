<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Reservas - Green Vacations</title>
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
            color: var(--text-color);
            margin: 0;
            padding: 40px;
            line-height: 1.6;
        }
        h2 {
            text-align: center;
            color: var(--green-dark);
            margin-bottom: 30px;
            font-family: var(--font-heading);
            font-size: 30px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .report-container {
            max-width: 850px;
            margin: auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .reserva-section {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .section-title {
            font-weight: 700;
            color: var(--green-base);
            margin-bottom: 15px;
            font-family: var(--font-heading);
            font-size: 18px;
        }
        .dato {
            margin-bottom: 8px;
            display: flex;
            align-items: baseline;
        }
        .dato strong {
            color: var(--green-dark);
            min-width: 120px;
            display: inline-block;
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 13px;
        }
        .dato span, .dato small {
            font-family: var(--font-body);
            font-size: 14px;
            color: var(--text-color);
        }
        .dato small {
            font-size: 12px;
            color: #777;
        }
        .line-separator {
            border-top: 1px dashed #c0c0c0;
            margin: 15px 0;
        }
        .total {
            font-weight: 700;
            color: var(--green-dark);
            text-align: right;
            font-size: 18px;
            font-family: var(--font-heading);
            margin-top: 15px;
        }
        .resumen-general {
            background: #eaf5ed;
            border: 1px solid var(--green-base);
            border-radius: 10px;
            padding: 25px;
            margin-top: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .resumen-general .dato {
            font-size: 16px;
            font-family: var(--font-heading);
            font-weight: 600;
            color: var(--green-dark);
        }
        .resumen-general .dato strong {
            min-width: 150px;
        }
        @media (max-width: 768px) {
            body { padding: 20px; }
            .report-container { padding: 20px; }
            h2 { font-size: 26px; margin-bottom: 20px; }
            .reserva-section { padding: 15px; }
            .section-title { font-size: 16px; margin-bottom: 10px; }
            .dato strong { min-width: 90px; font-size: 12px; }
            .dato span, .dato small { font-size: 13px; }
            .total { font-size: 16px; }
            .resumen-general { padding: 20px; margin-top: 30px; }
            .resumen-general .dato { font-size: 14px; }
        }
        @media print {
            body { background: none; padding: 0; margin: 0; }
            .report-container { box-shadow: none; border: none; border-radius: 0; margin: 0; max-width: initial; padding: 0; }
            .reserva-section { border: 1px solid #ccc; box-shadow: none; page-break-inside: avoid; margin-bottom: 15px; }
            .line-separator { border-top: 1px solid #ddd; }
            h2 { font-size: 24px; }
            .section-title { font-size: 16px; }
            .total { font-size: 16px; }
            .resumen-general { box-shadow: none; border: 1px solid #ccc; }
        }
    </style>
</head>
<body>
<div class="report-container">
    <h2> Reporte General de Reservas - Green Vacations Costa Rica </h2>

    @foreach($reservas as $reserva)
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

        <div class="reserva-section">
            <div class="section-title">Código: {{ $reserva->booking_reference }}</div>

            <div class="dato"><strong>Cliente:</strong> <span>{{ optional($reserva->user)->full_name }}</span> <small>({{ optional($reserva->user)->email }})</small></div>
            <div class="dato"><strong>Tour:</strong> <span>{{ $tour->name }}</span></div>
            <div class="dato"><strong>Fecha Reserva:</strong> <span>{{ \Carbon\Carbon::parse($reserva->booking_date)->format('d/m/Y') }}</span></div>
            <div class="dato"><strong>Fecha Tour:</strong> <span>{{ \Carbon\Carbon::parse($detail->tour_date)->format('d/m/Y') }}</span></div>
            <div class="dato"><strong>Horario:</strong> <span>{{ $horario }}</span></div>
            <div class="dato"><strong>Hotel:</strong> <span>{{ $hotel }}</span></div>
            <div class="dato"><strong>Estado:</strong> <span>{{ ucfirst($reserva->status) }}</span></div>

            <div class="line-separator"></div>

            <div class="dato"><strong>Adultos (x{{ $aQty }}):</strong> <span>${{ number_format($aPrice * $aQty, 2) }}</span></div>
            <div class="dato"><strong>Niños (x{{ $kQty }}):</strong> <span>${{ number_format($kPrice * $kQty, 2) }}</span></div>
            <div class="dato"><strong>Personas:</strong> <span>{{ $aQty + $kQty }}</span></div>

            <div class="line-separator"></div>

            <div class="total">TOTAL: ${{ number_format($reserva->total, 2) }}</div>
        </div>
    @endforeach

    <div class="line-separator"></div>

    <div class="resumen-general">
        <div class="dato"><strong>Total Adultos:</strong> <span>{{ $totalAdults }}</span></div>
        <div class="dato"><strong>Total Niños:</strong> <span>{{ $totalKids }}</span></div>
        <div class="dato"><strong>Total Personas:</strong> <span>{{ $totalPersons }}</span></div>
    </div>
</div>
</body>
</html>
