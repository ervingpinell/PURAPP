<?php

return [

    'hotels' => [

        'title'             => 'Liste des Hôtels',
        'header'            => 'Hôtels enregistrés',
        'sort_alpha'        => 'Trier alphabétiquement',

        'name'              => 'Nom',
        'status'            => 'Statut',
        'actions'           => 'Actions',
        'active'            => 'Actif',
        'inactive'          => 'Inactif',
        'add'               => 'Ajouter',
        'edit'              => 'Modifier',
        'delete'            => 'Supprimer',
        'activate'          => 'Activer',
        'deactivate'        => 'Désactiver',
        'save_changes'      => 'Enregistrer les modifications',
        'cancel'            => 'Annuler',
        'close'             => 'Fermer',
        'no_records'        => 'Aucun hôtel enregistré.',
        'name_placeholder'  => 'Ex.: Hôtel Arenal Springs',

        'confirm_activate_title'    => 'Activer l’hôtel ?',
        'confirm_activate_text'     => 'Voulez-vous vraiment activer « :name » ?',
        'confirm_deactivate_title'  => 'Désactiver l’hôtel ?',
        'confirm_deactivate_text'   => 'Voulez-vous vraiment désactiver « :name » ?',
        'confirm_delete_title'      => 'Supprimer définitivement ?',
        'confirm_delete_text'       => '« :name » sera supprimé. Cette action est irréversible.',

        'created_success'    => 'Hôtel créé avec succès.',
        'updated_success'    => 'Hôtel mis à jour avec succès.',
        'deleted_success'    => 'Hôtel supprimé avec succès.',
        'activated_success'  => 'Hôtel activé avec succès.',
        'deactivated_success'=> 'Hôtel désactivé avec succès.',
        'sorted_success'     => 'Hôtels triés alphabétiquement.',
        'unexpected_error'   => 'Une erreur inattendue est survenue. Veuillez réessayer.',

        'validation' => [
            'name_required' => 'Le nom est obligatoire.',
            'name_unique'   => 'Cet hôtel existe déjà dans la liste.',
            'name_max'      => 'Le nom ne peut pas dépasser 255 caractères.',
        ],
        'error_title' => 'Erreur',

        'edit_title' => 'Modifier l’hôtel',
    ],

    'meeting_point' => [

        'ui' => [
            'page_title'   => 'Points de rencontre',
            'page_heading' => 'Points de rencontre',
        ],

        'badges' => [
            'count_badge' => ':count enregistrements',
            'active'      => 'Actif',
            'inactive'    => 'Inactif',
        ],

        'create' => [
            'title' => 'Ajouter un point de rencontre',
        ],

        'list' => [
            'title' => 'Liste',
            'empty' => 'Aucun enregistrement. Créez le premier ci-dessus.',
        ],

        'labels' => [
            'time'       => 'Heure',
            'sort_order' => 'Ordre',
        ],

        'fields' => [
            'name'                    => 'Nom',
            'pickup_time'             => 'Heure de prise en charge',
            'sort_order'              => 'Ordre',
            'description'             => 'Description',
            'map_url'                 => 'URL de la carte',
            'active'                  => 'Actif',
            'time_short'              => 'Heure',
            'map'                     => 'Carte',
            'status'                  => 'Statut',
            'actions'                 => 'Actions',

            'name_base'               => 'Nom (base)',
            'description_base'        => 'Description (base)',
            'locale'                  => 'Langue',
            'name_translation'        => 'Nom (traduction)',
            'description_translation' => 'Description (traduction)',
        ],

        'placeholders' => [
            'name'        => 'Parc central de La Fortuna',
            'pickup_time' => '07h10',
            'description' => 'Centre de La Fortuna',
            'map_url'     => 'https://maps.google.com/...',
            'search'      => 'Rechercher…',
            'optional'    => 'Optionnel',
        ],

        'hints' => [
            'name_example'   => 'Ex : « Parc central de La Fortuna ».',
            'name_base_sync' => 'Si vous ne le modifiez pas, il restera identique. Le nom par langue se modifie ci-dessous.',
            'fallback_sync'  => 'Si vous choisissez la langue <strong>:fallback</strong>, elle sera également synchronisée avec les champs de base.',
        ],

        'buttons' => [
            'reload'       => 'Recharger',
            'save'         => 'Enregistrer',
            'clear'        => 'Effacer',
            'create'       => 'Créer',
            'cancel'       => 'Annuler',
            'save_changes' => 'Enregistrer les modifications',
            'close'        => 'Fermer',
            'ok'           => 'OK',
            'confirm'      => 'Oui, continuer',
            'delete'       => 'Supprimer',
            'activate'     => 'Activer',
            'deactivate'   => 'Désactiver',
        ],

        'actions' => [
            'view_map'    => 'Voir la carte',
            'view_on_map' => 'Afficher sur la carte',
            'edit'        => 'Modifier',
            'delete'      => 'Supprimer',
            'activate'    => 'Activer',
            'deactivate'  => 'Désactiver',
        ],

        'confirm' => [
            'create_title'             => 'Créer un nouveau point ?',
            'create_text_with_name'    => '« :name » sera créé.',
            'create_text'              => 'Un nouveau point de rencontre sera créé.',
            'save_title'               => 'Enregistrer les modifications ?',
            'save_text'                => 'Le point et sa traduction seront mis à jour.',
            'deactivate_title'         => 'Désactiver le point ?',
            'deactivate_title_short'   => 'Désactiver ?',
            'deactivate_text'          => '« :name » deviendra inactif.',
            'activate_title'           => 'Activer le point ?',
            'activate_title_short'     => 'Activer ?',
            'activate_text'            => '« :name » deviendra actif.',
            'delete_title'             => 'Supprimer le point ?',
            'delete_title_short'       => 'Supprimer ?',
            'delete_text'              => '« :name » sera supprimé définitivement. Cette action est irréversible.',
        ],

        'validation' => [
            'title'                         => 'Erreurs de validation',
            'missing_translated_name_title' => 'Nom traduit manquant',
            'missing_translated_name_text'  => 'Veuillez compléter le champ du nom traduit.',
        ],

        'toasts' => [
            'success_title' => 'Succès',
            'error_title'   => 'Erreur',
        ],
    ],

];
