<?php

return [

    'expiry_minutes' => env('CART_EXPIRY_MINUTES', 1),
    'extend_minutes' => env('CART_EXTEND_MINUTES', 5),
    'max_extensions' => env('CART_MAX_EXTENSIONS', 1),
    'purge_expired_after_hours' => env('CART_PURGE_EXPIRED_AFTER_HOURS', 24),
    'purge_inactive_after_days' => env('CART_PURGE_INACTIVE_AFTER_DAYS', default: 7),
    'purge_empty_after_days' => env('CART_PURGE_EMPTY_AFTER_DAYS', 7),
];
