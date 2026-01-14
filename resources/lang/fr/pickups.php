<?php

return [

    'hotels' => [

        // Titre / en-têtes
        'title'             => 'Liste des hôtels',
        'header'            => 'Hôtels enregistrés',
        'sort_alpha'        => 'Trier par ordre alphabétique',

        // Champs / colonnes / actions
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
        'name_placeholder'  => 'Ex. : Hôtel Arenal Springs',

        // Confirmations
        'confirm_activate_title'    => 'Activer l’hôtel ?',
        'confirm_activate_text'     => 'Es-tu sûr(e) de vouloir activer « :name » ?',
        'confirm_deactivate_title'  => 'Désactiver l’hôtel ?',
        'confirm_deactivate_text'   => 'Es-tu sûr(e) de vouloir désactiver « :name » ?',
        'confirm_delete_title'      => 'Supprimer définitivement ?',
        'confirm_delete_text'       => '« :name » sera supprimé. Cette action ne peut pas être annulée.',

        // Messages (flash)
        'created_success'    => 'Hôtel créé avec succès.',
        'updated_success'    => 'Hôtel mis à jour avec succès.',
        'deleted_success'    => 'Hôtel supprimé avec succès.',
        'activated_success'  => 'Hôtel activé avec succès.',
        'deactivated_success' => 'Hôtel désactivé avec succès.',
        'sorted_success'     => 'Hôtels triés par ordre alphabétique.',
        'unexpected_error'   => 'Une erreur inattendue s’est produite. Merci de réessayer.',

        // Validation / génériques
        'validation' => [
            'name_required' => 'Le nom est obligatoire.',
            'name_unique'   => 'Cet hôtel existe déjà dans la liste.',
            'name_max'      => 'Le nom ne peut pas dépasser 255 caractères.',
        ],
        'error_title' => 'Erreur',

        // Modales
        'edit_title' => 'Modifier l’hôtel',
    ],

    'meeting_point' => [

        // UI
        'ui' => [
            'page_title'   => 'Points de rencontre',
            'page_heading' => 'Points de rencontre',
        ],

        // Badges
        'badges' => [
            'count_badge' => ':count enregistrements',
            'active'      => 'Actif',
            'inactive'    => 'Inactif',
        ],

        // Création
        'create' => [
            'title' => 'Ajouter un point',
        ],

        // Liste
        'list' => [
            'title' => 'Liste',
            'empty' => 'Aucun enregistrement. Crée le premier en haut.',
        ],

        // Labels compacts sur cartes
        'labels' => [
            'time'       => 'Heure',
            'sort_order' => 'Ordre',
        ],

        // Champs
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
            'instructions'            => 'Instructions',

            // Édition / traductions
            'name_base'               => 'Nom (base)',
            'description_base'        => 'Description (base)',
            'instructions_base'       => 'Instructions (base)',
            'locale'                  => 'Langue',
            'name_translation'        => 'Nom (traduction)',
            'description_translation' => 'Description (traduction)',
            'instructions_translation' => 'Instructions (traduction)',
        ],

        // Placeholders
        'placeholders' => [
            'name'         => 'Parc Central de La Fortuna',
            'pickup_time'  => '7:10 AM',
            'description'  => 'Centre-ville de La Fortuna',
            'instructions' => 'Veuillez nous retrouver devant l\'église du parc central. Notre guide portera un t-shirt vert avec le logo :company.',
            'map_url'      => 'https://maps.google.com/...',
            'search'       => 'Rechercher…',
            'optional'     => 'Optionnel',
        ],

        // Aides
        'hints' => [
            'name_example'   => 'Ex. : « Parc central de La Fortuna ».',
            'name_base_sync' => 'Si tu ne le modifies pas, il reste inchangé. Le nom par langue se modifie ci-dessous.',
            'fallback_sync'  => 'Si tu choisis le locale <strong>:fallback</strong>, il sera également synchronisé avec les champs de base.',
        ],

        // Boutons
        'buttons' => [
            'reload'       => 'Recharger',
            'save'         => 'Enregistrer',
            'clear'        => 'Effacer',
            'create'       => 'Créer',
            'cancel'       => 'Annuler',
            'save_changes' => 'Enregistrer les modifications',
            'close'        => 'Fermer',
            'ok'           => 'Compris',
            'confirm'      => 'Oui, continuer',
            'delete'       => 'Supprimer',
            'activate'     => 'Activer',
            'deactivate'   => 'Désactiver',
        ],

        // Actions (titres / tooltips)
        'actions' => [
            'view_map'    => 'Voir la carte',
            'view_on_map' => 'Voir sur la carte',
            'edit'        => 'Modifier',
            'delete'      => 'Supprimer',
            'activate'    => 'Activer',
            'deactivate'  => 'Désactiver',
        ],

        // Confirmations
        'confirm' => [
            'create_title'             => 'Créer un nouveau point de rencontre ?',
            'create_text_with_name'    => '« :name » sera créé.',
            'create_text'              => 'Un nouveau point de rencontre sera créé.',

            'save_title'               => 'Enregistrer les modifications ?',
            'save_text'                => 'Le point de rencontre et la traduction sélectionnée seront mis à jour.',

            'deactivate_title'         => 'Désactiver le point de rencontre ?',
            'deactivate_title_short'   => 'Désactiver ?',
            'deactivate_text'          => '« :name » sera marqué comme inactif.',

            'activate_title'           => 'Activer le point de rencontre ?',
            'activate_title_short'     => 'Activer ?',
            'activate_text'            => '« :name » sera marqué comme actif.',

            'delete_title'             => 'Supprimer le point de rencontre ?',
            'delete_title_short'       => 'Supprimer ?',
            'delete_text'              => '« :name » sera supprimé définitivement. Cette action ne peut pas être annulée.',
        ],

        // Validation / Toastr / SweetAlert
        'validation' => [
            'title'                         => 'Erreurs de validation',
            'missing_translated_name_title' => 'Nom traduit manquant',
            'missing_translated_name_text'  => 'Complète le champ de nom traduit.',
        ],

        'toasts' => [
            'success_title'        => 'Succès',
            'error_title'          => 'Erreur',
            'created_success'      => 'Point créé avec succès.',
            'updated_success'      => 'Point mis à jour avec succès.',
            'deleted_success'      => 'Point déplacé vers la corbeille.',
            'activated_success'    => 'Point activé avec succès.',
            'deactivated_success'  => 'Point désactivé avec succès.',
        ],

        // Gestion de la corbeille
        'trash' => [
            'title'                => 'Corbeille',
            'empty'                => 'Aucun point supprimé.',
            'deleted_by'           => 'Supprimé par',
            'deleted_at'           => 'Supprimé le',
            'auto_delete_in'       => 'Sera supprimé dans',
            'days'                 => '{1} :count jour|[2,*] :count jours',
            'restore_success'      => 'Point restauré avec succès.',
            'force_delete_success' => 'Point supprimé définitivement.',
        ],
    ],

];
