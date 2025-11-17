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
 *  [08] IMAGES           -> Zeile 579
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
        'na'                   => 'Nicht konfiguriert',
        'create'               => 'Erstellen',
        'previous'             => 'Zurück',
        'info'                 => 'Information',
        'close'                => 'Schließen',
        'save'                 => 'Speichern',
        'required'             => 'Dieses Feld ist erforderlich.',
        'add'                  => 'Hinzufügen',
        'translating'          => 'Übersetze...',
        'error_translating'    => 'Der Text konnte nicht übersetzt werden.',
        'confirm'              => 'Bestätigen',
        'yes'                  => 'Ja',
        'form_errors_title'    => 'Bitte korrigiere die folgenden Fehler:',
        'delete'               => 'Löschen',
        'delete_all'           => 'Alle löschen',
        'actions'              => 'Aktionen',
        'updated_at'           => 'Letzte Aktualisierung',
        'not_set'              => 'Nicht angegeben',
        'error_deleting'       => 'Beim Löschen ist ein Fehler aufgetreten. Bitte versuche es erneut.',
        'error_saving'         => 'Beim Speichern ist ein Fehler aufgetreten. Bitte versuche es erneut.',
        'crud_go_to_index'     => ':element verwalten',
        'validation_title'     => 'Es liegen Validierungsfehler vor',
        'ok'                   => 'OK',
    ],

    // =========================================================
    // [02] AMENITY
    // =========================================================
    'amenity' => [
        'singular' => 'Ausstattung',
        'plural'   => 'Ausstattungen',

        'fields' => [
            'name' => 'Name',
            'icon' => 'Icon (FontAwesome)',
        ],

        'status' => [
            'active'   => 'Aktiv',
            'inactive' => 'Inaktiv',
        ],

        'ui' => [
            'page_title'    => 'Ausstattungen',
            'page_heading'  => 'Verwaltung der Ausstattungen',
            'list_title'    => 'Liste der Ausstattungen',

            'add'           => 'Ausstattung hinzufügen',
            'create_title'  => 'Ausstattung registrieren',
            'edit_title'    => 'Ausstattung bearbeiten',
            'save'          => 'Speichern',
            'update'        => 'Aktualisieren',
            'cancel'        => 'Abbrechen',
            'close'         => 'Schließen',
            'state'         => 'Status',
            'actions'       => 'Aktionen',
            'delete_forever'=> 'Endgültig löschen',

            'processing'    => 'Verarbeite...',
            'applying'      => 'Wende an...',
            'deleting'      => 'Lösche...',

            'toggle_on'     => 'Ausstattung aktivieren',
            'toggle_off'    => 'Ausstattung deaktivieren',

            'toggle_confirm_on_title'  => 'Ausstattung aktivieren?',
            'toggle_confirm_off_title' => 'Ausstattung deaktivieren?',
            'toggle_confirm_on_html'   => 'Die Ausstattung <b>:label</b> wird aktiv sein.',
            'toggle_confirm_off_html'  => 'Die Ausstattung <b>:label</b> wird inaktiv sein.',

            'delete_confirm_title' => 'Endgültig löschen?',
            'delete_confirm_html'  => '<b>:label</b> wird gelöscht und kann nicht rückgängig gemacht werden.',

            'yes_continue' => 'Ja, fortfahren',
            'yes_delete'   => 'Ja, löschen',

            'item_this'    => 'diese Ausstattung',
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
            'included_required' => 'Du musst mindestens eine enthaltene Ausstattung auswählen.',
            'name' => [
                'title'    => 'Ungültiger Name',
                'required' => 'Das Feld :attribute ist erforderlich.',
                'string'   => 'Das Feld :attribute muss eine Zeichenkette sein.',
                'max'      => 'Das Feld :attribute darf :max Zeichen nicht überschreiten.',
            ],
        ],

        'hints' => [
            'fontawesome' => 'Verwende FontAwesome-Klassen, z. B.: "fas fa-check".',
        ],

        'quick_create' => [
            'button'           => 'Neue Ausstattung',
            'title'            => 'Schnell-Ausstattung erstellen',
            'name_label'       => 'Name der Ausstattung',
            'icon_label'       => 'Icon (optional)',
            'icon_placeholder' => 'Z. B.: fas fa-utensils',
            'icon_help'        => 'Verwende eine FontAwesome-Icon-Klasse oder lasse das Feld leer.',
            'save'             => 'Ausstattung speichern',
            'cancel'           => 'Abbrechen',
            'saving'           => 'Speichere...',
            'error_generic'    => 'Die Ausstattung konnte nicht erstellt werden. Bitte versuche es erneut.',
            'go_to_index'      => 'Alle anzeigen',
            'go_to_index_title'=> 'Zur vollständigen Liste der Ausstattungen gehen',
            'success_title'    => 'Ausstattung erstellt',
            'success_text'     => 'Die Ausstattung wurde zur Tourliste hinzugefügt.',
            'error_title'      => 'Fehler beim Erstellen der Ausstattung',
            'error_duplicate'  => 'Es existiert bereits eine Ausstattung mit diesem Namen.',
        ],
    ],

    // =========================================================
    // [03] SCHEDULE
    // =========================================================
    'schedule' => [
        'plural'   => 'Zeiten',
        'singular' => 'Zeitplan',

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
            'page_title'        => 'Tour-Zeiten',
            'page_heading'      => 'Verwaltung der Zeiten',

            'general_title'     => 'Allgemeine Zeiten',
            'new_schedule'      => 'Neuer Zeitplan',
            'new_general_title' => 'Neuer allgemeiner Zeitplan',
            'new'               => 'Neu',
            'edit_schedule'     => 'Zeitplan bearbeiten',
            'edit_global'       => 'Bearbeiten (global)',

            'assign_existing'    => 'Bestehenden zuweisen',
            'assign_to_tour'     => 'Zeitplan ":tour" zuweisen',
            'select_schedule'    => 'Zeitplan auswählen',
            'choose'             => 'Auswählen',
            'assign'             => 'Zuweisen',
            'new_for_tour_title' => 'Neuer Zeitplan für ":tour"',

            'time_range'        => 'Zeit',
            'state'             => 'Status',
            'actions'           => 'Aktionen',
            'schedule_state'    => 'Zeitplan',
            'assignment_state'  => 'Zuweisung',
            'no_general'        => 'Es gibt keine allgemeinen Zeiten.',
            'no_tour_schedules' => 'Diese Tour hat noch keine Zeiten.',
            'no_label'          => 'Ohne Bezeichnung',
            'assigned_count'    => 'zugewiesene(r) Zeitplan/Zeiten',

            'toggle_global_title'      => 'Aktivieren/Deaktivieren (global)',
            'toggle_global_on_title'   => 'Zeitplan (global) aktivieren?',
            'toggle_global_off_title'  => 'Zeitplan (global) deaktivieren?',
            'toggle_global_on_html'    => '<b>:label</b> wird für alle Touren aktiviert.',
            'toggle_global_off_html'   => '<b>:label</b> wird für alle Touren deaktiviert.',

            'toggle_on_tour'           => 'In dieser Tour aktivieren',
            'toggle_off_tour'          => 'In dieser Tour deaktivieren',
            'toggle_assign_on_title'   => 'In dieser Tour aktivieren?',
            'toggle_assign_off_title'  => 'In dieser Tour deaktivieren?',
            'toggle_assign_on_html'    => 'Die Zuweisung wird für <b>:tour</b> <b>aktiv</b> sein.',
            'toggle_assign_off_html'   => 'Die Zuweisung wird für <b>:tour</b> <b>inaktiv</b> sein.',

            'detach_from_tour'     => 'Von der Tour entfernen',
            'detach_confirm_title' => 'Von der Tour entfernen?',
            'detach_confirm_html'  => 'Der Zeitplan wird von <b>:tour</b> <b>entfernt</b>.',

            'delete_forever'       => 'Löschen (global)',
            'delete_confirm_title' => 'Endgültig löschen?',
            'delete_confirm_html'  => '<b>:label</b> wird (global) gelöscht und kann nicht rückgängig gemacht werden.',

            'yes_continue' => 'Ja, fortfahren',
            'yes_delete'   => 'Ja, löschen',
            'yes_detach'   => 'Ja, entfernen',

            'this_schedule' => 'dieser Zeitplan',
            'this_tour'     => 'diese Tour',

            'processing'     => 'Verarbeite...',
            'applying'       => 'Wende an...',
            'deleting'       => 'Lösche...',
            'removing'       => 'Entferne...',
            'saving_changes' => 'Änderungen werden gespeichert...',
            'save'           => 'Speichern',
            'save_changes'   => 'Änderungen speichern',
            'cancel'         => 'Abbrechen',

            'missing_fields_title' => 'Daten fehlen',
            'missing_fields_text'  => 'Bitte prüfe die Pflichtfelder (Beginn, Ende und Kapazität).',
            'could_not_save'       => 'Konnte nicht gespeichert werden',

            'base_capacity_tour'               => 'Grundkapazität der Tour:',
            'capacity_not_defined'             => 'Nicht definiert',
            'capacity_optional'                => 'Kapazität (optional)',
            'capacity_placeholder_with_value'  => 'Z. B.: :capacity',
            'capacity_placeholder_generic'     => 'Tour-Kapazität verwenden',
            'capacity_hint_with_value'         => 'Leer lassen → :capacity',
            'capacity_hint_generic'            => 'Leer lassen → allgemeine Tour-Kapazität',
            'tip_label'                        => 'Tipp:',
            'capacity_tip'                     => 'Du kannst das Kapazitätsfeld leer lassen, damit das System die allgemeine Tour-Kapazität (:capacity) verwendet.',
            'new_schedule_for_tour'            => 'Neuer Zeitplan',
            'modal_new_for_tour_title'         => 'Zeitplan für :tour erstellen',
            'modal_save'                       => 'Zeitplan speichern',
            'modal_cancel'                     => 'Abbrechen',
            'capacity_modal_info_with_value'   => 'Die Grundkapazität der Tour beträgt :capacity. Wenn du die Kapazität leer lässt, wird dieser Wert verwendet.',
            'capacity_modal_info_generic'      => 'Wenn du die Kapazität leer lässt, wird die allgemeine Tour-Kapazität verwendet, sofern sie definiert ist.',
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
            'detach'               => 'Der Zeitplan konnte nicht von der Tour entfernt werden.',
            'assignment_toggle'    => 'Der Status der Zuweisung konnte nicht geändert werden.',
            'not_assigned_to_tour' => 'Der Zeitplan ist dieser Tour nicht zugewiesen.',
            'delete'               => 'Beim Löschen des Zeitplans ist ein Problem aufgetreten.',
        ],

        'placeholders' => [
            'morning' => 'Z. B.: Morgen',
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
            'list_title'          => 'Reiseplan-Elemente',
            'add_item'            => 'Element hinzufügen',
            'register_item'       => 'Element registrieren',
            'edit_item'           => 'Element bearbeiten',
            'save'                => 'Speichern',
            'update'              => 'Aktualisieren',
            'cancel'              => 'Abbrechen',
            'state'               => 'Status',
            'actions'             => 'Aktionen',
            'see_more'            => 'Mehr anzeigen',
            'see_less'            => 'Weniger anzeigen',
            'assigned_items'      => 'Dem Reiseplan zugewiesene Elemente',
            'drag_to_order'       => 'Ziehe die Elemente, um die Reihenfolge festzulegen.',
            'pool_hint'           => 'Markiere verfügbare Elemente, die du in diesen Reiseplan aufnehmen möchtest.',
            'register_item_hint'  => 'Registriere neue Elemente, wenn du zusätzliche Schritte brauchst, die noch nicht existieren.',

            'toggle_on'           => 'Element aktivieren',
            'toggle_off'          => 'Element deaktivieren',

            'delete_forever'       => 'Endgültig löschen',
            'delete_confirm_title' => 'Endgültig löschen?',
            'delete_confirm_html'  => '<b>:label</b> wird gelöscht und kann nicht rückgängig gemacht werden.',
            'yes_delete'           => 'Ja, löschen',
            'item_this'            => 'dieses Element',

            'processing' => 'Verarbeite...',
            'applying'   => 'Wende an...',
            'deleting'   => 'Lösche...',
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
        'plural'   => 'Reisepläne',
        'singular' => 'Reiseplan',

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
            'page_title'          => 'Reisepläne und Elemente',
            'page_heading'        => 'Reisepläne und Elementverwaltung',
            'new_itinerary'       => 'Neuer Reiseplan',
            'select_or_create_hint' => 'Wähle einen bestehenden Reiseplan oder erstelle einen neuen für diese Tour.',
            'save_changes'          => 'Speichere den Reiseplan, um die Änderungen auf die Tour anzuwenden.',
            'select_existing'       => 'Bestehenden Reiseplan auswählen',
            'create_new'            => 'Neuen Reiseplan erstellen',
            'add_item'              => 'Element hinzufügen',
            'min_one_item'          => 'Der Reiseplan muss mindestens ein Element enthalten',

            'assign'        => 'Zuweisen',
            'edit'          => 'Bearbeiten',
            'save'          => 'Speichern',
            'cancel'        => 'Abbrechen',
            'close'         => 'Schließen',
            'create_title'  => 'Neuen Reiseplan erstellen',
            'create_button' => 'Erstellen',

            'toggle_on'                => 'Reiseplan aktivieren',
            'toggle_off'               => 'Reiseplan deaktivieren',
            'toggle_confirm_on_title'  => 'Reiseplan aktivieren?',
            'toggle_confirm_off_title' => 'Reiseplan deaktivieren?',
            'toggle_confirm_on_html'   => 'Der Reiseplan <b>:label</b> wird <b>aktiv</b> sein.',
            'toggle_confirm_off_html'  => 'Der Reiseplan <b>:label</b> wird <b>inaktiv</b> sein.',
            'yes_continue'             => 'Ja, fortfahren',

            'assign_title'          => 'Elemente :name zuweisen',
            'drag_hint'             => 'Ziehe und lasse Elemente fallen, um die Reihenfolge festzulegen.',
            'drag_handle'           => 'Zum Neuordnen ziehen',
            'select_one_title'      => 'Du musst mindestens ein Element auswählen',
            'select_one_text'       => 'Bitte wähle mindestens ein Element aus, um fortzufahren.',
            'assign_confirm_title'  => 'Ausgewählte Elemente zuweisen?',
            'assign_confirm_button' => 'Ja, zuweisen',
            'assigning'             => 'Weise zu...',

            'no_items_assigned'       => 'Diesem Reiseplan sind keine Elemente zugewiesen.',
            'itinerary_this'          => 'dieser Reiseplan',
            'processing'              => 'Verarbeite...',
            'saving'                  => 'Speichere...',
            'activating'              => 'Aktiviere...',
            'deactivating'            => 'Deaktiviere...',
            'applying'                => 'Wende an...',
            'deleting'                => 'Lösche...',
            'flash_success_title'     => 'Erfolg',
            'flash_error_title'       => 'Fehler',
            'validation_failed_title' => 'Konnte nicht verarbeitet werden',
            'go_to_crud'              => 'Zum Modul gehen',
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
                'item'          => 'Element',
                'required'      => 'Du musst mindestens ein Element auswählen.',
                'array'         => 'Das Format der Elemente ist ungültig.',
                'min'           => 'Du musst mindestens ein Element auswählen.',
                'order_integer' => 'Die Reihenfolge muss eine ganze Zahl sein.',
                'order_min'     => 'Die Reihenfolge darf nicht negativ sein.',
                'order_max'     => 'Die Reihenfolge darf 9999 nicht überschreiten.',
            ],
        ],

        'item'  => 'Element',
        'items' => 'Elemente',
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
            'page_heading' => 'Verwaltung der Sprachen',
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

            'processing'   => 'Verarbeite...',
            'saving'       => 'Speichere...',
            'activating'   => 'Aktiviere...',
            'deactivating' => 'Deaktiviere...',
            'deleting'     => 'Lösche...',

            'toggle_on'                => 'Sprache aktivieren',
            'toggle_off'               => 'Sprache deaktivieren',
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
            'iso_639_1' => 'ISO-639-1-Code, zum Beispiel: es, en, fr.',
        ],
    ],

    // =========================================================
    // [07] TOUR
    // =========================================================
    'tour' => [

        'validation' => [
            // Allgemeine Meldungen
            'required' => 'Dieses Feld ist erforderlich.',
            'min'      => 'Dieses Feld muss mindestens :min Zeichen enthalten.',
            'max'      => 'Dieses Feld darf :max Zeichen nicht überschreiten.',
            'number'   => 'Dieses Feld muss eine gültige Zahl sein.',
            'slug'     => 'Der Slug darf nur Kleinbuchstaben, Zahlen und Bindestriche enthalten.',
            'color'    => 'Bitte wähle eine gültige Farbe.',
            'select'   => 'Bitte wähle eine Option.',

            // Feldspezifisch
            'length_in_hours'   => 'Dauer in Stunden (z. B.: 2, 2.5, 4)',
            'max_capacity_help' => 'Maximale Anzahl von Personen pro Tour',

            // Formulare
            'form_error_title'   => 'Achtung!',
            'form_error_message' => 'Bitte korrigiere die Fehler im Formular, bevor du fortfährst.',
            'saving'             => 'Speichere...',

            // Erfolg
            'success'            => 'Erfolg!',
            'tour_type_created'  => 'Tourtyp erfolgreich erstellt.',
            'language_created'   => 'Sprache erfolgreich erstellt.',

            // Fehler
            'tour_type_error'  => 'Fehler beim Erstellen des Tourtyps.',
            'language_error'   => 'Fehler beim Erstellen der Sprache.',
        ],

        'wizard' => [
            // Allgemeine Titel
            'create_new_tour' => 'Neue Tour erstellen',
            'edit_tour'       => 'Tour bearbeiten',
            'step_number'     => 'Schritt :number',
            'edit_step'       => 'Bearbeiten',
            'leave_warning'   => 'Du hast ungespeicherte Änderungen an der Tour. Wenn du jetzt gehst, bleibt der Entwurf in der Datenbank. Bist du sicher, dass du die Seite verlassen möchtest?',
            'cancel_title'    => 'Tour-Konfiguration abbrechen?',
            'cancel_text'     => 'Wenn du diesen Assistenten verlässt, können Änderungen in diesem Schritt verloren gehen.',
            'cancel_confirm'  => 'Ja, Änderungen verwerfen',
            'cancel_cancel'   => 'Nein, weiter bearbeiten',
            'details_validation_text' => 'Bitte prüfe die Pflichtfelder im Detailformular, bevor du fortfährst.',
            'most_recent'     => 'Neueste',
            'last_modified'   => 'Zuletzt bearbeitet',
            'start_fresh'     => 'Neu beginnen',
            'draft_details'   => 'Entwurfsdetails',
            'drafts_found'    => 'Es wurde ein Entwurf gefunden',
            'basic_info'      => 'Details',

            // Schritte des Wizards
            'steps' => [
                'details'   => 'Grundlegende Details',
                'itinerary' => 'Reiseplan',
                'schedules' => 'Zeiten',
                'amenities' => 'Ausstattungen',
                'prices'    => 'Preise',
                'summary'   => 'Zusammenfassung',
            ],

            // Aktionen
            'save_and_continue' => 'Speichern und fortfahren',
            'publish_tour'      => 'Tour veröffentlichen',
            'delete_draft'      => 'Entwurf löschen',
            'ready_to_publish'  => 'Bereit zur Veröffentlichung?',

            // Meldungen
            'details_saved'    => 'Details erfolgreich gespeichert.',
            'itinerary_saved'  => 'Reiseplan erfolgreich gespeichert.',
            'schedules_saved'  => 'Zeiten erfolgreich gespeichert.',
            'amenities_saved'  => 'Ausstattungen erfolgreich gespeichert.',
            'prices_saved'     => 'Preise erfolgreich gespeichert.',
            'published_successfully' => 'Tour erfolgreich veröffentlicht!',
            'draft_cancelled'  => 'Entwurf gelöscht.',

            // Zustände
            'draft_mode'           => 'Entwurfsmodus',
            'draft_explanation'    => 'Diese Tour wird als Entwurf gespeichert, bis du alle Schritte abgeschlossen und sie veröffentlicht hast.',
            'already_published'    => 'Diese Tour wurde bereits veröffentlicht. Verwende den normalen Editor, um sie zu bearbeiten.',
            'cannot_cancel_published' => 'Du kannst eine bereits veröffentlichte Tour nicht über den Assistenten abbrechen.',

            // Bestätigungen
            'confirm_cancel' => 'Bist du sicher, dass du abbrechen und diesen Entwurf löschen möchtest?',

            // Zusammenfassung
            'publish_explanation' => 'Überprüfe alle Informationen, bevor du veröffentlichst. Nach der Veröffentlichung steht die Tour für Buchungen zur Verfügung.',
            'can_edit_later'      => 'Du kannst die Tour nach der Veröffentlichung aus dem Administrationsbereich bearbeiten.',
            'incomplete_warning'  => 'Einige Schritte sind unvollständig. Du kannst trotzdem veröffentlichen, aber es wird empfohlen, alle Informationen zu vervollständigen.',

            // Checkliste
            'checklist'              => 'Checkliste',
            'checklist_details'      => 'Grundlegende Details abgeschlossen',
            'checklist_itinerary'    => 'Reiseplan konfiguriert',
            'checklist_schedules'    => 'Zeiten hinzugefügt',
            'checklist_amenities'    => 'Ausstattungen konfiguriert',
            'checklist_prices'       => 'Preise festgelegt',

            // Hinweise
            'hints' => [
                'status' => 'Der Status kann nach der Veröffentlichung geändert werden.',
            ],

            // Modal für existierende Entwürfe
            'existing_drafts_title'   => 'Du hast unvollendete Tour-Entwürfe!',
            'existing_drafts_message' => 'Wir haben :count Tour im Entwurf gefunden, die du noch nicht abgeschlossen hast.',
            'current_step'            => 'Aktueller Schritt',
            'step'                   => 'Schritt',

            // Aktionen im Modal
            'continue_draft'       => 'Mit diesem Entwurf fortfahren',
            'delete_all_drafts'    => 'Alle Entwürfe löschen',
            'create_new_anyway'    => 'Trotzdem neue Tour erstellen',

            // Zusatzinfo
            'drafts_info' => 'Du kannst einen bestehenden Entwurf weiterbearbeiten, ihn einzeln löschen, alle Entwürfe entfernen oder eine neue Tour erstellen und die aktuellen Entwürfe ignorieren.',

            // Löschbestätigungen
            'confirm_delete_title'        => 'Diesen Entwurf löschen?',
            'confirm_delete_message'      => 'Diese Aktion kann nicht rückgängig gemacht werden. Der Entwurf wird dauerhaft gelöscht:',
            'confirm_delete_all_title'    => 'Alle Entwürfe löschen?',
            'confirm_delete_all_message'  => ':count Entwurf/Entwürfe werden dauerhaft gelöscht. Diese Aktion kann nicht rückgängig gemacht werden.',

            // Erfolgsmeldungen
            'draft_deleted'       => 'Entwurf erfolgreich gelöscht.',
            'all_drafts_deleted'  => ':count Entwurf/Entwürfe erfolgreich gelöscht.',
            'continuing_draft'    => 'Setze deinen Entwurf fort...',

            // Fehlermeldungen
            'not_a_draft' => 'Diese Tour ist kein Entwurf mehr und kann nicht über den Assistenten bearbeitet werden.',
        ],

        'title' => 'Touren',

        'fields' => [
            'id'           => 'ID',
            'name'         => 'Name',
            'details'      => 'Details',
            'price'        => 'Preise',
            'overview'     => 'Übersicht',
            'amenities'    => 'Ausstattungen',
            'exclusions'   => 'Ausschlüsse',
            'itinerary'    => 'Reiseplan',
            'languages'    => 'Sprachen',
            'schedules'    => 'Zeiten',
            'adult_price'  => 'Preis Erwachsene',
            'kid_price'    => 'Preis Kinder',
            'length_hours' => 'Dauer (Stunden)',
            'max_capacity' => 'Max. Kapazität',
            'type'         => 'Tourtyp',
            'viator_code'  => 'Viator-Code',
            'status'       => 'Status',
            'actions'      => 'Aktionen',
            'group_size'   => 'Gruppengröße',
        ],

        'pricing' => [
            'configured_categories' => 'Konfigurierte Kategorien',
            'create_category'       => 'Kategorie erstellen',
            'note_title'            => 'Hinweis:',
            'note_text'             => 'Lege hier die Basispreise für jede Kundenkategorie fest.',
            'manage_detailed_hint'  => 'Für eine detaillierte Verwaltung verwende oben die Schaltfläche „Detaillierte Preise verwalten“.',
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
            'empty_title'           => 'Für diese Tour sind keine Kategorien konfiguriert.',
            'empty_hint'            => 'Verwende das Formular auf der rechten Seite, um Kategorien hinzuzufügen.',

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
            'add_existing_hint'           => 'Füge nur die Kundenkategorien hinzu, die du für diese Tour benötigst.',
            'remove_category'            => 'Kategorie entfernen',
            'category_already_added'     => 'Diese Kategorie ist bereits der Tour hinzugefügt.',
            'no_prices_preview'          => 'Es sind noch keine Preise konfiguriert.',
            'already_added'              => 'Diese Kategorie ist bereits der Tour hinzugefügt.',
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
                'age_to_empty_means_plus' => 'Wenn du das Feld „Alter bis“ leer lässt, wird es als „+“ interpretiert (z. B. 12+).',
                'min_le_max'              => 'Das Minimum muss kleiner oder gleich dem Maximum sein.',
            ],

            'errors' => [
                'min_le_max' => 'Das Minimum muss kleiner oder gleich dem Maximum sein.',
            ],
        ],

        'schedules_form' => [
            'available_title'       => 'Verfügbare Zeiten',
            'select_hint'           => 'Wähle die Zeiten für diese Tour',
            'no_schedules'          => 'Es sind keine Zeiten verfügbar.',
            'create_schedules_link' => 'Zeiten erstellen',

            'create_new_title'      => 'Neue Zeit erstellen',
            'label_placeholder'     => 'Z. B.: Morgen, Nachmittag',
            'create_and_assign'     => 'Diese Zeit erstellen und der Tour zuweisen',

            'info_title'            => 'Informationen',
            'schedules_title'       => 'Zeiten',
            'schedules_text'        => 'Wähle eine oder mehrere Zeiten, zu denen diese Tour verfügbar sein soll.',
            'create_block_title'    => 'Neu erstellen',
            'create_block_text'     => 'Wenn du eine Zeit benötigst, die noch nicht existiert, kannst du sie hier erstellen, indem du das Kontrollkästchen „Diese Zeit erstellen und der Tour zuweisen“ aktivierst.',

            'current_title'         => 'Aktuelle Zeiten',
            'none_assigned'         => 'Keine Zeiten zugewiesen',
        ],

        'summary' => [
            'preview_title'       => 'Tour-Vorschau',
            'preview_text_create' => 'Überprüfe alle Informationen, bevor du die Tour erstellst.',
            'preview_text_update' => 'Überprüfe alle Informationen, bevor du die Tour aktualisierst.',

            'basic_details_title' => 'Grundlegende Details',
            'description_title'   => 'Beschreibung',
            'prices_title'        => 'Preise nach Kategorie',
            'schedules_title'     => 'Zeiten',
            'languages_title'     => 'Sprachen',
            'itinerary_title'     => 'Reiseplan',

            'table' => [
                'category' => 'Kategorie',
                'price'    => 'Preis',
                'min_max'  => 'Min–Max',
                'status'   => 'Status',
            ],

            'not_specified'      => 'Nicht angegeben',
            'slug_autogenerated' => 'Wird automatisch generiert',
            'no_description'     => 'Keine Beschreibung',
            'no_active_prices'   => 'Keine aktiven Preise konfiguriert',
            'no_languages'       => 'Keine Sprachen zugewiesen',
            'none_included'      => 'Keine enthaltenen Leistungen angegeben',
            'none_excluded'      => 'Keine ausgeschlossenen Leistungen angegeben',

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
            'purge_text_with_bookings' => 'Diese Tour hat :count Buchung(en). Sie werden nicht gelöscht, bleiben aber ohne zugeordnete Tour.',
            'toggle_question_active'   => 'Tour deaktivieren?',
            'toggle_question_inactive' => 'Tour aktivieren?',
        ],

        'flash' => [
            'created'      => 'Tour erfolgreich erstellt.',
            'updated'      => 'Tour erfolgreich aktualisiert.',
            'deleted_soft' => 'Tour nach „Gelöscht“ verschoben.',
            'restored'     => 'Tour erfolgreich wiederhergestellt.',
            'purged'       => 'Tour endgültig gelöscht.',
            'toggled_on'   => 'Tour aktiviert.',
            'toggled_off'  => 'Tour deaktiviert.',
        ],

        'table' => [
            'id'           => 'ID',
            'name'         => 'Name',
            'overview'     => 'Übersicht',
            'amenities'    => 'Ausstattungen',
            'exclusions'   => 'Ausschlüsse',
            'itinerary'    => 'Reiseplan',
            'languages'    => 'Sprachen',
            'schedules'    => 'Zeiten',
            'adult_price'  => 'Preis Erwachsene',
            'kid_price'    => 'Preis Kinder',
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
            'group_size' => 'Gruppengröße pro Guide oder allgemeine Gruppengröße für diese Tour. (Dieser Wert wird in den Produktinformationen angezeigt.)',
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
            'purge_has_bookings' => 'Dauerhaftes Löschen ist nicht möglich: Die Tour hat Buchungen.',
        ],

        'ui' => [
            'add_tour_type' => 'Tourtyp hinzufügen',
            'back'          => 'Zurück',
            'page_title'    => 'Verwaltung der Touren',
            'page_heading'  => 'Verwaltung der Touren',
            'create_title'  => 'Tour registrieren',
            'edit_title'    => 'Tour bearbeiten',
            'delete_title'  => 'Tour löschen',
            'cancel'        => 'Abbrechen',
            'save'          => 'Speichern',
            'save_changes'  => 'Änderungen speichern',
            'update'        => 'Aktualisieren',
            'delete_confirm'=> 'Diese Tour löschen?',
            'toggle_on'     => 'Aktivieren',
            'toggle_off'    => 'Deaktivieren',
            'toggle_on_title'  => 'Tour aktivieren?',
            'toggle_off_title' => 'Tour deaktivieren?',
            'toggle_on_button' => 'Ja, aktivieren',
            'toggle_off_button'=> 'Ja, deaktivieren',
            'see_more'          => 'Mehr anzeigen',
            'see_less'          => 'Weniger anzeigen',
            'load_more'         => 'Mehr laden',
            'loading'           => 'Lade...',
            'load_more_error'   => 'Es konnten keine weiteren Touren geladen werden.',
            'confirm_title'     => 'Bestätigung',
            'confirm_text'      => 'Möchtest du diese Aktion bestätigen?',
            'yes_confirm'       => 'Ja, bestätigen',
            'no_confirm'        => 'Nein, abbrechen',
            'add_tour'          => 'Tour hinzufügen',
            'edit_tour'         => 'Tour bearbeiten',
            'delete_tour'       => 'Tour löschen',
            'toggle_tour'       => 'Tour aktivieren/deaktivieren',
            'view_cart'         => 'Warenkorb anzeigen',
            'add_to_cart'       => 'Zum Warenkorb hinzufügen',
            'slug_help'         => 'Identifikator der Tour in der URL (ohne Leerzeichen oder Akzente)',
            'generate_auto'     => 'Automatisch generieren',
            'slug_preview_label'=> 'Vorschau',
            'saved'             => 'Gespeichert',

            'available_languages'    => 'Verfügbare Sprachen',
            'default_capacity'       => 'Standardkapazität',
            'create_new_schedules'   => 'Neue Zeiten erstellen',
            'multiple_hint_ctrl_cmd' => 'Halte CTRL/CMD gedrückt, um mehrere auszuwählen',
            'use_existing_schedules' => 'Bestehende Zeiten verwenden',
            'add_schedule'           => 'Zeit hinzufügen',
            'schedules_title'        => 'Tour-Zeiten',
            'amenities_included'     => 'Inklusive Ausstattungen',
            'amenities_excluded'     => 'Nicht enthaltene Ausstattungen',
            'color'                  => 'Farbe der Tour',
            'remove'                 => 'Entfernen',
            'choose_itinerary'       => 'Reiseplan wählen',
            'select_type'            => 'Typ auswählen',
            'empty_means_default'    => 'Standard',
            'actives'                => 'Aktive',
            'inactives'              => 'Inaktive',
            'archived'               => 'Archiviert',
            'all'                    => 'Alle',
            'help_title'             => 'Hilfe',
            'amenities_included_hint'=> 'Wähle aus, was in der Tour enthalten ist.',
            'amenities_excluded_hint'=> 'Wähle aus, was in der Tour NICHT enthalten ist.',
            'help_included_title'    => 'Inklusive',
            'help_included_text'     => 'Markiere alles, was im Tourpreis enthalten ist (Transport, Mahlzeiten, Eintrittsgelder, Ausrüstung, Guide usw.).',
            'help_excluded_title'    => 'Nicht inklusive',
            'help_excluded_text'     => 'Markiere alles, was der Kunde separat bezahlen oder mitbringen muss (Trinkgelder, alkoholische Getränke, Souvenirs usw.).',
            'select_or_create_title' => 'Reiseplan auswählen oder erstellen',
            'select_existing_items'  => 'Bestehende Elemente auswählen',
            'name_hint'              => 'Bezeichnender Name für diesen Reiseplan',
            'click_add_item_hint'    => 'Klicke auf „Element hinzufügen“, um neue Elemente zu erstellen',
            'scroll_hint'            => 'Horizontal scrollen, um weitere Spalten zu sehen',
            'no_schedules'           => 'Keine Zeiten',
            'no_prices'              => 'Keine Preise konfiguriert',
            'edit'                   => 'Bearbeiten',
            'slug_auto'              => 'Wird automatisch generiert',
            'added_to_cart'          => 'Zum Warenkorb hinzugefügt',
            'add_language'           => 'Sprache hinzufügen',
            'added_to_cart_text'     => 'Die Tour wurde erfolgreich zum Warenkorb hinzugefügt.',
            'amenities_excluded_auto_hint' => 'Standardmäßig markieren wir als „nicht enthalten“ alle Ausstattungen, die du nicht als enthalten ausgewählt hast. Du kannst diejenigen abwählen, die für die Tour nicht gelten.',
            'quick_create_language_hint'   => 'Füge schnell eine neue Sprache hinzu, wenn sie nicht in der Liste erscheint.',
            'quick_create_type_hint'       => 'Füge schnell einen neuen Tourtyp hinzu, wenn er nicht in der Liste erscheint.',

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
            'confirm_archive_text'  => 'Die Tour wird für neue Buchungen deaktiviert, bestehende Buchungen bleiben jedoch erhalten.',
            'confirm_purge_title'   => 'Endgültig löschen',
            'confirm_purge_text'    => 'Diese Aktion ist irreversibel und nur erlaubt, wenn die Tour nie Buchungen hatte.',

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
        'saved'               => 'Gespeichert',
        'caption_updated'     => 'Bildunterschrift erfolgreich aktualisiert.',
        'deleted'             => 'Gelöscht',
        'image_removed'       => 'Bild erfolgreich gelöscht.',
        'invalid_order'       => 'Ungültige Reihenfolge der Bilder.',
        'nothing_to_reorder'  => 'Nichts zum Neuordnen.',
        'order_saved'         => 'Reihenfolge gespeichert.',
        'cover_updated_title' => 'Titelbild aktualisieren',
        'cover_updated_text'  => 'Dieses Bild ist nun das Titelbild.',
        'deleting'            => 'Lösche...',

        'ui' => [
            // Tour-Auswahlseite
            'page_title_pick'     => 'Tour-Bilder',
            'page_heading'        => 'Tour-Bilder',
            'choose_tour'         => 'Tour auswählen',
            'search_placeholder'  => 'Nach ID oder Name suchen…',
            'search_button'       => 'Suchen',
            'no_results'          => 'Keine Touren gefunden.',
            'manage_images'       => 'Bilder verwalten',
            'cover_alt'           => 'Titelbild',
            'images_label'        => 'Bilder',

            // Allgemeine Buttons
            'upload_btn'          => 'Hochladen',
            'delete_btn'          => 'Löschen',
            'show_btn'            => 'Anzeigen',
            'close_btn'           => 'Schließen',
            'preview_title'       => 'Bildvorschau',

            // Status-Texte
            'error_title'         => 'Fehler',
            'warning_title'       => 'Achtung',
            'success_title'       => 'Erfolg',
            'cancel_btn'          => 'Abbrechen',

            // Bestätigungen
            'confirm_delete_title' => 'Dieses Bild löschen?',
            'confirm_delete_text'  => 'Diese Aktion kann nicht rückgängig gemacht werden.',

            // Titelbild-Verwaltung (klassisches Formular)
            'cover_current_title'    => 'Aktuelles Titelbild',
            'upload_new_cover_title' => 'Neues Titelbild hochladen',
            'cover_file_label'       => 'Titelbild-Datei',
            'file_help_cover'        => 'JPEG/PNG/WebP, maximal 30 MB.',
            'id_label'               => 'ID',

            // Navigation/Kopfzeile in der Tour-Ansicht
            'back_btn'          => 'Zurück zur Liste',

            // Stats (obere Leiste)
            'stats_images'      => 'Hochgeladene Bilder',
            'stats_cover'       => 'Definierte Titelbilder',
            'stats_selected'    => 'Ausgewählt',

            // Upload-Bereich
            'drag_or_click'     => 'Ziehe deine Bilder hierher oder klicke zum Auswählen.',
            'upload_help'       => 'Erlaubte Formate: JPG, PNG, WebP. Gesamte maximale Größe 100 MB.',
            'select_btn'        => 'Dateien auswählen',
            'limit_badge'       => 'Limit von :max Bildern erreicht',
            'files_word'        => 'Dateien',

            // Mehrfachauswahl-Toolbar
            'select_all'        => 'Alle auswählen',
            'delete_selected'   => 'Ausgewählte löschen',
            'delete_all'        => 'Alle löschen',

            // Bild-Selector (Chip)
            'select_image_title' => 'Dieses Bild auswählen',
            'select_image_aria'  => 'Bild :id auswählen',

            // Titelbild (pro Karte)
            'cover_label'       => 'Titelbild',
            'cover_btn'         => 'Als Titelbild festlegen',

            // Status/Helfer
            'caption_placeholder' => 'Bildunterschrift (optional)',
            'saving_label'        => 'Speichere…',
            'saving_fallback'     => 'Speichere…',
            'none_label'          => 'Keine Bildunterschrift',
            'limit_word'          => 'Limit',

            // Erweiterte Bestätigungen (JS)
            'confirm_set_cover_title' => 'Als Titelbild festlegen?',
            'confirm_set_cover_text'  => 'Dieses Bild wird das Haupttitelbild der Tour.',
            'confirm_btn'             => 'Ja, fortfahren',

            'confirm_bulk_delete_title' => 'Ausgewählte Bilder löschen?',
            'confirm_bulk_delete_text'  => 'Die ausgewählten Bilder werden endgültig gelöscht.',

            'confirm_delete_all_title'  => 'Alle Bilder löschen?',
            'confirm_delete_all_text'   => 'Alle Bilder dieser Tour werden gelöscht.',

            // Ansicht ohne Bilder
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
            'too_large'      => 'Die Datei überschreitet die maximal zulässige Größe. Bitte versuche es mit einem kleineren Bild.',
        ],
    ],

    'prices' => [
        'ui' => [
            'page_title'         => 'Preise – :name',
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
            'delete_text'    => 'Diese Kategorie von dieser Tour löschen?',
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
            'auto_disabled_tooltip' => 'Preis 0 $ – automatisch deaktiviert.',
            'fix_errors'            => 'Korrigiere die Minimal- und Maximalmengen.',
        ],

        'quick_category' => [
            'title'                 => 'Schnell-Kategorie erstellen',
            'button'                => 'Neue Kategorie',
            'go_to_index'           => 'Alle Kategorien anzeigen',
            'go_to_index_title'     => 'Die vollständige Kategorienliste öffnen',
            'name_label'            => 'Name der Kategorie',
            'age_from'              => 'Alter von',
            'age_to'                => 'Alter bis',
            'save'                  => 'Kategorie speichern',
            'cancel'                => 'Abbrechen',
            'saving'                => 'Speichere...',
            'success_title'         => 'Kategorie erstellt',
            'success_text'          => 'Die Kategorie wurde erfolgreich erstellt und der Tour hinzugefügt.',
            'error_title'           => 'Fehler',
            'error_generic'         => 'Beim Erstellen der Kategorie ist ein Problem aufgetreten.',
            'created_ok'            => 'Kategorie erfolgreich erstellt.',
        ],
    ],

    'ajax' => [
        'category_created'  => 'Kategorie erfolgreich erstellt.',
        'category_error'    => 'Fehler beim Erstellen der Kategorie.',
        'language_created'  => 'Sprache erfolgreich erstellt.',
        'language_error'    => 'Fehler beim Erstellen der Sprache.',
        'amenity_created'   => 'Ausstattung erfolgreich erstellt.',
        'amenity_error'     => 'Fehler beim Erstellen der Ausstattung.',
        'schedule_created'  => 'Zeitplan erfolgreich erstellt.',
        'schedule_error'    => 'Fehler beim Erstellen des Zeitplans.',
        'itinerary_created' => 'Reiseplan erfolgreich erstellt.',
        'itinerary_error'   => 'Fehler beim Erstellen des Reiseplans.',
        'translation_error' => 'Fehler bei der Übersetzung.',
    ],

    'modal' => [
        'create_category'  => 'Neue Kategorie erstellen',
        'create_language'  => 'Neue Sprache erstellen',
        'create_amenity'   => 'Neue Ausstattung erstellen',
        'create_schedule'  => 'Neuen Zeitplan erstellen',
        'create_itinerary' => 'Neuen Reiseplan erstellen',
    ],

    'validation' => [
        'slug_taken'     => 'Dieser Slug wird bereits verwendet.',
        'slug_available' => 'Slug verfügbar.',
    ],

    'tour_type' => [
        'fields' => [
            'name'        => 'Name',
            'description' => 'Beschreibung',
            'status'      => 'Status',
        ],
    ],

];
