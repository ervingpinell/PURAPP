<?php

return [
    'unique' => 'Das :attribute ist bereits vergeben.',

    'custom' => [
        'email' => [
            'unique' => 'Diese E-Mail ist bereits registriert.',
        ],
        'password' => [
            'min' => 'Das Passwort muss mindestens :min Zeichen enthalten.',
            'regex' => 'Das Passwort muss mindestens eine Zahl und ein Sonderzeichen enthalten.',
            'confirmed' => 'Die Passwortbestätigung stimmt nicht überein.',
        ],
    ],

    'attributes' => [
        'full_name' => 'vollständiger Name',
        'first_name' => 'Vorname',
        'last_name' => 'Nachname',
        'email' => 'E-Mail',
        'phone' => 'Telefonnummer',
        'password' => 'Passwort',
        'password_confirmation' => 'Passwortbestätigung',
    ],
    // --- Benutzerdefinierte Validierung ---
    'validation_error_title' => 'Bitte korrigieren Sie die folgenden Fehler:',
    'required_full_name' => 'Der vollständige Name ist erforderlich.',
    'required_email' => 'Die E-Mail-Adresse ist erforderlich.',
    'required_password' => 'Das Passwort ist erforderlich.',
    'required_password_confirmation' => 'Die Passwortbestätigung ist erforderlich.',
    'invalid_email' => 'Die E-Mail-Adresse ist ungültig.',
    'email_already_taken' => 'Diese E-Mail ist bereits registriert.',
    'password_confirmation' => 'Die Passwortbestätigung ist erforderlich.',

    'password_requirements' => [
        'length' => '- Mindestens 8 Zeichen',
        'special' => '- 1 Sonderzeichen ( .¡!@#$%^&*()_+- )',
        'number' => '- 1 Zahl',
    ],


    'invalid_credentials' => 'Ungültige Anmeldeinformationen. Bitte versuchen Sie es erneut.',

];
