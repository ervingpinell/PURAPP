<?php

/*************************************************************
 *  MODULE DE TRADUCTION : TOURS
 *  Fichier : resources/lang/fr/m_tours.php
 *
 *  Sommaire (section & ligne de début)
 *  [01] COMMON           -> ligne 19
 *  [02] AMENITY          -> ligne 27
 *  [03] SCHEDULE         -> ligne 90
 *  [04] ITINERARY_ITEM   -> ligne 176
 *  [05] ITINERARY        -> ligne 239
 *  [06] LANGUAGE         -> ligne 302
 *  [07] TOUR             -> ligne 386
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
            'page_title'    => 'Équipements',
            'page_heading'  => 'Gestion des équipements',
            'list_title'    => 'Liste des équipements',

            'add'            => 'Ajouter un équipement',
            'create_title'   => 'Créer un équipement',
            'edit_title'     => 'Modifier un équipement',
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

            'toggle_on'  => 'Activer l’équipement',
            'toggle_off' => 'Désactiver l’équipement',

            'toggle_confirm_on_title'  => 'Activer l’équipement ?',
            'toggle_confirm_off_title' => 'Désactiver l’équipement ?',
            'toggle_confirm_on_html'   => 'L’équipement <b>:label</b> sera activé.',
            'toggle_confirm_off_html'  => 'L’équipement <b>:label</b> sera désactivé.',

            'delete_confirm_title' => 'Supprimer définitivement ?',
            'delete_confirm_html'  => '<b>:label</b> sera supprimé et l’action est irréversible.',

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
            'toggle' => 'Impossible de changer le statut de l’équipement.',
            'delete' => 'Impossible de supprimer l’équipement.',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Nom invalide',
                'required' => 'Le champ :attribute est obligatoire.',
                'string'   => 'Le champ :attribute doit être une chaîne de caractères.',
                'max'      => 'Le champ :attribute ne peut pas dépasser :max caractères.',
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
            'label'          => 'Étiquette',
            'label_optional' => 'Étiquette (optionnel)',
            'max_capacity'   => 'Capacité max.',
            'active'         => 'Actif',
        ],

        'status' => [
            'active'   => 'Actif',
            'inactive' => 'Inactif',
        ],

        'ui' => [
            'page_title'        => 'Horaires des circuits',
            'page_heading'      => 'Gestion des horaires',

            'general_title'     => 'Horaires généraux',
            'new_schedule'      => 'Nouvel horaire',
            'new_general_title' => 'Nouvel horaire général',
            'new'               => 'Nouveau',
            'edit_schedule'     => 'Modifier l’horaire',
            'edit_global'       => 'Modifier (global)',

            'assign_existing'    => 'Affecter un existant',
            'assign_to_tour'     => 'Affecter un horaire à « :tour »',
            'select_schedule'    => 'Sélectionnez un horaire',
            'choose'             => 'Choisir',
            'assign'             => 'Affecter',
            'new_for_tour_title' => 'Nouvel horaire pour « :tour »',

            'time_range'        => 'Plage horaire',
            'state'             => 'Statut',
            'actions'           => 'Actions',
            'schedule_state'    => 'Horaire',
            'assignment_state'  => 'Affectation',
            'no_general'        => 'Aucun horaire général.',
            'no_tour_schedules' => 'Ce circuit n’a pas encore d’horaires.',
            'no_label'          => 'Sans étiquette',
            'assigned_count'    => 'horaire(s) affecté(s)',

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
            'detach_confirm_html'  => 'L’horaire sera <b>retiré</b> de <b>:tour</b>.',

            'delete_forever'       => 'Supprimer (global)',
            'delete_confirm_title' => 'Supprimer définitivement ?',
            'delete_confirm_html'  => '<b>:label</b> sera supprimé (global) et cela est irréversible.',

            'yes_continue' => 'Oui, continuer',
            'yes_delete'   => 'Oui, supprimer',
            'yes_detach'   => 'Oui, retirer',

            'this_schedule' => 'cet horaire',
            'this_tour'     => 'ce circuit',

            'processing'     => 'Traitement...',
            'applying'       => 'Application...',
            'deleting'       => 'Suppression...',
            'removing'       => 'Retrait...',
            'saving_changes' => 'Enregistrement des modifications...',
            'save'           => 'Enregistrer',
            'save_changes'   => 'Enregistrer les modifications',
            'cancel'         => 'Annuler',

            'missing_fields_title' => 'Données manquantes',
            'missing_fields_text'  => 'Veuillez vérifier les champs requis (début, fin et capacité).',
            'could_not_save'       => 'Enregistrement impossible',
        ],

        'success' => [
            'created'                => 'Horaire créé avec succès.',
            'updated'                => 'Horaire mis à jour avec succès.',
            'activated_global'       => 'Horaire activé (global) avec succès.',
            'deactivated_global'     => 'Horaire désactivé (global) avec succès.',
            'attached'               => 'Horaire affecté au circuit.',
            'detached'               => 'Horaire retiré du circuit.',
            'assignment_activated'   => 'Affectation activée pour ce circuit.',
            'assignment_deactivated' => 'Affectation désactivée pour ce circuit.',
            'deleted'                => 'Horaire supprimé avec succès.',
        ],

        'error' => [
            'create'               => 'Problème lors de la création de l’horaire.',
            'update'               => 'Problème lors de la mise à jour de l’horaire.',
            'toggle'               => 'Impossible de changer le statut global de l’horaire.',
            'attach'               => 'Impossible d’affecter l’horaire au circuit.',
            'detach'               => 'Impossible de retirer l’horaire du circuit.',
            'assignment_toggle'    => 'Impossible de changer le statut de l’affectation.',
            'not_assigned_to_tour' => 'L’horaire n’est pas affecté à ce circuit.',
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
            'register_item' => 'Enregistrer un élément',
            'edit_item'     => 'Modifier un élément',
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
            'delete_confirm_html'  => '<b>:label</b> sera supprimé et l’action est irréversible.',
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
                'required' => 'Le champ :attribute est obligatoire.',
                'string'   => 'Le champ :attribute doit être une chaîne de caractères.',
                'max'      => 'Le champ :attribute ne peut pas dépasser :max caractères.',
            ],
            'description' => [
                'required' => 'Le champ :attribute est obligatoire.',
                'string'   => 'Le champ :attribute doit être une chaîne de caractères.',
                'max'      => 'Le champ :attribute ne peut pas dépasser :max caractères.',
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
            'description_optional' => 'Description (optionnel)',
        ],

        'status' => [
            'active'   => 'Actif',
            'inactive' => 'Inactif',
        ],

        'ui' => [
            'page_title'    => 'Itinéraires & Éléments',
            'page_heading'  => 'Gestion des itinéraires et éléments',
            'new_itinerary' => 'Nouvel itinéraire',

            'assign'        => 'Affecter',
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

            'assign_title'          => 'Affecter des éléments à :name',
            'drag_hint'             => 'Glisser-déposer les éléments pour définir l’ordre.',
            'drag_handle'           => 'Glisser pour réorganiser',
            'select_one_title'      => 'Sélectionnez au moins un élément',
            'select_one_text'       => 'Veuillez sélectionner au moins un élément pour continuer.',
            'assign_confirm_title'  => 'Affecter les éléments sélectionnés ?',
            'assign_confirm_button' => 'Oui, affecter',
            'assigning'             => 'Affectation...',

            'no_items_assigned'       => 'Aucun élément n’est affecté à cet itinéraire.',
            'itinerary_this'          => 'cet itinéraire',
            'processing'              => 'Traitement...',
            'saving'                  => 'Enregistrement...',
            'activating'              => 'Activation...',
            'deactivating'            => 'Désactivation...',
            'applying'                => 'Application...',
            'deleting'                => 'Suppression...',
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
            'items_assigned' => 'Éléments affectés avec succès.',
        ],

        'error' => [
            'create'  => 'Impossible de créer l’itinéraire.',
            'update'  => 'Impossible de mettre à jour l’itinéraire.',
            'toggle'  => 'Impossible de changer le statut de l’itinéraire.',
            'delete'  => 'Impossible de supprimer l’itinéraire.',
            'assign'  => 'Impossible d’affecter les éléments.',
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
                'required' => 'Le nom de la langue est obligatoire.',
                'string'   => 'Le champ :attribute doit être une chaîne de caractères.',
                'max'      => 'Le champ :attribute ne peut pas dépasser :max caractères.',
                'unique'   => 'Une langue avec ce nom existe déjà.',
            ],
        ],
    ],

    // =========================================================
    // [07] TOUR
    // =========================================================
    'tour' => [
        'fields' => [
            'id'           => 'ID',
            'name'         => 'Nom',
            'overview'     => 'Aperçu',
            'amenities'    => 'Équipements',
            'exclusions'   => 'Exclusions',
            'itinerary'    => 'Itinéraire',
            'languages'    => 'Langues',
            'schedules'    => 'Horaires',
            'adult_price'  => 'Tarif adulte',
            'kid_price'    => 'Tarif enfant',
            'length_hours' => 'Durée (h)',
            'max_capacity' => 'Capacité max.',
            'type'         => 'Type',
            'viator_code'  => 'Code Viator',
            'status'       => 'Statut',
            'actions'      => 'Actions',
        ],
        'table' => [
            'id'           => 'ID',
            'name'         => 'Nom',
            'overview'     => 'Aperçu',
            'amenities'    => 'Équipements',
            'exclusions'   => 'Exclusions',
            'itinerary'    => 'Itinéraire',
            'languages'    => 'Langues',
            'schedules'    => 'Horaires',
            'adult_price'  => 'Tarif adulte',
            'kid_price'    => 'Tarif enfant',
            'length_hours' => 'Durée (h)',
            'max_capacity' => 'Capacité max.',
            'type'         => 'Type',
            'viator_code'  => 'Code Viator',
            'status'       => 'Statut',
            'actions'      => 'Actions',
        ],
        'status' => [
            'active'   => 'Actif',
            'inactive' => 'Inactif',
        ],
        'ui' => [
            'page_title'   => 'Circuits',
            'page_heading' => 'Gestion des circuits',

            'font_decrease_title' => 'Diminuer la taille de la police',
            'font_increase_title' => 'Augmenter la taille de la police',

            'see_more' => 'Voir plus',
            'see_less' => 'Voir moins',

            'none' => [
                'amenities'       => 'Aucun équipement',
                'exclusions'      => 'Aucune exclusion',
                'languages'       => 'Aucune langue',
                'itinerary'       => 'Aucun itinéraire',
                'itinerary_items' => '(Aucun élément)',
                'schedules'       => 'Aucun horaire',
            ],

            'toggle_on'         => 'Activer',
            'toggle_off'        => 'Désactiver',
            'toggle_on_title'   => 'Voulez-vous activer ce circuit ?',
            'toggle_off_title'  => 'Voulez-vous désactiver ce circuit ?',
            'toggle_on_button'  => 'Oui, activer',
            'toggle_off_button' => 'Oui, désactiver',

            'confirm_title'   => 'Confirmation',
            'confirm_text'    => 'Confirmer l’action ?',
            'yes_confirm'     => 'Oui, confirmer',
            'cancel'          => 'Annuler',

            'load_more'       => 'Charger plus',
            'loading'         => 'Chargement...',
            'load_more_error' => 'Impossible de charger davantage',
        ],
        'success' => [
            'created'     => 'Circuit créé avec succès.',
            'updated'     => 'Circuit mis à jour avec succès.',
            'activated'   => 'Circuit activé avec succès.',
            'deactivated' => 'Circuit désactivé avec succès.',
        ],
        'error' => [
            'create' => 'Un problème est survenu lors de la création du circuit.',
            'update' => 'Un problème est survenu lors de la mise à jour du circuit.',
            'toggle' => 'Un problème est survenu lors du changement de statut du circuit.',
        ],
    ],
    // =========================================================
    // [08] IMAGES
    // =========================================================
    'image' => [
    'limit_reached_title' => 'Limite atteinte',
    'limit_reached_text'  => 'La limite d’images a été atteinte pour ce circuit.',
    'upload_success'      => 'Images importées avec succès.',
    'upload_none'         => 'Aucune image n’a été importée.',
    'upload_truncated'    => 'Certains fichiers ont été ignorés en raison de la limite par circuit.',
    'done'                => 'Terminé',
    'notice'              => 'Avis',
    'saved'               => 'Enregistré',
    'caption_updated'     => 'Légende mise à jour avec succès.',
    'deleted'             => 'Supprimé',
    'image_removed'       => 'Image supprimée avec succès.',
    'invalid_order'       => 'Charge de tri invalide.',
    'nothing_to_reorder'  => 'Rien à réorganiser.',
    'order_saved'         => 'Ordre enregistré.',
    'cover_updated_title' => 'Couverture mise à jour',
    'cover_updated_text'  => 'Cette image est désormais la couverture.',

    'ui' => [
        'page_title_pick'   => 'Images des circuits — Choisir un circuit',
        'page_heading'      => 'Images des circuits',
        'choose_tour'       => 'Choisir un circuit',
        'search_placeholder'=> 'Rechercher par ID ou nom…',
        'search_button'     => 'Rechercher',
        'no_results'        => 'Aucun circuit trouvé.',
        'manage_images'     => 'Gérer les images',
        'cover_alt'         => 'Couverture',
    ],
],

];
