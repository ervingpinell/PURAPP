<?php
/*************************************************************
 *  Index (ancres recherchables)
 *  [01] POLICIES LIGNE 20
 *  [02] TOURTYPES LIGNE 139
 *  [03] FAQ LIGNE 198
 *  [04] TRANSLATIONS LIGNE 249
 *  [05] PROMOCODE LIGNE 359
 *  [06] CUT-OFF LIGNE 436
 *************************************************************/

return [

    // =========================================================
    // ==== POLICIES ===========================================
    // =========================================================
    'policies' => [
        // Titres / en-têtes
        'categories_title'        => 'Catégories de politiques',
        'sections_title'          => 'Sections — :policy',

        // Colonnes / champs
        'id'                      => 'ID',
        'internal_name'           => 'Nom interne',
        'title_current_locale'    => 'Titre',
        'validity_range'          => 'Période de validité',
        'valid_from'              => 'Valable à partir du',
        'valid_to'                => 'Valable jusqu’au',
        'status'                  => 'Statut',
        'sections'                => 'Sections',
        'actions'                 => 'Actions',
        'active'                  => 'Actif',
        'inactive'                => 'Inactif',

        // Liste des catégories : actions
        'new_category'            => 'Nouvelle catégorie',
        'view_sections'           => 'Voir les sections',
        'edit'                    => 'Modifier',
        'activate_category'       => 'Activer la catégorie',
        'deactivate_category'     => 'Désactiver la catégorie',
        'delete'                  => 'Supprimer',
        'delete_category_confirm' => 'Supprimer cette catégorie et TOUTES ses sections ?<br>Cette action est irréversible.',
        'no_categories'           => 'Aucune catégorie trouvée.',
        'edit_category'           => 'Modifier la catégorie',

        // Formulaires (catégorie)
        'title_label'             => 'Titre',
        'description_label'       => 'Description',
        'register'                => 'Créer',
        'save_changes'            => 'Enregistrer les modifications',
        'close'                   => 'Fermer',

        // Sections
        'back_to_categories'      => 'Retour aux catégories',
        'new_section'             => 'Nouvelle section',
        'key'                     => 'Clé',
        'order'                   => 'Ordre',
        'activate_section'        => 'Activer la section',
        'deactivate_section'      => 'Désactiver la section',
        'delete_section_confirm'  => 'Voulez-vous vraiment supprimer cette section ?<br>Action irréversible.',
        'no_sections'             => 'Aucune section trouvée.',
        'edit_section'            => 'Modifier la section',
        'internal_key_optional'   => 'Clé interne (optionnel)',
        'content_label'           => 'Contenu',

        // Public
        'page_title'              => 'Politiques',
        'no_policies'             => 'Aucune politique disponible pour le moment.',
        'section'                 => 'Section',
        'cancellation_policy'     => 'Politique d’annulation',
        'refund_policy'           => 'Politique de remboursement',
        'no_cancellation_policy'  => 'Aucune politique d’annulation configurée.',
        'no_refund_policy'        => 'Aucune politique de remboursement configurée.',

        // Messages (catégories)
        'category_created'        => 'Catégorie créée avec succès.',
        'category_updated'        => 'Catégorie mise à jour avec succès.',
        'category_activated'      => 'Catégorie activée avec succès.',
        'category_deactivated'    => 'Catégorie désactivée avec succès.',
        'category_deleted'        => 'Catégorie supprimée avec succès.',

        // --- Nouvelles clés (refactor / utilitaires) ---
        'untitled'                => 'Sans titre',
        'no_content'              => 'Aucun contenu disponible.',
        'display_name'            => 'Nom affiché',
        'name'                    => 'Nom',
        'name_base'               => 'Nom de base',
        'name_base_help'          => 'Identifiant court/slug de la section (interne).',
        'translation_content'     => 'Contenu',
        'locale'                  => 'Langue',
        'save'                    => 'Enregistrer',
        'name_base_label'         => 'Nom de base',
        'translation_name'        => 'Nom traduit',
        'lang_autodetect_hint'    => 'Vous pouvez écrire dans n’importe quelle langue ; détection automatique.',
        'bulk_edit_sections'      => 'Édition rapide des sections',
        'bulk_edit_hint'          => 'Les changements seront enregistrés avec la traduction de la catégorie en cliquant sur « Enregistrer ».',
        'no_changes_made'         => 'Aucune modification effectuée.',
        'no_sections_found'       => 'Aucune section trouvée.',
        'editing_locale'          => 'Édition',

        // Messages (sections)
        'section_created'         => 'Section créée avec succès.',
        'section_updated'         => 'Section mise à jour avec succès.',
        'section_activated'       => 'Section activée avec succès.',
        'section_deactivated'     => 'Section désactivée avec succès.',
        'section_deleted'         => 'Section supprimée avec succès.',

        // Messages génériques du module
        'created_success'         => 'Créé avec succès.',
        'updated_success'         => 'Mis à jour avec succès.',
        'deleted_success'         => 'Supprimé avec succès.',
        'activated_success'       => 'Activé avec succès.',
        'deactivated_success'     => 'Désactivé avec succès.',
        'unexpected_error'        => 'Une erreur inattendue est survenue.',

        // Boutons / SweetAlert
        'create'                  => 'Créer',
        'activate'                => 'Activer',
        'deactivate'              => 'Désactiver',
        'cancel'                  => 'Annuler',
        'ok'                      => 'OK',
        'validation_errors'       => 'Erreurs de validation',
        'error_title'             => 'Erreur',

        // Confirmations spécifiques section
        'confirm_create_section'      => 'Créer cette section ?',
        'confirm_edit_section'        => 'Enregistrer les modifications de cette section ?',
        'confirm_deactivate_section'  => 'Voulez-vous désactiver cette section ?',
        'confirm_activate_section'    => 'Voulez-vous activer cette section ?',
        'confirm_delete_section'      => 'Supprimer cette section ?<br>Action irréversible.',
    ],

    // =========================================================
    // ==== TOURTYPES ==========================================
    // =========================================================
    'tourtypes' => [
        // Titres / en-têtes
        'title'                   => 'Types de circuits',
        'new'                     => 'Ajouter un type de circuit',

        // Colonnes / champs
        'id'                      => 'ID',
        'name'                    => 'Nom',
        'description'             => 'Description',
        'duration'                => 'Durée',
        'status'                  => 'Statut',
        'actions'                 => 'Actions',
        'active'                  => 'Actif',
        'inactive'                => 'Inactif',

        // Boutons / actions
        'register'                => 'Enregistrer',
        'update'                  => 'Mettre à jour',
        'save'                    => 'Enregistrer',
        'close'                   => 'Fermer',
        'cancel'                  => 'Annuler',
        'edit'                    => 'Modifier',
        'delete'                  => 'Supprimer',
        'activate'                => 'Activer',
        'deactivate'              => 'Désactiver',

        // Titres de modale
        'edit_title'              => 'Modifier le type de circuit',
        'create_title'            => 'Créer un type de circuit',

        // Aides / placeholders
        'examples_placeholder'    => 'Ex. : Aventure, Nature, Détente',
        'duration_placeholder'    => 'Ex. : 4 heures, 8 heures',
        'suggested_duration_hint' => 'Format suggéré : « 4 heures », « 8 heures ».',
        'keep_default_hint'       => 'Laissez « 4 heures » si applicable ; modifiable.',
        'optional'                => 'optionnel',

        // Confirmations
        'confirm_delete'          => 'Supprimer « :name » ? Action irréversible.',
        'confirm_activate'        => 'Activer « :name » ?',
        'confirm_deactivate'      => 'Désactiver « :name » ?',

        // Messages (flash)
        'created_success'         => 'Type de circuit créé avec succès.',
        'updated_success'         => 'Type de circuit mis à jour avec succès.',
        'deleted_success'         => 'Type de circuit supprimé avec succès.',
        'activated_success'       => 'Type de circuit activé avec succès.',
        'deactivated_success'     => 'Type de circuit désactivé avec succès.',
        'in_use_error'            => 'Suppression impossible : ce type de circuit est utilisé.',
        'unexpected_error'        => 'Erreur inattendue. Réessayez.',

        // Validation / génériques
        'validation_errors'       => 'Vérifiez les champs surlignés.',
        'error_title'             => 'Erreur',
    ],

    // =========================================================
    // ==== FAQ ================================================
    // =========================================================
    'faq' => [
        // Titre / en-tête
        'title'            => 'Foire aux questions',

        // Champs / colonnes
        'question'         => 'Question',
        'answer'           => 'Réponse',
        'status'           => 'Statut',
        'actions'          => 'Actions',
        'active'           => 'Actif',
        'inactive'         => 'Inactif',

        // Boutons / actions
        'new'              => 'Nouvelle question',
        'create'           => 'Créer',
        'save'             => 'Enregistrer',
        'edit'             => 'Modifier',
        'delete'           => 'Supprimer',
        'activate'         => 'Activer',
        'deactivate'       => 'Désactiver',
        'cancel'           => 'Annuler',
        'close'            => 'Fermer',
        'ok'               => 'OK',

        // UI
        'read_more'        => 'Lire la suite',
        'read_less'        => 'Réduire',

        // Confirmations
        'confirm_create'   => 'Créer cette question ?',
        'confirm_edit'     => 'Enregistrer les modifications ?',
        'confirm_delete'   => 'Supprimer cette question ?<br>Action irréversible.',
        'confirm_activate' => 'Activer cette question ?',
        'confirm_deactivate'=> 'Désactiver cette question ?',

        // Validation / erreurs
        'validation_errors'=> 'Erreurs de validation',
        'error_title'      => 'Erreur',

        // Messages (flash)
        'created_success'      => 'Question créée avec succès.',
        'updated_success'      => 'Question mise à jour avec succès.',
        'deleted_success'      => 'Question supprimée avec succès.',
        'activated_success'    => 'Question activée avec succès.',
        'deactivated_success'  => 'Question désactivée avec succès.',
        'unexpected_error'     => 'Erreur inattendue.',
    ],

    // =========================================================
    // ==== TRANSLATIONS =======================================
    // =========================================================
    'translations' => [
        // Titres / textes généraux
        'title'                 => 'Gestion des traductions',
        'index_title'           => 'Gestion des traductions',
        'select_entity_title'   => 'Sélectionnez :entity à traduire',
        'edit_title'            => 'Modifier la traduction',
        'main_information'      => 'Informations principales',
        'ok'                    => 'OK',
        'save'                  => 'Enregistrer',
        'validation_errors'     => 'Erreurs de validation',
        'updated_success'       => 'Traduction mise à jour avec succès.',
        'unexpected_error'      => 'Impossible de mettre à jour la traduction.',

        // Sélecteur de langue
        'choose_locale_title'   => 'Sélectionner la langue',
        'choose_locale_hint'    => 'Choisissez la langue vers laquelle traduire cet élément.',
        'select_language_title' => 'Sélectionner la langue',
        'select_language_intro' => 'Choisissez la langue vers laquelle traduire cet élément.',
        'languages' => [
            'es' => 'Espagnol',
            'en' => 'Anglais',
            'fr' => 'Français',
            'pt' => 'Portugais',
            'de' => 'Allemand',
        ],

        // Listes / boutons
        'select'                => 'Sélectionner',
        'id_unavailable'        => 'ID indisponible',
        'no_items'              => 'Aucun :entity disponible à traduire.',

        // Champs communs
        'name'                  => 'Nom',
        'description'           => 'Description',
        'content'               => 'Contenu',
        'overview'              => 'Aperçu',
        'itinerary'             => 'Itinéraire',
        'itinerary_name'        => 'Nom de l’itinéraire',
        'itinerary_description' => 'Description de l’itinéraire',
        'itinerary_items'       => 'Éléments de l’itinéraire',
        'item'                  => 'Élément',
        'item_title'            => 'Titre de l’élément',
        'item_description'      => 'Description de l’élément',
        'sections'              => 'Sections',
        'edit'                  => 'Modifier',
        'close'                 => 'Fermer',
        'actions'               => 'Actions',

        // Étiquettes modulaires
        'fields' => [
            'name'                  => 'Nom',
            'title'                 => 'Titre',
            'overview'              => 'Aperçu',
            'description'           => 'Description',
            'content'               => 'Contenu',
            'duration'              => 'Durée',
            'question'              => 'Question',
            'answer'                => 'Réponse',

            'itinerary'             => 'Itinéraire',
            'itinerary_name'        => 'Nom de l’itinéraire',
            'itinerary_description' => 'Description de l’itinéraire',
            'item'                  => 'Élément',
            'item_title'            => 'Titre de l’élément',
            'item_description'      => 'Description de l’élément',
        ],

        // Overrides par ENTITÉ et CHAMP
        'entity_fields' => [
            'tour_types' => [
                'duration' => 'Durée suggérée',
                'name'     => 'Nom du type de circuit',
            ],
            'faqs' => [
                'question' => 'Question (affichée au client)',
                'answer'   => 'Réponse (affichée au client)',
            ],
        ],

        // Noms d’entités (pluriel)
        'entities' => [
            'tours'            => 'Circuits',
            'itineraries'      => 'Itinéraires',
            'itinerary_items'  => 'Éléments d’itinéraire',
            'amenities'        => 'Équipements',
            'faqs'             => 'FAQs',
            'policies'         => 'Politiques',
            'tour_types'       => 'Types de circuit',
        ],

        // Noms d’entités (singulier)
        'entities_singular' => [
            'tours'            => 'circuit',
            'itineraries'      => 'itinéraire',
            'itinerary_items'  => 'élément d’itinéraire',
            'amenities'        => 'équipement',
            'faqs'             => 'FAQ',
            'policies'         => 'politique',
            'tour_types'       => 'type de circuit',
        ],
    ],

    // =========================================================
    // ==== PROMOCODE ==========================================
    // =========================================================
    'promocode' => [
        'title'        => 'Codes promotionnels',
        'create_title' => 'Générer un nouveau code promo',
        'list_title'   => 'Codes promo existants',

        'success_title' => 'Succès',
        'error_title'   => 'Erreur',

        'fields' => [
            'code'        => 'Code',
            'discount'    => 'Remise',
            'type'        => 'Type',
            'valid_from'  => 'Valable à partir du',
            'valid_until' => 'Valable jusqu’au',
            'usage_limit' => 'Limite d’utilisations',
        ],

        'types' => [
            'percent' => '%',
            'amount'  => '$',
        ],

        'symbols' => [
            'percent'  => '%',
            'currency' => '$',
        ],

        'table' => [
            'code'         => 'Code',
            'discount'     => 'Remise',
            'validity'     => 'Validité',
            'date_status'  => 'Statut (date)',
            'usage'        => 'Utilisations',
            'usage_status' => 'Statut (usage)',
            'actions'      => 'Actions',
        ],

        'status' => [
            'used'      => 'Utilisé',
            'available' => 'Disponible',
        ],

        'date_status' => [
            'scheduled' => 'Programmé',
            'active'    => 'Actif',
            'expired'   => 'Expiré',
        ],

        'actions' => [
            'generate' => 'Générer',
            'delete'   => 'Supprimer',
        ],

        'labels' => [
            'unlimited_placeholder' => 'Vide = illimité',
            'unlimited_hint'        => 'Laissez vide pour illimité. Mettez 1 pour un seul usage.',
            'no_limit'              => '(sans limite)',
            'remaining'             => 'restants',
        ],

        'confirm_delete' => 'Voulez-vous supprimer ce code ?',
        'empty'          => 'Aucun code promo disponible.',

        'messages' => [
            'created_success'         => 'Code promo créé avec succès.',
            'deleted_success'         => 'Code promo supprimé avec succès.',
            'percent_over_100'        => 'Le pourcentage ne peut pas dépasser 100.',
            'code_exists_normalized'  => 'Ce code (espaces et casse ignorés) existe déjà.',
            'invalid_or_used'         => 'Code invalide ou déjà utilisé.',
            'valid'                   => 'Code valide.',
            'server_error'            => 'Erreur serveur, réessayez.',
        ],
    ],

    // =========================================================
    // ==== CUTOFF =============================================
    // =========================================================
    'cut-off' => [
        // Titres / en-têtes
        'title'       => 'Paramètres de cut-off',
        'header'      => 'Paramètres de réservation',
        'server_time' => 'Heure du serveur (:tz)',

        // Onglets
        'tabs' => [
            'global'   => 'Global (par défaut)',
            'tour'     => 'Verrouillage par circuit',
            'schedule' => 'Verrouillage par horaire',
            'summary'  => 'Résumé',
            'help'     => 'Aide',
        ],

        // Champs
        'fields' => [
            'cutoff_hour'       => 'Heure de coupure (24h)',
            'cutoff_hour_short' => 'Coupure (24h)',
            'lead_days'         => 'Jours d’anticipation',
            'timezone'          => 'Fuseau horaire',
            'tour'              => 'Circuit',
            'schedule'          => 'Horaire',
        ],

        // Selects / placeholders
        'selects' => [
            'tour' => '— Sélectionnez un circuit —',
            'time' => '— Sélectionnez un horaire —',
        ],

        // Étiquettes
        'labels' => [
            'status' => 'Statut',
        ],

        // Badges / chips
        'badges' => [
            'inherits'            => 'Hérite du global',
            'override'            => 'Verrou',
            'inherit_tour_global' => 'Hérite du circuit/global',
            'schedule'            => 'Horaire',
            'tour'                => 'Circuit',
            'global'              => 'Global',
        ],

        // Actions
        'actions' => [
            'save_global'   => 'Enregistrer global',
            'save_tour'     => 'Enregistrer le verrou du circuit',
            'save_schedule' => 'Enregistrer le verrou de l’horaire',
            'clear'         => 'Effacer le verrou',
            'confirm'       => 'Confirmer',
            'cancel'        => 'Annuler',
            'actions'       => 'Actions',
        ],

        // Confirmations (modales)
        'confirm' => [
            'tour' => [
                'title' => 'Enregistrer le verrou du circuit ?',
                'text'  => 'Un verrou spécifique sera appliqué à ce circuit. Laissez vide pour hériter.',
            ],
            'schedule' => [
                'title' => 'Enregistrer le verrou de l’horaire ?',
                'text'  => 'Un verrou spécifique sera appliqué à cet horaire. Laissez vide pour hériter.',
            ],
        ],

        // Résumé
        'summary' => [
            'tour_title'            => 'Verrous par circuit',
            'no_tour_overrides'     => 'Aucun verrou au niveau circuit.',
            'schedule_title'        => 'Verrous par horaire',
            'no_schedule_overrides' => 'Aucun verrou au niveau horaire.',
            'search_placeholder'    => 'Rechercher un circuit ou un horaire…',
        ],

        // Flash / toasts
        'flash' => [
            'success_title' => 'Succès',
            'error_title'   => 'Erreur',
        ],

        // Aide
        'help' => [
            'title'      => 'Comment ça marche ?',
            'global'     => 'Valeur par défaut pour tout le site.',
            'tour'       => 'Si un circuit a une coupure/jours définis, cela prime sur le global.',
            'schedule'   => 'Si un horaire a un verrou, il prime sur le circuit.',
            'precedence' => 'Priorité',
        ],

        // Aides
        'hints' => [
            // Global
            'cutoff_example'    => 'Ex. : :ex. Après cette heure, « aujourd’hui » n’est plus disponible.',
            'pattern_24h'       => 'Format 24h HH:MM (ex. 09:30, 18:00).',
            'cutoff_behavior'   => 'Si l’heure de coupure est passée, la première date disponible passe au lendemain.',
            'lead_days'         => 'Jours minimum d’anticipation (0 autorise aujourd’hui si la coupure n’est pas passée).',
            'lead_days_detail'  => 'Plage autorisée : 0–30. 0 autorise la réservation le jour même si la coupure n’est pas passée.',
            'timezone_source'   => 'Issu de config(\'app.timezone\').',

            // Circuit
            'pick_tour'             => 'Sélectionnez d’abord un circuit ; définissez ensuite son verrou (optionnel).',
            'tour_override_explain' => 'Si vous ne définissez qu’un seul champ (coupure ou jours), l’autre hérite du global.',
            'clear_button_hint'     => 'Utilisez « Effacer le verrou » pour revenir à l’héritage.',
            'leave_empty_inherit'   => 'Laissez vide pour hériter.',

            // Horaire
            'pick_schedule'             => 'Sélectionnez ensuite l’horaire du circuit.',
            'schedule_override_explain' => 'Les valeurs ici priment sur celles du circuit. Laissez vide pour hériter.',
            'schedule_precedence_hint'  => 'Priorité : Horaire → Circuit → Global.',

            // Résumé
            'dash_means_inherit' => 'Le symbole « — » indique que la valeur est héritée.',
        ],
    ],

];
