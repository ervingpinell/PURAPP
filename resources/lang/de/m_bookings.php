<?php

return [

    'messages' => [
        'date_no_longer_available' => 'Das Datum :date ist nicht mehr für Buchungen verfügbar (Minimum: :min).',
        'limited_seats_available' => 'Nur noch :available Plätze für ":tour" am :date verfügbar.',
        'bookings_created_from_cart' => 'Ihre Buchungen wurden erfolgreich aus dem Warenkorb erstellt.',
        'capacity_exceeded' => 'Kapazität Überschritten',
        'meeting_point_hint' => 'Nur der Name des Punktes wird in der Liste angezeigt.',
    ],

    'availability' => [
        'fields' => [
            'tour'        => 'Tour',
            'date'        => 'Datum',
            'start_time'  => 'Startzeit',
            'end_time'    => 'Endzeit',
            'available'   => 'Verfügbar',
            'is_active'   => 'Aktiv',
        ],

        'success' => [
            'created'     => 'Verfügbarkeit erfolgreich erstellt.',
            'updated'     => 'Verfügbarkeit erfolgreich aktualisiert.',
            'deactivated' => 'Verfügbarkeit erfolgreich deaktiviert.',
        ],

        'error' => [
            'create'     => 'Verfügbarkeit konnte nicht erstellt werden.',
            'update'     => 'Verfügbarkeit konnte nicht aktualisiert werden.',
            'deactivate' => 'Verfügbarkeit konnte nicht deaktiviert werden.',
        ],

        'ui' => [
            'page_title'           => 'Verfügbarkeit',
            'page_heading'         => 'Verfügbarkeit',
            'blocked_page_title'   => 'Gesperrte Touren',
            'blocked_page_heading' => 'Gesperrte Touren',
        ],

        'states' => [
            'available' => 'Verfügbar',
            'blocked'   => 'Gesperrt',
        ],

        'buttons' => [
            'mark_all'         => 'Alle markieren',
            'unmark_all'       => 'Alle abwählen',
            'block_all'        => 'Alle sperren',
            'unblock_all'      => 'Alle entsperren',
            'block_selected'   => 'Ausgewählte sperren',
            'unblock_selected' => 'Ausgewählte entsperren',
            'back'             => 'Zurück',
            'open'             => 'Öffnen',
            'cancel'           => 'Abbrechen',
            'block'            => 'Sperren',
            'unblock'          => 'Entsperren',
        ],
    ],

    'bookings' => [
        'ui' => [
            'page_title'         => 'Buchungen',
            'page_heading'       => 'Buchungsverwaltung',
            'register_booking'   => 'Buchung Registrieren',
            'add_booking'        => 'Buchung Hinzufügen',
            'edit_booking'       => 'Buchung Bearbeiten',
            'booking_details'    => 'Buchungsdetails',
            'download_receipt'   => 'Quittung Herunterladen',
            'actions'            => 'Aktionen',
            'view_details'       => 'Details Anzeigen',
            'click_to_view'      => 'Klicken Sie, um Details anzuzeigen',
            'zoom_in'            => 'Vergrößern',
            'zoom_out'           => 'Verkleinern',
            'zoom_reset'         => 'Zoom Zurücksetzen',
        ],

        'fields' => [
            'booking_id'        => 'Buchungs-ID',
            'status'            => 'Status',
            'booking_date'      => 'Buchungsdatum',
            'booking_origin'    => 'Buchungsdatum (Ursprung)',
            'reference'         => 'Referenz',
            'customer'          => 'Kunde',
            'email'             => 'E-Mail',
            'phone'             => 'Telefon',
            'tour'              => 'Tour',
            'language'          => 'Sprache',
            'tour_date'         => 'Tour-Datum',
            'hotel'             => 'Hotel',
            'other_hotel'       => 'Anderer Hotelname',
            'meeting_point'     => 'Treffpunkt',
            'pickup_location'   => 'Abholort',
            'schedule'          => 'Zeitplan',
            'type'              => 'Typ',
            'adults'            => 'Erwachsene',
            'adults_quantity'   => 'Anzahl Erwachsene',
            'children'          => 'Kinder',
            'children_quantity' => 'Anzahl Kinder',
            'promo_code'        => 'Promo-Code',
            'total'             => 'Gesamt',
            'total_to_pay'      => 'Zu Zahlender Betrag',
            'adult_price'       => 'Erwachsenenpreis',
            'child_price'       => 'Kinderpreis',
            'notes'             => 'Notizen',
        ],

        'placeholders' => [
            'select_customer'  => 'Kunde auswählen',
            'select_tour'      => 'Tour auswählen',
            'select_schedule'  => 'Zeitplan auswählen',
            'select_language'  => 'Sprache auswählen',
            'select_hotel'     => 'Hotel auswählen',
            'select_point'     => 'Treffpunkt auswählen',
            'select_status'    => 'Status auswählen',
            'enter_hotel_name' => 'Hotelnamen eingeben',
            'enter_promo_code' => 'Promo-Code eingeben',
            'other'            => 'Andere…',
        ],

        'statuses' => [
            'pending'   => 'Ausstehend',
            'confirmed' => 'Bestätigt',
            'cancelled' => 'Storniert',
        ],

        'buttons' => [
            'save'            => 'Speichern',
            'cancel'          => 'Abbrechen',
            'edit'            => 'Bearbeiten',
            'delete'          => 'Löschen',
            'confirm_changes' => 'Änderungen Bestätigen',
            'apply'           => 'Anwenden',
            'update'          => 'Aktualisieren',
            'close'           => 'Schließen',
        ],

        'meeting_point' => [
            'time'     => 'Zeit:',
            'view_map' => 'Karte Anzeigen',
        ],

        'pricing' => [
            'title' => 'Preisübersicht',
        ],

        'optional' => 'optional',

        'messages' => [
            'past_booking_warning'  => 'Diese Buchung entspricht einem vergangenen Datum und kann nicht bearbeitet werden.',
            'tour_archived_warning' => 'Die Tour dieser Buchung wurde gelöscht/archiviert und konnte nicht geladen werden. Wählen Sie eine Tour aus, um ihre Zeitpläne anzuzeigen.',
            'no_schedules'          => 'Keine Zeitpläne verfügbar',
            'deleted_tour'          => 'Gelöschte Tour',
            'deleted_tour_snapshot' => 'Gelöschte Tour (:name)',
            'tour_archived'         => '(archiviert)',
            'meeting_point_hint'    => 'Nur der Name des Punktes wird in der Liste angezeigt.',
            'customer_locked'       => 'Der Kunde ist gesperrt und kann nicht bearbeitet werden.',

        ],

        'alerts' => [
            'error_summary' => 'Bitte korrigieren Sie die folgenden Fehler:',
        ],

        'validation' => [
            'past_date'      => 'Sie können nicht für Daten vor heute buchen.',
            'promo_required' => 'Geben Sie zuerst einen Promo-Code ein.',
            'promo_checking' => 'Code wird überprüft…',
            'promo_invalid'  => 'Ungültiger Promo-Code.',
            'promo_error'    => 'Code konnte nicht validiert werden.',
        ],

        'promo' => [
            'applied'         => 'Code angewendet',
            'applied_percent' => 'Code angewendet: -:percent%',
            'applied_amount'  => 'Code angewendet: -$:amount',
        ],

        'loading' => [
            'saving'     => 'Wird gespeichert...',
            'validating' => 'Wird validiert…',
            'updating'   => 'Wird aktualisiert...',
        ],

        'success' => [
            'created'          => 'Buchung erfolgreich erstellt.',
            'updated'          => 'Buchung erfolgreich aktualisiert.',
            'deleted'          => 'Buchung erfolgreich gelöscht.',
            'status_updated'   => 'Buchungsstatus erfolgreich aktualisiert.',
            'status_confirmed' => 'Buchung erfolgreich bestätigt.',
            'status_cancelled' => 'Buchung erfolgreich storniert.',
            'status_pending'   => 'Buchung erfolgreich als ausstehend festgelegt.',
        ],

        'errors' => [
            'create'                => 'Buchung konnte nicht erstellt werden.',
            'update'                => 'Buchung konnte nicht aktualisiert werden.',
            'delete'                => 'Buchung konnte nicht gelöscht werden.',
            'status_update_failed'  => 'Buchungsstatus konnte nicht aktualisiert werden.',
            'detail_not_found'      => 'Buchungsdetails nicht gefunden.',
            'schedule_not_found'    => 'Zeitplan nicht gefunden.',
            'insufficient_capacity' => 'Buchung kann nicht bestätigt werden. Unzureichende Kapazität für :tour am :date um :time. Angefordert: :requested Personen, Verfügbar: :available/:max.',
        ],

        'confirm' => [
            'delete' => 'Sind Sie sicher, dass Sie diese Buchung löschen möchten?',
        ],
    ],

    'actions' => [
        'confirm'        => 'Bestätigen',
        'cancel'         => 'Buchung Stornieren',
        'confirm_cancel' => 'Sind Sie sicher, dass Sie diese Buchung stornieren möchten?',
    ],

    'filters' => [
        'advanced_filters' => 'Erweiterte Filter',
        'dates'            => 'Daten',
        'booked_from'      => 'Gebucht von',
        'booked_until'     => 'Gebucht bis',
        'tour_from'        => 'Tour von',
        'tour_until'       => 'Tour bis',
        'all'              => 'Alle',
        'apply'            => 'Anwenden',
        'clear'            => 'Löschen',
        'close_filters'    => 'Filter Schließen',
        'search_reference' => 'Referenz Suchen...',
        'enter_reference'  => 'Buchungsreferenz Eingeben',
    ],

    'reports' => [
        'excel_title'          => 'Buchungsexport',
        'pdf_title'            => 'Buchungsbericht - Green Vacations CR',
        'general_report_title' => 'Allgemeiner Buchungsbericht - Green Vacations Costa Rica',
        'download_pdf'         => 'PDF Herunterladen',
        'export_excel'         => 'Excel Exportieren',
        'coupon'               => 'Gutschein',
        'adjustment'           => 'Anpassung',
        'totals'               => 'Summen',
        'adults_qty'           => 'Erwachsene (x:qty)',
        'kids_qty'             => 'Kinder (x:qty)',
        'people'               => 'Personen',
        'subtotal'             => 'Zwischensumme',
        'discount'             => 'Rabatt',
        'surcharge'            => 'Aufpreis',
        'original_price'       => 'Originalpreis',
        'total_adults'         => 'Gesamt Erwachsene',
        'total_kids'           => 'Gesamt Kinder',
        'total_people'         => 'Gesamt Personen',
    ],

    'receipt' => [
        'title'         => 'Buchungsquittung',
        'company'       => 'Green Vacations CR',
        'code'          => 'Code',
        'client'        => 'Kunde',
        'tour'          => 'Tour',
        'booking_date'  => 'Buchungsdatum',
        'tour_date'     => 'Tour-Datum',
        'schedule'      => 'Zeitplan',
        'hotel'         => 'Hotel',
        'meeting_point' => 'Treffpunkt',
        'status'        => 'Status',
        'adults_x'      => 'Erwachsene (x:count)',
        'kids_x'        => 'Kinder (x:count)',
        'people'        => 'Personen',
        'subtotal'      => 'Zwischensumme',
        'discount'      => 'Rabatt',
        'surcharge'     => 'Aufpreis',
        'total'         => 'GESAMT',
        'no_schedule'   => 'Kein Zeitplan',
        'qr_alt'        => 'QR-Code',
        'qr_scan'       => 'Scannen Sie, um die Buchung anzuzeigen',
        'thanks'        => 'Vielen Dank, dass Sie sich für :company entschieden haben!',
    ],

    'details' => [
        'booking_info'  => 'Buchungsinformationen',
        'customer_info' => 'Kundeninformationen',
        'tour_info'     => 'Tour-Informationen',
        'pricing_info'  => 'Preisinformationen',
        'subtotal'      => 'Zwischensumme',
        'discount'      => 'Rabatt',
    ],

];
