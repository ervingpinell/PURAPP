<?php

/*************************************************************
 *  MÓDULO DE TRADUCCIONES: TOURS
 *  Archivo: resources/lang/de/m_tours.php
 *
 *  Índice (sección y línea de inicio)
 *  [01] COMMON           -> línea 23
 *  [02] AMENITY          -> línea 31
 *  [03] SCHEDULE         -> línea 106
 *  [04] ITINERARY_ITEM   -> línea 218
 *  [05] ITINERARY        -> línea 288
 *  [06] LANGUAGE         -> línea 364
 *  [07] TOUR             -> línea 453
 *  [08] IMAGES           -> línea 579
 *************************************************************/

return [

    // =========================================================
    // [01] COMMON
    // =========================================================
    'common' => [
        'open' => 'Öffnen',
        'success_title' => 'Erfolg',
        'error_title'   => 'Fehler',
        'people' => 'Personen',
        'hours' => 'Stunden',
        'success' => 'Erfolg',
        'error' => 'Fehler',
        'cancel' => 'Abbrechen',
        'confirm_delete' => 'Ja, löschen',
        'unspecified' => 'Nicht angegeben',
        'no_description' => 'Keine Beschreibung',
        'required_fields_title' => 'Erforderliche Felder',
        'required_fields_text' => 'Bitte füllen Sie die Pflichtfelder aus: Name und maximale Kapazität.',
        'active' => 'Aktiv',
        'inactive' => 'Inaktiv',
        'notice' => 'Hinweis',
        'na'    => 'Nicht konfiguriert',
        'create' => 'Erstellen',
        'previous' => 'Zurück',
        'info'               => 'Information',
        'close'              => 'Schließen',
        'save'              => 'Speichern',
        'required'           => 'Dieses Feld ist erforderlich.',
        'add'                => 'Hinzufügen',
        'translating'        => 'Übersetze...',
        'error_translating'  => 'Der Text konnte nicht übersetzt werden.',
        'confirm' => 'Bestätigen',
        'yes' => 'Ja',
        'form_errors_title' => 'Bitte korrigieren Sie die folgenden Fehler:',
        'delete' => 'Löschen',
        'delete_all' => 'Alle löschen',
        'actions' => 'Aktionen',
        'updated_at' => 'Letzte Aktualisierung',
        'not_set' => 'Nicht angegeben',
        'error_deleting' => 'Beim Löschen ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.',
        'error_saving' => 'Beim Speichern ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.',
        'crud_go_to_index' => ':element verwalten',
        'validation_title' => 'Es liegen Validierungsfehler vor',
        'ok'               => 'OK',
        'confirm_delete_title' => 'Dieses Element löschen?',
        'confirm_delete_text' => 'Diese Aktion kann nicht rückgängig gemacht werden.',
        'saving' => 'Speichere...',
        'network_error' => 'Netzwerkfehler',
        'custom' => 'Benutzerdefiniert',
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

            'processing' => 'Verarbeite...',
            'applying'   => 'Wendet an...',
            'deleting'   => 'Lösche...',

            'toggle_on'  => 'Ausstattung aktivieren',
            'toggle_off' => 'Ausstattung deaktivieren',

            'toggle_confirm_on_title'  => 'Ausstattung aktivieren?',
            'toggle_confirm_off_title' => 'Ausstattung deaktivieren?',
            'toggle_confirm_on_html'   => 'Die Ausstattung <b>:label</b> wird aktiviert.',
            'toggle_confirm_off_html'  => 'Die Ausstattung <b>:label</b> wird deaktiviert.',

            'delete_confirm_title' => 'Endgültig löschen?',
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
            'deleted'     => 'Ausstattung endgültig gelöscht.',
        ],

        'error' => [
            'create' => 'Die Ausstattung konnte nicht erstellt werden.',
            'update' => 'Die Ausstattung konnte nicht aktualisiert werden.',
            'toggle' => 'Der Status der Ausstattung konnte nicht geändert werden.',
            'delete' => 'Die Ausstattung konnte nicht gelöscht werden.',
        ],

        'validation' => [
            'included_required' => 'Sie müssen mindestens eine inkludierte Ausstattung auswählen.',
            'name' => [
                'title'    => 'Ungültiger Name',
                'required' => 'Das Feld :attribute ist erforderlich.',
                'string'   => 'Das Feld :attribute muss eine Zeichenkette sein.',
                'max'      => 'Das Feld :attribute darf :max Zeichen nicht überschreiten.',
            ],
        ],

        'hints' => [
            'fontawesome' => 'Verwenden Sie FontAwesome-Klassen, z. B.: "fas fa-check".',
        ],
        'quick_create' => [
            'button'           => 'Neue Ausstattung',
            'title'            => 'Schnelle Ausstattung erstellen',
            'name_label'       => 'Name der Ausstattung',
            'icon_label'       => 'Icon (optional)',
            'icon_placeholder' => 'Z. B.: fas fa-utensils',
            'icon_help'        => 'Verwenden Sie eine Icon-Klasse von Font Awesome oder lassen Sie das Feld leer.',
            'save'             => 'Ausstattung speichern',
            'cancel'           => 'Abbrechen',
            'saving'           => 'Speichere...',
            'error_generic'    => 'Die Ausstattung konnte nicht erstellt werden. Bitte versuchen Sie es erneut.',
            'go_to_index'         => 'Alle anzeigen',
            'go_to_index_title'   => 'Zur vollständigen Liste der Ausstattungen wechseln',
            'success_title'       => 'Ausstattung erstellt',
            'success_text'        => 'Die Ausstattung wurde zur Liste des Tours hinzugefügt.',
            'error_title'         => 'Fehler beim Erstellen der Ausstattung',
            'error_duplicate'     => 'Es gibt bereits eine Ausstattung mit diesem Namen.',
        ],
    ],

    // =========================================================
    // [03] SCHEDULE
    // =========================================================
    'schedule' => [
        'plural'       => 'Zeiten',
        'singular'     => 'Zeit',
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

            'time_range'        => 'Zeitspanne',
            'state'             => 'Status',
            'actions'           => 'Aktionen',
            'schedule_state'    => 'Zeit',
            'assignment_state'  => 'Zuweisung',
            'no_general'        => 'Es gibt keine allgemeinen Zeiten.',
            'no_tour_schedules' => 'Diese Tour hat noch keine Zeiten.',
            'no_label'          => 'Keine Bezeichnung',
            'assigned_count'    => 'zugewiesene Zeit(en)',

            'toggle_global_title'     => 'Aktivieren/Deaktivieren (global)',
            'toggle_global_on_title'  => 'Zeit global aktivieren?',
            'toggle_global_off_title' => 'Zeit global deaktivieren?',
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
            'detach_confirm_html'  => 'Die Zeit wird von <b>:tour</b> <b>entfernt</b>.',

            'delete_forever'       => 'Löschen (global)',
            'delete_confirm_title' => 'Endgültig löschen?',
            'delete_confirm_html'  => '<b>:label</b> (global) wird gelöscht und kann nicht rückgängig gemacht werden.',

            'yes_continue' => 'Ja, fortfahren',
            'yes_delete'   => 'Ja, löschen',
            'yes_detach'   => 'Ja, entfernen',

            'this_schedule' => 'diese Zeit',
            'this_tour'     => 'diese Tour',

            'processing'     => 'Verarbeite...',
            'applying'       => 'Wendet an...',
            'deleting'       => 'Lösche...',
            'removing'       => 'Entferne...',
            'saving_changes' => 'Speichere Änderungen...',
            'save'           => 'Speichern',
            'save_changes'   => 'Änderungen speichern',
            'cancel'         => 'Abbrechen',

            'missing_fields_title' => 'Fehlende Daten',
            'missing_fields_text'  => 'Bitte prüfen Sie die Pflichtfelder (Beginn, Ende und Kapazität).',
            'could_not_save'       => 'Konnte nicht gespeichert werden',
            'base_capacity_tour'             => 'Grundkapazität der Tour:',
            'capacity_not_defined'           => 'Nicht definiert',
            'capacity_optional'              => 'Kapazität (optional)',
            'capacity_placeholder_with_value' => 'Z. B.: :capacity',
            'capacity_placeholder_generic'   => 'Kapazität der Tour verwenden',
            'capacity_hint_with_value'       => 'Leer lassen → :capacity',
            'capacity_hint_generic'          => 'Leer lassen → Kapazität der Tour',
            'tip_label'                      => 'Tipp:',
            'capacity_tip'                   => 'Sie können die Kapazität leer lassen, damit das System die allgemeine Kapazität der Tour (:capacity) verwendet.',
            'new_schedule_for_tour'            => 'Neue Zeit',
            'modal_new_for_tour_title'         => 'Zeit für :tour erstellen',
            'modal_save'                       => 'Zeit speichern',
            'modal_cancel'                     => 'Abbrechen',
            'capacity_modal_info_with_value'   => 'Die Grundkapazität der Tour beträgt :capacity. Wenn Sie das Kapazitätsfeld leer lassen, wird dieser Wert verwendet.',
            'capacity_modal_info_generic'      => 'Wenn Sie das Kapazitätsfeld leer lassen, wird die allgemeine Kapazität der Tour verwendet, sofern definiert.',
        ],

        'success' => [
            'created'                => 'Zeit erfolgreich erstellt.',
            'updated'                => 'Zeit erfolgreich aktualisiert.',
            'activated_global'       => 'Zeit erfolgreich global aktiviert.',
            'deactivated_global'     => 'Zeit erfolgreich global deaktiviert.',
            'attached'               => 'Zeit wurde der Tour zugewiesen.',
            'detached'               => 'Zeit wurde erfolgreich von der Tour entfernt.',
            'assignment_activated'   => 'Zuweisung für diese Tour aktiviert.',
            'assignment_deactivated' => 'Zuweisung für diese Tour deaktiviert.',
            'deleted'                => 'Zeit erfolgreich gelöscht.',
            'created_and_attached'   => 'Die Zeit wurde erstellt und der Tour erfolgreich zugewiesen.',
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
            'morning' => 'Z. B.: Morgen',
        ],
        'validation' => [
            'no_schedule_selected' => 'Sie müssen mindestens eine Zeit auswählen.',
            'title' => 'Validierung der Zeiten',
            'end_after_start' => 'Die Endzeit muss nach der Startzeit liegen.',
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
            'assigned_items'       => 'Dem Reiseplan zugewiesene Elemente',
            'drag_to_order'        => 'Ziehen Sie die Elemente, um die Reihenfolge festzulegen.',
            'pool_hint'            => 'Markieren Sie die verfügbaren Elemente, die Sie in diesen Reiseplan aufnehmen möchten.',
            'register_item_hint'   => 'Registrieren Sie neue Elemente, wenn Sie zusätzliche Schritte benötigen, die noch nicht existieren.',
            'translations_updated' => 'Übersetzung aktualisiert',
            'toggle_on'  => 'Element aktivieren',
            'toggle_off' => 'Element deaktivieren',

            'delete_forever'       => 'Endgültig löschen',
            'delete_confirm_title' => 'Endgültig löschen?',
            'delete_confirm_html'  => '<b>:label</b> wird gelöscht und kann nicht rückgängig gemacht werden.',
            'yes_delete'           => 'Ja, löschen',
            'item_this'            => 'dieses Element',

            'processing' => 'Verarbeite...',
            'applying'   => 'Wendet an...',
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
        'plural'           => 'Reisepläne',
        'singular'         => 'Reiseplan',

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
            'page_heading'  => 'Reisepläne und Elementverwaltung',
            'new_itinerary' => 'Neuer Reiseplan',
            'select_or_create_hint' => 'Wählen Sie einen vorhandenen Reiseplan oder erstellen Sie einen neuen für diese Tour.',
            'save_changes'          => 'Speichern Sie den Reiseplan, um die Änderungen auf die Tour anzuwenden.',
            'select_existing' => 'Bestehenden Reiseplan auswählen',
            'create_new' => 'Neuen Reiseplan erstellen',
            'add_item' => 'Element hinzufügen',
            'min_one_item' => 'Der Reiseplan muss mindestens ein Element enthalten.',
            'cannot_delete_item' => 'Kann nicht gelöscht werden',
            'item_added' => 'Element hinzugefügt',
            'item_added_success' => 'Das Element wurde erfolgreich zum Reiseplan hinzugefügt.',
            'error_creating_item' => 'Validierungsfehler beim Erstellen des Elements.',
            'translations_updated' => 'Übersetzung aktualisiert',

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
            'drag_hint'             => 'Ziehen und ablegen, um die Reihenfolge der Elemente festzulegen.',
            'drag_handle'           => 'Ziehen zum Neuordnen',
            'select_one_title'      => 'Sie müssen mindestens ein Element auswählen',
            'select_one_text'       => 'Bitte wählen Sie mindestens ein Element aus, um fortzufahren.',
            'assign_confirm_title'  => 'Ausgewählte Elemente zuweisen?',
            'assign_confirm_button' => 'Ja, zuweisen',
            'assigning'             => 'Weise zu...',

            'no_items_assigned'       => 'Diesem Reiseplan sind keine Elemente zugewiesen.',
            'itinerary_this'          => 'dieser Reiseplan',
            'processing'              => 'Verarbeite...',
            'saving'                  => 'Speichere...',
            'activating'              => 'Aktiviere...',
            'deactivating'            => 'Deaktiviere...',
            'applying'                => 'Wendet an...',
            'deleting'                => 'Lösche...',
            'flash_success_title'     => 'Erfolg',
            'flash_error_title'       => 'Fehler',
            'validation_failed_title' => 'Konnte nicht verarbeitet werden',
            'go_to_crud' => 'Zum Modul wechseln',
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
            'name_required' => 'Sie müssen einen Namen für den Reiseplan angeben.',
            'must_add_items' => 'Sie müssen dem neuen Reiseplan mindestens ein Element hinzufügen.',
            'title' => 'Validierung des Reiseplans',
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
                'item' => 'Element',
                'required'      => 'Sie müssen mindestens ein Element auswählen.',
                'array'         => 'Das Format der Elemente ist ungültig.',
                'min'           => 'Sie müssen mindestens ein Element auswählen.',
                'order_integer' => 'Die Reihenfolge muss eine ganze Zahl sein.',
                'order_min'     => 'Die Reihenfolge darf nicht negativ sein.',
                'order_max'     => 'Die Reihenfolge darf 9999 nicht überschreiten.',
            ],
        ],
        'item' => 'Element',
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
            'delete_forever' => 'Endgültig löschen',

            'processing'   => 'Verarbeite...',
            'saving'       => 'Speichere...',
            'activating'   => 'Aktiviere...',
            'deactivating' => 'Deaktiviere...',
            'deleting'     => 'Lösche...',

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

        'validation' => [
            // Mensajes generales
            'required' => 'Dieses Feld ist erforderlich.',
            'min' => 'Dieses Feld muss mindestens :min Zeichen haben.',
            'max' => 'Dieses Feld darf :max Zeichen nicht überschreiten.',
            'number' => 'Dieses Feld muss eine gültige Zahl sein.',
            'slug' => 'Der Slug darf nur Kleinbuchstaben, Zahlen und Bindestriche enthalten.',
            'color' => 'Bitte wählen Sie eine gültige Farbe aus.',
            'select' => 'Bitte wählen Sie eine Option aus.',

            // Mensajes específicos de campos
            'length_in_hours' => 'Dauer in Stunden (z. B.: 2, 2.5, 4)',
            'max_capacity_help' => 'Maximale Anzahl von Personen pro Tour',

            // Formularios
            'form_error_title' => 'Achtung!',
            'form_error_message' => 'Bitte korrigieren Sie die Fehler im Formular, bevor Sie fortfahren.',
            'saving' => 'Speichere...',

            // Éxito
            'success' => 'Erfolg!',
            'tour_type_created' => 'Tourtyp erfolgreich erstellt.',
            'language_created' => 'Sprache erfolgreich erstellt.',

            // Errores
            'tour_type_error' => 'Fehler beim Erstellen des Tourtyps.',
            'language_error' => 'Fehler beim Erstellen der Sprache.',
            'languages_hint' => 'Wählen Sie die für diesen Tour verfügbaren Sprachen aus.',
        ],

        'wizard' => [
            // Títulos generales
            'create_new_tour' => 'Neue Tour erstellen',
            'edit_tour' => 'Tour bearbeiten',
            'step_number' => 'Schritt :number',
            'edit_step' => 'Bearbeiten',
            'leave_warning' => 'Sie haben ungespeicherte Änderungen an der Tour. Wenn Sie jetzt verlassen, bleibt der Entwurf in der Datenbank bestehen. Sind Sie sicher, dass Sie verlassen möchten?',
            'cancel_title'   => 'Tour-Konfiguration abbrechen?',
            'cancel_text'    => 'Wenn Sie diesen Assistenten verlassen, können Änderungen in diesem Schritt verloren gehen.',
            'cancel_confirm' => 'Ja, Änderungen verwerfen',
            'cancel_cancel'  => 'Nein, weiter bearbeiten',
            'details_validation_text' => 'Bitte prüfen Sie die Pflichtfelder im Details-Formular, bevor Sie fortfahren.',
            'most_recent'  => 'Neueste',
            'last_modified'  => 'Zuletzt geändert',
            'start_fresh'  => 'Neu beginnen',
            'draft_details'  => 'Entwurfsdetails',
            'drafts_found'  => 'Ein Entwurf wurde gefunden',
            'basic_info'  => 'Details',

            // Pasos del wizard
            'steps' => [
                'details' => 'Grundlegende Details',
                'itinerary' => 'Reiseplan',
                'schedules' => 'Zeiten',
                'amenities' => 'Ausstattungen',
                'prices' => 'Preise',
                'summary' => 'Zusammenfassung',
            ],

            // Acciones
            'save_and_continue' => 'Speichern und fortfahren',
            'publish_tour' => 'Tour veröffentlichen',
            'delete_draft' => 'Entwurf löschen',
            'ready_to_publish' => 'Bereit zum Veröffentlichen?',

            // Mensajes
            'details_saved' => 'Details erfolgreich gespeichert.',
            'itinerary_saved' => 'Reiseplan erfolgreich gespeichert.',
            'schedules_saved' => 'Zeiten erfolgreich gespeichert.',
            'amenities_saved' => 'Ausstattungen erfolgreich gespeichert.',
            'prices_saved' => 'Preise erfolgreich gespeichert.',
            'published_successfully' => 'Tour erfolgreich veröffentlicht!',
            'draft_cancelled' => 'Entwurf gelöscht.',

            // Estados
            'draft_mode' => 'Entwurfsmodus',
            'draft_explanation' => 'Diese Tour wird als Entwurf gespeichert, bis Sie alle Schritte abgeschlossen und sie veröffentlicht haben.',
            'already_published' => 'Diese Tour wurde bereits veröffentlicht. Verwenden Sie den normalen Editor, um sie zu bearbeiten.',
            'cannot_cancel_published' => 'Eine bereits veröffentlichte Tour kann nicht abgebrochen werden.',

            // Confirmaciones
            'confirm_cancel' => 'Sind Sie sicher, dass Sie abbrechen und diesen Entwurf löschen möchten?',

            // Summary
            'publish_explanation' => 'Überprüfen Sie alle Informationen, bevor Sie veröffentlichen. Nach der Veröffentlichung ist die Tour für Buchungen verfügbar.',
            'can_edit_later' => 'Sie können die Tour nach der Veröffentlichung im Administrationsbereich bearbeiten.',
            'incomplete_warning' => 'Einige Schritte sind unvollständig. Sie können trotzdem veröffentlichen, es wird jedoch empfohlen, alle Informationen zu vervollständigen.',

            // Checklist
            'checklist' => 'Checkliste',
            'checklist_details' => 'Grundlegende Details abgeschlossen',
            'checklist_itinerary' => 'Reiseplan konfiguriert',
            'checklist_schedules' => 'Zeiten hinzugefügt',
            'checklist_amenities' => 'Ausstattungen konfiguriert',
            'checklist_prices' => 'Preise festgelegt',

            // Hints
            'hints' => [
                'status' => 'Der Status kann nach der Veröffentlichung geändert werden.',
            ],

            // Modal de drafts existentes
            'existing_drafts_title' => 'Sie haben unvollständige Tour-Entwürfe!',
            'existing_drafts_message' => 'Wir haben :count Tour-Entwurf gefunden, den Sie noch nicht fertiggestellt haben.',
            'current_step' => 'Aktueller Schritt',
            'step' => 'Schritt',

            // Acciones del modal
            'continue_draft' => 'Mit diesem Entwurf fortfahren',
            'delete_all_drafts' => 'Alle Entwürfe löschen',
            'create_new_anyway' => 'Trotzdem neue Tour erstellen',

            // Información adicional
            'drafts_info' => 'Sie können einen bestehenden Entwurf weiter bearbeiten, ihn einzeln löschen, alle Entwürfe löschen oder eine neue Tour erstellen und die aktuellen Entwürfe ignorieren.',

            // Confirmaciones de eliminación
            'confirm_delete_title' => 'Diesen Entwurf löschen?',
            'confirm_delete_message' => 'Diese Aktion kann nicht rückgängig gemacht werden. Der Entwurf wird dauerhaft gelöscht:',
            'confirm_delete_all_title' => 'Alle Entwürfe löschen?',
            'confirm_delete_all_message' => ':count Entwurf/Entwürfe werden dauerhaft gelöscht. Diese Aktion kann nicht rückgängig gemacht werden.',

            // Mensajes de éxito
            'draft_deleted' => 'Entwurf erfolgreich gelöscht.',
            'all_drafts_deleted' => ':count Entwurf/Entwürfe erfolgreich gelöscht.',
            'continuing_draft' => 'Mit Ihrem Entwurf wird fortgefahren...',

            // Mensajes de error
            'not_a_draft' => 'Diese Tour ist kein Entwurf mehr und kann nicht über den Assistenten bearbeitet werden.',
        ],

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
            'already_added' => 'Diese Kategorie wurde bereits hinzugefügt.',
            'configured_categories' => 'Konfigurierte Kategorien',
            'create_category' => 'Kategorie erstellen',
            'note_title'              => 'Hinweis:',
            'note_text'               => 'Definieren Sie hier die Basispreise für jede Kundenkategorie.',
            'manage_detailed_hint'    => 'Für eine detaillierte Verwaltung verwenden Sie oben die Schaltfläche „Detaillierte Preise verwalten“.',
            'price_usd'               => 'Preis (USD)',
            'min_quantity'            => 'Mindestmenge',
            'max_quantity'            => 'Höchstmenge',
            'status'                  => 'Status',
            'active'                  => 'Aktiv',
            'no_categories'           => 'Es sind keine Kundenkategorien konfiguriert.',
            'create_categories_first' => 'Erstellen Sie zuerst Kategorien',
            'page_title'         => 'Preise – :name',
            'header_title'       => 'Preise: :name',
            'back_to_tours'      => 'Zurück zu den Touren',

            'configured_title'   => 'Konfigurierte Kategorien und Preise',
            'empty_title'        => 'Für diese Tour sind keine Kategorien konfiguriert.',
            'empty_hint'         => 'Verwenden Sie das Formular rechts, um Kategorien hinzuzufügen.',

            'save_changes'       => 'Änderungen speichern',
            'auto_disable_note'  => 'Preise von $0 werden automatisch deaktiviert.',
            'not_available_for_date' => 'Für dieses Datum nicht verfügbar',

            // Calendar price indicators
            'price_lower' => 'Niedrigerer Preis',
            'price_higher' => 'Höherer Preis',
            'price_normal' => 'Normaler Preis',
            'price_legend' => 'Preislegende',

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
            'rule_zero_disable'  => 'Preise von $0 werden automatisch deaktiviert.',
            'rule_only_active'   => 'Nur aktive Kategorien werden auf der öffentlichen Seite angezeigt.',

            'status_active'      => 'Aktiv',
            'add_existing_category'      => 'Bestehende Kategorie hinzufügen',
            'choose_category_placeholder' => 'Kategorie auswählen…',
            'add_button'                 => 'Hinzufügen',
            'add_existing_hint'          => 'Fügen Sie nur die Kundenkategorien hinzu, die für diese Tour benötigt werden.',
            'remove_category'            => 'Kategorie entfernen',
            'category_already_added'     => 'Diese Kategorie wurde der Tour bereits hinzugefügt.',
            'no_prices_preview'          => 'Es sind noch keine Preise konfiguriert.',
            'already_added'              => 'Diese Kategorie wurde bereits zur Tour hinzugefügt.',

            // Seasonal pricing
            'valid_from'                  => 'Gültig ab',
            'valid_until'                 => 'Gültig bis',
            'default_price'               => 'Standardpreis',
            'seasonal_price'              => 'Saisonpreis',
            'season_label'                => 'Saison',
            'all_year'                    => 'Ganzjährig',
            'date_overlap_warning'        => 'Die Daten überschneiden sich mit einem anderen Preis für diese Kategorie',
            'invalid_date_range'          => 'Das Startdatum muss vor dem Enddatum liegen',
            'wizard_description'          => 'Preise nach Saison und Kundenkategorie festlegen',
            'add_period'                  => 'Preisperiode hinzufügen',
            'confirm_remove_period'       => 'Diese Preisperiode entfernen?',
            'category_already_in_period'  => 'Diese Kategorie ist bereits zu dieser Periode hinzugefügt',
            'category'                    => 'Kategorie',
            'age_range'                   => 'Alter',
            'taxes'                       => 'Steuern',
            'category_removed_success'    => 'Kategorie erfolgreich entfernt',
            'leave_empty_no_limit'        => 'Leer lassen für kein Limit',
            'category_added_success'      => 'Kategorie erfolgreich hinzugefügt',
            'period_removed_success'      => 'Zeitraum erfolgreich entfernt',
            'period_added_success'        => 'Zeitraum erfolgreich hinzugefügt',
            'overlap_not_allowed_title'   => 'Datumsbereich nicht zulässig',
            'overlap_not_allowed_text'    => 'Die ausgewählten Daten überschneiden sich mit einem anderen Preiszeitraum. Bitte passen Sie den Bereich an, um Konflikte zu vermeiden.',
            'overlap_conflict_with'       => 'Konflikt mit folgenden Zeiträumen:',
            'duplicate_category_title'    => 'Doppelte Kategorie',
            'invalid_date_range_title'    => 'Ungültiger Datumsbereich',
            'remove_category_confirm_text' => 'Diese Kategorie wird aus dem Zeitraum entfernt',
            'validation_failed'           => 'Validierung fehlgeschlagen',
            'are_you_sure'                => 'Sind Sie sicher?',
            'yes_delete'                  => 'Ja, löschen',
            'cancel'                      => 'Abbrechen',
            'attention'                   => 'Achtung',
        ],
        'modal' => [
            'create_category' => 'Kategorie erstellen',

            'fields' => [
                'name'          => 'Name',
                'age_from'      => 'Alter von',
                'age_to'        => 'Alter bis',
                'age_range'     => 'Altersbereich',
                'min'           => 'Minimum',
                'max'           => 'Maximum',
                'order'         => 'Reihenfolge',
                'is_active'     => 'Aktiv',
                'auto_translate' => 'Automatisch übersetzen',
            ],

            'placeholders' => [
                'name'              => 'Z. B.: Erwachsener, Kind, Kleinkind',
                'age_to_optional'   => 'Leer lassen für „+“',
            ],

            'hints' => [
                'age_to_empty_means_plus' => 'Wenn Sie das Feld „Alter bis“ leer lassen, wird es als „+“ interpretiert (z. B. 12+).',
                'min_le_max'              => 'Das Minimum muss kleiner oder gleich dem Maximum sein.',
            ],

            'errors' => [
                'min_le_max' => 'Das Minimum muss kleiner oder gleich dem Maximum sein.',
            ],
        ],

        'schedules_form' => [
            'available_title'        => 'Verfügbare Zeiten',
            'select_hint'            => 'Wählen Sie die Zeiten für diese Tour aus.',
            'no_schedules'           => 'Es sind keine Zeiten verfügbar.',
            'create_schedules_link'  => 'Zeiten erstellen',

            'create_new_title'       => 'Neue Zeit erstellen',
            'label_placeholder'      => 'Z. B.: Morgen, Nachmittag',
            'create_and_assign'      => 'Diese Zeit erstellen und der Tour zuweisen',

            'info_title'             => 'Informationen',
            'schedules_title'        => 'Zeiten',
            'schedules_text'         => 'Wählen Sie eine oder mehrere Zeiten aus, zu denen diese Tour verfügbar sein wird.',
            'create_block_title'     => 'Neu erstellen',
            'create_block_text'      => 'Wenn Sie eine Zeit benötigen, die noch nicht existiert, können Sie sie hier erstellen, indem Sie das Kontrollkästchen „Diese Zeit erstellen und der Tour zuweisen“ markieren.',

            'current_title'          => 'Aktuelle Zeiten',
            'none_assigned'          => 'Keine Zeiten zugewiesen',
        ],

        'summary' => [
            'preview_title'        => 'Tour-Vorschau',
            'preview_text_create'  => 'Überprüfen Sie alle Informationen, bevor Sie die Tour erstellen.',
            'preview_text_update'  => 'Überprüfen Sie alle Informationen, bevor Sie die Tour aktualisieren.',

            'basic_details_title'  => 'Grundlegende Details',
            'description_title'    => 'Beschreibung',
            'prices_title'         => 'Preise nach Kategorie',
            'schedules_title'      => 'Zeiten',
            'languages_title'      => 'Sprachen',
            'itinerary_title'      => 'Reiseplan',
            'amenities_title' => 'Ausstattung',

            'table' => [
                'category' => 'Kategorie',
                'price'    => 'Preis',
                'min_max'  => 'Min–Max',
                'status'   => 'Status'
            ],

            'not_specified'        => 'Nicht angegeben',
            'slug_autogenerated'   => 'Wird automatisch generiert',
            'deactivate' => 'Deaktivieren',
            'manage_prices' => 'Preise verwalten',
            'manage_images' => 'Bilder verwalten',
            'manage_delete' => 'Löschen',
            'no_description'       => 'Keine Beschreibung',
            'no_active_prices'     => 'Keine aktiven Preise konfiguriert',
            'no_languages'         => 'Keine Sprachen zugewiesen',
            'none_included'        => 'Keine inkludierten Leistungen angegeben',
            'none_excluded'        => 'Keine ausgeschlossenen Leistungen angegeben',
            'date_range'           => 'Datumsbereich',

            'units' => [
                'hours'  => 'Stunden',
                'people' => 'Personen',
            ],

            'create_note' => 'Zeiten, Preise, Sprachen und Ausstattungen werden hier angezeigt, nachdem Sie die Tour gespeichert haben.',
        ],
        'alerts' => [
            'delete_title' => 'Tour löschen?',
            'delete_text'  => 'Die Tour wird in „Gelöscht“ verschoben. Sie können sie später wiederherstellen.',
            'purge_title'  => 'Endgültig löschen?',
            'purge_text'   => 'Diese Aktion ist unwiderruflich.',
            'purge_text_with_bookings' => 'Diese Tour hat :count Buchung(en). Sie werden nicht gelöscht, bleiben aber ohne zugeordnete Tour.',
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
            'group_size'    => 'Max. Gruppe'
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
            'group_size' => 'Gruppengröße pro Guide oder allgemein für diese Tour. (Diese Angabe wird in den Produktinformationen angezeigt.)',
        ],

        'success' => [
            'created'     => 'Die Tour wurde erfolgreich erstellt.',
            'updated'     => 'Die Tour wurde erfolgreich aktualisiert.',
            'deleted'     => 'Die Tour wurde gelöscht.',
            'toggled'     => 'Der Status der Tour wurde aktualisiert.',
            'activated'   => 'Tour erfolgreich aktiviert.',
            'deactivated' => 'Tour erfolgreich deaktiviert.',
            // nuevos
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
            // nuevos
            'restore'            => 'Die Tour konnte nicht wiederhergestellt werden.',
            'purge'              => 'Die Tour konnte nicht dauerhaft gelöscht werden.',
            'purge_has_bookings' => 'Dauerhaftes Löschen nicht möglich: Die Tour hat Buchungen.',
        ],

        'ui' => [
            'add_tour_type' => 'Tourtyp hinzufügen',
            'back' => 'Zurück',
            'page_title'       => 'Verwaltung der Touren',
            'page_heading'     => 'Verwaltung der Touren',
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
            'toggle_on_button' => 'Ja, aktivieren',
            'toggle_off_button' => 'Ja, deaktivieren',
            'see_more'         => 'Mehr anzeigen',
            'see_less'         => 'Weniger anzeigen',
            'load_more'        => 'Mehr laden',
            'loading'          => 'Lade...',
            'load_more_error'  => 'Weitere Touren konnten nicht geladen werden.',
            'confirm_title'    => 'Bestätigung',
            'confirm_text'     => 'Möchten Sie diese Aktion bestätigen?',
            'yes_confirm'      => 'Ja, bestätigen',
            'no_confirm'       => 'Nein, abbrechen',
            'add_tour'         => 'Tour hinzufügen',
            'edit_tour'        => 'Tour bearbeiten',
            'delete_tour'      => 'Tour löschen',
            'toggle_tour'      => 'Tour aktivieren/deaktivieren',
            'view_cart'        => 'Warenkorb anzeigen',
            'add_to_cart'      => 'Zum Warenkorb hinzufügen',
            'slug_help'        => 'URL-Identifikator der Tour (ohne Leerzeichen und Akzente)',
            'generate_auto'       => 'Automatisch generieren',
            'slug_preview_label'  => 'Vorschau',
            'saved'               => 'Gespeichert',
            // claves extra de UI (ya usadas en el Blade)
            'available_languages'    => 'Verfügbare Sprachen',
            'default_capacity'       => 'Standardkapazität',
            'create_new_schedules'   => 'Neue Zeiten erstellen',
            'multiple_hint_ctrl_cmd' => 'Strg/Cmd gedrückt halten, um mehrere auszuwählen.',
            'use_existing_schedules' => 'Bestehende Zeiten verwenden',
            'add_schedule'           => 'Zeit hinzufügen',
            'schedules_title'        => 'Tour-Zeiten',
            'amenities_included'     => 'Inklusive Ausstattungen',
            'amenities_excluded'     => 'Nicht inkludierte Ausstattungen',
            'color'                  => 'Tour-Farbe',
            'remove'                 => 'Entfernen',
            'delete'                 => 'Löschen',
            'choose_itinerary'       => 'Reiseplan wählen',
            'select_type'            => 'Typ auswählen',
            'empty_means_default'    => 'Standard',
            'actives'                 => 'Aktive',
            'inactives'               => 'Inaktive',
            'archived'                => 'Archivierte',
            'all'                     => 'Alle',
            'help_title'              => 'Hilfe',
            'amenities_included_hint' => 'Wählen Sie aus, was in der Tour enthalten ist.',
            'amenities_excluded_hint' => 'Wählen Sie aus, was in der Tour NICHT enthalten ist.',
            'help_included_title'     => 'Inklusive',
            'help_included_text'      => 'Markieren Sie alles, was im Tourpreis enthalten ist (Transport, Mahlzeiten, Eintrittsgelder, Ausrüstung, Guide usw.).',
            'help_excluded_title'     => 'Nicht inklusive',
            'help_excluded_text'      => 'Markieren Sie alles, was der Kunde separat bezahlen oder mitbringen muss (Trinkgelder, alkoholische Getränke, Souvenirs usw.).',
            'select_or_create_title' => 'Reiseplan auswählen oder erstellen',
            'select_existing_items'  => 'Bestehende Elemente auswählen',
            'name_hint'              => 'Interner Name für diesen Reiseplan',
            'click_add_item_hint'    => 'Klicken Sie auf „Element hinzufügen“, um neue Elemente zu erstellen.',
            'scroll_hint' => 'Horizontal scrollen, um weitere Spalten zu sehen.',
            'no_schedules' => 'Keine Zeiten',
            'no_prices' => 'Keine Preise konfiguriert',

            // Preis-Badges
            'prices_by_period' => 'Preise nach Zeitraum',
            'period' => 'Zeitraum',
            'periods' => 'Zeiträume',
            'all_year' => 'Ganzjährig',
            'from' => 'Von',
            'until' => 'Bis',
            'no_prices' => 'Keine Preise',

            'edit' => 'Bearbeiten',
            'slug_auto' => 'Wird automatisch generiert',
            'deactivate' => 'Deaktivieren',
            'manage_prices' => 'Preise verwalten',
            'manage_images' => 'Bilder verwalten',
            'manage_delete' => 'Löschen',
            'added_to_cart' => 'Zum Warenkorb hinzugefügt',
            'add_language' => 'Sprache hinzufügen',
            'added_to_cart_text' => 'Die Tour wurde erfolgreich zum Warenkorb hinzugefügt.',
            'amenities_excluded_auto_hint'    => 'Standardmäßig werden alle Ausstattungen, die Sie nicht als „inkludiert“ markiert haben, als „nicht inkludiert“ markiert. Sie können die Einträge anpassen.',
            "quick_create_language_hint" => "Fügen Sie schnell eine neue Sprache hinzu, wenn sie nicht in der Liste erscheint.",
            "quick_create_type_hint" => "Fügen Sie schnell einen neuen Tourtyp hinzu, wenn er nicht in der Liste erscheint.",

            'none' => [
                'amenities'       => 'Keine Ausstattungen',
                'exclusions'      => 'Keine Ausschlüsse',
                'itinerary'       => 'Kein Reiseplan',
                'itinerary_items' => 'Keine Elemente',
                'languages'       => 'Keine Sprachen',
                'schedules'       => 'Keine Zeiten',
            ],

            // NUEVO: acciones de archivado/restauración/purga
            'archive' => 'Archivieren',
            'restore' => 'Wiederherstellen',
            'purge'   => 'Endgültig löschen',

            'confirm_archive_title' => 'Tour archivieren?',
            'confirm_archive_text'  => 'Die Tour wird für neue Buchungen deaktiviert, bestehende Buchungen bleiben erhalten.',
            'confirm_purge_title'   => 'Endgültig löschen',
            'confirm_purge_text'    => 'Diese Aktion ist unwiderruflich und nur möglich, wenn die Tour nie Buchungen hatte.',

            // Filtros de estado
            'filters' => [
                'active'   => 'Aktive',
                'inactive' => 'Inaktive',
                'archived' => 'Archivierte',
                'all'      => 'Alle',
            ],

            // Toolbar de fuente (usado en tourlist.blade.php)
            'font_decrease_title' => 'Schriftgröße verringern',
            'font_increase_title' => 'Schriftgröße erhöhen',
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
        'upload_truncated'    => 'Einige Dateien wurden aufgrund des Tour-Limits ausgelassen.',
        'done'                => 'Fertig',
        'notice'              => 'Hinweis',
        'saved'               => 'Gespeichert',
        'caption_updated'     => 'Beschriftung erfolgreich aktualisiert.',
        'deleted'             => 'Gelöscht',
        'image_removed'       => 'Bild erfolgreich gelöscht.',
        'invalid_order'       => 'Ungültige Reihenfolge.',
        'nothing_to_reorder'  => 'Nichts zum Neuordnen.',
        'order_saved'         => 'Reihenfolge gespeichert.',
        'cover_updated_title' => 'Titelbild aktualisieren',
        'cover_updated_text'  => 'Dieses Bild ist jetzt das Titelbild.',
        'deleting'            => 'Lösche...',

        'ui' => [
            // Página de selección de tour
            'page_title_pick'     => 'Tour-Bilder',
            'page_heading'        => 'Tour-Bilder',
            'choose_tour'         => 'Tour auswählen',
            'search_placeholder'  => 'Nach ID oder Name suchen…',
            'search_button'       => 'Suchen',
            'no_results'          => 'Es wurden keine Touren gefunden.',
            'manage_images'       => 'Bilder verwalten',
            'cover_alt'           => 'Titelbild',
            'images_label'        => 'Bilder',

            // Botones genéricos
            'upload_btn'          => 'Hochladen',
            'delete_btn'          => 'Löschen',
            'show_btn'            => 'Anzeigen',
            'close_btn'           => 'Schließen',
            'preview_title'       => 'Bildvorschau',

            // Textos generales de estado
            'error_title'         => 'Fehler',
            'warning_title'       => 'Achtung',
            'success_title'       => 'Erfolg',
            'cancel_btn'          => 'Abbrechen',

            // Confirmaciones básicas
            'confirm_delete_title' => 'Dieses Bild löschen?',
            'confirm_delete_text'  => 'Diese Aktion kann nicht rückgängig gemacht werden.',

            // Gestión de portada por formulario clásico
            'cover_current_title'    => 'Aktuelles Titelbild',
            'upload_new_cover_title' => 'Neues Titelbild hochladen',
            'cover_file_label'       => 'Titelbild-Datei',
            'file_help_cover'        => 'JPEG/PNG/WebP, max. 30 MB.',
            'id_label'               => 'ID',

            // Navegación / cabecera en vista de un tour
            'back_btn'          => 'Zurück zur Liste',

            // Stats (barra superior)
            'stats_images'      => 'Hochgeladene Bilder',
            'stats_cover'       => 'Definierte Titelbilder',
            'stats_selected'    => 'Ausgewählt',

            // Zona de subida
            'drag_or_click'     => 'Ziehen Sie Ihre Bilder hierher oder klicken Sie, um auszuwählen.',
            'upload_help'       => 'Erlaubte Formate: JPG, PNG, WebP. Gesamte Maximalgröße 100 MB.',
            'select_btn'        => 'Dateien auswählen',
            'limit_badge'       => 'Limit von :max Bildern erreicht',
            'files_word'        => 'Dateien',

            // Toolbar de selección múltiple
            'select_all'        => 'Alle auswählen',
            'delete_selected'   => 'Ausgewählte löschen',
            'delete_all'        => 'Alle löschen',

            // Selector por imagen (chip)
            'select_image_title' => 'Dieses Bild auswählen',
            'select_image_aria'  => 'Bild :id auswählen',

            // Portada (chip / botón por tarjeta)
            'cover_label'       => 'Titelbild',
            'cover_btn'         => 'Als Titelbild festlegen',

            // Estados de guardado / helpers JS
            'caption_placeholder' => 'Beschriftung (optional)',
            'saving_label'        => 'Speichere…',
            'saving_fallback'     => 'Speichere…',
            'none_label'          => 'Keine Beschriftung',
            'limit_word'          => 'Limit',

            // Confirmaciones avanzadas (JS)
            'confirm_set_cover_title' => 'Als Titelbild festlegen?',
            'confirm_set_cover_text'  => 'Dieses Bild wird das Haupttitelbild der Tour.',
            'confirm_btn'             => 'Ja, fortfahren',

            'confirm_bulk_delete_title' => 'Ausgewählte Bilder löschen?',
            'confirm_bulk_delete_text'  => 'Die ausgewählten Bilder werden dauerhaft gelöscht.',

            'confirm_delete_all_title'  => 'Alle Bilder löschen?',
            'confirm_delete_all_text'   => 'Alle Bilder dieser Tour werden gelöscht.',

            // Vista sin imágenes
            'no_images'           => 'Für diese Tour sind noch keine Bilder vorhanden.',
        ],

        'errors' => [
            'validation'     => 'Die übermittelten Daten sind ungültig.',
            'upload_generic' => 'Einige Bilder konnten nicht hochgeladen werden.',
            'update_caption' => 'Die Beschriftung konnte nicht aktualisiert werden.',
            'delete'         => 'Das Bild konnte nicht gelöscht werden.',
            'reorder'        => 'Die Reihenfolge konnte nicht gespeichert werden.',
            'set_cover'      => 'Das Titelbild konnte nicht festgelegt werden.',
            'load_list'      => 'Die Liste konnte nicht geladen werden.',
            'too_large'      => 'Die Datei überschreitet die maximal zulässige Größe. Bitte verwenden Sie ein kleineres Bild.',
        ],
    ],

    'prices' => [
        'ui' => [
            'page_title'         => 'Preise – :name',
            'header_title'       => 'Preise: :name',
            'back_to_tours'      => 'Zurück zu den Touren',

            'configured_title'   => 'Konfigurierte Kategorien und Preise',
            'empty_title'        => 'Für diese Tour sind keine Kategorien konfiguriert.',
            'empty_hint'         => 'Verwenden Sie das Formular rechts, um Kategorien hinzuzufügen.',

            'save_changes'       => 'Änderungen speichern',
            'auto_disable_note'  => 'Preise von $0 werden automatisch deaktiviert.',

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
            'rule_zero_disable'  => 'Preise von $0 werden automatisch deaktiviert.',
            'rule_only_active'   => 'Nur aktive Kategorien werden auf der öffentlichen Seite angezeigt.',
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
            'create_disabled_hint' => 'Wenn der Preis $0 ist, wird die Kategorie deaktiviert erstellt.',
            'add'                 => 'Hinzufügen',
        ],

        'modal' => [
            'delete_title'   => 'Kategorie löschen',
            'delete_text'    => 'Diese Kategorie von dieser Tour entfernen?',
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
            'auto_disabled_tooltip' => 'Preis $0 – automatisch deaktiviert',
            'fix_errors'            => 'Bitte korrigieren Sie die Minimal- und Maximalwerte.',
        ],
        'quick_category' => [
            'title'                 => 'Schnelle Kategorie erstellen',
            'button'                => 'Neue Kategorie',
            'go_to_index'           => 'Alle Kategorien anzeigen',
            'go_to_index_title'     => 'Komplette Kategorienliste öffnen',
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
            'no_limit'              => 'Leer für ohne Limit',
        ],

        'validation' => [
            'title' => 'Preisvalidierung',
            'no_categories' => 'Sie müssen mindestens eine Preiskategorie hinzufügen.',
            'no_price_greater_zero' => 'Es muss mindestens eine Kategorie mit einem Preis größer als $0,00 geben.',
            'price_required' => 'Der Preis ist erforderlich.',
            'price_min' => 'Der Preis muss größer oder gleich 0 sein.',
            'age_to_greater_equal' => 'Das Feld „Alter bis“ muss größer oder gleich dem Feld „Alter von“ sein.',
        ],

        'alerts' => [
            'price_updated' => 'Preis erfolgreich aktualisiert',
            'price_created' => 'Kategorie erfolgreich zum Zeitraum hinzugefügt',
            'price_deleted' => 'Preis erfolgreich gelöscht',
            'status_updated' => 'Status aktualisiert',
            'period_updated' => 'Zeitraumdaten aktualisiert',
            'period_deleted' => 'Zeitraum erfolgreich gelöscht',

            'error_title' => 'Fehler',
            'error_unexpected' => 'Ein unerwarteter Fehler ist aufgetreten',
            'error_delete_price' => 'Der Preis konnte nicht gelöscht werden',
            'error_add_category' => 'Die Kategorie konnte nicht hinzugefügt werden',
            'error_update_period' => 'Die Zeitraumdaten konnten nicht aktualisiert werden',

            'attention' => 'Achtung',
            'select_category_first' => 'Wählen Sie zuerst eine Kategorie aus',
            'duplicate_category_title' => 'Doppelte Kategorie',
            'duplicate_category_text' => 'Diese Kategorie ist bereits in diesem Zeitraum enthalten',

            'confirm_delete_price_title' => 'Preis löschen?',
            'confirm_delete_price_text' => 'Diese Aktion kann nicht rückgängig gemacht werden.',
            'confirm_delete_period_title' => 'Diesen Zeitraum löschen?',
            'confirm_delete_period_text' => 'Alle mit diesem Zeitraum verbundenen Preise werden gelöscht.',
            'confirm_yes_delete' => 'Ja, löschen',
            'confirm_cancel' => 'Abbrechen',

            'no_categories' => 'Dieser Zeitraum hat keine Kategorien',
        ],
    ],

    'ajax' => [
        'category_created' => 'Kategorie erfolgreich erstellt.',
        'category_error' => 'Fehler beim Erstellen der Kategorie.',
        'language_created' => 'Sprache erfolgreich erstellt.',
        'language_error' => 'Fehler beim Erstellen der Sprache.',
        'amenity_created' => 'Ausstattung erfolgreich erstellt.',
        'amenity_error' => 'Fehler beim Erstellen der Ausstattung.',
        'schedule_created' => 'Zeit erfolgreich erstellt.',
        'schedule_error' => 'Fehler beim Erstellen der Zeit.',
        'itinerary_created' => 'Reiseplan erfolgreich erstellt.',
        'itinerary_error' => 'Fehler beim Erstellen des Reiseplans.',
        'translation_error' => 'Fehler bei der Übersetzung.',
    ],

    'modal' => [
        'create_category' => 'Neue Kategorie erstellen',
        'create_language' => 'Neue Sprache erstellen',
        'create_amenity' => 'Neue Ausstattung erstellen',
        'create_schedule' => 'Neue Zeit erstellen',
        'create_itinerary' => 'Neuen Reiseplan erstellen',
    ],

    'validation' => [
        'slug_taken' => 'Dieser Slug wird bereits verwendet.',
        'slug_available' => 'Slug verfügbar.',
    ],
    'tour_type' => [
        'fields' => [
            'name' => 'Name',
            'description' => 'Beschreibung',
            'status' => 'Status',
            'duration' => 'Dauer',
            'duration_hint' => 'Vorgeschlagene Tourdauer (optional)',
            'duration_placeholder' => 'Beispiel: 4 Stunden, 6 Stunden usw.',
        ],
    ],
];
