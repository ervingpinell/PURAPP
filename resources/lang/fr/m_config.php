<?php
/*************************************************************
 *  MODULE DE CONFIGURATION – TRADUCTIONS (FR)
 *  Fichier : resources/lang/fr/m_config.php
 *
 *  Index (ancres recherchables)
 *  [01] POLICIES 16
 *  [02] TOURTYPES 134
 *  [03] FAQ 193
 *  [04] TRANSLATIONS 244
 *  [05] PROMOCODE 351
 *************************************************************/

return [

    // =========================================================
    // ==== POLICIES ===========================================
    // =========================================================
    'policies' => [
        // Titres / en-têtes
        'categories_title'        => 'Catégories de politiques',
        'sections_title'          => 'Sections — :policy',

        // Colonnes / champs communs
        'id'                      => 'ID',
        'internal_name'           => 'Nom interne',
        'title_current_locale'    => 'Titre',
        'validity_range'          => 'Période de validité',
        'valid_from'              => 'Valide à partir du',
        'valid_to'                => 'Valide jusqu’au',
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
        'delete_section_confirm'  => 'Voulez-vous vraiment supprimer cette section ?<br>Cette action est irréversible.',
        'no_sections'             => 'Aucune section trouvée.',
        'edit_section'            => 'Modifier la section',
        'internal_key_optional'   => 'Clé interne (facultatif)',
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

        // --- NOUVELLES CLÉS (refactor / utilitaires) ---
        'untitled'                => 'Sans titre',
        'no_content'              => 'Aucun contenu disponible.',
        'display_name'            => 'Nom d’affichage',
        'name'                    => 'Nom',
        'name_base'               => 'Nom de base',
        'name_base_help'          => 'Identifiant/slug court pour la section (interne uniquement).',
        'translation_content'     => 'Contenu',
        'locale'                  => 'Langue',
        'save'                    => 'Enregistrer',
        'name_base_label'         => 'Nom de base',
        'translation_name'        => 'Nom traduit',
        'lang_autodetect_hint'    => 'Vous pouvez écrire dans n’importe quelle langue ; elle sera détectée automatiquement.',
        'bulk_edit_sections'      => 'Édition rapide des sections',
        'bulk_edit_hint'          => 'Les modifications sur toutes les sections seront enregistrées avec la traduction de la catégorie lorsque vous cliquerez sur « Enregistrer ».',
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
        'unexpected_error'        => 'Une erreur inattendue s’est produite.',

        // Boutons / textes communs (SweetAlert)
        'create'                  => 'Créer',
        'activate'                => 'Activer',
        'deactivate'              => 'Désactiver',
        'cancel'                  => 'Annuler',
        'ok'                      => 'OK',
        'validation_errors'       => 'Des erreurs de validation se sont produites',
        'error_title'             => 'Erreur',

        // Confirmations spécifiques aux sections
        'confirm_create_section'      => 'Créer cette section ?',
        'confirm_edit_section'        => 'Enregistrer les modifications de cette section ?',
        'confirm_deactivate_section'  => 'Voulez-vous vraiment désactiver cette section ?',
        'confirm_activate_section'    => 'Voulez-vous vraiment activer cette section ?',
        'confirm_delete_section'      => 'Voulez-vous vraiment supprimer cette section ?<br>Cette action est irréversible.',
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

        // Indices / aides
        'examples_placeholder'    => 'Ex. : Aventure, Nature, Détente',
        'duration_placeholder'    => 'Ex. : 4 heures, 8 heures',
        'suggested_duration_hint' => 'Format suggéré : « 4 heures », « 8 heures ».',
        'keep_default_hint'       => 'Laissez « 4 heures » si applicable ; vous pouvez le modifier.',
        'optional'                => 'facultatif',

        // Confirmations
        'confirm_delete'          => 'Voulez-vous vraiment supprimer « :name » ? Cette action est irréversible.',
        'confirm_activate'        => 'Voulez-vous vraiment activer « :name » ?',
        'confirm_deactivate'      => 'Voulez-vous vraiment désactiver « :name » ?',

        // Messages (flash)
        'created_success'         => 'Type de circuit créé avec succès.',
        'updated_success'         => 'Type de circuit mis à jour avec succès.',
        'deleted_success'         => 'Type de circuit supprimé avec succès.',
        'activated_success'       => 'Type de circuit activé avec succès.',
        'deactivated_success'     => 'Type de circuit désactivé avec succès.',
        'in_use_error'            => 'Suppression impossible : ce type de circuit est utilisé.',
        'unexpected_error'        => 'Une erreur inattendue s’est produite. Veuillez réessayer.',

        // Validation / génériques
        'validation_errors'       => 'Veuillez vérifier les champs mis en évidence.',
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
        'read_less'        => 'Lire moins',

        // Confirmations
        'confirm_create'   => 'Créer cette question ?',
        'confirm_edit'     => 'Enregistrer les modifications de cette question ?',
        'confirm_delete'   => 'Voulez-vous vraiment supprimer cette question ?<br>Cette action est irréversible.',
        'confirm_activate' => 'Voulez-vous vraiment activer cette question ?',
        'confirm_deactivate'=> 'Voulez-vous vraiment désactiver cette question ?',

        // Validation / erreurs
        'validation_errors'=> 'Des erreurs de validation se sont produites',
        'error_title'      => 'Erreur',

        // Messages (flash)
        'created_success'      => 'Question créée avec succès.',
        'updated_success'      => 'Question mise à jour avec succès.',
        'deleted_success'      => 'Question supprimée avec succès.',
        'activated_success'    => 'Question activée avec succès.',
        'deactivated_success'  => 'Question désactivée avec succès.',
        'unexpected_error'     => 'Une erreur inattendue s’est produite.',
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
        'validation_errors'     => 'Des erreurs de validation se sont produites',
        'updated_success'       => 'Traduction mise à jour avec succès.',
        'unexpected_error'      => 'La traduction n’a pas pu être mise à jour.',

        // Sélecteur de langue
        'choose_locale_title'   => 'Choisir la langue',
        'choose_locale_hint'    => 'Sélectionnez la langue dans laquelle vous souhaitez traduire cet élément.',
        'select_language_title' => 'Choisir la langue',
        'select_language_intro' => 'Sélectionnez la langue dans laquelle vous souhaitez traduire cet élément.',
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

        // Champs de formulaire communs
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

        // === Libellés modulaires ==============================
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

            // Itinéraire / éléments
            'itinerary'             => 'Itinéraire',
            'itinerary_name'        => 'Nom de l’itinéraire',
            'itinerary_description' => 'Description de l’itinéraire',
            'item'                  => 'Élément',
            'item_title'            => 'Titre de l’élément',
            'item_description'      => 'Description de l’élément',
        ],

        // === Surcharges par entité (optionnel) ================
        'entity_fields' => [
            'tour_types' => [
                'duration' => 'Durée suggérée',
                'name'     => 'Nom du type de circuit',
            ],
            'faqs' => [
                'question' => 'Question (visible par le client)',
                'answer'   => 'Réponse (visible par le client)',
            ],
        ],

        // Noms d’entités (pluriel)
        'entities' => [
            'tours'            => 'Circuits',
            'itineraries'      => 'Itinéraires',
            'itinerary_items'  => 'Éléments de l’itinéraire',
            'amenities'        => 'Équipements',
            'faqs'             => 'FAQ',
            'policies'         => 'Politiques',
            'tour_types'       => 'Types de circuit',
        ],

        // Noms d’entités (singulier)
        'entities_singular' => [
            'tours'            => 'circuit',
            'itineraries'      => 'itinéraire',
            'itinerary_items'  => 'élément de l’itinéraire',
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
    'create_title' => 'Générer un nouveau code promotionnel',
    'list_title'   => 'Codes promotionnels existants',

    'success_title' => 'Succès',
    'error_title'   => 'Erreur',

    'fields' => [
        'code'        => 'Code',
        'discount'    => 'Remise',
        'type'        => 'Type',
        'valid_from'  => 'Valide à partir du',
        'valid_until' => 'Valide jusqu’au',
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
        'usage_status' => 'Statut (utilisation)',
        'actions'      => 'Actions',
    ],

    'status' => [
        'used'      => 'Utilisé',
        'available' => 'Disponible',
    ],

    'date_status' => [
        'scheduled' => 'Programmé',
        'active'    => 'En vigueur',
        'expired'   => 'Expiré',
    ],

    'actions' => [
        'generate' => 'Générer',
        'delete'   => 'Supprimer',
    ],

    'labels' => [
        'unlimited_placeholder' => 'Vide = illimité',
        'unlimited_hint'        => 'Laissez vide pour un nombre illimité d’utilisations. Indiquez 1 pour un seul usage.',
        'no_limit'              => '(sans limite)',
        'remaining'             => 'restants',
    ],

    'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer ce code ?',
    'empty'          => 'Aucun code promotionnel disponible.',

    'messages' => [
        'created_success'        => 'Code promotionnel créé avec succès.',
        'deleted_success'        => 'Code promotionnel supprimé avec succès.',
        'percent_over_100'       => 'Le pourcentage ne peut pas être supérieur à 100.',
        'code_exists_normalized' => 'Ce code (en ignorant les espaces et la casse) existe déjà.',
        'invalid_or_used'        => 'Code invalide, hors de validité ou sans utilisations restantes.',
        'valid'                  => 'Code valide.',
        'server_error'           => 'Erreur du serveur, réessayez.',
    ],
],

];
