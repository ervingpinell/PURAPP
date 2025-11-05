<?php

return [
    'ui' => [
        'page_title_index'  => 'Kundenkategorien',
        'page_title_create' => 'Neue Kundenkategorie',
        'page_title_edit'   => 'Kategorie bearbeiten',
        'header_index'      => 'Kundenkategorien',
        'header_create'     => 'Neue Kundenkategorie',
        'header_edit'       => 'Kategorie bearbeiten: :name',
        'info_card_title'   => 'Informationen',
        'list_title'        => 'Kategorieliste',
        'empty_list'        => 'Keine Kategorien registriert.',
    ],

    'buttons' => [
        'new_category' => 'Neue Kategorie',
        'save'         => 'Speichern',
        'update'       => 'Aktualisieren',
        'cancel'       => 'Abbrechen',
        'back'         => 'Zurück',
        'delete'       => 'Löschen',
        'edit'         => 'Bearbeiten',
    ],

    'table' => [
        'name'     => 'Name',
        'age_from' => 'Alter von',
        'age_to'   => 'Alter bis',
        'range'    => 'Bereich',
        'active'   => 'Aktiv',
        'actions'  => 'Aktionen',
        'order'    => 'Reihenfolge',
        'slug'     => 'Slug',
    ],

    'form' => [
        'name' => [
            'label'       => 'Name',
            'placeholder' => 'Z. B.: Erwachsener, Kind, Säugling',
            'required'    => 'Der Name ist erforderlich',
        ],
        'slug' => [
            'label'       => 'Slug (eindeutiger Bezeichner)',
            'placeholder' => 'Z. B.: adult, child, infant',
            'title'       => 'Nur Kleinbuchstaben, Zahlen, Bindestriche und Unterstriche',
            'helper'      => 'Nur Kleinbuchstaben, Zahlen, Bindestriche (-) und Unterstriche (_)',
        ],
        'age_from' => [
            'label'       => 'Alter von',
            'placeholder' => 'Z. B.: 0, 3, 13, 65',
        ],
        'age_to' => [
            'label'         => 'Alter bis',
            'placeholder'   => 'Z. B.: 2, 12, 64 (leer lassen für „kein Limit“)',
            'hint_no_limit' => 'leer lassen für „kein Limit“',
        ],
        'order' => [
            'label'  => 'Anzeigereihenfolge',
            'helper' => 'Bestimmt die Reihenfolge der Anzeige (kleiner = zuerst)',
        ],
        'active' => [
            'label'  => 'Aktive Kategorie',
            'helper' => 'Nur aktive Kategorien werden in Buchungsformularen angezeigt',
        ],
        'min_per_booking' => [
            'label'       => 'Minimum pro Buchung',
            'placeholder' => 'Z. B.: 0, 1',
        ],
        'max_per_booking' => [
            'label'       => 'Maximum pro Buchung',
            'placeholder' => 'Z. B.: 10 (leer lassen für „kein Limit“)',
        ],
    ],

    'states' => [
        'active'   => 'Aktiv',
        'inactive' => 'Inaktiv',
    ],

    'alerts' => [
        'success_created' => 'Kategorie erfolgreich erstellt.',
        'success_updated' => 'Kategorie erfolgreich aktualisiert.',
        'success_deleted' => 'Kategorie erfolgreich gelöscht.',
        'warning_title'  => 'Warnung',
        'warning_text'   => 'Das Löschen einer Kategorie, die in Touren oder Buchungen verwendet wird, kann Probleme verursachen. Es wird empfohlen, sie zu deaktivieren, anstatt sie zu löschen.',
    ],

    'dialogs' => [
        'delete' => [
            'title'   => 'Löschung bestätigen',
            'text'    => 'Sind Sie sicher, dass Sie die Kategorie :name löschen möchten?',
            'caution' => 'Diese Aktion kann nicht rückgängig gemacht werden.',
        ],
    ],

    'rules' => [
        'title'                 => 'Wichtige Regeln',
        'no_overlap'            => 'Altersbereiche dürfen sich zwischen aktiven Kategorien nicht überschneiden.',
        'no_upper_limit_hint'   => '„Alter bis“ leer lassen, um „kein oberes Limit“ anzugeben.',
        'slug_unique'           => 'Der Slug muss eindeutig sein.',
        'order_affects_display' => 'Die Reihenfolge bestimmt die Anzeige im System.',
    ],

    'help' => [
        'title'           => 'Hilfe',
        'examples_title'  => 'Beispiele für Kategorien',
        'infant'          => 'Säugling',
        'child'           => 'Kind',
        'adult'           => 'Erwachsener',
        'senior'          => 'Senior',
        'age_from_tip'    => 'Alter von:',
        'age_to_tip'      => 'Alter bis:',
        'range_tip'       => 'Bereich:',
        'notes_title'     => 'Hinweise',
        'notes' => [
            'use_null_age_to' => 'Verwenden Sie age_to = NULL, um „kein oberes Limit“ anzugeben (z. B.: 18+ Jahre).',
            'inactive_hidden' => 'Inaktive Kategorien werden in Buchungsformularen nicht angezeigt.',
        ],
    ],

    'info' => [
        'id'        => 'ID:',
        'created'   => 'Erstellt:',
        'updated'   => 'Aktualisiert:',
        'date_fmt'  => 'd.m.Y H:i',
    ],

    'validation' => [
        'age_to_gte_age_from' => '„Alter bis“ muss größer oder gleich „Alter von“ sein.',
    ],
];
