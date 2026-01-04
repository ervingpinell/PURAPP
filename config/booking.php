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
    'max_days_advance' => env('BOOKING_MAX_DAYS_ADVANCE', 730),

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

    // Reserve-Now-Pay-Later (fallback values, real values from settings table)
    'pay_later' => [
        'enabled' => env('PAY_LATER_ENABLED', false),
        'days_before_auto_charge' => env('PAY_LATER_DAYS_BEFORE', 2),
        'reminder_days_before' => env('PAY_LATER_REMINDER_DAYS', 3),
        'payment_failure_grace_hours' => 24,
        'checkout_link_expires_hours' => 72, // Payment link valid for 72h
    ],

    // Hold & Expiration Times (minutes)
    'hold_times' => [
        'unpaid_booking' => 720,         // 12 hours for unpaid bookings
        'extension_duration' => 720,     // 12 hours per extension
        'max_extensions' => 3,           // Maximum extensions allowed
    ],

    // Admin Notifications
    'admin_notifications' => [
        'unpaid_expiry_warning_hours' => 2,  // Warn admin 2h before expiry
        // Uses 'email.notification_email' setting from database
    ],

    // Operations Email Configuration
    'operations_email' => env('BOOKING_OPERATIONS_EMAIL', 'info@greenvacationscr.com'),

    // Email sender configuration
    'email_config' => [
        'from' => 'noreply@greenvacationscr.com',
        'reply_to' => 'info@greenvacationscr.com',
    ],
];
