<?php

return [
    'public_readonly'           => env('PUBLIC_READONLY', false),
    'allow_public_registration' => env('ALLOW_PUBLIC_REGISTRATION', false),
    'allow_public_login'        => env('ALLOW_PUBLIC_LOGIN', true),
    'allow_guest_checkout'      => env('ALLOW_GUEST_CHECKOUT', true),
];
