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
    'hello' => 'Bonjour',
'captcha_failed' => 'La vérification du captcha a échoué.',

    // 2. Authentication Messages
    'login_message' => 'Connectez-vous pour démarrer votre session',
    'register_message' => 'Enregistrer un nouveau compte',
    'password_reset_message' => 'Réinitialiser le mot de passe',
    'reset_password' => 'Réinitialiser le mot de passe',
    'send_password_reset_link' => 'Envoyer le lien de réinitialisation',
    'i_forgot_my_password' => 'J\'ai oublié mon mot de passe',
    'back_to_login' => 'Retour à la connexion',
    'verify_message' => 'Votre compte nécessite une vérification par e-mail',
    'verify_email_sent' => 'Un nouveau lien de vérification a été envoyé à votre adresse e-mail.',
    'verify_check_email' => 'Veuillez vérifier vos e-mails pour le lien de vérification avant de continuer.',
    'verify_if_not_received' => 'Si vous n\'avez pas reçu l\'e-mail',
    'verify_request_another' => 'cliquez ici pour en demander un nouveau',

    // 3. Password Validations
    'passwords' => [
        'reset'     => 'Votre mot de passe a été réinitialisé.',
        'sent'      => 'Nous vous avons envoyé le lien de réinitialisation du mot de passe par e-mail.',
        'throttled' => 'Veuillez patienter avant de réessayer.',
        'token'     => 'Ce jeton de réinitialisation du mot de passe n\'est pas valide.',
        'user'      => "Nous ne trouvons aucun utilisateur avec cette adresse e-mail.",
        'match'     => 'Les mots de passe correspondent.',
        'link_sent' => 'Un lien de réinitialisation du mot de passe a été envoyé à votre adresse e-mail.',
    ],

    // 4. Password Requirements
    'password_requirements_title' => 'Votre mot de passe doit contenir :',
    'password_requirements' => [
        'length'  => '- Au moins 8 caractères',
        'special' => '- 1 caractère spécial ( .¡!@#$%^&*()_+- )',
        'number'  => '- 1 chiffre',
    ],

    // 5. Buttons and Actions
    'sign_in' => 'Se connecter',
    'sign_out' => 'Se déconnecter',
    'register' => 'S\'inscrire',
    'send_link' => 'Envoyer le lien',
    'confirm_password' => 'Confirmer le mot de passe',

    // 6. Account Lock/Unlock
    'account' => [
        'locked_title'     => 'Votre compte a été verrouillé',
        'locked_message'   => 'Vous avez dépassé le nombre de tentatives autorisées. Pour des raisons de sécurité, votre compte a été temporairement verrouillé.',
        'unlock_hint'      => 'Entrez votre adresse e-mail et nous vous enverrons un lien pour déverrouiller votre compte.',
        'send_unlock'      => 'Envoyer le lien de déverrouillage',
        'unlock_link_sent' => 'Si le compte existe et est verrouillé, nous avons envoyé un lien de déverrouillage.',
        'unlock_mail_subject' => 'Déverrouiller le compte',
        'unlock_mail_intro'   => 'Nous avons reçu une demande de déverrouillage de votre compte.',
        'unlock_mail_action'  => 'Déverrouiller mon compte',
        'unlock_mail_outro'   => 'Si vous n\'êtes pas à l\'origine de cette demande, veuillez ignorer cet e-mail.',
        'unlocked'            => 'Votre compte a été déverrouillé. Vous pouvez maintenant vous connecter.',
        'locked'              => 'Votre compte est verrouillé. Vérifier vos e-mails.',
    ],

    // 7. Email Verification
    'verify' => [
        'title'     => 'Vérifiez votre adresse e-mail',
        'message'   => 'Veuillez vérifier votre e-mail avant de continuer. Nous vous avons envoyé un lien de vérification.',
        'resend'    => 'Renvoyer l\'e-mail de vérification',
        'link_sent' => 'Nous vous avons envoyé un nouveau lien de vérification.',
        'sent_to'      => 'Nous avons envoyé un lien de vérification à :email.',
'email_label'  => 'Adresse e-mail',
        'subject'   => 'Vérifiez votre adresse e-mail',
        'intro'     => 'Veuillez cliquer sur le bouton pour vérifier votre adresse e-mail.',
        'action'    => 'Vérifier l\'adresse e-mail',
        'outro'     => 'Si vous n\'avez pas créé ce compte, veuillez ignorer ce message.',
        'browser_hint' => 'Si vous avez des difficultés à cliquer sur le bouton ":action", copiez cette URL et collez-la dans votre navigateur : :url',
        'verified_success' => 'Votre e-mail a été vérifié avec succès.',
        'verify_email_title'        => 'Vérifier l\'e-mail',
        'verify_email_header'       => 'Vérifiez votre boîte de réception',
        'verify_email_sent'         => 'Nous avons envoyé un lien de vérification à votre e-mail.',
        'verify_email_sent_to'      => 'Nous venons d\'envoyer le lien de vérification à :',
        'verify_email_generic'      => 'Nous venons de vous envoyer un lien de vérification à votre e-mail.',
        'verify_email_instructions' => 'Ouvrez l\'e-mail et cliquez sur le lien pour activer votre compte. Si vous ne voyez pas l\'e-mail, vérifiez votre dossier spam.',
        'back_to_login'             => 'Retour à la connexion',
        'back_to_home'              => 'Retour à l\'accueil',
    ],

    // 8. Unauthorized Admin
    'unauthorized_admin' => 'Accès refusé. Vous n\'êtes pas autorisé à accéder au panneau d\'administration.',

    // 9. Account Locked Message
    'locked' => 'Votre compte est verrouillé. Vérifiez vos e-mails pour le déverrouiller ou contactez le support.',

    // 10. Two Factor Authentication
    'two_factor' => [
        'title'                => 'Authentification à deux facteurs',
        'message'              => 'Entrez votre code d\'authentification ou un code de récupération.',
        'code_placeholder'     => 'Code 123456',
        'recovery_placeholder' => 'Code de récupération',
        'remember_device'      => 'Se souvenir de cet appareil',
        'verify_button'        => 'Vérifier',
    ],

    // 11. Two Factor Notices
    'notices' => [
        'two_factor_confirmed'             => 'Authentification à deux facteurs activée.',
        'two_factor_recovery_regenerated'  => 'Vos codes de récupération ont été régénérés.',
        'two_factor_disabled'              => 'Authentification à deux facteurs désactivée.',
        'two_factor_invalid_code'          => 'Le code est invalide. Veuillez réessayer.',
        'two_factor_invalid_recovery_code' => 'Le code de récupération est invalide ou a déjà été utilisé.',
    ],

];
