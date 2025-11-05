<?php

return [
    'ui' => [
        'page_title_index'  => 'Catégories de Clients',
        'page_title_create' => 'Nouvelle Catégorie de Client',
        'page_title_edit'   => 'Modifier la Catégorie',
        'header_index'      => 'Catégories de Clients',
        'header_create'     => 'Nouvelle Catégorie de Client',
        'header_edit'       => 'Modifier la Catégorie : :name',
        'info_card_title'   => 'Informations',
        'list_title'        => 'Liste des Catégories',
        'empty_list'        => 'Aucune catégorie enregistrée.',
    ],

    'buttons' => [
        'new_category' => 'Nouvelle Catégorie',
        'save'         => 'Enregistrer',
        'update'       => 'Mettre à jour',
        'cancel'       => 'Annuler',
        'back'         => 'Retour',
        'delete'       => 'Supprimer',
        'edit'         => 'Modifier',
    ],

    'table' => [
        'name'     => 'Nom',
        'age_from' => 'Âge à partir de',
        'age_to'   => 'Âge jusqu’à',
        'range'    => 'Plage',
        'active'   => 'Actif',
        'actions'  => 'Actions',
        'order'    => 'Ordre',
        'slug'     => 'Slug',
    ],

    'form' => [
        'name' => [
            'label'       => 'Nom',
            'placeholder' => 'Ex : Adulte, Enfant, Bébé',
            'required'    => 'Le nom est obligatoire',
        ],
        'slug' => [
            'label'       => 'Slug (identifiant unique)',
            'placeholder' => 'Ex : adult, child, infant',
            'title'       => 'Minuscules, chiffres, tirets et underscores uniquement',
            'helper'      => 'Uniquement lettres minuscules, chiffres, tirets (-) et underscores (_)',
        ],
        'age_from' => [
            'label'       => 'Âge à partir de',
            'placeholder' => 'Ex : 0, 3, 13, 65',
        ],
        'age_to' => [
            'label'         => 'Âge jusqu’à',
            'placeholder'   => 'Ex : 2, 12, 64 (laisser vide pour “sans limite”)',
            'hint_no_limit' => 'laisser vide pour “sans limite”',
        ],
        'order' => [
            'label'  => 'Ordre d’Affichage',
            'helper' => 'Détermine l’ordre d’affichage des catégories (plus petit = premier)',
        ],
        'active' => [
            'label'  => 'Catégorie Active',
            'helper' => 'Seules les catégories actives apparaissent dans les formulaires de réservation',
        ],
        'min_per_booking' => [
            'label'       => 'Minimum par Réservation',
            'placeholder' => 'Ex : 0, 1',
        ],
        'max_per_booking' => [
            'label'       => 'Maximum par Réservation',
            'placeholder' => 'Ex : 10 (laisser vide pour “sans limite”)',
        ],
    ],

    'states' => [
        'active'   => 'Actif',
        'inactive' => 'Inactif',
    ],

    'alerts' => [
        'success_created' => 'Catégorie créée avec succès.',
        'success_updated' => 'Catégorie mise à jour avec succès.',
        'success_deleted' => 'Catégorie supprimée avec succès.',
        'warning_title'  => 'Avertissement',
        'warning_text'   => 'Supprimer une catégorie utilisée dans des circuits ou réservations peut causer des problèmes. Il est recommandé de la désactiver plutôt que de la supprimer.',
    ],

    'dialogs' => [
        'delete' => [
            'title'   => 'Confirmer la Suppression',
            'text'    => 'Êtes-vous sûr de vouloir supprimer la catégorie :name ?',
            'caution' => 'Cette action est irréversible.',
        ],
    ],

    'rules' => [
        'title'                 => 'Règles Importantes',
        'no_overlap'            => 'Les plages d’âge ne peuvent pas se chevaucher entre les catégories actives.',
        'no_upper_limit_hint'   => 'Laissez “Âge jusqu’à” vide pour indiquer “sans limite supérieure”.',
        'slug_unique'           => 'Le slug doit être unique.',
        'order_affects_display' => 'L’ordre détermine leur affichage dans le système.',
    ],

    'help' => [
        'title'           => 'Aide',
        'examples_title'  => 'Exemples de Catégories',
        'infant'          => 'Bébé',
        'child'           => 'Enfant',
        'adult'           => 'Adulte',
        'senior'          => 'Senior',
        'age_from_tip'    => 'Âge à partir de :',
        'age_to_tip'      => 'Âge jusqu’à :',
        'range_tip'       => 'Plage :',
        'notes_title'     => 'Remarques',
        'notes' => [
            'use_null_age_to' => 'Utilisez age_to = NULL pour indiquer "sans limite supérieure" (ex : 18+ ans).',
            'inactive_hidden' => 'Les catégories inactives ne sont pas affichées dans les formulaires de réservation.',
        ],
    ],

    'info' => [
        'id'        => 'ID :',
        'created'   => 'Créé :',
        'updated'   => 'Mis à jour :',
        'date_fmt'  => 'd/m/Y H:i',
    ],

    'validation' => [
        'age_to_gte_age_from' => '“Âge jusqu’à” doit être supérieur ou égal à “Âge à partir de”.',
    ],
];
