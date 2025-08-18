<?php

return [

    'hello' => 'Hello',

    'login_message' => 'Sign in to start your session',
    'register_message' => 'Register a new account',
    'password_reset_message' => 'Reset Password',
    'reset_password' => 'Reset Password',
    'send_password_reset_link' => 'Send password reset link',
    'i_forgot_my_password' => 'I forgot my password',
    'back_to_login' => 'Back to login',
    'verify_message' => 'Your account needs email verification',
    'verify_email_sent' => 'A new verification link has been sent to your email.',
    'verify_check_email' => 'Before continuing, please check your email for the verification link.',
    'verify_if_not_received' => 'If you did not receive the email',
    'verify_request_another' => 'click here to request another',

    'passwords' => [
        'reset'     => 'Your password has been reset.',
        'sent'      => 'We have emailed your password reset link.',
        'throttled' => 'Please wait before retrying.',
        'token'     => 'This password reset token is invalid.',
        'user'      => "We can’t find a user with that email address.",
        'match'     => 'Passwords match.',
        'link_sent' => 'A password reset link has been sent to your email address.',
    ],

    'password_requirements_title' => 'Your password must contain:',
    'password_requirements' => [
        'length'  => '- At least 8 characters',
        'special' => '- 1 special character ( .¡!@#$%^&*()_+- )',
        'number'  => '- 1 number',
    ],

    'sign_in' => 'Sign in',
    'sign_out' => 'Sign out',
    'register' => 'Register',
    'send_link' => 'Send link',
    'confirm_password' => 'Confirm Password',

    'account' => [
        'locked_title'     => 'Your account has been locked',
        'locked_message'   => 'You exceeded the allowed number of attempts. For security, your account was temporarily locked.',
        'unlock_hint'      => 'Enter your email and we will send you an unlock link.',
        'send_unlock'      => 'Send unlock link',
        'unlock_link_sent' => 'If the account exists and is locked, we sent an unlock link.',
        'unlock_mail_subject' => 'Account Unlock',
        'unlock_mail_intro'   => 'We received a request to unlock your account.',
        'unlock_mail_action'  => 'Unlock my account',
        'unlock_mail_outro'   => 'If this was not you, please ignore this email.',
        'unlocked'            => 'Your account has been unlocked. You can now sign in.',
        'locked'              => 'Your account is locked.',
    ],

    'verify' => [
        'title'     => 'Verify your email address',
        'message'   => 'Before continuing, please verify your email. We have sent you a verification link.',
        'resend'    => 'Resend verification email',
        'link_sent' => 'We have sent you a new verification link.',
        'subject'   => 'Verify your email address',
        'intro'     => 'Please click the button to verify your email address.',
        'action'    => 'Verify email address',
        'outro'     => 'If you did not create this account, please ignore this message.',
        'browser_hint' => 'If you have trouble clicking the ":action" button, copy and paste this URL into your browser: :url',
        'verified_success' => 'Your email has been successfully verified.',
        'verify_email_title'        => 'Verify your email',
        'verify_email_header'       => 'Check your inbox',
        'verify_email_sent'         => 'We sent you a verification link to your email.',
        'verify_email_sent_to'      => 'We just sent the verification link to:',
        'verify_email_generic'      => 'We just sent you a verification link to your email.',
        'verify_email_instructions' => 'Open the email and click the link to activate your account. If you don’t see it, check your spam folder.',
        'back_to_login'             => 'Back to login',
        'back_to_home'              => 'Back to home',
    ],

];
