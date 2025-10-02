<?php

/*************************************************************
 *  MODULE DE TRADUCTION : TOURS
 *  Fichier : resources/lang/fr/m_tours.php
 *
 *  Index (section et ligne de début)
 *  [01] COMMON           -> ligne 23
 *  [02] AMENITY          -> ligne 31
 *  [03] SCHEDULE         -> ligne 106
 *  [04] ITINERARY_ITEM   -> ligne 218
 *  [05] ITINERARY        -> ligne 288
 *  [06] LANGUAGE         -> ligne 364
 *  [07] TOUR             -> ligne 454
 *  [08] IMAGES           -> ligne 578
 *************************************************************/

return [

    // =========================================================
    // [01] COMMON
    // =========================================================
    'common' => [
        'success_title' => 'Succès',
        'error_title'   => 'Erreur',
    ],

    // =========================================================
    // [02] AMENITY
    // =========================================================
    'amenity' => [
        'fields' => [
            'name' => 'Nom',
        ],

        'status' => [
            'active'   => 'Actif',
            'inactive' => 'Inactif',
        ],

        'ui' => [
            'page_title'    => 'Commodités',
            'page_heading'  => 'Gestion des commodités',
            'list_title'    => 'Liste des commodités',

            'add'            => 'Ajouter une commodité',
            'create_title'   => 'Enregistrer une commodité',
            'edit_title'     => 'Modifier la commodité',
            'save'           => 'Enregistrer',
            'update'         => 'Mettre à jour',
            'cancel'         => 'Annuler',
            'close'          => 'Fermer',
            'state'          => 'Statut',
            'actions'        => 'Actions',
            'delete_forever' => 'Supprimer définitivement',

            'processing' => 'Traitement...',
            'applying'   => 'Application...',
            'deleting'   => 'Suppression...',

            'toggle_on'  => 'Activer la commodité',
            'toggle_off' => 'Désactiver la commodité',

            'toggle_confirm_on_title'  => 'Activer la commodité ?',
            'toggle_confirm_off_title' => 'Désactiver la commodité ?',
            'toggle_confirm_on_html'   => 'La commodité <b>:label</b> sera active.',
            'toggle_confirm_off_html'  => 'La commodité <b>:label</b> sera inactive.',

            'delete_confirm_title' => 'Supprimer définitivement ?',
            'delete_confirm_html'  => '<b>:label</b> sera supprimée et vous ne pourrez pas annuler.',

            'yes_continue' => 'Oui, continuer',
            'yes_delete'   => 'Oui, supprimer',

            'item_this' => 'cette commodité',
        ],

        'success' => [
            'created'     => 'Commodité créée avec succès.',
            'updated'     => 'Commodité mise à jour avec succès.',
            'activated'   => 'Commodité activée avec succès.',
            'deactivated' => 'Commodité désactivée avec succès.',
            'deleted'     => 'Commodité supprimée définitivement.',
        ],

        'error' => [
            'create' => 'Impossible de créer la commodité.',
            'update' => 'Impossible de mettre à jour la commodité.',
            'toggle' => 'Impossible de changer le statut de la commodité.',
            'delete' => 'Impossible de supprimer la commodité.',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Nom invalide',
                'required' => 'Le :attribute est requis.',
                'string'   => 'Le :attribute doit être une chaîne.',
                'max'      => 'Le :attribute ne peut pas dépasser :max caractères.',
            ],
        ],
    ],

    // =========================================================
    // [03] SCHEDULE
    // =========================================================
    'schedule' => [
        'fields' => [
            'start_time'     => 'Début',
            'end_time'       => 'Fin',
            'label'          => 'Libellé',
            'label_optional' => 'Libellé (optionnel)',
            'max_capacity'   => 'Capacité max.',
            'active'         => 'Actif',
        ],

        'status' => [
            'active'   => 'Actif',
            'inactive' => 'Inactif',
        ],

        'ui' => [
            'page_title'        => 'Horaires des visites',
            'page_heading'      => 'Gestion des horaires',

            'general_title'     => 'Horaires généraux',
            'new_schedule'      => 'Nouvel horaire',
            'new_general_title' => 'Nouvel horaire général',
            'new'               => 'Nouveau',
            'edit_schedule'     => 'Modifier l’horaire',
            'edit_global'       => 'Modifier (global)',

            'assign_existing'    => 'Assigner un existant',
            'assign_to_tour'     => 'Assigner l’horaire à « :tour »',
            'select_schedule'    => 'Sélectionnez un horaire',
            'choose'             => 'Choisir',
            'assign'             => 'Assigner',
            'new_for_tour_title' => 'Nouvel horaire pour « :tour »',

            'time_range'        => 'Plage horaire',
            'state'             => 'Statut',
            'actions'           => 'Actions',
            'schedule_state'    => 'Horaire',
            'assignment_state'  => 'Affectation',
            'no_general'        => 'Aucun horaire général.',
            'no_tour_schedules' => 'Cette visite n’a pas encore d’horaires.',
            'no_label'          => 'Sans libellé',
            'assigned_count'    => 'horaire(s) assigné(s)',

            'toggle_global_title'     => 'Activer/Désactiver (global)',
            'toggle_global_on_title'  => 'Activer l’horaire (global) ?',
            'toggle_global_off_title' => 'Désactiver l’horaire (global) ?',
            'toggle_global_on_html'   => '<b>:label</b> sera activé pour toutes les visites.',
            'toggle_global_off_html'  => '<b>:label</b> sera désactivé pour toutes les visites.',

            'toggle_on_tour'          => 'Activer sur cette visite',
            'toggle_off_tour'         => 'Désactiver sur cette visite',
            'toggle_assign_on_title'  => 'Activer sur cette visite ?',
            'toggle_assign_off_title' => 'Désactiver sur cette visite ?',
            'toggle_assign_on_html'   => 'L’affectation sera <b>active</b> pour <b>:tour</b>.',
            'toggle_assign_off_html'  => 'L’affectation sera <b>inactive</b> pour <b>:tour</b>.',

            'detach_from_tour'     => 'Retirer de la visite',
            'detach_confirm_title' => 'Retirer de la visite ?',
            'detach_confirm_html'  => 'L’horaire sera <b>désassigné</b> de <b>:tour</b>.',

            'delete_forever'       => 'Supprimer (global)',
            'delete_confirm_title' => 'Supprimer définitivement ?',
            'delete_confirm_html'  => '<b>:label</b> (global) sera supprimé et vous ne pourrez pas annuler.',

            'yes_continue' => 'Oui, continuer',
            'yes_delete'   => 'Oui, supprimer',
            'yes_detach'   => 'Oui, retirer',

            'this_schedule' => 'cet horaire',
            'this_tour'     => 'cette visite',

            'processing'     => 'Traitement...',
            'applying'       => 'Application...',
            'deleting'       => 'Suppression...',
            'removing'       => 'Retrait...',
            'saving_changes' => 'Enregistrement des modifications...',
            'save'           => 'Enregistrer',
            'save_changes'   => 'Enregistrer',
            'cancel'         => 'Annuler',

            'missing_fields_title' => 'Données manquantes',
            'missing_fields_text'  => 'Vérifiez les champs requis (début, fin et capacité).',
            'could_not_save'       => 'Impossible d’enregistrer',
        ],

        'success' => [
            'created'                => 'Horaire créé avec succès.',
            'updated'                => 'Horaire mis à jour avec succès.',
            'activated_global'       => 'Horaire activé (global).',
            'deactivated_global'     => 'Horaire désactivé (global).',
            'attached'               => 'Horaire assigné à la visite.',
            'detached'               => 'Horaire retiré de la visite.',
            'assignment_activated'   => 'Affectation activée pour cette visite.',
            'assignment_deactivated' => 'Affectation désactivée pour cette visite.',
            'deleted'                => 'Horaire supprimé avec succès.',
        ],

        'error' => [
            'create'               => 'Problème lors de la création de l’horaire.',
            'update'               => 'Problème lors de la mise à jour de l’horaire.',
            'toggle'               => 'Impossible de changer le statut global de l’horaire.',
            'attach'               => 'Impossible d’assigner l’horaire à la visite.',
            'detach'               => 'Impossible de désassigner l’horaire de la visite.',
            'assignment_toggle'    => 'Impossible de changer le statut de l’affectation.',
            'not_assigned_to_tour' => 'L’horaire n’est pas assigné à cette visite.',
            'delete'               => 'Problème lors de la suppression de l’horaire.',
        ],
    ],

    // =========================================================
    // [04] ITINERARY_ITEM
    // =========================================================
    'itinerary_item' => [
        'fields' => [
            'title'       => 'Titre',
            'description' => 'Description',
        ],

        'status' => [
            'active'   => 'Actif',
            'inactive' => 'Inactif',
        ],

        'ui' => [
            'list_title'    => 'Éléments d’itinéraire',
            'add_item'      => 'Ajouter un élément',
            'register_item' => 'Enregistrer l’élément',
            'edit_item'     => 'Modifier l’élément',
            'save'          => 'Enregistrer',
            'update'        => 'Mettre à jour',
            'cancel'        => 'Annuler',
            'state'         => 'Statut',
            'actions'       => 'Actions',
            'see_more'      => 'Voir plus',
            'see_less'      => 'Voir moins',

            'toggle_on'  => 'Activer l’élément',
            'toggle_off' => 'Désactiver l’élément',

            'delete_forever'       => 'Supprimer définitivement',
            'delete_confirm_title' => 'Supprimer définitivement ?',
            'delete_confirm_html'  => '<b>:label</b> sera supprimé et vous ne pourrez pas annuler.',
            'yes_delete'           => 'Oui, supprimer',
            'item_this'            => 'cet élément',

            'processing' => 'Traitement...',
            'applying'   => 'Application...',
            'deleting'   => 'Suppression...',
        ],

        'success' => [
            'created'     => 'Élément d’itinéraire créé avec succès.',
            'updated'     => 'Élément mis à jour avec succès.',
            'activated'   => 'Élément activé avec succès.',
            'deactivated' => 'Élément désactivé avec succès.',
            'deleted'     => 'Élément supprimé définitivement.',
        ],

        'error' => [
            'create' => 'Impossible de créer l’élément.',
            'update' => 'Impossible de mettre à jour l’élément.',
            'toggle' => 'Impossible de changer le statut de l’élément.',
            'delete' => 'Impossible de supprimer l’élément.',
        ],

        'validation' => [
            'title' => [
                'required' => 'Le :attribute est requis.',
                'string'   => 'Le :attribute doit être une chaîne.',
                'max'      => 'Le :attribute ne peut pas dépasser :max caractères.',
            ],
            'description' => [
                'required' => 'La :attribute est requise.',
                'string'   => 'La :attribute doit être une chaîne.',
                'max'      => 'La :attribute ne peut pas dépasser :max caractères.',
            ],
        ],
    ],

    // =========================================================
    // [05] ITINERARY
    // =========================================================
    'itinerary' => [
        'fields' => [
            'name'                 => 'Nom de l’itinéraire',
            'description'          => 'Description',
            'description_optional' => 'Description (optionnelle)',
        ],

        'status' => [
            'active'   => 'Actif',
            'inactive' => 'Inactif',
        ],

        'ui' => [
            'page_title'    => 'Itinéraires & éléments',
            'page_heading'  => 'Gestion des itinéraires et éléments',
            'new_itinerary' => 'Nouvel itinéraire',

            'assign'        => 'Assigner',
            'edit'          => 'Modifier',
            'save'          => 'Enregistrer',
            'cancel'        => 'Annuler',
            'close'         => 'Fermer',
            'create_title'  => 'Créer un nouvel itinéraire',
            'create_button' => 'Créer',

            'toggle_on'  => 'Activer l’itinéraire',
            'toggle_off' => 'Désactiver l’itinéraire',
            'toggle_confirm_on_title'  => 'Activer l’itinéraire ?',
            'toggle_confirm_off_title' => 'Désactiver l’itinéraire ?',
            'toggle_confirm_on_html'   => 'L’itinéraire <b>:label</b> sera <b>actif</b>.',
            'toggle_confirm_off_html'  => 'L’itinéraire <b>:label</b> sera <b>inactif</b>.',
            'yes_continue' => 'Oui, continuer',

            'assign_title'          => 'Assigner des éléments à :name',
            'drag_hint'             => 'Glissez-déposez les éléments pour définir l’ordre.',
            'drag_handle'           => 'Glisser pour réorganiser',
            'select_one_title'      => 'Sélectionnez au moins un élément',
            'select_one_text'       => 'Veuillez sélectionner au moins un élément pour continuer.',
            'assign_confirm_title'  => 'Assigner les éléments sélectionnés ?',
            'assign_confirm_button' => 'Oui, assigner',
            'assigning'             => 'Affectation...',

            'no_items_assigned'       => 'Aucun élément assigné à cet itinéraire.',
            'itinerary_this'          => 'cet itinéraire',
            'processing'              => 'Traitement...',
            'saving'                  => 'Enregistrement...',
            'activating'              => 'Activation...',
            'deactivating'            => 'Désactivation...',
            'applying'                => 'Application...',
            'deleting'                => 'Suppression...',
            'flash_success_title'     => 'Succès',
            'flash_error_title'       => 'Erreur',
            'validation_failed_title' => 'Impossible de traiter',
        ],

        'success' => [
            'created'        => 'Itinéraire créé avec succès.',
            'updated'        => 'Itinéraire mis à jour avec succès.',
            'activated'      => 'Itinéraire activé avec succès.',
            'deactivated'    => 'Itinéraire désactivé avec succès.',
            'deleted'        => 'Itinéraire supprimé définitivement.',
            'items_assigned' => 'Éléments assignés avec succès.',
        ],

        'error' => [
            'create'  => 'Impossible de créer l’itinéraire.',
            'update'  => 'Impossible de mettre à jour l’itinéraire.',
            'toggle'  => 'Impossible de changer le statut de l’itinéraire.',
            'delete'  => 'Impossible de supprimer l’itinéraire.',
            'assign'  => 'Impossible d’assigner les éléments.',
        ],
    ],

    // =========================================================
    // [06] LANGUAGE
    // =========================================================
    'language' => [
        'fields' => [
            'name' => 'Langue',
        ],

        'status' => [
            'active'   => 'Actif',
            'inactive' => 'Inactif',
        ],

        'ui' => [
            'page_title'   => 'Langues des visites',
            'page_heading' => 'Gestion des langues',
            'list_title'   => 'Liste des langues',

            'table' => [
                'id'      => 'ID',
                'name'    => 'Langue',
                'state'   => 'Statut',
                'actions' => 'Actions',
            ],

            'add'            => 'Ajouter une langue',
            'create_title'   => 'Enregistrer une langue',
            'edit_title'     => 'Modifier la langue',
            'save'           => 'Enregistrer',
            'update'         => 'Mettre à jour',
            'cancel'         => 'Annuler',
            'close'          => 'Fermer',
            'actions'        => 'Actions',
            'delete_forever' => 'Supprimer définitivement',

            'processing'   => 'Traitement...',
            'saving'       => 'Enregistrement...',
            'activating'   => 'Activation...',
            'deactivating' => 'Désactivation...',
            'deleting'     => 'Suppression...',

            'toggle_on'  => 'Activer la langue',
            'toggle_off' => 'Désactiver la langue',
            'toggle_confirm_on_title'  => 'Activer la langue ?',
            'toggle_confirm_off_title' => 'Désactiver la langue ?',
            'toggle_confirm_on_html'   => 'La langue <b>:label</b> sera <b>active</b>.',
            'toggle_confirm_off_html'  => 'La langue <b>:label</b> sera <b>inactive</b>.',
            'edit_confirm_title'       => 'Enregistrer les modifications ?',
            'edit_confirm_button'      => 'Oui, enregistrer',

            'yes_continue' => 'Oui, continuer',
            'yes_delete'   => 'Oui, supprimer',
            'item_this'    => 'cette langue',

            'flash' => [
                'activated_title'   => 'Langue activée',
                'deactivated_title' => 'Langue désactivée',
                'updated_title'     => 'Langue mise à jour',
                'created_title'     => 'Langue enregistrée',
                'deleted_title'     => 'Langue supprimée',
            ],
        ],

        'success' => [
            'created'     => 'Langue créée avec succès.',
            'updated'     => 'Langue mise à jour avec succès.',
            'activated'   => 'Langue activée avec succès.',
            'deactivated' => 'Langue désactivée avec succès.',
            'deleted'     => 'Langue supprimée avec succès.',
        ],

        'error' => [
            'create' => 'Impossible de créer la langue.',
            'update' => 'Impossible de mettre à jour la langue.',
            'toggle' => 'Impossible de changer le statut de la langue.',
            'delete' => 'Impossible de supprimer la langue.',
            'save'   => 'Enregistrement impossible',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Nom invalide',
                'required' => 'Le nom de la langue est requis.',
                'string'   => 'Le :attribute doit être une chaîne.',
                'max'      => 'Le :attribute ne peut pas dépasser :max caractères.',
                'unique'   => 'Une langue avec ce nom existe déjà.',
            ],
        ],
    ],

// =========================================================
// [07] TOUR
// =========================================================
'tour' => [
    'title' => 'Tours',

    'fields' => [
        'id'            => 'ID',
        'name'          => 'Nom',
        'overview'      => 'Aperçu',
        'amenities'     => 'Commodités',
        'exclusions'    => 'Exclusions',
        'itinerary'     => 'Itinéraire',
        'languages'     => 'Langues',
        'schedules'     => 'Horaires',
        'adult_price'   => 'Tarif adulte',
        'kid_price'     => 'Tarif enfant',
        'length_hours'  => 'Durée (heures)',
        'max_capacity'  => 'Capacité max.',
        'type'          => 'Type de tour',
        'viator_code'   => 'Code Viator',
        'status'        => 'Statut',
        'actions'       => 'Actions',
    ],

    'table' => [
        'id'            => 'ID',
        'name'          => 'Nom',
        'overview'      => 'Aperçu',
        'amenities'     => 'Commodités',
        'exclusions'    => 'Exclusions',
        'itinerary'     => 'Itinéraire',
        'languages'     => 'Langues',
        'schedules'     => 'Horaires',
        'adult_price'   => 'Tarif adulte',
        'kid_price'     => 'Tarif enfant',
        'length_hours'  => 'Durée (h)',
        'max_capacity'  => 'Cap. max.',
        'type'          => 'Type',
        'viator_code'   => 'Code Viator',
        'status'        => 'Statut',
        'actions'       => 'Actions',
        'slug'          => 'url',
    ],

    'status' => [
        'active'   => 'Actif',
        'inactive' => 'Inactif',
    ],

    'success' => [
        'created'     => 'Tour créé avec succès.',
        'updated'     => 'Tour mis à jour avec succès.',
        'deleted'     => 'Tour supprimé.',
        'toggled'     => 'Statut du tour mis à jour.',
        'activated'   => 'Tour activé avec succès.',
        'deactivated' => 'Tour désactivé avec succès.',
    ],

    'error' => [
        'create'    => 'Un problème est survenu lors de la création du tour.',
        'update'    => 'Un problème est survenu lors de la mise à jour du tour.',
        'delete'    => 'Un problème est survenu lors de la suppression du tour.',
        'toggle'    => 'Un problème est survenu lors du changement de statut du tour.',
        'not_found' => 'Le tour n’existe pas.',
    ],

    'ui' => [
        'page_title'       => 'Gestion des tours',
        'page_heading'     => 'Gestion des tours',
        'create_title'     => 'Créer un tour',
        'edit_title'       => 'Modifier le tour',
        'delete_title'     => 'Supprimer le tour',
        'cancel'           => 'Annuler',
        'save'             => 'Enregistrer',
        'update'           => 'Mettre à jour',
        'delete_confirm'   => 'Supprimer ce tour ?',
        'toggle_on'        => 'Activer',
        'toggle_off'       => 'Désactiver',
        'toggle_on_title'  => 'Activer le tour ?',
        'toggle_off_title' => 'Désactiver le tour ?',
        'toggle_on_button' => 'Oui, activer',
        'toggle_off_button'=> 'Oui, désactiver',
        'see_more'         => 'Voir plus',
        'see_less'         => 'Masquer',
        'load_more'        => 'Charger plus',
        'loading'          => 'Chargement…',
        'load_more_error'  => 'Impossible de charger plus de tours.',
        'confirm_title'    => 'Confirmation',
        'confirm_text'     => 'Voulez-vous confirmer cette action ?',
        'yes_confirm'      => 'Oui, confirmer',
        'no_confirm'       => 'Non, annuler',
        'add_tour'         => 'Ajouter un tour',
        'edit_tour'        => 'Modifier le tour',
        'delete_tour'      => 'Supprimer le tour',
        'toggle_tour'      => 'Activer/Désactiver le tour',
        'view_cart'        => 'Voir le panier',
        'add_to_cart'      => 'Ajouter au panier',

        'available_languages'    => 'Langues disponibles',
        'default_capacity'       => 'Capacité par défaut',
        'create_new_schedules'   => 'Créer de nouveaux horaires',
        'multiple_hint_ctrl_cmd' => 'Maintenez CTRL/CMD pour en sélectionner plusieurs',
        'use_existing_schedules' => 'Utiliser des horaires existants',
        'add_schedule'           => 'Ajouter un horaire',
        'schedules_title'        => 'Horaires du tour',
        'amenities_included'     => 'Commodités incluses',
        'amenities_excluded'     => 'Commodités exclues',
        'color'                  => 'Couleur du tour',
        'remove'                 => 'Supprimer',
        'choose_itinerary'       => 'Choisir un itinéraire',
        'select_type'            => 'Sélectionner un type',
        'empty_means_default'    => 'Par défaut',

        'none' => [
            'amenities'       => 'Aucune commodité',
            'exclusions'      => 'Aucune exclusion',
            'itinerary'       => 'Aucun itinéraire',
            'itinerary_items' => 'Aucun élément',
            'languages'       => 'Aucune langue',
            'schedules'       => 'Aucun horaire',
        ],
    ],
],

// =========================================================
// [08] IMAGES
// =========================================================
'image' => [

    'limit_reached_title' => 'Limite atteinte',
    'limit_reached_text'  => 'La limite d’images pour ce tour a été atteinte.',
    'upload_success'      => 'Images téléchargées avec succès.',
    'upload_none'         => 'Aucune image n’a été téléchargée.',
    'upload_truncated'    => 'Certains fichiers ont été ignorés en raison de la limite par tour.',
    'done'                => 'Terminé',
    'notice'              => 'Avis',
    'saved'               => 'Enregistré',
    'caption_updated'     => 'Légende mise à jour avec succès.',
    'deleted'             => 'Supprimé',
    'image_removed'       => 'Image supprimée avec succès.',
    'invalid_order'       => 'Ordre invalide.',
    'nothing_to_reorder'  => 'Rien à réorganiser.',
    'order_saved'         => 'Ordre enregistré.',
    'cover_updated_title' => 'Couverture mise à jour',
    'cover_updated_text'  => 'Cette image est maintenant la couverture.',
    'deleting'            => 'Suppression...',

    'ui' => [
        'page_title_pick'     => 'Images de Tours — Choisir un tour',
        'page_heading'        => 'Images de Tours',
        'choose_tour'         => 'Choisir un tour',
        'search_placeholder'  => 'Rechercher par ID ou nom…',
        'search_button'       => 'Rechercher',
        'no_results'          => 'Aucun tour trouvé.',
        'manage_images'       => 'Gérer les images',
        'cover_alt'           => 'Couverture',
        'images_label'        => 'images',
        'upload_btn'          => 'Téléverser',
        'caption_placeholder' => 'Légende (optionnelle)',
        'set_cover_btn'       => 'Définir comme couverture',
        'no_images'           => 'Aucune image pour ce tour pour l’instant.',
        'delete_btn'          => 'Supprimer',
        'show_btn'       => 'Afficher',
        'close_btn'      => 'Fermer',
        'preview_title'  => 'Aperçu de l\'image',

        'error_title'         => 'Erreur',
        'warning_title'       => 'Attention',
        'success_title'       => 'Succès',
        'cancel_btn'          => 'Annuler',
        'confirm_delete_title'=> 'Supprimer cette image ?',
        'confirm_delete_text' => 'Cette action est irréversible.',
    ],

    'errors' => [
        'validation'     => 'Les données envoyées ne sont pas valides.',
        'upload_generic' => 'Certaines images n’ont pas pu être téléchargées.',
        'update_caption' => 'La légende n’a pas pu être mise à jour.',
        'delete'         => 'L’image n’a pas pu être supprimée.',
        'reorder'        => 'L’ordre n’a pas pu être enregistré.',
        'set_cover'      => 'La couverture n’a pas pu être définie.',
        'load_list'      => 'La liste n’a pas pu être chargée.',
        'too_large'      => 'Le fichier dépasse la taille maximale autorisée. Veuillez essayer avec une image plus petite.',
    ],
],

];
