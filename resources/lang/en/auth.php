<?php


return [
    // Standard Laravel messages
    'failed'   => 'The email or password is incorrect.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'captcha_failed' => 'CAPTCHA verification failed. Please try again.',

    // Business-specific messages
    'inactive'   => 'Your account is inactive. Please contact support to reactivate it.',
    'locked'     => 'Your account is locked. Please contact support to unlock it.',
    'unverified' => 'You must verify your email address before logging in. Please check your inbox.',

    // Fortify 2FA (status & UI text)
    'two_factor' => [
        'title'                   => 'Two-Factor Authentication',
        'header'                  => 'Two-Factor Authentication',
        'enabled'                 => 'Two-factor authentication enabled.',
        'confirmed'               => 'Two-factor authentication confirmed.',
        'disabled'                => 'Two-factor authentication disabled.',
        'recovery_codes_generated' => 'New recovery codes have been generated.',
        'remember_device'         => 'Remember this device for 30 days',
        'enter_code'              => 'Enter the 6-digit code',
        'use_recovery'            => 'Use a recovery code',
        'use_authenticator'       => 'Use authenticator app',
        'code'                    => 'Authentication code',
        'recovery_code'           => 'Recovery code',
        'confirm'                 => 'Confirm',
    ],

    // Throttle screen
    'too_many_attempts' => [
        'title'        => 'Too Many Attempts',
        'intro'        => 'You have made too many login attempts.',
        'blocked_for'  => 'Blocked for ~:minutes min',
        'retry_in'     => 'You can try again in',
        'seconds_hint' => 'Time will update automatically.',
        'generic_wait' => 'Please wait a moment before trying again.',
        'back'         => 'Back',
        'go_login'     => 'Go to login',
    ],

    'throttle_page' => [
        'title'         => 'Too Many Attempts',
        'message'       => 'You have made too many login attempts.',
        'retry_in'      => 'You can try again in',
        'minutes_abbr'  => 'min',
        'seconds_abbr'  => 's',
        'total_seconds' => 'total seconds',
        'redirecting'   => 'Redirecting…',
    ],

    'remember_me'      => 'Remember me',
    'forgot_password'  => 'Forgot your password?',
    'send_link'        => 'Send link',
    'confirm_password' => 'Confirm password',

    'login' => [
        'remaining_attempts' => '{0} Invalid credentials.|{1} Invalid credentials. You have 1 attempt left before lockout.|[2,*] Invalid credentials. You have :count attempts left before lockout.',
    ],

    'account' => [
        'locked'   => 'Your account is locked. Check your email to unlock it.',
        'unlocked' => 'Your account has been unlocked. You can now log in.',
    ],

    'verify.verified' => 'Your email has been verified. You can now log in.',

    'verify' => [
        'already'  => 'You have already verified your email,',
        'verified' => 'Your email has been verified.',
    ],
    'email_change_subject'        => 'Confirm your email address change',
    'email_change_title'          => 'Confirm your new email address',
    'email_change_hello'          => 'Hello :name,',
    'email_change_intro'          => 'You have requested to change the email address associated with your account. To complete the process, click the button below:',
    'email_change_button'         => 'Confirm new email',
    'email_change_footer'         => 'If you did not request this change, you may ignore this message and your current email will remain unchanged.',
    'email_change_link_expired'   => 'The link to change your email has expired. Please request the change again from your profile.',
    'email_change_confirmed'      => 'Your email address has been successfully updated and verified.',

    'reset_password' => [
        'subject'    => 'Reset Password Notification',
        'greeting'   => 'Hello!',
        'line1'      => 'You are receiving this email because we received a password reset request for your account.',
        'action'     => 'Reset Password',
        'line2'      => 'This password reset link will expire in :count minutes.',
        'line3'      => 'If you did not request a password reset, no further action is required.',
        'salutation' => 'Regards,',
    ],

    'email_updated_notification' => [
        'subject'         => 'Your email address has been updated',
        'greeting'        => 'Hello!',
        'message'         => 'The email address for your account has been successfully updated to: :email',
        'contact_support' => 'If you did not make this change, please contact support.',
        'salutation'      => 'Regards,',
    ],

    'password_updated_notification' => [
        'subject'         => 'Your password has been updated',
        'greeting'        => 'Hello!',
        'line1'           => 'This is a confirmation that the password for your account has been successfully changed.',
        'line2'           => 'If you did not make this change, please contact support immediately.',
        'action'          => 'Login',
        'salutation'      => 'Regards,',
    ],

    // Password setup (guest to registered)
    'no_password_set' => 'You haven\'t set a password yet.',
    'send_setup_link' => 'Send setup link',
    'setup_link_sent' => 'Setup link sent to your email.',
    'create_account' => 'Create Account',
    'verify_email' => [
        'subject' => 'Verify Email Address',
        'title' => 'Verify Your Email Address',
        'line_1' => 'Please click the button below to verify your email address.',
        'action' => 'Verify Email Address',
        'line_2' => 'If you did not create an account, no further action is required.',
        'button_trouble' => 'If you\'re having trouble clicking the ":actionText" button, copy and paste the URL below into your web browser:',
    ],
    'account_created_verify_email' => 'Account created successfully. Please check your email to verify your account.',
];
