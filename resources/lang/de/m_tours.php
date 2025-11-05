<?php

/*************************************************************
 *  MODUL FÜR ÜBERSETZUNGEN: TOURS
 *  Datei: resources/lang/de/m_tours.php  (Parte 1/2)
 *
 *  Enthält:
 *  [01] COMMON
 *  [02] AMENITY
 *  [03] SCHEDULE
 *  [04] ITINERARY_ITEM
 *************************************************************/

return [

    // =========================================================
    // [01] COMMON
    // =========================================================
    'common' => [
        'success_title'        => 'Erfolg',
        'error_title'          => 'Fehler',
        'people'               => 'Personen',
        'hours'                => 'Stunden',
        'success'              => 'Erfolg',
        'error'                => 'Fehler',
        'cancel'               => 'Abbrechen',
        'confirm_delete'       => 'Ja, löschen',
        'unspecified'          => 'Nicht angegeben',
        'no_description'       => 'Keine Beschreibung',
        'required_fields_title'=> 'Pflichtfelder',
        'required_fields_text' => 'Bitte fülle die Pflichtfelder aus: Name und maximale Kapazität.',
        'active'               => 'Aktiv',
        'inactive'             => 'Inaktiv',
        'notice'               => 'Hinweis',
        'na'                   => 'Non configuré',
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
            'page_title'    => 'Ausstattungen',
            'page_heading'  => 'Verwaltung der Ausstattungen',
            'list_title'    => 'Ausstattungs-Liste',

            'add'            => 'Ausstattung hinzufügen',
            'create_title'   => 'Ausstattung anlegen',
            'edit_title'     => 'Ausstattung bearbeiten',
            'save'           => 'Speichern',
            'update'         => 'Aktualisieren',
            'cancel'         => 'Abbrechen',
            'close'          => 'Schließen',
            'state'          => 'Status',
            'actions'        => 'Aktionen',
            'delete_forever' => 'Dauerhaft löschen',

            'processing' => 'Wird verarbeitet...',
            'applying'   => 'Wird angewendet...',
            'deleting'   => 'Wird gelöscht...',

            'toggle_on'  => 'Ausstattung aktivieren',
            'toggle_off' => 'Ausstattung deaktivieren',

            'toggle_confirm_on_title'  => 'Ausstattung aktivieren?',
            'toggle_confirm_off_title' => 'Ausstattung deaktivieren?',
            'toggle_confirm_on_html'   => 'Die Ausstattung <b>:label</b> wird aktiv.',
            'toggle_confirm_off_html'  => 'Die Ausstattung <b>:label</b> wird inaktiv.',

            'delete_confirm_title' => 'Dauerhaft löschen?',
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
            'deleted'     => 'Ausstattung dauerhaft gelöscht.',
        ],

        'error' => [
            'create' => 'Ausstattung konnte nicht erstellt werden.',
            'update' => 'Ausstattung konnte nicht aktualisiert werden.',
            'toggle' => 'Status der Ausstattung konnte nicht geändert werden.',
            'delete' => 'Ausstattung konnte nicht gelöscht werden.',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Ungültiger Name',
                'required' => 'Das :attribute ist erforderlich.',
                'string'   => 'Das :attribute muss eine Zeichenkette sein.',
                'max'      => 'Das :attribute darf :max Zeichen nicht überschreiten.',
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
            'page_title'        => 'Tour-Zeitpläne',
            'page_heading'      => 'Verwaltung der Zeitpläne',

            'general_title'     => 'Allgemeine Zeitpläne',
            'new_schedule'      => 'Neuer Zeitplan',
            'new_general_title' => 'Neuer allgemeiner Zeitplan',
            'new'               => 'Neu',
            'edit_schedule'     => 'Zeitplan bearbeiten',
            'edit_global'       => 'Bearbeiten (global)',

            'assign_existing'    => 'Vorhandenen zuweisen',
            'assign_to_tour'     => 'Zeitplan „:tour“ zuweisen',
            'select_schedule'    => 'Zeitplan auswählen',
            'choose'             => 'Auswählen',
            'assign'             => 'Zuweisen',
            'new_for_tour_title' => 'Neuer Zeitplan für „:tour“',

            'time_range'        => 'Zeitfenster',
            'state'             => 'Status',
            'actions'           => 'Aktionen',
            'schedule_state'    => 'Zeitplan',
            'assignment_state'  => 'Zuweisung',
            'no_general'        => 'Keine allgemeinen Zeitpläne vorhanden.',
            'no_tour_schedules' => 'Dieser Tour sind noch keine Zeitpläne zugewiesen.',
            'no_label'          => 'Ohne Bezeichnung',
            'assigned_count'    => 'zugewiesene(r) Zeitplan/-pläne',

            'toggle_global_title'     => 'Aktivieren/Deaktivieren (global)',
            'toggle_global_on_title'  => 'Zeitplan aktivieren (global)?',
            'toggle_global_off_title' => 'Zeitplan deaktivieren (global)?',
            'toggle_global_on_html'   => '<b>:label</b> wird für alle Touren aktiviert.',
            'toggle_global_off_html'  => '<b>:label</b> wird für alle Touren deaktiviert.',

            'toggle_on_tour'          => 'In dieser Tour aktivieren',
            'toggle_off_tour'         => 'In dieser Tour deaktivieren',
            'toggle_assign_on_title'  => 'In dieser Tour aktivieren?',
            'toggle_assign_off_title' => 'In dieser Tour deaktivieren?',
            'toggle_assign_on_html'   => 'Die Zuweisung wird für <b>:tour</b> <b>aktiv</b> sein.',
            'toggle_assign_off_html'  => 'Die Zuweisung wird für <b>:tour</b> <b>inaktiv</b> sein.',

            'detach_from_tour'     => 'Von der Tour entfernen',
            'detach_confirm_title' => 'Von der Tour entfernen?',
            'detach_confirm_html'  => 'Der Zeitplan wird von <b>:tour</b> <b>entfernt</b>.',

            'delete_forever'       => 'Löschen (global)',
            'delete_confirm_title' => 'Dauerhaft löschen?',
            'delete_confirm_html'  => '<b>:label</b> (global) wird gelöscht und dies kann nicht rückgängig gemacht werden.',

            'yes_continue' => 'Ja, fortfahren',
            'yes_delete'   => 'Ja, löschen',
            'yes_detach'   => 'Ja, entfernen',

            'this_schedule' => 'dieser Zeitplan',
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
            'missing_fields_text'  => 'Bitte die Pflichtfelder prüfen (Beginn, Ende und Kapazität).',
            'could_not_save'       => 'Konnte nicht gespeichert werden',
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
            'toggle'               => 'Globaler Status des Zeitplans konnte nicht geändert werden.',
            'attach'               => 'Zeitplan konnte der Tour nicht zugewiesen werden.',
            'detach'               => 'Zeitplan konnte nicht von der Tour entfernt werden.',
            'assignment_toggle'    => 'Zuweisungsstatus konnte nicht geändert werden.',
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
            'list_title'    => 'Programmpunkte',
            'add_item'      => 'Programmpunkt hinzufügen',
            'register_item' => 'Programmpunkt anlegen',
            'edit_item'     => 'Programmpunkt bearbeiten',
            'save'          => 'Speichern',
            'update'        => 'Aktualisieren',
            'cancel'        => 'Abbrechen',
            'state'         => 'Status',
            'actions'       => 'Aktionen',
            'see_more'      => 'Mehr anzeigen',
            'see_less'      => 'Weniger anzeigen',

            'toggle_on'  => 'Programmpunkt aktivieren',
            'toggle_off' => 'Programmpunkt deaktivieren',

            'delete_forever'       => 'Dauerhaft löschen',
            'delete_confirm_title' => 'Dauerhaft löschen?',
            'delete_confirm_html'  => '<b>:label</b> wird gelöscht und dies kann nicht rückgängig gemacht werden.',
            'yes_delete'           => 'Ja, löschen',
            'item_this'            => 'dieser Programmpunkt',

            'processing' => 'Wird verarbeitet...',
            'applying'   => 'Wird angewendet...',
            'deleting'   => 'Wird gelöscht...',
        ],

        'success' => [
            'created'     => 'Programmpunkt erfolgreich erstellt.',
            'updated'     => 'Programmpunkt erfolgreich aktualisiert.',
            'activated'   => 'Programmpunkt erfolgreich aktiviert.',
            'deactivated' => 'Programmpunkt erfolgreich deaktiviert.',
            'deleted'     => 'Programmpunkt dauerhaft gelöscht.',
        ],

        'error' => [
            'create' => 'Programmpunkt konnte nicht erstellt werden.',
            'update' => 'Programmpunkt konnte nicht aktualisiert werden.',
            'toggle' => 'Status des Programmpunkts konnte nicht geändert werden.',
            'delete' => 'Programmpunkt konnte nicht gelöscht werden.',
        ],

        'validation' => [
            'title' => [
                'required' => 'Das :attribute ist erforderlich.',
                'string'   => 'Das :attribute muss eine Zeichenkette sein.',
                'max'      => 'Das :attribute darf :max Zeichen nicht überschreiten.',
            ],
            'description' => [
                'required' => 'Die :attribute ist erforderlich.',
                'string'   => 'Die :attribute muss eine Zeichenkette sein.',
                'max'      => 'Die :attribute darf :max Zeichen nicht überschreiten.',
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
            'page_title'    => 'Reisepläne & Programmpunkte',
            'page_heading'  => 'Reisepläne & Verwaltung der Programmpunkte',
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
            'yes_continue' => 'Ja, fortfahren',

            'assign_title'          => 'Programmpunkte zu :name zuweisen',
            'drag_hint'             => 'Zum Festlegen der Reihenfolge per Drag & Drop verschieben.',
            'drag_handle'           => 'Zum Sortieren ziehen',
            'select_one_title'      => 'Mindestens einen Punkt auswählen',
            'select_one_text'       => 'Bitte wähle mindestens einen Programmpunkt aus, um fortzufahren.',
            'assign_confirm_title'  => 'Ausgewählte Punkte zuweisen?',
            'assign_confirm_button' => 'Ja, zuweisen',
            'assigning'             => 'Wird zugewiesen...',

            'no_items_assigned'       => 'Diesem Reiseplan sind keine Programmpunkte zugewiesen.',
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

        'success' => [
            'created'        => 'Reiseplan erfolgreich erstellt.',
            'updated'        => 'Reiseplan erfolgreich aktualisiert.',
            'activated'      => 'Reiseplan erfolgreich aktiviert.',
            'deactivated'    => 'Reiseplan erfolgreich deaktiviert.',
            'deleted'        => 'Reiseplan dauerhaft gelöscht.',
            'items_assigned' => 'Programmpunkte erfolgreich zugewiesen.',
        ],

        'error' => [
            'create'  => 'Reiseplan konnte nicht erstellt werden.',
            'update'  => 'Reiseplan konnte nicht aktualisiert werden.',
            'toggle'  => 'Status des Reiseplans konnte nicht geändert werden.',
            'delete'  => 'Reiseplan konnte nicht gelöscht werden.',
            'assign'  => 'Programmpunkte konnten nicht zugewiesen werden.',
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
                'required'      => 'Mindestens ein Programmpunkt muss ausgewählt werden.',
                'array'         => 'Das Format der Programmpunkte ist ungültig.',
                'min'           => 'Mindestens ein Programmpunkt muss ausgewählt werden.',
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
            'create' => 'Sprache konnte nicht erstellt werden.',
            'update' => 'Sprache konnte nicht aktualisiert werden.',
            'toggle' => 'Sprachstatus konnte nicht geändert werden.',
            'delete' => 'Sprache konnte nicht gelöscht werden.',
            'save'   => 'Konnte nicht gespeichert werden',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Ungültiger Name',
                'required' => 'Der Sprachname ist erforderlich.',
                'string'   => 'Das :attribute muss eine Zeichenkette sein.',
                'max'      => 'Das :attribute darf :max Zeichen nicht überschreiten.',
                'unique'   => 'Es existiert bereits eine Sprache mit diesem Namen.',
            ],
        ],
    ],

    // =========================================================
    // [07] TOUR
    // =========================================================
    'tour' => [
        'title' => 'Tours',

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
            'schedules'     => 'Zeitpläne',
            'adult_price'   => 'Preis Erwachsene',
            'kid_price'     => 'Preis Kinder',
            'length_hours'  => 'Dauer (Stunden)',
            'max_capacity'  => 'Max. Kapazität',
            'type'          => 'Tour-Typ',
            'viator_code'   => 'Viator-Code',
            'status'        => 'Status',
            'actions'       => 'Aktionen',
            'group_size'    => 'Gruppengröße',
        ],

        'pricing' => [
            'note_title'              => 'Hinweis:',
            'note_text'               => 'Lege hier die Basispreise für jede Kundenkategorie fest.',
            'manage_detailed_hint'    => ' Für eine detaillierte Verwaltung nutze oben den Button „Detaillierte Preise verwalten“.',
            'price_usd'               => 'Preis (USD)',
            'min_quantity'            => 'Mindestmenge',
            'max_quantity'            => 'Höchstmenge',
            'status'                  => 'Status',
            'active'                  => 'Aktiv',
            'no_categories'           => 'Keine Kundenkategorien konfiguriert.',
            'create_categories_first' => 'Zuerst Kategorien erstellen',
        ],

        'schedules_form' => [
            'available_title'        => 'Verfügbare Zeitpläne',
            'select_hint'            => 'Wähle die Zeitpläne für diese Tour',
            'no_schedules'           => 'Keine Zeitpläne verfügbar.',
            'create_schedules_link'  => 'Zeitpläne erstellen',

            'create_new_title'       => 'Neuen Zeitplan erstellen',
            'label_placeholder'      => 'Z. B.: Morgen, Nachmittag',
            'create_and_assign'      => 'Diesen Zeitplan erstellen und der Tour zuweisen',

            'info_title'             => 'Information',
            'schedules_title'        => 'Zeitpläne',
            'schedules_text'         => 'Wähle einen oder mehrere Zeitpläne, in denen diese Tour verfügbar ist.',
            'create_block_title'     => 'Neu erstellen',
            'create_block_text'      => 'Falls ein benötigter Zeitplan nicht existiert, kannst du ihn hier erstellen und direkt der Tour zuweisen.',

            'current_title'          => 'Aktuelle Zeitpläne',
            'none_assigned'          => 'Keine Zeitpläne zugewiesen',
        ],

        'summary' => [
            'preview_title'        => 'Tour-Vorschau',
            'preview_text_create'  => 'Prüfe alle Informationen, bevor du die Tour erstellst.',
            'preview_text_update'  => 'Prüfe alle Informationen, bevor du die Tour aktualisierst.',

            'basic_details_title'  => 'Basisdetails',
            'description_title'    => 'Beschreibung',
            'prices_title'         => 'Preise nach Kategorie',
            'schedules_title'      => 'Zeitpläne',
            'languages_title'      => 'Sprachen',
            'itinerary_title'      => 'Reiseplan',

            'table' => [
                'category' => 'Kategorie',
                'price'    => 'Preis',
                'min_max'  => 'Min-Max',
            ],

            'not_specified'        => 'Nicht angegeben',
            'slug_autogenerated'   => 'Wird automatisch generiert',
            'no_description'       => 'Keine Beschreibung',
            'no_active_prices'     => 'Keine aktiven Preise konfiguriert',
            'no_languages'         => 'Keine Sprachen zugewiesen',
            'none_included'        => 'Keine Inklusivleistungen angegeben',
            'none_excluded'        => 'Keine Ausschlüsse angegeben',

            'units' => [
                'hours'  => 'Stunden',
                'people' => 'Personen',
            ],

            'create_note' => 'Zeitpläne, Preise, Sprachen und Ausstattungen werden hier angezeigt, nachdem die Tour gespeichert wurde.',
        ],

        'alerts' => [
            'delete_title' => 'Tour löschen?',
            'delete_text'  => 'Die Tour wird in „Gelöscht“ verschoben. Du kannst sie später wiederherstellen.',
            'purge_title'  => 'Endgültig löschen?',
            'purge_text'   => 'Diese Aktion ist irreversibel.',
            'purge_text_with_bookings' => 'Diese Tour hat :count Buchung(en). Sie werden nicht gelöscht, sondern bleiben ohne zugeordnete Tour bestehen.',
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
            'id'            => 'ID',
            'name'          => 'Name',
            'overview'      => 'Übersicht',
            'amenities'     => 'Ausstattungen',
            'exclusions'    => 'Ausschlüsse',
            'itinerary'     => 'Reiseplan',
            'languages'     => 'Sprachen',
            'schedules'     => 'Zeitpläne',
            'adult_price'   => 'Preis Erwachsene',
            'kid_price'     => 'Preis Kinder',
            'length_hours'  => 'Dauer (h)',
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
            'purged'      => 'Tour endgültig gelöscht.',
        ],

        'error' => [
            'create'    => 'Die Tour konnte nicht erstellt werden.',
            'update'    => 'Die Tour konnte nicht aktualisiert werden.',
            'delete'    => 'Die Tour konnte nicht gelöscht werden.',
            'toggle'    => 'Der Tourstatus konnte nicht geändert werden.',
            'not_found' => 'Die Tour existiert nicht.',
            'restore'            => 'Die Tour konnte nicht wiederhergestellt werden.',
            'purge'              => 'Die Tour konnte nicht endgültig gelöscht werden.',
            'purge_has_bookings' => 'Endgültiges Löschen nicht möglich: Die Tour hat Buchungen.',
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
            'see_less'         => 'Weniger anzeigen',
            'load_more'        => 'Mehr laden',
            'loading'          => 'Wird geladen...',
            'load_more_error'  => 'Weitere Tours konnten nicht geladen werden.',
            'confirm_title'    => 'Bestätigung',
            'confirm_text'     => 'Möchtest du diese Aktion bestätigen?',
            'yes_confirm'      => 'Ja, bestätigen',
            'no_confirm'       => 'Nein, abbrechen',
            'add_tour'         => 'Tour hinzufügen',
            'edit_tour'        => 'Tour bearbeiten',
            'delete_tour'      => 'Tour löschen',
            'toggle_tour'      => 'Tour aktivieren/deaktivieren',
            'view_cart'        => 'Warenkorb anzeigen',
            'add_to_cart'      => 'In den Warenkorb',
            'slug_help'        => 'URL-Bezeichner der Tour (ohne Leerzeichen oder Akzente)',
            'generate_auto'       => 'Automatisch generieren',
            'slug_preview_label'  => 'Vorschau',
            'saved'               => 'Gespeichert',

            'available_languages'    => 'Verfügbare Sprachen',
            'default_capacity'       => 'Standardkapazität',
            'create_new_schedules'   => 'Neue Zeitpläne erstellen',
            'multiple_hint_ctrl_cmd' => 'Halte STRG/CMD, um mehrere auszuwählen',
            'use_existing_schedules' => 'Bestehende Zeitpläne verwenden',
            'add_schedule'           => 'Zeitplan hinzufügen',
            'schedules_title'        => 'Zeitpläne der Tour',
            'amenities_included'     => 'Inklusive Ausstattungen',
            'amenities_excluded'     => 'Nicht enthaltene Ausstattungen',
            'color'                  => 'Tour-Farbe',
            'remove'                 => 'Entfernen',
            'choose_itinerary'       => 'Reiseplan wählen',
            'select_type'            => 'Typ auswählen',
            'empty_means_default'    => 'Standard',
            'actives'                => 'Aktive',
            'inactives'              => 'Inaktive',
            'archived'               => 'Archivierte',
            'all'                    => 'Alle',
            'help_title'             => 'Hilfe',
            'amenities_included_hint' => 'Wähle aus, was im Tourpreis enthalten ist.',
            'amenities_excluded_hint' => 'Wähle aus, was NICHT enthalten ist.',
            'help_included_title'      => 'Inklusive',
            'help_included_text'       => 'Markiere alles, was im Preis enthalten ist (Transport, Mahlzeiten, Eintritt, Ausrüstung, Guide usw.).',
            'help_excluded_title'      => 'Nicht enthalten',
            'help_excluded_text'       => 'Markiere, was separat zu zahlen/mitzubringen ist (Trinkgelder, alkoholische Getränke, Souvenirs usw.).',
            'select_or_create_title'  => 'Reiseplan auswählen oder erstellen',
            'select_existing_items'   => 'Vorhandene Programmpunkte auswählen',
            'name_hint'               => 'Bezeichnender Name für diesen Reiseplan',
            'click_add_item_hint'     => 'Klicke auf „Programmpunkt hinzufügen“, um neue Punkte zu erstellen',
            'scroll_hint'             => 'Horizontal scrollen, um mehr Spalten zu sehen',
            'no_schedules'            => 'Keine Zeitpläne',
            'no_prices'               => 'Keine Preise konfiguriert',
            'edit'                    => 'Bearbeiten',
            'slug_auto'               => 'Wird automatisch generiert',
            'added_to_cart'           => 'Zum Warenkorb hinzugefügt',
            'added_to_cart_text'      => 'Die Tour wurde dem Warenkorb erfolgreich hinzugefügt.',

            'none' => [
                'amenities'       => 'Keine Ausstattungen',
                'exclusions'      => 'Keine Ausschlüsse',
                'itinerary'       => 'Kein Reiseplan',
                'itinerary_items' => 'Keine Programmpunkte',
                'languages'       => 'Keine Sprachen',
                'schedules'       => 'Keine Zeitpläne',
            ],

            // Archivieren/Wiederherstellen/Endgültig löschen
            'archive' => 'Archivieren',
            'restore' => 'Wiederherstellen',
            'purge'   => 'Endgültig löschen',

            'confirm_archive_title' => 'Tour archivieren?',
            'confirm_archive_text'  => 'Die Tour ist für neue Buchungen deaktiviert, bestehende Buchungen bleiben erhalten.',
            'confirm_purge_title'   => 'Endgültig löschen',
            'confirm_purge_text'    => 'Diese Aktion ist irreversibel und nur erlaubt, wenn die Tour nie Buchungen hatte.',

            // Statusfilter
            'filters' => [
                'active'   => 'Aktive',
                'inactive' => 'Inaktive',
                'archived' => 'Archivierte',
                'all'      => 'Alle',
            ],

            // Schriftgrößen-Toolbar (tourlist.blade.php)
            'font_decrease_title' => 'Schriftgröße verringern',
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
        'upload_none'         => 'Keine Bilder hochgeladen.',
        'upload_truncated'    => 'Einige Dateien wurden wegen des Tour-Limits übersprungen.',
        'done'                => 'Fertig',
        'notice'              => 'Hinweis',
        'saved'               => 'Speichern',
        'caption_updated'     => 'Bildunterschrift erfolgreich aktualisiert.',
        'deleted'             => 'Gelöscht',
        'image_removed'       => 'Bild erfolgreich entfernt.',
        'invalid_order'       => 'Ungültige Sortierdaten.',
        'nothing_to_reorder'  => 'Nichts zu sortieren.',
        'order_saved'         => 'Reihenfolge gespeichert.',
        'cover_updated_title' => 'Titelbild aktualisieren',
        'cover_updated_text'  => 'Dieses Bild ist jetzt das Titelbild.',
        'deleting'            => 'Wird gelöscht...',

        'ui' => [
            'page_title_pick'     => 'Tour-Bilder',
            'page_heading'        => 'Tour-Bilder',
            'choose_tour'         => 'Tour auswählen',
            'search_placeholder'  => 'Nach ID oder Name suchen…',
            'search_button'       => 'Suchen',
            'no_results'          => 'Keine Tours gefunden.',
            'manage_images'       => 'Bilder verwalten',
            'cover_alt'           => 'Titelbild',
            'images_label'        => 'Bilder',
            'upload_btn'          => 'Hochladen',
            'caption_placeholder' => 'Bildunterschrift (optional)',
            'set_cover_btn'       => 'Wähle das gewünschte Titelbild',
            'no_images'           => 'Für diese Tour gibt es noch keine Bilder.',
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
            'cover_current_title'      => 'Aktuelles Titelbild',
            'upload_new_cover_title'   => 'Neues Titelbild hochladen',
            'cover_file_label'         => 'Titelbild-Datei',
            'file_help_cover'          => 'JPEG/PNG/WebP, max. 30 MB',
            'id_label'                 => 'ID',
        ],

        'errors' => [
            'validation'     => 'Die gesendeten Daten sind ungültig.',
            'upload_generic' => 'Einige Bilder konnten nicht hochgeladen werden.',
            'update_caption' => 'Bildunterschrift konnte nicht aktualisiert werden.',
            'delete'         => 'Bild konnte nicht gelöscht werden.',
            'reorder'        => 'Reihenfolge konnte nicht gespeichert werden.',
            'set_cover'      => 'Titelbild konnte nicht gesetzt werden.',
            'load_list'      => 'Liste konnte nicht geladen werden.',
            'too_large'      => 'Die Datei überschreitet die zulässige Größe. Bitte ein kleineres Bild verwenden.',
        ],
    ],
'prices' => [
    'ui' => [
        'page_title'         => 'Preise - :name',
        'header_title'       => 'Preise: :name',
        'back_to_tours'      => 'Zurück zu den Touren',

        'configured_title'   => 'Konfigurierte Kategorien und Preise',
        'empty_title'        => 'Keine Kategorien für diese Tour konfiguriert.',
        'empty_hint'         => 'Verwenden Sie das Formular rechts, um Kategorien hinzuzufügen.',

        'save_changes'       => 'Änderungen speichern',
        'auto_disable_note'  => 'Preise mit 0 $ werden automatisch deaktiviert',

        'add_category'       => 'Kategorie hinzufügen',

        'all_assigned_title' => 'Alle Kategorien sind zugewiesen',
        'all_assigned_text'  => 'Keine weiteren Kategorien für diese Tour verfügbar.',

        'info_title'         => 'Informationen',
        'tour_label'         => 'Tour',
        'configured_count'   => 'Konfigurierte Kategorien',
        'active_count'       => 'Aktive Kategorien',

        'fields_title'       => 'Felder',
        'rules_title'        => 'Regeln',

        'field_price'        => 'Preis',
        'field_min'          => 'Min',
        'field_max'          => 'Max',
        'field_status'       => 'Status',

        'rule_min_le_max'    => 'Das Minimum muss kleiner oder gleich dem Maximum sein',
        'rule_zero_disable'  => 'Preise mit 0 $ werden automatisch deaktiviert',
        'rule_only_active'   => 'Nur aktive Kategorien erscheinen auf der öffentlichen Website',
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
        'select_placeholder'  => '-- Auswählen --',
        'category'            => 'Kategorie',
        'price_usd'           => 'Preis (USD)',
        'min'                 => 'Minimum',
        'max'                 => 'Maximum',
        'create_disabled_hint'=> 'Wenn der Preis 0 $ beträgt, wird die Kategorie deaktiviert erstellt',
        'add'                 => 'Hinzufügen',
    ],

    'modal' => [
        'delete_title'   => 'Kategorie löschen',
        'delete_text'    => 'Diese Kategorie aus dieser Tour löschen?',
        'cancel'         => 'Abbrechen',
        'delete'         => 'Löschen',
        'delete_tooltip' => 'Kategorie löschen',
    ],

    'flash' => [
        'success' => 'Vorgang erfolgreich abgeschlossen.',
        'error'   => 'Ein Fehler ist aufgetreten.',
    ],

    'js' => [
        'max_ge_min'            => 'Das Maximum muss größer oder gleich dem Minimum sein',
        'auto_disabled_tooltip' => 'Preis 0 $ – automatisch deaktiviert',
        'fix_errors'            => 'Bitte korrigieren Sie die Mindest- und Höchstmengen',
    ],
],

];
