<?php

/*************************************************************
 *  MODULE DE CONFIGURATION – TRADUCTIONS (FR)
 *  Fichier : resources/lang/fr/m_config.php
 *
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
        'categories_title'        => 'Politiques',
        'sections_title'          => 'Sections',

        // Colonnes / champs communs
        'id'                      => 'ID',
        'internal_name'           => 'Nom interne',
        'title_current_locale'    => 'Titre',
        'validity_range'          => 'Période de validité',
        'valid_from'              => 'Valable à partir du',
        'valid_to'                => 'Valable jusqu\'au',
        'status'                  => 'Statut',
        'sections'                => 'Sections',
        'actions'                 => 'Actions',
        'active'                  => 'Actif',
        'inactive'                => 'Inactif',
        'slug'                    => 'URL',
        'slug_hint'               => 'optionnel',
        'slug_auto_hint'          => 'Sera généré automatiquement à partir du nom si laissé vide.',
        'slug_edit_hint'          => 'Modifie l\'URL de la politique. Utilise uniquement des lettres minuscules, des chiffres et des tirets.',
        'updated'                 => 'Politique mise à jour avec succès.',
        'propagate_to_all_langs' => 'Propager ce changement à toutes les langues (EN, FR, DE, PT)',
        'propagate_hint'         => 'Sera automatiquement traduit à partir du texte actuel et écrasera les traductions existantes dans ces langues.',
        'update_base_es'         => 'Mettre à jour également la base (ES)',
        'update_base_hint'       => 'Remplace le nom et le contenu de la politique dans la table de base (espagnol). À utiliser uniquement si tu souhaites modifier aussi le texte original.',
        'filter_active'    => 'Actives',
        'filter_inactive'  => 'Inactives',
        'filter_archived'  => 'Archivées',
        'filter_all'       => 'Toutes',

        'slug_hint'      => 'minuscules, sans espaces, séparées par des tirets',
        'slug_auto_hint' => 'S’il est laissé vide, il sera généré automatiquement à partir du titre.',
        'slug_edit_hint' => 'Modifier cette URL peut affecter les liens publics existants.',

        'valid_from' => 'Valable à partir du',
        'valid_to'   => 'Valable jusqu’au',

        'move_to_trash'  => 'Mettre à la corbeille',
        'in_trash'       => 'Dans la corbeille',
        'moved_to_trash' => 'La catégorie a été déplacée dans la corbeille.',

        'restore_category'         => 'Restaurer',
        'restore_category_confirm' => 'Restaurer cette catégorie et toutes ses sections ?',
        'restored_ok'              => 'La catégorie a été restaurée avec succès.',

        'delete_permanently'         => 'Supprimer définitivement',
        'delete_permanently_confirm' => 'Supprimer définitivement cette catégorie et toutes ses sections ? Cette action est irréversible.',
        'deleted_permanently'        => 'La catégorie et ses sections ont été supprimées définitivement.',
        'restore' => 'Restaurer',
        'force_delete_confirm' => 'Supprimer définitivement cette catégorie et toutes ses sections ? Cette action est irréversible.',
        'created' => 'Catégorie de politique créée avec succès.',

        // Liste de catégories : actions
        'new_category'            => 'Nouvelle catégorie',
        'view_sections'           => 'Voir les sections',
        'edit'                    => 'Modifier',
        'activate_category'       => 'Activer la catégorie',
        'deactivate_category'     => 'Désactiver la catégorie',
        'delete'                  => 'Supprimer',
        'delete_category_confirm' => 'Supprimer cette catégorie et TOUTES ses sections ?<br>Cette action ne peut pas être annulée.',
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
        'delete_section_confirm'  => 'Es-tu sûr(e) de vouloir supprimer cette section ?<br>Cette action ne peut pas être annulée.',
        'no_sections'             => 'Aucune section trouvée.',
        'edit_section'            => 'Modifier la section',
        'internal_key_optional'   => 'Clé interne (optionnel)',
        'content_label'           => 'Contenu',
        'section_content'         => 'Contenu',
        'base_content_hint'       => 'Ceci est le texte principal de la politique. Il sera automatiquement traduit dans d\'autres langues lors de la création, mais tu peux ensuite modifier chaque traduction.',

        // Public
        'page_title'              => 'Politiques',
        'no_policies'             => 'Aucune politique n\'est disponible pour le moment.',
        'section'                 => 'Section',
        'cancellation_policy'     => 'Politique d\'annulation',
        'refund_policy'           => 'Politique de remboursement',
        'no_cancellation_policy'  => 'Aucune politique d\'annulation configurée.',
        'no_refund_policy'        => 'Aucune politique de remboursement configurée.',

        // Messages (catégories)
        'category_created'        => 'Catégorie créée avec succès.',
        'category_updated'        => 'Catégorie mise à jour avec succès.',
        'category_activated'      => 'Catégorie activée avec succès.',
        'category_deactivated'    => 'Catégorie désactivée avec succès.',
        'category_deleted'        => 'Catégorie supprimée avec succès.',

        // --- NOUVELLES CLÉS (refactor / utilitaires) ---
        'untitled'                => 'Sans titre',
        'no_content'              => 'Aucun contenu disponible.',
        'display_name'            => 'Nom affiché',
        'name'                    => 'Nom',
        'name_base'               => 'Nom de base',
        'name_base_help'          => 'Identifiant court/slug de la section (usage interne uniquement).',
        'translation_content'     => 'Contenu',
        'locale'                  => 'Langue',
        'save'                    => 'Enregistrer',
        'name_base_label'         => 'Nom de base',
        'translation_name'        => 'Nom traduit',
        'lang_autodetect_hint'    => 'Tu peux écrire dans n\'importe quelle langue ; elle sera détectée automatiquement.',
        'bulk_edit_sections'      => 'Édition rapide des sections',
        'bulk_edit_hint'          => 'Les modifications de toutes les sections seront enregistrées avec la traduction de la catégorie lorsque tu cliqueras sur « Enregistrer ».',
        'no_changes_made'         => 'Aucun changement n\'a été effectué.',
        'no_sections_found'       => 'Aucune section trouvée.',
        'editing_locale'          => 'Édition de',

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
        'unexpected_error'        => 'Une erreur inattendue s\'est produite.',

        // Boutons / textes communs (SweetAlert)
        'create'                  => 'Créer',
        'activate'                => 'Activer',
        'deactivate'              => 'Désactiver',
        'cancel'                  => 'Annuler',
        'ok'                      => 'OK',
        'validation_errors'       => 'Il y a des erreurs de validation',
        'error_title'             => 'Erreur',

        // Confirmations spécifiques aux sections
        'confirm_create_section'      => 'Créer cette section ?',
        'confirm_edit_section'        => 'Enregistrer les modifications de cette section ?',
        'confirm_deactivate_section'  => 'Es-tu sûr(e) de vouloir désactiver cette section ?',
        'confirm_activate_section'    => 'Es-tu sûr(e) de vouloir activer cette section ?',
        'confirm_delete_section'      => 'Es-tu sûr(e) de vouloir supprimer cette section ?<br>Cette action ne peut pas être annulée.',
    ],

    // =========================================================
    // ==== TOURTYPES ==========================================
    // =========================================================
    'tourtypes' => [
        // Titres / en-têtes
        'title'                   => 'Types de tours',
        'new'                     => 'Ajouter un type de tour',

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
        'edit_title'              => 'Modifier le type de tour',
        'create_title'            => 'Créer un type de tour',

        // Placeholders / aides
        'examples_placeholder'    => 'Ex. : Aventure, Nature, Détente',
        'duration_placeholder'    => 'Ex. : 4 heures, 8 heures',
        'suggested_duration_hint' => 'Format suggéré : « 4 heures », « 8 heures ».',
        'keep_default_hint'       => 'Laisse « 4 heures » si c\'est approprié ; tu peux le modifier.',
        'optional'                => 'optionnel',

        // Confirmations
        'confirm_delete'          => 'Es-tu sûr(e) de vouloir supprimer « :name » ? Cette action ne peut pas être annulée.',
        'confirm_activate'        => 'Es-tu sûr(e) de vouloir activer « :name » ?',
        'confirm_deactivate'      => 'Es-tu sûr(e) de vouloir désactiver « :name » ?',

        // Messages (flash)
        'created_success'         => 'Type de tour créé avec succès.',
        'updated_success'         => 'Type de tour mis à jour avec succès.',
        'deleted_success'         => 'Type de tour supprimé avec succès.',
        'activated_success'       => 'Type de tour activé avec succès.',
        'deactivated_success'     => 'Type de tour désactivé avec succès.',
        'in_use_error'            => 'Impossible de supprimer : ce type de tour est utilisé.',
        'unexpected_error'        => 'Une erreur inattendue s\'est produite. Merci de réessayer.',

        // Validation / génériques
        'validation_errors'       => 'Merci de vérifier les champs surlignés.',
        'error_title'             => 'Erreur',
    ],

    // =========================================================
    // ==== FAQ ================================================
    // =========================================================
    'faq' => [
        // Titre / en-tête
        'title'            => 'FAQ',

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
        'read_more'        => 'Lire plus',
        'read_less'        => 'Lire moins',

        // Confirmations
        'confirm_create'   => 'Créer cette question fréquente ?',
        'confirm_edit'     => 'Enregistrer les modifications de cette question fréquente ?',
        'confirm_delete'   => 'Es-tu sûr(e) de vouloir supprimer cette question fréquente ?<br>Cette action ne peut pas être annulée.',
        'confirm_activate' => 'Es-tu sûr(e) de vouloir activer cette question fréquente ?',
        'confirm_deactivate' => 'Es-tu sûr(e) de vouloir désactiver cette question fréquente ?',

        // Validation / erreurs
        'validation_errors' => 'Il y a des erreurs de validation',
        'error_title'      => 'Erreur',

        // Messages (flash)
        'created_success'      => 'Question fréquente créée avec succès.',
        'updated_success'      => 'Question fréquente mise à jour avec succès.',
        'deleted_success'      => 'Question fréquente supprimée avec succès.',
        'activated_success'    => 'Question fréquente activée avec succès.',
        'deactivated_success'  => 'Question fréquente désactivée avec succès.',
        'unexpected_error'     => 'Une erreur inattendue s\'est produite.',
    ],

    // =========================================================
    // ==== TRANSLATIONS =======================================
    // =========================================================
    'translations' => [
        // Titres / textes généraux
        'title'                 => 'Gestion des traductions',
        'index_title'           => 'Gestion des traductions',
        'select_entity_title'   => 'Sélectionner :entity à traduire',
        'edit_title'            => 'Modifier la traduction',
        'main_information'      => 'Informations principales',
        'ok'                    => 'OK',
        'save'                  => 'Enregistrer',
        'validation_errors'     => 'Il y a des erreurs de validation',
        'updated_success'       => 'Traduction mise à jour avec succès.',
        'unexpected_error'      => 'La traduction n\'a pas pu être mise à jour.',

        'editing'            => 'Édition',
        'policy_name'        => 'Nom de la politique',
        'policy_content'     => 'Contenu',
        'policy_sections'    => 'Sections de la politique',
        'section'            => 'Section',
        'section_name'       => 'Nom de la section',
        'section_content'    => 'Contenu de la section',

        // Sélecteur de langue (écran et aides)
        'choose_locale_title'   => 'Sélectionner la langue',
        'choose_locale_hint'    => 'Sélectionne la langue dans laquelle tu souhaites traduire cet élément.',
        'select_language_title' => 'Sélectionner la langue',
        'select_language_intro' => 'Sélectionne la langue dans laquelle tu souhaites traduire cet élément.',
        'languages' => [
            'es' => 'Espagnol',
            'en' => 'Anglais',
            'fr' => 'Français',
            'pt' => 'Portugais',
            'de' => 'Allemand',
        ],

        // Listes / boutons
        'select'                => 'Sélectionner',
        'id_unavailable'        => 'ID non disponible',
        'no_items'              => 'Aucun :entity disponible à traduire.',

        // Champs communs des formulaires de traduction
        'name'                  => 'Nom',
        'description'           => 'Description',
        'content'               => 'Contenu',
        'overview'              => 'Aperçu',
        'itinerary'             => 'Itinéraire',
        'itinerary_name'        => 'Nom de l\'itinéraire',
        'itinerary_description' => 'Description de l\'itinéraire',
        'itinerary_items'       => 'Éléments de l\'itinéraire',
        'item'                  => 'Élément',
        'item_title'            => 'Titre de l\'élément',
        'item_description'      => 'Description de l\'élément',
        'sections'              => 'Sections',
        'edit'                  => 'Modifier',
        'close'                 => 'Fermer',
        'actions'               => 'Actions',

        // === Libellés MODULAIRES par champ ====================
        // Utilisation : __('m_config.translations.fields.<champ>')
        'fields' => [
            // Génériques
            'name'                  => 'Nom',
            'title'                 => 'Titre',
            'overview'              => 'Aperçu',
            'description'           => 'Description',
            'content'               => 'Contenu',
            'duration'              => 'Durée',
            'question'              => 'Question',
            'answer'                => 'Réponse',

            // Itinéraire / éléments (partial de tours)
            'itinerary'             => 'Itinéraire',
            'itinerary_name'        => 'Nom de l\'itinéraire',
            'itinerary_description' => 'Description de l\'itinéraire',
            'item'                  => 'Élément',
            'item_title'            => 'Titre de l\'élément',
            'item_description'      => 'Description de l\'élément',
        ],

        // === Overrides par ENTITÉ et CHAMP (optionnel) ========
        // Dans le blade : cherche d\'abord entity_fields.<type>.<field>,
        // sinon utilise fields.<field>.
        'entity_fields' => [
            'tour_types' => [
                'duration' => 'Durée suggérée',
                'name'     => 'Nom du type de tour',
            ],
            'faqs' => [
                'question' => 'Question (visible par le client)',
                'answer'   => 'Réponse (visible par le client)',
            ],
        ],

        // Noms des entités (pluriel)
        'entities' => [
            'tours'            => 'Tours',
            'itineraries'      => 'Itinéraires',
            'itinerary_items'  => 'Éléments d\'itinéraire',
            'amenities'        => 'Équipements',
            'faqs'             => 'Questions fréquentes',
            'policies'         => 'Politiques',
            'tour_types'       => 'Types de tour',
        ],

        // Noms des entités (singulier)
        'entities_singular' => [
            'tours'            => 'tour',
            'itineraries'      => 'itinéraire',
            'itinerary_items'  => 'élément d\'itinéraire',
            'amenities'        => 'équipement',
            'faqs'             => 'question fréquente',
            'policies'         => 'politique',
            'tour_types'       => 'type de tour',
        ],
    ],

    // =========================================================
    // ==== PROMOCODE ==========================================
    // =========================================================
    'promocode' => [
        'title'        => 'Codes promotionnels',
        'create_title' => 'Générer un nouveau code promotionnel',
        'list_title'   => 'Codes promotionnels existants',

        'success_title' => 'Succès',
        'error_title'   => 'Erreur',

        'fields' => [
            'code'        => 'Code',
            'discount'    => 'Montant',

            'type'        => 'Type',
            'operation'   => 'Opération',
            'valid_from'  => 'Valable à partir du',
            'valid_until' => 'Valable jusqu\'au',
            'usage_limit' => 'Limite d\'utilisation',
            'promocode_hint'        => 'Après application, le coupon sera enregistré à l\'envoi du formulaire et les instantanés de l\'historique seront mis à jour.',
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
            'discount'     => 'Montant',
            'operation'    => 'Opération',
            'validity'     => 'Validité',
            'date_status'  => 'Statut (date)',
            'usage'        => 'Utilisations',
            'usage_status' => 'Statut (utilisation)',
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
            'toggle_operation' => 'Basculer entre Ajouter/Soustraire',
        ],

        'labels' => [
            'unlimited_placeholder' => 'Vide = illimité',
            'unlimited_hint'        => 'Laisser vide pour une utilisation illimitée. Mets 1 pour une seule utilisation.',
            'no_limit'              => '(sans limite)',
            'remaining'             => 'restant(s)',
        ],

        'confirm_delete' => 'Es-tu sûr(e) de vouloir supprimer ce code ?',
        'empty'          => 'Aucun code promotionnel disponible.',

        'messages' => [
            'created_success'         => 'Code promotionnel créé avec succès.',
            'deleted_success'         => 'Code promotionnel supprimé avec succès.',
            'percent_over_100'        => 'Le pourcentage ne peut pas être supérieur à 100.',
            'code_exists_normalized'  => 'Ce code (en ignorant les espaces et la casse) existe déjà.',
            'invalid_or_used'         => 'Code invalide ou déjà utilisé.',
            'valid'                   => 'Code valide.',
            'server_error'            => 'Erreur serveur, merci de réessayer.',
            'operation_updated'       => 'Opération mise à jour avec succès.',
        ],

        'operations' => [
            'add'            => 'Ajouter',
            'subtract'       => 'Soustraire',
            'make_add'       => 'Passer à « Ajouter »',
            'make_subtract'  => 'Passer à « Soustraire »',
            'surcharge'      => 'Supplément',
            'discount'       => 'Réduction',
        ],
    ],

    // =========================================================
    // ==== CUTOFF =============================================
    // =========================================================
    'cut-off' => [
        // Titres / en-têtes
        'title'       => 'Cut-off',
        'header'      => 'Configuration du Cut-off',
        'server_time' => 'Heure du serveur (:tz)',

        // Onglets
        'tabs' => [
            'global'   => 'Global (par défaut)',
            'tour'     => 'Blocage par tour',
            'schedule' => 'Blocage par horaire',
            'summary'  => 'Résumé',
            'help'     => 'Aide',
        ],

        // Champs
        'fields' => [
            'cutoff_hour'       => 'Heure de cut-off (24h)',
            'cutoff_hour_short' => 'Cut-off (24h)',
            'lead_days'         => 'Jours d\'anticipation',
            'timezone'          => 'Fuseau horaire',
            'tour'              => 'Tour',
            'schedule'          => 'Horaire',
            'actions'           => 'Actions'
        ],

        // Sélecteurs / placeholders
        'selects' => [
            'tour' => '— Sélectionner un tour —',
            'time' => '— Sélectionner un horaire —',
        ],

        // Libellés
        'labels' => [
            'status' => 'Statut',
        ],

        // Badges / chips
        'badges' => [
            'inherits'            => 'Hérite du global',
            'override'            => 'Blocage',
            'inherit_tour_global' => 'Hérite du tour/global',
            'schedule'            => 'Horaire',
            'tour'                => 'Tour',
            'global'              => 'Global',
        ],

        // Actions
        'actions' => [
            'save_global'   => 'Enregistrer global',
            'save_tour'     => 'Enregistrer le blocage du tour',
            'save_schedule' => 'Enregistrer le blocage de l\'horaire',
            'clear'         => 'Effacer le blocage',
            'confirm'       => 'Confirmer',
            'cancel'        => 'Annuler',
        ],

        // Confirmations (modales)
        'confirm' => [
            'tour' => [
                'title' => 'Enregistrer le blocage du tour ?',
                'text'  => 'Un blocage spécifique sera appliqué pour ce tour. Laisse vide pour hériter.',
            ],
            'schedule' => [
                'title' => 'Enregistrer le blocage de l\'horaire ?',
                'text'  => 'Un blocage spécifique sera appliqué pour cet horaire. Laisse vide pour hériter.',
            ],
        ],

        // Résumé
        'summary' => [
            'tour_title'            => 'Blocages par tour',
            'no_tour_overrides'     => 'Aucun blocage au niveau du tour.',
            'schedule_title'        => 'Blocages par horaire',
            'no_schedule_overrides' => 'Aucun blocage au niveau de l\'horaire.',
            'search_placeholder'    => 'Rechercher un tour ou un horaire…',
        ],

        // Flash / toasts
        'flash' => [
            'success_title' => 'Succès',
            'error_title'   => 'Erreur',
        ],

        // Aide
        'help' => [
            'title'      => 'Comment ça fonctionne ?',
            'global'     => 'Valeur par défaut pour tout le site.',
            'tour'       => 'Si un tour a un cut-off/jours configurés, il a priorité sur le global.',
            'schedule'   => 'Si un horaire du tour a un blocage, il a priorité sur le tour.',
            'precedence' => 'Priorité',
        ],

        // Indications / hints
        'hints' => [
            // Utilisés dans Global
            'cutoff_example'    => 'Ex. : :ex. Après cette heure, « aujourd\'hui » n\'est plus disponible.',
            'pattern_24h'       => 'Format 24h HH:MM (ex. 09:30, 18:00).',
            'cutoff_behavior'   => 'Si l\'heure de cut-off est déjà passée, la date disponible la plus proche passe au lendemain.',
            'lead_days'         => 'Nombre minimum de jours d\'anticipation (0 permet de réserver pour aujourd\'hui si l\'heure de cut-off n\'est pas passée).',
            'lead_days_detail'  => 'Plage autorisée : 0–30. 0 permet de réserver le jour même si l\'heure de cut-off n\'est pas atteinte.',
            'timezone_source'   => 'Utilise config(\'app.timezone\').',

            // Utilisés dans Tour
            'pick_tour'             => 'Sélectionne d\'abord un tour, puis définis son blocage (optionnel).',
            'tour_override_explain' => 'Si tu définis seulement une valeur (cut-off ou jours), l\'autre hérite de la valeur globale.',
            'clear_button_hint'     => 'Utilise « Effacer le blocage » pour revenir à l\'héritage.',
            'leave_empty_inherit'   => 'Laisse vide pour hériter.',

            // Utilisés dans Horaire (schedule)
            'pick_schedule'             => 'Sélectionne ensuite l\'horaire du tour.',
            'schedule_override_explain' => 'Les valeurs définies ici ont priorité sur celles du tour. Laisse vide pour hériter.',
            'schedule_precedence_hint'  => 'Priorité : Horaire → Tour → Global.',

            // Utilisés dans Résumé
            'dash_means_inherit' => 'Le symbole « — » indique que la valeur est héritée.',
        ],
    ],

];
