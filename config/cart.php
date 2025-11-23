<?php

return [
    // Read from settings via helper function at runtime, not during config load
    // Default to 30 minutes
    'expiration_minutes' => env('CART_EXPIRATION_MINUTES', 30),

    // Cleanup settings
    'purge_expired_after_hours' => env('CART_PURGE_EXPIRED_AFTER_HOURS', 24),
    'purge_inactive_after_days' => env('CART_PURGE_INACTIVE_AFTER_DAYS', 7),
    'purge_empty_after_days' => env('CART_PURGE_EMPTY_AFTER_DAYS', 7),
];
