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
        'free_cancellation_until' => "Jusqu’à :time le :date",
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
];
