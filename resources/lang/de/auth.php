<?php

return [
    // Standard Laravel-Nachrichten
    'failed'   => 'Die E-Mail-Adresse oder das Passwort ist falsch.',
    'password' => 'Das angegebene Passwort ist falsch.',
    'throttle' => 'Zu viele Anmeldeversuche. Bitte versuchen Sie es in :seconds Sekunden erneut.',

    // Geschäftsspezifische Nachrichten
    'inactive'   => 'Ihr Konto ist inaktiv. Kontaktieren Sie den Support, um es zu reaktivieren.',
    'locked'     => 'Ihr Konto ist gesperrt. Kontaktieren Sie den Support, um es zu entsperren.',
    'unverified' => 'Sie müssen Ihre E-Mail-Adresse verifizieren, bevor Sie sich anmelden können. Überprüfen Sie Ihr Postfach.',

    // Fortify 2FA (Status- und UI-Texte)
    'two_factor' => [
        'title'                 => 'Zwei-Faktor-Authentifizierung',
        'header'                 => 'Zwei-Faktor-Authentifizierung',
        'enabled'                  => 'Zwei-Faktor-Authentifizierung aktiviert.',
        'confirmed'                => 'Zwei-Faktor-Authentifizierung bestätigt.',
        'disabled'                 => 'Zwei-Faktor-Authentifizierung deaktiviert.',
        'recovery_codes_generated' => 'Neue Wiederherstellungscodes wurden generiert.',
        'remember_device'   => 'Dieses Gerät 30 Tage merken',
        'enter_code'        => 'Geben Sie den 6-stelligen Code ein',
        'use_recovery'      => 'Wiederherstellungscode verwenden',
        'use_authenticator' => 'Authenticator-App verwenden',
        'code'              => 'Authentifizierungscode',
        'recovery_code'     => 'Wiederherstellungscode',
        'confirm'           => 'Bestätigen',
    ],

    // Sperrbildschirm
    'too_many_attempts' => [
        'title'        => 'Zu viele Versuche',
        'intro'        => 'Sie haben zu viele Anmeldeversuche unternommen.',
        'blocked_for'  => 'Gesperrt für ~:minutes Min',
        'retry_in'     => 'Sie können es erneut versuchen in',
        'seconds_hint' => 'Die Zeit wird automatisch aktualisiert.',
        'generic_wait' => 'Bitte warten Sie einen Moment, bevor Sie es erneut versuchen.',
        'back'         => 'Zurück',
        'go_login'     => 'Zum Login',
    ],

    'throttle_page' => [
        'title'         => 'Zu viele Versuche',
        'message'       => 'Sie haben zu viele Anmeldeversuche unternommen.',
        'retry_in'      => 'Sie können es erneut versuchen in',
        'minutes_abbr'  => 'Min',
        'seconds_abbr'  => 'Sek',
        'total_seconds' => 'Gesamtsekunden',
        'redirecting'   => 'Weiterleitung…',
    ],
    'remember_me' => 'Angemeldet bleiben',
    'forgot_password' => 'Passwort vergessen?',
    'send_link' => 'Link senden',
    'confirm_password' => 'Passwort bestätigen',

    'login' => [
        'remaining_attempts' => '{0} Ungültige Zugangsdaten.|{1} Ungültige Zugangsdaten. Sie haben noch 1 Versuch, bevor Ihr Konto gesperrt wird.|[2,*] Ungültige Zugangsdaten. Sie haben noch :count Versuche, bevor Ihr Konto gesperrt wird.',
    ],

    'account' => [
        'locked'   => 'Ihr Konto ist gesperrt. Überprüfen Sie Ihre E-Mails, um es zu entsperren.',
        'unlocked' => 'Ihr Konto wurde entsperrt. Sie können sich jetzt anmelden.',
    ],
];
