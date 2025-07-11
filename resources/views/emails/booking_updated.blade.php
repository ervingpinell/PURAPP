<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualización de Reserva</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <h2 style="color: #2E8B57;">¡Hola {{ $booking->user->full_name }}!</h2>

    <p>Queremos informarte que tu reserva con <strong>Green Vacations</strong> ha sido <strong>actualizada</strong> con éxito.</p>

    <h4>📅 Detalles de tu Reserva Actualizada:</h4>
    <ul>
        <li><strong>Código de Reserva:</strong> {{ $booking->booking_reference }}</li>
        <li><strong>Tour:</strong> {{ $booking->tour->name }}</li>
        <li><strong>Fecha:</strong> {{ optional($booking->details->first())->tour_date ? \Carbon\Carbon::parse($booking->details->first()->tour_date)->format('d/m/Y') : '-' }}</li>
        <li><strong>Adultos:</strong> {{ $booking->details->first()->adults_quantity ?? '-' }}</li>
        <li><strong>Niños:</strong> {{ $booking->details->first()->kids_quantity ?? '-' }}</li>
        <li><strong>Hotel:</strong>
            @if(optional($booking->details->first())->is_other_hotel)
                {{ $booking->details->first()->other_hotel_name }}
            @else
                {{ optional($booking->details->first()->hotel)->name ?? '-' }}
            @endif
        </li>
        <li><strong>Estado:</strong> {{ ucfirst($booking->status) }}</li>
        <li><strong>Total:</strong> ${{ number_format($booking->total, 2) }}</li>
    </ul>

    <p>Si tienes alguna consulta sobre este cambio, contáctanos a 
        <a href="mailto:info@greenvacations.com">info@greenvacations.com</a>.
    </p>

    <p>¡Gracias por confiar en nosotros!<br>
    El equipo de Green Vacations 🌿</p>
</body>
</html>
