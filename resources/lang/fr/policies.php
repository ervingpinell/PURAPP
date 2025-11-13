<?php

return [

    // =========================================================
    // [00] GÉNÉRIQUE
    // =========================================================
    'page_title'  => 'Politiques',
    'no_policies' => 'Aucune politique n’est disponible pour le moment.',
    'no_sections' => 'Aucune section n’est disponible pour le moment.',
    'propagate_to_all_langs' => 'Propager ce changement à toutes les langues (EN, FR, DE, PT)',
    'propagate_hint'         => 'Le texte sera automatiquement traduit à partir du contenu actuel et les traductions existantes dans ces langues seront écrasées.',
    'update_base_es'         => 'Mettre à jour aussi la base (ES)',
    'update_base_hint'       => 'Remplace le nom et le contenu de la politique dans la table de base (espagnol). À utiliser uniquement si tu souhaites également modifier le texte original.',

    // =========================================================
    // [01] CHECKOUT
    // =========================================================
    'checkout' => [
        'card_title'  => 'Ta commande',
        'details'     => 'Détails',
        'must_accept' => 'Tu dois lire et accepter toutes les politiques pour continuer avec le paiement.',
        'accept_label_html' =>
            'J’ai lu et j’accepte les <strong>Conditions Générales</strong>, la <strong>Politique de Confidentialité</strong> et toutes les <strong>Politiques d’Annulation, de Remboursement et de Garantie</strong>.',
        'back'       => 'Retour',
        'pay'        => 'Procéder au paiement',
        'order_full' => 'Détail complet de la commande',

        'titles' => [
            'terms'        => 'Conditions Générales',
            'privacy'      => 'Politique de Confidentialité',
            'cancellation' => 'Politique d’Annulation',
            'refunds'      => 'Politique de Remboursement',
            'warranty'     => 'Politique de Garantie',
            'payments'     => 'Moyens de Paiement',
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
