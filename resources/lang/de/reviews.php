<?php

return [

    'what_visitors_say' => 'Was sagen unsere Gäste?',
    'powered_by'        => 'Bereitgestellt von',

    // =========================
    // Allgemein
    // =========================
    'common' => [
        'reviews'   => 'Bewertungen',
        'provider'  => 'Anbieter',
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
        'hide'      => 'Ausblenden',
        'flag'      => 'Melden',
        'unflag'    => 'Meldung aufheben',
        'apply'     => 'Anwenden',
        'yes'       => 'Ja',
        'no'        => 'Nein',
        'not_found' => 'Keine Ergebnisse gefunden.',
        'clear'     => 'Leeren',
        'language'  => 'Sprache',

        // Ergänzungen für Anbieter-Panel
        'new'              => 'Neu',
        'name'             => 'Name',
        'active'           => 'Aktiv',
        'inactive'         => 'Inaktiv',
        'indexable'        => 'Indexierbar',
        'indexable_yes'    => 'Enthält indexierbares Markup/JSON-LD',
        'indexable_no'     => 'Nicht indexierbar',
        'activate'         => 'Anbieter aktivieren',
        'deactivate'       => 'Anbieter deaktivieren',
        'activate_title'   => 'Anbieter aktivieren?',
        'activate_text'    => 'Der Anbieter wird aktiviert.',
        'deactivate_title' => 'Anbieter deaktivieren?',
        'deactivate_text'  => 'Der Anbieter wird deaktiviert.',
        'cancel'           => 'Abbrechen',
        'test'             => 'Verbindung testen',
        'flush_cache'      => 'Cache leeren',
        'delete_confirm'   => 'Anbieter löschen?',
        'system_locked'    => 'Systemanbieter (gesperrt)',
    ],

    // =========================
    // Bewertungsstatus
    // =========================
    'status' => [
        'pending'   => 'ausstehend',
        'published' => 'veröffentlicht',
        'hidden'    => 'ausgeblendet',
        'flagged'   => 'gemeldet',
    ],

    // =========================
    // Admin – Liste / Moderation
    // =========================
    'admin' => [
        'index_title' => 'Bewertungen',
        'index_titel' => 'Bewertungen', // legacy Alias

        'new_local'  => 'Neu (lokal)',
        'bulk_apply' => 'Auf Auswahl anwenden',

        'responded'  => 'Beantwortet?',
        'last_reply' => 'Letzte:',

        'filters' => [
            'provider'  => 'Anbieter',
            'status'    => 'Status',
            'tour_id'   => 'Tour-ID',
            'stars'     => '⭐',
            'q'         => 'Text/Autor suchen…',
            'responded' => 'Beantwortet?',
        ],

        'table' => [
            'date'   => 'Datum',
            'review' => 'Bewertung',
            'client' => 'Kunde',
            'tour'   => 'Tour',
        ],

        'messages' => [
            'created'        => 'Bewertung erstellt.',
            'updated'        => 'Bewertung aktualisiert.',
            'deleted'        => 'Bewertung gelöscht.',
            'published'      => 'Bewertung veröffentlicht.',
            'hidden'         => 'Bewertung ausgeblendet.',
            'flagged'        => 'Bewertung gemeldet.',
            'unflagged'      => 'Meldung aufgehoben.',
            'bulk_published' => ':n Bewertungen veröffentlicht.',
            'bulk_hidden'    => ':n Bewertungen ausgeblendet.',
            'bulk_flagged'   => ':n Bewertungen gemeldet.',
            'bulk_deleted'   => ':n Bewertungen gelöscht.',
            'publish_min_rating' => 'Veröffentlichung nicht möglich, da die Bewertung (:rating★) unter dem zulässigen Minimum (:min★) liegt.',
            'bulk_published_partial' => ':ok Bewertungen veröffentlicht. :skipped übersprungen, da Bewertung unter :min★.',
        ],
    ],

    // =========================
    // Admin – Antworten
    // =========================
    'replies' => [
        'reply'            => 'Antworten',
        'title_create'     => 'Antwort — Bewertung #:id',
        'label_body'       => 'Antwort',
        'label_is_public'  => 'Öffentlich',
        'label_notify'     => 'E-Mail an Kunden senden',
        'notify_to'        => 'Wird gesendet an: :email',
        'warn_no_email'    => 'Achtung: Für diese Bewertung wurde keine Kunden-E-Mail gefunden. Die Antwort wird gespeichert, es wird jedoch keine E-Mail gesendet.',
        'saved_notified'   => 'Antwort veröffentlicht und an :email gesendet.',
        'saved_no_email'   => 'Antwort veröffentlicht. Keine E-Mail gesendet, da kein Empfänger gefunden wurde.',
        'deleted'          => 'Antwort gelöscht.',
        'visibility_ok'    => 'Sichtbarkeit aktualisiert.',
        'thread_title'     => 'Konversation — Bewertung #:id',
        'thread_empty'     => 'Keine Antworten.',
        'last_reply'       => 'Letzte:',
    ],

    // =========================
    // Admin – Anfragen nach dem Kauf
    // =========================
    'requests' => [
        'index_title' => 'Bewertungen anfordern',
        'subtitle'    => 'Senden Sie Bewertungslinks nach dem Kauf und verwalten Sie bereits gesendete Anfragen.',

        'tabs' => [
            'eligible'  => 'Berechtigt (Buchungen)',
            'requested' => 'Angefordert (gesendet)',
        ],

        'filters' => [
            'q_placeholder' => 'ID, Name oder E-Mail',
            'any_status'    => '— Beliebig —',
            'from'          => 'Von',
            'to'            => 'Bis',
        ],

        'window_days'      => 'Fenster (Tage)',
        'date_column'      => 'Datumsspalte',
        'calculated_range' => 'Berechneter Zeitraum',
        'tour_id'          => 'Tour-ID',
        'btn_request'      => 'Bewertung anfordern',
        'no_eligible'      => 'Keine berechtigten Buchungen.',

        'table' => [
            'booking'   => 'Buchung',
            'reference' => 'Referenz',
            'sent_at'   => 'Gesendet',
            'states'    => 'Status',
        ],

        'labels' => [
            'expires_in_days' => 'Ablauftage',
            'expires_at'      => 'Läuft ab',
            'used_at'         => 'Verwendet',
        ],

        'actions' => [
            'resend'         => 'Erneut senden',
            'confirm_delete' => 'Diese Anfrage löschen?',
        ],

        'status' => [
            'active'    => 'Aktiv',
            'sent'      => 'Gesendet',
            'reminded'  => 'Erneut gesendet',
            'used'      => 'Verwendet',
            'expired'   => 'Abgelaufen',
            'cancelled' => 'Storniert',
        ],

        'status_labels' => [
            'created'   => 'erstellt',
            'sent'      => 'gesendet',
            'reminded'  => 'erneut gesendet',
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
            'used'    => 'Diese Anfrage wurde bereits verwendet.',
            'expired' => 'Diese Anfrage ist abgelaufen.',
        ],
    ],

    // =========================
    // Öffentlich (Formular)
    // =========================
    'public' => [
        'form_title'   => 'Bewertung abgeben',
        'labels'       => [
            'rating'       => 'Bewertung',
            'title'        => 'Titel (optional)',
            'body'         => 'Ihre Erfahrung',
            'author_name'  => 'Ihr Name (optional)',
            'author_email' => 'Ihre E-Mail (optional)',
            'submit'       => 'Bewertung senden',
        ],
        'thanks'       => 'Vielen Dank für Ihre Bewertung! 🌿',
        'thanks_dup'   => 'Danke! Ihre Bewertung liegt uns bereits vor 🙌',
        'expired'      => 'Dieser Link ist abgelaufen – vielen Dank trotzdem 💚',
        'used'         => 'Diese Anfrage wurde bereits verwendet.',
        'used_help'    => 'Dieser Bewertungslink wurde bereits verwendet. Wenn Sie glauben, dass es sich um einen Fehler handelt oder Ihren Kommentar aktualisieren möchten, kontaktieren Sie uns – wir helfen gerne.',
        'not_found'    => 'Anfrage nicht gefunden.',
    ],

    // =========================
    // E-Mails
    // =========================
    'emails' => [
        'brand_from'   => 'Green Vacations CR',
        'contact_line' => 'Wenn Sie Hilfe benötigen, kontaktieren Sie uns unter :email oder :phone. Besuchen Sie uns unter :url.',
        'request' => [
            'subject'   => 'Wie war Ihre Erfahrung bei :tour?',
            'cta'       => 'Meine Bewertung abgeben',
            'footer'    => 'Danke für die Unterstützung des lokalen Tourismus. Wir freuen uns auf ein Wiedersehen! 🌿',
            'expires'   => '* Dieser Link ist aktiv bis: :date.',
            'greeting'  => 'Hallo :name,',
            'intro'     => 'Pura vida! 🙌 Vielen Dank, dass Sie sich für uns entschieden haben. Wir möchten wissen, wie es bei :tour war.',
            'ask'       => 'Hätten Sie 1–2 Minuten Zeit für eine Bewertung? Das hilft uns sehr.',
            'fallback'  => 'Funktioniert der Button nicht? Kopieren Sie diesen Link in Ihren Browser:',
        ],
        'reply' => [
            'subject'  => 'Antwort auf Ihre Bewertung',
            'greeting' => 'Hallo :name,',
            'intro'    => 'Unser Team hat auf Ihre Bewertung geantwortet: :extra.',
            'quote'    => '„:text“',
            'sign'     => '— :admin',
        ],
    ],

    // =========================
    // Front
    // =========================
    'front' => [
        'see_more'   => 'Weitere Bewertungen anzeigen',
        'no_reviews' => 'Noch keine Bewertungen.',
    ],

    // =========================
    // Anbieter
    // =========================
    'providers' => [
        'index_title' => 'Bewertungsanbieter',
        'system_locked' => 'Systemanbieter',
        'messages' => [
            'cannot_delete_local' => 'Der „lokale“ Anbieter ist ein Systemdatensatz und kann nicht gelöscht werden.',
            'created'        => 'Anbieter erstellt.',
            'updated'        => 'Anbieter aktualisiert.',
            'deleted'        => 'Anbieter gelöscht.',
            'status_updated' => 'Status aktualisiert.',
            'cache_flushed'  => 'Cache geleert.',
            'test_fetched'   => ':n Bewertungen abgerufen.',
        ],
    ],

    // =========================
    // Sync
    // =========================
    'sync' => [
        'queued' => 'Synchronisierung für :target in die Warteschlange gestellt.',
        'all'    => 'alle Anbieter',
    ],

    // =========================
    // Thread / Unterhaltung
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
    // Admin-Formular (Erstellen/Bearbeiten)
    // =========================
    'form' => [
        'title_edit'       => 'Bewertung bearbeiten',
        'title_new'        => 'Neue Bewertung',
        'visible_publicly' => 'Öffentlich sichtbar',
    ],

    // =========================
    // Antwort-E-Mail Alias
    // =========================
    'reply' => [
        'subject'          => 'Antwort auf Ihre Bewertung',
        'greeting'         => 'Hallo :name,',
        'about_html'       => 'über <strong>:tour</strong>',
        'about_text'       => 'über :tour',
        'intro'            => 'Unser Team hat auf Ihre Bewertung geantwortet: :extra.',
        'quote'            => '„:text“',
        'sign'             => '— :admin',
        'closing'          => 'Wenn Sie Fragen haben oder Ihren Kommentar erweitern möchten, antworten Sie einfach auf diese E-Mail. Pura vida! 🌿',
        'rights_reserved'  => 'Alle Rechte vorbehalten',
    ],

    'traveler' => 'Reisende/r',

    // =========================
    // Legacy-Kompatibilität
    // =========================
    'loaded'           => 'Bewertungen erfolgreich geladen.',
    'provider_error'   => 'Es gab ein Problem mit dem Bewertungsanbieter.',
    'service_busy'     => 'Der Dienst ist ausgelastet. Bitte versuchen Sie es in Kürze erneut.',
    'unexpected_error' => 'Beim Laden der Bewertungen ist ein unerwarteter Fehler aufgetreten.',
    'anonymous'        => 'Anonym',

    'what_customers_think_about' => 'Was Kunden über … denken',
    'previous_review'            => 'Vorherige Bewertung',
    'next_review'                => 'Nächste Bewertung',
    'loading'                    => 'Bewertungen werden geladen…',
    'reviews_title'              => 'Kundenbewertungen',
    'view_on_viator'             => ':name auf Viator ansehen',

    'open_tour_title'    => 'Tour öffnen?',
    'open_tour_text_pre' => 'Sie sind dabei, die Tour-Seite zu öffnen',
    'open_tour_confirm'  => 'Jetzt öffnen',
    'open_tour_cancel'   => 'Abbrechen',

    'previous' => 'Zurück',
    'next'     => 'Weiter',
    'see_more' => 'Mehr anzeigen',
    'see_less' => 'Weniger anzeigen',
];
