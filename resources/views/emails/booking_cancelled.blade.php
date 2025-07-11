<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cancelación de Reserva</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <h2 style="color: #D9534F;">¡Hola {{ $booking->user->full_name }}!</h2>

    <p>Lamentamos informarte que tu reserva con <strong>Green Vacations</strong> ha sido <strong>cancelada</strong>.</p>

    <h4>📅 Detalles de la Reserva Cancelada:</h4>
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
        <li><strong>Estado:</strong> Cancelada</li>
    </ul>

    <p>Si necesitas reprogramar tu reserva o tienes dudas, por favor contáctanos a 
        <a href="mailto:info@greenvacations.com">info@greenvacations.com</a>.
    </p>

    <p>Sentimos los inconvenientes y esperamos poder ayudarte pronto.<br>
    El equipo de Green Vacations 🌿</p>
</body>
</html>
