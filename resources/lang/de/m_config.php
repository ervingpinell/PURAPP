<?php
/*************************************************************
 *  Index (durchsuchbare Anker)
 *  [01] POLICIES ZEILE 20
 *  [02] TOURTYPES ZEILE 139
 *  [03] FAQ ZEILE 198
 *  [04] TRANSLATIONS ZEILE 249
 *  [05] PROMOCODE ZEILE 359
 *  [06] CUT-OFF ZEILE 436
 *************************************************************/

return [

    // =========================================================
    // ==== POLICIES ===========================================
    // =========================================================
    'policies' => [
        // Titel / Überschriften
        'categories_title'        => 'Richtlinienkategorien',
        'sections_title'          => 'Abschnitte — :policy',

        // Spalten / Felder
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
        'slug'                    => 'URL',
        'slug_hint'               => 'optional',
        'slug_auto_hint'          => 'Wird automatisch aus dem Namen generiert, wenn es leer gelassen wird',
        'slug_edit_hint'          => 'Ändert die URL der Richtlinie. Verwenden Sie nur Kleinbuchstaben, Zahlen und Bindestriche.',
        'updated'                  => 'Richtlinie erfolgreich aktualisiert.',



        // Kategorienliste: Aktionen
        'new_category'            => 'Neue Kategorie',
        'view_sections'           => 'Abschnitte anzeigen',
        'edit'                    => 'Bearbeiten',
        'activate_category'       => 'Kategorie aktivieren',
        'deactivate_category'     => 'Kategorie deaktivieren',
        'delete'                  => 'Löschen',
        'delete_category_confirm' => 'Diese Kategorie und ALLE zugehörigen Abschnitte löschen?<br>Dies kann nicht rückgängig gemacht werden.',
        'no_categories'           => 'Keine Kategorien gefunden.',
        'edit_category'           => 'Kategorie bearbeiten',

        // Formulare (Kategorie)
        'title_label'             => 'Titel',
        'description_label'       => 'Beschreibung',
        'register'                => 'Erstellen',
        'save_changes'            => 'Änderungen speichern',
        'close'                   => 'Schließen',

        // Abschnitte
        'back_to_categories'      => 'Zurück zu Kategorien',
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
        'section_content'         => 'Inhalt',
        'base_content_hint'       => 'Dies ist der Haupttext der Richtlinie. Er wird automatisch in andere Sprachen übersetzt, wenn er erstellt wird, aber Sie können jede Übersetzung später bearbeiten.',

        // Öffentlich
        'page_title'              => 'Richtlinien',
        'no_policies'             => 'Derzeit sind keine Richtlinien verfügbar.',
        'section'                 => 'Abschnitt',
        'cancellation_policy'     => 'Stornierungsrichtlinie',
        'refund_policy'           => 'Erstattungsrichtlinie',
        'no_cancellation_policy'  => 'Keine Stornierungsrichtlinie konfiguriert.',
        'no_refund_policy'        => 'Keine Erstattungsrichtlinie konfiguriert.',

        // Nachrichten (Kategorien)
        'category_created'        => 'Kategorie erfolgreich erstellt.',
        'category_updated'        => 'Kategorie erfolgreich aktualisiert.',
        'category_activated'      => 'Kategorie erfolgreich aktiviert.',
        'category_deactivated'    => 'Kategorie erfolgreich deaktiviert.',
        'category_deleted'        => 'Kategorie erfolgreich gelöscht.',

        // --- Neue Schlüssel (Refactor / Utilities) ---
        'untitled'                => 'Ohne Titel',
        'no_content'              => 'Kein Inhalt verfügbar.',
        'display_name'            => 'Anzeigename',
        'name'                    => 'Name',
        'name_base'               => 'Basisname',
        'name_base_help'          => 'Kurzer Bezeichner/Slug des Abschnitts (nur intern).',
        'translation_content'     => 'Inhalt',
        'locale'                  => 'Sprache',
        'save'                    => 'Speichern',
        'name_base_label'         => 'Basisname',
        'translation_name'        => 'Übersetzter Name',
        'lang_autodetect_hint'    => 'Sie können in jeder Sprache schreiben; automatische Erkennung.',
        'bulk_edit_sections'      => 'Schnellbearbeitung von Abschnitten',
        'bulk_edit_hint'          => 'Alle Änderungen an Abschnitten werden zusammen mit der Kategorietraduction gespeichert, wenn Sie „Speichern“ klicken.',
        'no_changes_made'         => 'Keine Änderungen vorgenommen.',
        'no_sections_found'       => 'Keine Abschnitte gefunden.',
        'editing_locale'          => 'Bearbeiten',

        // Nachrichten (Abschnitte)
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

        // Buttons / SweetAlert
        'create'                  => 'Erstellen',
        'activate'                => 'Aktivieren',
        'deactivate'              => 'Deaktivieren',
        'cancel'                  => 'Abbrechen',
        'ok'                      => 'OK',
        'validation_errors'       => 'Validierungsfehler',
        'error_title'             => 'Fehler',

        // Abschnittsbestätigungen
        'confirm_create_section'      => 'Diesen Abschnitt erstellen?',
        'confirm_edit_section'        => 'Änderungen an diesem Abschnitt speichern?',
        'confirm_deactivate_section'  => 'Diesen Abschnitt wirklich deaktivieren?',
        'confirm_activate_section'    => 'Diesen Abschnitt wirklich aktivieren?',
        'confirm_delete_section'      => 'Diesen Abschnitt wirklich löschen?<br>Dies kann nicht rückgängig gemacht werden.',
    ],

    // =========================================================
    // ==== TOURTYPES ==========================================
    // =========================================================
    'tourtypes' => [
        // Titel / Überschriften
        'title'                   => 'Tourarten',
        'new'                     => 'Tourart hinzufügen',

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

        // Modal-Titel
        'edit_title'              => 'Tourart bearbeiten',
        'create_title'            => 'Tourart erstellen',

        // Platzhalter / Hinweise
        'examples_placeholder'    => 'Z. B.: Abenteuer, Natur, Erholung',
        'duration_placeholder'    => 'Z. B.: 4 Stunden, 8 Stunden',
        'suggested_duration_hint' => 'Empfohlenes Format: „4 Stunden“, „8 Stunden“.',
        'keep_default_hint'       => 'Lassen Sie „4 Stunden“, falls zutreffend; anpassbar.',
        'optional'                => 'optional',

        // Bestätigungen
        'confirm_delete'          => '„:name“ löschen? Diese Aktion kann nicht rückgängig gemacht werden.',
        'confirm_activate'        => '„:name“ aktivieren?',
        'confirm_deactivate'      => '„:name“ deaktivieren?',

        // Meldungen (Flash)
        'created_success'         => 'Tourart erfolgreich erstellt.',
        'updated_success'         => 'Tourart erfolgreich aktualisiert.',
        'deleted_success'         => 'Tourart erfolgreich gelöscht.',
        'activated_success'       => 'Tourart erfolgreich aktiviert.',
        'deactivated_success'     => 'Tourart erfolgreich deaktiviert.',
        'in_use_error'            => 'Löschen nicht möglich: Diese Tourart wird verwendet.',
        'unexpected_error'        => 'Unerwarteter Fehler. Bitte erneut versuchen.',

        // Validierung / generisch
        'validation_errors'       => 'Bitte die markierten Felder prüfen.',
        'error_title'             => 'Fehler',
    ],

    // =========================================================
    // ==== FAQ ================================================
    // =========================================================
    'faq' => [
        // Titel
        'title'            => 'Häufig gestellte Fragen',

        // Felder
        'question'         => 'Frage',
        'answer'           => 'Antwort',
        'status'           => 'Status',
        'actions'          => 'Aktionen',
        'active'           => 'Aktiv',
        'inactive'         => 'Inaktiv',

        // Aktionen
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
        'read_less'        => 'Weniger anzeigen',

        // Bestätigungen
        'confirm_create'   => 'Diese Frage erstellen?',
        'confirm_edit'     => 'Änderungen speichern?',
        'confirm_delete'   => 'Diese Frage wirklich löschen?<br>Dies kann nicht rückgängig gemacht werden.',
        'confirm_activate' => 'Diese Frage wirklich aktivieren?',
        'confirm_deactivate'=> 'Diese Frage wirklich deaktivieren?',

        // Validierung / Fehler
        'validation_errors'=> 'Validierungsfehler',
        'error_title'      => 'Fehler',

        // Meldungen
        'created_success'      => 'FAQ erfolgreich erstellt.',
        'updated_success'      => 'FAQ erfolgreich aktualisiert.',
        'deleted_success'      => 'FAQ erfolgreich gelöscht.',
        'activated_success'    => 'FAQ erfolgreich aktiviert.',
        'deactivated_success'  => 'FAQ erfolgreich deaktiviert.',
        'unexpected_error'     => 'Unerwarteter Fehler.',
    ],

    // =========================================================
    // ==== TRANSLATIONS =======================================
    // =========================================================
    'translations' => [
        // Titel / allgemeine Texte
        'title'                 => 'Übersetzungsverwaltung',
        'index_title'           => 'Übersetzungsverwaltung',
        'select_entity_title'   => ':entity zum Übersetzen auswählen',
        'edit_title'            => 'Übersetzung bearbeiten',
        'main_information'      => 'Hauptinformationen',
        'ok'                    => 'OK',
        'save'                  => 'Speichern',
        'validation_errors'     => 'Validierungsfehler',
        'updated_success'       => 'Übersetzung erfolgreich aktualisiert.',
        'unexpected_error'      => 'Übersetzung konnte nicht aktualisiert werden.',
                'editing'         => 'Bearbeiten',
        'policy_name'     => 'Richtlinientitel',
        'policy_content'  => 'Inhalt',
        'policy_sections' => 'Richtlinienabschnitte',
        'section'         => 'Abschnitt',
        'section_name'    => 'Abschnittsname',
        'section_content' => 'Abschnittsinhalt',

        // Sprachwahl
        'choose_locale_title'   => 'Sprache auswählen',
        'choose_locale_hint'    => 'Wählen Sie die Sprache, in die dieses Element übersetzt werden soll.',
        'select_language_title' => 'Sprache auswählen',
        'select_language_intro' => 'Wählen Sie die Sprache, in die dieses Element übersetzt werden soll.',
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
        'no_items'              => 'Keine :entity zum Übersetzen verfügbar.',

        // Formularfelder
        'name'                  => 'Name',
        'description'           => 'Beschreibung',
        'content'               => 'Inhalt',
        'overview'              => 'Übersicht',
        'itinerary'             => 'Reiseplan',
        'itinerary_name'        => 'Name des Reiseplans',
        'itinerary_description' => 'Beschreibung des Reiseplans',
        'itinerary_items'       => 'Reiseplan-Elemente',
        'item'                  => 'Element',
        'item_title'            => 'Elementtitel',
        'item_description'      => 'Elementbeschreibung',
        'sections'              => 'Abschnitte',
        'edit'                  => 'Bearbeiten',
        'close'                 => 'Schließen',
        'actions'               => 'Aktionen',

        // Modulare Feldlabels
        'fields' => [
            'name'                  => 'Name',
            'title'                 => 'Titel',
            'overview'              => 'Übersicht',
            'description'           => 'Beschreibung',
            'content'               => 'Inhalt',
            'duration'              => 'Dauer',
            'question'              => 'Frage',
            'answer'                => 'Antwort',

            'itinerary'             => 'Reiseplan',
            'itinerary_name'        => 'Name des Reiseplans',
            'itinerary_description' => 'Beschreibung des Reiseplans',
            'item'                  => 'Element',
            'item_title'            => 'Elementtitel',
            'item_description'      => 'Elementbeschreibung',
        ],

        // Overrides je ENTITÄT und FELD
        'entity_fields' => [
            'tour_types' => [
                'duration' => 'Empfohlene Dauer',
                'name'     => 'Name der Tourart',
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
            'itinerary_items'  => 'Reiseplan-Elemente',
            'amenities'        => 'Ausstattungen',
            'faqs'             => 'FAQs',
            'policies'         => 'Richtlinien',
            'tour_types'       => 'Tourarten',
        ],

        // Entitätsnamen (Singular)
        'entities_singular' => [
            'tours'            => 'Tour',
            'itineraries'      => 'Reiseplan',
            'itinerary_items'  => 'Reiseplan-Element',
            'amenities'        => 'Ausstattung',
            'faqs'             => 'FAQ',
            'policies'         => 'Richtlinie',
            'tour_types'       => 'Tourart',
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
            'operation'   => 'Operation',
            'valid_from'  => 'Gültig ab',
            'valid_until' => 'Gültig bis',
            'usage_limit' => 'Nutzungsgrenze',
            'promocode_hint'        => 'Nach der Anwendung wird der Gutschein beim Absenden des Formulars gespeichert und die Snapshots des Verlaufs aktualisiert.',

        ],

        'types' => [
            'percent' => '%',
            'amount'  => '€',
        ],

        'symbols' => [
            'percent'  => '%',
            'currency' => '€',
        ],

        'table' => [
            'code'         => 'Code',
            'discount'     => 'Rabatt',
            'operation'    => 'Operation',
            'validity'     => 'Gültigkeit',
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
            'active'    => 'Aktiv',
            'expired'   => 'Abgelaufen',
        ],

        'actions' => [
            'generate'         => 'Erstellen',
            'delete'           => 'Löschen',
            'toggle_operation' => 'Zwischen Add/Subtract wechseln',
        ],

        'labels' => [
            'unlimited_placeholder' => 'Leer = unbegrenzt',
            'unlimited_hint'        => 'Leer lassen für unbegrenzte Nutzung. 1 = einmalige Nutzung.',
            'no_limit'              => '(ohne Limit)',
            'remaining'             => 'verbleibend',
        ],

        'confirm_delete' => 'Diesen Code wirklich löschen?',
        'empty'          => 'Keine Gutscheincodes verfügbar.',

        'messages' => [
            'created_success'        => 'Gutscheincode erfolgreich erstellt.',
            'deleted_success'        => 'Gutscheincode erfolgreich gelöscht.',
            'percent_over_100'       => 'Prozentsatz darf 100 nicht überschreiten.',
            'code_exists_normalized' => 'Dieser Code (ohne Leerzeichen und Groß-/Kleinschreibung) existiert bereits.',
            'invalid_or_used'        => 'Ungültiger oder bereits verwendeter Code.',
            'valid'                  => 'Gültiger Code.',
            'server_error'           => 'Serverfehler. Bitte erneut versuchen.',
            'operation_updated'      => 'Operation erfolgreich aktualisiert.',
        ],

        'operations' => [
            'add'           => 'Addieren',
            'subtract'      => 'Subtrahieren',
            'make_add'      => 'Zu „Addieren“ wechseln',
            'make_subtract' => 'Zu „Subtrahieren“ wechseln',
            'surcharge'     => 'Aufschlag',
            'discount'      => 'Rabatt',
        ],
    ],


    // =========================================================
    // ==== CUTOFF =============================================
    // =========================================================
    'cut-off' => [
        // Titel / Überschriften
        'title'       => 'Cut-off-Einstellungen',
        'header'      => 'Buchungseinstellungen',
        'server_time' => 'Serverzeit (:tz)',

        // Tabs
        'tabs' => [
            'global'   => 'Global (Standard)',
            'tour'     => 'Sperre pro Tour',
            'schedule' => 'Sperre pro Zeitplan',
            'summary'  => 'Übersicht',
            'help'     => 'Hilfe',
        ],

        // Felder
        'fields' => [
            'cutoff_hour'       => 'Sperrzeit (24h)',
            'cutoff_hour_short' => 'Sperre (24h)',
            'lead_days'         => 'Vorlauftage',
            'timezone'          => 'Zeitzone',
            'tour'              => 'Tour',
            'schedule'          => 'Zeitplan',
        ],

        // Selects / Platzhalter
        'selects' => [
            'tour' => '— Tour auswählen —',
            'time' => '— Zeit auswählen —',
        ],

        // Labels
        'labels' => [
            'status' => 'Status',
        ],

        // Badges / Chips
        'badges' => [
            'inherits'            => 'Erbt global',
            'override'            => 'Sperre',
            'inherit_tour_global' => 'Erbt von Tour/Global',
            'schedule'            => 'Zeitplan',
            'tour'                => 'Tour',
            'global'              => 'Global',
        ],

        // Aktionen
        'actions' => [
            'save_global'   => 'Global speichern',
            'save_tour'     => 'Toursperre speichern',
            'save_schedule' => 'Zeitplansperre speichern',
            'clear'         => 'Sperre löschen',
            'confirm'       => 'Bestätigen',
            'cancel'        => 'Abbrechen',
            'actions'       => 'Aktionen',

        ],

        // Bestätigungen (Modale)
        'confirm' => [
            'tour' => [
                'title' => 'Toursperre speichern?',
                'text'  => 'Für diese Tour wird eine spezifische Sperre angewendet. Leer lassen zum Vererben.',
            ],
            'schedule' => [
                'title' => 'Zeitplansperre speichern?',
                'text'  => 'Für diesen Zeitplan wird eine spezifische Sperre angewendet. Leer lassen zum Vererben.',
            ],
        ],

        // Übersicht
        'summary' => [
            'tour_title'            => 'Sperren nach Tour',
            'no_tour_overrides'     => 'Keine Sperren auf Tour-Ebene.',
            'schedule_title'        => 'Sperren nach Zeitplan',
            'no_schedule_overrides' => 'Keine Sperren auf Zeitplan-Ebene.',
            'search_placeholder'    => 'Tour oder Zeitplan suchen…',
        ],

        // Flash / Toasts
        'flash' => [
            'success_title' => 'Erfolg',
            'error_title'   => 'Fehler',
        ],

        // Hilfe
        'help' => [
            'title'      => 'Wie funktioniert das?',
            'global'     => 'Standardwert für die gesamte Website.',
            'tour'       => 'Hat eine Tour Cut-off/Vorlauftage, hat sie Vorrang vor Global.',
            'schedule'   => 'Hat ein Zeitplan eine Sperre, hat er Vorrang vor der Tour.',
            'precedence' => 'Vorrang',
        ],

        // Hinweise
        'hints' => [
            // Global
            'cutoff_example'    => 'z. B. :ex. Nach dieser Zeit ist „heute“ nicht mehr verfügbar.',
            'pattern_24h'       => '24-h-Format HH:MM (z. B. 09:30, 18:00).',
            'cutoff_behavior'   => 'Nach der Sperrzeit verschiebt sich das nächstmögliche Datum auf den nächsten Tag.',
            'lead_days'         => 'Minimale Vorlauftage (0 erlaubt Buchung am selben Tag, sofern Sperrzeit nicht überschritten).',
            'lead_days_detail'  => 'Erlaubter Bereich: 0–30. 0 erlaubt Tagesbuchung, wenn Sperrzeit nicht überschritten.',
            'timezone_source'   => 'Aus config(\'app.timezone\').',

            // Tour
            'pick_tour'             => 'Wählen Sie zuerst eine Tour; definieren Sie dann optional die Sperre.',
            'tour_override_explain' => 'Wenn Sie nur eines setzen (Sperre oder Tage), erbt das andere von Global.',
            'clear_button_hint'     => 'Mit „Sperre löschen“ zum Vererben zurückkehren.',
            'leave_empty_inherit'   => 'Leer lassen zum Vererben.',

            // Zeitplan
            'pick_schedule'             => 'Wählen Sie anschließend den Zeitplan.',
            'schedule_override_explain' => 'Werte hier haben Vorrang vor der Tour. Leer lassen zum Vererben.',
            'schedule_precedence_hint'  => 'Vorrang: Zeitplan → Tour → Global.',

            // Übersicht
            'dash_means_inherit' => '„—“ bedeutet: Wert wird vererbt.',
        ],
    ],

];
