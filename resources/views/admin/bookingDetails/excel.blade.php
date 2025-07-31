<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Exportación de Reservas</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>ID Reserva</th>
                <th>Referencia</th>
                <th>Estado</th>
                <th>Fecha Reserva</th>
                <th>Cliente</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Tour</th>
                <th>Fecha Tour</th>
                <th>Hotel</th>
                <th>Horario</th>
                <th>Tipo</th>
                <th>Adultos</th>
                <th>Niños</th>
                <th>Precio Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAdultos = 0;
                $totalNinos = 0;
                $totalDinero = 0;
            @endphp

            @foreach($bookings as $reserva)
                @php
                    $detalle  = $reserva->detail->first();
                    $tour     = $detalle->tour ?? null;
                    $hotel    = $detalle->hotel ?? null;
                    $schedule = $detalle->schedule ?? null;

                    $adultos = $detalle->adults_quantity ?? 0;
                    $ninos   = $detalle->kids_quantity ?? 0;

                    $totalAdultos += $adultos;
                    $totalNinos   += $ninos;
                    $totalDinero  += $reserva->total;
                @endphp
                <tr>
                    <td>{{ $reserva->booking_id }}</td>
                    <td>{{ $reserva->booking_reference }}</td>
                    <td>{{ ucfirst($reserva->status) }}</td>
                    <td>{{ $reserva->booking_date ?? '—' }}</td>
                    <td>{{ $reserva->user->full_name ?? '—' }}</td>
                    <td>{{ $reserva->user->email ?? '—' }}</td>
                    <td>{{ $reserva->user->phone ?? '—' }}</td>
                    <td>{{ $tour ? preg_replace('/\s*\([^)]*\)/', '', $tour->name) : '—' }}</td>
                    <td>{{ $detalle->tour_date ?? '—' }}</td>
                    <td>{{ $detalle->is_other_hotel ? $detalle->other_hotel_name : ($hotel->name ?? '—') }}</td>
                    <td>{{ $schedule ? $schedule->start_time . ' - ' . $schedule->end_time : '—' }}</td>
                    <td>{{ $tour && $tour->tourType ? $tour->tourType->name : '—' }}</td>
                    <td>{{ $adultos }}</td>
                    <td>{{ $ninos }}</td>
                    <td>{{ number_format($reserva->total, 2) }}</td>
                </tr>
            @endforeach

            {{-- ✅ Fila con totales --}}
            <tr style="font-weight: bold; background-color: #f2f2f2;">
                <td colspan="12" style="text-align: right;">Totales:</td>
                <td>{{ $totalAdultos }}</td>
                <td>{{ $totalNinos }}</td>
                <td>${{ number_format($totalDinero, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
