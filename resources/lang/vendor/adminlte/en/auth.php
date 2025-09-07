<?php

/*
|--------------------------------------------------------------------------
|  1. Greetings ............................................. Line 10
|  2. Authentication Messages ............................... Line 13
|  3. Password Validations .................................. Line 27
|  4. Password Requirements ................................. Line 38
|  5. Buttons and Actions ................................... Line 46
|  6. Account Lock/Unlock ................................... Line 53
|  7. Email Verification .................................... Line 67
|  8. Unauthorized Admin .................................... Line 87
|  9. Account Locked Message ................................ Line 90
| 10. Two Factor Authentication ............................. Line 93
| 11. Two Factor Notices .................................... Line 102
|--------------------------------------------------------------------------
*/

return [

    // 1. Greetings
    'hello' => 'Hello',

    // 2. Authentication Messages
    'login_message' => 'Sign in to start your session',
    'register_message' => 'Register a new account',
    'password_reset_message' => 'Reset password',
    'reset_password' => 'Reset password',
    'send_password_reset_link' => 'Send reset link',
    'i_forgot_my_password' => 'I forgot my password',
    'back_to_login' => 'Back to login',
    'verify_message' => 'Your account needs email verification',
    'verify_email_sent' => 'A new verification link has been sent to your email address.',
    'verify_check_email' => 'Before proceeding, check your email for the verification link.',
    'verify_if_not_received' => 'If you did not receive the email',
    'verify_request_another' => 'click here to request another',

    // 3. Password Validations
    'passwords' => [
        'reset'     => 'Your password has been reset.',
        'sent'      => 'We have emailed your password reset link.',
        'throttled' => 'Please wait before retrying.',
        'token'     => 'This password reset token is invalid.',
        'user'      => "We can't find a user with that email address.",
        'match'     => 'Passwords match.',
        'link_sent' => 'A password reset link has been sent to your email address.',
    ],

    // 4. Password Requirements
    'password_requirements_title' => 'Your password must contain:',
    'password_requirements' => [
        'length'  => '- At least 8 characters',
        'special' => '- 1 special character ( .¡!@#$%^&*()_+- )',
        'number'  => '- 1 number',
    ],

    // 5. Buttons and Actions
    'sign_in' => 'Sign in',
    'sign_out' => 'Sign out',
    'register' => 'Register',
    'send_link' => 'Send link',
    'confirm_password' => 'Confirm password',

    // 6. Account Lock/Unlock
    'account' => [
        'locked_title'     => 'Your account has been locked',
        'locked_message'   => 'You have exceeded the allowed number of attempts. For security, your account was temporarily locked.',
        'unlock_hint'      => 'Enter your email and we will send you a link to unlock your account.',
        'send_unlock'      => 'Send unlock link',
        'unlock_link_sent' => 'If the account exists and is locked, we have sent an unlock link.',
        'unlock_mail_subject' => 'Account Unlock',
        'unlock_mail_intro'   => 'We received a request to unlock your account.',
        'unlock_mail_action'  => 'Unlock my account',
        'unlock_mail_outro'   => 'If this was not you, please ignore this email.',
        'unlocked'            => 'Your account has been unlocked. You can now sign in.',
        'locked'              => 'Your account is locked.',
    ],

    // 7. Email Verification
    'verify' => [
        'title'     => 'Verify your email address',
        'message'   => 'Before proceeding, please verify your email. We have sent you a verification link.',
        'resend'    => 'Resend verification email',
        'link_sent' => 'We have sent you a new verification link.',
        'subject'   => 'Verify your email address',
        'intro'     => 'Please click the button to verify your email address.',
        'action'    => 'Verify email address',
        'outro'     => 'If you did not create this account, please ignore this message.',
        'browser_hint' => 'If you’re having trouble clicking the ":action" button, copy and paste this URL into your browser: :url',
        'verified_success' => 'Your email has been successfully verified.',
        'verify_email_title'        => 'Verify your email',
        'verify_email_header'       => 'Check your inbox',
        'verify_email_sent'         => 'We have sent a verification link to your email.',
        'verify_email_sent_to'      => 'We just sent the verification link to:',
        'verify_email_generic'      => 'We just sent you a verification link to your email.',
        'verify_email_instructions' => 'Open the email and click the link to activate your account. If you don’t see it, check your spam folder.',
        'back_to_login'             => 'Back to login',
        'back_to_home'              => 'Go to home',
    ],

    // 8. Unauthorized Admin
    'unauthorized_admin' => 'Access denied. You do not have permission to access the admin panel.',

    // 9. Account Locked Message
    'locked' => 'Your account is locked. Check your email to unlock it or contact support.',

    // 10. Two Factor Authentication
    'two_factor' => [
        'title'                => 'Two-factor authentication',
        'message'              => 'Enter your authentication code or a recovery code.',
        'code_placeholder'     => 'Code 123456',
        'recovery_placeholder' => 'Recovery code',
        'remember_device'      => 'Remember this device',
        'verify_button'        => 'Verify',
    ],

    // 11. Two Factor Notices
    'notices' => [
        'two_factor_confirmed'             => 'Two-factor authentication enabled.',
        'two_factor_recovery_regenerated'  => 'Your recovery codes have been regenerated.',
        'two_factor_disabled'              => 'Two-factor authentication has been disabled.',
        'two_factor_invalid_code'          => 'The code is invalid. Please try again.',
        'two_factor_invalid_recovery_code' => 'The recovery code is invalid or has already been used.',
    ],

];
