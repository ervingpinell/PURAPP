<?php

return [
    // Minutes to extend/refresh a cart’s expiration
    'expiry_minutes'   => env('CART_EXPIRY_MINUTES', 15),

    // Inactive carts older than this (days) will be purged by the command
    'purge_after_days' => env('CART_PURGE_AFTER_DAYS', 30),
];
