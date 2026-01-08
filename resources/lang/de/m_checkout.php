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

    'customer_info' => [
        'title'              => 'Kundeninformation',
        'subtitle'              => 'Bitte geben Sie Ihre Kontaktdaten ein, um fortzufahren',
        'full_name'              => 'Vollständiger Name',
        'first_name'             => 'Vorname',
        'last_name'              => 'Nachname',
        'email'              => 'E-Mail',
        'phone'              => 'Telefon',
        'optional'              => 'optional',
        'placeholder_name'              => 'Max Mustermann',
        'placeholder_email'              => 'email@beispiel.de',
        'why_need_title'              => 'Warum wir dies benötigen',
        'why_need_text'              => 'Ihre E-Mail-Adresse wird verwendet, um Buchungsbestätigungen, Updates und Zahlungslinks zu senden. Sie können optional nach der Buchung ein Konto erstellen, um Ihre Reservierungen zu verwalten.',
        'logged_in_as'              => 'Angemeldet als',
        'address'            => 'Adresse',
        'city'               => 'Stadt',
        'state'              => 'Bundesland / Kanton',
        'zip'                => 'Postleitzahl',
        'country'            => 'Land',
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

    'payment' => [
        'title'              => 'Zahlung',
        'total'              => 'Gesamt',
        'secure_payment'     => 'Sichere Zahlung',
        'powered_by'         => 'Bereitgestellt von',
        'proceed_to_payment' => 'Zur Zahlung vorgehen',
        'secure_transaction' => 'Sichere Transaktion',
        'error_occurred'     => 'Beim Verarbeiten der Zahlung ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.',
        'invalid_response'   => 'Die Antwort des Zahlungsanbieters ist ungültig.',

        // Friendly error messages for Alignet
        'cancelled_by_user'  => 'Sie haben die Zahlung abgebrochen.',
        'timeout'            => 'Die Zahlungsfrist ist abgelaufen.',
        'insufficient_funds' => 'Unzureichende Mittel.',
        'card_declined'      => 'Ihre Karte wurde abgelehnt.',
        'invalid_card'       => 'Ungültige Kartendaten.',
        'failed'             => 'Die Zahlung konnte nicht verarbeitet werden.',
        'success'            => 'Zahlung erfolgreich! Sie erhalten in Kürze eine Bestätigungs-E-Mail.',
        'session_expired'    => 'Ihre Sitzung ist abgelaufen. Bitte melden Sie sich erneut an.',

        // Alignet Bank Specific Messages
        'operation_denied'   => 'Operation verweigert.',
        'operation_rejected' => 'Operation abgelehnt.',
        'operation_authorized' => 'Operation genehmigt.',

        // Debug info for bank support
        'debug_info'         => 'DEBUG - Code: :code | Auth: :auth | Message: :message',
    ],
    'booking' => [
        'summary'   => 'Buchungsübersicht',
        'reference' => 'Referenz',
        'date'      => 'Datum',
        'passengers' => 'Passagiere',
    ],
    'tour' => [
        'name' => 'Tour',
    ],
];
