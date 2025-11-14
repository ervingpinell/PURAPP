<?php

/*************************************************************
 *  MODUL FÜR ÜBERSETZUNGEN: TOURS
 *  Datei: resources/lang/de/m_tours.php
 *************************************************************/

return [

    // =========================================================
    // [01] COMMON
    // =========================================================
    'common' => [
        'success_title'      => 'Erfolg',
        'error_title'        => 'Fehler',
        'people'             => 'Personen',
        'hours'              => 'Stunden',
        'success'            => 'Erfolg',
        'error'              => 'Fehler',
        'cancel'             => 'Abbrechen',
        'confirm_delete'     => 'Ja, löschen',
        'unspecified'        => 'Ohne Angabe',
        'no_description'     => 'Keine Beschreibung',
        'required_fields_title' => 'Pflichtfelder',
        'required_fields_text'  => 'Bitte fülle die Pflichtfelder aus: Name und maximale Kapazität',
        'active'             => 'Aktiv',
        'inactive'           => 'Inaktiv',
        'notice'             => 'Hinweis',
        'na'                 => 'Nicht konfiguriert',
        'create'             => 'Erstellen',
        'info'               => 'Information',
        'close'              => 'Schließen',
        'required'           => 'Dieses Feld ist erforderlich.',
        'add'                => 'Hinzufügen',
        'translating'        => 'Wird übersetzt...',
        'error_translating'  => 'Der Text konnte nicht übersetzt werden.',
    ],

    // =========================================================
    // [02] AMENITY
    // =========================================================
    'amenity' => [
        'fields' => [
            'name' => 'Name',
            'icon' => 'Symbol (FontAwesome)',
        ],

        'status' => [
            'active'   => 'Aktiv',
            'inactive' => 'Inaktiv',
        ],

        'ui' => [
            'page_title'    => 'Ausstattungen',
            'page_heading'  => 'Verwaltung von Ausstattungen',
            'list_title'    => 'Liste der Ausstattungen',

            'add'            => 'Ausstattung hinzufügen',
            'create_title'   => 'Ausstattung registrieren',
            'edit_title'     => 'Ausstattung bearbeiten',
            'save'           => 'Speichern',
            'update'         => 'Aktualisieren',
            'cancel'         => 'Abbrechen',
            'close'          => 'Schließen',
            'state'          => 'Status',
            'actions'        => 'Aktionen',
            'delete_forever' => 'Endgültig löschen',

            'processing' => 'Wird verarbeitet...',
            'applying'   => 'Wird angewendet...',
            'deleting'   => 'Wird gelöscht...',

            'toggle_on'  => 'Ausstattung aktivieren',
            'toggle_off' => 'Ausstattung deaktivieren',

            'toggle_confirm_on_title'  => 'Ausstattung aktivieren?',
            'toggle_confirm_off_title' => 'Ausstattung deaktivieren?',
            'toggle_confirm_on_html'   => 'Die Ausstattung <b>:label</b> wird aktiviert.',
            'toggle_confirm_off_html'  => 'Die Ausstattung <b>:label</b> wird deaktiviert.',

            'delete_confirm_title' => 'Endgültig löschen?',
            'delete_confirm_html'  => '<b>:label</b> wird gelöscht und dies kann nicht rückgängig gemacht werden.',

            'yes_continue' => 'Ja, fortfahren',
            'yes_delete'   => 'Ja, löschen',

            'item_this' => 'diese Ausstattung',
        ],

        'success' => [
            'created'     => 'Ausstattung erfolgreich erstellt.',
            'updated'     => 'Ausstattung erfolgreich aktualisiert.',
            'activated'   => 'Ausstattung erfolgreich aktiviert.',
            'deactivated' => 'Ausstattung erfolgreich deaktiviert.',
            'deleted'     => 'Ausstattung endgültig gelöscht.',
        ],

        'error' => [
            'create' => 'Die Ausstattung konnte nicht erstellt werden.',
            'update' => 'Die Ausstattung konnte nicht aktualisiert werden.',
            'toggle' => 'Der Status der Ausstattung konnte nicht geändert werden.',
            'delete' => 'Die Ausstattung konnte nicht gelöscht werden.',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Ungültiger Name',
                'required' => 'Das Feld :attribute ist erforderlich.',
                'string'   => 'Das Feld :attribute muss eine Zeichenkette sein.',
                'max'      => 'Das Feld :attribute darf :max Zeichen nicht überschreiten.',
            ],
        ],

        'hints' => [
            'fontawesome' => 'Benutze FontAwesome-Klassen, z. B.: "fas fa-check".',
        ],
    ],

    // =========================================================
    // [03] SCHEDULE
    // =========================================================
    'schedule' => [
        'fields' => [
            'start_time'     => 'Beginn',
            'end_time'       => 'Ende',
            'label'          => 'Bezeichnung',
            'label_optional' => 'Bezeichnung (optional)',
            'max_capacity'   => 'Max. Kapazität',
            'active'         => 'Aktiv',
        ],

        'status' => [
            'active'   => 'Aktiv',
            'inactive' => 'Inaktiv',
        ],

        'ui' => [
            'page_title'        => 'Tourzeiten',
            'page_heading'      => 'Verwaltung von Tourzeiten',

            'general_title'     => 'Allgemeine Zeiten',
            'new_schedule'      => 'Neue Zeit',
            'new_general_title' => 'Neue allgemeine Zeit',
            'new'               => 'Neu',
            'edit_schedule'     => 'Zeit bearbeiten',
            'edit_global'       => 'Bearbeiten (global)',

            'assign_existing'    => 'Bestehende zuweisen',
            'assign_to_tour'     => 'Zeit ":tour" zuweisen',
            'select_schedule'    => 'Eine Zeit auswählen',
            'choose'             => 'Auswählen',
            'assign'             => 'Zuweisen',
            'new_for_tour_title' => 'Neue Zeit für ":tour"',

            'time_range'        => 'Zeitplan',
            'state'             => 'Status',
            'actions'           => 'Aktionen',
            'schedule_state'    => 'Zeit',
            'assignment_state'  => 'Zuweisung',
            'no_general'        => 'Es gibt keine allgemeinen Zeiten.',
            'no_tour_schedules' => 'Diese Tour hat noch keine Zeiten.',
            'no_label'          => 'Ohne Bezeichnung',
            'assigned_count'    => 'zugewiesene Zeit(en)',

            'toggle_global_title'     => 'Aktivieren/Deaktivieren (global)',
            'toggle_global_on_title'  => 'Zeit (global) aktivieren?',
            'toggle_global_off_title' => 'Zeit (global) deaktivieren?',
            'toggle_global_on_html'   => '<b>:label</b> wird für alle Touren aktiviert.',
            'toggle_global_off_html'  => '<b>:label</b> wird für alle Touren deaktiviert.',

            'toggle_on_tour'          => 'Für diese Tour aktivieren',
            'toggle_off_tour'         => 'Für diese Tour deaktivieren',
            'toggle_assign_on_title'  => 'Für diese Tour aktivieren?',
            'toggle_assign_off_title' => 'Für diese Tour deaktivieren?',
            'toggle_assign_on_html'   => 'Die Zuweisung wird für <b>:tour</b> <b>aktiv</b> sein.',
            'toggle_assign_off_html'  => 'Die Zuweisung wird für <b>:tour</b> <b>inaktiv</b> sein.',

            'detach_from_tour'     => 'Von Tour entfernen',
            'detach_confirm_title' => 'Von der Tour entfernen?',
            'detach_confirm_html'  => 'Die Zeit wird von <b>:tour</b> <b>entfernt</b>.',

            'delete_forever'       => 'Löschen (global)',
            'delete_confirm_title' => 'Endgültig löschen?',
            'delete_confirm_html'  => '<b>:label</b> (global) wird gelöscht und dies kann nicht rückgängig gemacht werden.',

            'yes_continue' => 'Ja, fortfahren',
            'yes_delete'   => 'Ja, löschen',
            'yes_detach'   => 'Ja, entfernen',

            'this_schedule' => 'diese Zeit',
            'this_tour'     => 'diese Tour',

            'processing'     => 'Wird verarbeitet...',
            'applying'       => 'Wird angewendet...',
            'deleting'       => 'Wird gelöscht...',
            'removing'       => 'Wird entfernt...',
            'saving_changes' => 'Änderungen werden gespeichert...',
            'save'           => 'Speichern',
            'save_changes'   => 'Änderungen speichern',
            'cancel'         => 'Abbrechen',

            'missing_fields_title' => 'Daten fehlen',
            'missing_fields_text'  => 'Bitte prüfe die Pflichtfelder (Beginn, Ende und Kapazität).',
            'could_not_save'       => 'Konnte nicht gespeichert werden',
        ],

        'success' => [
            'created'                => 'Zeit erfolgreich erstellt.',
            'updated'                => 'Zeit erfolgreich aktualisiert.',
            'activated_global'       => 'Zeit erfolgreich aktiviert (global).',
            'deactivated_global'     => 'Zeit erfolgreich deaktiviert (global).',
            'attached'               => 'Zeit der Tour zugewiesen.',
            'detached'               => 'Zeit erfolgreich von der Tour entfernt.',
            'assignment_activated'   => 'Zuweisung für diese Tour aktiviert.',
            'assignment_deactivated' => 'Zuweisung für diese Tour deaktiviert.',
            'deleted'                => 'Zeit erfolgreich gelöscht.',
        ],

        'error' => [
            'create'               => 'Beim Erstellen der Zeit ist ein Problem aufgetreten.',
            'update'               => 'Beim Aktualisieren der Zeit ist ein Problem aufgetreten.',
            'toggle'               => 'Der globale Status der Zeit konnte nicht geändert werden.',
            'attach'               => 'Die Zeit konnte der Tour nicht zugewiesen werden.',
            'detach'               => 'Die Zeit konnte nicht von der Tour entfernt werden.',
            'assignment_toggle'    => 'Der Status der Zuweisung konnte nicht geändert werden.',
            'not_assigned_to_tour' => 'Die Zeit ist dieser Tour nicht zugewiesen.',
            'delete'               => 'Beim Löschen der Zeit ist ein Problem aufgetreten.',
        ],

        'placeholders' => [
            'morning' => 'z. B.: Morgen',
        ],
    ],

    // =========================================================
    // [04] ITINERARY_ITEM
    // =========================================================
    'itinerary_item' => [
        'fields' => [
            'title'       => 'Titel',
            'description' => 'Beschreibung',
        ],

        'status' => [
            'active'   => 'Aktiv',
            'inactive' => 'Inaktiv',
        ],

        'ui' => [
            'list_title'    => 'Reiseplan-Elemente',
            'add_item'      => 'Element hinzufügen',
            'register_item' => 'Element registrieren',
            'edit_item'     => 'Element bearbeiten',
            'save'          => 'Speichern',
            'update'        => 'Aktualisieren',
            'cancel'        => 'Abbrechen',
            'state'         => 'Status',
            'actions'       => 'Aktionen',
            'see_more'      => 'Mehr anzeigen',
            'see_less'      => 'Weniger anzeigen',

            'toggle_on'  => 'Element aktivieren',
            'toggle_off' => 'Element deaktivieren',

            'delete_forever'       => 'Endgültig löschen',
            'delete_confirm_title' => 'Endgültig löschen?',
            'delete_confirm_html'  => '<b>:label</b> wird gelöscht und dies kann nicht rückgängig gemacht werden.',
            'yes_delete'           => 'Ja, löschen',
            'item_this'            => 'dieses Element',

            'processing' => 'Wird verarbeitet...',
            'applying'   => 'Wird angewendet...',
            'deleting'   => 'Wird gelöscht...',
        ],

        'success' => [
            'created'     => 'Reiseplan-Element erfolgreich erstellt.',
            'updated'     => 'Element erfolgreich aktualisiert.',
            'activated'   => 'Element erfolgreich aktiviert.',
            'deactivated' => 'Element erfolgreich deaktiviert.',
            'deleted'     => 'Element endgültig gelöscht.',
        ],

        'error' => [
            'create' => 'Das Element konnte nicht erstellt werden.',
            'update' => 'Das Element konnte nicht aktualisiert werden.',
            'toggle' => 'Der Status des Elements konnte nicht geändert werden.',
            'delete' => 'Das Element konnte nicht gelöscht werden.',
        ],

        'validation' => [
            'title' => [
                'required' => 'Das Feld :attribute ist erforderlich.',
                'string'   => 'Das Feld :attribute muss eine Zeichenkette sein.',
                'max'      => 'Das Feld :attribute darf :max Zeichen nicht überschreiten.',
            ],
            'description' => [
                'required' => 'Das Feld :attribute ist erforderlich.',
                'string'   => 'Das Feld :attribute muss eine Zeichenkette sein.',
                'max'      => 'Das Feld :attribute darf :max Zeichen nicht überschreiten.',
            ],
        ],
    ],

    // =========================================================
    // [05] ITINERARY
    // =========================================================
    'itinerary' => [
        'fields' => [
            'name'                 => 'Name des Reiseplans',
            'description'          => 'Beschreibung',
            'description_optional' => 'Beschreibung (optional)',
            'items'                => 'Elemente',
            'item_title'           => 'Titel des Elements',
            'item_description'     => 'Beschreibung des Elements',
        ],

        'status' => [
            'active'   => 'Aktiv',
            'inactive' => 'Inaktiv',
        ],

        'ui' => [
            'page_title'    => 'Reisepläne und Elemente',
            'page_heading'  => 'Reisepläne und Verwaltung von Elementen',
            'new_itinerary' => 'Neuer Reiseplan',

            'assign'        => 'Zuweisen',
            'edit'          => 'Bearbeiten',
            'save'          => 'Speichern',
            'cancel'        => 'Abbrechen',
            'close'         => 'Schließen',
            'create_title'  => 'Neuen Reiseplan erstellen',
            'create_button' => 'Erstellen',

            'toggle_on'  => 'Reiseplan aktivieren',
            'toggle_off' => 'Reiseplan deaktivieren',
            'toggle_confirm_on_title'  => 'Reiseplan aktivieren?',
            'toggle_confirm_off_title' => 'Reiseplan deaktivieren?',
            'toggle_confirm_on_html'   => 'Der Reiseplan <b>:label</b> wird <b>aktiv</b> sein.',
            'toggle_confirm_off_html'  => 'Der Reiseplan <b>:label</b> wird <b>inaktiv</b> sein.',
            'yes_continue' => 'Ja, fortfahren',

            'assign_title'          => 'Elemente :name zuweisen',
            'drag_hint'             => 'Ziehe und lege die Elemente ab, um die Reihenfolge festzulegen.',
            'drag_handle'           => 'Zum Neuordnen ziehen',
            'select_one_title'      => 'Du musst mindestens ein Element auswählen',
            'select_one_text'       => 'Bitte wähle mindestens ein Element aus, um fortzufahren.',
            'assign_confirm_title'  => 'Ausgewählte Elemente zuweisen?',
            'assign_confirm_button' => 'Ja, zuweisen',
            'assigning'             => 'Wird zugewiesen...',

            'no_items_assigned'       => 'Es sind keine Elemente diesem Reiseplan zugewiesen.',
            'itinerary_this'          => 'dieser Reiseplan',
            'processing'              => 'Wird verarbeitet...',
            'saving'                  => 'Wird gespeichert...',
            'activating'              => 'Wird aktiviert...',
            'deactivating'            => 'Wird deaktiviert...',
            'applying'                => 'Wird angewendet...',
            'deleting'                => 'Wird gelöscht...',
            'flash_success_title'     => 'Erfolg',
            'flash_error_title'       => 'Fehler',
            'validation_failed_title' => 'Konnte nicht verarbeitet werden',
        ],
        'modal' => [
            'create_itinerary' => 'Reiseplan erstellen',
        ],

        'success' => [
            'created'        => 'Reiseplan erfolgreich erstellt.',
            'updated'        => 'Reiseplan erfolgreich aktualisiert.',
            'activated'      => 'Reiseplan erfolgreich aktiviert.',
            'deactivated'    => 'Reiseplan erfolgreich deaktiviert.',
            'deleted'        => 'Reiseplan endgültig gelöscht.',
            'items_assigned' => 'Elemente erfolgreich zugewiesen.',
        ],

        'error' => [
            'create'  => 'Der Reiseplan konnte nicht erstellt werden.',
            'update'  => 'Der Reiseplan konnte nicht aktualisiert werden.',
            'toggle'  => 'Der Status des Reiseplans konnte nicht geändert werden.',
            'delete'  => 'Der Reiseplan konnte nicht gelöscht werden.',
            'assign'  => 'Die Elemente konnten nicht zugewiesen werden.',
        ],

        'validation' => [
            'name_required' => 'Du musst einen Namen für den Reiseplan angeben.',
            'name' => [
                'required' => 'Der Name des Reiseplans ist erforderlich.',
                'string'   => 'Der Name muss Text sein.',
                'max'      => 'Der Name darf 255 Zeichen nicht überschreiten.',
                'unique'   => 'Es existiert bereits ein Reiseplan mit diesem Namen.',
            ],
            'description' => [
                'string' => 'Die Beschreibung muss Text sein.',
                'max'    => 'Die Beschreibung darf 1000 Zeichen nicht überschreiten.',
            ],
            'items' => [
                'required'      => 'Du musst mindestens ein Element auswählen.',
                'array'         => 'Das Format der Elemente ist ungültig.',
                'min'           => 'Du musst mindestens ein Element auswählen.',
                'order_integer' => 'Die Reihenfolge muss eine ganze Zahl sein.',
                'order_min'     => 'Die Reihenfolge darf nicht negativ sein.',
                'order_max'     => 'Die Reihenfolge darf 9999 nicht überschreiten.',
            ],
        ],

    ],

    // =========================================================
    // [06] LANGUAGE
    // =========================================================
    'language' => [
        'fields' => [
            'name' => 'Sprache',
            'code' => 'Code',
        ],

        'status' => [
            'active'   => 'Aktiv',
            'inactive' => 'Inaktiv',
        ],

        'ui' => [
            'page_title'   => 'Tour-Sprachen',
            'page_heading' => 'Verwaltung von Sprachen',
            'list_title'   => 'Sprachenliste',

            'table' => [
                'id'      => 'ID',
                'name'    => 'Sprache',
                'state'   => 'Status',
                'actions' => 'Aktionen',
            ],

            'add'            => 'Sprache hinzufügen',
            'create_title'   => 'Sprache registrieren',
            'edit_title'     => 'Sprache bearbeiten',
            'save'           => 'Speichern',
            'update'         => 'Aktualisieren',
            'cancel'         => 'Abbrechen',
            'close'          => 'Schließen',
            'actions'        => 'Aktionen',
            'delete_forever' => 'Endgültig löschen',

            'processing'   => 'Wird verarbeitet...',
            'saving'       => 'Wird gespeichert...',
            'activating'   => 'Wird aktiviert...',
            'deactivating' => 'Wird deaktiviert...',
            'deleting'     => 'Wird gelöscht...',

            'toggle_on'  => 'Sprache aktivieren',
            'toggle_off' => 'Sprache deaktivieren',
            'toggle_confirm_on_title'  => 'Sprache aktivieren?',
            'toggle_confirm_off_title' => 'Sprache deaktivieren?',
            'toggle_confirm_on_html'   => 'Die Sprache <b>:label</b> wird <b>aktiv</b> sein.',
            'toggle_confirm_off_html'  => 'Die Sprache <b>:label</b> wird <b>inaktiv</b> sein.',
            'edit_confirm_title'       => 'Änderungen speichern?',
            'edit_confirm_button'      => 'Ja, speichern',

            'yes_continue' => 'Ja, fortfahren',
            'yes_delete'   => 'Ja, löschen',
            'item_this'    => 'diese Sprache',

            'flash' => [
                'activated_title'   => 'Sprache aktiviert',
                'deactivated_title' => 'Sprache deaktiviert',
                'updated_title'     => 'Sprache aktualisiert',
                'created_title'     => 'Sprache registriert',
                'deleted_title'     => 'Sprache gelöscht',
            ],
        ],

        'success' => [
            'created'     => 'Sprache erfolgreich erstellt.',
            'updated'     => 'Sprache erfolgreich aktualisiert.',
            'activated'   => 'Sprache erfolgreich aktiviert.',
            'deactivated' => 'Sprache erfolgreich deaktiviert.',
            'deleted'     => 'Sprache erfolgreich gelöscht.',
        ],

        'error' => [
            'create' => 'Die Sprache konnte nicht erstellt werden.',
            'update' => 'Die Sprache konnte nicht aktualisiert werden.',
            'toggle' => 'Der Status der Sprache konnte nicht geändert werden.',
            'delete' => 'Die Sprache konnte nicht gelöscht werden.',
            'save'   => 'Konnte nicht gespeichert werden.',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Ungültiger Name',
                'required' => 'Der Name der Sprache ist erforderlich.',
                'string'   => 'Das Feld :attribute muss eine Zeichenkette sein.',
                'max'      => 'Das Feld :attribute darf :max Zeichen nicht überschreiten.',
                'unique'   => 'Es existiert bereits eine Sprache mit diesem Namen.',
            ],
        ],
        'hints' => [
            'iso_639_1' => 'ISO-639-1-Code, z. B.: es, en, fr.',
        ],
    ],

    // =========================================================
    // [07] TOUR
    // =========================================================
    'tour' => [
        'title' => 'Touren',

        'fields' => [
            'id'            => 'ID',
            'name'          => 'Name',
            'details'       => 'Details',
            'price'         => 'Preise',
            'overview'      => 'Übersicht',
            'amenities'     => 'Ausstattungen',
            'exclusions'    => 'Ausschlüsse',
            'itinerary'     => 'Reiseplan',
            'languages'     => 'Sprachen',
            'schedules'     => 'Zeiten',
            'adult_price'   => 'Preis Erwachsener',
            'kid_price'     => 'Preis Kind',
            'length_hours'  => 'Dauer (Stunden)',
            'max_capacity'  => 'Max. Kapazität',
            'type'          => 'Tourtyp',
            'viator_code'   => 'Viator-Code',
            'status'        => 'Status',
            'actions'       => 'Aktionen',
            'group_size'    => 'Gruppengröße',
        ],

        'pricing' => [
            'configured_categories' => 'Konfigurierte Kategorien',
            'create_category'       => 'Kategorie erstellen',
            'note_title'            => 'Hinweis:',
            'note_text'             => 'Definiere hier die Basispreise für jede Kundenkategorie.',
            'manage_detailed_hint'  => 'Für eine detaillierte Verwaltung nutze oben die Schaltfläche "Detaillierte Preise verwalten".',
            'price_usd'             => 'Preis (USD)',
            'min_quantity'          => 'Mindestmenge',
            'max_quantity'          => 'Höchstmenge',
            'status'                => 'Status',
            'active'                => 'Aktiv',
            'no_categories'         => 'Es sind keine Kundenkategorien konfiguriert.',
            'create_categories_first' => 'Zuerst Kategorien erstellen',
            'page_title'            => 'Preise – :name',
            'header_title'          => 'Preise: :name',
            'back_to_tours'         => 'Zurück zu den Touren',

            'configured_title'      => 'Konfigurierte Kategorien und Preise',
            'empty_title'           => 'Es sind keine Kategorien für diese Tour konfiguriert.',
            'empty_hint'            => 'Nutze das Formular rechts, um Kategorien hinzuzufügen.',

            'save_changes'          => 'Änderungen speichern',
            'auto_disable_note'     => 'Preise von 0 $ werden automatisch deaktiviert',

            'add_category'          => 'Kategorie hinzufügen',

            'all_assigned_title'    => 'Alle Kategorien sind zugewiesen',
            'all_assigned_text'     => 'Es sind keine weiteren Kategorien für diese Tour verfügbar.',

            'info_title'            => 'Informationen',
            'tour_label'            => 'Tour',
            'configured_count'      => 'Konfigurierte Kategorien',
            'active_count'          => 'Aktive Kategorien',

            'fields_title'          => 'Felder',
            'rules_title'           => 'Regeln',

            'field_price'           => 'Preis',
            'field_min'             => 'Minimum',
            'field_max'             => 'Maximum',
            'field_status'          => 'Status',

            'rule_min_le_max'       => 'Das Minimum muss kleiner oder gleich dem Maximum sein.',
            'rule_zero_disable'     => 'Preise von 0 $ werden automatisch deaktiviert.',
            'rule_only_active'      => 'Nur aktive Kategorien erscheinen auf der öffentlichen Website.',

            'status_active'         => 'Aktiv',
            'add_existing_category'       => 'Bestehende Kategorie hinzufügen',
            'choose_category_placeholder' => 'Kategorie auswählen…',
            'add_button'                  => 'Hinzufügen',
            'add_existing_hint'           => 'Füge nur die benötigten Kundenkategorien für diese Tour hinzu.',
            'remove_category'             => 'Kategorie entfernen',
            'category_already_added'      => 'Diese Kategorie wurde der Tour bereits hinzugefügt.',
            'no_prices_preview'           => 'Es sind noch keine Preise konfiguriert.',
        ],

        'modal' => [
            'create_category' => 'Kategorie erstellen',

            'fields' => [
                'name'           => 'Name',
                'age_from'       => 'Alter von',
                'age_to'         => 'Alter bis',
                'age_range'      => 'Altersbereich',
                'min'            => 'Minimum',
                'max'            => 'Maximum',
                'order'          => 'Reihenfolge',
                'is_active'      => 'Aktiv',
                'auto_translate' => 'Automatisch übersetzen',
            ],

            'placeholders' => [
                'name'            => 'Z. B.: Erwachsener, Kind, Kleinkind',
                'age_to_optional' => 'Leer lassen für "+"',
            ],

            'hints' => [
                'age_to_empty_means_plus' => 'Wenn du das Höchstalter leer lässt, wird es als "+" interpretiert (z. B. 12+).',
                'min_le_max'              => 'Das Minimum muss kleiner oder gleich dem Maximum sein.',
            ],

            'errors' => [
                'min_le_max' => 'Das Minimum muss kleiner oder gleich dem Maximum sein.',
            ],
        ],

        'schedules_form' => [
            'available_title'        => 'Verfügbare Zeiten',
            'select_hint'            => 'Wähle die Zeiten für diese Tour',
            'no_schedules'           => 'Es sind keine Zeiten verfügbar.',
            'create_schedules_link'  => 'Zeiten erstellen',

            'create_new_title'       => 'Neue Zeit erstellen',
            'label_placeholder'      => 'Z. B.: Morgen, Nachmittag',
            'create_and_assign'      => 'Diese Zeit erstellen und der Tour zuweisen',

            'info_title'             => 'Informationen',
            'schedules_title'        => 'Zeiten',
            'schedules_text'         => 'Wähle eine oder mehrere Zeiten, in denen diese Tour verfügbar ist.',
            'create_block_title'     => 'Neu erstellen',
            'create_block_text'      => 'Wenn du eine Zeit benötigst, die es noch nicht gibt, kannst du sie hier erstellen, indem du das Kästchen „Diese Zeit erstellen und der Tour zuweisen“ aktivierst.',

            'current_title'          => 'Aktuelle Zeiten',
            'none_assigned'          => 'Keine Zeiten zugewiesen',
        ],

        'summary' => [
            'preview_title'        => 'Tour-Vorschau',
            'preview_text_create'  => 'Überprüfe alle Informationen, bevor du die Tour erstellst.',
            'preview_text_update'  => 'Überprüfe alle Informationen, bevor du die Tour aktualisierst.',

            'basic_details_title'  => 'Grundlegende Details',
            'description_title'    => 'Beschreibung',
            'prices_title'         => 'Preise nach Kategorie',
            'schedules_title'      => 'Zeiten',
            'languages_title'      => 'Sprachen',
            'itinerary_title'      => 'Reiseplan',

            'table' => [
                'category' => 'Kategorie',
                'price'    => 'Preis',
                'min_max'  => 'Min–Max',
            ],

            'not_specified'        => 'Ohne Angabe',
            'slug_autogenerated'   => 'Wird automatisch generiert',
            'no_description'       => 'Keine Beschreibung',
            'no_active_prices'     => 'Keine aktiven Preise konfiguriert',
            'no_languages'         => 'Keine Sprachen zugewiesen',
            'none_included'        => 'Nichts als enthalten angegeben',
            'none_excluded'        => 'Nichts als ausgeschlossen angegeben',

            'units' => [
                'hours'  => 'Stunden',
                'people' => 'Personen',
            ],

            'create_note' => 'Zeiten, Preise, Sprachen und Ausstattungen werden hier angezeigt, nachdem die Tour gespeichert wurde.',
        ],

        'alerts' => [
            'delete_title' => 'Tour löschen?',
            'delete_text'  => 'Die Tour wird in „Gelöscht“ verschoben. Du kannst sie später wiederherstellen.',
            'purge_title'  => 'Endgültig löschen?',
            'purge_text'   => 'Diese Aktion ist irreversibel.',
            'purge_text_with_bookings' => 'Diese Tour hat :count Buchung(en). Sie werden nicht gelöscht, sondern bleiben ohne zugeordnete Tour.',
            'toggle_question_active'   => 'Tour deaktivieren?',
            'toggle_question_inactive' => 'Tour aktivieren?',
        ],

        'flash' => [
            'created'       => 'Tour erfolgreich erstellt.',
            'updated'       => 'Tour erfolgreich aktualisiert.',
            'deleted_soft'  => 'Tour nach „Gelöscht“ verschoben.',
            'restored'      => 'Tour erfolgreich wiederhergestellt.',
            'purged'        => 'Tour endgültig gelöscht.',
            'toggled_on'    => 'Tour aktiviert.',
            'toggled_off'   => 'Tour deaktiviert.',
        ],

        'table' => [
            'id'            => 'ID',
            'name'          => 'Name',
            'overview'      => 'Übersicht',
            'amenities'     => 'Ausstattungen',
            'exclusions'    => 'Ausschlüsse',
            'itinerary'     => 'Reiseplan',
            'languages'     => 'Sprachen',
            'schedules'     => 'Zeiten',
            'adult_price'   => 'Preis Erwachsener',
            'kid_price'     => 'Preis Kind',
            'length_hours'  => 'Dauer (Std.)',
            'max_capacity'  => 'Max. Kapazität',
            'type'          => 'Typ',
            'viator_code'   => 'Viator-Code',
            'status'        => 'Status',
            'actions'       => 'Aktionen',
            'slug'          => 'URL',
            'prices'        => 'Preise',
            'capacity'      => 'Kapazität',
            'group_size'    => 'Max. Gruppe',
        ],

        'status' => [
            'active'   => 'Aktiv',
            'inactive' => 'Inaktiv',
            'archived' => 'Archiviert',
        ],

        'placeholders' => [
            'group_size' => 'Z. B.: 10',
        ],

        'hints' => [
            'group_size' => 'Empfohlene Kapazität/Größe pro Gruppe für diese Tour.',
        ],

        'success' => [
            'created'     => 'Die Tour wurde erfolgreich erstellt.',
            'updated'     => 'Die Tour wurde erfolgreich aktualisiert.',
            'deleted'     => 'Die Tour wurde gelöscht.',
            'toggled'     => 'Der Status der Tour wurde aktualisiert.',
            'activated'   => 'Tour erfolgreich aktiviert.',
            'deactivated' => 'Tour erfolgreich deaktiviert.',
            'archived'    => 'Tour erfolgreich archiviert.',
            'restored'    => 'Tour erfolgreich wiederhergestellt.',
            'purged'      => 'Tour dauerhaft gelöscht.',
        ],

        'error' => [
            'create'    => 'Die Tour konnte nicht erstellt werden.',
            'update'    => 'Die Tour konnte nicht aktualisiert werden.',
            'delete'    => 'Die Tour konnte nicht gelöscht werden.',
            'toggle'    => 'Der Status der Tour konnte nicht geändert werden.',
            'not_found' => 'Die Tour existiert nicht.',
            'restore'            => 'Die Tour konnte nicht wiederhergestellt werden.',
            'purge'              => 'Die Tour konnte nicht dauerhaft gelöscht werden.',
            'purge_has_bookings' => 'Dauerhaftes Löschen nicht möglich: Die Tour hat Buchungen.',
        ],

        'ui' => [
            'page_title'       => 'Verwaltung von Touren',
            'page_heading'     => 'Verwaltung von Touren',
            'create_title'     => 'Tour registrieren',
            'edit_title'       => 'Tour bearbeiten',
            'delete_title'     => 'Tour löschen',
            'cancel'           => 'Abbrechen',
            'save'             => 'Speichern',
            'save_changes'     => 'Änderungen speichern',
            'update'           => 'Aktualisieren',
            'delete_confirm'   => 'Diese Tour löschen?',
            'toggle_on'        => 'Aktivieren',
            'toggle_off'       => 'Deaktivieren',
            'toggle_on_title'  => 'Tour aktivieren?',
            'toggle_off_title' => 'Tour deaktivieren?',
            'toggle_on_button'  => 'Ja, aktivieren',
            'toggle_off_button' => 'Ja, deaktivieren',
            'see_more'         => 'Mehr anzeigen',
            'see_less'         => 'Weniger anzeigen',
            'load_more'        => 'Mehr laden',
            'loading'          => 'Wird geladen...',
            'load_more_error'  => 'Weitere Touren konnten nicht geladen werden.',
            'confirm_title'    => 'Bestätigung',
            'confirm_text'     => 'Möchtest du diese Aktion bestätigen?',
            'yes_confirm'      => 'Ja, bestätigen',
            'no_confirm'       => 'Nein, abbrechen',
            'add_tour'         => 'Tour hinzufügen',
            'edit_tour'        => 'Tour bearbeiten',
            'delete_tour'      => 'Tour löschen',
            'toggle_tour'      => 'Tour aktivieren/deaktivieren',
            'view_cart'        => 'Warenkorb ansehen',
            'add_to_cart'      => 'Zum Warenkorb hinzufügen',
            'slug_help'        => 'Bezeichner der Tour in der URL (ohne Leerzeichen und Akzente)',
            'generate_auto'       => 'Automatisch generieren',
            'slug_preview_label'  => 'Vorschau',
            'saved'               => 'Gespeichert',

            'available_languages'    => 'Verfügbare Sprachen',
            'default_capacity'       => 'Standardkapazität',
            'create_new_schedules'   => 'Neue Zeiten erstellen',
            'multiple_hint_ctrl_cmd' => 'Halte STRG/CMD gedrückt, um mehrere auszuwählen',
            'use_existing_schedules' => 'Bestehende Zeiten verwenden',
            'add_schedule'           => 'Zeit hinzufügen',
            'schedules_title'        => 'Tourzeiten',
            'amenities_included'     => 'Inklusive Ausstattungen',
            'amenities_excluded'     => 'Nicht enthaltene Ausstattungen',
            'color'                  => 'Tourfarbe',
            'remove'                 => 'Entfernen',
            'choose_itinerary'       => 'Reiseplan wählen',
            'select_type'            => 'Typ auswählen',
            'empty_means_default'    => 'Standard',
            'actives'                => 'Aktive',
            'inactives'              => 'Inaktive',
            'archived'               => 'Archivierte',
            'all'                    => 'Alle',
            'help_title'             => 'Hilfe',
            'amenities_included_hint' => 'Wähle aus, was in der Tour enthalten ist.',
            'amenities_excluded_hint' => 'Wähle aus, was in der Tour NICHT enthalten ist.',
            'help_included_title'     => 'Enthalten',
            'help_included_text'      => 'Markiere alles, was im Tourpreis enthalten ist (Transport, Mahlzeiten, Eintrittsgelder, Ausrüstung, Guide usw.).',
            'help_excluded_title'     => 'Nicht enthalten',
            'help_excluded_text'      => 'Markiere alles, was der Kunde separat bezahlen oder mitbringen muss (Trinkgelder, alkoholische Getränke, Souvenirs usw.).',
            'select_or_create_title' => 'Reiseplan wählen oder erstellen',
            'select_existing_items'  => 'Bestehende Elemente auswählen',
            'name_hint'              => 'Identifizierender Name für diesen Reiseplan',
            'click_add_item_hint'    => 'Klicke auf „Element hinzufügen“, um neue Elemente zu erstellen',
            'scroll_hint'            => 'Horizontal scrollen, um mehr Spalten zu sehen',
            'no_schedules'           => 'Keine Zeiten',
            'no_prices'              => 'Keine Preise konfiguriert',
            'edit'                   => 'Bearbeiten',
            'slug_auto'              => 'Wird automatisch generiert',
            'added_to_cart'          => 'Zum Warenkorb hinzugefügt',
            'added_to_cart_text'     => 'Die Tour wurde erfolgreich zum Warenkorb hinzugefügt.',

            'none' => [
                'amenities'       => 'Keine Ausstattungen',
                'exclusions'      => 'Keine Ausschlüsse',
                'itinerary'       => 'Kein Reiseplan',
                'itinerary_items' => 'Keine Elemente',
                'languages'       => 'Keine Sprachen',
                'schedules'       => 'Keine Zeiten',
            ],

            'archive' => 'Archivieren',
            'restore' => 'Wiederherstellen',
            'purge'   => 'Endgültig löschen',

            'confirm_archive_title' => 'Tour archivieren?',
            'confirm_archive_text'  => 'Die Tour wird für neue Buchungen deaktiviert, aber bestehende Buchungen bleiben erhalten.',
            'confirm_purge_title'   => 'Endgültig löschen',
            'confirm_purge_text'    => 'Diese Aktion ist irreversibel und nur erlaubt, wenn die Tour noch nie Buchungen hatte.',

            'filters' => [
                'active'   => 'Aktive',
                'inactive' => 'Inaktive',
                'archived' => 'Archivierte',
                'all'      => 'Alle',
            ],

            'font_decrease_title' => 'Schriftgröße verkleinern',
            'font_increase_title' => 'Schriftgröße vergrößern',
        ],

    ],

    // =========================================================
    // [08] IMAGES
    // =========================================================
    'image' => [

        'limit_reached_title' => 'Limit erreicht',
        'limit_reached_text'  => 'Das Bildlimit für diese Tour wurde erreicht.',
        'upload_success'      => 'Bilder erfolgreich hochgeladen.',
        'upload_none'         => 'Es wurden keine Bilder hochgeladen.',
        'upload_truncated'    => 'Einige Dateien wurden aufgrund des Limits pro Tour ausgelassen.',
        'done'                => 'Fertig',
        'notice'              => 'Hinweis',
        'saved'               => 'Speichern',
        'caption_updated'     => 'Bildunterschrift erfolgreich aktualisiert.',
        'deleted'             => 'Gelöscht',
        'image_removed'       => 'Bild erfolgreich gelöscht.',
        'invalid_order'       => 'Ungültige Reihenfolge.',
        'nothing_to_reorder'  => 'Nichts zum Neuordnen.',
        'order_saved'         => 'Reihenfolge gespeichert.',
        'cover_updated_title' => 'Titelfoto aktualisieren',
        'cover_updated_text'  => 'Dieses Bild ist jetzt das Titelfoto.',
        'deleting'            => 'Wird gelöscht...',

        'ui' => [
            'page_title_pick'     => 'Tour-Bilder',
            'page_heading'        => 'Tour-Bilder',
            'choose_tour'         => 'Tour auswählen',
            'search_placeholder'  => 'Nach ID oder Name suchen…',
            'search_button'       => 'Suchen',
            'no_results'          => 'Es wurden keine Touren gefunden.',
            'manage_images'       => 'Bilder verwalten',
            'cover_alt'           => 'Titelfoto',
            'images_label'        => 'Bilder',

            'upload_btn'          => 'Hochladen',
            'delete_btn'          => 'Löschen',
            'show_btn'            => 'Anzeigen',
            'close_btn'           => 'Schließen',
            'preview_title'       => 'Bildvorschau',

            'error_title'         => 'Fehler',
            'warning_title'       => 'Achtung',
            'success_title'       => 'Erfolg',
            'cancel_btn'          => 'Abbrechen',

            'confirm_delete_title' => 'Dieses Bild löschen?',
            'confirm_delete_text'  => 'Diese Aktion kann nicht rückgängig gemacht werden.',

            'cover_current_title'    => 'Aktuelles Titelfoto',
            'upload_new_cover_title' => 'Neues Titelfoto hochladen',
            'cover_file_label'       => 'Titelfoto-Datei',
            'file_help_cover'        => 'JPEG/PNG/WebP, max. 30 MB.',
            'id_label'               => 'ID',

            'back_btn'          => 'Zurück zur Liste',

            'stats_images'      => 'Hochgeladene Bilder',
            'stats_cover'       => 'Definierte Titelfotos',
            'stats_selected'    => 'Ausgewählt',

            'drag_or_click'     => 'Ziehe deine Bilder hierher oder klicke, um sie auszuwählen.',
            'upload_help'       => 'Erlaubte Formate: JPG, PNG, WebP. Maximale Gesamtgröße 100 MB.',
            'select_btn'        => 'Dateien auswählen',
            'limit_badge'       => 'Limit von :max Bildern erreicht',
            'files_word'        => 'Dateien',

            'select_all'        => 'Alle auswählen',
            'delete_selected'   => 'Ausgewählte löschen',
            'delete_all'        => 'Alle löschen',

            'select_image_title' => 'Dieses Bild auswählen',
            'select_image_aria'  => 'Bild :id auswählen',

            'cover_label'       => 'Titelfoto',
            'cover_btn'         => 'Als Titelfoto festlegen',

            'caption_placeholder' => 'Bildunterschrift (optional)',
            'saving_label'        => 'Wird gespeichert…',
            'saving_fallback'     => 'Wird gespeichert…',
            'none_label'          => 'Keine Bildunterschrift',
            'limit_word'          => 'Limit',

            'confirm_set_cover_title' => 'Als Titelfoto festlegen?',
            'confirm_set_cover_text'  => 'Dieses Bild wird das Haupttitelfoto der Tour.',
            'confirm_btn'             => 'Ja, fortfahren',

            'confirm_bulk_delete_title' => 'Ausgewählte Bilder löschen?',
            'confirm_bulk_delete_text'  => 'Die ausgewählten Bilder werden endgültig gelöscht.',

            'confirm_delete_all_title'  => 'Alle Bilder löschen?',
            'confirm_delete_all_text'   => 'Alle Bilder dieser Tour werden gelöscht.',

            'no_images'           => 'Es gibt noch keine Bilder für diese Tour.',
        ],

        'errors' => [
            'validation'     => 'Die gesendeten Daten sind ungültig.',
            'upload_generic' => 'Einige Bilder konnten nicht hochgeladen werden.',
            'update_caption' => 'Die Bildunterschrift konnte nicht aktualisiert werden.',
            'delete'         => 'Das Bild konnte nicht gelöscht werden.',
            'reorder'        => 'Die Reihenfolge konnte nicht gespeichert werden.',
            'set_cover'      => 'Das Titelfoto konnte nicht festgelegt werden.',
            'load_list'      => 'Die Liste konnte nicht geladen werden.',
            'too_large'      => 'Die Datei überschreitet die maximal zulässige Größe. Bitte verwende ein kleineres Bild.',
        ],
    ],

    'prices' => [
        'ui' => [
            'page_title'         => 'Preise – :name',
            'header_title'       => 'Preise: :name',
            'back_to_tours'      => 'Zurück zu den Touren',

            'configured_title'   => 'Konfigurierte Kategorien und Preise',
            'empty_title'        => 'Es sind keine Kategorien für diese Tour konfiguriert.',
            'empty_hint'         => 'Nutze das Formular rechts, um Kategorien hinzuzufügen.',

            'save_changes'       => 'Änderungen speichern',
            'auto_disable_note'  => 'Preise von 0 $ werden automatisch deaktiviert',

            'add_category'       => 'Kategorie hinzufügen',

            'all_assigned_title' => 'Alle Kategorien sind zugewiesen',
            'all_assigned_text'  => 'Es sind keine weiteren Kategorien für diese Tour verfügbar.',

            'info_title'         => 'Informationen',
            'tour_label'         => 'Tour',
            'configured_count'   => 'Konfigurierte Kategorien',
            'active_count'       => 'Aktive Kategorien',

            'fields_title'       => 'Felder',
            'rules_title'        => 'Regeln',

            'field_price'        => 'Preis',
            'field_min'          => 'Minimum',
            'field_max'          => 'Maximum',
            'field_status'       => 'Status',

            'rule_min_le_max'    => 'Das Minimum muss kleiner oder gleich dem Maximum sein.',
            'rule_zero_disable'  => 'Preise von 0 $ werden automatisch deaktiviert.',
            'rule_only_active'   => 'Nur aktive Kategorien erscheinen auf der öffentlichen Website.',
        ],

        'table' => [
            'category'   => 'Kategorie',
            'age_range'  => 'Altersbereich',
            'price_usd'  => 'Preis (USD)',
            'min'        => 'Min',
            'max'        => 'Max',
            'status'     => 'Status',
            'action'     => 'Aktion',
            'active'     => 'Aktiv',
            'inactive'   => 'Inaktiv',
        ],

        'forms' => [
            'select_placeholder'   => '-- Auswählen --',
            'category'             => 'Kategorie',
            'price_usd'            => 'Preis (USD)',
            'min'                  => 'Minimum',
            'max'                  => 'Maximum',
            'create_disabled_hint' => 'Wenn der Preis 0 $ beträgt, wird die Kategorie deaktiviert erstellt',
            'add'                  => 'Hinzufügen',
        ],

        'modal' => [
            'delete_title'   => 'Kategorie löschen',
            'delete_text'    => 'Diese Kategorie für diese Tour löschen?',
            'cancel'         => 'Abbrechen',
            'delete'         => 'Löschen',
            'delete_tooltip' => 'Kategorie löschen',
        ],

        'flash' => [
            'success' => 'Operation erfolgreich durchgeführt.',
            'error'   => 'Es ist ein Fehler aufgetreten.',
        ],

        'js' => [
            'max_ge_min'            => 'Das Maximum muss größer oder gleich dem Minimum sein.',
            'auto_disabled_tooltip' => 'Preis 0 $ – automatisch deaktiviert',
            'fix_errors'            => 'Bitte korrigiere die Minimal- und Maximalmengen.',
        ],
    ],

    'ajax' => [
        'category_created' => 'Kategorie erfolgreich erstellt',
        'category_error'   => 'Fehler beim Erstellen der Kategorie',
        'language_created' => 'Sprache erfolgreich erstellt',
        'language_error'   => 'Fehler beim Erstellen der Sprache',
        'amenity_created'  => 'Ausstattung erfolgreich erstellt',
        'amenity_error'    => 'Fehler beim Erstellen der Ausstattung',
        'schedule_created' => 'Zeit erfolgreich erstellt',
        'schedule_error'   => 'Fehler beim Erstellen der Zeit',
        'itinerary_created' => 'Reiseplan erfolgreich erstellt',
        'itinerary_error'   => 'Fehler beim Erstellen des Reiseplans',
        'translation_error' => 'Fehler beim Übersetzen',
    ],

    'modal' => [
        'create_category'  => 'Neue Kategorie erstellen',
        'create_language'  => 'Neue Sprache erstellen',
        'create_amenity'   => 'Neue Ausstattung erstellen',
        'create_schedule'  => 'Neue Zeit erstellen',
        'create_itinerary' => 'Neuen Reiseplan erstellen',
    ],

    'validation' => [
        'slug_taken'     => 'Dieser Slug wird bereits verwendet',
        'slug_available' => 'Slug verfügbar',
    ],

];
