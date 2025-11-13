<?php

return [
    'title' => 'Benutzerverwaltung',
    'add_user' => 'Benutzer hinzufügen',
    'no_role' => 'Keine Rolle',
    'user_marked_verified' => 'Benutzer erfolgreich als verifiziert markiert.',

    'filters' => [
        'role' => 'Nach Rolle filtern:',
        'state' => 'Nach Status filtern:',
        'email' => 'Nach E-Mail filtern:',
        'email_placeholder' => 'beispiel@domain.com',
        'all' => '-- Alle --',
        'search' => 'Suchen',
        'clear' => 'Zurücksetzen',
    ],

    'table' => [
        'id' => 'ID',
        'name' => 'Name',
        'email' => 'E-Mail',
        'role' => 'Rolle',
        'phone' => 'Telefon',
        'status' => 'Status',
        'verified' => 'Verifiziert',
        'locked' => 'Gesperrt',
        'actions' => 'Aktionen',
    ],

    'status' => [
        'active' => 'Aktiv',
        'inactive' => 'Inaktiv',
    ],

    'verified' => [
        'yes' => 'Ja',
        'no'  => 'Nein',
    ],

    'locked' => [
        'yes' => 'Ja',
        'no'  => 'Nein',
    ],

    'actions' => [
        'edit' => 'Bearbeiten',
        'deactivate' => 'Deaktivieren',
        'reactivate' => 'Reaktivieren',
        'lock' => 'Sperren',
        'unlock' => 'Entsperren',
        'mark_verified' => 'Als verifiziert markieren',
    ],

    'dialog' => [
        'title' => 'Bestätigung',
        'cancel' => 'Abbrechen',
        'confirm_lock' => 'Diesen Benutzer sperren?',
        'confirm_unlock' => 'Diesen Benutzer entsperren?',
        'confirm_deactivate' => 'Diesen Benutzer deaktivieren?',
        'confirm_reactivate' => 'Diesen Benutzer reaktivieren?',
        'confirm_mark_verified' => 'Als verifiziert markieren?',
        'action_lock' => 'Ja, sperren',
        'action_unlock' => 'Ja, entsperren',
        'action_deactivate' => 'Ja, deaktivieren',
        'action_reactivate' => 'Ja, reaktivieren',
        'action_mark_verified' => 'Ja, markieren',
    ],

    'modals' => [
        'register_user' => 'Benutzer registrieren',
        'edit_user' => 'Benutzer bearbeiten',
        'save' => 'Speichern',
        'update' => 'Aktualisieren',
        'cancel' => 'Abbrechen',
        'close' => 'Schließen',
    ],

    'form' => [
        'full_name' => 'Name',
        'email' => 'E-Mail',
        'role' => 'Rolle',
        'country_code' => 'Ländervorwahl',
        'phone_number' => 'Telefonnummer',
        'password' => 'Passwort',
        'password_confirmation' => 'Passwort bestätigen',
        'toggle_password' => 'Passwort anzeigen/ausblenden',
    ],

    'password_reqs' => [
        'length'  => 'Mindestens 8 Zeichen',
        'special' => '1 Sonderzeichen (.,!@#$%^&*()_+-)',
        'number'  => '1 Zahl',
        'match'   => 'Passwörter stimmen überein',
    ],

    'alert' => [
        'success' => 'Erfolg',
        'error'   => 'Fehler',
    ],
];
