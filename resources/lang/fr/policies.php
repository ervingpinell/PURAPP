<?php

return [

    'page_title' => 'Politiques',
    'no_policies' => 'Aucune politique disponible pour le moment.',
    'no_sections' => 'Aucune section disponible pour le moment.',
    // =========================================================
    // [01] CHAMPS
    // =========================================================
    'fields' => [
        'title'       => 'Titre',
        'description' => 'Description',
        'type'        => 'Type',
        'is_active'   => 'Actif',
    ],

    // =========================================================
    // [02] TYPES
    // =========================================================
    'types' => [
        'cancellation' => 'Politique d\'annulation',
        'refund'       => 'Politique de remboursement',
        'terms'        => 'Termes et conditions',
    ],

    // =========================================================
    // [03] MESSAGES
    // =========================================================
    'success' => [
        'created'   => 'Politique créée avec succès.',
        'updated'   => 'Politique mise à jour avec succès.',
        'deleted'   => 'Politique supprimée avec succès.',
    ],

    'error' => [
        'create' => 'Impossible de créer la politique.',
        'update' => 'Impossible de mettre à jour la politique.',
        'delete' => 'Impossible de supprimer la politique.',
    ],
];
