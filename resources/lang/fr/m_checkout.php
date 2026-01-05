<?php

return [
    'title'                  => 'Paiement',
    'panels' => [
        'terms_title'        => 'Politiques et Conditions',
        'secure_subtitle'    => 'Le paiement est rapide et sécurisé',
        'required_title'     => 'Champs obligatoires',
        'required_read_accept' => 'Vous devez lire et accepter toutes les politiques pour poursuivre le paiement',
        'terms_block_title'  => 'Termes, Conditions et Politiques',
        'version'            => 'v',
        'no_policies_configured' => 'Aucune politique configurée. Veuillez contacter l\'administrateur.',
    ],

    'customer_info' => [
        'title'              => 'Informations Client',
        'subtitle'              => 'Veuillez fournir vos coordonnées pour continuer',
        'full_name'              => 'Nom Complet',
        'first_name'             => 'Prénom',
        'last_name'              => 'Nom de famille',
        'email'              => 'E-mail',
        'phone'              => 'Téléphone',
        'optional'              => 'optionnel',
        'placeholder_name'              => 'Jean Dupont',
        'placeholder_email'              => 'email@exemple.fr',
        'why_need_title'              => 'Pourquoi nous en avons besoin',
        'why_need_text'              => 'Votre e-mail sera utilisé pour envoyer des confirmations de réservation, des mises à jour et des liens de paiement. Vous pouvez optionnellement créer un compte après la réservation pour gérer vos réservations.',
        'logged_in_as'              => 'Connecté en tant que',
        'address'            => 'Adresse',
        'city'               => 'Ville',
        'state'              => 'État / Province',
        'zip'                => 'Code Postal',
        'country'            => 'Pays',
    ],

    'steps' => [
        'review'             => 'Révision',
        'payment'            => 'Paiement',
        'confirmation'       => 'Confirmation',
    ],

    'buttons' => [
        'back'               => 'Retour',
        'go_to_payment'      => 'Aller au paiement',
        'view_details'       => 'Voir les détails',
        'edit'               => 'Changer la date ou les participants',
        'close'              => 'Fermer',
    ],

    'summary' => [
        'title'              => 'Récapitulatif de la commande',
        'item'               => 'article',
        'items'              => 'articles',
        'free_cancellation'  => 'Annulation gratuite',
        'free_cancellation_until' => 'Avant :time le :date',
        'subtotal'           => 'Sous-total',
        'promo_code'         => 'Code promo',
        'total'              => 'Total',
        'taxes_included'     => 'Toutes taxes et frais inclus',
        'order_details'      => 'Détails de la commande',
    ],

    'blocks' => [
        'pickup_meeting'     => 'Pickup / Point de rencontre',
        'hotel'              => 'Hôtel',
        'meeting_point'      => 'Point de rencontre',
        'pickup_time'        => 'Heure de prise en charge',
        'add_ons'            => 'Extras',
        'duration'           => 'Durée',
        'hours'              => 'heures',
        'guide'              => 'Guide',
        'notes'              => 'Notes',
        'ref'                => 'Réf',
        'item'               => 'Article',
    ],

    'categories' => [
        'adult'              => 'Adulte',
        'kid'                => 'Enfant',
        'category'           => 'Catégorie',
        'qty_badge'          => ':qtyx',
        'unit_price'         => '($:price × :qty)',
        'line_total'         => '$:total',
    ],

    'accept' => [
        'label_html'         => "J’ai lu et j’accepte les <strong>Termes et Conditions</strong>, la <strong>Politique de Confidentialité</strong> et toutes les <strong>Politiques d’Annulation, de Remboursement et de Garantie</strong>. *",
        'error'              => 'Vous devez accepter les politiques pour continuer.',
    ],

    'misc' => [
        'at'                 => 'à',
        'participant'        => 'participant',
        'participants'       => 'participants',
    ],

    'payment' => [
        'title'              => 'Paiement',
        'total'              => 'Total',
        'secure_payment'     => 'Paiement Sécurisé',
        'powered_by'         => 'Propulsé par',
        'proceed_to_payment' => 'Procéder au Paiement',
        'secure_transaction' => 'Transaction Sécurisée',
    ],
    'booking' => [
        'summary'   => 'Résumé de la réservation',
        'reference' => 'Référence',
        'date'      => 'Date',
        'passengers' => 'Passagers',
    ],
    'tour' => [
        'name' => 'Tour',
    ],
];
