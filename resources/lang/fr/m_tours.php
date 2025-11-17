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
        'success_title'        => 'Succès',
        'error_title'          => 'Erreur',
        'people'               => 'personnes',
        'hours'                => 'heures',
        'success'              => 'Succès',
        'error'                => 'Erreur',
        'cancel'               => 'Annuler',
        'confirm_delete'       => 'Oui, supprimer',
        'unspecified'          => 'Non spécifié',
        'no_description'       => 'Sans description',
        'required_fields_title'=> 'Champs obligatoires',
        'required_fields_text' => 'Veuillez compléter les champs obligatoires : Nom et Capacité maximale',
        'active'               => 'Actif',
        'inactive'             => 'Inactif',
        'notice'               => 'Avis',
        'na'                   => 'Non configuré',
        'create'               => 'Créer',
        'previous'             => 'Retour',
        'info'                 => 'Information',
        'close'                => 'Fermer',
        'save'                 => 'Enregistrer',
        'required'             => 'Ce champ est obligatoire.',
        'add'                  => 'Ajouter',
        'translating'          => 'Traduction en cours…',
        'error_translating'    => 'Le texte n’a pas pu être traduit.',
        'confirm'              => 'confirmer',
        'yes'                  => 'Oui',
        'form_errors_title'    => 'Veuillez corriger les erreurs suivantes :',
        'delete'               => 'Supprimer',
        'delete_all'           => 'Tout supprimer',
        'actions'              => 'Actions',
        'updated_at'           => 'Dernière mise à jour',
        'not_set'              => 'Non spécifié',
        'error_deleting'       => 'Une erreur est survenue lors de la suppression. Veuillez réessayer.',
        'error_saving'         => 'Une erreur est survenue lors de l’enregistrement. Veuillez réessayer.',
        'crud_go_to_index'     => 'Gérer :element',
        'validation_title'     => 'Il y a des erreurs de validation',
        'ok'                   => 'Accepter',
    ],

    // =========================================================
    // [02] AMENITY
    // =========================================================
    'amenity' => [
        'singular' => 'aménagement',
        'plural'   => 'aménagements',

        'fields' => [
            'name' => 'Nom',
            'icon' => 'Icône (FontAwesome)',
        ],

        'status' => [
            'active'   => 'Actif',
            'inactive' => 'Inactif',
        ],

        'ui' => [
            'page_title'    => 'Aménagements',
            'page_heading'  => 'Gestion des aménagements',
            'list_title'    => 'Liste des aménagements',

            'add'            => 'Ajouter un aménagement',
            'create_title'   => 'Enregistrer un aménagement',
            'edit_title'     => 'Modifier un aménagement',
            'save'           => 'Enregistrer',
            'update'         => 'Mettre à jour',
            'cancel'         => 'Annuler',
            'close'          => 'Fermer',
            'state'          => 'Statut',
            'actions'        => 'Actions',
            'delete_forever' => 'Supprimer définitivement',

            'processing' => 'Traitement…',
            'applying'   => 'Application…',
            'deleting'   => 'Suppression…',

            'toggle_on'  => 'Activer l’aménagement',
            'toggle_off' => 'Désactiver l’aménagement',

            'toggle_confirm_on_title'  => 'Activer l’aménagement ?',
            'toggle_confirm_off_title' => 'Désactiver l’aménagement ?',
            'toggle_confirm_on_html'   => 'L’aménagement <b>:label</b> sera actif.',
            'toggle_confirm_off_html'  => 'L’aménagement <b>:label</b> sera inactif.',

            'delete_confirm_title' => 'Supprimer définitivement ?',
            'delete_confirm_html'  => 'L’élément <b>:label</b> sera supprimé et cette action est irréversible.',

            'yes_continue' => 'Oui, continuer',
            'yes_delete'   => 'Oui, supprimer',

            'item_this' => 'cet aménagement',
        ],

        'success' => [
            'created'     => 'Aménagement créé avec succès.',
            'updated'     => 'Aménagement mis à jour avec succès.',
            'activated'   => 'Aménagement activé avec succès.',
            'deactivated' => 'Aménagement désactivé avec succès.',
            'deleted'     => 'Aménagement supprimé définitivement.',
        ],

        'error' => [
            'create' => 'Impossible de créer l’aménagement.',
            'update' => 'Impossible de mettre à jour l’aménagement.',
            'toggle' => 'Impossible de changer le statut de l’aménagement.',
            'delete' => 'Impossible de supprimer l’aménagement.',
        ],

        'validation' => [
            'included_required' => 'Vous devez sélectionner au moins un aménagement inclus.',
            'name' => [
                'title'    => 'Nom invalide',
                'required' => 'Le :attribute est obligatoire.',
                'string'   => 'Le :attribute doit être une chaîne de caractères.',
                'max'      => 'Le :attribute ne peut pas dépasser :max caractères.',
            ],
        ],

        'hints' => [
            'fontawesome' => 'Utilisez des classes FontAwesome, par exemple : "fas fa-check".',
        ],

        'quick_create' => [
            'button'           => 'Nouvel aménagement',
            'title'            => 'Création rapide d’aménagement',
            'name_label'       => 'Nom de l’aménagement',
            'icon_label'       => 'Icône (optionnel)',
            'icon_placeholder' => 'Ex : fas fa-utensils',
            'icon_help'        => 'Utilisez une classe d’icône Font Awesome ou laissez vide.',
            'save'             => 'Enregistrer l’aménagement',
            'cancel'           => 'Annuler',
            'saving'           => 'Enregistrement…',
            'error_generic'    => 'Impossible de créer l’aménagement. Veuillez réessayer.',
            'go_to_index'         => 'Voir tous',
            'go_to_index_title'   => 'Aller à la liste complète des aménagements',
            'success_title'       => 'Aménagement créé',
            'success_text'        => 'L’aménagement a été ajouté à la liste du tour.',
            'error_title'         => 'Erreur lors de la création de l’aménagement',
            'error_duplicate'     => 'Un aménagement portant ce nom existe déjà.',
        ],
    ],

    // =========================================================
    // [03] SCHEDULE
    // =========================================================
    'schedule' => [
        'plural'   => 'Horaires',
        'singular' => 'Horaire',

        'fields' => [
            'start_time'     => 'Début',
            'end_time'       => 'Fin',
            'label'          => 'Libellé',
            'label_optional' => 'Libellé (facultatif)',
            'max_capacity'   => 'Capacité max.',
            'active'         => 'Actif',
        ],

        'status' => [
            'active'   => 'Actif',
            'inactive' => 'Inactif',
        ],

        'ui' => [
            'page_title'   => 'Horaires des tours',
            'page_heading' => 'Gestion des horaires',

            'general_title'     => 'Horaires généraux',
            'new_schedule'      => 'Nouvel horaire',
            'new_general_title' => 'Nouvel horaire général',
            'new'               => 'Nouveau',
            'edit_schedule'     => 'Modifier l’horaire',
            'edit_global'       => 'Modifier (global)',

            'assign_existing'    => 'Assigner un horaire existant',
            'assign_to_tour'     => 'Assigner l’horaire à ":tour"',
            'select_schedule'    => 'Sélectionnez un horaire',
            'choose'             => 'Choisir',
            'assign'             => 'Assigner',
            'new_for_tour_title' => 'Nouvel horaire pour ":tour"',

            'time_range'        => 'Horaire',
            'state'             => 'Statut',
            'actions'           => 'Actions',
            'schedule_state'    => 'Horaire',
            'assignment_state'  => 'Affectation',
            'no_general'        => 'Aucun horaire général.',
            'no_tour_schedules' => 'Ce tour n’a pas encore d’horaires.',
            'no_label'          => 'Sans libellé',
            'assigned_count'    => 'horaire(s) assigné(s)',

            'toggle_global_title'     => 'Activer/Désactiver (global)',
            'toggle_global_on_title'  => 'Activer cet horaire (global) ?',
            'toggle_global_off_title' => 'Désactiver cet horaire (global) ?',
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
            'detach_confirm_html'  => 'L’horaire sera <b>désassigné</b> de <b>:tour</b>.',

            'delete_forever'       => 'Supprimer (global)',
            'delete_confirm_title' => 'Supprimer définitivement ?',
            'delete_confirm_html'  => '<b>:label</b> sera supprimé (globalement) et cette action est irréversible.',

            'yes_continue' => 'Oui, continuer',
            'yes_delete'   => 'Oui, supprimer',
            'yes_detach'   => 'Oui, retirer',

            'this_schedule' => 'cet horaire',
            'this_tour'     => 'ce tour',

            'processing'     => 'Traitement…',
            'applying'       => 'Application…',
            'deleting'       => 'Suppression…',
            'removing'       => 'Retrait…',
            'saving_changes' => 'Enregistrement des changements…',
            'save'           => 'Enregistrer',
            'save_changes'   => 'Enregistrer les changements',
            'cancel'         => 'Annuler',

            'missing_fields_title' => 'Données manquantes',
            'missing_fields_text'  => 'Vérifiez les champs obligatoires (début, fin et capacité).',
            'could_not_save'       => 'Impossible d’enregistrer',
            'base_capacity_tour'             => 'Capacité de base du tour :',
            'capacity_not_defined'           => 'Non définie',
            'capacity_optional'              => 'Capacité (facultatif)',
            'capacity_placeholder_with_value' => 'Ex : :capacity',
            'capacity_placeholder_generic'   => 'Utiliser la capacité du tour',
            'capacity_hint_with_value'       => 'Laisser vide → :capacity',
            'capacity_hint_generic'          => 'Laisser vide → capacité générale du tour',
            'tip_label'                      => 'Astuce :',
            'capacity_tip'                   => 'Vous pouvez laisser la capacité vide pour que le système utilise la capacité générale du tour (:capacity).',
            'new_schedule_for_tour'            => 'Nouvel horaire',
            'modal_new_for_tour_title'         => 'Créer un horaire pour :tour',
            'modal_save'                       => 'Enregistrer l’horaire',
            'modal_cancel'                     => 'Annuler',
            'capacity_modal_info_with_value'   => 'La capacité de base du tour est :capacity. Si vous laissez la capacité vide, cette valeur sera utilisée.',
            'capacity_modal_info_generic'      => 'Si vous laissez la capacité vide, la capacité générale du tour sera utilisée lorsqu’elle est définie.',
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
            'detach'               => 'Impossible de désassigner l’horaire du tour.',
            'assignment_toggle'    => 'Impossible de changer le statut de l’affectation.',
            'not_assigned_to_tour' => 'L’horaire n’est pas assigné à ce tour.',
            'delete'               => 'Un problème est survenu lors de la suppression de l’horaire.',
        ],

        'placeholders' => [
            'morning' => 'Ex : Matin',
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
            'assigned_items'       => 'Éléments assignés à l’itinéraire',
            'drag_to_order'        => 'Faites glisser les éléments pour définir l’ordre.',
            'pool_hint'            => 'Cochez les éléments disponibles que vous souhaitez inclure dans cet itinéraire.',
            'register_item_hint'   => 'Enregistrez de nouveaux éléments si vous avez besoin d’étapes supplémentaires qui n’existent pas encore.',

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
            'toggle' => 'Impossible de changer le statut de l’élément.',
            'delete' => 'Impossible de supprimer l’élément.',
        ],

        'validation' => [
            'title' => [
                'required' => 'Le :attribute est obligatoire.',
                'string'   => 'Le :attribute doit être une chaîne de texte.',
                'max'      => 'Le :attribute ne peut pas dépasser :max caractères.',
            ],
            'description' => [
                'required' => 'La :attribute est obligatoire.',
                'string'   => 'La :attribute doit être une chaîne de texte.',
                'max'      => 'La :attribute ne peut pas dépasser :max caractères.',
            ],
        ],
    ],

    // =========================================================
    // [05] ITINERARY
    // =========================================================
    'itinerary' => [
        'plural'   => 'Itinéraires',
        'singular' => 'Itinéraire',

        'fields' => [
            'name'                 => 'Nom de l’itinéraire',
            'description'          => 'Description',
            'description_optional' => 'Description (facultatif)',
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
            'select_or_create_hint' => 'Sélectionnez un itinéraire existant ou créez-en un nouveau pour ce tour.',
            'save_changes'          => 'Enregistrez l’itinéraire pour appliquer les changements au tour.',
            'select_existing' => 'Sélectionner un itinéraire existant',
            'create_new'      => 'Créer un nouvel itinéraire',
            'add_item'        => 'Ajouter un élément',
            'min_one_item'    => 'Il doit y avoir au moins un élément dans l’itinéraire',

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
            'drag_hint'             => 'Faites glisser et déposez les éléments pour définir l’ordre.',
            'drag_handle'           => 'Glisser pour réorganiser',
            'select_one_title'      => 'Vous devez sélectionner au moins un élément',
            'select_one_text'       => 'Veuillez sélectionner au moins un élément pour continuer.',
            'assign_confirm_title'  => 'Assigner les éléments sélectionnés ?',
            'assign_confirm_button' => 'Oui, assigner',
            'assigning'             => 'Assignation…',

            'no_items_assigned'       => 'Aucun élément assigné à cet itinéraire.',
            'itinerary_this'          => 'cet itinéraire',
            'processing'              => 'Traitement…',
            'saving'                  => 'Enregistrement…',
            'activating'              => 'Activation…',
            'deactivating'            => 'Désactivation…',
            'applying'                => 'Application…',
            'deleting'                => 'Suppression…',
            'flash_success_title'     => 'Succès',
            'flash_error_title'       => 'Erreur',
            'validation_failed_title' => 'Impossible de traiter',
            'go_to_crud'              => 'Aller au module',
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
                'unique'   => 'Un itinéraire portant ce nom existe déjà.',
            ],
            'description' => [
                'string' => 'La description doit être du texte.',
                'max'    => 'La description ne peut pas dépasser 1000 caractères.',
            ],
            'items' => [
                'item'          => 'Élément',
                'required'      => 'Vous devez sélectionner au moins un élément.',
                'array'         => 'Le format des éléments n’est pas valide.',
                'min'           => 'Vous devez sélectionner au moins un élément.',
                'order_integer' => 'L’ordre doit être un nombre entier.',
                'order_min'     => 'L’ordre ne peut pas être négatif.',
                'order_max'     => 'L’ordre ne peut pas dépasser 9999.',
            ],
        ],

        'item'  => 'Élément',
        'items' => 'Éléments',
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
            'edit_confirm_title'       => 'Enregistrer les changements ?',
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
            'save'   => 'Impossible d’enregistrer.',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Nom invalide',
                'required' => 'Le nom de la langue est obligatoire.',
                'string'   => 'Le :attribute doit être une chaîne de texte.',
                'max'      => 'Le :attribute ne peut pas dépasser :max caractères.',
                'unique'   => 'Une langue portant ce nom existe déjà.',
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

        'validation' => [
            // Messages généraux
            'required' => 'Ce champ est obligatoire.',
            'min'      => 'Ce champ doit contenir au moins :min caractères.',
            'max'      => 'Ce champ ne peut pas dépasser :max caractères.',
            'number'   => 'Ce champ doit être un nombre valide.',
            'slug'     => 'Le slug ne peut contenir que des lettres minuscules, des chiffres et des tirets.',
            'color'    => 'Veuillez choisir une couleur valide.',
            'select'   => 'Veuillez sélectionner une option.',

            // Messages spécifiques des champs
            'length_in_hours'   => 'Durée en heures (ex : 2, 2.5, 4)',
            'max_capacity_help' => 'Nombre maximal de personnes par tour',

            // Formulaires
            'form_error_title'   => 'Attention !',
            'form_error_message' => 'Veuillez corriger les erreurs du formulaire avant de continuer.',
            'saving'             => 'Enregistrement…',

            // Succès
            'success'           => 'Succès !',
            'tour_type_created' => 'Type de tour créé avec succès.',
            'language_created'  => 'Langue créée avec succès.',

            // Erreurs
            'tour_type_error' => 'Erreur lors de la création du type de tour.',
            'language_error'  => 'Erreur lors de la création de la langue.',
        ],

        'wizard' => [
            // Titres généraux
            'create_new_tour' => 'Créer un nouveau tour',
            'edit_tour'       => 'Modifier le tour',
            'step_number'     => 'Étape :number',
            'edit_step'       => 'Modifier',
            'leave_warning'   => 'Vous avez des changements non enregistrés dans le tour. Si vous quittez maintenant, le brouillon restera dans la base de données. Êtes-vous sûr de vouloir quitter ?',
            'cancel_title'    => 'Annuler la configuration du tour ?',
            'cancel_text'     => 'Si vous quittez cet assistant, vous pourriez perdre des changements non enregistrés à cette étape.',
            'cancel_confirm'  => 'Oui, ignorer les changements',
            'cancel_cancel'   => 'Non, continuer l’édition',
            'details_validation_text' => 'Vérifiez les champs obligatoires du formulaire de détails avant de continuer.',
            'most_recent'     => 'Plus récent',
            'last_modified'   => 'Dernière modification',
            'start_fresh'     => 'Recommencer',
            'draft_details'   => 'Détails du brouillon',
            'drafts_found'    => 'Un brouillon a été trouvé',
            'basic_info'      => 'Détails',

            // Étapes du wizard
            'steps' => [
                'details'   => 'Détails de base',
                'itinerary' => 'Itinéraire',
                'schedules' => 'Horaires',
                'amenities' => 'Aménagements',
                'prices'    => 'Prix',
                'summary'   => 'Résumé',
            ],

            // Actions
            'save_and_continue' => 'Enregistrer et continuer',
            'publish_tour'      => 'Publier le tour',
            'delete_draft'      => 'Supprimer le brouillon',
            'ready_to_publish'  => 'Prêt à publier ?',

            // Messages
            'details_saved'    => 'Détails enregistrés avec succès.',
            'itinerary_saved'  => 'Itinéraire enregistré avec succès.',
            'schedules_saved'  => 'Horaires enregistrés avec succès.',
            'amenities_saved'  => 'Aménagements enregistrés avec succès.',
            'prices_saved'     => 'Prix enregistrés avec succès.',
            'published_successfully' => 'Tour publié avec succès !',
            'draft_cancelled'  => 'Brouillon supprimé.',

            // États
            'draft_mode'          => 'Mode brouillon',
            'draft_explanation'   => 'Ce tour sera enregistré comme brouillon jusqu’à ce que vous complétiez toutes les étapes et le publiiez.',
            'already_published'   => 'Ce tour a déjà été publié. Utilisez l’éditeur normal pour le modifier.',
            'cannot_cancel_published' => 'Vous ne pouvez pas annuler un tour déjà publié.',

            // Confirmations
            'confirm_cancel' => 'Êtes-vous sûr de vouloir annuler et supprimer ce brouillon ?',

            // Résumé
            'publish_explanation' => 'Vérifiez toutes les informations avant de publier. Une fois publié, le tour sera disponible pour les réservations.',
            'can_edit_later'      => 'Vous pourrez modifier le tour après sa publication depuis le panneau d’administration.',
            'incomplete_warning'  => 'Certaines étapes sont incomplètes. Vous pouvez publier quand même, mais il est recommandé de compléter toutes les informations.',

            // Checklist
            'checklist'              => 'Liste de vérification',
            'checklist_details'      => 'Détails de base complétés',
            'checklist_itinerary'    => 'Itinéraire configuré',
            'checklist_schedules'    => 'Horaires ajoutés',
            'checklist_amenities'    => 'Aménagements configurés',
            'checklist_prices'       => 'Prix définis',

            // Indices (hints)
            'hints' => [
                'status' => 'Le statut peut être modifié après la publication.',
            ],

            // Modal des brouillons existants
            'existing_drafts_title'   => 'Vous avez des tours en brouillon non terminés !',
            'existing_drafts_message' => 'Nous avons trouvé :count tour en brouillon que vous n’avez pas terminé.',
            'current_step'            => 'Étape actuelle',
            'step'                   => 'Étape',

            // Actions du modal
            'continue_draft'      => 'Continuer avec ce brouillon',
            'delete_all_drafts'   => 'Supprimer tous les brouillons',
            'create_new_anyway'   => 'Créer un nouveau tour quand même',

            // Informations supplémentaires
            'drafts_info' => 'Vous pouvez continuer à modifier un brouillon existant, le supprimer individuellement, supprimer tous les brouillons, ou créer un nouveau tour en ignorant les brouillons actuels.',

            // Confirmations de suppression
            'confirm_delete_title'       => 'Supprimer ce brouillon ?',
            'confirm_delete_message'     => 'Cette action est irréversible. Le brouillon sera supprimé définitivement :',
            'confirm_delete_all_title'   => 'Supprimer tous les brouillons ?',
            'confirm_delete_all_message' => 'Les :count brouillon(s) seront supprimés définitivement. Cette action est irréversible.',

            // Messages de succès
            'draft_deleted'      => 'Brouillon supprimé avec succès.',
            'all_drafts_deleted' => ':count brouillon(s) supprimé(s) avec succès.',
            'continuing_draft'   => 'Reprise de votre brouillon…',

            // Messages d’erreur
            'not_a_draft' => 'Ce tour n’est plus un brouillon et ne peut pas être modifié via l’assistant.',
        ],

        'title' => 'Tours',

        'fields' => [
            'id'           => 'ID',
            'name'         => 'Nom',
            'details'      => 'Détails',
            'price'        => 'Prix',
            'overview'     => 'Résumé',
            'amenities'    => 'Aménagements',
            'exclusions'   => 'Exclusions',
            'itinerary'    => 'Itinéraire',
            'languages'    => 'Langues',
            'schedules'    => 'Horaires',
            'adult_price'  => 'Prix adulte',
            'kid_price'    => 'Prix enfant',
            'length_hours' => 'Durée (heures)',
            'max_capacity' => 'Capacité maximale',
            'type'         => 'Type de tour',
            'viator_code'  => 'Code Viator',
            'status'       => 'Statut',
            'actions'      => 'Actions',
            'group_size'   => 'Taille du groupe',
        ],

        'pricing' => [
            'configured_categories' => 'Catégories configurées',
            'create_category'       => 'Créer une catégorie',
            'note_title'            => 'Note :',
            'note_text'             => 'Définissez ici les prix de base pour chaque catégorie de client.',
            'manage_detailed_hint'  => ' Pour une gestion détaillée, utilisez le bouton "Gérer les prix détaillés" ci-dessus.',
            'price_usd'             => 'Prix (USD)',
            'min_quantity'          => 'Quantité minimale',
            'max_quantity'          => 'Quantité maximale',
            'status'                => 'Statut',
            'active'                => 'Actif',
            'no_categories'         => 'Aucune catégorie de clients configurée.',
            'create_categories_first' => 'Créer d’abord des catégories',
            'page_title'            => 'Prix - :name',
            'header_title'          => 'Prix : :name',
            'back_to_tours'         => 'Retour aux tours',

            'configured_title'   => 'Catégories et prix configurés',
            'empty_title'        => 'Aucune catégorie n’est configurée pour ce tour.',
            'empty_hint'         => 'Utilisez le formulaire à droite pour ajouter des catégories.',

            'save_changes'       => 'Enregistrer les changements',
            'auto_disable_note'  => 'Les prix à 0 $ sont automatiquement désactivés',

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
            'rule_zero_disable'  => 'Les prix à 0 $ sont automatiquement désactivés',
            'rule_only_active'   => 'Seules les catégories actives apparaissent sur le site public',

            'status_active'           => 'Actif',
            'add_existing_category'   => 'Ajouter une catégorie existante',
            'choose_category_placeholder' => 'Sélectionnez une catégorie…',
            'add_button'              => 'Ajouter',
            'add_existing_hint'       => 'Ajoutez uniquement les catégories de clients nécessaires pour ce tour.',
            'remove_category'         => 'Retirer la catégorie',
            'category_already_added'  => 'Cette catégorie est déjà ajoutée au tour.',
            'no_prices_preview'       => 'Aucun prix n’est encore configuré.',
            'already_added'           => 'Cette catégorie est déjà ajoutée au tour.',
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
                'name'            => 'Ex : Adulte, Enfant, Bébé',
                'age_to_optional' => 'Laisser vide pour "+"',
            ],

            'hints' => [
                'age_to_empty_means_plus' => 'Si vous laissez l’âge maximal vide, il sera interprété comme "+" (par exemple 12+).',
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
            'label_placeholder'      => 'Ex : Matin, Après-midi',
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
            'preview_title'       => 'Aperçu du tour',
            'preview_text_create' => 'Vérifiez toutes les informations avant de créer le tour.',
            'preview_text_update' => 'Vérifiez toutes les informations avant de mettre à jour le tour.',

            'basic_details_title' => 'Détails de base',
            'description_title'   => 'Description',
            'prices_title'        => 'Prix par catégorie',
            'schedules_title'     => 'Horaires',
            'languages_title'     => 'Langues',
            'itinerary_title'     => 'Itinéraire',

            'table' => [
                'category' => 'Catégorie',
                'price'    => 'Prix',
                'min_max'  => 'Min-Max',
                'status'   => 'Statut',
            ],

            'not_specified'      => 'Non spécifié',
            'slug_autogenerated' => 'Sera généré automatiquement',
            'no_description'     => 'Sans description',
            'no_active_prices'   => 'Aucun prix actif configuré',
            'no_languages'       => 'Aucune langue assignée',
            'none_included'      => 'Aucun élément inclus spécifié',
            'none_excluded'      => 'Aucun élément exclu spécifié',

            'units' => [
                'hours'  => 'heures',
                'people' => 'personnes',
            ],

            'create_note' => 'Les horaires, prix, langues et aménagements apparaîtront ici après l’enregistrement du tour.',
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
            'created'      => 'Tour créé avec succès.',
            'updated'      => 'Tour mis à jour avec succès.',
            'deleted_soft' => 'Tour déplacé dans Supprimés.',
            'restored'     => 'Tour restauré avec succès.',
            'purged'       => 'Tour supprimé définitivement.',
            'toggled_on'   => 'Tour activé.',
            'toggled_off'  => 'Tour désactivé.',
        ],

        'table' => [
            'id'           => 'ID',
            'name'         => 'Nom',
            'overview'     => 'Résumé',
            'amenities'    => 'Aménagements',
            'exclusions'   => 'Exclusions',
            'itinerary'    => 'Itinéraire',
            'languages'    => 'Langues',
            'schedules'    => 'Horaires',
            'adult_price'  => 'Prix adulte',
            'kid_price'    => 'Prix enfant',
            'length_hours' => 'Durée (h)',
            'max_capacity' => 'Capacité max.',
            'type'         => 'Type',
            'viator_code'  => 'Code Viator',
            'status'       => 'Statut',
            'actions'      => 'Actions',
            'slug'         => 'URL',
            'prices'       => 'Prix',
            'capacity'     => 'Capacité',
            'group_size'   => 'Taille max. du groupe',
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
            'group_size' => 'Taille du groupe par guide ou générale pour ce tour. (Cette donnée est affichée dans les informations du produit)',
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
            'purged'      => 'Tour supprimé définitivement.',
        ],

        'error' => [
            'create'    => 'Impossible de créer le tour.',
            'update'    => 'Impossible de mettre à jour le tour.',
            'delete'    => 'Impossible de supprimer le tour.',
            'toggle'    => 'Impossible de changer le statut du tour.',
            'not_found' => 'Le tour n’existe pas.',
            'restore'            => 'Impossible de restaurer le tour.',
            'purge'              => 'Impossible de supprimer définitivement le tour.',
            'purge_has_bookings' => 'Suppression définitive impossible : le tour possède des réservations.',
        ],

        'ui' => [
            'add_tour_type' => 'Ajouter un type de tour',
            'back'          => 'Retour',
            'page_title'    => 'Gestion des tours',
            'page_heading'  => 'Gestion des tours',
            'create_title'  => 'Enregistrer un tour',
            'edit_title'    => 'Modifier un tour',
            'delete_title'  => 'Supprimer un tour',
            'cancel'        => 'Annuler',
            'save'          => 'Enregistrer',
            'save_changes'  => 'Enregistrer les changements',
            'update'        => 'Mettre à jour',
            'delete_confirm'=> 'Supprimer ce tour ?',
            'toggle_on'     => 'Activer',
            'toggle_off'    => 'Désactiver',
            'toggle_on_title'   => 'Activer le tour ?',
            'toggle_off_title'  => 'Désactiver le tour ?',
            'toggle_on_button'  => 'Oui, activer',
            'toggle_off_button' => 'Oui, désactiver',
            'see_more'          => 'Voir plus',
            'see_less'          => 'Masquer',
            'load_more'         => 'Charger plus',
            'loading'           => 'Chargement…',
            'load_more_error'   => 'Impossible de charger plus de tours.',
            'confirm_title'     => 'Confirmation',
            'confirm_text'      => 'Souhaitez-vous confirmer cette action ?',
            'yes_confirm'       => 'Oui, confirmer',
            'no_confirm'        => 'Non, annuler',
            'add_tour'          => 'Ajouter un tour',
            'edit_tour'         => 'Modifier le tour',
            'delete_tour'       => 'Supprimer le tour',
            'toggle_tour'       => 'Activer/Désactiver le tour',
            'view_cart'         => 'Voir le panier',
            'add_to_cart'       => 'Ajouter au panier',
            'slug_help'         => 'Identifiant du tour dans l’URL (sans espaces ni accents)',
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
            'amenities_included'     => 'Aménagements inclus',
            'amenities_excluded'     => 'Aménagements non inclus',
            'color'                  => 'Couleur du tour',
            'remove'                 => 'Supprimer',
            'choose_itinerary'       => 'Choisir un itinéraire',
            'select_type'            => 'Sélectionner un type',
            'empty_means_default'    => 'Par défaut',
            'actives'                => 'Actifs',
            'inactives'              => 'Inactifs',
            'archived'               => 'Archivés',
            'all'                    => 'Tous',
            'help_title'             => 'Aide',
            'amenities_included_hint' => 'Sélectionnez ce qui est inclus dans le tour.',
            'amenities_excluded_hint' => 'Sélectionnez ce qui n’est PAS inclus dans le tour.',
            'help_included_title'     => 'Inclus',
            'help_included_text'      => 'Cochez tout ce qui est inclus dans le prix du tour (transport, repas, entrées, équipement, guide, etc.).',
            'help_excluded_title'     => 'Non inclus',
            'help_excluded_text'      => 'Indiquez ce que le client doit payer séparément ou apporter (pourboires, boissons alcoolisées, souvenirs, etc.).',
            'select_or_create_title'  => 'Sélectionner ou créer un itinéraire',
            'select_existing_items'   => 'Sélectionner des éléments existants',
            'name_hint'               => 'Nom identifiant pour cet itinéraire',
            'click_add_item_hint'     => 'Cliquez sur "Ajouter un élément" pour créer de nouveaux éléments',
            'scroll_hint'             => 'Faites défiler horizontalement pour voir plus de colonnes',
            'no_schedules'            => 'Aucun horaire',
            'no_prices'               => 'Aucun prix configuré',
            'edit'                    => 'Modifier',
            'slug_auto'               => 'Sera généré automatiquement',
            'added_to_cart'           => 'Ajouté au panier',
            'add_language'            => 'Ajouter une langue',
            'added_to_cart_text'      => 'Le tour a été ajouté au panier avec succès.',
            'amenities_excluded_auto_hint' => 'Par défaut, nous marquons comme "non inclus" tous les aménagements que vous n’avez pas sélectionnés comme inclus. Vous pouvez décocher ceux qui ne s’appliquent pas au tour.',
            'quick_create_language_hint' => 'Ajoutez rapidement une nouvelle langue si elle n’apparaît pas dans la liste.',
            'quick_create_type_hint'     => 'Ajoutez rapidement un nouveau type de tour s’il n’apparaît pas dans la liste.',

            'none' => [
                'amenities'       => 'Sans aménagements',
                'exclusions'      => 'Sans exclusions',
                'itinerary'       => 'Sans itinéraire',
                'itinerary_items' => 'Sans éléments',
                'languages'       => 'Sans langues',
                'schedules'       => 'Sans horaires',
            ],

            // Actions d’archivage/restauration/purge
            'archive' => 'Archiver',
            'restore' => 'Restaurer',
            'purge'   => 'Supprimer définitivement',

            'confirm_archive_title' => 'Archiver le tour ?',
            'confirm_archive_text'  => 'Le tour ne sera plus disponible pour de nouvelles réservations, mais les réservations existantes seront conservées.',
            'confirm_purge_title'   => 'Supprimer définitivement',
            'confirm_purge_text'    => 'Cette action est irréversible et n’est autorisée que si le tour n’a jamais eu de réservations.',

            // Filtres de statut
            'filters' => [
                'active'   => 'Actifs',
                'inactive' => 'Inactifs',
                'archived' => 'Archivés',
                'all'      => 'Tous',
            ],

            // Barre d’outils de police (tourlist.blade.php)
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
        'upload_none'         => 'Aucune image téléchargée.',
        'upload_truncated'    => 'Certains fichiers ont été ignorés en raison de la limite par tour.',
        'done'                => 'Terminé',
        'notice'              => 'Avis',
        'saved'               => 'Enregistrer',
        'caption_updated'     => 'Légende mise à jour avec succès.',
        'deleted'             => 'Supprimé',
        'image_removed'       => 'Image supprimée avec succès.',
        'invalid_order'       => 'Ordre de chargement invalide.',
        'nothing_to_reorder'  => 'Rien à réorganiser.',
        'order_saved'         => 'Ordre enregistré.',
        'cover_updated_title' => 'Mettre à jour la couverture',
        'cover_updated_text'  => 'Cette image est maintenant la couverture.',
        'deleting'            => 'Suppression…',

        'ui' => [
            // Page de sélection du tour
            'page_title_pick'     => 'Images des tours',
            'page_heading'        => 'Images des tours',
            'choose_tour'         => 'Choisir un tour',
            'search_placeholder'  => 'Rechercher par ID ou nom…',
            'search_button'       => 'Rechercher',
            'no_results'          => 'Aucun tour trouvé.',
            'manage_images'       => 'Gérer les images',
            'cover_alt'           => 'Couverture',
            'images_label'        => 'images',

            // Boutons génériques
            'upload_btn'          => 'Téléverser',
            'delete_btn'          => 'Supprimer',
            'show_btn'            => 'Afficher',
            'close_btn'           => 'Fermer',
            'preview_title'       => 'Aperçu de l’image',

            // Textes généraux de statut
            'error_title'   => 'Erreur',
            'warning_title' => 'Attention',
            'success_title' => 'Succès',
            'cancel_btn'    => 'Annuler',

            // Confirmations basiques
            'confirm_delete_title' => 'Supprimer cette image ?',
            'confirm_delete_text'  => 'Cette action est irréversible.',

            // Gestion de la couverture via formulaire classique
            'cover_current_title'    => 'Couverture actuelle',
            'upload_new_cover_title' => 'Téléverser une nouvelle couverture',
            'cover_file_label'       => 'Fichier de couverture',
            'file_help_cover'        => 'JPEG/PNG/WebP, 30 Mo max.',
            'id_label'               => 'ID',

            // Navigation / en-tête dans la vue d’un tour
            'back_btn'          => 'Retour à la liste',

            // Statistiques (barre supérieure)
            'stats_images'      => 'Images téléversées',
            'stats_cover'       => 'Couvertures définies',
            'stats_selected'    => 'Sélectionnées',

            // Zone de téléversement
            'drag_or_click'     => 'Glissez-déposez vos images ou cliquez pour sélectionner.',
            'upload_help'       => 'Formats autorisés : JPG, PNG, WebP. Taille totale max 100 Mo.',
            'select_btn'        => 'Choisir des fichiers',
            'limit_badge'       => 'Limite de :max images atteinte',
            'files_word'        => 'fichiers',

            // Barre d’outils de sélection multiple
            'select_all'        => 'Tout sélectionner',
            'delete_selected'   => 'Supprimer la sélection',
            'delete_all'        => 'Tout supprimer',

            // Sélecteur par image (chip)
            'select_image_title' => 'Sélectionner cette image',
            'select_image_aria'  => 'Sélectionner l’image :id',

            // Couverture (chip / bouton par carte)
            'cover_label'       => 'Couverture',
            'cover_btn'         => 'Définir comme couverture',

            // États d’enregistrement / helpers JS
            'caption_placeholder' => 'Légende (facultatif)',
            'saving_label'        => 'Enregistrement…',
            'saving_fallback'     => 'Enregistrement…',
            'none_label'          => 'Sans légende',
            'limit_word'          => 'Limite',

            // Confirmations avancées (JS)
            'confirm_set_cover_title' => 'Définir comme couverture ?',
            'confirm_set_cover_text'  => 'Cette image sera la couverture principale du tour.',
            'confirm_btn'             => 'Oui, continuer',

            'confirm_bulk_delete_title' => 'Supprimer les images sélectionnées ?',
            'confirm_bulk_delete_text'  => 'Les images sélectionnées seront supprimées définitivement.',

            'confirm_delete_all_title'  => 'Supprimer toutes les images ?',
            'confirm_delete_all_text'   => 'Toutes les images de ce tour seront supprimées.',

            // Vue sans images
            'no_images' => 'Aucune image pour ce tour pour le moment.',
        ],

        'errors' => [
            'validation'     => 'Les données envoyées ne sont pas valides.',
            'upload_generic' => 'Impossible de téléverser certaines images.',
            'update_caption' => 'Impossible de mettre à jour la légende.',
            'delete'         => 'Impossible de supprimer l’image.',
            'reorder'        => 'Impossible d’enregistrer l’ordre.',
            'set_cover'      => 'Impossible de définir la couverture.',
            'load_list'      => 'Impossible de charger la liste.',
            'too_large'      => 'Le fichier dépasse la taille maximale autorisée. Essayez avec une image plus légère.',
        ],
    ],

    'prices' => [
        'ui' => [
            'page_title'         => 'Prix - :name',
            'header_title'       => 'Prix : :name',
            'back_to_tours'      => 'Retour aux tours',

            'configured_title'   => 'Catégories et prix configurés',
            'empty_title'        => 'Aucune catégorie configurée pour ce tour.',
            'empty_hint'         => 'Utilisez le formulaire à droite pour ajouter des catégories.',

            'save_changes'       => 'Enregistrer les changements',
            'auto_disable_note'  => 'Les prix à 0 $ sont automatiquement désactivés',

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
            'rule_zero_disable'  => 'Les prix à 0 $ sont automatiquement désactivés',
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
            'create_disabled_hint' => 'Si le prix est de 0 $, la catégorie sera créée désactivée',
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
            'success' => 'Opération effectuée avec succès.',
            'error'   => 'Une erreur est survenue.',
        ],

        'js' => [
            'max_ge_min'            => 'Le maximum doit être supérieur ou égal au minimum',
            'auto_disabled_tooltip' => 'Prix à 0 $ – désactivé automatiquement',
            'fix_errors'            => 'Corrigez les quantités minimales et maximales',
        ],

        'quick_category' => [
            'title'                 => 'Créer une catégorie rapide',
            'button'                => 'Nouvelle catégorie',
            'go_to_index'           => 'Voir toutes les catégories',
            'go_to_index_title'     => 'Ouvrir la liste complète des catégories',
            'name_label'            => 'Nom de la catégorie',
            'age_from'              => 'Âge depuis',
            'age_to'                => 'Âge jusqu’à',
            'save'                  => 'Enregistrer la catégorie',
            'cancel'                => 'Annuler',
            'saving'                => 'Enregistrement…',
            'success_title'         => 'Catégorie créée',
            'success_text'          => 'La catégorie a été créée avec succès et ajoutée au tour.',
            'error_title'           => 'Erreur',
            'error_generic'         => 'Un problème est survenu lors de la création de la catégorie.',
            'created_ok'            => 'Catégorie créée avec succès.',
        ],
    ],

    'ajax' => [
        'category_created'   => 'Catégorie créée avec succès',
        'category_error'     => 'Erreur lors de la création de la catégorie',
        'language_created'   => 'Langue créée avec succès',
        'language_error'     => 'Erreur lors de la création de la langue',
        'amenity_created'    => 'Aménagement créé avec succès',
        'amenity_error'      => 'Erreur lors de la création de l’aménagement',
        'schedule_created'   => 'Horaire créé avec succès',
        'schedule_error'     => 'Erreur lors de la création de l’horaire',
        'itinerary_created'  => 'Itinéraire créé avec succès',
        'itinerary_error'    => 'Erreur lors de la création de l’itinéraire',
        'translation_error'  => 'Erreur lors de la traduction',
    ],

    'modal' => [
        'create_category'  => 'Créer une nouvelle catégorie',
        'create_language'  => 'Créer une nouvelle langue',
        'create_amenity'   => 'Créer un nouvel aménagement',
        'create_schedule'  => 'Créer un nouvel horaire',
        'create_itinerary' => 'Créer un nouvel itinéraire',
    ],

    'validation' => [
        'slug_taken'     => 'Ce slug est déjà utilisé',
        'slug_available' => 'Slug disponible',
    ],

    'tour_type' => [
        'fields' => [
            'name'        => 'Nom',
            'description' => 'Description',
            'status'      => 'Statut',
        ],
    ],

];
