<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reserva Confirmada</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <h2 style="color: #2E8B57;">¡Hola {{ $booking->user->full_name }}!</h2>

    <p>¡Tu reserva con <strong>Green Vacations</strong> ha sido <strong>confirmada</strong> con éxito! 🎉</p>

    <h4>📅 Detalles de tu Reserva:</h4>
    <ul>
        <li><strong>Tour:</strong> {{ $booking->tour->name }}</li>
        <li><strong>Fecha:</strong> {{ $booking->detail->tour_date->format('d/m/Y') }}</li>
        <li><strong>Adultos:</strong> {{ $booking->detail->adults_quantity }}</li>
        <li><strong>Niños:</strong> {{ $booking->detail->kids_quantity }}</li>
        <li><strong>Hotel:</strong> {{ $booking->detail->hotel->name ?? $booking->detail->other_hotel_name }}</li>
        <li><strong>Total:</strong> ${{ number_format($booking->total, 2) }}</li>
    </ul>

    <p>🙌 Ahora todo está listo. Si tienes alguna pregunta, contáctanos a <a href="mailto:info@greenvacations.com">info@greenvacations.com</a>.</p>

    <p>¡Nos vemos pronto!<br>El equipo de Green Vacations 🌿</p>
</body>
</html>
