<?php

return [
    // Email
    'email_subject' => 'Finalisez la configuration de votre compte',

    // Setup page
    'title' => 'Finalisez la configuration de votre compte',
    'welcome' => 'Bienvenue, :name !',
    'booking_confirmed' => 'Votre réservation ref :reference a été confirmée.',
    'payment_success_message' => 'Paiement réussi & Réservation confirmée !',
    'create_password' => 'Créez un mot de passe pour :',
    'benefits' => [
        'view_bookings' => 'Voir toutes vos réservations',
        'manage_profile' => 'Gérer votre profil',
        'exclusive_offers' => 'Recevoir des offres exclusives',
    ],
    'password_label' => 'Mot de passe',
    'confirm_password_label' => 'Confirmer le mot de passe',
    'submit_button' => 'Créer mon compte',
    'maybe_later' => 'Peut-être plus tard',

    // Validation
    'password_requirements' => 'Le mot de passe doit contenir au moins 1 chiffre et 1 caractère spécial (.¡!@#$%^&*()_+-)',
    'password_min_length' => 'Le mot de passe doit contenir au moins 8 caractères',
    'requirements' => [
        'one_number' => 'Au moins 1 chiffre',
        'one_special_char' => 'Au moins 1 caractère spécial',
    ],

    // Email Welcome
    'email_welcome_subject' => 'Bienvenue chez ' . config('app.name') . ' !',
    'email_welcome_title' => 'Bienvenue, :name !',
    'email_welcome_text' => 'Votre compte a été créé avec succès. Vous pouvez maintenant accéder à vos réservations et gérer votre profil.',
    'email_action_button' => 'Aller à mon tableau de bord',

    // JS / Strength
    'strength' => [
        'weak' => 'Faible',
        'medium' => 'Moyen',
        'strong' => 'Fort',
    ],
    'passwords_do_not_match' => 'Les mots de passe ne correspondent pas',
    'creating_account' => 'Création du compte...',

    // Messages
    'token_expired' => 'Ce lien a expiré. Veuillez en demander un nouveau.',
    'token_invalid' => 'Lien de configuration invalide.',
    'success' => 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.',
    'user_not_found' => 'Utilisateur introuvable.',
    'already_has_password' => 'Cet utilisateur a déjà un mot de passe défini.',
    'too_many_requests' => 'Trop de demandes. Veuillez réessayer plus tard.',
    'send_failed' => 'Échec de l\'envoi de l\'e-mail. Veuillez réessayer plus tard.',
];
