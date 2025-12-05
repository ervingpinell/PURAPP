<?php

return [

    'messages' => [
        'date_no_longer_available'   => 'Das Datum :date ist nicht mehr für Buchungen verfügbar (Minimum: :min).',
        'limited_seats_available'    => 'Es sind nur noch :available Plätze für „:tour“ am :date verfügbar.',
        'bookings_created_from_cart' => 'Deine Buchungen wurden erfolgreich aus dem Warenkorb erstellt.',
        'capacity_exceeded'          => 'Kapazität überschritten',
        'meeting_point_hint'         => 'In der Liste wird nur der Name des Treffpunkts angezeigt.',
    ],

    'validation' => [
        'max_persons_exceeded'    => 'Maximal :max Personen pro Buchung insgesamt.',
        'min_adults_required'     => 'Es sind mindestens :min Erwachsene pro Buchung erforderlich.',
        'max_kids_exceeded'       => 'Maximal :max Kinder pro Buchung.',
        'no_active_categories'    => 'Diese Tour hat keine aktiven Kundenkategorien.',
        'min_category_not_met'    => 'Es sind mindestens :min Personen in der Kategorie „:category“ erforderlich.',
        'max_category_exceeded'   => 'Maximal :max Personen in der Kategorie „:category“ erlaubt.',
        'min_one_person_required' => 'Es muss mindestens eine Person in der Buchung vorhanden sein.',
        'category_not_available'  => 'Die Kategorie mit der ID :category_id ist für diese Tour nicht verfügbar.',
        'max_persons_label'       => 'Maximal erlaubte Personen pro Buchung',
        'date_range_hint'         => 'Wähle ein Datum zwischen :from — :to',
    ],

    // =========================================================
    // [01] VERFÜGBARKEIT
    // =========================================================
    'availability' => [
        'fields' => [
            'tour'       => 'Tour',
            'date'       => 'Datum',
            'start_time' => 'Startzeit',
            'end_time'   => 'Endzeit',
            'available'  => 'Verfügbar',
            'is_active'  => 'Aktiv',
        ],

        'success' => [
            'created'     => 'Verfügbarkeit erfolgreich erstellt.',
            'updated'     => 'Verfügbarkeit erfolgreich aktualisiert.',
            'deactivated' => 'Verfügbarkeit erfolgreich deaktiviert.',
        ],

        'error' => [
            'create'     => 'Die Verfügbarkeit konnte nicht erstellt werden.',
            'update'     => 'Die Verfügbarkeit konnte nicht aktualisiert werden.',
            'deactivate' => 'Die Verfügbarkeit konnte nicht deaktiviert werden.',
        ],

        'validation' => [
            'tour_id' => [
                'required' => 'Das Feld :attribute ist erforderlich.',
                'integer'  => 'Das Feld :attribute muss eine ganze Zahl sein.',
                'exists'   => 'Das ausgewählte :attribute existiert nicht.',
            ],
            'date' => [
                'required'    => 'Das Feld :attribute ist erforderlich.',
                'date_format' => 'Das Feld :attribute muss das Format JJJJ-MM-TT haben.',
            ],
            'start_time' => [
                'date_format'   => 'Das Feld :attribute muss das Format HH:MM (24h) haben.',
                'required_with' => 'Das Feld :attribute ist erforderlich, wenn eine Endzeit angegeben wird.',
            ],
            'end_time' => [
                'date_format'    => 'Das Feld :attribute muss das Format HH:MM (24h) haben.',
                'after_or_equal' => 'Das Feld :attribute muss größer oder gleich der Startzeit sein.',
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
            'blocked_page_title'   => 'Blockierte Touren',
            'blocked_page_heading' => 'Blockierte Touren',
            'tours_count'          => '( :count Touren )',
            'blocked_count'        => '( :count blockiert )',
        ],

        'filters' => [
            'date'               => 'Datum',
            'days'               => 'Tage',
            'product'            => 'Produkt',
            'search_placeholder' => 'Tour suchen...',
            'update_state'       => 'Status aktualisieren',
            'view_blocked'       => 'Blockierte anzeigen',
            'tip'                => 'Tipp: Markiere Zeilen und nutze eine Aktion aus dem Menü.',
        ],

        'blocks' => [
            'am_tours'    => 'AM-Touren (alle Touren, die vor 12:00 Uhr starten)',
            'pm_tours'    => 'PM-Touren (alle Touren, die nach 12:00 Uhr starten)',
            'am_blocked'  => 'AM blockiert',
            'pm_blocked'  => 'PM blockiert',
            'empty_block' => 'In diesem Block gibt es keine Touren.',
            'empty_am'    => 'Keine blockierten Touren am Vormittag.',
            'empty_pm'    => 'Keine blockierten Touren am Nachmittag.',
            'no_data'     => 'Für die ausgewählten Filter liegen keine Daten vor.',
            'no_blocked'  => 'Es gibt keine blockierten Touren im ausgewählten Zeitraum.',
        ],

        'states' => [
            'available' => 'Verfügbar',
            'blocked'   => 'Blockiert',
        ],

        'buttons' => [
            'mark_all'         => 'Alle markieren',
            'unmark_all'       => 'Markierung aufheben',
            'block_all'        => 'Alle blockieren',
            'unblock_all'      => 'Alle freigeben',
            'block_selected'   => 'Ausgewählte blockieren',
            'unblock_selected' => 'Ausgewählte freigeben',
            'back'             => 'Zurück',
            'open'             => 'Öffnen',
            'cancel'           => 'Abbrechen',
            'block'            => 'Blockieren',
            'unblock'          => 'Freigeben',
        ],

        'confirm' => [
            'view_blocked_title'    => 'Blockierte Touren anzeigen',
            'view_blocked_text'     => 'Die Ansicht mit blockierten Touren wird geöffnet, um diese freizugeben.',
            'block_title'           => 'Tour blockieren?',
            'block_html'            => '<b>:label</b> wird für das Datum <b>:day</b> blockiert.',
            'block_btn'             => 'Ja, blockieren',
            'unblock_title'         => 'Tour freigeben?',
            'unblock_html'          => '<b>:label</b> wird für das Datum <b>:day</b> freigegeben.',
            'unblock_btn'           => 'Ja, freigeben',
            'bulk_title'            => 'Aktion bestätigen',
            'bulk_items_html'       => 'Zu ändernde Einträge: <b>:count</b>.',
            'bulk_block_day_html'   => 'Alle verfügbaren Touren für den Tag <b>:day</b> blockieren',
            'bulk_block_block_html' => 'Alle verfügbaren Touren im Block <b>:block</b> am <b>:day</b> blockieren',
        ],

        'toasts' => [
            'applying_filters'   => 'Filter werden angewendet...',
            'searching'          => 'Suche...',
            'updating_range'     => 'Zeitraum wird aktualisiert...',
            'invalid_date_title' => 'Ungültiges Datum',
            'invalid_date_text'  => 'Vergangene Daten sind nicht erlaubt.',
            'marked_n'           => ':n markiert',
            'unmarked_n'         => ':n Markierungen entfernt',
            'updated'            => 'Änderung angewendet',
            'updated_count'      => 'Aktualisiert: :count',
            'unblocked_count'    => 'Freigegeben: :count',
            'no_selection_title' => 'Keine Auswahl',
            'no_selection_text'  => 'Markiere mindestens eine Tour.',
            'no_changes_title'   => 'Keine Änderungen',
            'no_changes_text'    => 'Es gibt keine anwendbaren Einträge.',
            'error_generic'      => 'Die Aktualisierung konnte nicht abgeschlossen werden.',
            'error_update'       => 'Konnte nicht aktualisiert werden.',
        ],
    ],

    // =========================================================
    // [02] BUCHUNGEN
    // =========================================================
    'bookings' => [
        'singular' => 'Buchung',
        'plural' => 'Buchungen',
        'customer' => 'Kunde',
        'payment_link_regenerated' => 'Zahlungslink erfolgreich neu generiert',
        'regenerate_payment_link' => 'Zahlungslink neu generieren',
        'confirm_regenerate_payment_link' => 'Sind Sie sicher, dass Sie den Zahlungslink neu generieren möchten? Der alte Link funktioniert dann nicht mehr.',
        'payment_link_expired_label' => 'Link abgelaufen',
        'steps' => [
            'customer' => 'Kunde',
            'select_tour_date' => 'Tour und Datum auswählen',
            'select_schedule_language' => 'Uhrzeit und Sprache auswählen',
            'select_participants' => 'Teilnehmer auswählen',
            'customer_details' => 'Kunde und Details',
        ],
        'ui' => [
            'page_title'        => 'Buchungen',
            'page_heading'      => 'Buchungsverwaltung',
            'register_booking'  => 'Buchung registrieren',
            'add_booking'       => 'Buchung hinzufügen',
            'edit_booking'      => 'Buchung bearbeiten',
            'booking_details'   => 'Buchungsdetails',
            'download_receipt'  => 'Beleg herunterladen',
            'actions'           => 'Aktionen',
            'view_details'      => 'Details anzeigen',
            'click_to_view'     => 'Klicken, um Details zu sehen',
            'zoom_in'           => 'Vergrößern',
            'zoom_out'          => 'Verkleinern',
            'zoom_reset'        => 'Zoom zurücksetzen',
            'no_promo'          => 'Kein Promo-Code angewendet',
            'create_booking'    => 'Buchung erstellen',
            'create_title'      => 'Neue Buchung Erstellen',
            'booking_info'      => 'Buchungsinformationen',
            'select_customer'   => 'Kunden auswählen',
            'select_tour'       => 'Tour auswählen',
            'select_tour_first' => 'Wähle zuerst eine Tour aus',
            'select_option'     => 'Auswählen',
            'select_tour_to_see_categories' => 'Wähle eine Tour, um die Kategorien zu sehen',
            'loading'           => 'Laden...',
            'no_results'        => 'Keine Ergebnisse',
            'error_loading'     => 'Fehler beim Laden der Daten',
            'tour_without_categories' => 'Für diese Tour sind keine Kategorien konfiguriert',
            'verifying'         => 'Wird überprüft...',
            'min'               => 'Minimum',
            'max'               => 'Maximum',
            'confirm_booking' => 'Buchung bestätigen',
            'subtotal' => 'Zwischensumme',
            'total' => 'Gesamt',
            'select_meeting_point' => 'Treffpunkt auswählen',
            'no_pickup' => 'Keine Abholung',
            'hotel' => 'Hotel',
            'meeting_point' => 'Treffpunkt',
            'surcharge' => 'Aufschlag',
            'discount' => 'Rabatt',
            'participants' => 'Teilnehmer',
            'price_breakdown' => 'Preisübersicht',
            'enter_promo' => 'Promo-Code eingeben',
            'select_hotel' => 'Hotel auswählen',
            'payment_link' => 'Zahlungslink',
            'view_payment' => 'Zahlung anzeigen',
            'hotel_pickup' => 'Hotelabholung',
            'meeting_point_pickup' => 'Treffpunkt',
            'no_pickup' => 'Keine Abholung',
            'optional' => '(Optional)',
            'pickup_info' => 'Legen Sie die Abholzeit für diese Buchung fest.',
            'confirm_booking_alert' => 'Wenn Sie diese Buchung bestätigen, wird eine Bestätigungs-E-Mail an den Kunden gesendet.',
            'regenerating' => 'Wird regeneriert...',
            'copied' => 'Kopiert!',
            'copy_failed' => 'Kopieren fehlgeschlagen',
            'pickup_warning' => 'Warnung: Abholzeit ist :pickup, aber die Tour beginnt um :tour. Bitte überprüfen.',
        ],

        'fields' => [
            'booking_id'        => 'Buchungs-ID',
            'status'            => 'Status',
            'booking_date'      => 'Buchungsdatum',
            'booking_origin'    => 'Buchungsdatum (Ursprung)',
            'reference'         => 'Referenz',
            'booking_reference' => 'Buchungsreferenz',
            'customer'          => 'Kunde',
            'email'             => 'E-Mail',
            'phone'             => 'Telefon',
            'tour'              => 'Tour',
            'language'          => 'Sprache',
            'tour_date'         => 'Tourdatum',
            'hotel'             => 'Hotel',
            'other_hotel'       => 'Name eines anderen Hotels',
            'meeting_point'     => 'Treffpunkt',
            'pickup_location'   => 'Abholort',
            'schedule'          => 'Uhrzeit',
            'type'              => 'Typ',
            'adults'            => 'Erwachsene',
            'adults_quantity'   => 'Anzahl der Erwachsenen',
            'children'          => 'Kinder',
            'children_quantity' => 'Anzahl der Kinder',
            'promo_code'        => 'Promo-Code',
            'total'             => 'Gesamt',
            'total_to_pay'      => 'Zu zahlender Gesamtbetrag',
            'adult_price'       => 'Preis Erwachsener',
            'child_price'       => 'Preis Kind',
            'notes'             => 'Notizen',
            'hotel_name'        => 'Hotelname',
            'travelers'         => 'Reisende',
            'subtotal'          => 'Zwischensumme',
            'discount'          => 'Rabatt',
            'total_persons'     => 'Personenanzahl',
            'pickup_place'      => 'Abholort',
            'pickup_time'       => 'Abholzeit',
            'date'              => 'Datum',
            'category'          => 'Kategorie',
            'quantity'          => 'Menge',
            'price'             => 'Preis',
            'pickup'            => 'Abholung',
        ],

        'placeholders' => [
            'select_customer'  => 'Kunden auswählen',
            'select_tour'      => 'Eine Tour auswählen',
            'select_schedule'  => 'Uhrzeit auswählen',
            'select_language'  => 'Sprache auswählen',
            'select_hotel'     => 'Hotel auswählen',
            'select_point'     => 'Treffpunkt auswählen',
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
            'close'           => 'Schließen',
            'back'            => 'Zurück',
        ],

        'meeting_point' => [
            'time'     => 'Uhrzeit:',
            'view_map' => 'Karte anzeigen',
        ],

        'pricing' => [
            'title' => 'Preisübersicht',
        ],

        'optional' => 'optional',

        'messages' => [
            'past_booking_warning'  => 'Diese Buchung liegt in der Vergangenheit und kann nicht bearbeitet werden.',
            'tour_archived_warning' => 'Die Tour dieser Buchung wurde gelöscht/archiviert und konnte nicht geladen werden. Wähle eine Tour, um ihre Uhrzeiten zu sehen.',
            'no_schedules'          => 'Keine Uhrzeiten verfügbar',
            'deleted_tour'          => 'Gelöschte Tour',
            'deleted_tour_snapshot' => 'Gelöschte Tour (:name)',
            'tour_archived'         => '(archiviert)',
            'meeting_point_hint'    => 'In der Liste wird nur der Name des Treffpunkts angezeigt.',
            'customer_locked'       => 'Der Kunde ist gesperrt und kann nicht bearbeitet werden.',
            'promo_applied_subtract' => 'Rabatt angewendet:',
            'promo_applied_add'     => 'Aufschlag angewendet:',
            'hotel_locked_by_meeting_point'   => 'Ein Treffpunkt wurde ausgewählt; ein Hotel kann nicht ausgewählt werden.',
            'meeting_point_locked_by_hotel'   => 'Ein Hotel wurde ausgewählt; ein Treffpunkt kann nicht ausgewählt werden.',
            'promo_removed'         => 'Promo-Code entfernt',
        ],

        'alerts' => [
            'error_summary' => 'Bitte korrigiere die folgenden Fehler:',
        ],

        'validation' => [
            'past_date'          => 'Du kannst nicht für Daten vor heute buchen.',
            'promo_required'     => 'Gib zuerst einen Promo-Code ein.',
            'promo_checking'     => 'Code wird überprüft…',
            'promo_invalid'      => 'Ungültiger Promo-Code.',
            'promo_error'        => 'Der Code konnte nicht validiert werden.',
            'promo_apply_required' => 'Bitte klicke auf Anwenden, um deinen Promo-Code zuerst zu validieren.',
            'promo_empty'        => 'Gib zuerst einen Code ein.',
            'promo_needs_subtotal' => 'Füge mindestens 1 Passagier hinzu, um den Rabatt zu berechnen.',
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
            'status_pending'   => 'Buchung erfolgreich auf „ausstehend“ gesetzt.',
        ],

        'errors' => [
            'create'               => 'Die Buchung konnte nicht erstellt werden.',
            'update'               => 'Die Buchung konnte nicht aktualisiert werden.',
            'delete'               => 'Die Buchung konnte nicht gelöscht werden.',
            'status_update_failed' => 'Der Buchungsstatus konnte nicht aktualisiert werden.',
            'detail_not_found'     => 'Buchungsdetails nicht gefunden.',
            'schedule_not_found'   => 'Uhrzeit nicht gefunden.',
            'insufficient_capacity' => 'Nicht genügend Kapazität für „:tour“ am :date um :time. Angefragt: :requested, verfügbar: :available (max: :max).',
        ],

        'confirm' => [
            'delete' => 'Möchtest du diese Buchung wirklich löschen?',
        ],

        // SoftDelete & Papierkorb
        'trash' => [
            'active_bookings' => 'Aktive Buchungen',
            'trash' => 'Papierkorb',
            'restore_booking' => 'Buchung wiederherstellen',
            'permanently_delete' => 'Endgültig löschen',
            'force_delete_title' => 'ENDGÜLTIGES LÖSCHEN',
            'force_delete_warning' => 'Diese Aktion KANN NICHT rückgängig gemacht werden!',
            'force_delete_message' => 'wird endgültig gelöscht.',
            'force_delete_data_loss' => 'Alle zugehörigen Daten gehen für immer verloren.',
            'force_delete_confirm' => 'Ja, ENDGÜLTIG LÖSCHEN',
            'booking_deleted' => 'Buchung gelöscht.',
            'booking_restored' => 'Buchung erfolgreich wiederhergestellt.',
            'booking_force_deleted' => 'Buchung endgültig gelöscht. Zahlungsaufzeichnungen für Audit aufbewahrt.',
            'force_delete_failed' => 'Buchung konnte nicht endgültig gelöscht werden.',
            'deleted_booking_indicator' => '(GELÖSCHT)',
        ],

        // Checkout Links (for admin-created bookings)
        'checkout_link_label' => 'Kunden-Zahlungslink',
        'checkout_link_description' => 'Senden Sie diesen Link an den Kunden, damit er die Zahlung für seine Buchung abschließen kann.',
        'checkout_link_copy' => 'Link kopieren',
        'checkout_link_copied' => 'Link kopiert!',
        'checkout_link_copy_failed' => 'Link konnte nicht kopiert werden. Bitte manuell kopieren.',
        'checkout_link_valid_until' => 'Gültig bis',
        'checkout_link_expired' => 'Dieser Zahlungslink ist abgelaufen oder nicht mehr gültig.',
        'checkout_link_accessed' => 'Kunde hat auf Checkout zugegriffen',

        // Payment Status
        'payment_status' => [
            'label' => 'Zahlungsstatus',
            'pending' => 'Ausstehend',
            'paid' => 'Bezahlt',
            'failed' => 'Fehlgeschlagen',
            'refunded' => 'Erstattet',
        ],
    ],

    // =========================================================
    // [03] AKTIONEN
    // =========================================================
    'actions' => [
        'confirm'        => 'Bestätigen',
        'cancel'         => 'Buchung stornieren',
        'confirm_cancel' => 'Möchtest du diese Buchung wirklich stornieren?',
        'remove' => 'Entfernen',
        'confirm_create' => 'Bestätigen und erstellen',
        'review_booking' => 'Buchung überprüfen',
        'apply'          => 'Anwenden',
    ],

    // =========================================================
    // [04] FILTER
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
    // [05] BERICHTE
    // =========================================================
    'reports' => [
        'excel_title'          => 'Buchungsexport',
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
        'surcharge'            => 'Aufschlag',
        'original_price'       => 'Originalpreis',
        'total_adults'         => 'Gesamt Erwachsene',
        'total_kids'           => 'Gesamt Kinder',
        'total_people'         => 'Gesamt Personen',
    ],

    // =========================================================
    // [06] BELEG
    // =========================================================
    'receipt' => [
        'title'         => 'Buchungsbeleg',
        'company'       => 'Green Vacations CR',
        'code'          => 'Code',
        'client'        => 'Kunde',
        'tour'          => 'Tour',
        'booking_date'  => 'Buchungsdatum',
        'tour_date'     => 'Tourdatum',
        'schedule'      => 'Uhrzeit',
        'hotel'         => 'Hotel',
        'meeting_point' => 'Treffpunkt',
        'status'        => 'Status',
        'adults_x'      => 'Erwachsene (x:count)',
        'kids_x'        => 'Kinder (x:count)',
        'people'        => 'Personen',
        'subtotal'      => 'Zwischensumme',
        'discount'      => 'Rabatt',
        'surcharge'     => 'Aufschlag',
        'total'         => 'GESAMT',
        'no_schedule'   => 'Keine Uhrzeit',
        'qr_alt'        => 'QR-Code',
        'qr_scan'       => 'Scanne, um die Buchung zu sehen',
        'thanks'        => 'Danke, dass du dich für :company entschieden hast!',
        'payment_status' => 'Zahlungsstatus:',
    ],

    // =========================================================
    // [07] DETAILS-MODAL
    // =========================================================
    'details' => [
        'booking_info'  => 'Buchungsinformationen',
        'customer_info' => 'Kundeninformationen',
        'tour_info'     => 'Tourinformationen',
        'pricing_info'  => 'Preisinformationen',
        'subtotal'      => 'Zwischensumme',
        'discount'      => 'Rabatt',
        'total_persons' => 'Gesamtpersonen',
    ],

    // =========================================================
    // [08] REISENDE (MODAL)
    // =========================================================
    'travelers' => [
        'title_warning'        => 'Achtung',
        'title_info'           => 'Information',
        'title_error'          => 'Fehler',
        'max_persons_reached'  => 'Maximal :max Personen pro Buchung.',
        'max_category_reached' => 'Das Maximum für diese Kategorie beträgt :max.',
        'invalid_quantity'     => 'Ungültige Menge. Bitte eine gültige Zahl eingeben.',
        'age_between'          => 'Alter :min-:max',
        'age_from'             => 'Alter :min+',
        'age_to'               => 'Bis :max Jahre',
    ],


    'excluded_dates' => [

        'ui' => [
            'page_title'           => 'Verfügbarkeits- und Kapazitätsverwaltung',
            'page_heading'         => 'Verfügbarkeits- und Kapazitätsverwaltung',
            'tours_count'          => 'Touren',
            'blocked_page_title'   => 'Blockierte Touren',
            'blocked_page_heading' => 'Blockierte Touren',
            'blocked_count'        => ':count blockierte Touren',
        ],

        'legend' => [
            'title'                 => 'Kapazitätslegende',
            'base_tour'             => 'Basis-Tour',
            'override_schedule'     => 'Uhrzeit-Override',
            'override_day'          => 'Tages-Override',
            'override_day_schedule' => 'Tag+Uhrzeit-Override',
            'blocked'               => 'Blockiert',
        ],

        'filters' => [
            'date'               => 'Datum',
            'days'               => 'Tage',
            'product'            => 'Tour suchen',
            'search_placeholder' => 'Name der Tour…',
            'bulk_actions'       => 'Massenaktionen',
            'update_state'       => 'Status aktualisieren',
        ],

        'blocks' => [
            'am'          => 'AM-TOUREN',
            'pm'          => 'PM-TOUREN',
            'am_blocked'  => 'AM-TOUREN (blockiert)',
            'pm_blocked'  => 'PM-TOUREN (blockiert)',
            'empty_am'    => 'Es gibt keine Touren in diesem Block',
            'empty_pm'    => 'Es gibt keine Touren in diesem Block',
            'no_data'     => 'Keine Daten zum Anzeigen vorhanden',
            'no_blocked'  => 'Es gibt keine blockierten Touren für den ausgewählten Zeitraum',
        ],

        'buttons' => [
            'mark_all'          => 'Alle markieren',
            'unmark_all'        => 'Markierung aufheben',
            'block_all'         => 'Alle blockieren',
            'unblock_all'       => 'Alle freigeben',
            'block_selected'    => 'Ausgewählte blockieren',
            'unblock_selected'  => 'Ausgewählte freigeben',
            'set_capacity'      => 'Kapazität anpassen',
            'capacity'          => 'Kapazität',
            'view_blocked'      => 'Blockierte anzeigen',
            'capacity_settings' => 'Kapazitätseinstellungen',
            'block'             => 'Blockieren',
            'unblock'           => 'Freigeben',
            'apply'             => 'Anwenden',
            'save'              => 'Speichern',
            'cancel'            => 'Abbrechen',
            'back'              => 'Zurück',
        ],

        'states' => [
            'available' => 'Verfügbar',
            'blocked'   => 'Blockiert',
        ],

        'badges' => [
            'tooltip_prefix' => 'Belegt/Kapazität -',
        ],

        'modals' => [
            'capacity_title'          => 'Kapazität anpassen',
            'selected_capacity_title' => 'Kapazität der ausgewählten Einträge anpassen',
            'date'                    => 'Datum:',
            'hierarchy_title'         => 'Kapazitätshierarchie:',
            'new_capacity'            => 'Neue Kapazität',
            'hint_zero_blocks'        => 'Auf 0 setzen, um komplett zu blockieren',
            'selected_count'          => 'Die Kapazität von :count ausgewählten Einträgen wird aktualisiert.',
            'capacity_day_title'      => 'Kapazität für den Tag anpassen',
            'capacity_day_subtitle'   => 'Alle Uhrzeiten des Tages',
        ],

        'confirm' => [
            'block_title'       => 'Blockieren?',
            'unblock_title'     => 'Freigeben?',
            'block_html'        => '<strong>:label</strong><br>Datum: :day',
            'unblock_html'      => '<strong>:label</strong><br>Datum: :day',
            'block_btn'         => 'Blockieren',
            'unblock_btn'       => 'Freigeben',
            'bulk_title'        => 'Massenaktion bestätigen',
            'bulk_items_html'   => ':count Einträge werden betroffen sein',
            'block_day_title'   => 'Gesamten Tag blockieren',
            'block_block_title' => 'Block :block am :day blockieren',
        ],

        'toasts' => [
            'invalid_date_title' => 'Ungültiges Datum',
            'invalid_date_text'  => 'Du kannst keine vergangenen Daten auswählen',
            'searching'          => 'Suche…',
            'applying_filters'   => 'Filter werden angewendet…',
            'updating_range'     => 'Zeitraum wird aktualisiert…',
            'no_selection_title' => 'Keine Auswahl',
            'no_selection_text'  => 'Du musst mindestens einen Eintrag auswählen',
            'no_changes_title'   => 'Keine Änderungen',
            'no_changes_text'    => 'Es gibt keine Einträge zum Aktualisieren',
            'marked_n'           => ':n Einträge markiert',
            'unmarked_n'         => ':n Einträge demarkiert',
            'error_generic'      => 'Die Operation konnte nicht abgeschlossen werden',
            'updated'            => 'Aktualisiert',
            'updated_count'      => ':count Einträge aktualisiert',
            'unblocked_count'    => ':count Einträge freigegeben',
            'blocked'            => 'Blockiert',
            'unblocked'          => 'Freigegeben',
            'capacity_updated'   => 'Kapazität aktualisiert',
        ],

    ],

    'capacity' => [

        // =========================================================
        // [01] UI-TITEL & ÜBERSCHRIFTEN
        // =========================================================
        'ui' => [
            'page_title'   => 'Kapazitätsverwaltung',
            'page_heading' => 'Kapazitätsverwaltung',
        ],

        // =========================================================
        // [02] TABS
        // =========================================================
        'tabs' => [
            'global'        => 'Global',
            'by_tour'       => 'Pro Tour + Uhrzeit',
            'day_schedules' => 'Overrides Tag + Uhrzeit',
        ],

        // =========================================================
        // [03] HINWEISE
        // =========================================================
        'alerts' => [
            'global_info'        => '<strong>Globale Kapazitäten:</strong> Definieren die Basisgrenze für jede Tour (alle Tage und Uhrzeiten).',
            'by_tour_info'       => '<strong>Pro Tour + Uhrzeit:</strong> Kapazitäts-Override für jede Uhrzeit jeder Tour. Diese Overrides haben Vorrang vor der globalen Tourkapazität.',
            'day_schedules_info' => '<strong>Tag + Uhrzeit:</strong> Override mit höchster Priorität für einen bestimmten Tag und eine bestimmte Uhrzeit. Diese werden in der Ansicht „Verfügbarkeit und Kapazität“ verwaltet.',
        ],

        // =========================================================
        // [04] TABELLENKÖPFE
        // =========================================================
        'tables' => [
            'global' => [
                'tour'     => 'Tour',
                'type'     => 'Typ',
                'capacity' => 'Globale Kapazität',
                'level'    => 'Level',
            ],
            'by_tour' => [
                'schedule'    => 'Uhrzeit',
                'capacity'    => 'Override-Kapazität',
                'level'       => 'Level',
                'no_schedules' => 'Diese Tour hat keine zugewiesenen Uhrzeiten',
            ],
            'day_schedules' => [
                'date'        => 'Datum',
                'tour'        => 'Tour',
                'schedule'    => 'Uhrzeit',
                'capacity'    => 'Kapazität',
                'actions'     => 'Aktionen',
                'no_overrides' => 'Es gibt keine Overrides für Tag + Uhrzeit',
            ],
        ],

        // =========================================================
        // [05] BADGES / LABELS
        // =========================================================
        'badges' => [
            'base'      => 'Basis',
            'override'  => 'Override',
            'global'    => 'Global',
            'blocked'   => 'BLOCKIERT',
            'unlimited' => '∞',
        ],

        // =========================================================
        // [06] BUTTONS
        // =========================================================
        'buttons' => [
            'save'   => 'Speichern',
            'delete' => 'Löschen',
            'back'   => 'Zurück',
            'apply'  => 'Anwenden',
            'cancel' => 'Abbrechen',
        ],

        // =========================================================
        // [07] NACHRICHTEN
        // =========================================================
        'messages' => [
            'empty_placeholder' => 'Leer = globale Kapazität verwenden (:capacity)',
            'deleted_confirm'   => 'Diesen Override löschen?',
            'no_day_overrides'  => 'Es gibt keine Overrides für Tag + Uhrzeit.',
        ],

        // =========================================================
        // [08] TOASTS (SweetAlert2)
        // =========================================================
        'toasts' => [
            'success_title' => 'Erfolg',
            'error_title'   => 'Fehler',
        ],
    ],

];
