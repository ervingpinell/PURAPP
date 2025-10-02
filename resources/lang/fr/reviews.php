<?php

return [

    'what_visitors_say' => 'Que disent nos clients ?',
    'powered_by'        => 'Propulsé par',

    // =========================
    // Commun
    // =========================
    'common' => [
        'reviews'   => 'Avis',
        'provider'  => 'Fournisseur',
        'status'    => 'Statut',
        'tour'      => 'Tour',
        'rating'    => 'Note',
        'title'     => 'Titre',
        'body'      => 'Contenu',
        'author'    => 'Auteur',
        'actions'   => 'Actions',
        'filter'    => 'Filtrer',
        'search'    => 'Rechercher',
        'id'        => 'ID',
        'public'    => 'Publique',
        'private'   => 'Privée',
        'back'      => 'Retour',
        'save'      => 'Enregistrer',
        'create'    => 'Créer',
        'edit'      => 'Modifier',
        'delete'    => 'Supprimer',
        'publish'   => 'Publier',
        'hide'      => 'Masquer',
        'flag'      => 'Signaler',
        'unflag'    => 'Annuler le signalement',
        'apply'     => 'Appliquer',
        'yes'       => 'Oui',
        'no'        => 'Non',
        'not_found' => 'Aucun résultat trouvé.',
        'clear'     => 'Effacer',
        'language'  => 'Langue',

        // Ajouts pour le panneau Fournisseurs
        'new'              => 'Nouveau',
        'name'             => 'Nom',
        'active'           => 'Actif',
        'inactive'         => 'Inactif',
        'indexable'        => 'Indexable',
        'indexable_yes'    => 'Inclut un balisage indexable/JSON-LD',
        'indexable_no'     => 'Non indexable',
        'activate'         => 'Activer le fournisseur',
        'deactivate'       => 'Désactiver le fournisseur',
        'activate_title'   => 'Activer le fournisseur ?',
        'activate_text'    => 'Le fournisseur sera activé.',
        'deactivate_title' => 'Désactiver le fournisseur ?',
        'deactivate_text'  => 'Le fournisseur sera désactivé.',
        'cancel'           => 'Annuler',
        'test'             => 'Tester la connexion',
        'flush_cache'      => 'Vider le cache',
        'delete_confirm'   => 'Supprimer le fournisseur ?',
        'system_locked'    => 'Fournisseur système (verrouillé)',
    ],

    // =========================
    // Statuts d’avis
    // =========================
    'status' => [
        'pending'   => 'en attente',
        'published' => 'publiée',
        'hidden'    => 'masquée',
        'flagged'   => 'signalée',
    ],

    // =========================
    // Admin - liste / modération
    // =========================
    'admin' => [
        'index_title' => 'Avis',
        'index_titel' => 'Avis', // alias legacy

        'new_local'  => 'Nouvelle (locale)',
        'bulk_apply' => 'Appliquer à la sélection',

        'responded'  => 'Répondu ?',
        'last_reply' => 'Dernière :',

        'filters' => [
            'provider'  => 'Fournisseur',
            'status'    => 'Statut',
            'tour_id'   => 'ID du tour',
            'stars'     => '⭐',
            'q'         => 'Rechercher texte/auteur…',
            'responded' => 'Répondu ?',
        ],

        'table' => [
            'date'   => 'Date',
            'review' => 'Avis',
            'client' => 'Client',
            'tour'   => 'Tour',
        ],

        'messages' => [
            'created'        => 'Avis créé.',
            'updated'        => 'Avis mis à jour.',
            'deleted'        => 'Avis supprimé.',
            'published'      => 'Avis publié.',
            'hidden'         => 'Avis masqué.',
            'flagged'        => 'Avis signalé.',
            'unflagged'      => 'Signalement annulé.',
            'bulk_published' => ':n avis publiés.',
            'bulk_hidden'    => ':n avis masqués.',
            'bulk_flagged'   => ':n avis signalés.',
            'bulk_deleted'   => ':n avis supprimés.',
            'publish_min_rating' => 'Publication impossible : la note (:rating★) est inférieure au minimum autorisé (:min★).',
            'bulk_published_partial' => ':ok avis publiés. :skipped ignorés car note inférieure à :min★.',
        ],
    ],

    // =========================
    // Admin - réponses
    // =========================
    'replies' => [
        'reply'            => 'Répondre',
        'title_create'     => 'Répondre — Avis #:id',
        'label_body'       => 'Réponse',
        'label_is_public'  => 'Publique',
        'label_notify'     => 'Envoyer un email au client',
        'notify_to'        => 'Sera envoyé à : :email',
        'warn_no_email'    => 'Attention : adresse email du client introuvable pour cet avis. La réponse sera enregistrée, mais aucun email ne sera envoyé.',
        'saved_notified'   => 'Réponse publiée et envoyée à :email.',
        'saved_no_email'   => 'Réponse publiée. Aucun email envoyé faute de destinataire.',
        'deleted'          => 'Réponse supprimée.',
        'visibility_ok'    => 'Visibilité mise à jour.',
        'thread_title'     => 'Conversation — Avis #:id',
        'thread_empty'     => 'Aucune réponse.',
        'last_reply'       => 'Dernière :',
    ],

    // =========================
    // Admin - demandes post-achat
    // =========================
    'requests' => [
        'index_title' => 'Demander des avis',
        'subtitle'    => 'Envoyez des liens d’avis post-achat et gérez les demandes déjà envoyées.',

        'tabs' => [
            'eligible'  => 'Admissibles (réservations)',
            'requested' => 'Demandées (envoyées)',
        ],

        'filters' => [
            'q_placeholder' => 'ID, nom ou email',
            'any_status'    => '— N’importe —',
            'from'          => 'De',
            'to'            => 'À',
        ],

        'window_days'      => 'Fenêtre (jours)',
        'date_column'      => 'Colonne de date',
        'calculated_range' => 'Plage calculée',
        'tour_id'          => 'ID du tour',
        'btn_request'      => 'Demander un avis',
        'no_eligible'      => 'Aucune réservation admissible.',

        'table' => [
            'booking'   => 'Réservation',
            'reference' => 'Référence',
            'sent_at'   => 'Envoyé',
            'states'    => 'États',
        ],

        'labels' => [
            'expires_in_days' => 'Jours d’expiration',
            'expires_at'      => 'Expire',
            'used_at'         => 'Utilisée',
        ],

        'actions' => [
            'resend'         => 'Renvoyer',
            'confirm_delete' => 'Supprimer cette demande ?',
        ],

        'status' => [
            'active'    => 'Actives',
            'sent'      => 'Envoyées',
            'reminded'  => 'Renvoyées',
            'used'      => 'Utilisées',
            'expired'   => 'Expirées',
            'cancelled' => 'Annulées',
        ],

        'status_labels' => [
            'created'   => 'créée',
            'sent'      => 'envoyée',
            'reminded'  => 'renvoyée',
            'fulfilled' => 'complétée',
            'expired'   => 'expirée',
            'cancelled' => 'annulée',
            'active'    => 'active',
        ],

        'send_ok'   => 'Demande d’avis envoyée.',
        'resend_ok' => 'Demande renvoyée.',
        'remind_ok' => 'Rappel envoyé.',
        'expire_ok' => 'Demande expirée.',
        'deleted'   => 'Demande supprimée.',
        'none'      => 'Aucune demande.',

        'errors' => [
            'used'    => 'Cette demande a déjà été utilisée.',
            'expired' => 'Cette demande est expirée.',
        ],
    ],

    // =========================
    // Public (formulaire d’avis)
    // =========================
    'public' => [
        'form_title'   => 'Laisser un avis',
        'labels'       => [
            'rating'       => 'Note',
            'title'        => 'Titre (optionnel)',
            'body'         => 'Votre expérience',
            'author_name'  => 'Votre nom (optionnel)',
            'author_email' => 'Votre email (optionnel)',
            'submit'       => 'Envoyer l’avis',
        ],
        'thanks'       => 'Merci pour votre avis ! 🌿',
        'thanks_dup'   => 'Merci ! Nous avions déjà enregistré votre avis 🙌',
        'expired'      => 'Ce lien a expiré, mais merci pour votre intention 💚',
        'used'         => 'Cette demande a déjà été utilisée.',
        'used_help'    => 'Ce lien d’avis a déjà été utilisé. Si vous pensez qu’il s’agit d’une erreur ou si vous souhaitez mettre à jour votre commentaire, contactez-nous et nous vous aiderons avec plaisir.',
        'not_found'    => 'Demande introuvable.',
    ],

    // =========================
    // Emails
    // =========================
    'emails' => [
        'brand_from'   => 'Green Vacations CR',
        'contact_line' => 'Si vous avez besoin d’aide, contactez-nous à :email ou au :phone. Rendez-nous visite sur :url.',
        'request' => [
            'subject'   => 'Comment s’est passée votre expérience sur :tour ?',
            'cta'       => 'Laisser mon avis',
            'footer'    => 'Merci de soutenir le tourisme local. À très bientôt ! 🌿',
            'expires'   => '* Ce lien sera actif jusqu’au :date.',
            'greeting'  => 'Bonjour :name,',
            'intro'     => 'Pura vida ! 🙌 Merci de nous avoir choisis. Nous aimerions savoir comment s’est passée votre expérience sur :tour.',
            'ask'       => 'Auriez-vous 1–2 minutes pour laisser votre avis ? Cela compte énormément.',
            'fallback'  => 'Si le bouton ne fonctionne pas, copiez-collez ce lien dans votre navigateur :',
        ],
        'reply' => [
            'subject'  => 'Réponse à votre avis',
            'greeting' => 'Bonjour :name,',
            'intro'    => 'Notre équipe a répondu à votre avis :extra.',
            'quote'    => '« :text »',
            'sign'     => '— :admin',
        ],
    ],

    // =========================
    // Front
    // =========================
    'front' => [
        'see_more'   => 'Voir plus d’avis',
        'no_reviews' => 'Il n’y a pas encore d’avis.',
    ],

    // =========================
    // Fournisseurs
    // =========================
    'providers' => [
        'index_title' => 'Fournisseurs d’avis',
        'system_locked' => 'Fournisseur système',
        'messages' => [
            'cannot_delete_local' => 'Le fournisseur « local » est un enregistrement système et ne peut pas être supprimé.',
            'created'        => 'Fournisseur créé.',
            'updated'        => 'Fournisseur mis à jour.',
            'deleted'        => 'Fournisseur supprimé.',
            'status_updated' => 'Statut mis à jour.',
            'cache_flushed'  => 'Cache vidé.',
            'test_fetched'   => ':n avis récupérés.',
        ],
    ],

    // =========================
    // Sync
    // =========================
    'sync' => [
        'queued' => 'Synchronisation mise en file d’attente pour :target.',
        'all'    => 'tous les fournisseurs',
    ],

    // =========================
    // Fil / conversation
    // =========================
    'thread' => [
        'title'             => 'Fil de l’avis #:id',
        'header'            => 'Fil — Avis #:id',
        'replies_header'    => 'Réponses',
        'th_date'           => 'Date',
        'th_admin'          => 'Admin',
        'th_visible'        => 'Visible',
        'th_body'           => 'Contenu',
        'th_actions'        => 'Actions',
        'toggle_visibility' => 'Changer la visibilité',
        'delete'            => 'Supprimer',
        'confirm_delete'    => 'Supprimer la réponse ?',
        'empty'             => 'Aucune réponse pour le moment.',
    ],

    // =========================
    // Formulaire admin (créer/éditer)
    // =========================
    'form' => [
        'title_edit'       => 'Modifier l’avis',
        'title_new'        => 'Nouvel avis',
        'visible_publicly' => 'Visible publiquement',
    ],

    // =========================
    // Alias email de réponse (hors "emails")
    // =========================
    'reply' => [
        'subject'          => 'Réponse à votre avis',
        'greeting'         => 'Bonjour :name,',
        'about_html'       => 'à propos de <strong>:tour</strong>',
        'about_text'       => 'à propos de :tour',
        'intro'            => 'Notre équipe a répondu à votre avis :extra.',
        'quote'            => '« :text »',
        'sign'             => '— :admin',
        'closing'          => 'Si vous avez des questions ou souhaitez compléter votre commentaire, répondez simplement à cet email. Pura vida ! 🌿',
        'rights_reserved'  => 'Tous droits réservés',
    ],

    'traveler' => 'voyageur·euse',

    // =========================
    // Compatibilité legacy
    // =========================
    'loaded'           => 'Avis chargés avec succès.',
    'provider_error'   => 'Un problème est survenu avec le fournisseur d’avis.',
    'service_busy'     => 'Le service est occupé, veuillez réessayer bientôt.',
    'unexpected_error' => 'Une erreur inattendue est survenue lors du chargement des avis.',
    'anonymous'        => 'Anonyme',

    'what_customers_think_about' => 'Ce que les clients pensent de',
    'previous_review'            => 'Avis précédent',
    'next_review'                => 'Avis suivant',
    'loading'                    => 'Chargement des avis…',
    'reviews_title'              => 'Avis des clients',
    'view_on_viator'             => 'Voir :name sur Viator',

    'open_tour_title'    => 'Ouvrir le tour ?',
    'open_tour_text_pre' => 'Vous êtes sur le point d’ouvrir la page du tour',
    'open_tour_confirm'  => 'Ouvrir maintenant',
    'open_tour_cancel'   => 'Annuler',

    'previous' => 'Précédent',
    'next'     => 'Suivant',
    'see_more' => 'Voir plus',
    'see_less' => 'Voir moins',
];
