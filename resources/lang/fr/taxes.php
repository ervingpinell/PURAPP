<?php

return [
    'title' => 'Gestion des Taxes',
    'create' => 'Créer une Taxe',
    'edit' => 'Modifier la Taxe',
    'fields' => [
        'name' => 'Nom',
        'code' => 'Code',
        'rate' => 'Taux/Montant',
        'type' => 'Type',
        'apply_to' => 'Appliquer à',
        'is_inclusive' => 'Inclus',
        'is_active' => 'Actif',
        'sort_order' => 'Ordre',
    ],
    'types' => [
        'percentage' => 'Pourcentage (%)',
        'fixed' => 'Montant Fixe ($)',
    ],
    'apply_to_options' => [
        'subtotal' => 'Sous-total',
        'total' => 'Total (En cascade)',
        'per_person' => 'Par Personne',
    ],
    'messages' => [
        'created' => 'Taxe créée avec succès.',
        'updated' => 'Taxe mise à jour avec succès.',
        'deleted' => 'Taxe supprimée avec succès.',
        'toggled' => 'Statut de la taxe mis à jour.',
        'select_taxes' => 'Sélectionnez les taxes qui s\'appliquent à ce circuit.',
    ],
];
