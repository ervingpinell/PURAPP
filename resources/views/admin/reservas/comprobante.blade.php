<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante de Reserva - Green Vacations</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 13px;
            color: #333;
            background-color: #f9f9f9;
            padding: 30px;
        }
        .comprobante-container {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #2c6e49;
            max-width: 700px;
            margin: auto;
            box-shadow: 0 3px 10px rgba(44, 110, 73, 0.15);
        }
        h2 {
            text-align: center;
            color: #2c6e49;
            margin-bottom: 20px;
        }
        .dato {
            margin-bottom: 8px;
        }
        .dato strong {
            color: #1e4d2b;
        }
        .line-separator {
            border-top: 1px dashed #2c6e49;
            margin: 15px 0;
        }
        .total {
            font-weight: bold;
            color: #1e4d2b;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="comprobante-container">
        <h2>🎫 Comprobante de Reserva</h2>

        <div class="dato"><strong>Código:</strong> GV-{{ $reserva->codigo_reserva ?? $reserva->id }}</div>
        <div class="dato"><strong>Cliente:</strong> {{ $reserva->cliente->nombre }} ({{ $reserva->cliente->correo }})</div>
        <div class="dato"><strong>Tour:</strong> {{ $reserva->tour->nombre }} - {{ $reserva->tour->ubicacion }}</div>
        <div class="dato"><Strong>Categoria del Tour:</Strong> {{$reserva->tour->tipo_tour}}</div>
        <div class="dato"><strong>Fecha de Reserva:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y') }}</div>
        <div class="dato"><strong>Fechas del Tour:</strong> {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y h:i A') }} a {{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('d/m/Y h:i A') }}</div>
        
        <div class="dato"><strong>Estado:</strong> {{ $reserva->estado_reserva }}</div>

        <div class="line-separator"></div>

        <div class="dato"><strong>Cantidad total de personas:</strong> {{ $reserva->cantidad_adultos + $reserva->cantidad_ninos }}</div>
        <div class="dato"><strong>Adultos:</strong> {{ $reserva->cantidad_adultos }}</div>
        <div class="dato"><strong>Niños:</strong> {{ $reserva->cantidad_ninos }}</div>

        <div class="line-separator"></div>

        <div class="dato"><strong>Costo por adulto:</strong> ${{ number_format($reserva->precio_adulto, 2) }}</div>
        <div class="dato"><strong>Costo por niño:</strong> ${{ number_format($reserva->precio_nino, 2) }}</div>

        <div class="line-separator"></div>

        <div class="dato">
            <span class="total">
                TOTAL A PAGAR: ${{ number_format(($reserva->precio_adulto * $reserva->cantidad_adultos) + ($reserva->precio_nino * $reserva->cantidad_ninos), 2) }}
            </span>
        </div>
    </div>
</body>
</html>
