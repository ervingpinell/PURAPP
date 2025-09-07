<?php

return [

    'page_title'   => 'Richtlinien',
    'no_policies'  => 'Derzeit sind keine Richtlinien verfügbar.',
    'no_sections'  => 'Derzeit sind keine Abschnitte verfügbar.',
    // =========================================================
    // [01] FELDER
    // =========================================================
    'fields' => [
        'title'       => 'Titel',
        'description' => 'Beschreibung',
        'type'        => 'Typ',
        'is_active'   => 'Aktiv',
    ],

    // =========================================================
    // [02] ARTEN
    // =========================================================
    'types' => [
        'cancellation' => 'Stornierungsrichtlinie',
        'refund'       => 'Rückerstattungsrichtlinie',
        'terms'        => 'Allgemeine Geschäftsbedingungen',
    ],

    // =========================================================
    // [03] MELDUNGEN
    // =========================================================
    'success' => [
        'created'   => 'Richtlinie erfolgreich erstellt.',
        'updated'   => 'Richtlinie erfolgreich aktualisiert.',
        'deleted'   => 'Richtlinie erfolgreich gelöscht.',
    ],

    'error' => [
        'create' => 'Richtlinie konnte nicht erstellt werden.',
        'update' => 'Richtlinie konnte nicht aktualisiert werden.',
        'delete' => 'Richtlinie konnte nicht gelöscht werden.',
    ],
];
