<?php

/*************************************************************
 *  KONFIGURATIONS-MODUL – ÜBERSETZUNGEN (DE)
 *  Datei: resources/lang/de/m_config.php
 *
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
        'categories_title'        => 'Richtlinien',
        'sections_title'          => 'Abschnitte',

        // Spalten / gemeinsame Felder
        'id'                      => 'ID',
        'internal_name'           => 'Interner Name',
        'title_current_locale'    => 'Titel',
        'validity_range'          => 'Gültigkeitszeitraum',
        'valid_from'              => 'Gültig von',
        'valid_to'                => 'Gültig bis',
        'status'                  => 'Status',
        'sections'                => 'Abschnitte',
        'actions'                 => 'Aktionen',
        'active'                  => 'Aktiv',
        'inactive'                => 'Inaktiv',
        'slug'                    => 'URL',
        'slug_hint'               => 'optional',
        'slug_auto_hint'          => 'Wird automatisch aus dem Namen generiert, wenn leer gelassen.',
        'slug_edit_hint'          => 'Ändert die URL der Richtlinie. Verwende nur Kleinbuchstaben, Zahlen und Bindestriche.',
        'updated'                 => 'Richtlinie erfolgreich aktualisiert.',
        'propagate_to_all_langs' => 'Diese Änderung auf alle Sprachen anwenden (EN, FR, DE, PT)',
        'propagate_hint'         => 'Wird automatisch aus dem aktuellen Text übersetzt und überschreibt vorhandene Übersetzungen in diesen Sprachen.',
        'update_base_es'         => 'Auch die Basis (ES) aktualisieren',
        'update_base_hint'       => 'Überschreibt Name und Inhalt der Richtlinie in der Basistabelle (Spanisch). Nur verwenden, wenn sich auch der Originaltext ändern soll.',
        'filter_active'    => 'Aktiv',
        'filter_inactive'  => 'Inaktiv',
        'filter_archived'  => 'Archiviert',
        'filter_all'       => 'Alle',

        'slug_hint'      => 'kleingeschrieben, keine Leerzeichen, mit Bindestrichen getrennt',
        'slug_auto_hint' => 'Wenn leer, wird es automatisch aus dem Titel erzeugt.',
        'slug_edit_hint' => 'Das Ändern dieser URL kann vorhandene öffentliche Links beeinflussen.',

        'valid_from' => 'Gültig ab',
        'valid_to'   => 'Gültig bis',

        'move_to_trash'  => 'In Papierkorb verschieben',
        'in_trash'       => 'Im Papierkorb',
        'moved_to_trash' => 'Die Kategorie wurde in den Papierkorb verschoben.',

        'restore_category'         => 'Wiederherstellen',
        'restore_category_confirm' => 'Diese Kategorie und alle ihre Abschnitte wiederherstellen?',
        'restored_ok'              => 'Die Kategorie wurde erfolgreich wiederhergestellt.',

        'delete_permanently'         => 'Dauerhaft löschen',
        'delete_permanently_confirm' => 'Diese Kategorie und alle ihre Abschnitte dauerhaft löschen? Diese Aktion kann nicht rückgängig gemacht werden.',
        'deleted_permanently'        => 'Die Kategorie und ihre Abschnitte wurden dauerhaft gelöscht.',
        'restore' => 'Wiederherstellen',
        'force_delete_confirm' => 'Diese Kategorie und alle ihre Abschnitte dauerhaft löschen? Diese Aktion kann nicht rückgängig gemacht werden.',
        'created' => 'Richtlinienkategorie erfolgreich erstellt.',

        // Kategorien-Liste: Aktionen
        'new_category'            => 'Neue Kategorie',
        'view_sections'           => 'Abschnitte anzeigen',
        'edit'                    => 'Bearbeiten',
        'activate_category'       => 'Kategorie aktivieren',
        'deactivate_category'     => 'Kategorie deaktivieren',
        'delete'                  => 'Löschen',
        'delete_category_confirm' => 'Diese Kategorie und ALLE ihre Abschnitte löschen?<br>Diese Aktion kann nicht rückgängig gemacht werden.',
        'no_categories'           => 'Es wurden keine Kategorien gefunden.',
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
        'delete_section_confirm'  => 'Möchtest du diesen Abschnitt wirklich löschen?<br>Diese Aktion kann nicht rückgängig gemacht werden.',
        'no_sections'             => 'Es wurden keine Abschnitte gefunden.',
        'edit_section'            => 'Abschnitt bearbeiten',
        'internal_key_optional'   => 'Interner Schlüssel (optional)',
        'content_label'           => 'Inhalt',
        'section_content'         => 'Inhalt',
        'base_content_hint'       => 'Dies ist der Haupttext der Richtlinie. Beim Erstellen wird er automatisch in andere Sprachen übersetzt, aber du kannst jede Übersetzung später bearbeiten.',

        // Öffentlich
        'page_title'              => 'Richtlinien',
        'no_policies'             => 'Derzeit sind keine Richtlinien verfügbar.',
        'section'                 => 'Abschnitt',
        'cancellation_policy'     => 'Stornorichtlinie',
        'refund_policy'           => 'Erstattungsrichtlinie',
        'no_cancellation_policy'  => 'Es ist keine Stornorichtlinie konfiguriert.',
        'no_refund_policy'        => 'Es ist keine Erstattungsrichtlinie konfiguriert.',

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
        'name_base_help'          => 'Kurzer Bezeichner/Slug des Abschnitts (nur intern).',
        'translation_content'     => 'Inhalt',
        'locale'                  => 'Sprache',
        'save'                    => 'Speichern',
        'name_base_label'         => 'Basisname',
        'translation_name'        => 'Übersetzter Name',
        'lang_autodetect_hint'    => 'Du kannst in jeder Sprache schreiben; sie wird automatisch erkannt.',
        'bulk_edit_sections'      => 'Schnellbearbeitung von Abschnitten',
        'bulk_edit_hint'          => 'Die Änderungen an allen Abschnitten werden zusammen mit der Übersetzung der Kategorie gespeichert, wenn du auf „Speichern“ klickst.',
        'no_changes_made'         => 'Es wurden keine Änderungen vorgenommen.',
        'no_sections_found'       => 'Es wurden keine Abschnitte gefunden.',
        'editing_locale'          => 'Bearbeitung von',

        // Meldungen (Abschnitte)
        'section_created'         => 'Abschnitt erfolgreich erstellt.',
        'section_updated'         => 'Abschnitt erfolgreich aktualisiert.',
        'section_activated'       => 'Abschnitt erfolgreich aktiviert.',
        'section_deactivated'     => 'Abschnitt erfolgreich deaktiviert.',
        'section_deleted'         => 'Abschnitt erfolgreich gelöscht.',

        // Generische Meldungen des Moduls
        'created_success'         => 'Erfolgreich erstellt.',
        'updated_success'         => 'Erfolgreich aktualisiert.',
        'deleted_success'         => 'Erfolgreich gelöscht.',
        'activated_success'       => 'Erfolgreich aktiviert.',
        'deactivated_success'     => 'Erfolgreich deaktiviert.',
        'unexpected_error'        => 'Es ist ein unerwarteter Fehler aufgetreten.',

        // Buttons / allgemeine Texte (SweetAlert)
        'create'                  => 'Erstellen',
        'activate'                => 'Aktivieren',
        'deactivate'              => 'Deaktivieren',
        'cancel'                  => 'Abbrechen',
        'ok'                      => 'OK',
        'validation_errors'       => 'Es liegen Validierungsfehler vor',
        'error_title'             => 'Fehler',

        // Spezifische Bestätigungen für Abschnitte
        'confirm_create_section'      => 'Diesen Abschnitt erstellen?',
        'confirm_edit_section'        => 'Änderungen an diesem Abschnitt speichern?',
        'confirm_deactivate_section'  => 'Möchtest du diesen Abschnitt wirklich deaktivieren?',
        'confirm_activate_section'    => 'Möchtest du diesen Abschnitt wirklich aktivieren?',
        'confirm_delete_section'      => 'Möchtest du diesen Abschnitt wirklich löschen?<br>Diese Aktion kann nicht rückgängig gemacht werden.',
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
        'keep_default_hint'       => 'Lass „4 Stunden“, falls es passt; du kannst es ändern.',
        'optional'                => 'optional',

        // Bestätigungen
        'confirm_delete'          => '„:name“ wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.',
        'confirm_activate'        => '„:name“ wirklich aktivieren?',
        'confirm_deactivate'      => '„:name“ wirklich deaktivieren?',

        // Meldungen (Flash)
        'created_success'         => 'Tourtyp erfolgreich erstellt.',
        'updated_success'         => 'Tourtyp erfolgreich aktualisiert.',
        'deleted_success'         => 'Tourtyp erfolgreich gelöscht.',
        'activated_success'       => 'Tourtyp erfolgreich aktiviert.',
        'deactivated_success'     => 'Tourtyp erfolgreich deaktiviert.',
        'in_use_error'            => 'Löschen nicht möglich: Dieser Tourtyp wird verwendet.',
        'unexpected_error'        => 'Es ist ein unerwarteter Fehler aufgetreten. Bitte versuche es erneut.',

        // Validierung / generisch
        'validation_errors'       => 'Bitte prüfe die hervorgehobenen Felder.',
        'error_title'             => 'Fehler',
    ],

    // =========================================================
    // ==== FAQ ================================================
    // =========================================================
    'faq' => [
        // Titel / Kopfzeile
        'title'            => 'FAQ',

        // Felder / Spalten
        'question'         => 'Frage',
        'answer'           => 'Antwort',
        'status'           => 'Status',
        'actions'          => 'Aktionen',
        'active'           => 'Aktiv',
        'inactive'         => 'Inaktiv',

        // Buttons / Aktionen
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
        'confirm_create'   => 'Diese häufig gestellte Frage erstellen?',
        'confirm_edit'     => 'Änderungen an dieser häufig gestellten Frage speichern?',
        'confirm_delete'   => 'Möchtest du diese häufig gestellte Frage wirklich löschen?<br>Diese Aktion kann nicht rückgängig gemacht werden.',
        'confirm_activate' => 'Möchtest du diese häufig gestellte Frage wirklich aktivieren?',
        'confirm_deactivate' => 'Möchtest du diese häufig gestellte Frage wirklich deaktivieren?',

        // Validierung / Fehler
        'validation_errors' => 'Es liegen Validierungsfehler vor',
        'error_title'      => 'Fehler',

        // Meldungen (Flash)
        'created_success'      => 'Häufig gestellte Frage erfolgreich erstellt.',
        'updated_success'      => 'Häufig gestellte Frage erfolgreich aktualisiert.',
        'deleted_success'      => 'Häufig gestellte Frage erfolgreich gelöscht.',
        'activated_success'    => 'Häufig gestellte Frage erfolgreich aktiviert.',
        'deactivated_success'  => 'Häufig gestellte Frage erfolgreich deaktiviert.',
        'unexpected_error'     => 'Es ist ein unerwarteter Fehler aufgetreten.',
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
        'validation_errors'     => 'Es liegen Validierungsfehler vor',
        'updated_success'       => 'Übersetzung erfolgreich aktualisiert.',
        'unexpected_error'      => 'Die Übersetzung konnte nicht aktualisiert werden.',

        'editing'            => 'Bearbeiten',
        'policy_name'        => 'Name der Richtlinie',
        'policy_content'     => 'Inhalt',
        'policy_sections'    => 'Abschnitte der Richtlinie',
        'section'            => 'Abschnitt',
        'section_name'       => 'Name des Abschnitts',
        'section_content'    => 'Inhalt des Abschnitts',

        // Sprachauswahl (Bildschirm und Hilfen)
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
        'no_items'              => 'Es sind keine :entity zum Übersetzen verfügbar.',

        // Gemeinsame Felder der Übersetzungsformulare
        'name'                  => 'Name',
        'description'           => 'Beschreibung',
        'content'               => 'Inhalt',
        'overview'              => 'Überblick',
        'itinerary'             => 'Reiseverlauf',
        'itinerary_name'        => 'Name des Reiseverlaufs',
        'itinerary_description' => 'Beschreibung des Reiseverlaufs',
        'itinerary_items'       => 'Elemente des Reiseverlaufs',
        'item'                  => 'Element',
        'item_title'            => 'Titel des Elements',
        'item_description'      => 'Beschreibung des Elements',
        'sections'              => 'Abschnitte',
        'edit'                  => 'Bearbeiten',
        'close'                 => 'Schließen',
        'actions'               => 'Aktionen',

        // === Modulare Feldbezeichnungen ========================
        // Verwendung: __('m_config.translations.fields.<feld>')
        'fields' => [
            // Generisch
            'name'                  => 'Name',
            'title'                 => 'Titel',
            'overview'              => 'Überblick',
            'description'           => 'Beschreibung',
            'content'               => 'Inhalt',
            'duration'              => 'Dauer',
            'question'              => 'Frage',
            'answer'                => 'Antwort',

            // Reiseverlauf / Elemente (Tour-Partial)
            'itinerary'             => 'Reiseverlauf',
            'itinerary_name'        => 'Name des Reiseverlaufs',
            'itinerary_description' => 'Beschreibung des Reiseverlaufs',
            'item'                  => 'Element',
            'item_title'            => 'Titel des Elements',
            'item_description'      => 'Beschreibung des Elements',
        ],

        // === Überschreibungen nach ENTITÄT und FELD ===========
        // Im Blade: zuerst entity_fields.<type>.<field> prüfen,
        // sonst fields.<field> verwenden.
        'entity_fields' => [
            'tour_types' => [
                'duration' => 'Empfohlene Dauer',
                'name'     => 'Name des Tourtyps',
            ],
            'faqs' => [
                'question' => 'Frage (für Kunden sichtbar)',
                'answer'   => 'Antwort (für Kunden sichtbar)',
            ],
        ],

        // Namen der Entitäten (Plural)
        'entities' => [
            'tours'            => 'Touren',
            'itineraries'      => 'Reiseverläufe',
            'itinerary_items'  => 'Elemente des Reiseverlaufs',
            'amenities'        => 'Ausstattungen',
            'faqs'             => 'Häufig gestellte Fragen',
            'policies'         => 'Richtlinien',
            'tour_types'       => 'Tourtypen',
        ],

        // Namen der Entitäten (Singular)
        'entities_singular' => [
            'tours'            => 'Tour',
            'itineraries'      => 'Reiseverlauf',
            'itinerary_items'  => 'Element des Reiseverlaufs',
            'amenities'        => 'Ausstattung',
            'faqs'             => 'häufig gestellte Frage',
            'policies'         => 'Richtlinie',
            'tour_types'       => 'Tourtyp',
        ],
    ],

    // =========================================================
    // ==== PROMOCODE ==========================================
    // =========================================================
    'promocode' => [
        'title'        => 'Promo-Codes',
        'create_title' => 'Neuen Promo-Code generieren',
        'list_title'   => 'Vorhandene Promo-Codes',

        'success_title' => 'Erfolg',
        'error_title'   => 'Fehler',

        'fields' => [
            'code'        => 'Code',
            'discount'    => 'Betrag',

            'type'        => 'Typ',
            'operation'   => 'Operation',
            'valid_from'  => 'Gültig von',
            'valid_until' => 'Gültig bis',
            'usage_limit' => 'Nutzungsbegrenzung',
            'promocode_hint'        => 'Nach dem Anwenden wird der Gutschein beim Absenden des Formulars gespeichert und die Verlaufssnapshots aktualisiert.',
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
            'discount'     => 'Betrag',
            'operation'    => 'Operation',
            'validity'     => 'Gültigkeit',
            'date_status'  => 'Status (Datum)',
            'usage'        => 'Verwendung',
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
            'generate' => 'Generieren',
            'delete'   => 'Löschen',
            'toggle_operation' => 'Zwischen Addieren/Subtrahieren wechseln',
        ],

        'labels' => [
            'unlimited_placeholder' => 'Leer = unbegrenzt',
            'unlimited_hint'        => 'Leer lassen für unbegrenzte Nutzung. 1 bedeutet einmalige Nutzung.',
            'no_limit'              => '(ohne Begrenzung)',
            'remaining'             => 'verbleibend',
        ],

        'confirm_delete' => 'Möchtest du diesen Code wirklich löschen?',
        'empty'          => 'Es sind keine Promo-Codes verfügbar.',

        'messages' => [
            'created_success'         => 'Promo-Code erfolgreich erstellt.',
            'deleted_success'         => 'Promo-Code erfolgreich gelöscht.',
            'percent_over_100'        => 'Der Prozentsatz darf nicht größer als 100 sein.',
            'code_exists_normalized'  => 'Dieser Code (ohne Leerzeichen und Groß-/Kleinschreibung) existiert bereits.',
            'invalid_or_used'         => 'Ungültiger oder bereits verwendeter Code.',
            'valid'                   => 'Gültiger Code.',
            'server_error'            => 'Serverfehler, bitte versuche es erneut.',
            'operation_updated'       => 'Operation erfolgreich aktualisiert.',
        ],

        'operations' => [
            'add'            => 'Addieren',
            'subtract'       => 'Subtrahieren',
            'make_add'       => 'Zu „Addieren“ wechseln',
            'make_subtract'  => 'Zu „Subtrahieren“ wechseln',
            'surcharge'      => 'Aufschlag',
            'discount'       => 'Rabatt',
        ],
    ],

    // =========================================================
    // ==== CUTOFF =============================================
    // =========================================================
    'cut-off' => [
        // Titel / Überschriften
        'title'       => 'Cut-off',
        'header'      => 'Cut-off-Konfiguration',
        'server_time' => 'Serverzeit (:tz)',

        // Tabs
        'tabs' => [
            'global'   => 'Global (Standard)',
            'tour'     => 'Sperre nach Tour',
            'schedule' => 'Sperre nach Uhrzeit',
            'summary'  => 'Übersicht',
            'help'     => 'Hilfe',
        ],

        // Felder
        'fields' => [
            'cutoff_hour'       => 'Cut-off-Zeit (24h)',
            'cutoff_hour_short' => 'Cut-off (24h)',
            'lead_days'         => 'Vorlauftage',
            'timezone'          => 'Zeitzone',
            'tour'              => 'Tour',
            'schedule'          => 'Uhrzeit',
            'actions'           => 'Aktionen'
        ],

        // Selects / Platzhalter
        'selects' => [
            'tour' => '— Tour auswählen —',
            'time' => '— Uhrzeit auswählen —',
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
            'schedule'            => 'Uhrzeit',
            'tour'                => 'Tour',
            'global'              => 'Global',
        ],

        // Aktionen
        'actions' => [
            'save_global'   => 'Global speichern',
            'save_tour'     => 'Tour-Sperre speichern',
            'save_schedule' => 'Uhrzeit-Sperre speichern',
            'clear'         => 'Sperre entfernen',
            'confirm'       => 'Bestätigen',
            'cancel'        => 'Abbrechen',
        ],

        // Bestätigungen (Modale)
        'confirm' => [
            'tour' => [
                'title' => 'Tour-Sperre speichern?',
                'text'  => 'Es wird eine spezifische Sperre für diese Tour angewendet. Leer lassen, um zu erben.',
            ],
            'schedule' => [
                'title' => 'Uhrzeit-Sperre speichern?',
                'text'  => 'Es wird eine spezifische Sperre für diese Uhrzeit angewendet. Leer lassen, um zu erben.',
            ],
        ],

        // Übersicht
        'summary' => [
            'tour_title'            => 'Sperren nach Tour',
            'no_tour_overrides'     => 'Es gibt keine Sperren auf Tour-Ebene.',
            'schedule_title'        => 'Sperren nach Uhrzeit',
            'no_schedule_overrides' => 'Es gibt keine Sperren auf Uhrzeit-Ebene.',
            'search_placeholder'    => 'Tour oder Uhrzeit suchen…',
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
            'tour'       => 'Wenn eine Tour einen konfigurierten Cut-off/Vorlauftage hat, hat sie Vorrang vor Global.',
            'schedule'   => 'Wenn eine Uhrzeit der Tour eine Sperre hat, hat sie Vorrang vor der Tour.',
            'precedence' => 'Priorität',
        ],

        // Hinweise / Hints
        'hints' => [
            // Global
            'cutoff_example'    => 'Z. B.: :ex. Nach dieser Zeit ist „heute“ nicht mehr verfügbar.',
            'pattern_24h'       => '24-Stunden-Format HH:MM (z. B. 09:30, 18:00).',
            'cutoff_behavior'   => 'Wenn die Cut-off-Zeit bereits überschritten ist, verschiebt sich das früheste verfügbare Datum auf den nächsten Tag.',
            'lead_days'         => 'Mindestanzahl an Vorlauftagen (0 erlaubt Buchungen für heute, sofern die Cut-off-Zeit nicht überschritten ist).',
            'lead_days_detail'  => 'Erlaubter Bereich: 0–30. 0 erlaubt Buchungen am selben Tag, wenn die Cut-off-Zeit noch nicht erreicht ist.',
            'timezone_source'   => 'Wird aus config(\'app.timezone\') übernommen.',

            // Tour
            'pick_tour'             => 'Wähle zuerst eine Tour und definiere anschließend deren Sperre (optional).',
            'tour_override_explain' => 'Wenn du nur einen Wert (Cut-off oder Tage) festlegst, erbt der andere den globalen Wert.',
            'clear_button_hint'     => 'Verwende „Sperre entfernen“, um wieder zu erben.',
            'leave_empty_inherit'   => 'Leer lassen, um zu erben.',

            // Uhrzeit (Schedule)
            'pick_schedule'             => 'Wähle anschließend die Uhrzeit der Tour.',
            'schedule_override_explain' => 'Die hier gesetzten Werte haben Vorrang vor denen der Tour. Leer lassen, um zu erben.',
            'schedule_precedence_hint'  => 'Priorität: Uhrzeit → Tour → Global.',

            // Übersicht
            'dash_means_inherit' => 'Das Symbol „—“ bedeutet, dass der Wert geerbt wird.',
        ],
    ],

];
