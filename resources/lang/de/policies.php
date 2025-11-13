<?php

return [

    // =========================================================
    // [00] GENERISCH
    // =========================================================
    'page_title'  => 'Richtlinien',
    'no_policies' => 'Derzeit sind keine Richtlinien verfügbar.',
    'no_sections' => 'Derzeit sind keine Abschnitte verfügbar.',
    'propagate_to_all_langs' => 'Diese Änderung auf alle Sprachen übertragen (EN, FR, DE, PT)',
    'propagate_hint'         => 'Der Text wird automatisch aus dem aktuellen Inhalt übersetzt und vorhandene Übersetzungen in diesen Sprachen werden überschrieben.',
    'update_base_es'         => 'Basis (ES) ebenfalls aktualisieren',
    'update_base_hint'       => 'Überschreibt Name und Inhalt der Richtlinie in der Basistabelle (Spanisch). Verwende dies nur, wenn du auch den Originaltext ändern möchtest.',

    // =========================================================
    // [01] CHECKOUT
    // =========================================================
    'checkout' => [
        'card_title'  => 'Deine Bestellung',
        'details'     => 'Details',
        'must_accept' => 'Du musst alle Richtlinien lesen und akzeptieren, um mit der Zahlung fortzufahren.',
        'accept_label_html' =>
            'Ich habe die <strong>Allgemeinen Geschäftsbedingungen</strong>, die <strong>Datenschutzerklärung</strong> sowie alle <strong>Stornierungs-, Erstattungs- und Garantie-Richtlinien</strong> gelesen und akzeptiere sie.',
        'back'       => 'Zurück',
        'pay'        => 'Zahlung ausführen',
        'order_full' => 'Vollständige Bestelldetails',

        'titles' => [
            'terms'        => 'Allgemeine Geschäftsbedingungen',
            'privacy'      => 'Datenschutzerklärung',
            'cancellation' => 'Stornierungsrichtlinie',
            'refunds'      => 'Erstattungsrichtlinie',
            'warranty'     => 'Garantierichtlinie',
            'payments'     => 'Zahlungsmethoden',
        ],
    ],

    // =========================================================
    // [02] FELDER
    // =========================================================
    'fields' => [
        'title'       => 'Titel',
        'description' => 'Beschreibung',
        'type'        => 'Typ',
        'is_active'   => 'Aktiv',
    ],

    // =========================================================
    // [03] TYPEN
    // =========================================================
    'types' => [
        'cancellation' => 'Stornierungsrichtlinie',
        'refund'       => 'Erstattungsrichtlinie',
        'terms'        => 'Allgemeine Geschäftsbedingungen',
    ],

    // =========================================================
    // [04] MELDUNGEN
    // =========================================================
    'success' => [
        'created' => 'Richtlinie erfolgreich erstellt.',
        'updated' => 'Richtlinie erfolgreich aktualisiert.',
        'deleted' => 'Richtlinie erfolgreich gelöscht.',
    ],

    'error' => [
        'create' => 'Die Richtlinie konnte nicht erstellt werden.',
        'update' => 'Die Richtlinie konnte nicht aktualisiert werden.',
        'delete' => 'Die Richtlinie konnte nicht gelöscht werden.',
    ],
];
