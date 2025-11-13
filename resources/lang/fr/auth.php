<?php

return [
    // Standard Laravel messages
    'failed'   => 'L’e-mail ou le mot de passe est incorrect.',
    'password' => 'Le mot de passe fourni est incorrect.',
    'throttle' => 'Trop de tentatives de connexion. Réessayez dans :seconds secondes.',

    // Business-specific messages
    'inactive'   => 'Votre compte est inactif. Contactez le support pour le réactiver.',
    'locked'     => 'Votre compte est verrouillé. Contactez le support pour le déverrouiller.',
    'unverified' => 'Vous devez vérifier votre adresse e-mail avant de vous connecter. Vérifiez votre boîte de réception.',

    // Fortify 2FA (status & UI text)
    'two_factor' => [
        'title'                   => 'Authentification à deux facteurs',
        'header'                  => 'Authentification à deux facteurs',
        'enabled'                 => 'Authentification à deux facteurs activée.',
        'confirmed'               => 'Authentification à deux facteurs confirmée.',
        'disabled'                => 'Authentification à deux facteurs désactivée.',
        'recovery_codes_generated'=> 'De nouveaux codes de récupération ont été générés.',
        'remember_device'         => 'Se souvenir de cet appareil pendant 30 jours',
        'enter_code'              => 'Entrez le code à 6 chiffres',
        'use_recovery'            => 'Utiliser un code de récupération',
        'use_authenticator'       => 'Utiliser une application d’authentification',
        'code'                    => 'Code d’authentification',
        'recovery_code'           => 'Code de récupération',
        'confirm'                 => 'Confirmer',
    ],

    // Throttle screen
    'too_many_attempts' => [
        'title'        => 'Trop de tentatives',
        'intro'        => 'Vous avez effectué trop de tentatives de connexion.',
        'blocked_for'  => 'Bloqué pendant ~:minutes min',
        'retry_in'     => 'Vous pourrez réessayer dans',
        'seconds_hint' => 'Le temps se mettra à jour automatiquement.',
        'generic_wait' => 'Veuillez patienter un instant avant de réessayer.',
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

    'remember_me'      => 'Se souvenir de moi',
    'forgot_password'  => 'Mot de passe oublié ?',
    'send_link'        => 'Envoyer le lien',
    'confirm_password' => 'Confirmer le mot de passe',

    'login' => [
        'remaining_attempts' => '{0} Identifiants invalides.|{1} Identifiants invalides. Il vous reste 1 tentative avant le verrouillage.|[2,*] Identifiants invalides. Il vous reste :count tentatives avant le verrouillage.',
    ],

    'account' => [
        'locked'   => 'Votre compte est verrouillé. Vérifiez vos e-mails pour le déverrouiller.',
        'unlocked' => 'Votre compte a été déverrouillé. Vous pouvez maintenant vous connecter.',
    ],

    'verify.verified' => 'Votre adresse e-mail a été vérifiée. Vous pouvez maintenant vous connecter.',

    'verify' => [
        'already'  => 'Vous avez déjà vérifié votre adresse e-mail,',
        'verified' => 'Votre adresse e-mail a été vérifiée.',
    ],
    'email_change_subject'        => 'Confirmez le changement de votre adresse e-mail',
'email_change_title'          => 'Confirmez votre nouvelle adresse e-mail',
'email_change_hello'          => 'Bonjour :name,',
'email_change_intro'          => 'Vous avez demandé à changer l’adresse e-mail associée à votre compte. Pour finaliser la modification, cliquez sur le bouton ci-dessous :',
'email_change_button'         => 'Confirmer la nouvelle adresse',
'email_change_footer'         => 'Si vous n’êtes pas à l’origine de cette demande, vous pouvez ignorer ce message et votre adresse actuelle restera inchangée.',
'email_change_link_expired'   => 'Le lien pour changer votre adresse e-mail a expiré. Veuillez demander à nouveau la modification depuis votre profil.',
'email_change_confirmed'      => 'Votre adresse e-mail a été mise à jour et vérifiée avec succès.',

];
