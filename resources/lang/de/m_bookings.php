<?php

return [

    'messages' => [
        'date_no_longer_available' => 'Das Datum :date steht nicht mehr zur Buchung zur Verfügung (Mindest­alter: :min).',
        'limited_seats_available' => 'Es sind nur noch :available Plätze für „:tour“ am :date verfügbar.',
        'bookings_created_from_cart' => 'Ihre Buchungen wurden erfolgreich aus dem Warenkorb erstellt.',
        'capacity_exceeded' => 'Kapazität überschritten',
        'meeting_point_hint' => 'Nur der Name des Treffpunkts wird in der Liste angezeigt.',
    ],

    'validation' => [
        'max_persons_exceeded' => 'Maximal :max Personen pro Buchung insgesamt.',
        'min_adults_required' => 'Mindestens :min Erwachsene pro Buchung erforderlich.',
        'max_kids_exceeded' => 'Maximal :max Kinder pro Buchung.',
        'no_active_categories' => 'Für diese Tour gibt es keine aktiven Kundengruppen.',
        'min_category_not_met' => 'Mindestens :min Personen sind in der Kategorie „:category“ erforderlich.',
        'max_category_exceeded' => 'Maximal :max Personen sind in der Kategorie „:category“ erlaubt.',
        'min_one_person_required' => 'Es muss mindestens eine Person in der Buchung sein.',
        'category_not_available' => 'Die Kategorie mit der ID :category_id ist für diese Tour nicht verfügbar.',
        'max_persons_label' => 'Maximale Personenzahl pro Buchung',
        'date_range_hint' => 'Wählen Sie ein Datum zwischen :from — :to',
    ],

    // =========================================================
    // [01] VERFÜGBARKEIT
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
                'required' => 'Das :attribute ist erforderlich.',
                'integer'  => 'Das :attribute muss eine Ganzzahl sein.',
                'exists'   => 'Die gewählte :attribute-Auswahl existiert nicht.',
            ],
            'date' => [
                'required'    => 'Das :attribute ist erforderlich.',
                'date_format' => 'Das :attribute muss im Format YYYY-MM-DD vorliegen.',
            ],
            'start_time' => [
                'date_format'   => 'Die :attribute muss im Format HH:MM (24h) sein.',
                'required_with' => 'Die :attribute ist erforderlich, wenn die Endzeit angegeben ist.',
            ],
            'end_time' => [
                'date_format'    => 'Die :attribute muss im Format HH:MM (24h) sein.',
                'after_or_equal' => 'Die :attribute muss größer oder gleich der Startzeit sein.',
            ],
            'available' => [
                'boolean' => 'Das Feld :attribute ist ungültig.',
            ],
            'is_active' => [
                'boolean' => 'Das :attribute ist ungültig.',
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
            'search_placeholder' => 'Tour suchen…',
            'update_state'       => 'Status aktualisieren',
            'view_blocked'       => 'Blockierte anzeigen',
            'tip'                => 'Tipp: Zeilen markieren und Menüaktion verwenden.',
        ],

        'blocks' => [
            'am_tours'    => 'AM-Touren (alle Touren, die vor 12:00 Uhr beginnen)',
            'pm_tours'    => 'PM-Touren (alle Touren, die nach 12:00 Uhr beginnen)',
            'am_blocked'  => 'AM blockiert',
            'pm_blocked'  => 'PM blockiert',
            'empty_block' => 'Keine Touren in diesem Block.',
            'empty_am'    => 'Keine blockierten AM-Touren.',
            'empty_pm'    => 'Keine blockierten PM-Touren.',
            'no_data'     => 'Keine Daten für die ausgewählten Filter.',
            'no_blocked'  => 'Keine Touren im ausgewählten Bereich blockiert.',
        ],

        'states' => [
            'available' => 'Verfügbar',
            'blocked'   => 'Blockiert',
        ],

        'buttons' => [
            'mark_all'         => 'Alle markieren',
            'unmark_all'       => 'Alle demarkieren',
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
            'view_blocked_text'     => 'Die Ansicht mit blockierten Touren wird geöffnet, um sie freizugeben.',
            'block_title'           => 'Tour blockieren?',
            'block_html'            => '<b>:label</b> wird am <b>:day</b> blockiert.',
            'block_btn'             => 'Ja, blockieren',
            'unblock_title'         => 'Tour freigeben?',
            'unblock_html'          => '<b>:label</b> wird am <b>:day</b> freigegeben.',
            'unblock_btn'           => 'Ja, freigeben',
            'bulk_title'            => 'Aktion bestätigen',
            'bulk_items_html'       => 'Zu bearbeitende Elemente: <b>:count</b>.',
            'bulk_block_day_html'   => 'Alle verfügbaren am Tag <b>:day</b> blockieren',
            'bulk_block_block_html' => 'Alle verfügbaren im Block <b>:block</b> am <b>:day</b> blockieren',
        ],

        'toasts' => [
            'applying_filters'   => 'Filter werden angewendet…',
            'searching'          => 'Suche…',
            'updating_range'     => 'Bereich wird aktualisiert…',
            'invalid_date_title' => 'Ungültiges Datum',
            'invalid_date_text'  => 'Vergangene Daten sind nicht erlaubt.',
            'marked_n'           => ':n markiert',
            'unmarked_n'         => ':n demarkiert',
            'updated'            => 'Änderung übernommen',
            'updated_count'      => 'Aktualisiert: :count',
            'unblocked_count'    => 'Freigegeben: :count',
            'no_selection_title' => 'Keine Auswahl',
            'no_selection_text'  => 'Markieren Sie mindestens eine Tour.',
            'no_changes_title'   => 'Keine Änderungen',
            'no_changes_text'    => 'Es gibt keine zutreffenden Elemente.',
            'error_generic'      => 'Update konnte nicht abgeschlossen werden.',
            'error_update'       => 'Aktualisierung fehlgeschlagen.',
        ],
    ],

    // =========================================================
    // [02] BUCHUNGEN
    // =========================================================
    'bookings' => [
        'ui' => [
            'page_title'         => 'Buchungen',
            'page_heading'       => 'Buchungsverwaltung',
            'register_booking'   => 'Buchung registrieren',
            'add_booking'        => 'Buchung hinzufügen',
            'edit_booking'       => 'Buchung bearbeiten',
            'booking_details'    => 'Buchungsdetails',
            'download_receipt'   => 'Quittung herunterladen',
            'actions'            => 'Aktionen',
            'view_details'       => 'Details anzeigen',
            'click_to_view'      => 'Klicken, um Details zu sehen',
            'zoom_in'            => 'Vergrößern',
            'zoom_out'           => 'Verkleinern',
            'zoom_reset'         => 'Zoom zurücksetzen',
            'no_promo'           => 'Kein Rabattcode angewendet',
            'create_booking'     => 'Buchung erstellen',
            'booking_info'       => 'Buchungsinformation',
            'select_customer'    => 'Kunde auswählen',
            'select_tour'        => 'Tour auswählen',
            'select_tour_first'  => 'Wählen Sie zuerst eine Tour',
            'select_option'      => 'Auswählen',
            'select_tour_to_see_categories' => 'Wählen Sie eine Tour, um Kategorien zu sehen',
            'loading'            => 'Lädt…',
            'no_results'         => 'Keine Ergebnisse',
            'error_loading'      => 'Fehler beim Laden der Daten',
            'tour_without_categories' => 'Diese Tour hat keine Kategorien eingerichtet',
            'verifying'          => 'Überprüfung…',
        ],

        'fields' => [
            'booking_id'        => 'Buchungs-ID',
            'status'            => 'Status',
            'booking_date'      => 'Buchungsdatum',
            'booking_origin'    => 'Buchungsdatum (Herkunft)',
            'reference'         => 'Reference',
            'customer'          => 'Kunde',
            'email'             => 'E-Mail',
            'phone'             => 'Telefon',
            'tour'              => 'Tour',
            'language'          => 'Sprache',
            'tour_date'         => 'Tour-Datum',
            'hotel'             => 'Hotel',
            'other_hotel'       => 'Name eines anderen Hotels',
            'meeting_point'     => 'Treffpunkt',
            'pickup_location'   => 'Abholort',
            'schedule'          => 'Zeitplan',
            'type'              => 'Typ',
            'adults'            => 'Erwachsene',
            'adults_quantity'   => 'Anzahl Erwachsene',
            'children'          => 'Kinder',
            'children_quantity' => 'Anzahl Kinder',
            'promo_code'        => 'Rabattcode',
            'total'             => 'Gesamt',
            'total_to_pay'      => 'Zu zahlender Gesamtbetrag',
            'adult_price'       => 'Erwachsenenpreis',
            'child_price'       => 'Kinderpreis',
            'notes'             => 'Notizen',
            'hotel_name'        => 'Hotelname',
            'travelers'         => 'Reisende',
            'subtotal'          => 'Zwischensumme',
            'discount'          => 'Rabatt',
            'total_persons'     => 'Personenanzahl',
        ],

        'placeholders' => [
            'select_customer'  => 'Kunde auswählen',
            'select_tour'      => 'Tour auswählen',
            'select_schedule'  => 'Zeitplan auswählen',
            'select_language'  => 'Sprache auswählen',
            'select_hotel'     => 'Hotel auswählen',
            'select_point'     => 'Treffpunkt auswählen',
            'select_status'    => 'Status auswählen',
            'enter_hotel_name' => 'Geben Sie den Hotelnamen ein',
            'enter_promo_code' => 'Geben Sie den Rabattcode ein',
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
            'time'     => 'Zeit:',
            'view_map' => 'Karte anzeigen',
        ],

        'pricing' => [
            'title' => 'Preisübersicht',
        ],

        'optional' => 'optional',

        'messages' => [
            'past_booking_warning'   => 'Diese Buchung betrifft ein vergangenes Datum und kann nicht bearbeitet werden.',
            'tour_archived_warning'  => 'Die Tour für diese Buchung wurde gelöscht/archiviert und konnte nicht geladen werden. Bitte wählen Sie eine Tour, um die Zeitpläne anzuzeigen.',
            'no_schedules'           => 'Keine Zeitpläne verfügbar',
            'deleted_tour'           => 'Tour gelöscht',
            'deleted_tour_snapshot'  => 'Tour gelöscht (:name)',
            'tour_archived'          => '(archiviert)',
            'meeting_point_hint'     => 'Nur der Name des Treffpunkts wird in der Liste angezeigt.',
            'customer_locked'        => 'Der Kunde ist gesperrt und kann nicht bearbeitet werden.',
            'promo_applied_subtract' => 'Rabatt angewandt:',
            'promo_applied_add'      => 'Aufpreis angewandt:',
            'hotel_locked_by_meeting_point' => 'Ein Treffpunkt wurde ausgewählt; Hotel kann nicht ausgewählt werden.',
            'meeting_point_locked_by_hotel' => 'Ein Hotel wurde ausgewählt; Treffpunkt kann nicht ausgewählt werden.',
            'promo_removed'          => 'Rabattcode entfernt',
        ],

        'alerts' => [
            'error_summary' => 'Bitte korrigieren Sie die folgenden Fehler:',
        ],

        'validation' => [
            'past_date'       => 'Buchungen für vergangene Daten sind nicht möglich.',
            'promo_required'  => 'Geben Sie zuerst einen Rabattcode ein.',
            'promo_checking'  => 'Code wird geprüft…',
            'promo_invalid'   => 'Ungültiger Rabattcode.',
            'promo_error'     => 'Der Code konnte nicht geprüft werden.',
            'promo_empty'     => 'Geben Sie zuerst einen Code ein.',
            'promo_needs_subtotal' => 'Fügen Sie mindestens 1 Passagier hinzu, um den Rabatt zu berechnen.',
        ],

        'promo' => [
            'applied'         => 'Code angewendet',
            'applied_percent' => 'Code angewendet: -:percent%',
            'applied_amount'  => 'Code angewendet: -$:amount',
        ],

        'loading' => [
            'saving'     => 'Speichern…',
            'validating' => 'Validieren…',
            'updating'   => 'Aktualisieren…',
        ],

        'success' => [
            'created'          => 'Buchung erfolgreich erstellt.',
            'updated'          => 'Buchung erfolgreich aktualisiert.',
            'deleted'          => 'Buchung erfolgreich gelöscht.',
            'status_updated'   => 'Buchungsstatus erfolgreich aktualisiert.',
            'status_confirmed' => 'Buchung erfolgreich bestätigt.',
            'status_cancelled' => 'Buchung erfolgreich storniert.',
            'status_pending'   => 'Buchung erfolgreich auf ausstehend gesetzt.',
        ],

        'errors' => [
            'create'               => 'Die Buchung konnte nicht erstellt werden.',
            'update'               => 'Die Buchung konnte nicht aktualisiert werden.',
            'delete'               => 'Die Buchung konnte nicht gelöscht werden.',
            'status_update_failed' => 'Der Buchungsstatus konnte nicht aktualisiert werden.',
            'detail_not_found'     => 'Buchungsdetails nicht gefunden.',
            'schedule_not_found'   => 'Zeitplan nicht gefunden.',
            'insufficient_capacity'=> 'Nicht genügend Kapazität für „:tour“ am :date um :time vorhanden. Gewünscht: :requested, verfügbar: :available (max: :max).',
        ],

        'confirm' => [
            'delete' => 'Sind Sie sicher, dass Sie diese Buchung löschen möchten?',
        ],
    ],

    // =========================================================
    // [03] AKTIONEN
    // =========================================================
    'actions' => [
        'confirm'        => 'Bestätigen',
        'cancel'         => 'Buchung stornieren',
        'confirm_cancel' => 'Sind Sie sicher, dass Sie diese Buchung stornieren möchten?',
    ],

    // =========================================================
    // [04] FILTER
    // =========================================================
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
        'close_filters'    => 'Filter schließen',
        'search_reference' => 'Referenz suchen…',
        'enter_reference'  => 'Buchungs­referenz eingeben',
    ],

    // =========================================================
    // [05] BERICHTE
    // =========================================================
    'reports' => [
        'excel_title'          => 'Buchungs­export',
        'pdf_title'            => 'Buchungs­bericht – Green Vacations CR',
        'general_report_title' => 'Allgemeiner Buchungs­bericht – Green Vacations Costa Rica',
        'download_pdf'         => 'PDF herunterladen',
        'export_excel'         => 'Excel exportieren',
        'coupon'               => 'Coupon',
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

    // =========================================================
    // [06] QUITTUNG
    // =========================================================
    'receipt' => [
        'title'         => 'Buchungs­quittung',
        'company'       => 'Green Vacations CR',
        'code'          => 'Code',
        'client'        => 'Kunde',
        'tour'          => 'Tour',
        'booking_date'  => 'Buchungs­datum',
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
        'qr_alt'        => 'QR Code',
        'qr_scan'       => 'Scannen, um die Buchung zu sehen',
        'thanks'        => 'Danke, dass Sie sich für :company entschieden haben!',
    ],

    // =========================================================
    // [07] DETAILS MODAL
    // =========================================================
    'details' => [
        'booking_info'  => 'Buchungs­information',
        'customer_info' => 'Kunden­information',
        'tour_info'     => 'Tour­information',
        'pricing_info'  => 'Preis­information',
        'subtotal'      => 'Zwischensumme',
        'discount'      => 'Rabatt',
    ],

    'excluded_dates' => [

        'ui' => [
            'page_title'           => 'Verfügbarkeits- & Kapazitäts­verwaltung',
            'page_heading'         => 'Verfügbarkeits- & Kapazitäts­verwaltung',
            'tours_count'          => 'Touren',
            'blocked_page_title'   => 'Blockierte Touren',
            'blocked_page_heading' => 'Blockierte Touren',
            'blocked_count'        => ':count blockierte Touren',
        ],

        'legend' => [
            'title'                  => 'Kapazitätslegende',
            'base_tour'              => 'Basis-Tour',
            'override_schedule'      => 'Zeitplan überschreiben',
            'override_day'           => 'Tag überschreiben',
            'override_day_schedule'  => 'Tag+Zeitplan überschreiben',
            'blocked'                => 'Blockiert',
        ],

        'filters' => [
            'date'               => 'Datum',
            'days'               => 'Tage',
            'product'            => 'Tour suchen',
            'search_placeholder' => 'Tourname…',
            'bulk_actions'       => 'Massenaktionen',
            'update_state'       => 'Status aktualisieren',
        ],

        'blocks' => [
            'am'          => 'AM TOUREN',
            'pm'          => 'PM TOUREN',
            'am_blocked'  => 'AM TOUREN (blockiert)',
            'pm_blocked'  => 'PM TOUREN (blockiert)',
            'empty_am'    => 'Keine Touren in diesem Block',
            'empty_pm'    => 'Keine Touren in diesem Block',
            'no_data'     => 'Keine Daten zur Anzeige',
            'no_blocked'  => 'Keine blockierten Touren für den ausgewählten Bereich',
        ],

        'buttons' => [
            'mark_all'         => 'Alle markieren',
            'unmark_all'       => 'Alle demarkieren',
            'block_all'        => 'Alle blockieren',
            'unblock_all'      => 'Alle freigeben',
            'block_selected'   => 'Ausgewählte blockieren',
            'unblock_selected' => 'Ausgewählte freigeben',
            'set_capacity'     => 'Kapazität anpassen',
            'capacity'         => 'Kapazität',
            'view_blocked'     => 'Blockierte anzeigen',
            'capacity_settings'=> 'Kapazitäts­einstellungen',
            'block'            => 'Blockieren',
            'unblock'          => 'Freigeben',
            'apply'            => 'Anwenden',
            'save'             => 'Speichern',
            'cancel'           => 'Abbrechen',
            'back'             => 'Zurück',
        ],

        'states' => [
            'available' => 'Verfügbar',
            'blocked'   => 'Blockiert',
        ],

        'badges' => [
            'tooltip_prefix' => 'Belegt/Kapazität -',
        ],

        'modals' => [
            'capacity_title'            => 'Kapazität anpassen',
            'selected_capacity_title'   => 'Kapazität der Ausgewählten anpassen',
            'date'                      => 'Datum:',
            'hierarchy_title'           => 'Kapazitäts­hierarchie:',
            'new_capacity'              => 'Neue Kapazität',
            'hint_zero_blocks'          => 'Für vollständige Blockierung 0 lassen',
            'selected_count'            => 'Die Kapazität wird für :count ausgewählte Elemente aktualisiert.',
            'capacity_day_title'        => 'Kapazität für den Tag anpassen',
            'capacity_day_subtitle'     => 'Alle Zeitpläne des Tages',
        ],

        'confirm' => [
            'block_title'        => 'Blockieren?',
            'unblock_title'      => 'Freigeben?',
            'block_html'         => '<strong>:label</strong><br>Datum: :day',
            'unblock_html'       => '<strong>:label</strong><br>Datum: :day',
            'block_btn'          => 'Blockieren',
            'unblock_btn'        => 'Freigeben',
            'bulk_title'         => 'Massen­operation bestätigen',
            'bulk_items_html'    => ':count Elemente werden betroffen sein',
            'block_day_title'    => 'Ganzen Tag blockieren',
            'block_block_title'  => 'Block :block am :day blockieren',
        ],

        'toasts' => [
            'invalid_date_title' => 'Ungültiges Datum',
            'invalid_date_text'  => 'Sie können keine vergangenen Daten auswählen',
            'searching'          => 'Suche…',
            'applying_filters'   => 'Filter werden angewendet…',
            'updating_range'     => 'Bereich wird aktualisiert…',
            'no_selection_title' => 'Keine Auswahl',
            'no_selection_text'  => 'Mindestens ein Element muss ausgewählt werden',
            'no_changes_title'   => 'Keine Änderungen',
            'no_changes_text'    => 'Es gibt keine Elemente zu aktualisieren',
            'marked_n'           => 'Markiert :n Elemente',
            'unmarked_n'         => 'Demarkiert :n Elemente',
            'error_generic'      => 'Operation konnte nicht abgeschlossen werden',
            'updated'            => 'Aktualisiert',
            'updated_count'      => ':count Elemente aktualisiert',
            'unblocked_count'    => ':count Elemente freigegeben',
            'blocked'            => 'Blockiert',
            'unblocked'          => 'Freigegeben',
            'capacity_updated'   => 'Kapazität aktualisiert',
        ],
    ],

    'capacity' => [

    'ui' => [
        'page_title'   => 'Kapazitätsverwaltung',
        'page_heading' => 'Kapazitätsverwaltung',
    ],

    'tabs' => [
        'global'         => 'Global',
        'by_tour'        => 'Nach Tour + Zeitplan',
        'day_schedules'  => 'Überschreibungen Tag + Zeitplan',
    ],

    'alerts' => [
        'global_info' => '<strong>Globale Kapazitäten:</strong> Definiere das Basislimit für jede Tour (alle Tage und Zeiten).',
        'by_tour_info' => '<strong>Nach Tour + Zeitplan:</strong> Spezifische Kapazitätsüberschreibung für jeden Zeitplan einer Tour. Diese Überschreibungen haben Vorrang vor der globalen Tourkapazität.',
        'day_schedules_info' => '<strong>Tag + Zeitplan:</strong> Überschreibung mit höchster Priorität für einen bestimmten Tag und Zeitplan. Diese werden in der Ansicht „Verfügbarkeit und Kapazität“ verwaltet.',
    ],

    'tables' => [
        'global' => [
            'tour'       => 'Tour',
            'type'       => 'Typ',
            'capacity'   => 'Globale Kapazität',
            'level'      => 'Ebene',
        ],
        'by_tour' => [
            'schedule'   => 'Zeitplan',
            'capacity'   => 'Kapazitätsüberschreibung',
            'level'      => 'Ebene',
            'no_schedules' => 'Diese Tour hat keine zugewiesenen Zeitpläne',
        ],
        'day_schedules' => [
            'date'       => 'Datum',
            'tour'       => 'Tour',
            'schedule'   => 'Zeitplan',
            'capacity'   => 'Kapazität',
            'actions'    => 'Aktionen',
            'no_overrides' => 'Keine Überschreibungen für Tag + Zeitplan vorhanden',
        ],
    ],

    'badges' => [
        'base'       => 'Basis',
        'override'   => 'Überschreibung',
        'global'     => 'Global',
        'blocked'    => 'GESPERRT',
        'unlimited'  => '∞',
    ],

    'buttons' => [
        'save'      => 'Speichern',
        'delete'    => 'Löschen',
        'back'      => 'Zurück',
        'apply'     => 'Anwenden',
        'cancel'    => 'Abbrechen',
    ],

    'messages' => [
        'empty_placeholder' => 'Leer = verwendet globale Kapazität (:capacity)',
        'deleted_confirm'   => 'Diese Überschreibung löschen?',
        'no_day_overrides'  => 'Keine Überschreibungen für Tag + Zeitplan vorhanden.',
    ],

    'toasts' => [
        'success_title' => 'Erfolg',
        'error_title'   => 'Fehler',
    ],
],

];
