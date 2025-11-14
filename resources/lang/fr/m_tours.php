<?php

/*************************************************************
 *  MODULE DE TRADUCTIONS : TOURS
 *  Fichier : resources/lang/fr/m_tours.php
 *************************************************************/

return [

    // =========================================================
    // [01] COMMON
    // =========================================================
    'common' => [
        'success_title'      => 'Succès',
        'error_title'        => 'Erreur',
        'people'             => 'personnes',
        'hours'              => 'heures',
        'success'            => 'Succès',
        'error'              => 'Erreur',
        'cancel'             => 'Annuler',
        'confirm_delete'     => 'Oui, supprimer',
        'unspecified'        => 'Non spécifié',
        'no_description'     => 'Aucune description',
        'required_fields_title' => 'Champs obligatoires',
        'required_fields_text'  => 'Veuillez compléter les champs obligatoires : Nom et Capacité maximale',
        'active'             => 'Actif',
        'inactive'           => 'Inactif',
        'notice'             => 'Avis',
        'na'                 => 'Non configuré',
        'create'             => 'Créer',
        'info'               => 'Information',
        'close'              => 'Fermer',
        'required'           => 'Ce champ est obligatoire.',
        'add'                => 'Ajouter',
        'translating'        => 'Traduction en cours...',
        'error_translating'  => 'Le texte n’a pas pu être traduit.',
    ],

    // =========================================================
    // [02] AMENITY
    // =========================================================
    'amenity' => [
        'fields' => [
            'name' => 'Nom',
            'icon' => 'Icône (FontAwesome)',
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
            'edit_title'     => 'Modifier un équipement',
            'save'           => 'Enregistrer',
            'update'         => 'Mettre à jour',
            'cancel'         => 'Annuler',
            'close'          => 'Fermer',
            'state'          => 'Statut',
            'actions'        => 'Actions',
            'delete_forever' => 'Supprimer définitivement',

            'processing' => 'Traitement en cours...',
            'applying'   => 'Application en cours...',
            'deleting'   => 'Suppression en cours...',

            'toggle_on'  => 'Activer l’équipement',
            'toggle_off' => 'Désactiver l’équipement',

            'toggle_confirm_on_title'  => 'Activer l’équipement ?',
            'toggle_confirm_off_title' => 'Désactiver l’équipement ?',
            'toggle_confirm_on_html'   => 'L’équipement <b>:label</b> sera activé.',
            'toggle_confirm_off_html'  => 'L’équipement <b>:label</b> sera désactivé.',

            'delete_confirm_title' => 'Supprimer définitivement ?',
            'delete_confirm_html'  => '<b>:label</b> sera supprimé et vous ne pourrez pas annuler l’action.',

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

        'hints' => [
            'fontawesome' => 'Utilisez les classes FontAwesome, par exemple : "fas fa-check".',
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
            'page_title'        => 'Horaires de tours',
            'page_heading'      => 'Gestion des horaires',

            'general_title'     => 'Horaires globaux',
            'new_schedule'      => 'Nouvel horaire',
            'new_general_title' => 'Nouvel horaire global',
            'new'               => 'Nouveau',
            'edit_schedule'     => 'Modifier l’horaire',
            'edit_global'       => 'Modifier (global)',

            'assign_existing'    => 'Assigner un existant',
            'assign_to_tour'     => 'Assigner un horaire à ":tour"',
            'select_schedule'    => 'Sélectionner un horaire',
            'choose'             => 'Choisir',
            'assign'             => 'Assigner',
            'new_for_tour_title' => 'Nouvel horaire pour ":tour"',

            'time_range'        => 'Plage horaire',
            'state'             => 'Statut',
            'actions'           => 'Actions',
            'schedule_state'    => 'Horaire',
            'assignment_state'  => 'Affectation',
            'no_general'        => 'Aucun horaire global.',
            'no_tour_schedules' => 'Ce tour n’a pas encore d’horaires.',
            'no_label'          => 'Sans libellé',
            'assigned_count'    => 'horaire(s) assigné(s)',

            'toggle_global_title'     => 'Activer/Désactiver (global)',
            'toggle_global_on_title'  => 'Activer l’horaire (global) ?',
            'toggle_global_off_title' => 'Désactiver l’horaire (global) ?',
            'toggle_global_on_html'   => '<b>:label</b> sera activé pour tous les tours.',
            'toggle_global_off_html'  => '<b>:label</b> sera désactivé pour tous les tours.',

            'toggle_on_tour'          => 'Activer pour ce tour',
            'toggle_off_tour'         => 'Désactiver pour ce tour',
            'toggle_assign_on_title'  => 'Activer pour ce tour ?',
            'toggle_assign_off_title' => 'Désactiver pour ce tour ?',
            'toggle_assign_on_html'   => 'L’affectation sera <b>active</b> pour <b>:tour</b>.',
            'toggle_assign_off_html'  => 'L’affectation sera <b>inactive</b> pour <b>:tour</b>.',

            'detach_from_tour'     => 'Retirer du tour',
            'detach_confirm_title' => 'Retirer du tour ?',
            'detach_confirm_html'  => 'L’horaire sera <b>retiré</b> de <b>:tour</b>.',

            'delete_forever'       => 'Supprimer (global)',
            'delete_confirm_title' => 'Supprimer définitivement ?',
            'delete_confirm_html'  => '<b>:label</b> (global) sera supprimé et vous ne pourrez pas annuler l’action.',

            'yes_continue' => 'Oui, continuer',
            'yes_delete'   => 'Oui, supprimer',
            'yes_detach'   => 'Oui, retirer',

            'this_schedule' => 'cet horaire',
            'this_tour'     => 'ce tour',

            'processing'     => 'Traitement en cours...',
            'applying'       => 'Application en cours...',
            'deleting'       => 'Suppression en cours...',
            'removing'       => 'Retrait en cours...',
            'saving_changes' => 'Enregistrement des modifications...',
            'save'           => 'Enregistrer',
            'save_changes'   => 'Enregistrer les modifications',
            'cancel'         => 'Annuler',

            'missing_fields_title' => 'Données manquantes',
            'missing_fields_text'  => 'Vérifiez les champs obligatoires (début, fin et capacité).',
            'could_not_save'       => 'Impossible d’enregistrer',
        ],

        'success' => [
            'created'                => 'Horaire créé avec succès.',
            'updated'                => 'Horaire mis à jour avec succès.',
            'activated_global'       => 'Horaire activé avec succès (global).',
            'deactivated_global'     => 'Horaire désactivé avec succès (global).',
            'attached'               => 'Horaire assigné au tour.',
            'detached'               => 'Horaire retiré du tour avec succès.',
            'assignment_activated'   => 'Affectation activée pour ce tour.',
            'assignment_deactivated' => 'Affectation désactivée pour ce tour.',
            'deleted'                => 'Horaire supprimé avec succès.',
        ],

        'error' => [
            'create'               => 'Un problème est survenu lors de la création de l’horaire.',
            'update'               => 'Un problème est survenu lors de la mise à jour de l’horaire.',
            'toggle'               => 'Impossible de changer le statut global de l’horaire.',
            'attach'               => 'Impossible d’assigner l’horaire au tour.',
            'detach'               => 'Impossible de retirer l’horaire du tour.',
            'assignment_toggle'    => 'Impossible de changer le statut de l’affectation.',
            'not_assigned_to_tour' => 'L’horaire n’est pas assigné à ce tour.',
            'delete'               => 'Un problème est survenu lors de la suppression de l’horaire.',
        ],

        'placeholders' => [
            'morning' => 'Ex. : Matin',
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
            'assigned_items'       => 'Éléments affectés à l’itinéraire',
            'drag_to_order'        => 'Faites glisser les éléments pour définir leur ordre.',
            'pool_hint'            => 'Cochez les éléments disponibles que vous souhaitez inclure dans cet itinéraire.',
            'register_item_hint'   => 'Créez de nouveaux éléments si vous avez besoin d’étapes supplémentaires qui n’existent pas encore.',

            'toggle_on'  => 'Activer l’élément',
            'toggle_off' => 'Désactiver l’élément',

            'delete_forever'       => 'Supprimer définitivement',
            'delete_confirm_title' => 'Supprimer définitivement ?',
            'delete_confirm_html'  => '<b>:label</b> sera supprimé et vous ne pourrez pas annuler l’action.',
            'yes_delete'           => 'Oui, supprimer',
            'item_this'            => 'cet élément',

            'processing' => 'Traitement en cours...',
            'applying'   => 'Application en cours...',
            'deleting'   => 'Suppression en cours...',
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
            'description_optional' => 'Description (optionnelle)',
            'items'                => 'Éléments',
            'item_title'           => 'Titre de l’élément',
            'item_description'     => 'Description de l’élément',
        ],

        'status' => [
            'active'   => 'Actif',
            'inactive' => 'Inactif',
        ],

        'ui' => [
            'page_title'    => 'Itinéraires et éléments',
            'page_heading'  => 'Itinéraires et gestion des éléments',
            'new_itinerary' => 'Nouvel itinéraire',
            'select_or_create_hint' => 'Sélectionnez un itinéraire existant ou créez-en un nouveau pour cette excursion.',
            'save_changes'          => 'Enregistrez l’itinéraire pour appliquer les modifications à l’excursion.',

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
            'select_one_title'      => 'Vous devez sélectionner au moins un élément',
            'select_one_text'       => 'Veuillez sélectionner au moins un élément pour continuer.',
            'assign_confirm_title'  => 'Assigner les éléments sélectionnés ?',
            'assign_confirm_button' => 'Oui, assigner',
            'assigning'             => 'Assignation en cours...',

            'no_items_assigned'       => 'Aucun élément n’est assigné à cet itinéraire.',
            'itinerary_this'          => 'cet itinéraire',
            'processing'              => 'Traitement en cours...',
            'saving'                  => 'Enregistrement...',
            'activating'              => 'Activation...',
            'deactivating'            => 'Désactivation...',
            'applying'                => 'Application en cours...',
            'deleting'                => 'Suppression en cours...',
            'flash_success_title'     => 'Succès',
            'flash_error_title'       => 'Erreur',
            'validation_failed_title' => 'Impossible de traiter',
        ],
        'modal' => [
            'create_itinerary' => 'Créer un itinéraire',
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

        'validation' => [
            'name_required' => 'Vous devez indiquer un nom pour l’itinéraire.',
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
            'code' => 'Code',
        ],

        'status' => [
            'active'   => 'Actif',
            'inactive' => 'Inactif',
        ],

        'ui' => [
            'page_title'   => 'Langues des tours',
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
            'edit_title'     => 'Modifier une langue',
            'save'           => 'Enregistrer',
            'update'         => 'Mettre à jour',
            'cancel'         => 'Annuler',
            'close'          => 'Fermer',
            'actions'        => 'Actions',
            'delete_forever' => 'Supprimer définitivement',

            'processing'   => 'Traitement en cours...',
            'saving'       => 'Enregistrement...',
            'activating'   => 'Activation...',
            'deactivating' => 'Désactivation...',
            'deleting'     => 'Suppression en cours...',

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
            'save'   => 'Impossible d’enregistrer',
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
        'hints' => [
            'iso_639_1' => 'Code ISO 639-1, par exemple : es, en, fr.',
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
            'details'       => 'Détails',
            'price'         => 'Tarifs',
            'overview'      => 'Aperçu',
            'amenities'     => 'Équipements',
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
            'group_size'    => 'Taille du groupe',
        ],

        'pricing' => [
            'configured_categories' => 'Catégories configurées',
            'create_category'       => 'Créer une catégorie',
            'note_title'            => 'Note :',
            'note_text'             => 'Définissez ici les tarifs de base pour chaque catégorie de clients.',
            'manage_detailed_hint'  => 'Pour une gestion détaillée, utilisez le bouton "Gérer les tarifs détaillés" ci-dessus.',
            'price_usd'             => 'Prix (USD)',
            'min_quantity'          => 'Quantité minimale',
            'max_quantity'          => 'Quantité maximale',
            'status'                => 'Statut',
            'active'                => 'Actif',
            'no_categories'         => 'Aucune catégorie de clients configurée.',
            'create_categories_first' => 'Créez d’abord des catégories',
            'page_title'            => 'Tarifs - :name',
            'header_title'          => 'Tarifs : :name',
            'back_to_tours'         => 'Retour aux tours',

            'configured_title'      => 'Catégories et tarifs configurés',
            'empty_title'           => 'Aucune catégorie n’est configurée pour ce tour.',
            'empty_hint'            => 'Utilisez le formulaire à droite pour ajouter des catégories.',

            'save_changes'          => 'Enregistrer les modifications',
            'auto_disable_note'     => 'Les tarifs à 0 $ sont automatiquement désactivés',

            'add_category'          => 'Ajouter une catégorie',

            'all_assigned_title'    => 'Toutes les catégories sont assignées',
            'all_assigned_text'     => 'Il n’y a plus de catégories disponibles pour ce tour.',

            'info_title'            => 'Informations',
            'tour_label'            => 'Tour',
            'configured_count'      => 'Catégories configurées',
            'active_count'          => 'Catégories actives',

            'fields_title'          => 'Champs',
            'rules_title'           => 'Règles',

            'field_price'           => 'Prix',
            'field_min'             => 'Minimum',
            'field_max'             => 'Maximum',
            'field_status'          => 'Statut',

            'rule_min_le_max'       => 'Le minimum doit être inférieur ou égal au maximum',
            'rule_zero_disable'     => 'Les tarifs à 0 $ sont automatiquement désactivés',
            'rule_only_active'      => 'Seules les catégories actives apparaissent sur le site public',

            'status_active'         => 'Actif',
            'add_existing_category'       => 'Ajouter une catégorie existante',
            'choose_category_placeholder' => 'Sélectionnez une catégorie…',
            'add_button'                  => 'Ajouter',
            'add_existing_hint'           => 'Ajoutez uniquement les catégories de clients nécessaires pour ce tour.',
            'remove_category'             => 'Retirer la catégorie',
            'category_already_added'      => 'Cette catégorie a déjà été ajoutée au tour.',
            'no_prices_preview'           => 'Aucun tarif n’est encore configuré.',
        ],

        'modal' => [
            'create_category' => 'Créer une catégorie',

            'fields' => [
                'name'           => 'Nom',
                'age_from'       => 'Âge à partir de',
                'age_to'         => 'Âge jusqu’à',
                'age_range'      => 'Tranche d’âge',
                'min'            => 'Minimum',
                'max'            => 'Maximum',
                'order'          => 'Ordre',
                'is_active'      => 'Actif',
                'auto_translate' => 'Traduire automatiquement',
            ],

            'placeholders' => [
                'name'            => 'Ex. : Adulte, Enfant, Bébé',
                'age_to_optional' => 'Laisser vide pour "+"',
            ],

            'hints' => [
                'age_to_empty_means_plus' => 'Si vous laissez l’âge maximum vide, il sera interprété comme "+" (par exemple 12+).',
                'min_le_max'              => 'Le minimum doit être inférieur ou égal au maximum.',
            ],

            'errors' => [
                'min_le_max' => 'Le minimum doit être inférieur ou égal au maximum.',
            ],
        ],

        'schedules_form' => [
            'available_title'        => 'Horaires disponibles',
            'select_hint'            => 'Sélectionnez les horaires pour ce tour',
            'no_schedules'           => 'Aucun horaire disponible.',
            'create_schedules_link'  => 'Créer des horaires',

            'create_new_title'       => 'Créer un nouvel horaire',
            'label_placeholder'      => 'Ex. : Matin, Après-midi',
            'create_and_assign'      => 'Créer cet horaire et l’assigner au tour',

            'info_title'             => 'Informations',
            'schedules_title'        => 'Horaires',
            'schedules_text'         => 'Sélectionnez un ou plusieurs horaires pendant lesquels ce tour sera disponible.',
            'create_block_title'     => 'Créer un nouveau',
            'create_block_text'      => 'Si vous avez besoin d’un horaire qui n’existe pas, vous pouvez le créer ici en cochant la case "Créer cet horaire et l’assigner au tour".',

            'current_title'          => 'Horaires actuels',
            'none_assigned'          => 'Aucun horaire assigné',
        ],

        'summary' => [
            'preview_title'        => 'Aperçu du tour',
            'preview_text_create'  => 'Vérifiez toutes les informations avant de créer le tour.',
            'preview_text_update'  => 'Vérifiez toutes les informations avant de mettre à jour le tour.',

            'basic_details_title'  => 'Détails de base',
            'description_title'    => 'Description',
            'prices_title'         => 'Tarifs par catégorie',
            'schedules_title'      => 'Horaires',
            'languages_title'      => 'Langues',
            'itinerary_title'      => 'Itinéraire',

            'table' => [
                'category' => 'Catégorie',
                'price'    => 'Prix',
                'min_max'  => 'Min–Max',
            ],

            'not_specified'        => 'Non spécifié',
            'slug_autogenerated'   => 'Sera généré automatiquement',
            'no_description'       => 'Aucune description',
            'no_active_prices'     => 'Aucun tarif actif configuré',
            'no_languages'         => 'Aucune langue assignée',
            'none_included'        => 'Aucun élément indiqué comme inclus',
            'none_excluded'        => 'Aucun élément indiqué comme exclu',

            'units' => [
                'hours'  => 'heures',
                'people' => 'personnes',
            ],

            'create_note' => 'Les horaires, tarifs, langues et équipements seront affichés ici après l’enregistrement du tour.',
        ],

        'alerts' => [
            'delete_title' => 'Supprimer le tour ?',
            'delete_text'  => 'Le tour sera déplacé dans Supprimés. Vous pourrez le restaurer plus tard.',
            'purge_title'  => 'Supprimer définitivement ?',
            'purge_text'   => 'Cette action est irréversible.',
            'purge_text_with_bookings' => 'Ce tour a :count réservation(s). Elles ne seront pas supprimées ; elles resteront sans tour associé.',
            'toggle_question_active'   => 'Désactiver le tour ?',
            'toggle_question_inactive' => 'Activer le tour ?',
        ],

        'flash' => [
            'created'       => 'Tour créé avec succès.',
            'updated'       => 'Tour mis à jour avec succès.',
            'deleted_soft'  => 'Tour déplacé dans Supprimés.',
            'restored'      => 'Tour restauré avec succès.',
            'purged'        => 'Tour supprimé définitivement.',
            'toggled_on'    => 'Tour activé.',
            'toggled_off'   => 'Tour désactivé.',
        ],

        'table' => [
            'id'            => 'ID',
            'name'          => 'Nom',
            'overview'      => 'Aperçu',
            'amenities'     => 'Équipements',
            'exclusions'    => 'Exclusions',
            'itinerary'     => 'Itinéraire',
            'languages'     => 'Langues',
            'schedules'     => 'Horaires',
            'adult_price'   => 'Tarif adulte',
            'kid_price'     => 'Tarif enfant',
            'length_hours'  => 'Durée (h)',
            'max_capacity'  => 'Capacité max.',
            'type'          => 'Type',
            'viator_code'   => 'Code Viator',
            'status'        => 'Statut',
            'actions'       => 'Actions',
            'slug'          => 'URL',
            'prices'        => 'Tarifs',
            'capacity'      => 'Capacité',
            'group_size'    => 'Groupe max.',
        ],

        'status' => [
            'active'   => 'Actif',
            'inactive' => 'Inactif',
            'archived' => 'Archivé',
        ],

        'placeholders' => [
            'group_size' => 'Ex. : 10',
        ],

        'hints' => [
            'group_size' => 'Capacité/taille de groupe recommandée pour ce tour.',
        ],

        'success' => [
            'created'     => 'Le tour a été créé avec succès.',
            'updated'     => 'Le tour a été mis à jour avec succès.',
            'deleted'     => 'Le tour a été supprimé.',
            'toggled'     => 'Le statut du tour a été mis à jour.',
            'activated'   => 'Tour activé avec succès.',
            'deactivated' => 'Tour désactivé avec succès.',
            'archived'    => 'Tour archivé avec succès.',
            'restored'    => 'Tour restauré avec succès.',
            'purged'      => 'Tour supprimé de façon permanente.',
        ],

        'error' => [
            'create'    => 'Impossible de créer le tour.',
            'update'    => 'Impossible de mettre à jour le tour.',
            'delete'    => 'Impossible de supprimer le tour.',
            'toggle'    => 'Impossible de changer le statut du tour.',
            'not_found' => 'Le tour n’existe pas.',
            'restore'            => 'Impossible de restaurer le tour.',
            'purge'              => 'Impossible de supprimer définitivement le tour.',
            'purge_has_bookings' => 'Suppression permanente impossible : le tour a des réservations.',
        ],

        'ui' => [
            'page_title'       => 'Gestion des tours',
            'page_heading'     => 'Gestion des tours',
            'create_title'     => 'Enregistrer un tour',
            'edit_title'       => 'Modifier le tour',
            'delete_title'     => 'Supprimer le tour',
            'cancel'           => 'Annuler',
            'save'             => 'Enregistrer',
            'save_changes'     => 'Enregistrer les modifications',
            'update'           => 'Mettre à jour',
            'delete_confirm'   => 'Supprimer ce tour ?',
            'toggle_on'        => 'Activer',
            'toggle_off'       => 'Désactiver',
            'toggle_on_title'  => 'Activer le tour ?',
            'toggle_off_title' => 'Désactiver le tour ?',
            'toggle_on_button'  => 'Oui, activer',
            'toggle_off_button' => 'Oui, désactiver',
            'see_more'         => 'Voir plus',
            'see_less'         => 'Masquer',
            'load_more'        => 'Charger plus',
            'loading'          => 'Chargement...',
            'load_more_error'  => 'Impossible de charger plus de tours.',
            'confirm_title'    => 'Confirmation',
            'confirm_text'     => 'Souhaitez-vous confirmer cette action ?',
            'yes_confirm'      => 'Oui, confirmer',
            'no_confirm'       => 'Non, annuler',
            'add_tour'         => 'Ajouter un tour',
            'edit_tour'        => 'Modifier le tour',
            'delete_tour'      => 'Supprimer le tour',
            'toggle_tour'      => 'Activer/Désactiver le tour',
            'view_cart'        => 'Voir le panier',
            'add_to_cart'      => 'Ajouter au panier',
            'slug_help'        => 'Identifiant du tour dans l’URL (sans espaces ni accents)',
            'generate_auto'       => 'Générer automatiquement',
            'slug_preview_label'  => 'Aperçu',
            'saved'               => 'Enregistré',

            'available_languages'    => 'Langues disponibles',
            'default_capacity'       => 'Capacité par défaut',
            'create_new_schedules'   => 'Créer de nouveaux horaires',
            'multiple_hint_ctrl_cmd' => 'Maintenez CTRL/CMD pour sélectionner plusieurs éléments',
            'use_existing_schedules' => 'Utiliser des horaires existants',
            'add_schedule'           => 'Ajouter un horaire',
            'schedules_title'        => 'Horaires du tour',
            'amenities_included'     => 'Équipements inclus',
            'amenities_excluded'     => 'Équipements non inclus',
            'color'                  => 'Couleur du tour',
            'remove'                 => 'Retirer',
            'choose_itinerary'       => 'Choisir un itinéraire',
            'select_type'            => 'Sélectionner un type',
            'empty_means_default'    => 'Par défaut',
            'actives'                => 'Actifs',
            'inactives'              => 'Inactifs',
            'archived'               => 'Archivé',
            'all'                    => 'Tous',
            'help_title'             => 'Aide',
            'amenities_included_hint' => 'Sélectionnez ce qui est inclus dans le tour.',
            'amenities_excluded_hint' => 'Sélectionnez ce qui n’est PAS inclus dans le tour.',
            'help_included_title'     => 'Inclus',
            'help_included_text'      => 'Cochez tout ce qui est inclus dans le prix du tour (transport, repas, entrées, équipement, guide, etc.).',
            'help_excluded_title'     => 'Non inclus',
            'help_excluded_text'      => 'Cochez ce que le client doit payer séparément ou apporter (pourboires, boissons alcoolisées, souvenirs, etc.).',
            'select_or_create_title' => 'Sélectionner ou créer un itinéraire',
            'select_existing_items'  => 'Sélectionner des éléments existants',
            'name_hint'              => 'Nom identifiant pour cet itinéraire',
            'click_add_item_hint'    => 'Cliquez sur "Ajouter un élément" pour créer de nouveaux éléments',
            'scroll_hint'            => 'Faites défiler horizontalement pour voir plus de colonnes',
            'no_schedules'           => 'Aucun horaire',
            'no_prices'              => 'Aucun tarif configuré',
            'edit'                   => 'Modifier',
            'slug_auto'              => 'Sera généré automatiquement',
            'added_to_cart'          => 'Ajouté au panier',
            'added_to_cart_text'     => 'Le tour a été ajouté au panier avec succès.',

            'none' => [
                'amenities'       => 'Aucun équipement',
                'exclusions'      => 'Aucune exclusion',
                'itinerary'       => 'Aucun itinéraire',
                'itinerary_items' => 'Aucun élément',
                'languages'       => 'Aucune langue',
                'schedules'       => 'Aucun horaire',
            ],

            'archive' => 'Archiver',
            'restore' => 'Restaurer',
            'purge'   => 'Supprimer définitivement',

            'confirm_archive_title' => 'Archiver le tour ?',
            'confirm_archive_text'  => 'Le tour sera désactivé pour les nouvelles réservations, mais les réservations existantes seront conservées.',
            'confirm_purge_title'   => 'Supprimer définitivement',
            'confirm_purge_text'    => 'Cette action est irréversible et n’est autorisée que si le tour n’a jamais eu de réservations.',

            'filters' => [
                'active'   => 'Actifs',
                'inactive' => 'Inactifs',
                'archived' => 'Archivés',
                'all'      => 'Tous',
            ],

            'font_decrease_title' => 'Diminuer la taille de la police',
            'font_increase_title' => 'Augmenter la taille de la police',
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
        'saved'               => 'Enregistrer',
        'caption_updated'     => 'Légende mise à jour avec succès.',
        'deleted'             => 'Supprimé',
        'image_removed'       => 'Image supprimée avec succès.',
        'invalid_order'       => 'Ordre invalide.',
        'nothing_to_reorder'  => 'Rien à réorganiser.',
        'order_saved'         => 'Ordre enregistré.',
        'cover_updated_title' => 'Mettre à jour la couverture',
        'cover_updated_text'  => 'Cette image est maintenant la couverture.',
        'deleting'            => 'Suppression en cours...',

        'ui' => [
            'page_title_pick'     => 'Images des tours',
            'page_heading'        => 'Images des tours',
            'choose_tour'         => 'Choisir un tour',
            'search_placeholder'  => 'Rechercher par ID ou nom…',
            'search_button'       => 'Rechercher',
            'no_results'          => 'Aucun tour trouvé.',
            'manage_images'       => 'Gérer les images',
            'cover_alt'           => 'Couverture',
            'images_label'        => 'images',

            'upload_btn'          => 'Télécharger',
            'delete_btn'          => 'Supprimer',
            'show_btn'            => 'Afficher',
            'close_btn'           => 'Fermer',
            'preview_title'       => 'Aperçu de l’image',

            'error_title'         => 'Erreur',
            'warning_title'       => 'Attention',
            'success_title'       => 'Succès',
            'cancel_btn'          => 'Annuler',

            'confirm_delete_title' => 'Supprimer cette image ?',
            'confirm_delete_text'  => 'Cette action ne peut pas être annulée.',

            'cover_current_title'    => 'Couverture actuelle',
            'upload_new_cover_title' => 'Télécharger une nouvelle couverture',
            'cover_file_label'       => 'Fichier de couverture',
            'file_help_cover'        => 'JPEG/PNG/WebP, 30 Mo max.',
            'id_label'               => 'ID',

            'back_btn'          => 'Retour à la liste',

            'stats_images'      => 'Images téléchargées',
            'stats_cover'       => 'Couvertures définies',
            'stats_selected'    => 'Sélectionnées',

            'drag_or_click'     => 'Glissez-déposez vos images ou cliquez pour les sélectionner.',
            'upload_help'       => 'Formats autorisés : JPG, PNG, WebP. Taille totale maximale 100 Mo.',
            'select_btn'        => 'Choisir des fichiers',
            'limit_badge'       => 'Limite de :max images atteinte',
            'files_word'        => 'fichiers',

            'select_all'        => 'Tout sélectionner',
            'delete_selected'   => 'Supprimer la sélection',
            'delete_all'        => 'Tout supprimer',

            'select_image_title' => 'Sélectionner cette image',
            'select_image_aria'  => 'Sélectionner l’image :id',

            'cover_label'       => 'Couverture',
            'cover_btn'         => 'Définir comme couverture',

            'caption_placeholder' => 'Légende (optionnelle)',
            'saving_label'        => 'Enregistrement…',
            'saving_fallback'     => 'Enregistrement…',
            'none_label'          => 'Sans légende',
            'limit_word'          => 'Limite',

            'confirm_set_cover_title' => 'Définir comme couverture ?',
            'confirm_set_cover_text'  => 'Cette image sera la couverture principale du tour.',
            'confirm_btn'             => 'Oui, continuer',

            'confirm_bulk_delete_title' => 'Supprimer les images sélectionnées ?',
            'confirm_bulk_delete_text'  => 'Les images sélectionnées seront supprimées définitivement.',

            'confirm_delete_all_title'  => 'Supprimer toutes les images ?',
            'confirm_delete_all_text'   => 'Toutes les images de ce tour seront supprimées.',

            'no_images'           => 'Il n’y a pas encore d’images pour ce tour.',
        ],

        'errors' => [
            'validation'     => 'Les données envoyées ne sont pas valides.',
            'upload_generic' => 'Certaines images n’ont pas pu être téléchargées.',
            'update_caption' => 'Impossible de mettre à jour la légende.',
            'delete'         => 'Impossible de supprimer l’image.',
            'reorder'        => 'Impossible d’enregistrer l’ordre.',
            'set_cover'      => 'Impossible de définir la couverture.',
            'load_list'      => 'Impossible de charger la liste.',
            'too_large'      => 'Le fichier dépasse la taille maximum autorisée. Essayez avec une image plus légère.',
        ],
    ],

    'prices' => [
        'ui' => [
            'page_title'         => 'Tarifs - :name',
            'header_title'       => 'Tarifs : :name',
            'back_to_tours'      => 'Retour aux tours',

            'configured_title'   => 'Catégories et tarifs configurés',
            'empty_title'        => 'Aucune catégorie n’est configurée pour ce tour.',
            'empty_hint'         => 'Utilisez le formulaire à droite pour ajouter des catégories.',

            'save_changes'       => 'Enregistrer les modifications',
            'auto_disable_note'  => 'Les tarifs à 0 $ sont automatiquement désactivés',

            'add_category'       => 'Ajouter une catégorie',

            'all_assigned_title' => 'Toutes les catégories sont assignées',
            'all_assigned_text'  => 'Il n’y a plus de catégories disponibles pour ce tour.',

            'info_title'         => 'Informations',
            'tour_label'         => 'Tour',
            'configured_count'   => 'Catégories configurées',
            'active_count'       => 'Catégories actives',

            'fields_title'       => 'Champs',
            'rules_title'        => 'Règles',

            'field_price'        => 'Prix',
            'field_min'          => 'Minimum',
            'field_max'          => 'Maximum',
            'field_status'       => 'Statut',

            'rule_min_le_max'    => 'Le minimum doit être inférieur ou égal au maximum',
            'rule_zero_disable'  => 'Les tarifs à 0 $ sont automatiquement désactivés',
            'rule_only_active'   => 'Seules les catégories actives apparaissent sur le site public',
        ],

        'table' => [
            'category'   => 'Catégorie',
            'age_range'  => 'Tranche d’âge',
            'price_usd'  => 'Prix (USD)',
            'min'        => 'Min',
            'max'        => 'Max',
            'status'     => 'Statut',
            'action'     => 'Action',
            'active'     => 'Actif',
            'inactive'   => 'Inactif',
        ],

        'forms' => [
            'select_placeholder'   => '-- Sélectionner --',
            'category'             => 'Catégorie',
            'price_usd'            => 'Prix (USD)',
            'min'                  => 'Minimum',
            'max'                  => 'Maximum',
            'create_disabled_hint' => 'Si le prix est de 0 $, la catégorie sera créée comme désactivée',
            'add'                  => 'Ajouter',
        ],

        'modal' => [
            'delete_title'   => 'Supprimer la catégorie',
            'delete_text'    => 'Supprimer cette catégorie de ce tour ?',
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
            'auto_disabled_tooltip' => 'Prix 0 $ – désactivé automatiquement',
            'fix_errors'            => 'Corrigez les quantités minimales et maximales',
        ],
    ],

    'ajax' => [
        'category_created' => 'Catégorie créée avec succès',
        'category_error'   => 'Erreur lors de la création de la catégorie',
        'language_created' => 'Langue créée avec succès',
        'language_error'   => 'Erreur lors de la création de la langue',
        'amenity_created'  => 'Équipement créé avec succès',
        'amenity_error'    => 'Erreur lors de la création de l’équipement',
        'schedule_created' => 'Horaire créé avec succès',
        'schedule_error'   => 'Erreur lors de la création de l’horaire',
        'itinerary_created' => 'Itinéraire créé avec succès',
        'itinerary_error'   => 'Erreur lors de la création de l’itinéraire',
        'translation_error' => 'Erreur lors de la traduction',
    ],

    'modal' => [
        'create_category'  => 'Créer une nouvelle catégorie',
        'create_language'  => 'Créer une nouvelle langue',
        'create_amenity'   => 'Créer un nouvel équipement',
        'create_schedule'  => 'Créer un nouvel horaire',
        'create_itinerary' => 'Créer un nouvel itinéraire',
    ],

    'validation' => [
        'slug_taken'     => 'Ce slug est déjà utilisé',
        'slug_available' => 'Slug disponible',
    ],

];
