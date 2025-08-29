<?php
/*************************************************************
 *  KONFIGURATIONS-MODUL – ÜBERSETZUNGEN (DE)
 *  Datei: resources/lang/de/m_config.php
 *
 *  Index (durchsuchbare Anker)
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
        // Titel / Überschriften
        'categories_title'        => 'Richtlinienkategorien',
        'sections_title'          => 'Abschnitte — :policy',

        // Spalten / allgemeine Felder
        'id'                      => 'ID',
        'internal_name'           => 'Interner Name',
        'title_current_locale'    => 'Titel',
        'validity_range'          => 'Gültigkeitszeitraum',
        'valid_from'              => 'Gültig ab',
        'valid_to'                => 'Gültig bis',
        'status'                  => 'Status',
        'sections'                => 'Abschnitte',
        'actions'                 => 'Aktionen',
        'active'                  => 'Aktiv',
        'inactive'                => 'Inaktiv',

        // Kategorieliste: Aktionen
        'new_category'            => 'Neue Kategorie',
        'view_sections'           => 'Abschnitte anzeigen',
        'edit'                    => 'Bearbeiten',
        'activate_category'       => 'Kategorie aktivieren',
        'deactivate_category'     => 'Kategorie deaktivieren',
        'delete'                  => 'Löschen',
        'delete_category_confirm' => 'Diese Kategorie und ALLE ihre Abschnitte löschen?<br>Dies kann nicht rückgängig gemacht werden.',
        'no_categories'           => 'Keine Kategorien gefunden.',
        'edit_category'           => 'Kategorie bearbeiten',

        // Formulare (Kategorie)
        'title_label'             => 'Titel',
        'description_label'       => 'Beschreibung',
        'register'                => 'Erstellen',
        'save_changes'            => 'Änderungen speichern',
        'close'                   => 'Schließen',

        // Abschnitte
        'back_to_categories'      => 'Zurück zu den Kategorien',
        'new_section'             => 'Neuer Abschnitt',
        'key'                     => 'Schlüssel',
        'order'                   => 'Reihenfolge',
        'activate_section'        => 'Abschnitt aktivieren',
        'deactivate_section'      => 'Abschnitt deaktivieren',
        'delete_section_confirm'  => 'Diesen Abschnitt wirklich löschen?<br>Dies kann nicht rückgängig gemacht werden.',
        'no_sections'             => 'Keine Abschnitte gefunden.',
        'edit_section'            => 'Abschnitt bearbeiten',
        'internal_key_optional'   => 'Interner Schlüssel (optional)',
        'content_label'           => 'Inhalt',

        // Öffentlich
        'page_title'              => 'Richtlinien',
        'no_policies'             => 'Derzeit sind keine Richtlinien verfügbar.',
        'section'                 => 'Abschnitt',
        'cancellation_policy'     => 'Stornierungsbedingungen',
        'refund_policy'           => 'Rückerstattungsbedingungen',
        'no_cancellation_policy'  => 'Keine Stornierungsbedingungen konfiguriert.',
        'no_refund_policy'        => 'Keine Rückerstattungsbedingungen konfiguriert.',

        // Meldungen (Kategorien)
        'category_created'        => 'Kategorie erfolgreich erstellt.',
        'category_updated'        => 'Kategorie erfolgreich aktualisiert.',
        'category_activated'      => 'Kategorie erfolgreich aktiviert.',
        'category_deactivated'    => 'Kategorie erfolgreich deaktiviert.',
        'category_deleted'        => 'Kategorie erfolgreich gelöscht.',

        // --- NEUE SCHLÜSSEL (Refactor / Utilities) ---
        'untitled'                => 'Ohne Titel',
        'no_content'              => 'Kein Inhalt verfügbar.',
        'display_name'            => 'Anzeigename',
        'name'                    => 'Name',
        'name_base'               => 'Basisname',
        'name_base_help'          => 'Kurzer Bezeichner/Slug für den Abschnitt (nur intern).',
        'translation_content'     => 'Inhalt',
        'locale'                  => 'Sprache',
        'save'                    => 'Speichern',
        'name_base_label'         => 'Basisname',
        'translation_name'        => 'Übersetzter Name',
        'lang_autodetect_hint'    => 'Sie können in jeder Sprache schreiben; sie wird automatisch erkannt.',
        'bulk_edit_sections'      => 'Schnellbearbeitung der Abschnitte',
        'bulk_edit_hint'          => 'Änderungen an allen Abschnitten werden zusammen mit der Kategorietraduction beim Klick auf „Speichern“ gesichert.',
        'no_changes_made'         => 'Keine Änderungen vorgenommen.',
        'no_sections_found'       => 'Keine Abschnitte gefunden.',
        'editing_locale'          => 'Bearbeitung',

        // Meldungen (Abschnitte)
        'section_created'         => 'Abschnitt erfolgreich erstellt.',
        'section_updated'         => 'Abschnitt erfolgreich aktualisiert.',
        'section_activated'       => 'Abschnitt erfolgreich aktiviert.',
        'section_deactivated'     => 'Abschnitt erfolgreich deaktiviert.',
        'section_deleted'         => 'Abschnitt erfolgreich gelöscht.',

        // Generische Modulmeldungen
        'created_success'         => 'Erfolgreich erstellt.',
        'updated_success'         => 'Erfolgreich aktualisiert.',
        'deleted_success'         => 'Erfolgreich gelöscht.',
        'activated_success'       => 'Erfolgreich aktiviert.',
        'deactivated_success'     => 'Erfolgreich deaktiviert.',
        'unexpected_error'        => 'Ein unerwarteter Fehler ist aufgetreten.',

        // Buttons / allgemeine Texte (SweetAlert)
        'create'                  => 'Erstellen',
        'activate'                => 'Aktivieren',
        'deactivate'              => 'Deaktivieren',
        'cancel'                  => 'Abbrechen',
        'ok'                      => 'OK',
        'validation_errors'       => 'Es liegen Validierungsfehler vor',
        'error_title'             => 'Fehler',

        // Abschnittsbezogene Bestätigungen
        'confirm_create_section'      => 'Diesen Abschnitt erstellen?',
        'confirm_edit_section'        => 'Änderungen an diesem Abschnitt speichern?',
        'confirm_deactivate_section'  => 'Diesen Abschnitt wirklich deaktivieren?',
        'confirm_activate_section'    => 'Diesen Abschnitt wirklich aktivieren?',
        'confirm_delete_section'      => 'Diesen Abschnitt wirklich löschen?<br>Diese Aktion kann nicht rückgängig gemacht werden.',
    ],

    // =========================================================
    // ==== TOURTYPES ==========================================
    // =========================================================
    'tourtypes' => [
        // Titel / Überschriften
        'title'                   => 'Tourtypen',
        'new'                     => 'Tourtyp hinzufügen',

        // Spalten / Felder
        'id'                      => 'ID',
        'name'                    => 'Name',
        'description'             => 'Beschreibung',
        'duration'                => 'Dauer',
        'status'                  => 'Status',
        'actions'                 => 'Aktionen',
        'active'                  => 'Aktiv',
        'inactive'                => 'Inaktiv',

        // Buttons / Aktionen
        'register'                => 'Speichern',
        'update'                  => 'Aktualisieren',
        'save'                    => 'Speichern',
        'close'                   => 'Schließen',
        'cancel'                  => 'Abbrechen',
        'edit'                    => 'Bearbeiten',
        'delete'                  => 'Löschen',
        'activate'                => 'Aktivieren',
        'deactivate'              => 'Deaktivieren',

        // Modaltitel
        'edit_title'              => 'Tourtyp bearbeiten',
        'create_title'            => 'Tourtyp erstellen',

        // Platzhalter / Hinweise
        'examples_placeholder'    => 'Z. B.: Abenteuer, Natur, Entspannung',
        'duration_placeholder'    => 'Z. B.: 4 Stunden, 8 Stunden',
        'suggested_duration_hint' => 'Empfohlenes Format: „4 Stunden“, „8 Stunden“.',
        'keep_default_hint'       => 'Lassen Sie „4 Stunden“, falls zutreffend; Sie können es ändern.',
        'optional'                => 'optional',

        // Bestätigungen
        'confirm_delete'          => '„:name“ wirklich löschen? Dies kann nicht rückgängig gemacht werden.',
        'confirm_activate'        => '„:name“ wirklich aktivieren?',
        'confirm_deactivate'      => '„:name“ wirklich deaktivieren?',

        // Meldungen (Flash)
        'created_success'         => 'Tourtyp erfolgreich erstellt.',
        'updated_success'         => 'Tourtyp erfolgreich aktualisiert.',
        'deleted_success'         => 'Tourtyp erfolgreich gelöscht.',
        'activated_success'       => 'Tourtyp erfolgreich aktiviert.',
        'deactivated_success'     => 'Tourtyp erfolgreich deaktiviert.',
        'in_use_error'            => 'Löschen nicht möglich: Dieser Tourtyp wird verwendet.',
        'unexpected_error'        => 'Ein unerwarteter Fehler ist aufgetreten. Bitte erneut versuchen.',

        // Validierung / generisch
        'validation_errors'       => 'Bitte prüfen Sie die hervorgehobenen Felder.',
        'error_title'             => 'Fehler',
    ],

    // =========================================================
    // ==== FAQ ================================================
    // =========================================================
    'faq' => [
        // Titel / Kopfzeile
        'title'            => 'Häufige Fragen (FAQ)',

        // Felder / Spalten
        'question'         => 'Frage',
        'answer'           => 'Antwort',
        'status'           => 'Status',
        'actions'          => 'Aktionen',
        'active'           => 'Aktiv',
        'inactive'         => 'Inaktiv',

        // Schaltflächen / Aktionen
        'new'              => 'Neue Frage',
        'create'           => 'Erstellen',
        'save'             => 'Speichern',
        'edit'             => 'Bearbeiten',
        'delete'           => 'Löschen',
        'activate'         => 'Aktivieren',
        'deactivate'       => 'Deaktivieren',
        'cancel'           => 'Abbrechen',
        'close'            => 'Schließen',
        'ok'               => 'OK',

        // UI
        'read_more'        => 'Mehr lesen',
        'read_less'        => 'Weniger lesen',

        // Bestätigungen
        'confirm_create'   => 'Diese Frage erstellen?',
        'confirm_edit'     => 'Änderungen an dieser Frage speichern?',
        'confirm_delete'   => 'Diese Frage wirklich löschen?<br>Dies kann nicht rückgängig gemacht werden.',
        'confirm_activate' => 'Diese Frage wirklich aktivieren?',
        'confirm_deactivate'=> 'Diese Frage wirklich deaktivieren?',

        // Validierung / Fehler
        'validation_errors'=> 'Es liegen Validierungsfehler vor',
        'error_title'      => 'Fehler',

        // Meldungen (Flash)
        'created_success'      => 'Frage erfolgreich erstellt.',
        'updated_success'      => 'Frage erfolgreich aktualisiert.',
        'deleted_success'      => 'Frage erfolgreich gelöscht.',
        'activated_success'    => 'Frage erfolgreich aktiviert.',
        'deactivated_success'  => 'Frage erfolgreich deaktiviert.',
        'unexpected_error'     => 'Ein unerwarteter Fehler ist aufgetreten.',
    ],

    // =========================================================
    // ==== TRANSLATIONS =======================================
    // =========================================================
    'translations' => [
        // Titel / allgemeine Texte
        'title'                 => 'Übersetzungsverwaltung',
        'index_title'           => 'Übersetzungsverwaltung',
        'select_entity_title'   => 'Wähle :entity zum Übersetzen aus',
        'edit_title'            => 'Übersetzung bearbeiten',
        'main_information'      => 'Hauptinformationen',
        'ok'                    => 'OK',
        'save'                  => 'Speichern',
        'validation_errors'     => 'Es sind Validierungsfehler aufgetreten',
        'updated_success'       => 'Übersetzung erfolgreich aktualisiert.',
        'unexpected_error'      => 'Die Übersetzung konnte nicht aktualisiert werden.',

        // Sprachauswahl
        'choose_locale_title'   => 'Sprache auswählen',
        'choose_locale_hint'    => 'Wähle die Sprache, in die dieses Element übersetzt werden soll.',
        'select_language_title' => 'Sprache auswählen',
        'select_language_intro' => 'Wähle die Sprache, in die dieses Element übersetzt werden soll.',
        'languages' => [
            'es' => 'Spanisch',
            'en' => 'Englisch',
            'fr' => 'Französisch',
            'pt' => 'Portugiesisch',
            'de' => 'Deutsch',
        ],

        // Listen / Buttons
        'select'                => 'Auswählen',
        'id_unavailable'        => 'ID nicht verfügbar',
        'no_items'              => 'Keine :entity zum Übersetzen vorhanden.',

        // Gemeinsame Formularfelder
        'name'                  => 'Name',
        'description'           => 'Beschreibung',
        'content'               => 'Inhalt',
        'overview'              => 'Übersicht',
        'itinerary'             => 'Reiseplan',
        'itinerary_name'        => 'Name des Reiseplans',
        'itinerary_description' => 'Beschreibung des Reiseplans',
        'itinerary_items'       => 'Reiseplan-Einträge',
        'item'                  => 'Eintrag',
        'item_title'            => 'Titel des Eintrags',
        'item_description'      => 'Beschreibung des Eintrags',
        'sections'              => 'Abschnitte',
        'edit'                  => 'Bearbeiten',
        'close'                 => 'Schließen',
        'actions'               => 'Aktionen',

        // === Modulare Feldbezeichnungen =======================
        'fields' => [
            // Generisch
            'name'                  => 'Name',
            'title'                 => 'Titel',
            'overview'              => 'Übersicht',
            'description'           => 'Beschreibung',
            'content'               => 'Inhalt',
            'duration'              => 'Dauer',
            'question'              => 'Frage',
            'answer'                => 'Antwort',

            // Reiseplan / Einträge
            'itinerary'             => 'Reiseplan',
            'itinerary_name'        => 'Name des Reiseplans',
            'itinerary_description' => 'Beschreibung des Reiseplans',
            'item'                  => 'Eintrag',
            'item_title'            => 'Titel des Eintrags',
            'item_description'      => 'Beschreibung des Eintrags',
        ],

        // === Entity-spezifische Überschreibungen (optional) ===
        'entity_fields' => [
            'tour_types' => [
                'duration' => 'Empfohlene Dauer',
                'name'     => 'Name des Tourtyps',
            ],
            'faqs' => [
                'question' => 'Frage (kundenseitig sichtbar)',
                'answer'   => 'Antwort (kundenseitig sichtbar)',
            ],
        ],

        // Entitätsnamen (Plural)
        'entities' => [
            'tours'            => 'Touren',
            'itineraries'      => 'Reisepläne',
            'itinerary_items'  => 'Reiseplan-Einträge',
            'amenities'        => 'Ausstattungen',
            'faqs'             => 'FAQs',
            'policies'         => 'Richtlinien',
            'tour_types'       => 'Tourtypen',
        ],

        // Entitätsnamen (Singular)
        'entities_singular' => [
            'tours'            => 'tour',
            'itineraries'      => 'reiseplan',
            'itinerary_items'  => 'reiseplan-eintrag',
            'amenities'        => 'ausstattung',
            'faqs'             => 'faq',
            'policies'         => 'richtlinie',
            'tour_types'       => 'tourtyp',
        ],
    ],

    // =========================================================
    // ==== PROMOCODE ==========================================
    // =========================================================
'promocode' => [
    'title'        => 'Gutscheincodes',
    'create_title' => 'Neuen Gutscheincode erstellen',
    'list_title'   => 'Vorhandene Gutscheincodes',

    'success_title' => 'Erfolg',
    'error_title'   => 'Fehler',

    'fields' => [
        'code'        => 'Code',
        'discount'    => 'Rabatt',
        'type'        => 'Typ',
        'valid_from'  => 'Gültig ab',
        'valid_until' => 'Gültig bis',
        'usage_limit' => 'Nutzungslimit',
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
        'discount'     => 'Rabatt',
        'validity'     => 'Gültigkeitszeitraum',
        'date_status'  => 'Status (Datum)',
        'usage'        => 'Nutzungen',
        'usage_status' => 'Status (Nutzung)',
        'actions'      => 'Aktionen',
    ],

    'status' => [
        'used'      => 'Verwendet',
        'available' => 'Verfügbar',
    ],

    'date_status' => [
        'scheduled' => 'Geplant',
        'active'    => 'Gültig',
        'expired'   => 'Abgelaufen',
    ],

    'actions' => [
        'generate' => 'Erstellen',
        'delete'   => 'Löschen',
    ],

    'labels' => [
        'unlimited_placeholder' => 'Leer = unbegrenzt',
        'unlimited_hint'        => 'Leer lassen für unbegrenzte Nutzungen. Für einmalige Nutzung 1 eintragen.',
        'no_limit'              => '(kein Limit)',
        'remaining'             => 'verbleibend',
    ],

    'confirm_delete' => 'Möchten Sie diesen Code wirklich löschen?',
    'empty'          => 'Keine Gutscheincodes vorhanden.',

    'messages' => [
        'created_success'        => 'Gutscheincode erfolgreich erstellt.',
        'deleted_success'        => 'Gutscheincode erfolgreich gelöscht.',
        'percent_over_100'       => 'Der Prozentsatz darf nicht größer als 100 sein.',
        'code_exists_normalized' => 'Dieser Code (ohne Leerzeichen und Groß-/Kleinschreibung) existiert bereits.',
        'invalid_or_used'        => 'Ungültiger Code, nicht gültig oder keine verbleibenden Nutzungen.',
        'valid'                  => 'Code gültig.',
        'server_error'           => 'Serverfehler, bitte versuche es erneut.',
    ],
],
];
