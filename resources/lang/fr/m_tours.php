<?php

/*************************************************************
 *  MODULE DE TRADUCTIONS : TOURS
 *  Fichier : resources/lang/fr/m_tours.php
 *
 *  Index (section et ligne de début)
 *  [01] COMMON           -> ligne 23
 *  [02] AMENITY          -> ligne 31
 *  [03] SCHEDULE         -> ligne 106
 *  [04] ITINERARY_ITEM   -> ligne 218
 *  [05] ITINERARY        -> ligne 288
 *  [06] LANGUAGE         -> ligne 364
 *  [07] TOUR             -> ligne 453
 *  [08] IMAGES           -> ligne 579
 *************************************************************/

return [

    // =========================================================
    // [01] COMMON
    // =========================================================
    'common' => [
        'success_title' => 'Succès',
        'error_title'   => 'Erreur',
        'people' => 'personnes',
        'hours'  => 'heures',
        'success' => 'Succès',
        'error'   => 'Erreur',
        'cancel'  => 'Annuler',
        'confirm_delete' => 'Oui, supprimer',
        'unspecified'    => 'Non spécifié',
        'no_description' => 'Sans description',
        'required_fields_title' => 'Champs requis',
        'required_fields_text'  => 'Veuillez compléter les champs obligatoires : Nom et Capacité maximale',
        'active'   => 'Actif',
        'inactive' => 'Inactif',
        'notice'   => 'Avis',
        'na'       => 'Non configuré',
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
            'page_title'    => 'Équipements',
            'page_heading'  => 'Gestion des équipements',
            'list_title'    => 'Liste des équipements',

            'add'            => 'Ajouter un équipement',
            'create_title'   => 'Enregistrer un équipement',
            'edit_title'     => 'Modifier l’équipement',
            'save'           => 'Enregistrer',
            'update'         => 'Mettre à jour',
            'cancel'         => 'Annuler',
            'close'          => 'Fermer',
            'state'          => 'État',
            'actions'        => 'Actions',
            'delete_forever' => 'Supprimer définitivement',

            'processing' => 'Traitement…',
            'applying'   => 'Application…',
            'deleting'   => 'Suppression…',

            'toggle_on'  => 'Activer l’équipement',
            'toggle_off' => 'Désactiver l’équipement',

            'toggle_confirm_on_title'  => 'Activer l’équipement ?',
            'toggle_confirm_off_title' => 'Désactiver l’équipement ?',
            'toggle_confirm_on_html'   => 'L’équipement <b>:label</b> sera actif.',
            'toggle_confirm_off_html'  => 'L’équipement <b>:label</b> sera inactif.',

            'delete_confirm_title' => 'Supprimer définitivement ?',
            'delete_confirm_html'  => 'L’élément <b>:label</b> sera supprimé et cette action est irréversible.',

            'yes_continue' => 'Oui, continuer',
            'yes_delete'   => 'Oui, supprimer',

            'item_this' => 'cet équipement',
        ],

        'success' => [
            'created'     => 'Équipement créé avec succès.',
            'updated'     => 'Équipement mis à jour avec succès.',
            'activated'   => 'Équipement activé avec succès.',
            'deactivated' => 'Équipement désactivé avec succès.',
            'deleted'     => 'Équipement supprimé définitivement.',
        ],

        'error' => [
            'create' => 'Impossible de créer l’équipement.',
            'update' => 'Impossible de mettre à jour l’équipement.',
            'toggle' => 'Impossible de changer l’état de l’équipement.',
            'delete' => 'Impossible de supprimer l’équipement.',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Nom invalide',
                'required' => 'Le :attribute est obligatoire.',
                'string'   => 'Le :attribute doit être une chaîne de caractères.',
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
            'page_title'   => 'Horaires des circuits',
            'page_heading' => 'Gestion des horaires',

            'general_title'     => 'Horaires généraux',
            'new_schedule'      => 'Nouvel horaire',
            'new_general_title' => 'Nouvel horaire général',
            'new'               => 'Nouveau',
            'edit_schedule'     => 'Modifier l’horaire',
            'edit_global'       => 'Modifier (global)',

            'assign_existing'    => 'Assigner un existant',
            'assign_to_tour'     => 'Assigner un horaire à « :tour »',
            'select_schedule'    => 'Sélectionnez un horaire',
            'choose'             => 'Choisir',
            'assign'             => 'Assigner',
            'new_for_tour_title' => 'Nouvel horaire pour « :tour »',

            'time_range'        => 'Plage horaire',
            'state'             => 'État',
            'actions'           => 'Actions',
            'schedule_state'    => 'Horaire',
            'assignment_state'  => 'Affectation',
            'no_general'        => 'Aucun horaire général.',
            'no_tour_schedules' => 'Ce circuit n’a pas encore d’horaires.',
            'no_label'          => 'Sans libellé',
            'assigned_count'    => 'horaire(s) assigné(s)',

            'toggle_global_title'     => 'Activer/Désactiver (global)',
            'toggle_global_on_title'  => 'Activer l’horaire (global) ?',
            'toggle_global_off_title' => 'Désactiver l’horaire (global) ?',
            'toggle_global_on_html'   => '<b>:label</b> sera activé pour tous les circuits.',
            'toggle_global_off_html'  => '<b>:label</b> sera désactivé pour tous les circuits.',

            'toggle_on_tour'          => 'Activer sur ce circuit',
            'toggle_off_tour'         => 'Désactiver sur ce circuit',
            'toggle_assign_on_title'  => 'Activer sur ce circuit ?',
            'toggle_assign_off_title' => 'Désactiver sur ce circuit ?',
            'toggle_assign_on_html'   => 'L’affectation sera <b>active</b> pour <b>:tour</b>.',
            'toggle_assign_off_html'  => 'L’affectation sera <b>inactive</b> pour <b>:tour</b>.',

            'detach_from_tour'     => 'Retirer du circuit',
            'detach_confirm_title' => 'Retirer du circuit ?',
            'detach_confirm_html'  => 'L’horaire sera <b>désassigné</b> de <b>:tour</b>.',

            'delete_forever'       => 'Supprimer (global)',
            'delete_confirm_title' => 'Supprimer définitivement ?',
            'delete_confirm_html'  => '<b>:label</b> (global) sera supprimé et cette action est irréversible.',

            'yes_continue' => 'Oui, continuer',
            'yes_delete'   => 'Oui, supprimer',
            'yes_detach'   => 'Oui, retirer',

            'this_schedule' => 'cet horaire',
            'this_tour'     => 'ce circuit',

            'processing'     => 'Traitement…',
            'applying'       => 'Application…',
            'deleting'       => 'Suppression…',
            'removing'       => 'Retrait…',
            'saving_changes' => 'Enregistrement des modifications…',
            'save'           => 'Enregistrer',
            'save_changes'   => 'Enregistrer les modifications',
            'cancel'         => 'Annuler',

            'missing_fields_title' => 'Données manquantes',
            'missing_fields_text'  => 'Vérifiez les champs requis (début, fin et capacité).',
            'could_not_save'       => 'Enregistrement impossible',
        ],

        'success' => [
            'created'                => 'Horaire créé avec succès.',
            'updated'                => 'Horaire mis à jour avec succès.',
            'activated_global'       => 'Horaire activé avec succès (global).',
            'deactivated_global'     => 'Horaire désactivé avec succès (global).',
            'attached'               => 'Horaire assigné au circuit.',
            'detached'               => 'Horaire retiré du circuit avec succès.',
            'assignment_activated'   => 'Affectation activée pour ce circuit.',
            'assignment_deactivated' => 'Affectation désactivée pour ce circuit.',
            'deleted'                => 'Horaire supprimé avec succès.',
        ],

        'error' => [
            'create'               => 'Un problème est survenu lors de la création de l’horaire.',
            'update'               => 'Un problème est survenu lors de la mise à jour de l’horaire.',
            'toggle'               => 'Impossible de changer l’état global de l’horaire.',
            'attach'               => 'Impossible d’assigner l’horaire au circuit.',
            'detach'               => 'Impossible de désassigner l’horaire du circuit.',
            'assignment_toggle'    => 'Impossible de changer l’état de l’affectation.',
            'not_assigned_to_tour' => 'L’horaire n’est pas assigné à ce circuit.',
            'delete'               => 'Un problème est survenu lors de la suppression de l’horaire.',
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
            'register_item' => 'Enregistrer un élément',
            'edit_item'     => 'Modifier l’élément',
            'save'          => 'Enregistrer',
            'update'        => 'Mettre à jour',
            'cancel'        => 'Annuler',
            'state'         => 'État',
            'actions'       => 'Actions',
            'see_more'      => 'Voir plus',
            'see_less'      => 'Voir moins',

            'toggle_on'  => 'Activer l’élément',
            'toggle_off' => 'Désactiver l’élément',

            'delete_forever'       => 'Supprimer définitivement',
            'delete_confirm_title' => 'Supprimer définitivement ?',
            'delete_confirm_html'  => '<b>:label</b> sera supprimé et cette action est irréversible.',
            'yes_delete'           => 'Oui, supprimer',
            'item_this'            => 'cet élément',

            'processing' => 'Traitement…',
            'applying'   => 'Application…',
            'deleting'   => 'Suppression…',
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
            'toggle' => 'Impossible de changer l’état de l’élément.',
            'delete' => 'Impossible de supprimer l’élément.',
        ],

        'validation' => [
            'title' => [
                'required' => 'Le :attribute est obligatoire.',
                'string'   => 'Le :attribute doit être une chaîne de caractères.',
                'max'      => 'Le :attribute ne peut pas dépasser :max caractères.',
            ],
            'description' => [
                'required' => 'La :attribute est obligatoire.',
                'string'   => 'La :attribute doit être une chaîne de caractères.',
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
            'page_title'    => 'Itinéraires et éléments',
            'page_heading'  => 'Itinéraires et gestion des éléments',
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
            'drag_hint'             => 'Glisser-déposer les éléments pour définir l’ordre.',
            'drag_handle'           => 'Faire glisser pour réordonner',
            'select_one_title'      => 'Vous devez sélectionner au moins un élément',
            'select_one_text'       => 'Veuillez sélectionner au moins un élément pour continuer.',
            'assign_confirm_title'  => 'Assigner les éléments sélectionnés ?',
            'assign_confirm_button' => 'Oui, assigner',
            'assigning'             => 'Affectation…',

            'no_items_assigned'       => 'Aucun élément n’est assigné à cet itinéraire.',
            'itinerary_this'          => 'cet itinéraire',
            'processing'              => 'Traitement…',
            'saving'                  => 'Enregistrement…',
            'activating'              => 'Activation…',
            'deactivating'            => 'Désactivation…',
            'applying'                => 'Application…',
            'deleting'                => 'Suppression…',
            'flash_success_title'     => 'Succès',
            'flash_error_title'       => 'Erreur',
            'validation_failed_title' => 'Traitement impossible',
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
            'toggle'  => 'Impossible de changer l’état de l’itinéraire.',
            'delete'  => 'Impossible de supprimer l’itinéraire.',
            'assign'  => 'Impossible d’assigner les éléments.',
        ],

        'validation' => [
            'name' => [
                'required' => 'Le nom de l’itinéraire est obligatoire.',
                'string'   => 'Le nom doit être du texte.',
                'max'      => 'Le nom ne peut pas dépasser 255 caractères.',
                'unique'   => 'Un itinéraire avec ce nom existe déjà.',
            ],
            'description' => [
                'string' => 'La description doit être du texte.',
                'max'    => 'La description ne peut pas dépasser 1000 caractères.',
            ],
            'items' => [
                'required'      => 'Vous devez sélectionner au moins un élément.',
                'array'         => 'Le format des éléments est invalide.',
                'min'           => 'Vous devez sélectionner au moins un élément.',
                'order_integer' => 'L’ordre doit être un nombre entier.',
                'order_min'     => 'L’ordre ne peut pas être négatif.',
                'order_max'     => 'L’ordre ne peut pas dépasser 9999.',
            ],
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
            'page_title'   => 'Langues des circuits',
            'page_heading' => 'Gestion des langues',
            'list_title'   => 'Liste des langues',

            'table' => [
                'id'      => 'ID',
                'name'    => 'Langue',
                'state'   => 'État',
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

            'processing'   => 'Traitement…',
            'saving'       => 'Enregistrement…',
            'activating'   => 'Activation…',
            'deactivating' => 'Désactivation…',
            'deleting'     => 'Suppression…',

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
            'toggle' => 'Impossible de changer l’état de la langue.',
            'delete' => 'Impossible de supprimer la langue.',
            'save'   => 'Enregistrement impossible',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Nom invalide',
                'required' => 'Le nom de la langue est obligatoire.',
                'string'   => 'Le :attribute doit être une chaîne de caractères.',
                'max'      => 'Le :attribute ne peut pas dépasser :max caractères.',
                'unique'   => 'Une langue avec ce nom existe déjà.',
            ],
        ],
    ],

    // =========================================================
    // [07] TOUR
    // =========================================================
    'tour' => [
        'title' => 'Circuits',

        'fields' => [
            'id'            => 'ID',
            'name'          => 'Nom',
            'details'       => 'Détails',
            'price'         => 'Prix',
            'overview'      => 'Résumé',
            'amenities'     => 'Équipements',
            'exclusions'    => 'Exclusions',
            'itinerary'     => 'Itinéraire',
            'languages'     => 'Langues',
            'schedules'     => 'Horaires',
            'adult_price'   => 'Prix adulte',
            'kid_price'     => 'Prix enfant',
            'length_hours'  => 'Durée (heures)',
            'max_capacity'  => 'Capacité max.',
            'type'          => 'Type de circuit',
            'viator_code'   => 'Code Viator',
            'status'        => 'État',
            'actions'       => 'Actions',
            'group_size'    => 'Taille du groupe',
        ],

        'pricing' => [
            'note_title'              => 'Note :',
            'note_text'               => 'Définissez ici les prix de base pour chaque catégorie de client.',
            'manage_detailed_hint'    => ' Pour une gestion détaillée, utilisez le bouton « Gérer les prix détaillés » ci-dessus.',
            'price_usd'               => 'Prix (USD)',
            'min_quantity'            => 'Quantité min.',
            'max_quantity'            => 'Quantité max.',
            'status'                  => 'État',
            'active'                  => 'Actif',
            'no_categories'           => 'Aucune catégorie de clients configurée.',
            'create_categories_first' => 'Créer d’abord des catégories',
        ],

        'schedules_form' => [
            'available_title'        => 'Horaires disponibles',
            'select_hint'            => 'Sélectionnez les horaires pour ce circuit',
            'no_schedules'           => 'Aucun horaire disponible.',
            'create_schedules_link'  => 'Créer des horaires',

            'create_new_title'       => 'Créer un nouvel horaire',
            'label_placeholder'      => 'Ex : Matin, Après-midi',
            'create_and_assign'      => 'Créer cet horaire et l’assigner au circuit',

            'info_title'             => 'Information',
            'schedules_title'        => 'Horaires',
            'schedules_text'         => 'Sélectionnez un ou plusieurs horaires pendant lesquels ce circuit sera disponible.',
            'create_block_title'     => 'Créer nouveau',
            'create_block_text'      => 'Si vous avez besoin d’un horaire inexistant, vous pouvez le créer ici en cochant « Créer cet horaire et l’assigner au circuit ».',

            'current_title'          => 'Horaires actuels',
            'none_assigned'          => 'Aucun horaire assigné',
        ],

        'summary' => [
            'preview_title'        => 'Aperçu du circuit',
            'preview_text_create'  => 'Vérifiez toutes les informations avant de créer le circuit.',
            'preview_text_update'  => 'Vérifiez toutes les informations avant de mettre à jour le circuit.',

            'basic_details_title'  => 'Détails de base',
            'description_title'    => 'Description',
            'prices_title'         => 'Prix par catégorie',
            'schedules_title'      => 'Horaires',
            'languages_title'      => 'Langues',
            'itinerary_title'      => 'Itinéraire',

            'table' => [
                'category' => 'Catégorie',
                'price'    => 'Prix',
                'min_max'  => 'Min-Max',
            ],

            'not_specified'        => 'Non spécifié',
            'slug_autogenerated'   => 'Sera généré automatiquement',
            'no_description'       => 'Sans description',
            'no_active_prices'     => 'Aucun prix actif configuré',
            'no_languages'         => 'Aucune langue assignée',
            'none_included'        => 'Aucun élément inclus indiqué',
            'none_excluded'        => 'Aucun élément exclu indiqué',

            'units' => [
                'hours'  => 'heures',
                'people' => 'personnes',
            ],

            'create_note' => 'Les horaires, prix, langues et équipements apparaîtront ici après l’enregistrement du circuit.',
        ],

        'alerts' => [
            'delete_title' => 'Supprimer le circuit ?',
            'delete_text'  => 'Le circuit sera déplacé vers Supprimés. Vous pourrez le restaurer plus tard.',
            'purge_title'  => 'Supprimer définitivement ?',
            'purge_text'   => 'Cette action est irréversible.',
            'purge_text_with_bookings' => 'Ce circuit a :count réservation(s). Elles ne seront pas supprimées ; elles resteront sans circuit associé.',
            'toggle_question_active'   => 'Désactiver le circuit ?',
            'toggle_question_inactive' => 'Activer le circuit ?',
        ],

        'flash' => [
            'created'       => 'Circuit créé avec succès.',
            'updated'       => 'Circuit mis à jour avec succès.',
            'deleted_soft'  => 'Circuit déplacé vers Supprimés.',
            'restored'      => 'Circuit restauré avec succès.',
            'purged'        => 'Circuit supprimé définitivement.',
            'toggled_on'    => 'Circuit activé.',
            'toggled_off'   => 'Circuit désactivé.',
        ],

        'table' => [
            'id'            => 'ID',
            'name'          => 'Nom',
            'overview'      => 'Résumé',
            'amenities'     => 'Équipements',
            'exclusions'    => 'Exclusions',
            'itinerary'     => 'Itinéraire',
            'languages'     => 'Langues',
            'schedules'     => 'Horaires',
            'adult_price'   => 'Prix adulte',
            'kid_price'     => 'Prix enfant',
            'length_hours'  => 'Durée (h)',
            'max_capacity'  => 'Capacité max.',
            'type'          => 'Type',
            'viator_code'   => 'Code Viator',
            'status'        => 'État',
            'actions'       => 'Actions',
            'slug'          => 'URL',
            'prices'        => 'Prix',
            'capacity'      => 'Capacité',
            'group_size'    => 'Groupe max.',
        ],

        'status' => [
            'active'   => 'Actif',
            'inactive' => 'Inactif',
            'archived' => 'Archivé',
        ],

        'placeholders' => [
            'group_size' => 'Ex : 10',
        ],

        'hints' => [
            'group_size' => 'Capacité/groupe recommandée pour ce circuit.',
        ],

        'success' => [
            'created'     => 'Le circuit a été créé avec succès.',
            'updated'     => 'Le circuit a été mis à jour avec succès.',
            'deleted'     => 'Le circuit a été supprimé.',
            'toggled'     => 'L’état du circuit a été mis à jour.',
            'activated'   => 'Circuit activé avec succès.',
            'deactivated' => 'Circuit désactivé avec succès.',
            'archived'    => 'Circuit archivé avec succès.',
            'restored'    => 'Circuit restauré avec succès.',
            'purged'      => 'Circuit supprimé définitivement.',
        ],

        'error' => [
            'create'    => 'Impossible de créer le circuit.',
            'update'    => 'Impossible de mettre à jour le circuit.',
            'delete'    => 'Impossible de supprimer le circuit.',
            'toggle'    => 'Impossible de changer l’état du circuit.',
            'not_found' => 'Le circuit n’existe pas.',
            'restore'            => 'Impossible de restaurer le circuit.',
            'purge'              => 'Impossible de supprimer définitivement le circuit.',
            'purge_has_bookings' => 'Suppression définitive impossible : le circuit a des réservations.',
        ],

        'ui' => [
            'page_title'       => 'Gestion des circuits',
            'page_heading'     => 'Gestion des circuits',
            'create_title'     => 'Enregistrer un circuit',
            'edit_title'       => 'Modifier le circuit',
            'delete_title'     => 'Supprimer le circuit',
            'cancel'           => 'Annuler',
            'save'             => 'Enregistrer',
            'save_changes'     => 'Enregistrer les modifications',
            'update'           => 'Mettre à jour',
            'delete_confirm'   => 'Supprimer ce circuit ?',
            'toggle_on'        => 'Activer',
            'toggle_off'       => 'Désactiver',
            'toggle_on_title'  => 'Activer le circuit ?',
            'toggle_off_title' => 'Désactiver le circuit ?',
            'toggle_on_button'  => 'Oui, activer',
            'toggle_off_button' => 'Oui, désactiver',
            'see_more'         => 'Voir plus',
            'see_less'         => 'Masquer',
            'load_more'        => 'Charger plus',
            'loading'          => 'Chargement…',
            'load_more_error'  => 'Impossible de charger plus de circuits.',
            'confirm_title'    => 'Confirmation',
            'confirm_text'     => 'Voulez-vous confirmer cette action ?',
            'yes_confirm'      => 'Oui, confirmer',
            'no_confirm'       => 'Non, annuler',
            'add_tour'         => 'Ajouter un circuit',
            'edit_tour'        => 'Modifier le circuit',
            'delete_tour'      => 'Supprimer le circuit',
            'toggle_tour'      => 'Activer/Désactiver le circuit',
            'view_cart'        => 'Voir le panier',
            'add_to_cart'      => 'Ajouter au panier',
            'slug_help'        => 'Identifiant du circuit dans l’URL (sans espaces ni accents)',
            'generate_auto'       => 'Générer automatiquement',
            'slug_preview_label'  => 'Aperçu',
            'saved'               => 'Enregistré',

            'available_languages'    => 'Langues disponibles',
            'default_capacity'       => 'Capacité par défaut',
            'create_new_schedules'   => 'Créer de nouveaux horaires',
            'multiple_hint_ctrl_cmd' => 'Maintenez CTRL/CMD pour sélectionner plusieurs éléments',
            'use_existing_schedules' => 'Utiliser des horaires existants',
            'add_schedule'           => 'Ajouter un horaire',
            'schedules_title'        => 'Horaires du circuit',
            'amenities_included'     => 'Équipements inclus',
            'amenities_excluded'     => 'Équipements non inclus',
            'color'                  => 'Couleur du circuit',
            'remove'                 => 'Supprimer',
            'choose_itinerary'       => 'Choisir un itinéraire',
            'select_type'            => 'Sélectionner le type',
            'empty_means_default'    => 'Par défaut',
            'actives'                 => 'Actifs',
            'inactives'               => 'Inactifs',
            'archived'                => 'Archivé',
            'all'                     => 'Tous',
            'help_title'              => 'Aide',
            'amenities_included_hint' => 'Sélectionnez ce qui est inclus dans le circuit.',
            'amenities_excluded_hint' => 'Sélectionnez ce qui n’est PAS inclus dans le circuit.',
            'help_included_title'     => 'Inclus',
            'help_included_text'      => 'Cochez tout ce qui est inclus dans le prix du circuit (transport, repas, entrées, équipement, guide, etc.).',
            'help_excluded_title'     => 'Non inclus',
            'help_excluded_text'      => 'Indiquez ce que le client doit payer séparément ou apporter (pourboires, boissons alcoolisées, souvenirs, etc.).',
            'select_or_create_title' => 'Sélectionner ou créer un itinéraire',
            'select_existing_items'  => 'Sélectionner des éléments existants',
            'name_hint'              => 'Nom identifiant de cet itinéraire',
            'click_add_item_hint'    => 'Cliquez sur « Ajouter un élément » pour créer de nouveaux éléments',
            'scroll_hint'            => 'Faites défiler horizontalement pour voir plus de colonnes',
            'no_schedules'           => 'Sans horaires',
            'no_prices'              => 'Aucun prix configuré',
            'edit'                   => 'Modifier',
            'slug_auto'              => 'Sera généré automatiquement',
            'added_to_cart'          => 'Ajouté au panier',
            'added_to_cart_text'     => 'Le circuit a été ajouté au panier avec succès.',
            'none' => [
                'amenities'       => 'Aucun équipement',
                'exclusions'      => 'Aucune exclusion',
                'itinerary'       => 'Aucun itinéraire',
                'itinerary_items' => 'Aucun élément',
                'languages'       => 'Aucune langue',
                'schedules'       => 'Aucun horaire',
            ],

            // Archivage / restauration / purge
            'archive' => 'Archiver',
            'restore' => 'Restaurer',
            'purge'   => 'Supprimer définitivement',

            'confirm_archive_title' => 'Archiver le circuit ?',
            'confirm_archive_text'  => 'Le circuit sera indisponible pour de nouvelles réservations, mais les réservations existantes sont conservées.',
            'confirm_purge_title'   => 'Supprimer définitivement',
            'confirm_purge_text'    => 'Action irréversible autorisée uniquement si le circuit n’a jamais eu de réservations.',

            // Filtres d’état
            'filters' => [
                'active'   => 'Actifs',
                'inactive' => 'Inactifs',
                'archived' => 'Archivés',
                'all'      => 'Tous',
            ],

            // Barre de taille de police (tourlist.blade.php)
            'font_decrease_title' => 'Diminuer la taille de police',
            'font_increase_title' => 'Augmenter la taille de police',
        ],
    ],

    // =========================================================
    // [08] IMAGES
    // =========================================================
    'image' => [

        'limit_reached_title' => 'Limite atteinte',
        'limit_reached_text'  => 'La limite d’images pour ce circuit a été atteinte.',
        'upload_success'      => 'Images téléversées avec succès.',
        'upload_none'         => 'Aucune image téléversée.',
        'upload_truncated'    => 'Certains fichiers ont été ignorés en raison de la limite par circuit.',
        'done'                => 'Terminé',
        'notice'              => 'Avis',
        'saved'               => 'Enregistrer',
        'caption_updated'     => 'Légende mise à jour avec succès.',
        'deleted'             => 'Supprimé',
        'image_removed'       => 'Image supprimée avec succès.',
        'invalid_order'       => 'Chargement d’ordre invalide.',
        'nothing_to_reorder'  => 'Rien à réorganiser.',
        'order_saved'         => 'Ordre enregistré.',
        'cover_updated_title' => 'Mettre à jour la couverture',
        'cover_updated_text'  => 'Cette image est désormais la couverture.',
        'deleting'            => 'Suppression…',

        'ui' => [
            'page_title_pick'     => 'Images des circuits',
            'page_heading'        => 'Images des circuits',
            'choose_tour'         => 'Choisir un circuit',
            'search_placeholder'  => 'Rechercher par ID ou nom…',
            'search_button'       => 'Rechercher',
            'no_results'          => 'Aucun circuit trouvé.',
            'manage_images'       => 'Gérer les images',
            'cover_alt'           => 'Couverture',
            'images_label'        => 'images',
            'upload_btn'          => 'Téléverser',
            'caption_placeholder' => 'Légende (optionnelle)',
            'set_cover_btn'       => 'Choisissez l’image à utiliser comme couverture',
            'no_images'           => 'Aucune image pour ce circuit pour le moment.',
            'delete_btn'          => 'Supprimer',
            'show_btn'            => 'Afficher',
            'close_btn'           => 'Fermer',
            'preview_title'       => 'Aperçu de l’image',

            'error_title'         => 'Erreur',
            'warning_title'       => 'Attention',
            'success_title'       => 'Succès',
            'cancel_btn'          => 'Annuler',
            'confirm_delete_title' => 'Supprimer cette image ?',
            'confirm_delete_text'  => 'Cette action est irréversible.',
            'cover_current_title'      => 'Couverture actuelle',
            'upload_new_cover_title'   => 'Téléverser une nouvelle couverture',
            'cover_file_label'         => 'Fichier de couverture',
            'file_help_cover'          => 'JPEG/PNG/WebP, 30 Mo max.',
            'id_label'                 => 'ID',
        ],

        'errors' => [
            'validation'     => 'Les données envoyées ne sont pas valides.',
            'upload_generic' => 'Impossible de téléverser certaines images.',
            'update_caption' => 'Impossible de mettre à jour la légende.',
            'delete'         => 'Impossible de supprimer l’image.',
            'reorder'        => 'Impossible d’enregistrer l’ordre.',
            'set_cover'      => 'Impossible de définir la couverture.',
            'load_list'      => 'Impossible de charger la liste.',
            'too_large'      => 'Le fichier dépasse la taille maximale autorisée. Essayez une image plus légère.',
        ],
    ],
    'prices' => [
        'ui' => [
            'page_title'         => 'Prix - :name',
            'header_title'       => 'Prix : :name',
            'back_to_tours'      => 'Retour aux circuits',

            'configured_title'   => 'Catégories et prix configurés',
            'empty_title'        => 'Aucune catégorie configurée pour ce circuit.',
            'empty_hint'         => 'Utilisez le formulaire à droite pour ajouter des catégories.',

            'save_changes'       => 'Enregistrer les modifications',
            'auto_disable_note'  => 'Les prix à 0 $ sont automatiquement désactivés',

            'add_category'       => 'Ajouter une catégorie',

            'all_assigned_title' => 'Toutes les catégories sont assignées',
            'all_assigned_text'  => 'Aucune autre catégorie disponible pour ce circuit.',

            'info_title'         => 'Informations',
            'tour_label'         => 'Circuit',
            'configured_count'   => 'Catégories configurées',
            'active_count'       => 'Catégories actives',

            'fields_title'       => 'Champs',
            'rules_title'        => 'Règles',

            'field_price'        => 'Prix',
            'field_min'          => 'Min',
            'field_max'          => 'Max',
            'field_status'       => 'État',

            'rule_min_le_max'    => 'Le minimum doit être inférieur ou égal au maximum',
            'rule_zero_disable'  => 'Les prix à 0 $ sont automatiquement désactivés',
            'rule_only_active'   => 'Seules les catégories actives apparaissent sur le site public',
        ],

        'table' => [
            'category'   => 'Catégorie',
            'age_range'  => 'Tranche d’âge',
            'price_usd'  => 'Prix (USD)',
            'min'        => 'Min',
            'max'        => 'Max',
            'status'     => 'État',
            'action'     => 'Action',
            'active'     => 'Active',
            'inactive'   => 'Inactive',
        ],

        'forms' => [
            'select_placeholder'  => '-- Sélectionner --',
            'category'            => 'Catégorie',
            'price_usd'           => 'Prix (USD)',
            'min'                 => 'Minimum',
            'max'                 => 'Maximum',
            'create_disabled_hint'=> 'Si le prix est de 0 $, la catégorie sera créée désactivée',
            'add'                 => 'Ajouter',
        ],

        'modal' => [
            'delete_title'   => 'Supprimer la catégorie',
            'delete_text'    => 'Supprimer cette catégorie de ce circuit ?',
            'cancel'         => 'Annuler',
            'delete'         => 'Supprimer',
            'delete_tooltip' => 'Supprimer la catégorie',
        ],

        'flash' => [
            'success' => 'Opération réalisée avec succès.',
            'error'   => 'Une erreur est survenue.',
        ],

        'js' => [
            'max_ge_min'            => 'Le maximum doit être supérieur ou égal au minimum',
            'auto_disabled_tooltip' => 'Prix à 0 $ – désactivé automatiquement',
            'fix_errors'            => 'Veuillez corriger les quantités minimales et maximales',
        ],
    ],

];
