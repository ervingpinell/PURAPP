<?php

return [
    // Payment Page
    'payment' => 'Zahlung',
    'stripe_description' => 'Kredit-/Debitkartenzahlung',
    'paypal_description' => 'PayPal Zahlung',
    'tilopay_description' => 'Kredit-/Debitkartenzahlung (Tilopay)',
    'banco_nacional_description' => 'Banco Nacional Überweisung',
    'bac_description' => 'BAC Credomatic Überweisung',
    'bcr_description' => 'Banco de Costa Rica Überweisung',
    'payment_information' => 'Zahlungsinformationen',
    'secure_payment' => 'Sichere Zahlung',
    'select_payment_method' => 'Zahlungsmethode auswählen',
    'payment_secure_encrypted' => 'Ihre Zahlung ist sicher und verschlüsselt',
    'powered_by_stripe' => 'Powered by Stripe. Ihre Karteninformationen werden niemals auf unseren Servern gespeichert.',
    'pay' => 'Bezahlen',
    'back' => 'Zurück',
    'processing' => 'Verarbeitung...',
    'terms_agreement' => 'Mit Abschluss dieser Zahlung stimmen Sie unseren Allgemeinen Geschäftsbedingungen zu.',

    // Order Summary
    'order_summary' => 'Bestellübersicht',
    'subtotal' => 'Zwischensumme',
    'total' => 'Gesamt',
    'participants' => 'Teilnehmer',
    'free_cancellation' => 'Kostenlose Stornierung verfügbar',

    // Confirmation Page
    'payment_successful' => 'Zahlung Erfolgreich!',
    'booking_confirmed' => 'Ihre Buchung wurde bestätigt',
    'booking_reference' => 'Buchungsreferenz',
    'what_happens_next' => 'Was passiert als Nächstes?',
    'view_my_bookings' => 'Meine Buchungen Ansehen',
    'back_to_home' => 'Zurück zur Startseite',

    // Next Steps
    'next_step_email' => 'Sie erhalten eine Bestätigungs-E-Mail mit allen Details Ihrer Buchung',
    'next_step_confirmed' => 'Ihre Tour ist für das ausgewählte Datum und die Uhrzeit bestätigt',
    'next_step_manage' => 'Sie können Ihre Buchung unter "Meine Buchungen" einsehen und verwalten',
    'next_step_support' => 'Bei Fragen wenden Sie sich bitte an unser Support-Team',

    // Countdown Timer
    'time_remaining' => 'Verbleibende Zeit',
    'complete_payment_in' => 'Schließen Sie Ihre Zahlung ab in',
    'payment_expires_in' => 'Zahlung läuft ab in',
    'session_expired' => 'Ihre Zahlungssitzung ist abgelaufen',
    'session_expired_message' => 'Bitte kehren Sie zu Ihrem Warenkorb zurück und versuchen Sie es erneut.',

    // Errors
    'payment_failed' => 'Zahlung Fehlgeschlagen',
    'payment_error' => 'Bei der Verarbeitung Ihrer Zahlung ist ein Fehler aufgetreten',
    'payment_declined' => 'Ihre Zahlung wurde abgelehnt',
    'try_again' => 'Bitte versuchen Sie es erneut',
    'no_pending_bookings' => 'Keine ausstehenden Buchungen gefunden',
    'bookings_not_found' => 'Buchungen nicht gefunden',
    'payment_not_successful' => 'Die Zahlung war nicht erfolgreich. Bitte versuchen Sie es erneut.',
    'payment_confirmation_error' => 'Bei der Bestätigung Ihrer Zahlung ist ein Fehler aufgetreten.',
    'error_title' => 'Fehler',

    // Progress Steps
    'checkout' => 'Kasse',
    'confirmation' => 'Bestätigung',

    // Messages
    'complete_payment_message' => 'Bitte schließen Sie die Zahlung ab, um Ihre Buchung zu bestätigen',
    'payment_cancelled' => 'Die Zahlung wurde storniert. Sie können es erneut versuchen, wenn Sie bereit sind.',
    'redirect_paypal' => 'Klicken Sie auf Bezahlen, um zu PayPal weitergeleitet zu werden und Ihre Zahlung sicher abzuschließen.',
    'no_cart_data' => 'Keine Warenkorbdaten gefunden',

    // Admin / Management (merged from m_payments)
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
        'payment_timeline' => 'Zahlungszeitplan',
        'payment_created' => 'Zahlung erstellt',
        'payment_completed' => 'Zahlung abgeschlossen',
    ],

    'pagination' => [
        'showing' => 'Anzeigen',
        'to' => 'bis',
        'of' => 'von',
        'results' => 'Ergebnisse',
    ],
];
