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
    'hello' => 'Hallo',
'captcha_failed' => 'Die Captcha-Überprüfung ist fehlgeschlagen.',

    // 2. Authentication Messages
    'login_message' => 'Melden Sie sich an, um Ihre Sitzung zu starten',
    'register_message' => 'Ein neues Konto registrieren',
    'password_reset_message' => 'Passwort zurücksetzen',
    'reset_password' => 'Passwort zurücksetzen',
    'send_password_reset_link' => 'Link zum Zurücksetzen senden',
    'i_forgot_my_password' => 'Ich habe mein Passwort vergessen',
    'back_to_login' => 'Zurück zum Login',
    'verify_message' => 'Ihr Konto benötigt eine E-Mail-Bestätigung',
    'verify_email_sent' => 'Ein neuer Bestätigungslink wurde an Ihre E-Mail-Adresse gesendet.',
    'verify_check_email' => 'Bitte überprüfen Sie Ihre E-Mails auf den Bestätigungslink, bevor Sie fortfahren.',
    'verify_if_not_received' => 'Falls Sie die E-Mail nicht erhalten haben',
    'verify_request_another' => 'klicken Sie hier, um eine neue anzufordern',

    // 3. Password Validations
    'passwords' => [
        'reset'     => 'Ihr Passwort wurde zurückgesetzt.',
        'sent'      => 'Wir haben Ihnen den Link zum Zurücksetzen des Passworts per E-Mail gesendet.',
        'throttled' => 'Bitte warten Sie, bevor Sie es erneut versuchen.',
        'token'     => 'Dieses Token zum Zurücksetzen des Passworts ist ungültig.',
        'user'      => "Wir können keinen Benutzer mit dieser E-Mail-Adresse finden.",
        'match'     => 'Passwörter stimmen überein.',
        'link_sent' => 'Ein Link zum Zurücksetzen des Passworts wurde an Ihre E-Mail-Adresse gesendet.',
    ],

    // 4. Password Requirements
    'password_requirements_title' => 'Ihr Passwort muss enthalten:',
    'password_requirements' => [
        'length'  => '- Mindestens 8 Zeichen',
        'special' => '- 1 Sonderzeichen ( .¡!@#$%^&*()_+- )',
        'number'  => '- 1 Zahl',
    ],

    // 5. Buttons and Actions
    'sign_in' => 'Anmelden',
    'sign_out' => 'Abmelden',
    'register' => 'Registrieren',
    'send_link' => 'Link senden',
    'confirm_password' => 'Passwort bestätigen',

    // 6. Account Lock/Unlock
    'account' => [
        'locked_title'     => 'Ihr Konto wurde gesperrt',
        'locked_message'   => 'Sie haben die erlaubte Anzahl an Versuchen überschritten. Aus Sicherheitsgründen wurde Ihr Konto vorübergehend gesperrt.',
        'unlock_hint'      => 'Geben Sie Ihre E-Mail-Adresse ein und wir senden Ihnen einen Link zum Entsperren Ihres Kontos.',
        'send_unlock'      => 'Entsperr-Link senden',
        'unlock_link_sent' => 'Falls das Konto existiert und gesperrt ist, haben wir einen Entsperr-Link gesendet.',
        'unlock_mail_subject' => 'Konto entsperren',
        'unlock_mail_intro'   => 'Wir haben eine Anfrage zum Entsperren Ihres Kontos erhalten.',
        'unlock_mail_action'  => 'Mein Konto entsperren',
        'unlock_mail_outro'   => 'Falls Sie dies nicht waren, ignorieren Sie bitte diese E-Mail.',
        'unlocked'            => 'Ihr Konto wurde entsperrt. Sie können sich jetzt anmelden.',
        'locked' => 'Ihr Konto ist gesperrt. Überprüfen Sie Ihre E-Mails',
    ],

    // 7. Email Verification
    'verify' => [
        'title'     => 'Bestätigen Sie Ihre E-Mail-Adresse',
        'message'   => 'Bitte bestätigen Sie Ihre E-Mail, bevor Sie fortfahren. Wir haben Ihnen einen Bestätigungslink gesendet.',
        'resend'    => 'Bestätigungs-E-Mail erneut senden',
        'link_sent' => 'Wir haben Ihnen einen neuen Bestätigungslink gesendet.',
        'sent_to'      => 'Wir haben einen Bestätigungslink an :email gesendet.',
        'email_label'  => 'E-Mail-Adresse',
        'subject'   => 'Bestätigen Sie Ihre E-Mail-Adresse',
        'intro'     => 'Bitte klicken Sie auf die Schaltfläche, um Ihre E-Mail-Adresse zu bestätigen.',
        'action'    => 'E-Mail-Adresse bestätigen',
        'outro'     => 'Falls Sie dieses Konto nicht erstellt haben, ignorieren Sie bitte diese Nachricht.',
        'browser_hint' => 'Wenn Sie Probleme beim Klicken auf die Schaltfläche ":action" haben, kopieren Sie diese URL und fügen Sie sie in Ihren Browser ein: :url',
        'verified_success' => 'Ihre E-Mail wurde erfolgreich bestätigt.',
        'verify_email_title'        => 'E-Mail bestätigen',
        'verify_email_header'       => 'Überprüfen Sie Ihren Posteingang',
        'verify_email_sent'         => 'Wir haben einen Bestätigungslink an Ihre E-Mail gesendet.',
        'verify_email_sent_to'      => 'Wir haben den Bestätigungslink gerade gesendet an:',
        'verify_email_generic'      => 'Wir haben Ihnen gerade einen Bestätigungslink an Ihre E-Mail gesendet.',
        'verify_email_instructions' => 'Öffnen Sie die E-Mail und klicken Sie auf den Link, um Ihr Konto zu aktivieren. Falls Sie die E-Mail nicht sehen, überprüfen Sie Ihren Spam-Ordner.',
        'back_to_login'             => 'Zurück zum Login',
        'back_to_home'              => 'Zur Startseite',
    ],

    // 8. Unauthorized Admin
    'unauthorized_admin' => 'Zugriff verweigert. Sie haben keine Berechtigung, auf das Admin-Panel zuzugreifen.',

    // 9. Account Locked Message
    'locked' => 'Ihr Konto ist gesperrt. Überprüfen Sie Ihre E-Mails, um es zu entsperren, oder kontaktieren Sie den Support.',

    // 10. Two Factor Authentication
    'two_factor' => [
        'title'                => 'Zwei-Faktor-Authentifizierung',
        'message'              => 'Geben Sie Ihren Authentifizierungscode oder einen Wiederherstellungscode ein.',
        'code_placeholder'     => 'Code 123456',
        'recovery_placeholder' => 'Wiederherstellungscode',
        'remember_device'      => 'Dieses Gerät merken',
        'verify_button'        => 'Bestätigen',
    ],

    // 11. Two Factor Notices
    'notices' => [
        'two_factor_confirmed'             => 'Zwei-Faktor-Authentifizierung aktiviert.',
        'two_factor_recovery_regenerated'  => 'Ihre Wiederherstellungscodes wurden neu generiert.',
        'two_factor_disabled'              => 'Zwei-Faktor-Authentifizierung wurde deaktiviert.',
        'two_factor_invalid_code'          => 'Der Code ist ungültig. Bitte versuchen Sie es erneut.',
        'two_factor_invalid_recovery_code' => 'Der Wiederherstellungscode ist ungültig oder wurde bereits verwendet.',
    ],

];
