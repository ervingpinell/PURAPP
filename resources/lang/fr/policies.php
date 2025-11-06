<?php

return [

    // =========================================================
    // [00] GÉNÉRIQUE
    // =========================================================
    'page_title'  => 'Politiques',
    'no_policies' => 'Aucune politique disponible pour le moment.',
    'no_sections' => 'Aucune section disponible pour le moment.',

    // =========================================================
    // [01] CHECKOUT
    // =========================================================
    'checkout' => [
        'card_title'  => 'Votre commande',
        'details'     => 'Détail',
        'must_accept' => 'Vous devez lire et accepter toutes les politiques pour continuer le paiement.',
        'accept_label_html' =>
            "J'ai lu et j'accepte les <strong>Conditions Générales</strong>, la <strong>Politique de Confidentialité</strong> et toutes les <strong>Politiques d’Annulation, de Remboursement et de Garantie</strong>.",
        'back'       => 'Retour',
        'pay'        => 'Procéder au paiement',
        'order_full' => 'Détail complet de la commande',

        'version' => [
            'terms'   => 'v1',
            'privacy' => 'v1',
        ],

        'titles' => [
            'terms'        => 'Conditions Générales',
            'privacy'      => 'Politique de Confidentialité',
            'cancellation' => 'Politique d’Annulation',
            'refunds'      => 'Politique de Remboursement',
            'warranty'     => 'Politique de Garantie',
            'payments'     => 'Moyens de Paiement',
        ],

        'bodies' => [
        'terms_html' => <<<HTML
            <p>Ces conditions régissent l’achat de visites et de services proposés par Green Vacations CR.</p>
            <ul>
            <li><strong>Portée :</strong> L’achat s’applique exclusivement aux services listés pour les dates et horaires sélectionnés.</li>
            <li><strong>Tarifs et frais :</strong> Les prix sont affichés en USD et incluent les taxes lorsqu’elles s’appliquent. Tout frais supplémentaire sera communiqué avant le paiement.</li>
            <li><strong>Capacité et disponibilité :</strong> Les réservations sont soumises à la disponibilité et aux validations de capacité.</li>
            <li><strong>Modifications :</strong> Les changements de date/horaires dépendent de la disponibilité et peuvent entraîner des différences tarifaires.</li>
            <li><strong>Responsabilité :</strong> Les services sont fournis conformément à la réglementation costaricienne applicable.</li>
            </ul>
            HTML,
                    'privacy_html' => <<<HTML
            <p>Nous traitons les données personnelles conformément à la réglementation applicable. Nous collectons uniquement les données nécessaires pour gérer les réservations, les paiements et la communication avec les clients.</p>
            <ul>
            <li><strong>Utilisation des informations :</strong> Gestion des achats, service client, notifications opérationnelles et conformité légale.</li>
            <li><strong>Partage :</strong> Nous ne vendons ni n’échangeons les données personnelles.</li>
            <li><strong>Droits :</strong> Vous pouvez exercer vos droits d’accès, de rectification, d’opposition et d’effacement via nos canaux de contact.</li>
            </ul>
            HTML,
                    'cancellation_html' => <<<HTML
            <p>Vous pouvez demander l’annulation avant le début du service selon les délais suivants :</p>
            <ul>
            <li>Jusqu’à 2&nbsp;heures avant : <strong>remboursement intégral</strong>.</li>
            <li>Entre 2&nbsp;heures et 1&nbsp;heure avant : <strong>remboursement de 50&nbsp;%</strong>.</li>
            <li>Moins d’1&nbsp;heure : <strong>non remboursable</strong>.</li>
            </ul>
            <p>Les remboursements sont effectués sur la <strong>même carte</strong> utilisée lors de l’achat. Les délais de crédit dépendent de la banque émettrice.</p>
            <p>Veuillez indiquer votre <strong>numéro de commande</strong> et votre <strong>nom complet</strong> lors de la demande d’annulation. Les délais peuvent varier selon le tour si cela est précisé sur la fiche produit.</p>
            HTML,
                    'refunds_html' => <<<HTML
            <p>Le cas échéant, les remboursements sont effectués sur la <strong>même carte</strong> que celle utilisée pour l’achat. Les délais dépendent de l’émetteur du moyen de paiement.</p>
            <p>Pour demander un remboursement : info@greenvacationscr.com / (+506) 2479 1471.</p>
            HTML,
                    'warranty_html' => <<<HTML
            <p>S’applique aux services non fournis ou fournis de manière substantiellement différente de l’offre. Vous disposez de <strong>7&nbsp;jours</strong> pour signaler tout incident. La garantie s’applique aux services touristiques commercialisés par Green Vacations CR.</p>
            HTML,
                    'payments_html' => <<<HTML
            <p>Le paiement s’effectue via un lien de paiement Alignet avec des cartes Visa/Mastercard/Amex activées pour les achats en ligne.</p>
            HTML,
                ],
                ],

    // =========================================================
    // [02] CHAMPS
    // =========================================================
    'fields' => [
        'title'       => 'Titre',
        'description' => 'Description',
        'type'        => 'Type',
        'is_active'   => 'Actif',
    ],

    // =========================================================
    // [03] TYPES
    // =========================================================
    'types' => [
        'cancellation' => 'Politique d’Annulation',
        'refund'       => 'Politique de Remboursement',
        'terms'        => 'Conditions Générales',
    ],

    // =========================================================
    // [04] MESSAGES
    // =========================================================
    'success' => [
        'created' => 'Politique créée avec succès.',
        'updated' => 'Politique mise à jour avec succès.',
        'deleted' => 'Politique supprimée avec succès.',
    ],

    'error' => [
        'create' => 'Impossible de créer la politique.',
        'update' => 'Impossible de mettre à jour la politique.',
        'delete' => 'Impossible de supprimer la politique.',
    ],
];
