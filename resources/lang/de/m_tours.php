<?php

/*************************************************************
 *  ÜBERSETZUNGSMODUL: TOURS
 *  Datei: resources/lang/de/m_tours.php
 *
 *  Inhaltsverzeichnis (Bereiche und Startzeile)
 *  [01] COMMON           -> Zeile 19
 *  [02] AMENITY          -> Zeile 27
 *  [03] SCHEDULE         -> Zeile 90
 *  [04] ITINERARY_ITEM   -> Zeile 176
 *  [05] ITINERARY        -> Zeile 238
 *  [06] LANGUAGE         -> Zeile 301
 *  [07] TOUR             -> Zeile 385
 *************************************************************/

return [

    // =========================================================
    // [01] COMMON
    // =========================================================
    'common' => [
        'success_title' => 'Erfolg',
        'error_title'   => 'Fehler',
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
            'list_title'    => 'Liste der Ausstattungen',

            'add'            => 'Ausstattung hinzufügen',
            'create_title'   => 'Ausstattung erstellen',
            'edit_title'     => 'Ausstattung bearbeiten',
            'save'           => 'Speichern',
            'update'         => 'Aktualisieren',
            'cancel'         => 'Abbrechen',
            'close'          => 'Schließen',
            'state'          => 'Status',
            'actions'        => 'Aktionen',
            'delete_forever' => 'Dauerhaft löschen',

            'processing' => 'Wird verarbeitet…',
            'applying'   => 'Wird angewendet…',
            'deleting'   => 'Wird gelöscht…',

            'toggle_on'  => 'Ausstattung aktivieren',
            'toggle_off' => 'Ausstattung deaktivieren',

            'toggle_confirm_on_title'  => 'Ausstattung aktivieren?',
            'toggle_confirm_off_title' => 'Ausstattung deaktivieren?',
            'toggle_confirm_on_html'   => 'Die Ausstattung <b>:label</b> wird aktiviert.',
            'toggle_confirm_off_html'  => 'Die Ausstattung <b>:label</b> wird deaktiviert.',

            'delete_confirm_title' => 'Dauerhaft löschen?',
            'delete_confirm_html'  => '<b>:label</b> wird gelöscht und kann nicht rückgängig gemacht werden.',

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
                'required' => 'Das Feld :attribute ist erforderlich.',
                'string'   => 'Das Feld :attribute muss eine Zeichenkette sein.',
                'max'      => 'Das Feld :attribute darf nicht mehr als :max Zeichen enthalten.',
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
            'no_general'        => 'Keine allgemeinen Zeitpläne.',
            'no_tour_schedules' => 'Diese Tour hat noch keine Zeitpläne.',
            'no_label'          => 'Keine Bezeichnung',
            'assigned_count'    => 'zugewiesene(r) Zeitplan/-pläne',

            'toggle_global_title'     => 'Aktivieren/Deaktivieren (global)',
            'toggle_global_on_title'  => 'Zeitplan global aktivieren?',
            'toggle_global_off_title' => 'Zeitplan global deaktivieren?',
            'toggle_global_on_html'   => '<b>:label</b> wird für alle Touren aktiviert.',
            'toggle_global_off_html'  => '<b>:label</b> wird für alle Touren deaktiviert.',

            'toggle_on_tour'          => 'In dieser Tour aktivieren',
            'toggle_off_tour'         => 'In dieser Tour deaktivieren',
            'toggle_assign_on_title'  => 'In dieser Tour aktivieren?',
            'toggle_assign_off_title' => 'In dieser Tour deaktivieren?',
            'toggle_assign_on_html'   => 'Die Zuweisung wird für <b>:tour</b> aktiviert.',
            'toggle_assign_off_html'  => 'Die Zuweisung wird für <b>:tour</b> deaktiviert.',

            'detach_from_tour'     => 'Von Tour entfernen',
            'detach_confirm_title' => 'Von Tour entfernen?',
            'detach_confirm_html'  => 'Der Zeitplan wird von <b>:tour</b> <b>entfernt</b>.',

            'delete_forever'       => 'Löschen (global)',
            'delete_confirm_title' => 'Dauerhaft löschen?',
            'delete_confirm_html'  => '<b>:label</b> wird global gelöscht und kann nicht rückgängig gemacht werden.',

            'yes_continue' => 'Ja, fortfahren',
            'yes_delete'   => 'Ja, löschen',
            'yes_detach'   => 'Ja, entfernen',

            'this_schedule' => 'dieser Zeitplan',
            'this_tour'     => 'diese Tour',

            'processing'     => 'Wird verarbeitet…',
            'applying'       => 'Wird angewendet…',
            'deleting'       => 'Wird gelöscht…',
            'removing'       => 'Wird entfernt…',
            'saving_changes' => 'Änderungen werden gespeichert…',
            'save'           => 'Speichern',
            'save_changes'   => 'Änderungen speichern',
            'cancel'         => 'Abbrechen',

            'missing_fields_title' => 'Fehlende Daten',
            'missing_fields_text'  => 'Bitte die Pflichtfelder prüfen (Beginn, Ende und Kapazität).',
            'could_not_save'       => 'Speichern nicht möglich',
        ],

        'success' => [
            'created'                => 'Zeitplan erfolgreich erstellt.',
            'updated'                => 'Zeitplan erfolgreich aktualisiert.',
            'activated_global'       => 'Zeitplan (global) erfolgreich aktiviert.',
            'deactivated_global'     => 'Zeitplan (global) erfolgreich deaktiviert.',
            'attached'               => 'Zeitplan der Tour zugewiesen.',
            'detached'               => 'Zeitplan von der Tour entfernt.',
            'assignment_activated'   => 'Zuweisung für diese Tour aktiviert.',
            'assignment_deactivated' => 'Zuweisung für diese Tour deaktiviert.',
            'deleted'                => 'Zeitplan erfolgreich gelöscht.',
        ],

        'error' => [
            'create'               => 'Fehler beim Erstellen des Zeitplans.',
            'update'               => 'Fehler beim Aktualisieren des Zeitplans.',
            'toggle'               => 'Globaler Status des Zeitplans konnte nicht geändert werden.',
            'attach'               => 'Zeitplan konnte der Tour nicht zugewiesen werden.',
            'detach'               => 'Zeitplan konnte nicht von der Tour entfernt werden.',
            'assignment_toggle'    => 'Zuweisungsstatus konnte nicht geändert werden.',
            'not_assigned_to_tour' => 'Der Zeitplan ist dieser Tour nicht zugewiesen.',
            'delete'               => 'Fehler beim Löschen des Zeitplans.',
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
            'delete_confirm_html'  => '<b>:label</b> wird gelöscht und kann nicht rückgängig gemacht werden.',
            'yes_delete'           => 'Ja, löschen',
            'item_this'            => 'dieses Element',

            'processing' => 'Wird verarbeitet…',
            'applying'   => 'Wird angewendet…',
            'deleting'   => 'Wird gelöscht…',
        ],

        'success' => [
            'created'     => 'Reiseplan-Element erfolgreich erstellt.',
            'updated'     => 'Element erfolgreich aktualisiert.',
            'activated'   => 'Element erfolgreich aktiviert.',
            'deactivated' => 'Element erfolgreich deaktiviert.',
            'deleted'     => 'Element dauerhaft gelöscht.',
        ],

        'error' => [
            'create' => 'Element konnte nicht erstellt werden.',
            'update' => 'Element konnte nicht aktualisiert werden.',
            'toggle' => 'Elementstatus konnte nicht geändert werden.',
            'delete' => 'Element konnte nicht gelöscht werden.',
        ],

        'validation' => [
            'title' => [
                'required' => 'Das :attribute ist erforderlich.',
                'string'   => 'Das :attribute muss eine Zeichenkette sein.',
                'max'      => 'Das :attribute darf :max Zeichen nicht überschreiten.',
            ],
            'description' => [
                'required' => 'Das :attribute ist erforderlich.',
                'string'   => 'Das :attribute muss eine Zeichenkette sein.',
                'max'      => 'Das :attribute darf :max Zeichen nicht überschreiten.',
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
            'page_title'    => 'Reisepläne & Elemente',
            'page_heading'  => 'Verwaltung der Reisepläne und Elemente',
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

            'assign_title'          => 'Elemente für :name zuweisen',
            'drag_hint'             => 'Elemente per Drag & Drop anordnen.',
            'drag_handle'           => 'Ziehen zum Neuordnen',
            'select_one_title'      => 'Mindestens ein Element auswählen',
            'select_one_text'       => 'Bitte mindestens ein Element auswählen, um fortzufahren.',
            'assign_confirm_title'  => 'Ausgewählte Elemente zuweisen?',
            'assign_confirm_button' => 'Ja, zuweisen',
            'assigning'             => 'Wird zugewiesen…',

            'no_items_assigned'       => 'Diesem Reiseplan sind keine Elemente zugewiesen.',
            'itinerary_this'          => 'dieser Reiseplan',
            'processing'              => 'Wird verarbeitet…',
            'saving'                  => 'Wird gespeichert…',
            'activating'              => 'Wird aktiviert…',
            'deactivating'            => 'Wird deaktiviert…',
            'applying'                => 'Wird angewendet…',
            'deleting'                => 'Wird gelöscht…',
            'flash_success_title'     => 'Erfolg',
            'flash_error_title'       => 'Fehler',
            'validation_failed_title' => 'Verarbeitung fehlgeschlagen',
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
            'create'  => 'Reiseplan konnte nicht erstellt werden.',
            'update'  => 'Reiseplan konnte nicht aktualisiert werden.',
            'toggle'  => 'Status des Reiseplans konnte nicht geändert werden.',
            'delete'  => 'Reiseplan konnte nicht gelöscht werden.',
            'assign'  => 'Elemente konnten nicht zugewiesen werden.',
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
            'page_heading' => 'Sprachenverwaltung',
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

            'processing'   => 'Wird verarbeitet…',
            'saving'       => 'Wird gespeichert…',
            'activating'   => 'Wird aktiviert…',
            'deactivating' => 'Wird deaktiviert…',
            'deleting'     => 'Wird gelöscht…',

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
            'save'   => 'Speichern nicht möglich',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Ungültiger Name',
                'required' => 'Der Sprachname ist erforderlich.',
                'string'   => 'Das :attribute muss eine Zeichenkette sein.',
                'max'      => 'Das :attribute darf nicht länger als :max Zeichen sein.',
                'unique'   => 'Eine Sprache mit diesem Namen existiert bereits.',
            ],
        ],
    ],

    // =========================================================
    // [07] TOUR
    // =========================================================
    'tour' => [
        'fields' => [
            'id'           => 'ID',
            'name'         => 'Name',
            'overview'     => 'Übersicht',
            'amenities'    => 'Ausstattungen',
            'exclusions'   => 'Ausschlüsse',
            'itinerary'    => 'Reiseplan',
            'languages'    => 'Sprachen',
            'schedules'    => 'Zeitpläne',
            'adult_price'  => 'Erwachsenenpreis',
            'kid_price'    => 'Kinderpreis',
            'length_hours' => 'Dauer (Std.)',
            'max_capacity' => 'Max. Kapazität',
            'type'         => 'Typ',
            'viator_code'  => 'Viator-Code',
            'status'       => 'Status',
            'actions'      => 'Aktionen',
        ],
        'table' => [
            'id'           => 'ID',
            'name'         => 'Name',
            'overview'     => 'Übersicht',
            'amenities'    => 'Ausstattungen',
            'exclusions'   => 'Ausschlüsse',
            'itinerary'    => 'Reiseplan',
            'languages'    => 'Sprachen',
            'schedules'    => 'Zeitpläne',
            'adult_price'  => 'Erwachsenenpreis',
            'kid_price'    => 'Kinderpreis',
            'length_hours' => 'Dauer (Std.)',
            'max_capacity' => 'Max. Kapazität',
            'type'         => 'Typ',
            'viator_code'  => 'Viator-Code',
            'status'       => 'Status',
            'actions'      => 'Aktionen',
        ],
        'status' => [
            'active'   => 'Aktiv',
            'inactive' => 'Inaktiv',
        ],
        'ui' => [
            'page_title'   => 'Touren',
            'page_heading' => 'Tourverwaltung',

            'font_decrease_title' => 'Schrift verkleinern',
            'font_increase_title' => 'Schrift vergrößern',

            'see_more' => 'Mehr anzeigen',
            'see_less' => 'Weniger anzeigen',

            'none' => [
                'amenities'       => 'Keine Ausstattungen',
                'exclusions'      => 'Keine Ausschlüsse',
                'languages'       => 'Keine Sprachen',
                'itinerary'       => 'Kein Reiseplan',
                'itinerary_items' => '(Keine Einträge)',
                'schedules'       => 'Keine Zeitpläne',
            ],

            'toggle_on'         => 'Aktivieren',
            'toggle_off'        => 'Deaktivieren',
            'toggle_on_title'   => 'Möchten Sie diese Tour aktivieren?',
            'toggle_off_title'  => 'Möchten Sie diese Tour deaktivieren?',
            'toggle_on_button'  => 'Ja, aktivieren',
            'toggle_off_button' => 'Ja, deaktivieren',

            'confirm_title'   => 'Bestätigung',
            'confirm_text'    => 'Aktion bestätigen?',
            'yes_confirm'     => 'Ja, bestätigen',
            'cancel'          => 'Abbrechen',

            'load_more'       => 'Mehr laden',
            'loading'         => 'Wird geladen…',
            'load_more_error' => 'Weitere Inhalte konnten nicht geladen werden',
        ],
        'success' => [
            'created'     => 'Tour erfolgreich erstellt.',
            'updated'     => 'Tour erfolgreich aktualisiert.',
            'activated'   => 'Tour erfolgreich aktiviert.',
            'deactivated' => 'Tour erfolgreich deaktiviert.',
        ],
        'error' => [
            'create' => 'Beim Erstellen der Tour ist ein Problem aufgetreten.',
            'update' => 'Beim Aktualisieren der Tour ist ein Problem aufgetreten.',
            'toggle' => 'Beim Ändern des Tourstatus ist ein Problem aufgetreten.',
        ],
    ],
];
