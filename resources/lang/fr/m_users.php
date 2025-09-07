<?php

return [
    'title' => 'Gestion des utilisateurs',
    'add_user' => 'Ajouter un utilisateur',
    'no_role' => 'Aucun rôle',

    'filters' => [
        'role' => 'Filtrer par rôle :',
        'state' => 'Filtrer par statut :',
        'email' => 'Filtrer par e-mail :',
        'email_placeholder' => 'exemple@domaine.com',
        'all' => '-- Tous --',
        'search' => 'Rechercher',
        'clear' => 'Réinitialiser',
    ],

    'table' => [
        'id' => 'ID',
        'name' => 'Nom',
        'email' => 'E-mail',
        'role' => 'Rôle',
        'phone' => 'Téléphone',
        'status' => 'Statut',
        'verified' => 'Vérifié',
        'locked' => 'Bloqué',
        'actions' => 'Actions',
    ],

    'status' => [
        'active' => 'Actif',
        'inactive' => 'Inactif',
    ],

    'verified' => [
        'yes' => 'Oui',
        'no'  => 'Non',
    ],

    'locked' => [
        'yes' => 'Oui',
        'no'  => 'Non',
    ],

    'actions' => [
        'edit' => 'Modifier',
        'deactivate' => 'Désactiver',
        'reactivate' => 'Réactiver',
        'lock' => 'Bloquer',
        'unlock' => 'Débloquer',
        'mark_verified' => 'Marquer comme vérifié',
    ],

    'dialog' => [
        'title' => 'Confirmation',
        'cancel' => 'Annuler',
        'confirm_lock' => 'Bloquer cet utilisateur ?',
        'confirm_unlock' => 'Débloquer cet utilisateur ?',
        'confirm_deactivate' => 'Désactiver cet utilisateur ?',
        'confirm_reactivate' => 'Réactiver cet utilisateur ?',
        'confirm_mark_verified' => 'Marquer comme vérifié ?',
        'action_lock' => 'Oui, bloquer',
        'action_unlock' => 'Oui, débloquer',
        'action_deactivate' => 'Oui, désactiver',
        'action_reactivate' => 'Oui, réactiver',
        'action_mark_verified' => 'Oui, marquer',
    ],

    'modals' => [
        'register_user' => 'Enregistrer un utilisateur',
        'edit_user' => 'Modifier l’utilisateur',
        'save' => 'Enregistrer',
        'update' => 'Mettre à jour',
        'cancel' => 'Annuler',
        'close' => 'Fermer',
    ],

    'form' => [
        'full_name' => 'Nom',
        'email' => 'E-mail',
        'role' => 'Rôle',
        'country_code' => 'Indicatif pays',
        'phone_number' => 'Numéro de téléphone',
        'password' => 'Mot de passe',
        'password_confirmation' => 'Confirmer le mot de passe',
        'toggle_password' => 'Afficher/Masquer le mot de passe',
    ],

    'password_reqs' => [
        'length'  => 'Au moins 8 caractères',
        'special' => '1 caractère spécial (.,!@#$%^&*()_+-)',
        'number'  => '1 chiffre',
        'match'   => 'Les mots de passe correspondent',
    ],

    'alert' => [
        'success' => 'Succès',
        'error'   => 'Erreur',
    ],
];
