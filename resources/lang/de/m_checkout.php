<?php

return [
    'title'                  => 'Zahlung',
    'panels' => [
        'terms_title'        => 'Richtlinien & Bedingungen',
        'secure_subtitle'    => 'Der Checkout ist schnell und sicher',
        'required_title'     => 'Pflichtfelder',
        'required_read_accept' => 'Sie müssen alle Richtlinien lesen und akzeptieren, um mit der Zahlung fortzufahren',
        'terms_block_title'  => 'Allgemeine Geschäftsbedingungen & Richtlinien',
        'version'            => 'v',
        'no_policies_configured' => 'Keine Richtlinien konfiguriert. Bitte kontaktieren Sie den Administrator.',
    ],

    'steps' => [
        'review'             => 'Überprüfung',
        'payment'            => 'Zahlung',
        'confirmation'       => 'Bestätigung',
    ],

    'buttons' => [
        'back'               => 'Zurück',
        'go_to_payment'      => 'Zur Zahlung',
        'view_details'       => 'Details anzeigen',
        'edit'               => 'Datum oder Teilnehmende ändern',
        'close'              => 'Schließen',
    ],

    'summary' => [
        'title'              => 'Bestellübersicht',
        'item'               => 'Artikel',
        'items'              => 'Artikel',
        'free_cancellation'  => 'Kostenlose Stornierung',
        'free_cancellation_until' => 'Vor :time am :date',
        'subtotal'           => 'Zwischensumme',
        'promo_code'         => 'Gutscheincode',
        'total'              => 'Gesamt',
        'taxes_included'     => 'Alle Steuern und Gebühren inbegriffen',
        'order_details'      => 'Bestelldetails',
    ],

    'blocks' => [
        'pickup_meeting'     => 'Abholung / Treffpunkt',
        'hotel'              => 'Hotel',
        'meeting_point'      => 'Treffpunkt',
        'pickup_time'        => 'Abholzeit',
        'add_ons'            => 'Zusätze',
        'duration'           => 'Dauer',
        'hours'              => 'Stunden',
        'guide'              => 'Reiseleiter',
        'notes'              => 'Notizen',
        'ref'                => 'Ref',
        'item'               => 'Posten',
    ],

    'categories' => [
        'adult'              => 'Erwachsener',
        'kid'                => 'Kind',
        'category'           => 'Kategorie',
        'qty_badge'          => ':qty×',
        'unit_price'         => '($:price × :qty)',
        'line_total'         => '$:total',
    ],

    'accept' => [
        'label_html'         => 'Ich habe die <strong>Allgemeinen Geschäftsbedingungen</strong>, die <strong>Datenschutzrichtlinie</strong> sowie alle <strong>Richtlinien zu Stornierung, Erstattung und Garantie</strong> gelesen und akzeptiere sie. *',
        'error'              => 'Sie müssen die Richtlinien akzeptieren, um fortzufahren.',
    ],

    'misc' => [
        'at'                 => 'um',
        'participant'        => 'Teilnehmende:r',
        'participants'       => 'Teilnehmende',
    ],
];
