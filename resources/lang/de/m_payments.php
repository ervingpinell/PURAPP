<?php

return [

    'ui' => [
        'page_title' => 'Zahlungen',
        'page_heading' => 'Zahlungsverwaltung',
        'payment_details' => 'Zahlungsdetails',
        'payments_list' => 'Zahlungsliste',
        'filters' => 'Filter',
        'actions' => 'Aktionen',
        'quick_actions' => 'Schnellaktionen',
    ],

    'statistics' => [
        'total_revenue' => 'Gesamtumsatz',
        'completed_payments' => 'Abgeschlossene Zahlungen',
        'pending_payments' => 'Ausstehende Zahlungen',
        'failed_payments' => 'Fehlgeschlagene Zahlungen',
    ],

    'fields' => [
        'payment_id' => 'Zahlungs-ID',
        'booking_ref' => 'Buchungs-Ref.',
        'customer' => 'Kunde',
        'tour' => 'Tour',
        'amount' => 'Betrag',
        'gateway' => 'Gateway',
        'status' => 'Status',
        'date' => 'Datum',
        'payment_method' => 'Zahlungsmethode',
        'tour_date' => 'Tourdatum',
        'booking_status' => 'Buchungsstatus',
    ],

    'filters' => [
        'search' => 'Suchen',
        'search_placeholder' => 'Buchungs-Ref., E-Mail, Name...',
        'status' => 'Status',
        'gateway' => 'Gateway',
        'date_from' => 'Datum Von',
        'date_to' => 'Datum Bis',
        'all' => 'Alle',
    ],

    'statuses' => [
        'pending' => 'Ausstehend',
        'processing' => 'In Bearbeitung',
        'completed' => 'Abgeschlossen',
        'failed' => 'Fehlgeschlagen',
        'refunded' => 'Erstattet',
    ],

    'buttons' => [
        'export_csv' => 'CSV Exportieren',
        'view_details' => 'Details Anzeigen',
        'view_booking' => 'Buchung Anzeigen',
        'process_refund' => 'Rückerstattung Bearbeiten',
        'back_to_list' => 'Zurück zur Liste',
    ],

    'messages' => [
        'no_payments_found' => 'Keine Zahlungen gefunden',
        'booking_deleted' => 'Die Buchung wurde endgültig gelöscht',
        'booking_deleted_on' => 'Die Buchung wurde endgültig gelöscht am',
    ],

    'info' => [
        'payment_information' => 'Zahlungsinformationen',
        'booking_information' => 'Buchungsinformationen',
        'gateway_response' => 'Gateway-Antwort',
        'payment_timeline' => 'Zahlungszeitlinie',
        'payment_created' => 'Zahlung Erstellt',
        'payment_completed' => 'Zahlung Abgeschlossen',
    ],

];
