<?php

return [

    // =========================================================
    // [00] ALLGEMEIN
    // =========================================================
    'page_title'  => 'Richtlinien',
    'no_policies' => 'Derzeit sind keine Richtlinien verfügbar.',
    'no_sections' => 'Derzeit sind keine Abschnitte verfügbar.',

    // =========================================================
    // [01] CHECKOUT
    // =========================================================
    'checkout' => [
        'card_title'  => 'Deine Bestellung',
        'details'     => 'Details',
        'must_accept' => 'Du musst alle Richtlinien lesen und akzeptieren, um mit der Zahlung fortzufahren.',
        'accept_label_html' =>
            'Ich habe die <strong>Allgemeinen Geschäftsbedingungen</strong>, die <strong>Datenschutzrichtlinie</strong> sowie alle <strong>Richtlinien zu Stornierung, Rückerstattung und Garantie</strong> gelesen und akzeptiere sie.',
        'back'       => 'Zurück',
        'pay'        => 'Zur Zahlung fortfahren',
        'order_full' => 'Vollständige Bestelldetails',

        'version' => [
            'terms'   => 'v1',
            'privacy' => 'v1',
        ],

        'titles' => [
            'terms'        => 'Allgemeine Geschäftsbedingungen',
            'privacy'      => 'Datenschutzrichtlinie',
            'cancellation' => 'Stornierungsrichtlinie',
            'refunds'      => 'Rückerstattungsrichtlinie',
            'warranty'     => 'Garantierichtlinie',
            'payments'     => 'Zahlungsmethoden',
        ],

        'bodies' => [
                'terms_html' => <<<HTML
        <p>Diese Bedingungen regeln den Kauf von Touren und Dienstleistungen, die von Green Vacations CR angeboten werden.</p>
        <ul>
        <li><strong>Geltungsbereich:</strong> Der Kauf gilt ausschließlich für die aufgelisteten Leistungen zu den ausgewählten Daten und Zeiten.</li>
        <li><strong>Preise und Gebühren:</strong> Preise werden in USD angegeben und enthalten ggf. Steuern. Zusätzliche Gebühren werden vor der Zahlung mitgeteilt.</li>
        <li><strong>Kapazität und Verfügbarkeit:</strong> Buchungen unterliegen der Verfügbarkeit und Kapazitätsprüfungen.</li>
        <li><strong>Änderungen:</strong> Änderungen von Datum/Uhrzeit sind von der Verfügbarkeit abhängig und können Preisunterschiede verursachen.</li>
        <li><strong>Haftung:</strong> Die Leistungen werden gemäß den geltenden costa-ricanischen Vorschriften erbracht.</li>
        </ul>
        HTML,
                'privacy_html' => <<<HTML
        <p>Wir verarbeiten personenbezogene Daten gemäß den geltenden Vorschriften. Wir erheben nur die Daten, die zur Verwaltung von Buchungen, Zahlungen und der Kundenkommunikation erforderlich sind.</p>
        <ul>
        <li><strong>Nutzung der Informationen:</strong> Abwickeln des Kaufs, Kundensupport, betriebliche Benachrichtigungen und rechtliche Compliance.</li>
        <li><strong>Weitergabe:</strong> Wir verkaufen oder handeln nicht mit personenbezogenen Daten.</li>
        <li><strong>Rechte:</strong> Sie können Ihre Rechte auf Auskunft, Berichtigung, Widerspruch und Löschung über unsere Kontaktkanäle ausüben.</li>
        </ul>
        HTML,
                'cancellation_html' => <<<HTML
        <p>Eine Stornierung kann vor Beginn der Leistung zu folgenden Fristen beantragt werden:</p>
        <ul>
        <li>Bis zu 2 Stunden vorher: <strong>volle Erstattung</strong>.</li>
        <li>Zwischen 2 und 1 Stunde vorher: <strong>50&nbsp;% Erstattung</strong>.</li>
        <li>Weniger als 1 Stunde: <strong>keine Erstattung</strong>.</li>
        </ul>
        <p>Rückzahlungen erfolgen auf die <strong>gleiche Karte</strong>, die beim Kauf verwendet wurde. Die Gutschriftzeiten hängen vom kartenausgebenden Institut ab.</p>
        <p>Bitte geben Sie bei der Stornierung Ihre <strong>Bestellnummer</strong> und Ihren <strong>vollständigen Namen</strong> an. Fristen können je nach Tour variieren, wenn dies auf der Produktseite angegeben ist.</p>
        HTML,
                'refunds_html' => <<<HTML
        <p>Sofern zutreffend, erfolgen Rückzahlungen auf die <strong>gleiche Karte</strong> wie beim Kauf. Zeitrahmen hängen vom Zahlungsdienst-Emittenten ab.</p>
        <p>Für Rückerstattungen: info@greenvacationscr.com / (+506) 2479 1471.</p>
        HTML,
                'warranty_html' => <<<HTML
        <p>Gilt für nicht erbrachte Leistungen oder Leistungen, die wesentlich von dem angebotenen Umfang abweichen. Vorkommnisse sind innerhalb von <strong>7 Tagen</strong> zu melden. Die Garantie gilt für von Green Vacations CR vertriebene touristische Leistungen.</p>
        HTML,
                'payments_html' => <<<HTML
        <p>Die Zahlung erfolgt über Alignet Payment Link mit für Online-Käufe aktivierten Visa/Mastercard/Amex-Karten.</p>
        HTML,
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
        'refund'       => 'Rückerstattungsrichtlinie',
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
