<?php

return [
    'ui' => [
        'page_title_index'  => 'Catégories de clients',
        'page_title_create' => 'Nouvelle catégorie de client',
        'page_title_edit'   => 'Modifier la catégorie',
        'header_index'      => 'Catégories de clients',
        'header_create'     => 'Nouvelle catégorie de client',
        'header_edit'       => 'Modifier la catégorie : :name',
        'info_card_title'   => 'Informations',

        // Nouvelles pour l’index/liste
        'list_title'        => 'Liste des catégories',
        'empty_list'        => 'Aucune catégorie enregistrée.',
    ],

    'buttons' => [
        'new_category' => 'Nouvelle catégorie',
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
        'range'    => 'Tranche',
        'active'   => 'Actif',
        'actions'  => 'Actions',

        // Nouvelles utilisées dans l’index
        'order'    => 'Ordre',
        'slug'     => 'Slug',
    ],

    'form' => [
        'translations' => [
            'title'          => 'Traductions du nom',
            'auto_translate' => 'Traduire automatiquement les autres langues (DeepL)',
            'regen_missing'  => 'Remplir automatiquement les champs vides (DeepL)',
            'at_least_first' => 'Vous devez remplir au moins la première langue.',
            'locale_hint'    => 'Traduction pour le locale :loc.',
        ],
        'name' => [
            'label'       => 'Nom',
            'placeholder' => 'Ex : Adulte, Enfant, Nourrisson',
            'required'    => 'Le nom est obligatoire.',
        ],
        'slug' => [
            'label'       => 'Slug (identifiant unique)',
            'placeholder' => 'Ex : adult, child, infant',
            'title'       => 'Seulement des minuscules, chiffres, tirets et tirets bas',
            'helper'      => 'Uniquement des lettres minuscules, chiffres, tirets (-) et tirets bas (_)',
        ],
        'age_from' => [
            'label'       => 'Âge à partir de',
            'placeholder' => 'Ex : 0, 3, 13, 65',
        ],
        'age_to' => [
            'label'         => 'Âge jusqu’à',
            'placeholder'   => 'Ex : 2, 12, 64 (laisser vide pour « sans limite »)',
            'hint_no_limit' => 'laisser vide pour « sans limite »',
        ],
        'order' => [
            'label'  => 'Ordre d’affichage',
            'helper' => 'Détermine l’ordre dans lequel les catégories apparaissent (plus petit = en premier)',
        ],
        'active' => [
            'label'  => 'Catégorie active',
            'helper' => 'Seules les catégories actives sont affichées dans les formulaires de réservation',
        ],
        'min_per_booking' => [
            'label'       => 'Minimum par réservation',
            'placeholder' => 'Ex : 0, 1',
        ],
        'max_per_booking' => [
            'label'       => 'Maximum par réservation',
            'placeholder' => 'Ex : 10 (laisser vide pour « sans limite »)',
        ],
    ],

    'states' => [
        'active'     => 'Actif',
        'inactive'   => 'Inactif',
        'activate'   => 'Activer',
        'deactivate' => 'Désactiver',
    ],

    'alerts' => [
        'success_created' => 'Catégorie créée avec succès.',
        'success_updated' => 'Catégorie mise à jour avec succès.',
        'success_deleted' => 'Catégorie supprimée avec succès.',
        'warning_title'   => 'Avertissement',
        'warning_text'    => 'La suppression d’une catégorie utilisée dans des excursions ou des réservations peut causer des problèmes. Il est recommandé de la désactiver plutôt que de la supprimer.',
    ],

    'dialogs' => [
        'delete' => [
            'title'   => 'Confirmer la suppression',
            'text'    => 'Êtes-vous sûr de vouloir supprimer la catégorie :name ?',
            'caution' => 'Cette action est irréversible.',
        ],
    ],

    'rules' => [
        'title'                 => 'Règles importantes',
        'no_overlap'            => 'Les tranches d’âge ne peuvent pas se chevaucher entre les catégories actives.',
        'no_upper_limit_hint'   => 'Laissez « Âge jusqu’à » vide pour indiquer « sans limite supérieure ».',
        'slug_unique'           => 'Le slug doit être unique.',
        'order_affects_display' => 'L’ordre détermine la façon dont elles sont affichées dans le système.',
    ],

    'help' => [
        'title'           => 'Aide',
        'examples_title'  => 'Exemples de catégories',
        'infant'          => 'Nourrisson',
        'child'           => 'Enfant',
        'adult'           => 'Adulte',
        'senior'          => 'Senior',
        'age_from_tip'    => 'Âge à partir de :',
        'age_to_tip'      => 'Âge jusqu’à :',
        'range_tip'       => 'Tranche :',
        'notes_title'     => 'Remarques',
        'notes' => [
            'use_null_age_to' => 'Utilisez age_to = NULL pour indiquer « sans limite supérieure » (ex : 18+ ans).',
            'inactive_hidden' => 'Les catégories inactives ne sont pas affichées dans les formulaires de réservation.',
        ],
    ],

    'info' => [
        'id'       => 'ID :',
        'created'  => 'Créé :',
        'updated'  => 'Mis à jour :',
        'date_fmt' => 'd/m/Y H:i',
    ],

    'validation' => [
        'age_to_gte_age_from' => 'L’« âge jusqu’à » doit être supérieur ou égal à « l’âge à partir de ».',
    ],
];
