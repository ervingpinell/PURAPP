<?php

return [
    'unique' => 'Le :attribute est déjà utilisé.',

    'custom' => [
        'email' => [
            'unique' => 'Cet e-mail est déjà enregistré.',
        ],
        'password' => [
            'min' => 'Le mot de passe doit contenir au moins :min caractères.',
            'regex' => 'Le mot de passe doit inclure au moins un chiffre et un caractère spécial.',
            'confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ],
    ],

    'attributes' => [
        'full_name' => 'nom complet',
        'email' => 'e-mail',
        'phone' => 'téléphone',
        'password' => 'mot de passe',
        'password_confirmation' => 'confirmation du mot de passe',
    ],

                // --- Validation personnalisée ---
    'validation_error_title' => 'Veuillez corriger les erreurs suivantes :',
    'required_full_name' => 'Le nom complet est requis.',
    'required_email' => 'L\'adresse e-mail est requise.',
    'required_password' => 'Le mot de passe est requis.',
    'required_password_confirmation' => 'La confirmation du mot de passe est requise.',
        'invalid_email' => 'L\'adresse e-mail est invalide.',
    'email_already_taken' => 'Cette adresse e-mail est déjà enregistrée.',
    'password_confirmation' => 'La confirmation du mot de passe est requise.',


    'password_requirements' => [
        'length' => '- Minimum 8 caractères',
        'special' => '- 1 caractère spécial ( .¡!@#$%^&*()_+- )',
        'number' => '- 1 chiffre',
    ],
     'invalid_credentials'=> 'Identifiants invalides. Veuillez réessayer.',
];
