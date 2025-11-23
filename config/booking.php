<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Booking Configuration
    |--------------------------------------------------------------------------
    | 
    | IMPORTANTE: No usar setting() aquí porque config se carga antes que DB
    | Usar setting() directamente en los controladores/servicios donde se necesite
    */

    // Capacidad de reserva
    'max_persons_per_booking' => env('BOOKING_MAX_PERSONS', 12),
    'min_adults_per_booking'  => env('BOOKING_MIN_ADULTS', 0),


    // Estados que cuentan para capacidad
    'count_statuses' => ['confirmed', 'pending'],

    // Días de anticipación
    'min_days_advance' => env('BOOKING_MIN_DAYS_ADVANCE', 1),
    'max_days_advance' => env('BOOKING_MAX_DAYS_ADVANCE', 365),

    // Payment & Confirmation
    'auto_confirm_on_payment' => env('BOOKING_AUTO_CONFIRM', true),

    // Notifications
    'send_confirmation_email' => env('BOOKING_SEND_CONFIRMATION', true),
    'send_reminder_email'     => env('BOOKING_SEND_REMINDER', true),
    'reminder_hours_before'   => env('BOOKING_REMINDER_HOURS', 24),

    // Cancellation
    'allow_cancellation'           => env('BOOKING_ALLOW_CANCELLATION', true),
    'cancellation_hours_before'    => env('BOOKING_CANCELLATION_HOURS', 24),
    'cancellation_refund_percent'  => env('BOOKING_CANCELLATION_REFUND', 80),

    // Modificación
    'allow_modification'        => env('BOOKING_ALLOW_MODIFICATION', true),
    'modification_hours_before' => env('BOOKING_MODIFICATION_HOURS', 48),

    // Cleanup (legacy - now handled by cart cleanup)
    'auto_cancel_pending_after_hours' => env('BOOKING_AUTO_CANCEL_PENDING', 24),
    'purge_cancelled_after_days'      => env('BOOKING_PURGE_CANCELLED', 90),

    // Cutoff (usado como fallback si no existe en settings)
    'cutoff_hour' => env('BOOKING_CUTOFF_HOUR', '18:00'),
    'lead_days'   => env('BOOKING_LEAD_DAYS', 1),
];
