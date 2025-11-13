<?php

/*************************************************************
 *  ÜBERSETZUNGSMODUL: TOURS
 *  Datei: resources/lang/de/m_tours.php
 *************************************************************/

return [

    // =========================================================
    // [01] COMMON
    // =========================================================
    'common' => [
        'success_title'         => 'Erfolg',
        'error_title'           => 'Fehler',
        'people'                => 'Personen',
        'hours'                 => 'Stunden',
        'success'               => 'Erfolg',
        'error'                 => 'Fehler',
        'cancel'                => 'Abbrechen',
        'confirm_delete'        => 'Ja, löschen',
        'unspecified'           => 'Nicht angegeben',
        'no_description'        => 'Keine Beschreibung',
        'required_fields_title' => 'Pflichtfelder',
        'required_fields_text'  => 'Bitte fülle die Pflichtfelder aus: Name und maximale Kapazität.',
        'active'                => 'Aktiv',
        'inactive'              => 'Inaktiv',
        'notice'                => 'Hinweis',
        'na'                    => 'Nicht konfiguriert',
        'create'                => 'Erstellen',
    ],

    // =========================================================
    // [02] AMENITY
    // =========================================================
    'amenity' => [
        'fields' => [
            'name' => 'Name',
        ],

        'status' => [
            'active'   => 'Aktiv',
            'inactive' => 'Inaktiv',
        ],

        'ui' => [
            'page_title'    => 'Leistungen',
            'page_heading'  => 'Verwaltung von Leistungen',
            'list_title'    => 'Liste der Leistungen',

            'add'            => 'Leistung hinzufügen',
            'create_title'   => 'Leistung registrieren',
            'edit_title'     => 'Leistung bearbeiten',
            'save'           => 'Speichern',
            'update'         => 'Aktualisieren',
            'cancel'         => 'Abbrechen',
            'close'          => 'Schließen',
            'state'          => 'Status',
            'actions'        => 'Aktionen',
            'delete_forever' => 'Dauerhaft löschen',

            'processing' => 'Wird verarbeitet...',
            'applying'   => 'Wird übernommen...',
            'deleting'   => 'Wird gelöscht...',

            'toggle_on'  => 'Leistung aktivieren',
            'toggle_off' => 'Leistung deaktivieren',

            'toggle_confirm_on_title'  => 'Leistung aktivieren?',
            'toggle_confirm_off_title' => 'Leistung deaktivieren?',
            'toggle_confirm_on_html'   => 'Die Leistung <b>:label</b> wird aktiv.',
            'toggle_confirm_off_html'  => 'Die Leistung <b>:label</b> wird inaktiv.',

            'delete_confirm_title' => 'Dauerhaft löschen?',
            'delete_confirm_html'  => '<b>:label</b> wird gelöscht und kann nicht wiederhergestellt werden.',

            'yes_continue' => 'Ja, fortfahren',
            'yes_delete'   => 'Ja, löschen',

            'item_this' => 'diese Leistung',
        ],

        'success' => [
            'created'     => 'Leistung erfolgreich erstellt.',
            'updated'     => 'Leistung erfolgreich aktualisiert.',
            'activated'   => 'Leistung erfolgreich aktiviert.',
            'deactivated' => 'Leistung erfolgreich deaktiviert.',
            'deleted'     => 'Leistung dauerhaft gelöscht.',
        ],

        'error' => [
            'create' => 'Die Leistung konnte nicht erstellt werden.',
            'update' => 'Die Leistung konnte nicht aktualisiert werden.',
            'toggle' => 'Der Status der Leistung konnte nicht geändert werden.',
            'delete' => 'Die Leistung konnte nicht gelöscht werden.',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Ungültiger Name',
                'required' => 'Das Feld :attribute ist erforderlich.',
                'string'   => 'Das Feld :attribute muss eine Textzeichenkette sein.',
                'max'      => 'Das Feld :attribute darf :max Zeichen nicht überschreiten.',
            ],
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
            'page_title'   => 'Tour-Zeiten',
            'page_heading' => 'Verwaltung der Zeiten',

            'general_title'     => 'Allgemeine Zeiten',
            'new_schedule'      => 'Neue Zeit',
            'new_general_title' => 'Neue allgemeine Zeit',
            'new'               => 'Neu',
            'edit_schedule'     => 'Zeit bearbeiten',
            'edit_global'       => 'Bearbeiten (global)',

            'assign_existing'    => 'Bestehende zuweisen',
            'assign_to_tour'     => 'Zeitplan zu ":tour" zuweisen',
            'select_schedule'    => 'Zeitplan auswählen',
            'choose'             => 'Auswählen',
            'assign'             => 'Zuweisen',
            'new_for_tour_title' => 'Neue Zeit für ":tour"',

            'time_range'        => 'Zeitspanne',
            'state'             => 'Status',
            'actions'           => 'Aktionen',
            'schedule_state'    => 'Zeitplan',
            'assignment_state'  => 'Zuweisung',
            'no_general'        => 'Es gibt keine allgemeinen Zeiten.',
            'no_tour_schedules' => 'Diese Tour hat noch keine Zeiten.',
            'no_label'          => 'Keine Bezeichnung',
            'assigned_count'    => 'zugewiesene(r) Zeitplan/Zeiten',

            'toggle_global_title'     => 'Aktivieren/Deaktivieren (global)',
            'toggle_global_on_title'  => 'Zeit (global) aktivieren?',
            'toggle_global_off_title' => 'Zeit (global) deaktivieren?',
            'toggle_global_on_html'   => '<b>:label</b> wird für alle Touren aktiviert.',
            'toggle_global_off_html'  => '<b>:label</b> wird für alle Touren deaktiviert.',

            'toggle_on_tour'          => 'Für diese Tour aktivieren',
            'toggle_off_tour'         => 'Für diese Tour deaktivieren',
            'toggle_assign_on_title'  => 'Für diese Tour aktivieren?',
            'toggle_assign_off_title' => 'Für diese Tour deaktivieren?',
            'toggle_assign_on_html'   => 'Die Zuweisung wird für <b>:tour</b> <b>aktiv</b>.',
            'toggle_assign_off_html'  => 'Die Zuweisung wird für <b>:tour</b> <b>inaktiv</b>.',

            'detach_from_tour'     => 'Von Tour entfernen',
            'detach_confirm_title' => 'Von Tour entfernen?',
            'detach_confirm_html'  => 'Der Zeitplan wird von <b>:tour</b> <b>gelöst</b>.',

            'delete_forever'       => 'Löschen (global)',
            'delete_confirm_title' => 'Dauerhaft löschen?',
            'delete_confirm_html'  => '<b>:label</b> (global) wird gelöscht und kann nicht wiederhergestellt werden.',

            'yes_continue' => 'Ja, fortfahren',
            'yes_delete'   => 'Ja, löschen',
            'yes_detach'   => 'Ja, entfernen',

            'this_schedule' => 'diesen Zeitplan',
            'this_tour'     => 'diese Tour',

            'processing'     => 'Wird verarbeitet...',
            'applying'       => 'Wird übernommen...',
            'deleting'       => 'Wird gelöscht...',
            'removing'       => 'Wird entfernt...',
            'saving_changes' => 'Änderungen werden gespeichert...',
            'save'           => 'Speichern',
            'save_changes'   => 'Änderungen speichern',
            'cancel'         => 'Abbrechen',

            'missing_fields_title' => 'Fehlende Daten',
            'missing_fields_text'  => 'Überprüfe die Pflichtfelder (Beginn, Ende und Kapazität).',
            'could_not_save'       => 'Konnte nicht gespeichert werden.',
        ],

        'success' => [
            'created'                => 'Zeitplan erfolgreich erstellt.',
            'updated'                => 'Zeitplan erfolgreich aktualisiert.',
            'activated_global'       => 'Zeitplan erfolgreich aktiviert (global).',
            'deactivated_global'     => 'Zeitplan erfolgreich deaktiviert (global).',
            'attached'               => 'Zeitplan der Tour zugewiesen.',
            'detached'               => 'Zeitplan erfolgreich von der Tour entfernt.',
            'assignment_activated'   => 'Zuweisung für diese Tour aktiviert.',
            'assignment_deactivated' => 'Zuweisung für diese Tour deaktiviert.',
            'deleted'                => 'Zeitplan erfolgreich gelöscht.',
        ],

        'error' => [
            'create'               => 'Beim Erstellen des Zeitplans ist ein Problem aufgetreten.',
            'update'               => 'Beim Aktualisieren des Zeitplans ist ein Problem aufgetreten.',
            'toggle'               => 'Der globale Status des Zeitplans konnte nicht geändert werden.',
            'attach'               => 'Der Zeitplan konnte der Tour nicht zugewiesen werden.',
            'detach'               => 'Der Zeitplan konnte nicht von der Tour gelöst werden.',
            'assignment_toggle'    => 'Der Zuweisungsstatus konnte nicht geändert werden.',
            'not_assigned_to_tour' => 'Der Zeitplan ist dieser Tour nicht zugewiesen.',
            'delete'               => 'Beim Löschen des Zeitplans ist ein Problem aufgetreten.',
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

            'delete_forever'       => 'Dauerhaft löschen',
            'delete_confirm_title' => 'Dauerhaft löschen?',
            'delete_confirm_html'  => '<b>:label</b> wird gelöscht und kann nicht wiederhergestellt werden.',
            'yes_delete'           => 'Ja, löschen',
            'item_this'            => 'dieses Element',

            'processing' => 'Wird verarbeitet...',
            'applying'   => 'Wird übernommen...',
            'deleting'   => 'Wird gelöscht...',
        ],

        'success' => [
            'created'     => 'Reiseplan-Element erfolgreich erstellt.',
            'updated'     => 'Element erfolgreich aktualisiert.',
            'activated'   => 'Element erfolgreich aktiviert.',
            'deactivated' => 'Element erfolgreich deaktiviert.',
            'deleted'     => 'Element dauerhaft gelöscht.',
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
                'string'   => 'Das Feld :attribute muss eine Textzeichenkette sein.',
                'max'      => 'Das Feld :attribute darf :max Zeichen nicht überschreiten.',
            ],
            'description' => [
                'required' => 'Das Feld :attribute ist erforderlich.',
                'string'   => 'Das Feld :attribute muss eine Textzeichenkette sein.',
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
        ],

        'status' => [
            'active'   => 'Aktiv',
            'inactive' => 'Inaktiv',
        ],

        'ui' => [
            'page_title'    => 'Reisepläne und Elemente',
            'page_heading'  => 'Reisepläne und Element-Verwaltung',
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
            'toggle_confirm_on_html'   => 'Der Reiseplan <b>:label</b> wird <b>aktiv</b>.',
            'toggle_confirm_off_html'  => 'Der Reiseplan <b>:label</b> wird <b>inaktiv</b>.',
            'yes_continue'             => 'Ja, fortfahren',

            'assign_title'          => 'Elemente zu :name zuweisen',
            'drag_hint'             => 'Ziehe und lasse Elemente los, um die Reihenfolge festzulegen.',
            'drag_handle'           => 'Ziehen, um neu anzuordnen',
            'select_one_title'      => 'Mindestens ein Element auswählen',
            'select_one_text'       => 'Bitte wähle mindestens ein Element aus, um fortzufahren.',
            'assign_confirm_title'  => 'Ausgewählte Elemente zuweisen?',
            'assign_confirm_button' => 'Ja, zuweisen',
            'assigning'             => 'Wird zugewiesen...',

            'no_items_assigned'       => 'Diesem Reiseplan sind keine Elemente zugewiesen.',
            'itinerary_this'          => 'dieser Reiseplan',
            'processing'              => 'Wird verarbeitet...',
            'saving'                  => 'Wird gespeichert...',
            'activating'              => 'Wird aktiviert...',
            'deactivating'            => 'Wird deaktiviert...',
            'applying'                => 'Wird übernommen...',
            'deleting'                => 'Wird gelöscht...',
            'flash_success_title'     => 'Erfolg',
            'flash_error_title'       => 'Fehler',
            'validation_failed_title' => 'Konnte nicht verarbeitet werden',
        ],

        'success' => [
            'created'        => 'Reiseplan erfolgreich erstellt.',
            'updated'        => 'Reiseplan erfolgreich aktualisiert.',
            'activated'      => 'Reiseplan erfolgreich aktiviert.',
            'deactivated'    => 'Reiseplan erfolgreich deaktiviert.',
            'deleted'        => 'Reiseplan dauerhaft gelöscht.',
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
                'order_integer' => 'Die Reihenfolge muss eine Ganzzahl sein.',
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
        ],

        'status' => [
            'active'   => 'Aktiv',
            'inactive' => 'Inaktiv',
        ],

        'ui' => [
            'page_title'   => 'Tour-Sprachen',
            'page_heading' => 'Sprachverwaltung',
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
            'delete_forever' => 'Dauerhaft löschen',

            'processing'   => 'Wird verarbeitet...',
            'saving'       => 'Wird gespeichert...',
            'activating'   => 'Wird aktiviert...',
            'deactivating' => 'Wird deaktiviert...',
            'deleting'     => 'Wird gelöscht...',

            'toggle_on'  => 'Sprache aktivieren',
            'toggle_off' => 'Sprache deaktivieren',
            'toggle_confirm_on_title'  => 'Sprache aktivieren?',
            'toggle_confirm_off_title' => 'Sprache deaktivieren?',
            'toggle_confirm_on_html'   => 'Die Sprache <b>:label</b> wird <b>aktiv</b>.',
            'toggle_confirm_off_html'  => 'Die Sprache <b>:label</b> wird <b>inaktiv</b>.',
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
                'string'   => 'Das Feld :attribute muss eine Textzeichenkette sein.',
                'max'      => 'Das Feld :attribute darf :max Zeichen nicht überschreiten.',
                'unique'   => 'Es existiert bereits eine Sprache mit diesem Namen.',
            ],
        ],
    ],

    // =========================================================
    // [07] TOUR
    // =========================================================
    'tour' => [
        'title' => 'Touren',

        'fields' => [
            'id'           => 'ID',
            'name'         => 'Name',
            'details'      => 'Details',
            'price'        => 'Preise',
            'overview'     => 'Überblick',
            'amenities'    => 'Leistungen',
            'exclusions'   => 'Nicht enthalten',
            'itinerary'    => 'Reiseplan',
            'languages'    => 'Sprachen',
            'schedules'    => 'Zeiten',
            'adult_price'  => 'Preis Erwachsener',
            'kid_price'    => 'Preis Kind',
            'length_hours' => 'Dauer (Stunden)',
            'max_capacity' => 'Max. Kapazität',
            'type'         => 'Tour-Typ',
            'viator_code'  => 'Viator-Code',
            'status'       => 'Status',
            'actions'      => 'Aktionen',
            'group_size'   => 'Gruppengröße',
        ],

        'pricing' => [
            'configured_categories'   => 'Konfigurierte Kategorien',
            'create_category'         => 'Kategorie erstellen',
            'note_title'              => 'Hinweis:',
            'note_text'               => 'Definiere hier die Basispreise für jede Kundenkategorie.',
            'manage_detailed_hint'    => ' Für eine detaillierte Verwaltung nutze oben die Schaltfläche „Detaillierte Preise verwalten“.',
            'price_usd'               => 'Preis (USD)',
            'min_quantity'            => 'Mindestmenge',
            'max_quantity'            => 'Höchstmenge',
            'status'                  => 'Status',
            'active'                  => 'Aktiv',
            'no_categories'           => 'Es sind keine Kundenkategorien konfiguriert.',
            'create_categories_first' => 'Zuerst Kategorien erstellen',
            'page_title'              => 'Preise - :name',
            'header_title'            => 'Preise: :name',
            'back_to_tours'           => 'Zurück zu den Touren',

            'configured_title'   => 'Konfigurierte Kategorien und Preise',
            'empty_title'        => 'Für diese Tour sind keine Kategorien konfiguriert.',
            'empty_hint'         => 'Verwende das Formular rechts, um Kategorien hinzuzufügen.',

            'save_changes'       => 'Änderungen speichern',
            'auto_disable_note'  => 'Preise von 0 $ werden automatisch deaktiviert.',

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

            'status_active'      => 'Aktiv',
        ],

        'modal' => [
            'create_category' => 'Kategorie erstellen',
            'fields' => [
                'name'      => 'Name',
                'age_range' => 'Altersbereich',
                'min'       => 'Minimum',
                'max'       => 'Maximum',
            ],
        ],

        'schedules_form' => [
            'available_title'        => 'Verfügbare Zeiten',
            'select_hint'            => 'Wähle die Zeiten für diese Tour aus',
            'no_schedules'           => 'Es sind keine Zeiten verfügbar.',
            'create_schedules_link'  => 'Zeiten erstellen',

            'create_new_title'       => 'Neue Zeit erstellen',
            'label_placeholder'      => 'Z. B.: Vormittag, Nachmittag',
            'create_and_assign'      => 'Diese Zeit erstellen und der Tour zuweisen',

            'info_title'             => 'Informationen',
            'schedules_title'        => 'Zeiten',
            'schedules_text'         => 'Wähle eine oder mehrere Zeiten, in denen diese Tour verfügbar sein wird.',
            'create_block_title'     => 'Neu erstellen',
            'create_block_text'      => 'Wenn du eine Zeit benötigst, die noch nicht existiert, kannst du sie hier erstellen, indem du das Kästchen „Diese Zeit erstellen und der Tour zuweisen“ markierst.',

            'current_title'          => 'Aktuelle Zeiten',
            'none_assigned'          => 'Keine Zeiten zugewiesen',
        ],

        'summary' => [
            'preview_title'        => 'Tour-Vorschau',
            'preview_text_create'  => 'Überprüfe alle Informationen, bevor du die Tour erstellst.',
            'preview_text_update'  => 'Überprüfe alle Informationen, bevor du die Tour aktualisierst.',

            'basic_details_title'  => 'Grundlegende Details',
            'description_title'    => 'Beschreibung',
            'prices_title'         => 'Preise pro Kategorie',
            'schedules_title'      => 'Zeiten',
            'languages_title'      => 'Sprachen',
            'itinerary_title'      => 'Reiseplan',

            'table' => [
                'category' => 'Kategorie',
                'price'    => 'Preis',
                'min_max'  => 'Min–Max',
            ],

            'not_specified'        => 'Nicht angegeben',
            'slug_autogenerated'   => 'Wird automatisch generiert',
            'no_description'       => 'Keine Beschreibung',
            'no_active_prices'     => 'Keine aktiven Preise konfiguriert',
            'no_languages'         => 'Keine Sprachen zugewiesen',
            'none_included'        => 'Kein enthaltener Punkt angegeben',
            'none_excluded'        => 'Kein ausgeschlossener Punkt angegeben',

            'units' => [
                'hours'  => 'Stunden',
                'people' => 'Personen',
            ],

            'create_note' => 'Zeiten, Preise, Sprachen und Leistungen werden hier angezeigt, nachdem die Tour gespeichert wurde.',
        ],

        'alerts' => [
            'delete_title' => 'Tour löschen?',
            'delete_text'  => 'Die Tour wird in „Gelöscht“ verschoben. Du kannst sie später wiederherstellen.',
            'purge_title'  => 'Dauerhaft löschen?',
            'purge_text'   => 'Diese Aktion ist nicht umkehrbar.',
            'purge_text_with_bookings' => 'Diese Tour hat :count Buchung(en). Sie werden nicht gelöscht, sondern bleiben ohne zugeordnete Tour.',
            'toggle_question_active'   => 'Tour deaktivieren?',
            'toggle_question_inactive' => 'Tour aktivieren?',
        ],

        'flash' => [
            'created'       => 'Tour erfolgreich erstellt.',
            'updated'       => 'Tour erfolgreich aktualisiert.',
            'deleted_soft'  => 'Tour in „Gelöscht“ verschoben.',
            'restored'      => 'Tour erfolgreich wiederhergestellt.',
            'purged'        => 'Tour dauerhaft gelöscht.',
            'toggled_on'    => 'Tour aktiviert.',
            'toggled_off'   => 'Tour deaktiviert.',
        ],

        'table' => [
            'id'           => 'ID',
            'name'         => 'Name',
            'overview'     => 'Überblick',
            'amenities'    => 'Leistungen',
            'exclusions'   => 'Nicht enthalten',
            'itinerary'    => 'Reiseplan',
            'languages'    => 'Sprachen',
            'schedules'    => 'Zeiten',
            'adult_price'  => 'Preis Erwachsener',
            'kid_price'    => 'Preis Kind',
            'length_hours' => 'Dauer (Std.)',
            'max_capacity' => 'Max. Kapazität',
            'type'         => 'Typ',
            'viator_code'  => 'Viator-Code',
            'status'       => 'Status',
            'actions'      => 'Aktionen',
            'slug'         => 'URL',
            'prices'       => 'Preise',
            'capacity'     => 'Kapazität',
            'group_size'   => 'Max. Gruppe',
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
            'group_size' => 'Empfohlene Kapazität/Gruppe für diese Tour.',
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
            'page_title'       => 'Tour-Verwaltung',
            'page_heading'     => 'Tour-Verwaltung',
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
            'see_less'         => 'Ausblenden',
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
            'view_cart'        => 'Warenkorb anzeigen',
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
            'schedules_title'        => 'Zeiten der Tour',
            'amenities_included'     => 'Enthaltene Leistungen',
            'amenities_excluded'     => 'Nicht enthaltene Leistungen',
            'color'                  => 'Farbe der Tour',
            'remove'                 => 'Entfernen',
            'choose_itinerary'       => 'Reiseplan auswählen',
            'select_type'            => 'Typ auswählen',
            'empty_means_default'    => 'Standard',
            'actives'                => 'Aktive',
            'inactives'              => 'Inaktive',
            'archived'               => 'Archivierte',
            'all'                    => 'Alle',
            'help_title'             => 'Hilfe',
            'amenities_included_hint'=> 'Wähle, was in der Tour enthalten ist.',
            'amenities_excluded_hint'=> 'Wähle, was in der Tour NICHT enthalten ist.',
            'help_included_title'    => 'Enthalten',
            'help_included_text'     => 'Markiere alles, was im Tourpreis enthalten ist (Transport, Mahlzeiten, Eintritt, Ausrüstung, Guide etc.).',
            'help_excluded_title'    => 'Nicht enthalten',
            'help_excluded_text'     => 'Markiere alles, was der Kunde separat zahlen oder mitbringen muss (Trinkgelder, alkoholische Getränke, Souvenirs etc.).',
            'select_or_create_title' => 'Reiseplan auswählen oder erstellen',
            'select_existing_items'  => 'Bestehende Elemente auswählen',
            'name_hint'              => 'Bezeichnername für diesen Reiseplan',
            'click_add_item_hint'    => 'Klicke auf „Element hinzufügen“, um neue Elemente zu erstellen',
            'scroll_hint'            => 'Horizontal scrollen, um mehr Spalten zu sehen',
            'no_schedules'           => 'Keine Zeiten',
            'no_prices'              => 'Keine Preise konfiguriert',
            'edit'                   => 'Bearbeiten',
            'slug_auto'              => 'Wird automatisch generiert',
            'added_to_cart'          => 'Zum Warenkorb hinzugefügt',
            'added_to_cart_text'     => 'Die Tour wurde erfolgreich zum Warenkorb hinzugefügt.',

            'none' => [
                'amenities'       => 'Keine Leistungen',
                'exclusions'      => 'Keine Ausschlüsse',
                'itinerary'       => 'Kein Reiseplan',
                'itinerary_items' => 'Keine Elemente',
                'languages'       => 'Keine Sprachen',
                'schedules'       => 'Keine Zeiten',
            ],

            'archive' => 'Archivieren',
            'restore' => 'Wiederherstellen',
            'purge'   => 'Dauerhaft löschen',

            'confirm_archive_title' => 'Tour archivieren?',
            'confirm_archive_text'  => 'Die Tour wird für neue Buchungen deaktiviert, bestehende Buchungen bleiben erhalten.',
            'confirm_purge_title'   => 'Dauerhaft löschen',
            'confirm_purge_text'    => 'Diese Aktion ist nicht umkehrbar und nur erlaubt, wenn die Tour noch nie Buchungen hatte.',

            'filters' => [
                'active'   => 'Aktive',
                'inactive' => 'Inaktive',
                'archived' => 'Archivierte',
                'all'      => 'Alle',
            ],

            'font_decrease_title' => 'Schriftgröße verringern',
            'font_increase_title' => 'Schriftgröße erhöhen',
        ],

    ],

   'image' => [

    'limit_reached_title' => 'Limit erreicht',
    'limit_reached_text'  => 'Das Bildlimit für diese Tour wurde erreicht.',
    'upload_success'      => 'Bilder erfolgreich hochgeladen.',
    'upload_none'         => 'Es wurden keine Bilder hochgeladen.',
    'upload_truncated'    => 'Einige Dateien wurden aufgrund des Tourlimits übersprungen.',
    'done'                => 'Fertig',
    'notice'              => 'Hinweis',
    'saved'               => 'Gespeichert',
    'caption_updated'     => 'Bildunterschrift erfolgreich aktualisiert.',
    'deleted'             => 'Gelöscht',
    'image_removed'       => 'Bild erfolgreich entfernt.',
    'invalid_order'       => 'Ungültige Reihenfolge-Daten.',
    'nothing_to_reorder'  => 'Nichts zum Neuordnen.',
    'order_saved'         => 'Reihenfolge gespeichert.',
    'cover_updated_title' => 'Titelbild aktualisiert',
    'cover_updated_text'  => 'Dieses Bild ist nun das Titelbild.',
    'deleting'            => 'Löschen...',

    'ui' => [
        'page_title_pick'     => 'Tour-Bilder',
        'page_heading'        => 'Tour-Bilder',
        'choose_tour'         => 'Tour auswählen',
        'search_placeholder'  => 'Suche nach ID oder Name…',
        'search_button'       => 'Suchen',
        'no_results'          => 'Keine Touren gefunden.',
        'manage_images'       => 'Bilder verwalten',
        'cover_alt'           => 'Titelbild',
        'images_label'        => 'Bilder',

        'upload_btn'          => 'Hochladen',
        'delete_btn'          => 'Löschen',
        'show_btn'            => 'Anzeigen',
        'close_btn'           => 'Schließen',
        'preview_title'       => 'Bildvorschau',

        'error_title'         => 'Fehler',
        'warning_title'       => 'Warnung',
        'success_title'       => 'Erfolg',
        'cancel_btn'          => 'Abbrechen',

        'confirm_delete_title' => 'Dieses Bild löschen?',
        'confirm_delete_text'  => 'Diese Aktion kann nicht rückgängig gemacht werden.',

        'cover_current_title'    => 'Aktuelles Titelbild',
        'upload_new_cover_title' => 'Neues Titelbild hochladen',
        'cover_file_label'       => 'Titelbild-Datei',
        'file_help_cover'        => 'JPEG/PNG/WebP, max. 30 MB.',
        'id_label'               => 'ID',

        'back_btn'          => 'Zurück zur Liste',

        'stats_images'      => 'Hochgeladene Bilder',
        'stats_cover'       => 'Gesetzte Titelbilder',
        'stats_selected'    => 'Ausgewählt',

        'drag_or_click'     => 'Ziehe deine Bilder hierher oder klicke zum Auswählen.',
        'upload_help'       => 'Erlaubte Formate: JPG, PNG, WebP. Gesamtgröße max. 100 MB.',
        'select_btn'        => 'Dateien auswählen',
        'limit_badge'       => 'Limit von :max Bildern erreicht',
        'files_word'        => 'Dateien',

        'select_all'        => 'Alle auswählen',
        'delete_selected'   => 'Ausgewählte löschen',
        'delete_all'        => 'Alle löschen',

        'select_image_title' => 'Dieses Bild auswählen',
        'select_image_aria'  => 'Bild :id auswählen',

        'cover_label'       => 'Titelbild',
        'cover_btn'         => 'Als Titelbild festlegen',

        'caption_placeholder' => 'Bildunterschrift (optional)',
        'saving_label'        => 'Speichern…',
        'saving_fallback'     => 'Speichern…',
        'none_label'          => 'Keine Bildunterschrift',
        'limit_word'          => 'Limit',

        'confirm_set_cover_title' => 'Als Titelbild festlegen?',
        'confirm_set_cover_text'  => 'Dieses Bild wird das Haupttitelbild der Tour.',
        'confirm_btn'             => 'Ja, fortfahren',

        'confirm_bulk_delete_title' => 'Ausgewählte Bilder löschen?',
        'confirm_bulk_delete_text'  => 'Die ausgewählten Bilder werden endgültig gelöscht.',

        'confirm_delete_all_title' => 'Alle Bilder löschen?',
        'confirm_delete_all_text'  => 'Alle Bilder dieser Tour werden gelöscht.',

        'no_images'           => 'Für diese Tour gibt es noch keine Bilder.',
    ],

    'errors' => [
        'validation'     => 'Die gesendeten Daten sind ungültig.',
        'upload_generic' => 'Einige Bilder konnten nicht hochgeladen werden.',
        'update_caption' => 'Die Bildunterschrift konnte nicht aktualisiert werden.',
        'delete'         => 'Das Bild konnte nicht gelöscht werden.',
        'reorder'        => 'Die Reihenfolge konnte nicht gespeichert werden.',
        'set_cover'      => 'Das Titelbild konnte nicht festgelegt werden.',
        'load_list'      => 'Die Liste konnte nicht geladen werden.',
        'too_large'      => 'Die Datei überschreitet die zulässige Größe. Bitte ein kleineres Bild versuchen.',
    ],
],

    'prices' => [
        'ui' => [
            'page_title'         => 'Preise - :name',
            'header_title'       => 'Preise: :name',
            'back_to_tours'      => 'Zurück zu den Touren',

            'configured_title'   => 'Konfigurierte Kategorien und Preise',
            'empty_title'        => 'Für diese Tour sind keine Kategorien konfiguriert.',
            'empty_hint'         => 'Verwende das Formular rechts, um Kategorien hinzuzufügen.',

            'save_changes'       => 'Änderungen speichern',
            'auto_disable_note'  => 'Preise von 0 $ werden automatisch deaktiviert.',

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
            'create_disabled_hint' => 'Wenn der Preis 0 $ ist, wird die Kategorie deaktiviert erstellt.',
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
            'success' => 'Vorgang erfolgreich abgeschlossen.',
            'error'   => 'Es ist ein Fehler aufgetreten.',
        ],

        'js' => [
            'max_ge_min'            => 'Das Maximum muss größer oder gleich dem Minimum sein.',
            'auto_disabled_tooltip' => 'Preis ist 0 $ – automatisch deaktiviert',
            'fix_errors'            => 'Korrigiere die Mindest- und Höchstmengen.',
        ],
    ],

    'ajax' => [
        'category_created'   => 'Kategorie erfolgreich erstellt.',
        'category_error'     => 'Fehler beim Erstellen der Kategorie.',
        'language_created'   => 'Sprache erfolgreich erstellt.',
        'language_error'     => 'Fehler beim Erstellen der Sprache.',
        'amenity_created'    => 'Leistung erfolgreich erstellt.',
        'amenity_error'      => 'Fehler beim Erstellen der Leistung.',
        'schedule_created'   => 'Zeitplan erfolgreich erstellt.',
        'schedule_error'     => 'Fehler beim Erstellen des Zeitplans.',
        'itinerary_created'  => 'Reiseplan erfolgreich erstellt.',
        'itinerary_error'    => 'Fehler beim Erstellen des Reiseplans.',
        'translation_error'  => 'Fehler beim Übersetzen.',
    ],

    'modal' => [
        'create_category'  => 'Neue Kategorie erstellen',
        'create_language'  => 'Neue Sprache erstellen',
        'create_amenity'   => 'Neue Leistung erstellen',
        'create_schedule'  => 'Neuen Zeitplan erstellen',
        'create_itinerary' => 'Neuen Reiseplan erstellen',
    ],

    'validation' => [
        'slug_taken'     => 'Dieser Slug wird bereits verwendet.',
        'slug_available' => 'Slug verfügbar.',
    ],

];
