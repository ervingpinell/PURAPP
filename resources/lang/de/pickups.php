<?php

return [

    'hotels' => [

        'title'             => 'Hotelliste',
        'header'            => 'Registrierte Hotels',
        'sort_alpha'        => 'Alphabetisch sortieren',

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

        'confirm_activate_title'    => 'Hotel aktivieren?',
        'confirm_activate_text'     => 'Möchten Sie ":name" wirklich aktivieren?',
        'confirm_deactivate_title'  => 'Hotel deaktivieren?',
        'confirm_deactivate_text'   => 'Möchten Sie ":name" wirklich deaktivieren?',
        'confirm_delete_title'      => 'Dauerhaft löschen?',
        'confirm_delete_text'       => '":name" wird gelöscht. Diese Aktion kann nicht rückgängig gemacht werden.',

        'created_success'    => 'Hotel erfolgreich erstellt.',
        'updated_success'    => 'Hotel erfolgreich aktualisiert.',
        'deleted_success'    => 'Hotel erfolgreich gelöscht.',
        'activated_success'  => 'Hotel erfolgreich aktiviert.',
        'deactivated_success'=> 'Hotel erfolgreich deaktiviert.',
        'sorted_success'     => 'Hotels alphabetisch sortiert.',
        'unexpected_error'   => 'Ein unerwarteter Fehler ist aufgetreten. Bitte versuchen Sie es erneut.',

        'validation' => [
            'name_required' => 'Der Name ist erforderlich.',
            'name_unique'   => 'Dieses Hotel existiert bereits in der Liste.',
            'name_max'      => 'Der Name darf 255 Zeichen nicht überschreiten.',
        ],
        'error_title' => 'Fehler',

        'edit_title' => 'Hotel bearbeiten',
    ],

    'meeting_point' => [

        'ui' => [
            'page_title'   => 'Treffpunkte',
            'page_heading' => 'Treffpunkte',
        ],

        'badges' => [
            'count_badge' => ':count Einträge',
            'active'      => 'Aktiv',
            'inactive'    => 'Inaktiv',
        ],

        'create' => [
            'title' => 'Treffpunkt hinzufügen',
        ],

        'list' => [
            'title' => 'Liste',
            'empty' => 'Keine Einträge vorhanden. Erstellen Sie den ersten oben.',
        ],

        'labels' => [
            'time'       => 'Uhrzeit',
            'sort_order' => 'Reihenfolge',
        ],

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

            'name_base'               => 'Name (Basis)',
            'description_base'        => 'Beschreibung (Basis)',
            'locale'                  => 'Sprache',
            'name_translation'        => 'Name (Übersetzung)',
            'description_translation' => 'Beschreibung (Übersetzung)',
        ],

        'placeholders' => [
            'name'        => 'Zentralpark von La Fortuna',
            'pickup_time' => '07:10 Uhr',
            'description' => 'Zentrum von La Fortuna',
            'map_url'     => 'https://maps.google.com/...',
            'search'      => 'Suchen…',
            'optional'    => 'Optional',
        ],

        'hints' => [
            'name_example'   => 'Beispiel: „Zentralpark von La Fortuna“. ',
            'name_base_sync' => 'Wenn Sie es nicht ändern, bleibt es bestehen. Sprachspezifische Namen werden unten bearbeitet.',
            'fallback_sync'  => 'Wenn Sie das Gebietsschema <strong>:fallback</strong> wählen, wird es auch mit den Basisfeldern synchronisiert.',
        ],

        'buttons' => [
            'reload'       => 'Neu laden',
            'save'         => 'Speichern',
            'clear'        => 'Leeren',
            'create'       => 'Erstellen',
            'cancel'       => 'Abbrechen',
            'save_changes' => 'Änderungen speichern',
            'close'        => 'Schließen',
            'ok'           => 'OK',
            'confirm'      => 'Ja, fortfahren',
            'delete'       => 'Löschen',
            'activate'     => 'Aktivieren',
            'deactivate'   => 'Deaktivieren',
        ],

        'actions' => [
            'view_map'    => 'Karte ansehen',
            'view_on_map' => 'Auf Karte anzeigen',
            'edit'        => 'Bearbeiten',
            'delete'      => 'Löschen',
            'activate'    => 'Aktivieren',
            'deactivate'  => 'Deaktivieren',
        ],

        'confirm' => [
            'create_title'             => 'Neuen Treffpunkt erstellen?',
            'create_text_with_name'    => '":name" wird erstellt.',
            'create_text'              => 'Ein neuer Treffpunkt wird erstellt.',
            'save_title'               => 'Änderungen speichern?',
            'save_text'                => 'Der Treffpunkt und die Übersetzung werden aktualisiert.',
            'deactivate_title'         => 'Treffpunkt deaktivieren?',
            'deactivate_title_short'   => 'Deaktivieren?',
            'deactivate_text'          => '":name" wird inaktiv.',
            'activate_title'           => 'Treffpunkt aktivieren?',
            'activate_title_short'     => 'Aktivieren?',
            'activate_text'            => '":name" wird aktiv.',
            'delete_title'             => 'Treffpunkt löschen?',
            'delete_title_short'       => 'Löschen?',
            'delete_text'              => '":name" wird dauerhaft gelöscht. Diese Aktion kann nicht rückgängig gemacht werden.',
        ],

        'validation' => [
            'title'                         => 'Validierungsfehler',
            'missing_translated_name_title' => 'Fehlender übersetzter Name',
            'missing_translated_name_text'  => 'Bitte füllen Sie das Feld für den übersetzten Namen aus.',
        ],

        'toasts' => [
            'success_title' => 'Erfolg',
            'error_title'   => 'Fehler',
        ],
    ],

];
