<?php

/*************************************************************
 *  ÜBERSETZUNGSMODUL: TOUREN
 *  Datei: resources/lang/de/m_tours.php
 *
 *  Index (Abschnitt und Startzeile)
 *  [01] COMMON           -> Zeile 23
 *  [02] AMENITY          -> Zeile 31
 *  [03] SCHEDULE         -> Zeile 106
 *  [04] ITINERARY_ITEM   -> Zeile 218
 *  [05] ITINERARY        -> Zeile 288
 *  [06] LANGUAGE         -> Zeile 364
 *  [07] TOUR             -> Zeile 453
 *  [08] IMAGES           -> Zeile 578
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
            'page_title'    => 'Annehmlichkeiten',
            'page_heading'  => 'Verwaltung der Annehmlichkeiten',
            'list_title'    => 'Liste der Annehmlichkeiten',

            'add'            => 'Annehmlichkeit hinzufügen',
            'create_title'   => 'Annehmlichkeit registrieren',
            'edit_title'     => 'Annehmlichkeit bearbeiten',
            'save'           => 'Speichern',
            'update'         => 'Aktualisieren',
            'cancel'         => 'Abbrechen',
            'close'          => 'Schließen',
            'state'          => 'Status',
            'actions'        => 'Aktionen',
            'delete_forever' => 'Dauerhaft löschen',

            'processing' => 'Verarbeitung...',
            'applying'   => 'Wird angewendet...',
            'deleting'   => 'Wird gelöscht...',

            'toggle_on'  => 'Annehmlichkeit aktivieren',
            'toggle_off' => 'Annehmlichkeit deaktivieren',

            'toggle_confirm_on_title'  => 'Annehmlichkeit aktivieren?',
            'toggle_confirm_off_title' => 'Annehmlichkeit deaktivieren?',
            'toggle_confirm_on_html'   => 'Die Annehmlichkeit <b>:label</b> wird aktiv sein.',
            'toggle_confirm_off_html'  => 'Die Annehmlichkeit <b>:label</b> wird inaktiv sein.',

            'delete_confirm_title' => 'Dauerhaft löschen?',
            'delete_confirm_html'  => '<b>:label</b> wird gelöscht und kann nicht rückgängig gemacht werden.',

            'yes_continue' => 'Ja, fortfahren',
            'yes_delete'   => 'Ja, löschen',

            'item_this' => 'diese Annehmlichkeit',
        ],

        'success' => [
            'created'     => 'Annehmlichkeit erfolgreich erstellt.',
            'updated'     => 'Annehmlichkeit erfolgreich aktualisiert.',
            'activated'   => 'Annehmlichkeit erfolgreich aktiviert.',
            'deactivated' => 'Annehmlichkeit erfolgreich deaktiviert.',
            'deleted'     => 'Annehmlichkeit dauerhaft gelöscht.',
        ],

        'error' => [
            'create' => 'Annehmlichkeit konnte nicht erstellt werden.',
            'update' => 'Annehmlichkeit konnte nicht aktualisiert werden.',
            'toggle' => 'Status der Annehmlichkeit konnte nicht geändert werden.',
            'delete' => 'Annehmlichkeit konnte nicht gelöscht werden.',
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
            'page_heading'      => 'Zeitplanverwaltung',

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

            'time_range'        => 'Zeitspanne',
            'state'             => 'Status',
            'actions'           => 'Aktionen',
            'schedule_state'    => 'Zeitplan',
            'assignment_state'  => 'Zuweisung',
            'no_general'        => 'Keine allgemeinen Zeitpläne.',
            'no_tour_schedules' => 'Diese Tour hat noch keine Zeitpläne.',
            'no_label'          => 'Ohne Bezeichnung',
            'assigned_count'    => 'zugewiesene(r) Zeitplan/-pläne',

            'toggle_global_title'     => 'Aktivieren/Deaktivieren (global)',
            'toggle_global_on_title'  => 'Zeitplan aktivieren (global)?',
            'toggle_global_off_title' => 'Zeitplan deaktivieren (global)?',
            'toggle_global_on_html'   => '<b>:label</b> wird für alle Touren aktiviert.',
            'toggle_global_off_html'  => '<b>:label</b> wird für alle Touren deaktiviert.',

            'toggle_on_tour'          => 'Auf dieser Tour aktivieren',
            'toggle_off_tour'         => 'Auf dieser Tour deaktivieren',
            'toggle_assign_on_title'  => 'Auf dieser Tour aktivieren?',
            'toggle_assign_off_title' => 'Auf dieser Tour deaktivieren?',
            'toggle_assign_on_html'   => 'Die Zuweisung wird für <b>:tour</b> <b>aktiv</b> sein.',
            'toggle_assign_off_html'  => 'Die Zuweisung wird für <b>:tour</b> <b>inaktiv</b> sein.',

            'detach_from_tour'     => 'Von Tour entfernen',
            'detach_confirm_title' => 'Von Tour entfernen?',
            'detach_confirm_html'  => 'Der Zeitplan wird von <b>:tour</b> <b>gelöst</b>.',

            'delete_forever'       => 'Löschen (global)',
            'delete_confirm_title' => 'Dauerhaft löschen?',
            'delete_confirm_html'  => '<b>:label</b> (global) wird gelöscht und kann nicht rückgängig gemacht werden.',

            'yes_continue' => 'Ja, fortfahren',
            'yes_delete'   => 'Ja, löschen',
            'yes_detach'   => 'Ja, entfernen',

            'this_schedule' => 'dieser Zeitplan',
            'this_tour'     => 'diese Tour',

            'processing'     => 'Verarbeitung...',
            'applying'       => 'Wird angewendet...',
            'deleting'       => 'Wird gelöscht...',
            'removing'       => 'Wird entfernt...',
            'saving_changes' => 'Änderungen werden gespeichert...',
            'save'           => 'Speichern',
            'save_changes'   => 'Änderungen speichern',
            'cancel'         => 'Abbrechen',

            'missing_fields_title' => 'Fehlende Daten',
            'missing_fields_text'  => 'Prüfen Sie die Pflichtfelder (Beginn, Ende und Kapazität).',
            'could_not_save'       => 'Speichern nicht möglich',
        ],

        'success' => [
            'created'                => 'Zeitplan erfolgreich erstellt.',
            'updated'                => 'Zeitplan erfolgreich aktualisiert.',
            'activated_global'       => 'Zeitplan erfolgreich aktiviert (global).',
            'deactivated_global'     => 'Zeitplan erfolgreich deaktiviert (global).',
            'attached'               => 'Zeitplan der Tour zugewiesen.',
            'detached'               => 'Zeitplan von der Tour entfernt.',
            'assignment_activated'   => 'Zuweisung für diese Tour aktiviert.',
            'assignment_deactivated' => 'Zuweisung für diese Tour deaktiviert.',
            'deleted'                => 'Zeitplan erfolgreich gelöscht.',
        ],

        'error' => [
            'create'               => 'Beim Erstellen des Zeitplans ist ein Problem aufgetreten.',
            'update'               => 'Beim Aktualisieren des Zeitplans ist ein Problem aufgetreten.',
            'toggle'               => 'Globaler Status des Zeitplans konnte nicht geändert werden.',
            'attach'               => 'Zeitplan konnte der Tour nicht zugewiesen werden.',
            'detach'               => 'Zeitplan konnte von der Tour nicht gelöst werden.',
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

            'processing' => 'Verarbeitung...',
            'applying'   => 'Wird angewendet...',
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
            'page_title'    => 'Reisepläne & Elemente',
            'page_heading'  => 'Verwaltung von Reiseplänen und Elementen',
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
            'toggle_confirm_on_html'   => 'Reiseplan <b>:label</b> wird <b>aktiv</b> sein.',
            'toggle_confirm_off_html'  => 'Reiseplan <b>:label</b> wird <b>inaktiv</b> sein.',
            'yes_continue' => 'Ja, fortfahren',

            'assign_title'          => 'Elemente :name zuweisen',
            'drag_hint'             => 'Ziehen und ablegen, um die Reihenfolge festzulegen.',
            'drag_handle'           => 'Ziehen zum Neuordnen',
            'select_one_title'      => 'Mindestens ein Element auswählen',
            'select_one_text'       => 'Bitte wählen Sie mindestens ein Element aus, um fortzufahren.',
            'assign_confirm_title'  => 'Ausgewählte Elemente zuweisen?',
            'assign_confirm_button' => 'Ja, zuweisen',
            'assigning'             => 'Wird zugewiesen...',

            'no_items_assigned'       => 'Diesem Reiseplan sind keine Elemente zugewiesen.',
            'itinerary_this'          => 'dieser Reiseplan',
            'processing'              => 'Verarbeitung...',
            'saving'                  => 'Speichern...',
            'activating'              => 'Aktivierung...',
            'deactivating'            => 'Deaktivierung...',
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
            'items_assigned' => 'Elemente erfolgreich zugewiesen.',
        ],

        'error' => [
            'create'  => 'Reiseplan konnte nicht erstellt werden.',
            'update'  => 'Reiseplan konnte nicht aktualisiert werden.',
            'toggle'  => 'Reiseplanstatus konnte nicht geändert werden.',
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

            'processing'   => 'Verarbeitung...',
            'saving'       => 'Wird gespeichert...',
            'activating'   => 'Wird aktiviert...',
            'deactivating' => 'Wird deaktiviert...',
            'deleting'     => 'Wird gelöscht...',

            'toggle_on'  => 'Sprache aktivieren',
            'toggle_off' => 'Sprache deaktivieren',
            'toggle_confirm_on_title'  => 'Sprache aktivieren?',
            'toggle_confirm_off_title' => 'Sprache deaktivieren?',
            'toggle_confirm_on_html'   => 'Sprache <b>:label</b> wird <b>aktiv</b> sein.',
            'toggle_confirm_off_html'  => 'Sprache <b>:label</b> wird <b>inaktiv</b> sein.',
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
            'save'   => 'Speichern fehlgeschlagen',
        ],

        'validation' => [
            'name' => [
                'title'    => 'Ungültiger Name',
                'required' => 'Der Sprachname ist erforderlich.',
                'string'   => 'Das :attribute muss eine Zeichenkette sein.',
                'max'      => 'Das :attribute darf :max Zeichen nicht überschreiten.',
                'unique'   => 'Eine Sprache mit diesem Namen existiert bereits.',
            ],
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
        'overview'      => 'Kurzbeschreibung',
        'amenities'     => 'Annehmlichkeiten',
        'exclusions'    => 'Ausschlüsse',
        'itinerary'     => 'Reiseroute',
        'languages'     => 'Sprachen',
        'schedules'     => 'Zeitpläne',
        'adult_price'   => 'Preis (Erwachsene)',
        'kid_price'     => 'Preis (Kinder)',
        'length_hours'  => 'Dauer (Stunden)',
        'max_capacity'  => 'Max. Kapazität',
        'type'          => 'Tourtyp',
        'viator_code'   => 'Viator-Code',
        'status'        => 'Status',
        'actions'       => 'Aktionen',
    ],

    'table' => [
        'id'            => 'ID',
        'name'          => 'Name',
        'overview'      => 'Kurzbeschreibung',
        'amenities'     => 'Annehmlichkeiten',
        'exclusions'    => 'Ausschlüsse',
        'itinerary'     => 'Reiseroute',
        'languages'     => 'Sprachen',
        'schedules'     => 'Zeitpläne',
        'adult_price'   => 'Preis (Erw.)',
        'kid_price'     => 'Preis (Kinder)',
        'length_hours'  => 'Dauer (Std.)',
        'max_capacity'  => 'Max. Kap.',
        'type'          => 'Typ',
        'viator_code'   => 'Viator-Code',
        'status'        => 'Status',
        'actions'       => 'Aktionen',
        'slug'          => 'URL',
    ],

    'status' => [
        'active'   => 'Aktiv',
        'inactive' => 'Inaktiv',
    ],

    'success' => [
        'created'     => 'Tour erfolgreich erstellt.',
        'updated'     => 'Tour erfolgreich aktualisiert.',
        'deleted'     => 'Tour gelöscht.',
        'toggled'     => 'Tourstatus aktualisiert.',
        'activated'   => 'Tour erfolgreich aktiviert.',
        'deactivated' => 'Tour erfolgreich deaktiviert.',
    ],

    'error' => [
        'create'    => 'Beim Erstellen der Tour ist ein Problem aufgetreten.',
        'update'    => 'Beim Aktualisieren der Tour ist ein Problem aufgetreten.',
        'delete'    => 'Beim Löschen der Tour ist ein Problem aufgetreten.',
        'toggle'    => 'Beim Ändern des Tourstatus ist ein Problem aufgetreten.',
        'not_found' => 'Die Tour existiert nicht.',
    ],

    'ui' => [
        'page_title'       => 'Tourverwaltung',
        'page_heading'     => 'Tourverwaltung',
        'create_title'     => 'Tour erstellen',
        'edit_title'       => 'Tour bearbeiten',
        'delete_title'     => 'Tour löschen',
        'cancel'           => 'Abbrechen',
        'save'             => 'Speichern',
        'update'           => 'Aktualisieren',
        'delete_confirm'   => 'Diese Tour löschen?',
        'toggle_on'        => 'Aktivieren',
        'toggle_off'       => 'Deaktivieren',
        'toggle_on_title'  => 'Tour aktivieren?',
        'toggle_off_title' => 'Tour deaktivieren?',
        'toggle_on_button' => 'Ja, aktivieren',
        'toggle_off_button'=> 'Ja, deaktivieren',
        'see_more'         => 'Mehr anzeigen',
        'see_less'         => 'Weniger anzeigen',
        'load_more'        => 'Mehr laden',
        'loading'          => 'Wird geladen...',
        'load_more_error'  => 'Weitere Touren konnten nicht geladen werden.',
        'confirm_title'    => 'Bestätigung',
        'confirm_text'     => 'Möchten Sie diese Aktion bestätigen?',
        'yes_confirm'      => 'Ja, bestätigen',
        'no_confirm'       => 'Nein, abbrechen',
        'add_tour'         => 'Tour hinzufügen',
        'edit_tour'        => 'Tour bearbeiten',
        'delete_tour'      => 'Tour löschen',
        'toggle_tour'      => 'Tour aktivieren/deaktivieren',
        'view_cart'        => 'Warenkorb ansehen',
        'add_to_cart'      => 'Zum Warenkorb hinzufügen',

        'available_languages'    => 'Verfügbare Sprachen',
        'default_capacity'       => 'Standardkapazität',
        'create_new_schedules'   => 'Neue Zeitpläne erstellen',
        'multiple_hint_ctrl_cmd' => 'STRG/CMD gedrückt halten, um mehrere auszuwählen',
        'use_existing_schedules' => 'Vorhandene Zeitpläne verwenden',
        'add_schedule'           => 'Zeitplan hinzufügen',
        'schedules_title'        => 'Tourpläne',
        'amenities_included'     => 'Inklusive Annehmlichkeiten',
        'amenities_excluded'     => 'Nicht enthaltene Annehmlichkeiten',
        'color'                  => 'Tourfarbe',
        'remove'                 => 'Entfernen',
        'choose_itinerary'       => 'Reiseroute wählen',
        'select_type'            => 'Typ auswählen',
        'empty_means_default'    => 'Standard',

        'none' => [
            'amenities'       => 'Keine Annehmlichkeiten',
            'exclusions'      => 'Keine Ausschlüsse',
            'itinerary'       => 'Keine Reiseroute',
            'itinerary_items' => 'Keine Einträge',
            'languages'       => 'Keine Sprachen',
            'schedules'       => 'Keine Zeitpläne',
        ],
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
    'upload_truncated'    => 'Einige Dateien wurden aufgrund des Tour-Limits übersprungen.',
    'done'                => 'Fertig',
    'notice'              => 'Hinweis',
    'saved'               => 'Gespeichert',
    'caption_updated'     => 'Bildunterschrift erfolgreich aktualisiert.',
    'deleted'             => 'Gelöscht',
    'image_removed'       => 'Bild erfolgreich entfernt.',
    'invalid_order'       => 'Ungültige Reihenfolge.',
    'nothing_to_reorder'  => 'Nichts zu sortieren.',
    'order_saved'         => 'Reihenfolge gespeichert.',
    'cover_updated_title' => 'Titelbild aktualisiert',
    'cover_updated_text'  => 'Dieses Bild ist jetzt das Titelbild.',
    'deleting'            => 'Wird gelöscht...',

    'ui' => [
        'page_title_pick'     => 'Tour-Bilder — Tour auswählen',
        'page_heading'        => 'Tour-Bilder',
        'choose_tour'         => 'Tour auswählen',
        'search_placeholder'  => 'Suche nach ID oder Name…',
        'search_button'       => 'Suchen',
        'no_results'          => 'Keine Touren gefunden.',
        'manage_images'       => 'Bilder verwalten',
        'cover_alt'           => 'Titelbild',
        'images_label'        => 'Bilder',
        'upload_btn'          => 'Hochladen',
        'caption_placeholder' => 'Bildunterschrift (optional)',
        'set_cover_btn'       => 'Als Titelbild festlegen',
        'no_images'           => 'Noch keine Bilder für diese Tour.',
        'delete_btn'          => 'Löschen',
        'show_btn'            => 'Anzeigen',
        'close_btn'           => 'Schließen',
        'preview_title'      => 'Bildvorschau',


        'error_title'         => 'Fehler',
        'warning_title'       => 'Warnung',
        'success_title'       => 'Erfolg',
        'cancel_btn'          => 'Abbrechen',
        'confirm_delete_title'=> 'Dieses Bild löschen?',
        'confirm_delete_text' => 'Diese Aktion kann nicht rückgängig gemacht werden.',
    ],

    'errors' => [
        'validation'     => 'Die übermittelten Daten sind ungültig.',
        'upload_generic' => 'Einige Bilder konnten nicht hochgeladen werden.',
        'update_caption' => 'Die Bildunterschrift konnte nicht aktualisiert werden.',
        'delete'         => 'Das Bild konnte nicht gelöscht werden.',
        'reorder'        => 'Die Reihenfolge konnte nicht gespeichert werden.',
        'set_cover'      => 'Das Titelbild konnte nicht festgelegt werden.',
        'load_list'      => 'Die Liste konnte nicht geladen werden.',
        'too_large'      => 'Die Datei überschreitet die maximal zulässige Größe. Bitte versuchen Sie es mit einem kleineren Bild.',
    ],
],

];
