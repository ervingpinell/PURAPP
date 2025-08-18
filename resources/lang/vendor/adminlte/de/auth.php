<?php

return [

    'hello' => 'Hallo',

    'login_message' => 'Melden Sie sich an, um Ihre Sitzung zu starten',
    'register_message' => 'Ein neues Konto registrieren',
    'password_reset_message' => 'Passwort zurücksetzen',
    'reset_password' => 'Passwort zurücksetzen',
    'send_password_reset_link' => 'Link zum Zurücksetzen senden',
    'i_forgot_my_password' => 'Ich habe mein Passwort vergessen',
    'back_to_login' => 'Zurück zur Anmeldung',
    'verify_message' => 'Ihr Konto muss per E-Mail bestätigt werden',
    'verify_email_sent' => 'Ein neuer Bestätigungslink wurde an Ihre E-Mail gesendet.',
    'verify_check_email' => 'Bitte prüfen Sie vor dem Fortfahren Ihre E-Mail auf den Bestätigungslink.',
    'verify_if_not_received' => 'Wenn Sie die E-Mail nicht erhalten haben',
    'verify_request_another' => 'klicken Sie hier, um eine neue anzufordern',

    'passwords' => [
        'reset'     => 'Ihr Passwort wurde zurückgesetzt.',
        'sent'      => 'Wir haben Ihnen den Link zum Zurücksetzen per E-Mail gesendet.',
        'throttled' => 'Bitte warten Sie, bevor Sie es erneut versuchen.',
        'token'     => 'Dieses Passwort-Reset-Token ist ungültig.',
        'user'      => "Wir können keinen Benutzer mit dieser E-Mail-Adresse finden.",
        'match'     => 'Passwörter stimmen überein.',
        'link_sent' => 'Ein Link zum Zurücksetzen wurde an Ihre E-Mail gesendet.',
    ],

    'password_requirements_title' => 'Ihr Passwort muss enthalten:',
    'password_requirements' => [
        'length'  => '- Mindestens 8 Zeichen',
        'special' => '- 1 Sonderzeichen ( .¡!@#$%^&*()_+- )',
        'number'  => '- 1 Zahl',
    ],

    'sign_in' => 'Anmelden',
    'sign_out' => 'Abmelden',
    'register' => 'Registrieren',
    'send_link' => 'Link senden',
    'confirm_password' => 'Passwort bestätigen',

    'account' => [
        'locked_title'     => 'Ihr Konto wurde gesperrt',
        'locked_message'   => 'Sie haben die zulässige Anzahl von Versuchen überschritten. Aus Sicherheitsgründen wurde Ihr Konto vorübergehend gesperrt.',
        'unlock_hint'      => 'Geben Sie Ihre E-Mail ein, und wir senden Ihnen einen Entsperr-Link.',
        'send_unlock'      => 'Entsperr-Link senden',
        'unlock_link_sent' => 'Wenn das Konto existiert und gesperrt ist, haben wir einen Entsperr-Link gesendet.',
        'unlock_mail_subject' => 'Kontofreischaltung',
        'unlock_mail_intro'   => 'Wir haben eine Anfrage zum Entsperren Ihres Kontos erhalten.',
        'unlock_mail_action'  => 'Mein Konto entsperren',
        'unlock_mail_outro'   => 'Wenn dies nicht von Ihnen war, ignorieren Sie bitte diese E-Mail.',
        'unlocked'            => 'Ihr Konto wurde entsperrt. Sie können sich jetzt anmelden.',
        'locked'              => 'Ihr Konto ist gesperrt.',
    ],

    'verify' => [
        'title'     => 'Bestätigen Sie Ihre E-Mail-Adresse',
        'message'   => 'Bitte bestätigen Sie Ihre E-Mail, bevor Sie fortfahren. Wir haben Ihnen einen Bestätigungslink gesendet.',
        'resend'    => 'Bestätigungs-E-Mail erneut senden',
        'link_sent' => 'Wir haben Ihnen einen neuen Bestätigungslink gesendet.',
        'subject'   => 'Bestätigen Sie Ihre E-Mail-Adresse',
        'intro'     => 'Bitte klicken Sie auf die Schaltfläche, um Ihre E-Mail-Adresse zu bestätigen.',
        'action'    => 'E-Mail bestätigen',
        'outro'     => 'Wenn Sie dieses Konto nicht erstellt haben, ignorieren Sie bitte diese Nachricht.',
        'browser_hint' => 'Wenn Sie Probleme beim Klicken auf die Schaltfläche ":action" haben, kopieren Sie diese URL und fügen Sie sie in Ihren Browser ein: :url',
        'verified_success' => 'Ihre E-Mail wurde erfolgreich bestätigt.',
        'verify_email_title'        => 'Bestätigen Sie Ihre E-Mail',
        'verify_email_header'       => 'Überprüfen Sie Ihren Posteingang',
        'verify_email_sent'         => 'Wir haben einen Bestätigungslink an Ihre E-Mail gesendet.',
        'verify_email_sent_to'      => 'Wir haben den Bestätigungslink gerade gesendet an:',
        'verify_email_generic'      => 'Wir haben Ihnen gerade einen Bestätigungslink gesendet.',
        'verify_email_instructions' => 'Öffnen Sie die E-Mail und klicken Sie auf den Link, um Ihr Konto zu aktivieren. Wenn Sie sie nicht sehen, prüfen Sie den Spam-Ordner.',
        'back_to_login'             => 'Zurück zur Anmeldung',
        'back_to_home'              => 'Zurück zur Startseite',
    ],

];
