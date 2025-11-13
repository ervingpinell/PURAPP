<?php

return [

    'what_visitors_say' => 'Was sagen unsere Kunden?',
    'powered_by'        => 'Bereitgestellt von',

    'generic' => [
        'our_tour' => 'unserer Tour',
    ],

    // =========================
    // Gemeinsame Begriffe
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
        'flag'      => 'Markieren',
        'unflag'    => 'Markierung entfernen',
        'apply'     => 'Anwenden',
        'yes'       => 'Ja',
        'no'        => 'Nein',
        'not_found' => 'Keine Ergebnisse gefunden.',
        'clear'     => 'Leeren',
        'language'  => 'Sprache',

        // 🔹 Zusätzliche Schlüssel für Anbieter-Panel
        'new'             => 'Neu',
        'name'            => 'Name',
        'active'          => 'Aktiv',
        'inactive'        => 'Inaktiv',
        'indexable'       => 'Indexierbar',
        'indexable_yes'   => 'Enthält indexierbares/JSON-LD-Markup',
        'indexable_no'    => 'Nicht indexierbar',
        'activate'        => 'Anbieter aktivieren',
        'deactivate'      => 'Anbieter deaktivieren',
        'activate_title'  => 'Anbieter aktivieren?',
        'activate_text'   => 'Der Anbieter wird aktiviert.',
        'deactivate_title'=> 'Anbieter deaktivieren?',
        'deactivate_text' => 'Der Anbieter wird deaktiviert.',
        'cancel'          => 'Abbrechen',
        'test'            => 'Verbindung testen',
        'flush_cache'     => 'Cache leeren',
        'delete_confirm'  => 'Anbieter löschen?',
        'system_locked'   => 'Systemanbieter (gesperrt)',
    ],

    // =========================
    // Review-Status (Moderation)
    // =========================
    'status' => [
        'pending'   => 'ausstehend',
        'published' => 'veröffentlicht',
        'hidden'    => 'ausgeblendet',
        'flagged'   => 'markiert',
    ],

    // =========================
    // Admin – Liste / Moderation
    // =========================
    'admin' => [
        'index_title'   => 'Bewertungen',
        'index_titel'   => 'Bewertungen', // Alias wegen häufigem Tippfehler

        'new_local'     => 'Neu (lokal)',
        'bulk_apply'    => 'Auf Auswahl anwenden',

        'responded'     => 'Beantwortet?',
        'last_reply'    => 'Letzte:',

        'filters'       => [
            'provider'  => 'Anbieter',
            'status'    => 'Status',
            'tour_id'   => 'Tour-ID',
            'stars'     => '⭐',
            'q'         => 'Text/Autor suchen...',
            'responded' => 'Beantwortet?',
        ],

        'table' => [
            'date'     => 'Datum',
            'review'   => 'Bewertung',
            'client'   => 'Kunde',
            'tour'     => 'Tour',
        ],

        'messages' => [
            'created'        => 'Bewertung erstellt.',
            'updated'        => 'Bewertung aktualisiert.',
            'deleted'        => 'Bewertung gelöscht.',
            'published'      => 'Bewertung veröffentlicht.',
            'hidden'         => 'Bewertung ausgeblendet.',
            'flagged'        => 'Bewertung markiert.',
            'unflagged'      => 'Markierung entfernt.',
            'bulk_published' => ':n Bewertungen veröffentlicht.',
            'bulk_hidden'    => ':n Bewertungen ausgeblendet.',
            'bulk_flagged'   => ':n Bewertungen markiert.',
            'bulk_deleted'   => ':n Bewertungen gelöscht.',
            'publish_min_rating' => 'Kann nicht veröffentlicht werden: Bewertung (:rating★) liegt unter dem Mindestwert (:min★).',
            'bulk_published_partial' => ':ok Bewertungen veröffentlicht. :skipped übersprungen wegen zu niedriger Bewertung (< :min★).',
        ],
    ],

    // =========================
    // Admin – Antworten
    // =========================
    'replies' => [
        'reply'            => 'Antworten',
        'title_create'     => 'Antwort – Bewertung #:id',
        'label_body'       => 'Antwort',
        'label_is_public'  => 'Öffentlich',
        'label_notify'     => 'E-Mail an Kunden senden',
        'notify_to'        => 'Wird gesendet an: :email',
        'warn_no_email'    => 'Achtung: Wir konnten keine E-Mail-Adresse des Kunden finden. Die Antwort wird gespeichert, aber keine E-Mail gesendet.',
        'saved_notified'   => 'Antwort veröffentlicht und an :email gesendet.',
        'saved_no_email'   => 'Antwort veröffentlicht. Keine E-Mail gesendet.',
        'deleted'          => 'Antwort gelöscht.',
        'visibility_ok'    => 'Sichtbarkeit aktualisiert.',
        'thread_title'     => 'Konversation – Bewertung #:id',
        'thread_empty'     => 'Keine Antworten.',
        'last_reply'       => 'Letzte:',
    ],

    // =========================
    // Admin – Review-Anfragen (Post-Kauf)
    // =========================
    'requests' => [
        'index_title' => 'Bewertungen anfordern',
        'subtitle'    => 'Sende Bewertungslinks nach dem Kauf und verwalte gesendete Anfragen.',

        'tabs' => [
            'eligible'  => 'Geeignet (Buchungen)',
            'requested' => 'Angefragt (gesendet)',
        ],

        'filters' => [
            'q_placeholder' => 'ID, Name oder E-Mail',
            'any_status'    => '— Alle —',
            'from'          => 'Von',
            'to'            => 'Bis',
        ],

        'window_days'      => 'Zeitfenster (Tage)',
        'date_column'      => 'Datumsfeld',
        'calculated_range' => 'Berechneter Bereich',
        'tour_id'          => 'Tour-ID',
        'btn_request'      => 'Bewertung anfordern',
        'no_eligible'      => 'Keine geeigneten Buchungen.',

        'table' => [
            'booking'   => 'Buchung',
            'reference' => 'Referenz',
            'sent_at'   => 'Gesendet am',
            'states'    => 'Status',
        ],

        'labels' => [
            'expires_in_days' => 'Ablauf (Tage)',
            'expires_at'      => 'Läuft ab am',
            'used_at'         => 'Verwendet am',
        ],

        'actions' => [
            'resend'         => 'Erneut senden',
            'confirm_delete' => 'Diese Anfrage löschen?',
        ],

        'status' => [
            'active'    => 'Aktiv',
            'sent'      => 'Gesendet',
            'reminded'  => 'Erinnert',
            'used'      => 'Verwendet',
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
            'used'    => 'Diese Anfrage wurde bereits verwendet.',
            'expired' => 'Diese Anfrage ist abgelaufen.',
        ],
    ],

    // =========================
    // Öffentlich – Formular
    // =========================
    'public' => [
        'form_title'   => 'Bewertung abgeben',
        'labels'       => [
            'rating'       => 'Bewertung',
            'title'        => 'Titel (optional)',
            'body'         => 'Deine Erfahrung',
            'author_name'  => 'Dein Name (optional)',
            'author_email' => 'Deine E-Mail (optional)',
            'submit'       => 'Bewertung senden',
        ],
        'thanks'       => 'Danke für deine Bewertung! 🌿',
        'thanks_body'  => 'Deine Meinung ist sehr wichtig und hilft uns, besser zu werden. Vielen Dank!',
        'thanks_farewell' => "Wir hoffen, dass du eine großartige Zeit hattest und freuen uns darauf, dich bald wiederzusehen.\n\n🇨🇷 Pura Vida mae! 🇨🇷",
        'thanks_dup'   => 'Danke! Wir hatten deine Bewertung bereits gespeichert 🙌',
        'expired'      => 'Dieser Link ist abgelaufen – danke trotzdem für deine Intention 💚',
        'used'         => 'Diese Anfrage wurde bereits verwendet.',
        'used_help'    => 'Dieser Bewertungslink wurde bereits verwendet. Wenn du denkst, dass dies ein Fehler ist oder du deinen Kommentar aktualisieren möchtest, kontaktiere uns bitte.',
        'not_found'    => 'Anfrage nicht gefunden.',
        'back_home'    => 'Zurück',
    ],

    // =========================
    // Emails
    // =========================
    'emails' => [

        'brand_from'   => 'Green Vacations CR',
        'contact_line' => 'Wenn du Hilfe benötigst, kontaktiere uns unter :email oder :phone. Besuche uns auf :url.',
        'request' => [
            'preheader_with_date' => 'Erzähl uns von deiner Erfahrung bei :tour (:date). Es dauert nur eine Minute.',
            'preheader'           => 'Erzähl uns von deiner Erfahrung bei :tour. Es dauert nur eine Minute.',
            'subject'   => 'Wie war deine Erfahrung bei :tour?',
            'cta'       => 'Meine Bewertung abgeben',
            'footer'    => 'Danke, dass du den lokalen Tourismus unterstützt. Wir freuen uns auf deinen nächsten Besuch! 🌿',
            'expires'   => '* Dieser Link ist gültig bis: :date.',
            'greeting'  => 'Hallo :name,',
            'intro'     => 'Pura Vida! 🙌 Danke, dass du uns gewählt hast. Wir würden gerne wissen, wie deine Erfahrung bei :tour war.',
            'ask'       => 'Schenkst du uns 1–2 Minuten für deine Bewertung? Das bedeutet uns wirklich viel.',
            'fallback'  => 'Falls der Button nicht funktioniert, kopiere diesen Link in deinen Browser:',
        ],
        'reply' => [
            'subject'  => 'Antwort auf deine Bewertung',
            'greeting' => 'Hallo :name,',
            'intro'    => 'Unser Team hat auf deine Bewertung geantwortet :extra.',
            'quote'    => '„:text“',
            'sign'     => '— :admin',
        ],
        'submitted' =>[
            'subject' => 'Neue Bewertung erhalten',
        ],
    ],

    // =========================
    // Frontend
    // =========================
    'front' => [
        'see_more'   => 'Mehr Bewertungen ansehen',
        'no_reviews' => 'Noch keine Bewertungen.',
    ],

    // =========================
    // Anbieter
    // =========================
    'providers' => [
        'index_title' => 'Bewertungsanbieter',
        'system_locked' => 'Systemanbieter',
        'messages' => [
            'cannot_delete_local' => 'Der „local“-Anbieter ist systemrelevant und kann nicht gelöscht werden.',
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
        'queued' => 'Synchronisierung eingereiht für :target.',
        'all'    => 'alle Anbieter',
    ],

    // =========================
    // Thread / Gespräch
    // =========================
    'thread' => [
        'title'             => 'Bewertungs-Thread #:id',
        'header'            => 'Thread – Bewertung #:id',
        'replies_header'    => 'Antworten',
        'th_date'           => 'Datum',
        'th_admin'          => 'Admin',
        'th_visible'        => 'Sichtbar',
        'th_body'           => 'Inhalt',
        'th_actions'        => 'Aktionen',
        'toggle_visibility' => 'Sichtbarkeit wechseln',
        'delete'            => 'Löschen',
        'confirm_delete'    => 'Antwort löschen?',
        'empty'             => 'Noch keine Antworten.',
    ],

    // =========================
    // Admin – Formular
    // =========================
    'form' => [
        'title_edit'       => 'Bewertung bearbeiten',
        'title_new'        => 'Neue Bewertung',
        'visible_publicly' => 'Öffentlich sichtbar',
    ],

    // =========================
    // Alias (wenn außerhalb „emails“ benutzt)
    // =========================
    'reply' => [
        'subject'          => 'Antwort auf deine Bewertung',
        'greeting'         => 'Hallo :name,',
        'about_html'       => 'über <strong>:tour</strong>',
        'about_text'       => 'über :tour',
        'intro'            => 'Unser Team hat auf deine Bewertung geantwortet :extra.',
        'quote'            => '„:text“',
        'sign'             => '— :admin',
        'closing'          => 'Bei Fragen oder wenn du deinen Kommentar erweitern möchtest, antworte einfach auf diese E-Mail. Pura Vida! 🌿',
        'rights_reserved'  => 'Alle Rechte vorbehalten',
    ],

    // Fallback für Grüße
    'traveler' => 'Reisender/Reisende',

    // =========================
    // Legacy / Kompatibilität
    // =========================
    'loaded'           => 'Bewertungen erfolgreich geladen.',
    'provider_error'   => 'Es gab ein Problem mit dem Bewertungsanbieter.',
    'service_busy'     => 'Der Dienst ist ausgelastet, bitte versuche es später erneut.',
    'unexpected_error' => 'Beim Laden der Bewertungen ist ein unerwarteter Fehler aufgetreten.',
    'anonymous'        => 'Anonym',

    'what_customers_think_about' => 'Was Kunden denken über',
    'previous_review'            => 'Vorherige Bewertung',
    'next_review'                => 'Nächste Bewertung',
    'loading'                    => 'Bewertungen werden geladen...',
    'reviews_title'              => 'Kundenbewertungen',
    'view_on_viator'             => 'Sieh dir :name auf Viator an',

    // Legacy Modal
    'open_tour_title'    => 'Tour öffnen?',
    'open_tour_text_pre' => 'Du bist dabei, die Tourseite zu öffnen für',
    'open_tour_confirm'  => 'Jetzt öffnen',
    'open_tour_cancel'   => 'Abbrechen',

    // Legacy Carousel
    'previous' => 'Zurück',
    'next'     => 'Weiter',
    'see_more' => 'Mehr anzeigen',
    'see_less' => 'Weniger anzeigen',
];
