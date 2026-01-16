<?php

return [
    // Email
    'email_subject' => 'Complete Your Account Setup',

    // Setup page
    'title' => 'Complete Your Account Setup',
    'welcome' => 'Welcome, :name!',
    'booking_confirmed' => 'Your booking #:reference has been confirmed.',
    'create_password' => 'Create a password to:',
    'benefits' => [
        'view_bookings' => 'View all your bookings',
        'manage_profile' => 'Manage your profile',
        'exclusive_offers' => 'Receive exclusive offers',
    ],
    'password_label' => 'Password',
    'confirm_password_label' => 'Confirm Password',
    'submit_button' => 'Create My Account',
    'maybe_later' => 'Maybe later',

    // Validation
    'password_requirements' => 'Password must contain at least 1 number and 1 special character (.¡!@#$%^&*()_+-)',
    'password_min_length' => 'Password must be at least 8 characters',
    'requirements' => [
        'one_number' => 'At least 1 number',
        'one_special_char' => 'At least 1 special character',
    ],

    // Email Welcome
    'email_welcome_subject' => 'Welcome to ' . config('app.name') . '!',
    'email_welcome_title' => 'Welcome, :name!',
    'email_welcome_text' => 'Your account has been successfully created. You can now access your bookings and manage your profile.',
    'email_action_button' => 'Go to My Dashboard',

    // JS / Strength
    'strength' => [
        'weak' => 'Weak',
        'medium' => 'Medium',
        'strong' => 'Strong',
    ],
    'passwords_do_not_match' => 'Passwords do not match',
    'creating_account' => 'Creating account...',
    'payment_success_message' => 'Payment Successful & Booking Confirmed!',

    // Messages
    'token_expired' => 'This link has expired. Please request a new one.',
    'token_invalid' => 'Invalid setup link.',
    'expires_in' => 'This link expires in :days days',
    'fallback_link' => 'If the button does not work, copy and paste this link into your browser:',
    'success' => 'Account created successfully! You can now log in.',
    'user_not_found' => 'User not found.',
    'already_has_password' => 'This user already has a password set.',
    'too_many_requests' => 'Too many requests. Please try again later.',
    'send_failed' => 'Failed to send email. Please try again later.',
];
