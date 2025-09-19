<?php

return [
    'viator' => [
        // Ejemplo: https://api.sandbox.viator.com/partner/reviews/product
        'base'   => env('VIATOR_REVIEWS_BASE', 'https://api.viator.com/partner/reviews/product'),
        'header' => env('VIATOR_API_KEY_HEADER', 'exp-api-key'),
    ],
];
