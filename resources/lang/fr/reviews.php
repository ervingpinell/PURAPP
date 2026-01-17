<?php

return [
    'no_reviews' => 'No reviews available',

    'what_visitors_say' => 'What do our customers say?',
    'powered_by'        => 'Powered by',

    'generic' => [
        'our_tour' => 'our tour',
    ],

    // =========================
    // Common
    // =========================
    'common' => [
        'reviews'   => 'Reviews',
        'provider'  => 'Provider',
        'status'    => 'Status',
        'tour'      => 'Tour',
        'rating'    => 'Note',
        'title'     => 'Titre',
        'body'      => 'Contenu',
        'author'    => 'Auteur',
        'actions'   => 'Actions',
        'filter'    => 'Filtrer',
        'search'    => 'Rechercher',
        'id'        => 'ID',
        'public'    => 'Public',
        'private'   => 'Privé',
        'back'      => 'Retour',
        'save'      => 'Enregistrer',
        'create'    => 'Créer',
        'edit'      => 'Modifier',
        'delete'    => 'Supprimer',
        'publish'   => 'Publier',
        'hide'      => 'Masquer',
        'flag'      => 'Signaler',
        'unflag'    => 'Retirer le signalement',
        'apply'     => 'Appliquer',
        'yes'       => 'Oui',
        'no'        => 'Non',
        'not_found' => 'No results were found.',
        'clear'     => 'Clear',
        'language'  => 'Langue',

        // 🔹 Keys added for Providers panel
        'new'             => 'Nouveau',
        'name'            => 'Nom',
        'active'          => 'Actif',
        'inactive'        => 'Inactif',
        'indexable'       => 'Indexable',
        'indexable_yes'   => 'Inclut le balisage indexable/JSON-LD',
        'indexable_no'    => 'Non indexable',
        'activate'        => 'Activer le fournisseur',
        'deactivate'      => 'Désactiver le fournisseur',
        'activate_title'  => 'Activer le fournisseur ?',
        'activate_text'   => 'Le fournisseur sera activé.',
        'deactivate_title' => 'Désactiver le fournisseur ?',
        'deactivate_text' => 'Le fournisseur ne sera plus actif.',
        'cancel'          => 'Annuler',
        'test'            => 'Tester la connexion',
        'flush_cache'     => 'Vider le cache',
        'delete_confirm'  => 'Supprimer le fournisseur ?',
        'system_locked'   => 'Fournisseur système (verrouillé)',
    ],

    // =========================
    // Review statuses (moderation)
    // =========================
    'status' => [
        'pending'   => 'pending',
        'published' => 'published',
        'hidden'    => 'hidden',
        'flagged'   => 'flagged',
    ],

    // =========================
    // Admin - list / moderation
    // =========================
    'admin' => [
        'index_title'   => 'Reviews',
        'index_titel'   => 'Reviews', // alias for common typo

        'new_local'     => 'Nouvel avis (local)',
        'bulk_apply'    => 'Appliquer à la sélection',
        'external_provider_note' => 'Note du fournisseur externe',

        'responded'     => 'Répondu ?',
        'last_reply'    => 'Dernière :',

        'filters'       => [
            'provider'  => 'Fournisseur',
            'status'    => 'Statut',
            'tour_id'   => 'ID du tour',
            'stars'     => '⭐',
            'q'         => 'Recherche texte/auteur…',
            'responded' => 'Répondu ?',
        ],

        'table' => [
            'date'     => 'Date',
            'review'   => 'Avis',
            'client'   => 'Client',
            'tour'     => 'Tour',
        ],

        'messages' => [
            'created'        => 'Review created.',
            'updated'        => 'Review updated.',
            'deleted'        => 'Review deleted.',
            'published'      => 'Review published.',
            'hidden'         => 'Review hidden.',
            'flagged'        => 'Review flagged.',
            'unflagged'      => 'Review unflagged.',
            'bulk_published' => ':n reviews published.',
            'bulk_hidden'    => ':n reviews hidden.',
            'bulk_flagged'   => ':n reviews flagged.',
            'bulk_deleted'   => ':n reviews deleted.',
            'publish_min_rating' => 'Cannot publish because the rating (:rating★) is lower than the allowed minimum (:min★).',
            'bulk_published_partial' => ':ok reviews published. :skipped skipped because their rating was lower than :min★.',
        ],
    ],

    // =========================
    // Admin - replies
    // =========================
    'replies' => [
        'reply'            => 'Répondre',
        'title_create'     => 'Reply — Review #:id',
        'label_body'       => 'Reply',
        'label_is_public'  => 'Public',
        'label_notify'     => 'Send email to customer',
        'notify_to'        => 'It will be sent to: :email',
        'warn_no_email'    => 'Avertissement : nous n\'avons pas trouvé d\'adresse e-mail pour le client dans cet avis. La réponse sera enregistrée, mais aucun e-mail ne sera envoyé.',
        'saved_notified'   => 'Reply published and emailed to :email.',
        'saved_no_email'   => 'Reply published. No email was sent because no recipient was found.',
        'deleted'          => 'Reply deleted.',
        'visibility_ok'    => 'Visibility updated.',
        'thread_title'     => 'Conversation — Review #:id',
        'thread_empty'     => 'No replies.',
        'last_reply'       => 'Dernière:',
    ],

    // =========================
    // Admin - post-purchase review requests
    // =========================
    'requests' => [
        'index_title' => 'Demander des avis',
        'subtitle'    => 'Envoyer des liens d\'avis post-achat et gérer les demandes déjà envoyées.',

        // Tabs
        'tabs' => [
            'eligible'  => 'Éligibles (réservations)',
            'requested' => 'Demandées (envoyées)',
        ],

        // Filters
        'filters' => [
            'q_placeholder' => 'ID, nom ou e-mail',
            'any_status'    => '— Tout —',
            'from'          => 'De',
            'to'            => 'À',
        ],

        'window_days'      => 'Fenêtre (jours)',
        'date_column'      => 'Colonne de date',
        'date_options'     => [
            'created_at' => 'Réservation créée',
            'tour_date'  => 'Date du tour',
        ],
        'calculated_range' => 'Plage calculée',
        'tour_id'          => 'ID du tour',
        'btn_request'      => 'Demander un avis',
        'no_eligible'      => 'Aucune réservation éligible.',

        'table' => [
            'booking'      => 'Réservation',
            'reference'    => 'Référence',
            'sent_at'      => 'Envoyé le',
            'states'       => 'États',
            'expires_days' => 'Expire (jours)',
        ],

        'labels' => [
            'expires_in_days' => 'Expiration (jours)',
            'expires_at'      => 'Expire le',
            'used_at'         => 'Utilisé le',
        ],

        'actions' => [
            'resend'         => 'Renvoyer',
            'confirm_delete' => 'Supprimer cette demande ?',
        ],

        'status' => [
            'active'    => 'Actif',
            'sent'      => 'Envoyé',
            'reminded'  => 'Rappelé',
            'used'      => 'Utilisé',
            'expired'   => 'Expiré',
            'cancelled' => 'Annulé',
        ],

        'status_labels' => [
            'created'   => 'créé',
            'sent'      => 'envoyé',
            'reminded'  => 'rappelé',
            'fulfilled' => 'complété',
            'expired'   => 'expiré',
            'cancelled' => 'annulé',
            'active'    => 'actif',
        ],

        'send_ok'   => 'Demande d\'avis envoyée.',
        'resend_ok' => 'Demande renvoyée.',
        'remind_ok' => 'Rappel envoyé.',
        'expire_ok' => 'Demande expirée.',
        'deleted'   => 'Demande supprimée.',
        'none'      => 'Aucune demande.',

        'errors' => [
            'used'    => 'Cette demande a déjà été utilisée.',
            'expired' => 'Cette demande a expiré.',
        ],
        'no_requests' => 'Aucune demande trouvée.',
    ],

    // =========================
    // Public (review form)
    // =========================
    'public' => [
        'form_title'   => 'Leave a review',
        'labels'       => [
            'rating'       => 'Rating',
            'title'        => 'Title (optional)',
            'body'         => 'Your experience',
            'author_name'  => 'Your name (optional)',
            'author_email' => 'Your email (optional)',
            'submit'       => 'Submit review',
        ],
        'thanks'       => 'Thank you for your review! 🌿',
        'thanks_body'  => 'Your opinion is very important and helps us improve. We truly appreciate it.',
        'thanks_farewell' => "We hope you enjoyed your time with us and we hope to see you again soon.\n\n🇨🇷 Pura Vida mae! 🇨🇷",
        'thanks_dup'   => 'Thank you! We already had your review on file 🙌',
        'expired'      => 'This link has expired, but thank you so much for your intention 💚',
        'used'         => 'This request has already been used.',
        'used_help'    => 'This review link has already been used. If you think this is an error or want to update your comment, contact us and we will gladly help you.',
        'not_found'    => 'Request not found.',
        'back_home'    => 'Go back',
    ],

    // =========================
    // Emails
    // =========================
    'emails' => [

        'brand_from'   => config('app.name', 'Green Vacations CR'),
        'contact_line' => 'If you need help, contact us at :email or :phone. Visit us at :url.',
        'request' => [
            'preheader_with_date' => 'Tell us about your experience on :tour (:date). It only takes a minute.',
            'preheader'           => 'Tell us about your experience on :tour. It only takes a minute.',
            'subject'   => 'How was your experience on :tour?',
            'cta'       => 'Leave my review',
            'footer'    => 'Thank you for supporting local tourism. We hope to see you back soon! 🌿',
            'expires'   => '* This link will be active until: :date.',
            'greeting'  => 'Hi :name,',
            'intro'     => 'Pura vida! 🙌 Thank you for choosing us. We would love to know how your experience on :tour was.',
            'ask'       => 'Would you give us 1–2 minutes to leave your review? It really means a lot.',
            'fallback'  => 'If the button does not work, copy and paste this link into your browser:',
        ],
        'reply' => [
            'subject'  => 'Reply to your review',
            'greeting' => 'Hi :name,',
            'intro'    => 'Our team has replied to your review :extra.',
            'quote'    => '“:text”',
            'sign'     => '— :admin',
            'closing'  => 'Si vous avez des questions ou souhaitez développer votre commentaire, il vous suffit de répondre à cet e-mail. Pura vida ! 🌿',
        ],
        'submitted' => [
            'subject' => 'New review received',
        ],
    ],

    // =========================
    // Front
    // =========================
    'front' => [
        'see_more'   => 'See more reviews',
        'no_reviews' => 'There are no reviews yet.',
    ],

    // =========================
    // Providers
    // =========================
    'providers' => [
        'index_title' => 'Fournisseurs d\'avis',
        'indexable' => 'Indexable',
        'cache_ttl' => 'TTL Cache (sec)',
        'back' => 'Retour',
        'actions' => 'Actions',
        'system_locked' => 'Fournisseur Système',
        'messages' => [
            'cannot_delete_local' => 'Le fournisseur "local" est un fournisseur système et ne peut pas être supprimé.',
            'created'        => 'Fournisseur créé.',
            'updated'        => 'Fournisseur mis à jour.',
            'deleted'        => 'Fournisseur supprimé.',
            'status_updated' => 'Statut mis à jour.',
            'cache_flushed'  => 'Cache vidé.',
            'test_fetched'   => ':n avis récupérés.',
            'mapping_added'   => 'Mappage ajouté avec succès.',
            'mapping_updated' => 'Mappage mis à jour avec succès.',
            'mapping_deleted' => 'Mappage supprimé avec succès.',
        ],
        'product_map' => [
            'title' => 'Mappage de Produits - :provider',
        ],
        'product_mapping_title' => 'Mappage de Produits - :name',
        'product_mappings' => 'Mappages de Produits',
        'tour' => 'Tour',
        'select_tour' => 'Sélectionner un tour',
        'select_tour_placeholder' => 'Sélectionnez un tour...',
        'product_code' => 'Code produit',
        'product_code_placeholder' => 'Ex: 12732-ABC',
        'add_mapping' => 'Ajouter un mappage',
        'no_mappings' => 'Aucun mappage configuré',
        'confirm_delete_mapping' => 'Êtes-vous sûr de vouloir supprimer ce mappage ?',
        'help_title' => 'Aide',
        'help_text' => 'Mappez les codes produits externes aux tours internes pour synchroniser correctement les avis.',
        'help_step_1' => 'Sélectionnez un tour dans la liste',
        'help_step_2' => 'Entrez le code produit du fournisseur externe',
        'help_step_3' => 'Cliquez sur "Ajouter" pour créer le mappage',
    ],

    // =========================
    // Sync
    // =========================
    'sync' => [
        'queued' => 'Sync queued for :target.',
        'all'    => 'all providers',
    ],

    // =========================
    // Thread / conversation
    // =========================
    'thread' => [
        'title'             => 'Fil d\'avis #:id',
        'header'            => 'Fil — Avis #:id',
        'replies_header'    => 'Réponses',
        'th_date'           => 'Date',
        'th_admin'          => 'Admin',
        'th_visible'        => 'Visible',
        'th_body'           => 'Contenu',
        'th_actions'        => 'Actions',
        'toggle_visibility' => 'Basculer la visibilité',
        'delete'            => 'Supprimer',
        'confirm_delete'    => 'Supprimer la réponse ?',
        'empty'             => 'Aucune réponse pour le moment.',
    ],

    // =========================
    // Admin form (create/edit)
    // =========================
    'form' => [
        'title_edit'       => 'Edit review',
        'title_new'        => 'Créer un avis',
        'visible_publicly' => 'Visible publicly',
    ],

    // =========================
    // Alias for reply emails (if used outside "emails")
    // =========================
    'reply' => [
        'subject'          => 'Reply to your review',
        'greeting'         => 'Hi :name,',
        'about_html'       => 'about <strong>:tour</strong>',
        'about_text'       => 'about :tour',
        'intro'            => 'Our team has replied to your review :extra.',
        'quote'            => '“:text”',
        'sign'             => '— :admin',
        'closing'          => 'If you have any questions or would like to expand on your comment, just reply to this email. Pura vida! 🌿',
        'rights_reserved'  => 'All rights reserved',
    ],

    // Fallback for greeting if there is no name
    'traveler' => 'traveler',

    // =====================================================================
    // ==== Compatibility with old translation file (legacy) ================
    // =====================================================================

    'loaded'           => 'Reviews loaded successfully.',
    'provider_error'   => 'There was a problem with the review provider.',
    'service_busy'     => 'The service is busy, please try again shortly.',
    'unexpected_error' => 'An unexpected error occurred while loading reviews.',
    'anonymous'        => 'Anonymous',

    'what_customers_think_about' => 'What customers think about',
    'previous_review'            => 'Previous review',
    'next_review'                => 'Next review',
    'loading'                    => 'Loading reviews...',
    // 'what_visitors_say' already exists above; kept for compatibility
    'reviews_title'              => 'Customer reviews',
    // 'powered_by' already exists above; kept for compatibility
    'view_on_viator'             => 'View :name on Viator',

    // Modal / actions (legacy)
    'open_tour_title'    => 'Open tour page?',
    'open_tour_text_pre' => 'You are about to open the tour page for',
    'open_tour_confirm'  => 'Open now',
    'open_tour_cancel'   => 'Cancel',

    // Carousel controls (legacy, alias of front.see_more/less)
    'previous' => 'Previous',
    'next'     => 'Next',
    'see_more' => 'See more',
    'see_less' => 'See less',
];
