<?php

return [
    'no_reviews' => 'Aucun avis disponible',
    'what_visitors_say' => 'Que disent nos clients ?',
    'powered_by'        => 'Propulsé par',

    'generic' => [
        'our_tour' => 'notre excursion',
    ],

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
        'not_found' => 'Aucun résultat trouvé.',
        'clear'     => 'Effacer',
        'language'  => 'Langue',

        'new'             => 'Nouveau',
        'name'            => 'Nom',
        'active'          => 'Actif',
        'inactive'        => 'Inactif',
        'indexable'       => 'Indexable',
        'indexable_yes'   => 'Inclut du balisage indexable / JSON-LD',
        'indexable_no'    => 'Non indexable',
        'activate'        => 'Activer le fournisseur',
        'deactivate'      => 'Désactiver le fournisseur',
        'activate_title'  => 'Activer le fournisseur ?',
        'activate_text'   => 'Le fournisseur sera activé.',
        'deactivate_title' => 'Désactiver le fournisseur ?',
        'deactivate_text' => 'Le fournisseur sera désactivé.',
        'cancel'          => 'Annuler',
        'test'            => 'Tester la connexion',
        'flush_cache'     => 'Vider le cache',
        'delete_confirm'  => 'Supprimer le fournisseur ?',
        'system_locked'   => 'Fournisseur système (verrouillé)',
    ],

    // =========================
    // Statuts
    // =========================
    'status' => [
        'pending'   => 'en attente',
        'published' => 'publié',
        'hidden'    => 'masqué',
        'flagged'   => 'signalé',
    ],

    // =========================
    // Admin — Liste / modération
    // =========================
    'admin' => [
        'index_title'   => 'Avis',
        'index_titel'   => 'Avis',

        'new_local'     => 'Nouveau (local)',
        'bulk_apply'    => 'Appliquer à la sélection',

        'responded'     => 'Répondu ?',
        'last_reply'    => 'Dernière :',

        'filters' => [
            'provider'  => 'Fournisseur',
            'status'    => 'Statut',
            'tour_id'   => 'ID du tour',
            'stars'     => '⭐',
            'q'         => 'Rechercher texte/auteur...',
            'responded' => 'Répondu ?',
        ],

        'table' => [
            'date'     => 'Date',
            'review'   => 'Avis',
            'client'   => 'Client',
            'tour'     => 'Tour',
        ],

        'messages' => [
            'created'        => 'Avis créé.',
            'updated'        => 'Avis mis à jour.',
            'deleted'        => 'Avis supprimé.',
            'published'      => 'Avis publié.',
            'hidden'         => 'Avis masqué.',
            'flagged'        => 'Avis signalé.',
            'unflagged'      => 'Signalement retiré.',
            'bulk_published' => ':n avis publiés.',
            'bulk_hidden'    => ':n avis masqués.',
            'bulk_flagged'   => ':n avis signalés.',
            'bulk_deleted'   => ':n avis supprimés.',
            'publish_min_rating' => 'Impossible de publier car la note (:rating★) est inférieure au minimum requis (:min★).',
            'bulk_published_partial' => ':ok avis publiés. :skipped ignorés car note inférieure à :min★.',
        ],
    ],

    // =========================
    // Réponses admin
    // =========================
    'replies' => [
        'reply'            => 'Répondre',
        'title_create'     => 'Répondre — Avis #:id',
        'label_body'       => 'Réponse',
        'label_is_public'  => 'Public',
        'label_notify'     => 'Envoyer un e-mail au client',
        'notify_to'        => 'Sera envoyé à :email',
        'warn_no_email'    => 'Attention : aucune adresse e-mail trouvée. La réponse sera enregistrée mais aucun e-mail ne sera envoyé.',
        'saved_notified'   => 'Réponse publiée et envoyée à :email.',
        'saved_no_email'   => 'Réponse publiée. Aucun e-mail envoyé.',
        'deleted'          => 'Réponse supprimée.',
        'visibility_ok'    => 'Visibilité mise à jour.',
        'thread_title'     => 'Conversation — Avis #:id',
        'thread_empty'     => 'Aucune réponse.',
        'last_reply'       => 'Dernière :',
    ],

    // =========================
    // Demandes post-achat
    // =========================
    'requests' => [
        'index_title' => 'Demander des avis',
        'subtitle'    => 'Envoyez des liens d’avis post-achat et gérez les demandes envoyées.',

        'tabs' => [
            'eligible'  => 'Éligibles (réservations)',
            'requested' => 'Demandées (envoyées)',
        ],

        'filters' => [
            'q_placeholder' => 'ID, nom ou e-mail',
            'any_status'    => '— N’importe lequel —',
            'from'          => 'Depuis',
            'to'            => 'Jusqu’à',
        ],

        'window_days'      => 'Fenêtre (jours)',
        'date_column'      => 'Colonne date',
        'calculated_range' => 'Plage calculée',
        'tour_id'          => 'ID du tour',
        'btn_request'      => 'Demander un avis',
        'no_eligible'      => 'Aucune réservation éligible.',

        'table' => [
            'booking'   => 'Réservation',
            'reference' => 'Référence',
            'sent_at'   => 'Envoyé',
            'states'    => 'États',
        ],

        'labels' => [
            'expires_in_days' => 'Expire dans (jours)',
            'expires_at'      => 'Expire le',
            'used_at'         => 'Utilisé le',
        ],

        'actions' => [
            'resend'         => 'Renvoyer',
            'confirm_delete' => 'Supprimer cette demande ?',
        ],

        'status' => [
            'active'    => 'Actives',
            'sent'      => 'Envoyées',
            'reminded'  => 'Rappel envoyée',
            'used'      => 'Utilisées',
            'expired'   => 'Expirées',
            'cancelled' => 'Annulées',
        ],

        'status_labels' => [
            'created'   => 'créée',
            'sent'      => 'envoyée',
            'reminded'  => 'rappel envoyée',
            'fulfilled' => 'complétée',
            'expired'   => 'expirée',
            'cancelled' => 'annulée',
            'active'    => 'active',
        ],

        'send_ok'   => 'Demande envoyée.',
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
    // Public
    // =========================
    'public' => [
        'form_title'   => 'Laisser un avis',
        'labels'       => [
            'rating'       => 'Note',
            'title'        => 'Titre (optionnel)',
            'body'         => 'Votre expérience',
            'author_name'  => 'Votre nom (optionnel)',
            'author_email' => 'Votre e-mail (optionnel)',
            'submit'       => 'Envoyer l’avis',
        ],
        'thanks'       => 'Merci pour votre avis ! 🌿',
        'thanks_body'  => 'Votre opinion est très importante et nous aide à nous améliorer. Merci de tout cœur.',
        'thanks_farewell' => "Nous espérons que vous avez apprécié votre expérience et nous serions ravis de vous revoir très bientôt.\n\n🇨🇷 ¡Pura Vida mae ! 🇨🇷",
        'thanks_dup'   => 'Merci ! Nous avions déjà enregistré votre avis 🙌',
        'expired'      => 'Ce lien a expiré, mais merci pour votre intention 💚',
        'used'         => 'Cette demande a déjà été utilisée.',
        'used_help'    => 'Ce lien d’avis a déjà été utilisé. Si vous pensez qu’il s’agit d’une erreur ou souhaitez modifier votre avis, contactez-nous.',
        'not_found'    => 'Demande introuvable.',
        'back_home'    => 'Retour',
    ],

    // =========================
    // Emails
    // =========================
    'emails' => [

        'brand_from'   => 'Green Vacations CR',
        'contact_line' => 'Si vous avez besoin d’aide, contactez-nous à :email ou au :phone. Visitez :url.',
        'request' => [
            'preheader_with_date' => 'Dites-nous comment s’est passée votre expérience à :tour (:date). Cela ne prend qu’une minute.',
            'preheader'           => 'Dites-nous comment s’est passée votre expérience à :tour. Cela ne prend qu’une minute.',
            'subject'   => 'Comment s’est passée votre expérience à :tour ?',
            'cta'       => 'Laisser mon avis',
            'footer'    => 'Merci de soutenir le tourisme local. À très bientôt ! 🌿',
            'expires'   => '* Ce lien sera actif jusqu’au :date.',
            'greeting'  => 'Bonjour :name,',
            'intro'     => 'Pura vida ! 🙌 Merci de nous avoir choisis. Nous aimerions connaître votre expérience à :tour.',
            'ask'       => 'Pourriez-vous nous consacrer 1–2 minutes en laissant votre avis ? Cela compte vraiment beaucoup.',
            'fallback'  => 'Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :',
        ],
        'reply' => [
            'subject'  => 'Réponse à votre avis',
            'greeting' => 'Bonjour :name,',
            'intro'    => 'Notre équipe a répondu à votre avis :extra.',
            'quote'    => '« :text »',
            'sign'     => '— :admin',
        ],
        'submitted' => [
            'subject' => 'Nouvel avis reçu',
        ],
    ],

    // =========================
    // Front
    // =========================
    'front' => [
        'see_more'   => 'Voir plus d’avis',
        'no_reviews' => 'Aucun avis pour le moment.',
    ],

    // =========================
    // Fournisseurs
    // =========================
    'providers' => [
        'index_title' => 'Fournisseurs d’avis',
        'system_locked' => 'Fournisseur système',
        'messages' => [
            'cannot_delete_local' => 'Le fournisseur “local” est un fournisseur système et ne peut pas être supprimé.',
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
    // Thread
    // =========================
    'thread' => [
        'title'             => 'Fil de discussion — Avis #:id',
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
    // Formulaire admin
    // =========================
    'form' => [
        'title_edit'       => 'Modifier l’avis',
        'title_new'        => 'Nouvel avis',
        'visible_publicly' => 'Visible publiquement',
    ],

    // =========================
    // Alias (hors emails)
    // =========================
    'reply' => [
        'subject'          => 'Réponse à votre avis',
        'greeting'         => 'Bonjour :name,',
        'about_html'       => 'à propos de <strong>:tour</strong>',
        'about_text'       => 'à propos de :tour',
        'intro'            => 'Notre équipe a répondu à votre avis :extra.',
        'quote'            => '« :text »',
        'sign'             => '— :admin',
        'closing'          => 'Si vous avez des questions ou souhaitez compléter votre commentaire, répondez simplement à cet e-mail. Pura Vida ! 🌿',
        'rights_reserved'  => 'Tous droits réservés',
    ],

    'traveler' => 'voyageur/euse',

    // =========================
    // Legacy
    // =========================
    'loaded'           => 'Avis chargés avec succès.',
    'provider_error'   => 'Une erreur est survenue avec le fournisseur d’avis.',
    'service_busy'     => 'Le service est occupé. Veuillez réessayer bientôt.',
    'unexpected_error' => 'Une erreur inattendue s’est produite lors du chargement des avis.',
    'anonymous'        => 'Anonyme',

    'what_customers_think_about' => 'Ce que les clients pensent de',
    'previous_review'            => 'Avis précédent',
    'next_review'                => 'Avis suivant',
    'loading'                    => 'Chargement des avis...',
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
