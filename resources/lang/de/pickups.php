<?php

return [

    'hotels' => [

        // Titel / Überschriften
        'title'             => 'Hotelliste',
        'header'            => 'Registrierte Hotels',
        'sort_alpha'        => 'Alphabetisch sortieren',

        // Felder / Spalten / Aktionen
        'name'              => 'Name',
        'status'            => 'Status',
        'actions'           => 'Aktionen',
        'active'            => 'Aktiv',
        'inactive'          => 'Inaktiv',
        'add'               => 'Hinzufügen',
        'edit'              => 'Bearbeiten',
        'delete'            => 'Löschen',
        'activate'          => 'Aktivieren',
        'deactivate'        => 'Deaktivieren',
        'save_changes'      => 'Änderungen speichern',
        'cancel'            => 'Abbrechen',
        'close'             => 'Schließen',
        'no_records'        => 'Keine Hotels registriert.',
        'name_placeholder'  => 'Z. B.: Hotel Arenal Springs',

        // Bestätigungen
        'confirm_activate_title'    => 'Hotel aktivieren?',
        'confirm_activate_text'     => 'Möchtest du ":name" wirklich aktivieren?',
        'confirm_deactivate_title'  => 'Hotel deaktivieren?',
        'confirm_deactivate_text'   => 'Möchtest du ":name" wirklich deaktivieren?',
        'confirm_delete_title'      => 'Endgültig löschen?',
        'confirm_delete_text'       => '":name" wird gelöscht. Diese Aktion kann nicht rückgängig gemacht werden.',

        // Meldungen (Flash)
        'created_success'    => 'Hotel erfolgreich erstellt.',
        'updated_success'    => 'Hotel erfolgreich aktualisiert.',
        'deleted_success'    => 'Hotel erfolgreich gelöscht.',
        'activated_success'  => 'Hotel erfolgreich aktiviert.',
        'deactivated_success' => 'Hotel erfolgreich deaktiviert.',
        'sorted_success'     => 'Hotels alphabetisch sortiert.',
        'unexpected_error'   => 'Ein unerwarteter Fehler ist aufgetreten. Bitte versuche es erneut.',

        // Validierung / Generisches
        'validation' => [
            'name_required' => 'Der Name ist erforderlich.',
            'name_unique'   => 'Dieses Hotel existiert bereits in der Liste.',
            'name_max'      => 'Der Name darf 255 Zeichen nicht überschreiten.',
        ],
        'error_title' => 'Fehler',

        // Modale
        'edit_title' => 'Hotel bearbeiten',
    ],

    'meeting_point' => [

        // UI
        'ui' => [
            'page_title'   => 'Treffpunkte',
            'page_heading' => 'Treffpunkte',
        ],

        // Badges
        'badges' => [
            'count_badge' => ':count Einträge',
            'active'      => 'Aktiv',
            'inactive'    => 'Inaktiv',
        ],

        // Erstellen
        'create' => [
            'title' => 'Treffpunkt hinzufügen',
        ],

        // Liste
        'list' => [
            'title' => 'Liste',
            'empty' => 'Keine Einträge vorhanden. Erstelle den ersten oben.',
        ],

        // Kompakte Labels auf Karten
        'labels' => [
            'time'       => 'Uhrzeit',
            'sort_order' => 'Reihenfolge',
        ],

        // Felder
        'fields' => [
            'name'                    => 'Name',
            'pickup_time'             => 'Abholzeit',
            'sort_order'              => 'Reihenfolge',
            'description'             => 'Beschreibung',
            'map_url'                 => 'Karten-URL',
            'active'                  => 'Aktiv',
            'time_short'              => 'Zeit',
            'map'                     => 'Karte',
            'status'                  => 'Status',
            'actions'                 => 'Aktionen',
            'instructions'            => 'Anweisungen',

            // Bearbeitung / Übersetzungen
            'name_base'               => 'Name (Basis)',
            'description_base'        => 'Beschreibung (Basis)',
            'instructions_base'       => 'Anweisungen (Basis)',
            'locale'                  => 'Sprache',
            'name_translation'        => 'Name (Übersetzung)',
            'description_translation' => 'Beschreibung (Übersetzung)',
            'instructions_translation' => 'Anweisungen (Übersetzung)',
        ],

        // Platzhalter
        'placeholders' => [
            'name'         => 'Zentralpark von La Fortuna',
            'pickup_time'  => '7:10 Uhr',
            'description'  => 'Stadtzentrum von La Fortuna',
            'instructions' => 'Bitte treffen Sie uns vor der Kirche im Zentralpark. Unser Guide trägt ein grünes Hemd mit dem :company Logo.',
            'map_url'      => 'https://maps.google.com/...',
            'search'       => 'Suchen…',
            'optional'     => 'Optional',
        ],

        // Hinweise
        'hints' => [
            'name_example'   => 'Z. B.: „Zentralpark von La Fortuna“.',
            'name_base_sync' => 'Wenn du es nicht änderst, bleibt es unverändert. Der Name pro Sprache wird unten bearbeitet.',
            'fallback_sync'  => 'Wenn du das Locale <strong>:fallback</strong> wählst, wird es ebenfalls mit den Basisfeldern synchronisiert.',
        ],

        // Buttons
        'buttons' => [
            'reload'       => 'Neu laden',
            'save'         => 'Speichern',
            'clear'        => 'Leeren',
            'create'       => 'Erstellen',
            'cancel'       => 'Abbrechen',
            'save_changes' => 'Änderungen speichern',
            'close'        => 'Schließen',
            'ok'           => 'Verstanden',
            'confirm'      => 'Ja, fortfahren',
            'delete'       => 'Löschen',
            'activate'     => 'Aktivieren',
            'deactivate'   => 'Deaktivieren',
        ],

        // Aktionen (Titel / Tooltips)
        'actions' => [
            'view_map'    => 'Karte ansehen',
            'view_on_map' => 'Auf Karte ansehen',
            'edit'        => 'Bearbeiten',
            'delete'      => 'Löschen',
            'activate'    => 'Aktivieren',
            'deactivate'  => 'Deaktivieren',
        ],

        // Bestätigungen
        'confirm' => [
            'create_title'             => 'Neuen Treffpunkt erstellen?',
            'create_text_with_name'    => '":name" wird erstellt.',
            'create_text'              => 'Ein neuer Treffpunkt wird erstellt.',

            'save_title'               => 'Änderungen speichern?',
            'save_text'                => 'Der Treffpunkt und die ausgewählte Übersetzung werden aktualisiert.',

            'deactivate_title'         => 'Treffpunkt deaktivieren?',
            'deactivate_title_short'   => 'Deaktivieren?',
            'deactivate_text'          => '":name" wird inaktiv gesetzt.',

            'activate_title'           => 'Treffpunkt aktivieren?',
            'activate_title_short'     => 'Aktivieren?',
            'activate_text'            => '":name" wird aktiv gesetzt.',

            'delete_title'             => 'Treffpunkt löschen?',
            'delete_title_short'       => 'Löschen?',
            'delete_text'              => '":name" wird dauerhaft gelöscht. Diese Aktion kann nicht rückgängig gemacht werden.',
        ],

        // Validierung / Toastr / SweetAlert
        'validation' => [
            'title'                         => 'Validierungsfehler',
            'missing_translated_name_title' => 'Übersetzter Name fehlt',
            'missing_translated_name_text'  => 'Bitte fülle das Feld für den übersetzten Namen aus.',
        ],

        'toasts' => [
            'success_title'        => 'Erfolg',
            'error_title'          => 'Fehler',
            'created_success'      => 'Punkt erfolgreich erstellt.',
            'updated_success'      => 'Punkt erfolgreich aktualisiert.',
            'deleted_success'      => 'Punkt in den Papierkorb verschoben.',
            'activated_success'    => 'Punkt erfolgreich aktiviert.',
            'deactivated_success'  => 'Punkt erfolgreich deaktiviert.',
        ],

        // Papierkorbverwaltung
        'trash' => [
            'title'                => 'Papierkorb',
            'empty'                => 'Keine gelöschten Punkte.',
            'deleted_by'           => 'Gelöscht von',
            'deleted_at'           => 'Gelöscht am',
            'auto_delete_in'       => 'Wird gelöscht in',
            'days'                 => '{1} :count Tag|[2,*] :count Tagen',
            'restore_success'      => 'Punkt erfolgreich wiederhergestellt.',
            'force_delete_success' => 'Punkt dauerhaft gelöscht.',
        ],
    ],

];
