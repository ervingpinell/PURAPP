<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Reservas - Green Vacations</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Lora:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-dark: #1A5229; /* Un verde oscuro más sofisticado */
            --green-base: #2E8B57; /* Un verde base más vibrante */
            --gray-light: #f0f2f5; /* Un gris claro más suave */
            --text-color: #333; /* Color de texto principal para mejor contraste */
            --font-heading: 'Montserrat', sans-serif;
            --font-body: 'Lora', serif;
        }
        body {
            font-family: var(--font-body);
            font-size: 14px; /* Tamaño de fuente consistente con el comprobante */
            background: var(--gray-light);
            color: var(--text-color);
            margin: 0;
            padding: 40px; /* Más padding para una mejor presentación */
            line-height: 1.6;
        }
        h2 {
            text-align: center;
            color: var(--green-dark); /* Título en verde oscuro */
            margin-bottom: 30px; /* Más espacio debajo del título */
            font-family: var(--font-heading);
            font-size: 30px; /* Título más grande */
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .report-container {
            max-width: 850px; /* Ancho un poco mayor para el reporte general */
            margin: auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 30px; /* Padding interno */
        }
        .reserva-section {
            background: #fff; /* Fondo blanco para cada sección */
            border: 1px solid #e0e0e0; /* Borde sutil */
            border-radius: 8px; /* Bordes suaves */
            padding: 20px; /* Más padding */
            margin-bottom: 25px; /* Espacio entre cada reserva */
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); /* Sombra sutil para cada tarjeta */
        }
        .section-title {
            font-weight: 700; /* Más negrita */
            color: var(--green-base); /* Color de destaque */
            margin-bottom: 15px; /* Más espacio */
            font-family: var(--font-heading);
            font-size: 18px; /* Título de sección más grande */
            display: flex;
            align-items: center;
        }
        .section-title::before {
            content: '📌'; /* Icono de pin */
            margin-right: 8px;
            font-size: 1.2em;
            line-height: 1; /* Asegura que el icono no afecte la altura de la línea */
        }
        .dato {
            margin-bottom: 8px; /* Más espacio entre datos */
            display: flex;
            align-items: baseline;
        }
        .dato strong {
            color: var(--green-dark); /* Etiquetas en verde oscuro */
            min-width: 120px; /* Alinea los dos puntos */
            display: inline-block;
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 13px; /* Tamaño de etiqueta consistente */
        }
        .dato span, .dato small {
            font-family: var(--font-body);
            font-size: 14px; /* Tamaño del valor consistente */
            color: var(--text-color);
        }
        .dato small {
            font-size: 12px;
            color: #777;
        }
        .line-separator {
            border-top: 1px dashed #c0c0c0; /* Un guion más claro */
            margin: 15px 0; /* Espacio moderado */
        }
        .total {
            font-weight: 700;
            color: var(--green-dark);
            text-align: right; /* Alineación a la derecha */
            font-size: 18px; /* Total más grande */
            font-family: var(--font-heading);
            margin-top: 15px;
        }
        .resumen-general {
            background: #eaf5ed; /* Fondo claro con un toque verde */
            border: 1px solid var(--green-base);
            border-radius: 10px;
            padding: 25px; /* Más padding */
            margin-top: 40px; /* Más espacio antes del resumen */
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .resumen-general .dato {
            font-size: 16px; /* Datos del resumen más grandes */
            font-family: var(--font-heading);
            font-weight: 600;
            color: var(--green-dark);
        }
        .resumen-general .dato strong {
            color: var(--green-dark);
            min-width: 150px;
        }
        .resumen-general .dato:last-child {
            margin-bottom: 0; /* Eliminar margen del último dato */
        }

        /* Media Queries para responsividad */
        @media (max-width: 768px) {
            body {
                padding: 20px;
            }
            .report-container {
                padding: 20px;
            }
            h2 {
                font-size: 26px;
                margin-bottom: 20px;
            }
            .reserva-section {
                padding: 15px;
            }
            .section-title {
                font-size: 16px;
                margin-bottom: 10px;
            }
            .dato strong {
                min-width: 90px; /* Ajuste para móviles */
                font-size: 12px;
            }
            .dato span, .dato small {
                font-size: 13px;
            }
            .total {
                font-size: 16px;
            }
            .resumen-general {
                padding: 20px;
                margin-top: 30px;
            }
            .resumen-general .dato {
                font-size: 14px;
            }
        }
         /* Estilos específicos para impresión */
        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
            }
            .report-container {
                box-shadow: none;
                border: none; /* Quitamos borde principal si es necesario */
                border-radius: 0;
                margin: 0;
                max-width: initial;
                padding: 0;
            }
            .reserva-section {
                border: 1px solid #ccc; /* Un borde más visible para la impresión */
                box-shadow: none;
                page-break-inside: avoid; /* Evita cortar secciones por la mitad */
                margin-bottom: 15px; /* Menos margen entre secciones para ahorrar espacio */
            }
            .line-separator {
                border-top: 1px solid #ddd; /* Líneas sólidas para impresión */
            }
            h2 {
                font-size: 24px; /* Ajuste para impresión */
            }
            .section-title {
                font-size: 16px; /* Ajuste para impresión */
            }
            .total {
                font-size: 16px; /* Ajuste para impresión */
            }
            .resumen-general {
                box-shadow: none;
                border: 1px solid #ccc;
            }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <h2>🌿 Reporte General de Reservas - Green Vacations Costa Rica 🌿</h2>

        @foreach($reservas as $reserva)
            @php
                $d       = $reserva->detail;
                $tour    = $reserva->tour;
                $aQty    = $d->adults_quantity;
                $kQty    = $d->kids_quantity;
                $aPrice  = $d->adult_price;
                $kPrice  = $d->kid_price;
                $hotel   = $d->is_other_hotel
                                ? $d->other_hotel_name
                                : optional($d->hotel)->name ?? '—';
            @endphp

            <div class="reserva-section">
                <div class="section-title">Código: {{ $reserva->booking_reference }}</div>

                <div class="dato"><strong>Cliente:</strong> <span>{{ optional($reserva->user)->full_name }}</span> <small>({{ optional($reserva->user)->email }})</small></div>
                <div class="dato"><strong>Tour:</strong> <span>{{ $tour->name }}</span></div>
                <div class="dato"><strong>Fecha Reserva:</strong> <span>{{ \Carbon\Carbon::parse($reserva->booking_date)->format('d/m/Y') }}</span></div>
                <div class="dato"><strong>Fecha Tour:</strong> <span>{{ \Carbon\Carbon::parse($d->tour_date)->format('d/m/Y') }}</span></div>
                <div class="dato"><strong>Hotel:</strong> <span>{{ $hotel }}</span></div>
                <div class="dato"><strong>Estado:</strong> <span>{{ ucfirst($reserva->status) }}</span></div>

                <div class="line-separator"></div>

                <div class="dato"><strong>Adultos (x{{ $aQty }}):</strong> ${{ number_format($aPrice,2) }} = <span>${{ number_format($aPrice * $aQty,2) }}</span></div>
                <div class="dato"><strong>Niños (x{{ $kQty }}):</strong> ${{ number_format($kPrice,2) }} = <span>${{ number_format($kPrice * $kQty,2) }}</span></div>
                <div class="dato"><strong>Personas:</strong> <span>{{ $aQty + $kQty }}</span></div>

                <div class="line-separator"></div>

                <div class="total">TOTAL: ${{ number_format($reserva->total,2) }}</div>
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