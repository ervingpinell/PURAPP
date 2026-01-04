<?php

return [
    // Email
    'email_subject' => 'Schließen Sie Ihre Kontoeinrichtung ab',

    // Setup page
    'title' => 'Schließen Sie Ihre Kontoeinrichtung ab',
    'welcome' => 'Willkommen, :name!',
    'booking_confirmed' => 'Ihre Buchung Nr. :reference wurde bestätigt.',
    'payment_success_message' => 'Zahlung erfolgreich & Buchung bestätigt!',
    'create_password' => 'Erstellen Sie ein Passwort, um:',
    'benefits' => [
        'view_bookings' => 'Alle Ihre Buchungen anzusehen',
        'manage_profile' => 'Ihr Profil zu verwalten',
        'exclusive_offers' => 'Exklusive Angebote zu erhalten',
    ],
    'password_label' => 'Passwort',
    'confirm_password_label' => 'Passwort bestätigen',
    'submit_button' => 'Mein Konto erstellen',
    'maybe_later' => 'Vielleicht später',

    // Validation
    'password_requirements' => 'Das Passwort muss mindestens 1 Zahl und 1 Sonderzeichen enthalten (.¡!@#$%^&*()_+-)',
    'password_min_length' => 'Das Passwort muss mindestens 8 Zeichen lang sein',
    'requirements' => [
        'one_number' => 'Mindestens 1 Zahl',
        'one_special_char' => 'Mindestens 1 Sonderzeichen',
    ],

    // Email Welcome
    'email_welcome_subject' => 'Willkommen bei ' . config('app.name') . '!',
    'email_welcome_title' => 'Willkommen, :name!',
    'email_welcome_text' => 'Ihr Konto wurde erfolgreich erstellt. Sie können jetzt auf Ihre Buchungen zugreifen und Ihr Profil verwalten.',
    'email_action_button' => 'Zu meinem Dashboard',

    // JS / Strength
    'strength' => [
        'weak' => 'Schwach',
        'medium' => 'Mittel',
        'strong' => 'Stark',
    ],
    'passwords_do_not_match' => 'Passwörter stimmen nicht überein',
    'creating_account' => 'Konto wird erstellt...',

    // Messages
    'token_expired' => 'Dieser Link ist abgelaufen. Bitte fordern Sie einen neuen an.',
    'token_invalid' => 'Ungültiger Einrichtungslink.',
    'success' => 'Konto erfolgreich erstellt! Sie können sich jetzt anmelden.',
    'user_not_found' => 'Benutzer nicht gefunden.',
    'already_has_password' => 'Dieser Benutzer hat bereits ein Passwort festgelegt.',
    'too_many_requests' => 'Zu viele Anfragen. Bitte versuchen Sie es später erneut.',
    'send_failed' => 'E-Mail konnte nicht gesendet werden. Bitte versuchen Sie es später erneut.',
];
