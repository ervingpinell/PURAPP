<?php

return [
    // Titres / en-têtes
    'categories_title'        => 'Catégories de politiques',
    'sections_title'          => 'Sections — :policy',

    // Colonnes / champs communs
    'id'                      => 'ID',
    'internal_name'           => 'Nom interne',
    'title_current_locale'    => 'Titre (langue actuelle)',
    'validity_range'          => 'Période de validité',
    'valid_from'              => 'Valable à partir du',
    'valid_to'                => 'Valable jusqu’au',
    'status'                  => 'Statut',
    'sections'                => 'Sections',
    'actions'                 => 'Actions',
    'active'                  => 'Active',
    'inactive'                => 'Inactive',

    // Liste des catégories : actions
    'new_category'            => 'Nouvelle catégorie',
    'view_sections'           => 'Voir les sections',
    'edit'                    => 'Modifier',
    'activate_category'       => 'Activer la catégorie',
    'deactivate_category'     => 'Désactiver la catégorie',
    'delete'                  => 'Supprimer',
    'delete_category_confirm' => 'Supprimer la catégorie et TOUTES ses sections ?',
    'no_categories'           => 'Aucune catégorie enregistrée.',
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
    'delete_section_confirm'  => 'Supprimer cette section ?',
    'no_sections'             => 'Aucune section enregistrée.',
    'edit_section'            => 'Modifier la section',
    'internal_key_optional'   => 'Clé interne (facultatif)',
    'content_label'           => 'Contenu',

    // Public
    'page_title'              => 'Politiques',
    'no_policies'             => 'Aucune politique n’est disponible pour le moment.',
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

    // --- NOUVELLES CLÉS (refactor / utilitaires de module) ---
    'untitled'                => 'Sans titre',
    'no_content'              => 'Aucun contenu disponible.',
    'display_name'            => 'Nom affiché',
    'name'                    => 'Nom',
    'name_base'               => 'Nom de base',
    'name_base_help'          => 'Identifiant court/slug de la section (interne uniquement).',
    'translation_content'     => 'Contenu traduit',
    'locale'                  => 'Langue',
    'save'                    => 'Enregistrer',
    'name_base_label'         => 'Nom de base',
    'translation_name'        => 'Nom traduit',
    'lang_autodetect_hint'    => 'Vous pouvez écrire dans n’importe quelle langue ; la détection est automatique.',
    'bulk_edit_sections'      => 'Édition rapide des sections',
    'bulk_edit_hint'          => 'Les modifications de toutes les sections seront enregistrées avec la traduction de la catégorie lorsque vous cliquerez sur « Enregistrer ».',
    'no_changes_made'         => 'Aucun changement effectué.',
    'no_sections_found'       => 'Aucune section trouvée.',

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
    'validation_errors'       => 'Des erreurs de validation sont survenues',
    'error_title'             => 'Erreur',

    // Confirmations spécifiques aux sections
    'confirm_create_section'      => 'Créer cette section ?',
    'confirm_edit_section'        => 'Enregistrer les modifications de la section ?',
    'confirm_delete_section'      => 'Voulez-vous vraiment supprimer cette section ?',
    'confirm_deactivate_section'  => 'Voulez-vous vraiment désactiver cette section ?',
    'confirm_activate_section'    => 'Voulez-vous vraiment activer cette section ?',
];
