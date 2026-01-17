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
        'rating'    => 'Bewertung',
        'title'     => 'Titel',
        'body'      => 'Inhalt',
        'author'    => 'Autor',
        'actions'   => 'Aktionen',
        'filter'    => 'Filtern',
        'search'    => 'Suchen',
        'id'        => 'ID',
        'public'    => 'Öffentlich',
        'private'   => 'Privat',
        'back'      => 'Zurück',
        'save'      => 'Speichern',
        'create'    => 'Erstellen',
        'edit'      => 'Bearbeiten',
        'delete'    => 'Löschen',
        'publish'   => 'Veröffentlichen',
        'hide'      => 'Verbergen',
        'flag'      => 'Markieren',
        'unflag'    => 'Markierung entfernen',
        'apply'     => 'Anwenden',
        'yes'       => 'Ja',
        'no'        => 'Nein',
        'not_found' => 'No results were found.',
        'clear'     => 'Clear',
        'language'  => 'Sprache',

        // 🔹 Keys added for Providers panel
        'new'             => 'Neu',
        'name'            => 'Name',
        'active'          => 'Aktiv',
        'inactive'        => 'Inaktiv',
        'indexable'       => 'Indexierbar',
        'indexable_yes'   => 'Enthält indexierbares/JSON-LD Markup',
        'indexable_no'    => 'Nicht indexierbar',
        'activate'        => 'Anbieter aktivieren',
        'deactivate'      => 'Anbieter deaktivieren',
        'activate_title'  => 'Anbieter aktivieren?',
        'activate_text'   => 'Der Anbieter wird aktiviert.',
        'deactivate_title' => 'Anbieter deaktivieren?',
        'deactivate_text' => 'Der Anbieter wird nicht mehr aktiv sein.',
        'cancel'          => 'Abbrechen',
        'test'            => 'Verbindung testen',
        'flush_cache'     => 'Cache leeren',
        'delete_confirm'  => 'Anbieter löschen?',
        'system_locked'   => 'Systemanbieter (gesperrt)',
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

        'new_local'     => 'Neu (lokal)',
        'bulk_apply'    => 'Auf Auswahl anwenden',
        'external_provider_note' => 'Hinweis des externen Anbieters',

        'responded'     => 'Beantwortet?',
        'last_reply'    => 'Letzte:',

        'filters'       => [
            'provider'  => 'Anbieter',
            'status'    => 'Status',
            'tour_id'   => 'Tour-ID',
            'stars'     => '⭐',
            'q'         => 'Suche Text/Autor…',
            'responded' => 'Beantwortet?',
        ],

        'table' => [
            'date'     => 'Datum',
            'review'   => 'Bewertung',
            'client'   => 'Kunde',
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
        'reply'            => 'Antworten',
        'title_create'     => 'Reply — Review #:id',
        'label_body'       => 'Reply',
        'label_is_public'  => 'Public',
        'label_notify'     => 'Send email to customer',
        'notify_to'        => 'It will be sent to: :email',
        'warn_no_email'    => 'Warnung: Wir haben keine E-Mail-Adresse für den Kunden in dieser Bewertung gefunden. Die Antwort wird gespeichert, aber es wird keine E-Mail gesendet.',
        'saved_notified'   => 'Reply published and emailed to :email.',
        'saved_no_email'   => 'Reply published. No email was sent because no recipient was found.',
        'deleted'          => 'Reply deleted.',
        'visibility_ok'    => 'Visibility updated.',
        'thread_title'     => 'Conversation — Review #:id',
        'thread_empty'     => 'No replies.',
        'last_reply'       => 'Letzte:',
    ],

    // =========================
    // Admin - post-purchase review requests
    // =========================
    'requests' => [
        'index_title' => 'Bewertungen anfordern',
        'subtitle'    => 'Links für Bewertungen nach dem Kauf senden und bereits gesendete Anfragen verwalten.',

        // Tabs
        'tabs' => [
            'eligible'  => 'Berechtigt (Buchungen)',
            'requested' => 'Angefordert (gesendet)',
        ],

        // Filters
        'filters' => [
            'q_placeholder' => 'ID, Name oder E-Mail',
            'any_status'    => '— Alle —',
            'from'          => 'Von',
            'to'            => 'Bis',
        ],

        'window_days'      => 'Fenster (Tage)',
        'date_column'      => 'Datumsspalte',
        'date_options'     => [
            'created_at' => 'Buchung erstellt',
            'tour_date'  => 'Tour-Datum',
        ],
        'calculated_range' => 'Berechneter Bereich',
        'tour_id'          => 'Tour-ID',
        'btn_request'      => 'Bewertung anfordern',
        'no_eligible'      => 'Keine berechtigten Buchungen.',

        'table' => [
            'booking'   => 'Buchung',
            'reference' => 'Referenz',
            'sent_at'   => 'Gesendet am',
            'states'    => 'Status',
        ],

        'labels' => [
            'expires_in_days' => 'Ablauf (Tage)',
            'expires_at'      => 'Läuft ab am',
            'used_at'         => 'Genutzt am',
        ],

        'actions' => [
            'resend'         => 'Erneut senden',
            'confirm_delete' => 'Diese Anfrage löschen?',
        ],

        'status' => [
            'active'    => 'Aktiv',
            'sent'      => 'Gesendet',
            'reminded'  => 'Erinnert',
            'used'      => 'Genutzt',
            'expired'   => 'Abgelaufen',
            'cancelled' => 'Storniert',
        ],

        'status_labels' => [
            'created'   => 'erstellt',
            'sent'      => 'gesendet',
            'reminded'  => 'erinnert',
            'fulfilled' => 'abgeschlossen',
            'expired'   => 'abgelaufen',
            'cancelled' => 'storniert',
            'active'    => 'aktiv',
        ],

        'send_ok'   => 'Bewertungsanfrage gesendet.',
        'resend_ok' => 'Anfrage erneut gesendet.',
        'remind_ok' => 'Erinnerung gesendet.',
        'expire_ok' => 'Anfrage abgelaufen.',
        'deleted'   => 'Anfrage gelöscht.',
        'none'      => 'Keine Anfragen.',

        'errors' => [
            'used'    => 'Diese Anfrage wurde bereits genutzt.',
            'expired' => 'Diese Anfrage ist abgelaufen.',
        ],
        'no_requests' => 'Keine Anfragen gefunden.',
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
            'closing'  => 'Wenn Sie Fragen haben oder Ihren Kommentar erweitern möchten, antworten Sie einfach auf diese E-Mail. Pura vida! 🌿',
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
        'index_title' => 'Bewertungsanbieter',
        'indexable' => 'Indexierbar',
        'cache_ttl' => 'Cache TTL (Sek)',
        'back' => 'Zurück',
        'actions' => 'Aktionen',
        'system_locked' => 'Systemanbieter',
        'messages' => [
            'cannot_delete_local' => 'Der Anbieter „local“ ist ein Systemanbieter und kann nicht gelöscht werden.',
            'created'        => 'Anbieter erstellt.',
            'updated'        => 'Anbieter aktualisiert.',
            'deleted'        => 'Anbieter gelöscht.',
            'status_updated' => 'Status aktualisiert.',
            'cache_flushed'  => 'Cache geleert.',
            'test_fetched'   => ':n Bewertungen abgerufen.',
            'mapping_added'   => 'Zuordnung erfolgreich hinzugefügt.',
            'mapping_updated' => 'Zuordnung erfolgreich aktualisiert.',
            'mapping_deleted' => 'Zuordnung erfolgreich gelöscht.',
        ],
        'product_map' => [
            'title' => 'Produktzuordnung - :provider',
        ],
        'product_mapping_title' => 'Produktzuordnung - :name',
        'product_mappings' => 'Produktzuordnungen',
        'tour' => 'Tour',
        'select_tour' => 'Tour auswählen',
        'select_tour_placeholder' => 'Wählen Sie eine Tour...',
        'product_code' => 'Produktcode',
        'product_code_placeholder' => 'Bsp: 12732-ABC',
        'add_mapping' => 'Zuordnung hinzufügen',
        'no_mappings' => 'Keine Zuordnungen konfiguriert',
        'confirm_delete_mapping' => 'Sind Sie sicher, dass Sie diese Zuordnung löschen möchten?',
        'help_title' => 'Hilfe',
        'help_text' => 'Ordnen Sie externe Produktcodes internen Touren zu, um Bewertungen korrekt zu synchronisieren.',
        'help_step_1' => 'Wählen Sie eine Tour aus der Liste',
        'help_step_2' => 'Geben Sie den externen Produktcode des Anbieters ein',
        'help_step_3' => 'Klicken Sie auf "Hinzufügen", um die Zuordnung zu erstellen',
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
        'title'             => 'Bewertungs-Thread #:id',
        'header'            => 'Thread — Bewertung #:id',
        'replies_header'    => 'Antworten',
        'th_date'           => 'Datum',
        'th_admin'          => 'Admin',
        'th_visible'        => 'Sichtbar',
        'th_body'           => 'Inhalt',
        'th_actions'        => 'Aktionen',
        'toggle_visibility' => 'Sichtbarkeit umschalten',
        'delete'            => 'Löschen',
        'confirm_delete'    => 'Antwort löschen?',
        'empty'             => 'Noch keine Antworten.',
    ],

    // =========================
    // Admin form (create/edit)
    // =========================
    'form' => [
        'title_edit'       => 'Edit review',
        'title_new'        => 'Bewertung erstellen',
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
