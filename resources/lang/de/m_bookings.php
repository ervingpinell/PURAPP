<?php

return [

    'messages' => [
        'date_no_longer_available' => 'Das Datum :date ist nicht mehr für Buchungen verfügbar (Minimum: :min).',
        'limited_seats_available' => 'Nur noch :available Plätze für „:tour“ am :date.',
        'bookings_created_from_cart' => 'Ihre Buchungen wurden erfolgreich aus dem Warenkorb erstellt.',
        'capacity_exceeded' => 'Kapazität überschritten',
        'meeting_point_hint' => 'In der Liste wird nur der Name des Punktes angezeigt.',
    ],

    // =========================================================
    // [01] AVAILABILITY
    // =========================================================
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

        'validation' => [
            'tour_id' => [
                'required' => 'Das Feld :attribute ist erforderlich.',
                'integer'  => 'Das Feld :attribute muss eine ganze Zahl sein.',
                'exists'   => 'Das ausgewählte :attribute ist nicht vorhanden.',
            ],
            'date' => [
                'required'    => 'Das Feld :attribute ist erforderlich.',
                'date_format' => 'Das :attribute muss das Format JJJJ-MM-TT haben.',
            ],
            'start_time' => [
                'date_format'   => 'Das :attribute muss das Format HH:MM (24h) haben.',
                'required_with' => 'Das :attribute ist erforderlich, wenn eine Endzeit angegeben ist.',
            ],
            'end_time' => [
                'date_format'    => 'Das :attribute muss das Format HH:MM (24h) haben.',
                'after_or_equal' => 'Das :attribute muss größer oder gleich der Startzeit sein.',
            ],
            'available' => [
                'boolean' => 'Das Feld :attribute ist ungültig.',
            ],
            'is_active' => [
                'boolean' => 'Das Feld :attribute ist ungültig.',
            ],
        ],

        'ui' => [
            'page_title'           => 'Verfügbarkeit',
            'page_heading'         => 'Verfügbarkeit',
            'blocked_page_title'   => 'Gesperrte Touren',
            'blocked_page_heading' => 'Gesperrte Touren',
            'tours_count'          => '( :count Touren )',
            'blocked_count'        => '( :count gesperrt )',
        ],

        'filters' => [
            'date'               => 'Datum',
            'days'               => 'Tage',
            'product'            => 'Produkt',
            'search_placeholder' => 'Tour suchen...',
            'update_state'       => 'Status aktualisieren',
            'view_blocked'       => 'Gesperrte anzeigen',
            'tip'                => 'Tipp: Markieren Sie Zeilen und verwenden Sie eine Aktion aus dem Menü.',
        ],

        'blocks' => [
            'am_tours'    => 'Vormittags-Touren (alle Touren, die vor 12:00 Uhr starten)',
            'pm_tours'    => 'Nachmittags-Touren (alle Touren, die nach 12:00 Uhr starten)',
            'am_blocked'  => 'AM gesperrt',
            'pm_blocked'  => 'PM gesperrt',
            'empty_block' => 'Keine Touren in diesem Block.',
            'empty_am'    => 'Keine gesperrten Touren am Vormittag.',
            'empty_pm'    => 'Keine gesperrten Touren am Nachmittag.',
            'no_data'     => 'Keine Daten für die ausgewählten Filter.',
            'no_blocked'  => 'Keine gesperrten Touren im ausgewählten Zeitraum.',
        ],

        'states' => [
            'available' => 'Verfügbar',
            'blocked'   => 'Gesperrt',
        ],

        'buttons' => [
            'mark_all'         => 'Alle markieren',
            'unmark_all'       => 'Auswahl aufheben',
            'block_all'        => 'Alle sperren',
            'unblock_all'      => 'Alle entsperren',
            'block_selected'   => 'Auswahl sperren',
            'unblock_selected' => 'Auswahl entsperren',
            'back'             => 'Zurück',
            'open'             => 'Öffnen',
            'cancel'           => 'Abbrechen',
            'block'            => 'Sperren',
            'unblock'          => 'Entsperren',
        ],

        'confirm' => [
            'view_blocked_title'    => 'Gesperrte Touren ansehen',
            'view_blocked_text'     => 'Die Ansicht mit gesperrten Touren wird geöffnet, um sie zu entsperren.',
            'block_title'           => 'Tour sperren?',
            'block_html'            => '<b>:label</b> wird für das Datum <b>:day</b> gesperrt.',
            'block_btn'             => 'Ja, sperren',
            'unblock_title'         => 'Tour entsperren?',
            'unblock_html'          => '<b>:label</b> wird für das Datum <b>:day</b> entsperrt.',
            'unblock_btn'           => 'Ja, entsperren',
            'bulk_title'            => 'Aktion bestätigen',
            'bulk_items_html'       => 'Zu verändernde Elemente: <b>:count</b>.',
            'bulk_block_day_html'   => 'Alle verfügbaren für den Tag <b>:day</b> sperren',
            'bulk_block_block_html' => 'Alle verfügbaren im Block <b>:block</b> am <b>:day</b> sperren',
        ],

        'toasts' => [
            'applying_filters'   => 'Filter werden angewendet...',
            'searching'          => 'Suche...',
            'updating_range'     => 'Zeitraum wird aktualisiert...',
            'invalid_date_title' => 'Ungültiges Datum',
            'invalid_date_text'  => 'Vergangene Daten sind nicht erlaubt.',
            'marked_n'           => 'Markiert: :n',
            'unmarked_n'         => 'Markierung aufgehoben: :n',
            'updated'            => 'Änderung angewendet',
            'updated_count'      => 'Aktualisiert: :count',
            'unblocked_count'    => 'Entsperrt: :count',
            'no_selection_title' => 'Keine Auswahl',
            'no_selection_text'  => 'Markieren Sie mindestens eine Tour.',
            'no_changes_title'   => 'Keine Änderungen',
            'no_changes_text'    => 'Keine anwendbaren Elemente.',
            'error_generic'      => 'Aktualisierung konnte nicht abgeschlossen werden.',
            'error_update'       => 'Konnte nicht aktualisieren.',
        ],
    ],

    // =========================================================
    // [02] BOOKINGS
    // =========================================================
    'bookings' => [
        'ui' => [
            'page_title'       => 'Buchungen',
            'page_heading'     => 'Buchungsverwaltung',
            'register_booking' => 'Buchung registrieren',
            'add_booking'      => 'Buchung hinzufügen',
            'edit_booking'     => 'Buchung bearbeiten',
            'booking_details'  => 'Buchungsdetails',
            'download_receipt' => 'Beleg herunterladen',
            'actions'          => 'Aktionen',
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
            'tour_date'         => 'Tourdatum',
            'hotel'             => 'Hotel',
            'other_hotel'       => 'Anderer Hotelname',
            'meeting_point'     => 'Abhol-/Treffpunkt',
            'schedule'          => 'Zeitplan',
            'type'              => 'Typ',
            'adults'            => 'Erwachsene',
            'adults_quantity'   => 'Anzahl Erwachsene',
            'children'          => 'Kinder',
            'children_quantity' => 'Anzahl Kinder',
            'promo_code'        => 'Promo-Code',
            'total'             => 'Gesamt',
            'total_to_pay'      => 'Zu zahlender Betrag',
            'adult_price'       => 'Preis Erwachsene',
            'child_price'       => 'Preis Kind',
            'notes'             => 'Notizen',
        ],

        'placeholders' => [
            'select_customer'  => 'Kunden auswählen',
            'select_tour'      => 'Tour auswählen',
            'select_schedule'  => 'Zeitplan auswählen',
            'select_language'  => 'Sprache auswählen',
            'select_hotel'     => 'Hotel auswählen',
            'select_point'     => '-- Punkt auswählen --',
            'select_status'    => 'Status auswählen',
            'enter_hotel_name' => 'Hotelname eingeben',
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
            'confirm_changes' => 'Änderungen bestätigen',
            'apply'           => 'Anwenden',
            'update'          => 'Aktualisieren',
        ],

        'meeting_point' => [
            'time'     => 'Zeit:',
            'view_map' => 'Karte anzeigen',
        ],

        'messages' => [
            'past_booking_warning'  => 'Diese Buchung betrifft ein vergangenes Datum und kann nicht bearbeitet werden.',
            'tour_archived_warning' => 'Die Tour dieser Buchung wurde gelöscht/archiviert und konnte nicht geladen werden. Wählen Sie eine Tour, um deren Zeitpläne zu sehen.',
            'no_schedules'          => 'Keine Zeitpläne verfügbar',
            'deleted_tour'          => 'Gelöschte Tour',
            'deleted_tour_snapshot' => 'Gelöschte Tour (:name)',
            'tour_archived'         => '(archiviert)',
            'meeting_point_hint'    => 'In der Liste wird nur der Name des Punktes angezeigt.',
        ],

        'alerts' => [
            'error_summary' => 'Bitte korrigieren Sie die folgenden Fehler:',
        ],

        'validation' => [
            'past_date'      => 'Sie können nicht für Daten vor heute buchen.',
            'promo_required' => 'Geben Sie zuerst einen Promo-Code ein.',
            'promo_checking' => 'Code wird geprüft…',
            'promo_invalid'  => 'Ungültiger Promo-Code.',
            'promo_error'    => 'Der Code konnte nicht validiert werden.',
        ],

        'promo' => [
            'applied'         => 'Code angewendet',
            'applied_percent' => 'Code angewendet: -:percent%',
            'applied_amount'  => 'Code angewendet: -$:amount',
        ],

        'loading' => [
            'saving'     => 'Speichern...',
            'validating' => 'Validierung…',
            'updating'   => 'Aktualisieren...',
        ],

        'success' => [
            'created' => 'Buchung erfolgreich erstellt.',
            'updated' => 'Buchung erfolgreich aktualisiert.',
            'deleted' => 'Buchung erfolgreich gelöscht.',
        ],

        'errors' => [
            'create' => 'Buchung konnte nicht erstellt werden.',
            'update' => 'Buchung konnte nicht aktualisiert werden.',
            'delete' => 'Buchung konnte nicht gelöscht werden.',
        ],

        'confirm' => [
            'delete' => 'Möchten Sie diese Buchung wirklich löschen?',
        ],
    ],

    // =========================================================
    // [03] FILTERS
    // =========================================================
    'filters' => [
        'advanced_filters' => 'Erweiterte Filter',
        'dates'            => 'Daten',
        'booked_from'      => 'Gebucht ab',
        'booked_until'     => 'Gebucht bis',
        'tour_from'        => 'Tour ab',
        'tour_until'       => 'Tour bis',
        'all'              => 'Alle',
        'apply'            => 'Anwenden',
        'clear'            => 'Zurücksetzen',
        'close_filters'    => 'Filter schließen',
        'search_reference' => 'Referenz suchen...',
        'enter_reference'  => 'Buchungsreferenz eingeben',
    ],

    // =========================================================
    // [04] REPORTS
    // =========================================================
    'reports' => [
        'excel_title'          => 'Buchungs-Export',
        'pdf_title'            => 'Buchungsbericht - Green Vacations CR',
        'general_report_title' => 'Allgemeiner Buchungsbericht - Green Vacations Costa Rica',
        'download_pdf'         => 'PDF herunterladen',
        'export_excel'         => 'Excel exportieren',
        'coupon'               => 'Gutschein',
        'adjustment'           => 'Anpassung',
        'totals'               => 'Summen',
        'adults_qty'           => 'Erwachsene (x:qty)',
        'kids_qty'             => 'Kinder (x:qty)',
        'people'               => 'Personen',
        'subtotal'             => 'Zwischensumme',
        'discount'             => 'Rabatt',
        'surcharge'            => 'Zuschlag',
        'original_price'       => 'Originalpreis',
        'total_adults'         => 'Gesamt Erwachsene',
        'total_kids'           => 'Gesamt Kinder',
        'total_people'         => 'Gesamt Personen',
    ],

    // =========================================================
    // [05] RECEIPT
    // =========================================================
    'receipt' => [
        'title'         => 'Buchungsbeleg',
        'company'       => 'Green Vacations CR',
        'code'          => 'Code',
        'client'        => 'Kunde',
        'tour'          => 'Tour',
        'booking_date'  => 'Buchungsdatum',
        'tour_date'     => 'Tourdatum',
        'schedule'      => 'Zeitplan',
        'hotel'         => 'Hotel',
        'meeting_point' => 'Treffpunkt',
        'status'        => 'Status',
        'adults_x'      => 'Erwachsene (x:count)',
        'kids_x'        => 'Kinder (x:count)',
        'people'        => 'Personen',
        'subtotal'      => 'Zwischensumme',
        'discount'      => 'Rabatt',
        'surcharge'     => 'Zuschlag',
        'total'         => 'GESAMT',
        'no_schedule'   => 'Kein Zeitplan',
        'qr_alt'        => 'QR-Code',
        'qr_scan'       => 'Scannen, um die Buchung anzuzeigen',
        'thanks'        => 'Danke, dass Sie sich für :company entschieden haben!',
    ],

];
