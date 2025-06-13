<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Reservas - Green Vacations</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }
        h2 {
            text-align: center;
            color: #2c6e49;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #2c6e49;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #dfeee7;
            color: #1e4d2b;
        }
        .reserva-section {
            margin-bottom: 40px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #2c6e49;
            box-shadow: 0 2px 6px rgb(44 110 73 / 0.2);
        }
        .section-title {
            font-weight: bold;
            color: #2c6e49;
            font-size: 14px;
            margin-top: 10px;
            margin-bottom: 5px;
        }
        .line-separator {
            border-top: 1px dashed #2c6e49;
            margin: 10px 0;
        }
        .total {
            font-weight: bold;
            color: #1e4d2b;
        }
    </style>
</head>
<body>
    <h2>🌿 Reporte General de Reservas - Green Vacations Costa Rica 🌿</h2>

    @foreach($reservas as $reserva)
        <div class="reserva-section">
            <div class="section-title">📌 Código: GV-{{ $reserva->codigo_reserva ?? $reserva->id }}</div>
            <div><strong>Cliente:</strong> {{ optional($reserva->user)->full_name ?? 'N/A' }} ({{ optional($reserva->user)->email ?? 'N/A' }})</div>
            <div><strong>Tour:</strong> {{ optional($reserva->tour)->nombre ?? 'N/A' }} - {{ optional($reserva->tour)->ubicacion ?? '' }}</div>
            <div><strong>Fecha Reserva:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y') }}</div>
            <div><strong>Fechas:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y h:i A') }} a {{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('d/m/Y h:i A') }}</div>
            <div><strong>Estado:</strong> {{ $reserva->estado_reserva }}</div>
            <div class="line-separator"></div>
            <div>
                <strong>Costos:</strong><br>
                Adultos ({{ $reserva->cantidad_adultos }}) x ${{ number_format($reserva->precio_adulto, 2) }} = ${{ number_format($reserva->precio_adulto * $reserva->cantidad_adultos, 2) }}<br>
                Niños ({{ $reserva->cantidad_ninos }}) x ${{ number_format($reserva->precio_nino, 2) }} = ${{ number_format($reserva->precio_nino * $reserva->cantidad_ninos, 2) }}<br>
                <span class="total">TOTAL: ${{ number_format(($reserva->precio_adulto * $reserva->cantidad_adultos) + ($reserva->precio_nino * $reserva->cantidad_ninos), 2) }}</span>
            </div>
        </div>
    @endforeach
</body>
</html>
