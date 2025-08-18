<?php

return [

    'hello' => 'Bonjour',

    'login_message' => 'Connectez-vous pour commencer votre session',
    'register_message' => 'Enregistrez un nouveau compte',
    'password_reset_message' => 'Réinitialiser le mot de passe',
    'reset_password' => 'Réinitialiser le mot de passe',
    'send_password_reset_link' => 'Envoyer le lien de réinitialisation',
    'i_forgot_my_password' => 'J’ai oublié mon mot de passe',
    'back_to_login' => 'Retour à la connexion',
    'verify_message' => 'Votre compte doit être vérifié par e-mail',
    'verify_email_sent' => 'Un nouveau lien de vérification a été envoyé à votre e-mail.',
    'verify_check_email' => 'Avant de continuer, veuillez vérifier votre e-mail pour le lien de vérification.',
    'verify_if_not_received' => 'Si vous n’avez pas reçu l’e-mail',
    'verify_request_another' => 'cliquez ici pour en demander un autre',

    'passwords' => [
        'reset'     => 'Votre mot de passe a été réinitialisé.',
        'sent'      => 'Nous avons envoyé par e-mail votre lien de réinitialisation du mot de passe.',
        'throttled' => 'Veuillez attendre avant de réessayer.',
        'token'     => 'Ce jeton de réinitialisation du mot de passe est invalide.',
        'user'      => "Nous ne trouvons pas d’utilisateur avec cette adresse e-mail.",
        'match'     => 'Les mots de passe correspondent.',
        'link_sent' => 'Un lien de réinitialisation a été envoyé à votre adresse e-mail.',
    ],

    'password_requirements_title' => 'Votre mot de passe doit contenir :',
    'password_requirements' => [
        'length'  => '- Au moins 8 caractères',
        'special' => '- 1 caractère spécial ( .¡!@#$%^&*()_+- )',
        'number'  => '- 1 chiffre',
    ],

    'sign_in' => 'Se connecter',
    'sign_out' => 'Se déconnecter',
    'register' => 'S’inscrire',
    'send_link' => 'Envoyer le lien',
    'confirm_password' => 'Confirmer le mot de passe',

    'account' => [
        'locked_title'     => 'Votre compte a été bloqué',
        'locked_message'   => 'Vous avez dépassé le nombre de tentatives autorisées. Pour des raisons de sécurité, votre compte a été temporairement bloqué.',
        'unlock_hint'      => 'Saisissez votre e-mail et nous vous enverrons un lien de déblocage.',
        'send_unlock'      => 'Envoyer le lien de déblocage',
        'unlock_link_sent' => 'Si le compte existe et est bloqué, nous avons envoyé un lien de déblocage.',
        'unlock_mail_subject' => 'Déblocage de compte',
        'unlock_mail_intro'   => 'Nous avons reçu une demande de déblocage de votre compte.',
        'unlock_mail_action'  => 'Débloquer mon compte',
        'unlock_mail_outro'   => 'Si ce n’était pas vous, ignorez cet e-mail.',
        'unlocked'            => 'Votre compte a été débloqué. Vous pouvez maintenant vous connecter.',
        'locked'              => 'Votre compte est bloqué.',
    ],

    'verify' => [
        'title'     => 'Vérifiez votre adresse e-mail',
        'message'   => 'Avant de continuer, veuillez vérifier votre e-mail. Nous vous avons envoyé un lien de vérification.',
        'resend'    => 'Renvoyer l’e-mail de vérification',
        'link_sent' => 'Nous vous avons envoyé un nouveau lien de vérification.',
        'subject'   => 'Vérifiez votre adresse e-mail',
        'intro'     => 'Veuillez cliquer sur le bouton pour vérifier votre adresse e-mail.',
        'action'    => 'Vérifier l’adresse e-mail',
        'outro'     => 'Si vous n’avez pas créé ce compte, veuillez ignorer ce message.',
        'browser_hint' => 'Si vous avez des problèmes pour cliquer sur le bouton ":action", copiez et collez cette URL dans votre navigateur : :url',
        'verified_success' => 'Votre e-mail a été vérifié avec succès.',
        'verify_email_title'        => 'Vérifiez votre e-mail',
        'verify_email_header'       => 'Vérifiez votre boîte de réception',
        'verify_email_sent'         => 'Nous vous avons envoyé un lien de vérification à votre e-mail.',
        'verify_email_sent_to'      => 'Nous venons d’envoyer le lien de vérification à :',
        'verify_email_generic'      => 'Nous venons de vous envoyer un lien de vérification à votre e-mail.',
        'verify_email_instructions' => 'Ouvrez l’e-mail et cliquez sur le lien pour activer votre compte. Si vous ne le voyez pas, vérifiez votre dossier spam.',
        'back_to_login'             => 'Retour à la connexion',
        'back_to_home'              => 'Retour à l’accueil',
    ],

];
