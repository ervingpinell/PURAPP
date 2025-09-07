<?php

return [
    // Standard Laravel messages
    'failed'   => 'The email or password is incorrect.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    // Business-specific messages
    'inactive'   => 'Your account is inactive. Contact support to reactivate it.',
    'locked'     => 'Your account is locked. Contact support to unlock it.',
    'unverified' => 'You must verify your email address before logging in. Check your inbox.',

    // Fortify 2FA (status and UI texts)
    'two_factor' => [
        'title'              => 'Two-Factor Authentication',
        'header'             => 'Two-Factor Authentication',
        'enabled'            => 'Two-factor authentication enabled.',
        'confirmed'          => 'Two-factor authentication confirmed.',
        'disabled'           => 'Two-factor authentication disabled.',
        'recovery_codes_generated' => 'New recovery codes have been generated.',
        'remember_device'   => 'Remember this device for 30 days',
        'enter_code'        => 'Enter the 6-digit code',
        'use_recovery'      => 'Use a recovery code',
        'use_authenticator' => 'Use authenticator app',
        'code'              => 'Authentication code',
        'recovery_code'     => 'Recovery code',
        'confirm'           => 'Confirm',
    ],

    // Throttle screen
    'too_many_attempts' => [
        'title'        => 'Too many attempts',
        'intro'        => 'You have made too many login attempts.',
        'blocked_for'  => 'Blocked for ~:minutes min',
        'retry_in'     => 'You can try again in',
        'seconds_hint' => 'The time will update automatically.',
        'generic_wait' => 'Please wait a moment before trying again.',
        'back'         => 'Back',
        'go_login'     => 'Go to login',
    ],

    'throttle_page' => [
        'title'         => 'Too many attempts',
        'message'       => 'You have made too many login attempts.',
        'retry_in'      => 'You can try again in',
        'minutes_abbr'  => 'min',
        'seconds_abbr'  => 's',
        'total_seconds' => 'total seconds',
        'redirecting'   => 'Redirecting…',
    ],
    'remember_me' => 'Remember me',
    'forgot_password' => 'Forgot your password?',
    'send_link' => 'Send link',
    'confirm_password' => 'Confirm password',

    'login' => [
        'remaining_attempts' => '{0} Invalid credentials.|{1} Invalid credentials. You have 1 attempt left before being locked out.|[2,*] Invalid credentials. You have :count attempts left before being locked out.',
    ],

    'account' => [
        'locked'   => 'Your account is locked. Check your email to unlock it.',
        'unlocked' => 'Your account has been unlocked. You can now log in.',
    ],
];
