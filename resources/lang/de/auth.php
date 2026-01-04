<?php

return [
    // Standard Laravel messages
    'failed'   => 'Die E-Mail-Adresse oder das Passwort ist falsch.',
    'password' => 'Das angegebene Passwort ist falsch.',
    'throttle' => 'Zu viele Anmeldeversuche. Bitte versuchen Sie es in :seconds Sekunden erneut.',
    'captcha_failed' => 'CAPTCHA-Überprüfung fehlgeschlagen. Bitte versuchen Sie es erneut.',

    // Geschäftsspezifische Nachrichten
    'inactive'   => 'Ihr Konto ist inaktiv. Bitte kontaktieren Sie den Support, um es zu reaktivieren.',
    'locked'     => 'Ihr Konto ist gesperrt. Bitte kontaktieren Sie den Support, um es zu entsperren.',
    'unverified' => 'Sie müssen Ihre E-Mail-Adresse verifizieren, bevor Sie sich anmelden. Bitte überprüfen Sie Ihren Posteingang.',

    // Fortify 2FA (status & UI text)
    'two_factor' => [
        'title'                   => 'Zwei-Faktor-Authentifizierung',
        'header'                  => 'Zwei-Faktor-Authentifizierung',
        'enabled'                 => 'Zwei-Faktor-Authentifizierung aktiviert.',
        'confirmed'               => 'Zwei-Faktor-Authentifizierung bestätigt.',
        'disabled'                => 'Zwei-Faktor-Authentifizierung deaktiviert.',
        'recovery_codes_generated' => 'Neue Wiederherstellungscodes wurden erstellt.',
        'remember_device'         => 'Dieses Gerät 30 Tage lang merken',
        'enter_code'              => 'Gib den 6-stelligen Code ein',
        'use_recovery'            => 'Wiederherstellungscode verwenden',
        'use_authenticator'       => 'Authentifizierungs-App verwenden',
        'code'                    => 'Authentifizierungscode',
        'recovery_code'           => 'Wiederherstellungscode',
        'confirm'                 => 'Bestätigen',
    ],

    // Throttle screen
    'too_many_attempts' => [
        'title'        => 'Zu viele Versuche',
        'intro'        => 'Du hast zu viele Login-Versuche unternommen.',
        'blocked_for'  => 'Gesperrt für ~:minutes Min.',
        'retry_in'     => 'Du kannst es erneut versuchen in',
        'seconds_hint' => 'Die Zeit wird automatisch aktualisiert.',
        'generic_wait' => 'Bitte warte einen Moment, bevor du es erneut versuchst.',
        'back'         => 'Zurück',
        'go_login'     => 'Zur Anmeldung',
    ],

    'throttle_page' => [
        'title'         => 'Zu viele Versuche',
        'message'       => 'Du hast zu viele Login-Versuche unternommen.',
        'retry_in'      => 'Du kannst es erneut versuchen in',
        'minutes_abbr'  => 'Min.',
        'seconds_abbr'  => 'Sek.',
        'total_seconds' => 'Gesamtsekunden',
        'redirecting'   => 'Weiterleiten…',
    ],

    'remember_me'      => 'Angemeldet bleiben',
    'forgot_password'  => 'Passwort vergessen?',
    'send_link'        => 'Link senden',
    'confirm_password' => 'Passwort bestätigen',

    'login' => [
        'remaining_attempts' => '{0} Ungültige Zugangsdaten.|{1} Ungültige Zugangsdaten. Du hast noch 1 Versuch, bevor das Konto gesperrt wird.|[2,*] Ungültige Zugangsdaten. Du hast noch :count Versuche, bevor das Konto gesperrt wird.',
    ],

    'account' => [
        'locked'   => 'Dein Konto ist gesperrt. Prüfe deine E-Mails, um es zu entsperren.',
        'unlocked' => 'Dein Konto wurde entsperrt. Du kannst dich jetzt anmelden.',
    ],

    'verify.verified' => 'Deine E-Mail-Adresse wurde verifiziert. Du kannst dich jetzt anmelden.',

    'verify' => [
        'already'  => 'Du hast deine E-Mail-Adresse bereits verifiziert,',
        'verified' => 'Deine E-Mail-Adresse wurde verifiziert.',
    ],
    'email_change_subject'        => 'Bestätige die Änderung deiner E-Mail-Adresse',
    'email_change_title'          => 'Bestätige deine neue E-Mail-Adresse',
    'email_change_hello'          => 'Hallo :name,',
    'email_change_intro'          => 'Du hast beantragt, die mit deinem Konto verknüpfte E-Mail-Adresse zu ändern. Um die Änderung abzuschließen, klicke auf die folgende Schaltfläche:',
    'email_change_button'         => 'Neue E-Mail bestätigen',
    'email_change_footer'         => 'Wenn du diese Änderung nicht angefordert hast, kannst du diese Nachricht ignorieren und deine aktuelle E-Mail bleibt unverändert.',
    'email_change_link_expired'   => 'Der Link zum Ändern deiner E-Mail ist abgelaufen. Bitte fordere die Änderung erneut über dein Profil an.',
    'email_change_confirmed'      => 'Deine E-Mail-Adresse wurde erfolgreich aktualisiert und verifiziert.',
    'create_account' => 'Konto erstellen',
];
