<?php

return [
    // Hora de corte diaria (24h). Si hoy ya pasó esta hora, "mañana" deja de estar disponible.
    'cutoff_hour' => env('BOOKING_CUTOFF_HOUR', '18:00'),

    // Días mínimos de antelación "base". 1 = mañana (si no pasó el cutoff).
    'lead_days'   => (int) env('BOOKING_LEAD_DAYS', 1),
];
