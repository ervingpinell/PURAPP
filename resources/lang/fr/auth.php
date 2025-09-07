<?php

return [
    // Standard Laravel messages
    'failed'   => "L'adresse e-mail ou le mot de passe est incorrect.",
    'password' => 'Le mot de passe fourni est incorrect.',
    'throttle' => 'Trop de tentatives de connexion. Veuillez réessayer dans :seconds secondes.',

    // Business-specific messages
    'inactive'   => 'Votre compte est inactif. Contactez le support pour le réactiver.',
    'locked'     => 'Votre compte est verrouillé. Contactez le support pour le déverrouiller.',
    'unverified' => 'Vous devez vérifier votre adresse e-mail avant de vous connecter. Vérifiez votre boîte de réception.',

    // Fortify 2FA (status and UI texts)
    'two_factor' => [
        'title'                 => 'Authentification à deux facteurs',
        'header'                 => 'Authentification à deux facteurs',
        'enabled'                  => 'Authentification à deux facteurs activée.',
        'confirmed'                => 'Authentification à deux facteurs confirmée.',
        'disabled'                 => 'Authentification à deux facteurs désactivée.',
        'recovery_codes_generated' => 'Nouveaux codes de récupération générés.',
        'remember_device'   => 'Se souvenir de cet appareil pendant 30 jours',
        'enter_code'        => 'Entrez le code à 6 chiffres',
        'use_recovery'      => 'Utiliser un code de récupération',
        'use_authenticator' => "Utiliser l'application d'authentification",
        'code'              => "Code d'authentification",
        'recovery_code'     => 'Code de récupération',
        'confirm'           => 'Confirmer',
    ],

    // Throttle screen
    'too_many_attempts' => [
        'title'        => 'Trop de tentatives',
        'intro'        => 'Vous avez effectué trop de tentatives de connexion.',
        'blocked_for'  => 'Bloqué pendant ~:minutes min',
        'retry_in'     => 'Vous pourrez réessayer dans',
        'seconds_hint' => 'Le temps sera mis à jour automatiquement.',
        'generic_wait' => 'Veuillez patienter avant de réessayer.',
        'back'         => 'Retour',
        'go_login'     => 'Aller à la connexion',
    ],

    'throttle_page' => [
        'title'         => 'Trop de tentatives',
        'message'       => 'Vous avez effectué trop de tentatives de connexion.',
        'retry_in'      => 'Vous pourrez réessayer dans',
        'minutes_abbr'  => 'min',
        'seconds_abbr'  => 's',
        'total_seconds' => 'secondes totales',
        'redirecting'   => 'Redirection…',
    ],
    'remember_me' => 'Se souvenir de moi',
    'forgot_password' => 'Mot de passe oublié ?',
    'send_link' => 'Envoyer le lien',
    'confirm_password' => 'Confirmer le mot de passe',

    'login' => [
        'remaining_attempts' => '{0} Identifiants invalides.|{1} Identifiants invalides. Il vous reste 1 tentative avant le verrouillage.|[2,*] Identifiants invalides. Il vous reste :count tentatives avant le verrouillage.',
    ],

    'account' => [
        'locked'   => 'Votre compte est verrouillé. Vérifiez vos e-mails pour le déverrouiller.',
        'unlocked' => 'Votre compte a été déverrouillé. Vous pouvez maintenant vous connecter.',
    ],
];
