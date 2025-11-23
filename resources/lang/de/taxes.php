<?php

return [
    'title' => 'Steuerverwaltung',
    'create' => 'Steuer erstellen',
    'edit' => 'Steuer bearbeiten',
    'fields' => [
        'name' => 'Name',
        'code' => 'Code',
        'rate' => 'Satz/Betrag',
        'type' => 'Typ',
        'apply_to' => 'Anwenden auf',
        'is_inclusive' => 'Inklusive',
        'is_active' => 'Aktiv',
        'sort_order' => 'Reihenfolge',
    ],
    'types' => [
        'percentage' => 'Prozentsatz (%)',
        'fixed' => 'Fester Betrag ($)',
    ],
    'apply_to_options' => [
        'subtotal' => 'Zwischensumme',
        'total' => 'Gesamt (Kaskadierend)',
        'per_person' => 'Pro Person',
    ],
    'messages' => [
        'created' => 'Steuer erfolgreich erstellt.',
        'updated' => 'Steuer erfolgreich aktualisiert.',
        'deleted' => 'Steuer erfolgreich gelöscht.',
        'toggled' => 'Steuerstatus aktualisiert.',
        'select_taxes' => 'Wählen Sie die Steuern aus, die für diese Tour gelten.',
    ],
];
