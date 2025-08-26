<?php

return [
    // Titel / Überschriften
    'categories_title'        => 'Richtlinienkategorien',
    'sections_title'          => 'Abschnitte — :policy',

    // Spalten / allgemeine Felder
    'id'                      => 'ID',
    'internal_name'           => 'Interner Name',
    'title_current_locale'    => 'Titel (aktuelle Sprache)',
    'validity_range'          => 'Gültigkeitszeitraum',
    'valid_from'              => 'Gültig ab',
    'valid_to'                => 'Gültig bis',
    'status'                  => 'Status',
    'sections'                => 'Abschnitte',
    'actions'                 => 'Aktionen',
    'active'                  => 'Aktiv',
    'inactive'                => 'Inaktiv',

    // Kategorienliste: Aktionen
    'new_category'            => 'Neue Kategorie',
    'view_sections'           => 'Abschnitte anzeigen',
    'edit'                    => 'Bearbeiten',
    'activate_category'       => 'Kategorie aktivieren',
    'deactivate_category'     => 'Kategorie deaktivieren',
    'delete'                  => 'Löschen',
    'delete_category_confirm' => 'Kategorie und ALLE ihre Abschnitte löschen?',
    'no_categories'           => 'Keine Kategorien vorhanden.',
    'edit_category'           => 'Kategorie bearbeiten',

    // Formulare (Kategorie)
    'title_label'             => 'Titel',
    'description_label'       => 'Beschreibung',
    'register'                => 'Erstellen',
    'save_changes'            => 'Änderungen speichern',
    'close'                   => 'Schließen',

    // Abschnitte
    'back_to_categories'      => 'Zurück zu den Kategorien',
    'new_section'             => 'Neuer Abschnitt',
    'key'                     => 'Schlüssel',
    'order'                   => 'Reihenfolge',
    'activate_section'        => 'Abschnitt aktivieren',
    'deactivate_section'      => 'Abschnitt deaktivieren',
    'delete_section_confirm'  => 'Diesen Abschnitt löschen?',
    'no_sections'             => 'Keine Abschnitte vorhanden.',
    'edit_section'            => 'Abschnitt bearbeiten',
    'internal_key_optional'   => 'Interner Schlüssel (optional)',
    'content_label'           => 'Inhalt',

    // Öffentlich
    'page_title'              => 'Richtlinien',
    'no_policies'             => 'Derzeit sind keine Richtlinien verfügbar.',
    'section'                 => 'Abschnitt',
    'cancellation_policy'     => 'Stornierungsrichtlinie',
    'refund_policy'           => 'Erstattungsrichtlinie',
    'no_cancellation_policy'  => 'Keine Stornierungsrichtlinie konfiguriert.',
    'no_refund_policy'        => 'Keine Erstattungsrichtlinie konfiguriert.',

    // Meldungen (Kategorien)
    'category_created'        => 'Kategorie erfolgreich erstellt.',
    'category_updated'        => 'Kategorie erfolgreich aktualisiert.',
    'category_activated'      => 'Kategorie erfolgreich aktiviert.',
    'category_deactivated'    => 'Kategorie erfolgreich deaktiviert.',
    'category_deleted'        => 'Kategorie erfolgreich gelöscht.',

    // --- NEUE SCHLÜSSEL (Refactor / Modul) ---
    'untitled'                => 'Ohne Titel',
    'no_content'              => 'Kein Inhalt verfügbar.',
    'display_name'            => 'Anzeigename',
    'name'                    => 'Name',
    'name_base'               => 'Basisname',
    'name_base_help'          => 'Kurzer Bezeichner/Slug für den Abschnitt (nur intern).',
    'translation_content'     => 'Übersetzter Inhalt',
    'locale'                  => 'Sprache',
    'save'                    => 'Speichern',
    'name_base_label'         => 'Basisname',
    'translation_name'        => 'Übersetzter Name',
    'lang_autodetect_hint'    => 'Sie können in jeder Sprache schreiben; sie wird automatisch erkannt.',
    'bulk_edit_sections'      => 'Schnellbearbeitung von Abschnitten',
    'bulk_edit_hint'          => 'Änderungen an allen Abschnitten werden zusammen mit der Übersetzung der Kategorie gespeichert, wenn Sie auf „Speichern“ klicken.',
    'no_changes_made'         => 'Keine Änderungen vorgenommen.',
    'no_sections_found'       => 'Keine Abschnitte gefunden.',

    // Meldungen (Abschnitte)
    'section_created'         => 'Abschnitt erfolgreich erstellt.',
    'section_updated'         => 'Abschnitt erfolgreich aktualisiert.',
    'section_activated'       => 'Abschnitt erfolgreich aktiviert.',
    'section_deactivated'     => 'Abschnitt erfolgreich deaktiviert.',
    'section_deleted'         => 'Abschnitt erfolgreich gelöscht.',

    // Generische Modulmeldungen
    'created_success'         => 'Erfolgreich erstellt.',
    'updated_success'         => 'Erfolgreich aktualisiert.',
    'deleted_success'         => 'Erfolgreich gelöscht.',
    'activated_success'       => 'Erfolgreich aktiviert.',
    'deactivated_success'     => 'Erfolgreich deaktiviert.',
    'unexpected_error'        => 'Es ist ein unerwarteter Fehler aufgetreten.',

    // Buttons / allgemeine Texte (SweetAlert)
    'create'                  => 'Erstellen',
    'activate'                => 'Aktivieren',
    'deactivate'              => 'Deaktivieren',
    'cancel'                  => 'Abbrechen',
    'ok'                      => 'OK',
    'validation_errors'       => 'Es liegen Validierungsfehler vor',
    'error_title'             => 'Fehler',

    // Abschnitts-spezifische Bestätigungen
    'confirm_create_section'      => 'Diesen Abschnitt erstellen?',
    'confirm_edit_section'        => 'Änderungen am Abschnitt speichern?',
    'confirm_delete_section'      => 'Möchten Sie diesen Abschnitt wirklich löschen?',
    'confirm_deactivate_section'  => 'Möchten Sie diesen Abschnitt wirklich deaktivieren?',
    'confirm_activate_section'    => 'Möchten Sie diesen Abschnitt wirklich aktivieren?',
];
