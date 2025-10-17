<?php

/**
 * Table of Contents
 *
 * 1. AUTHENTICATION AND REGISTRATION ........... Line 37
 * 2. HOTELS ................................... Line 57
 * 3. GENERAL NAVIGATION ....................... Line 67
 * 4. CONTENT AND PAGES ........................ Line 82
 * 5. TOURS AND REVIEWS ........................ Line 97
 * 6. SCHEDULES ................................ Line 131
 * 7. ITINERARIES .............................. Line 144
 * 8. HOTELS (DETAIL) .......................... Line 156
 * 9. CART AND BOOKINGS ........................ Line 180
 * 10. VALIDATION .............................. Line 219
 * 11. BUTTONS AND CRUD ........................ Line 225
 * 12. FOOTER .................................. Line 243
 * 13. WHATSAPP ................................ Line 247
 * 14. REVIEWS ................................. Line 257
 * 15. TRAVELERS ............................... Line 273
 * 16. CONTACT ................................. Line 286
 * 17. ERRORS .................................. Line 295
 * 18. CART LOGIN MODAL ........................ Line 298
 * 19. SWEETALERTS (ACTIONS) ................... Line 322
 * 20. SUCCESSES (USED IN CONTROLLERS) ......... Line 328
 * 21. MAIL .................................... Line 381
 * 22. DASHBOARD ............................... Line 386
 * 23. ENTITIES ................................ Line 394
 * 24. SECTIONS ................................ Line 408
 * 25. EMPTY STATES ............................ Line 414
 * 26. BUTTONS (GENERIC) ....................... Line 421
 * 27. LABELS .................................. Line 426
 */

return [

    // 1. AUTHENTICATION AND REGISTRATION
    'hello' => 'Hallo',
    'full_name' => 'Vollständiger Name',
    'email' => 'E-Mail',
    'password' => 'Passwort',
    'phone' => 'Telefon',
    'retype_password' => 'Passwort erneut eingeben',
    'remember_me' => 'Angemeldet bleiben',
    'remember_me_hint' => 'Sitzung unbegrenzt offen halten oder manuell schließen',
    'register' => 'Registrieren',
    'promo_invalid' => 'Ungültiger Aktionscode.',
    'promo_already_used' => 'Dieser Aktionscode wurde bereits bei einer anderen Buchung verwendet.',
    'no_past_dates' => 'Sie können keine Buchungen für vergangene Daten vornehmen.',
    'dupe_submit_cart' => 'Eine ähnliche Buchung wird bereits bearbeitet. Bitte versuchen Sie es in ein paar Sekunden erneut.',
    'schedule_not_available' => 'Der Zeitplan ist für diese Tour nicht verfügbar (inaktiv oder nicht zugewiesen).',
    'date_blocked' => 'Das ausgewählte Datum ist für diese Tour gesperrt.',
    'capacity_left' => 'Nur noch :available Plätze für diesen Zeitplan verfügbar.',
    'booking_created_success' => 'Buchung erfolgreich erstellt.',
    'booking_updated_success' => 'Buchung erfolgreich aktualisiert.',

    // 2. HOTELS
    'hotel_name_required' => 'Hotelname ist erforderlich.',
    'hotel_name_unique'   => 'Ein Hotel mit diesem Namen existiert bereits.',
    'hotel_name_max'      => 'Hotelname darf :max Zeichen nicht überschreiten.',
    'hotel_created_success' => 'Hotel erfolgreich erstellt.',
    'hotel_updated_success' => 'Hotel erfolgreich aktualisiert.',
    'is_active_required'  => 'Status ist erforderlich.',
    'is_active_boolean'   => 'Status muss wahr oder falsch sein.',
    'outside_list' => 'Dieses Hotel befindet sich außerhalb unserer Liste. Bitte kontaktieren Sie uns, um zu prüfen, ob ein Transport möglich ist.',
    'meeting_valid' => 'Der ausgewählte Treffpunkt ist gültig! Sobald Sie Ihre Buchung bestätigt haben, senden wir Ihnen die Anweisungen und die genaue Treffzeit.',
    'meeting_point' => 'Treffpunkt',
    'meetingPoint'  => 'Treffpunkt',
    'meeting_point_details' => 'Details zum Treffpunkt',
    'selectMeetingHelp' => 'Wählen Sie einen Treffpunkt aus der Liste.',
    'selectHotelHelp' => 'Wählen Sie Ihr Hotel aus der Liste.',
    'pickup_time' => 'Abholzeit',
    'pickupTime'  => 'Abholzeit',
    'meeting_time' => 'Treffzeit',
    'meetingTime'  => 'Treffzeit',
    'open_map' => 'Karte öffnen',
    'openMap'  => 'Karte öffnen',

    // 3. GENERAL NAVIGATION
    'back' => 'Zurück',
    'home' => 'Startseite',
    'dashboard_menu' => 'Dashboard', // (renombrado para evitar colisión con la sección 'dashboard')
    'profile' => 'Profil',
    'settings' => 'Einstellungen',
    'users' => 'Benutzer',
    'roles' => 'Rollen',
    'notifications' => 'Benachrichtigungen',
    'messages' => 'Nachrichten',
    'help' => 'Hilfe',
    'language' => 'Sprache',
    'support' => 'Support',
    'admin_panel' => 'Admin-Bereich',

    // 4. CONTENT AND PAGES
    'faq' => 'Häufig gestellte Fragen',
    'faqpage' => 'Häufig gestellte Fragen',
    'no_faqs_available' => 'Keine FAQs verfügbar.',
    'contact' => 'Kontakt',
    'about' => 'Über uns',
    'privacy_policy' => 'Datenschutzrichtlinie',
    'terms_and_conditions' => 'Allgemeine Geschäftsbedingungen',
    'all_policies' => 'Alle unsere Richtlinien',
    'cancellation_and_refunds_policies' => 'Stornierungs- und Rückerstattungsrichtlinien',
    'reports' => 'Berichte',
    'footer_text'=> 'Green Vacations CR',
    'quick_links'=> 'Schnellzugriffe',
    'rights_reserved' => 'Alle Rechte vorbehalten',

    // 5. TOURS AND REVIEWS
    'tours' => 'Touren',
    'tour' => 'Tour',
    'tour_name' => 'Tourname',
    'overview' => 'Überblick',
    'duration' => 'Dauer',
    'price' => 'Preis',
    'type' => 'Tourtyp',
    'languages_available' => 'Verfügbare Sprachen',
    'amenities_included' => 'Inklusive Leistungen',
    'excluded_amenities' => 'Ausgeschlossene Leistungen',
    'tour_details' => 'Tourdaten',
    'select_tour' => 'Tour auswählen',
    'reviews' => 'Bewertungen',
    'hero_title' => 'Entdecken Sie die Magie von Costa Rica',
    'hero_subtext' => 'Entdecken Sie unsere einzigartigen Touren und erleben Sie das Abenteuer.',
    'book_now' => 'Jetzt buchen',
    'our_tours' => 'Unsere Touren',
    'half_day' => 'Halbtags',
    'full_day' => 'Ganztags',
    'full_day_description' => 'Perfekt für alle, die ein ganztägiges Erlebnis suchen',
    'half_day_description' => 'Ideale Touren für ein kurzes Abenteuer für alle mit wenig Zeit.',
    'full_day_tours' => 'Ganztagestouren',
    'half_day_tours' => 'Halbtagestouren',
    'see_tour' => 'Tour ansehen',
    'see_tours' => 'Touren ansehen',
    'what_visitors_say' => 'Was unsere Besucher sagen',
    'quote_1' => 'Ein unvergessliches Erlebnis!',
    'guest_1' => 'Carlos M.',
    'quote_2' => 'Ich komme definitiv wieder.',
    'guest_2' => 'Ana G.',
    'tour_information'=> 'Tourinformationen',
    'group_size'=> 'Gruppengröße',

    // 6. SCHEDULES
    'schedule' => 'Zeitplan',
    'schedule_am' => 'Vormittagszeitplan',
    'schedule_pm' => 'Nachmittagszeitplan',
    'start_time' => 'Startzeit',
    'end_time' => 'Endzeit',
    'select_date' => 'Datum auswählen',
    'select_time' => 'Uhrzeit auswählen',
    'select_language' => 'Sprache auswählen',
    'schedules' => 'Zeitpläne',
    'horas' => 'Stunden',
    'hours' => 'Stunden',

    // 7. ITINERARIES
    'itinerary' => 'Reiseplan',
    'itineraries' => 'Reisepläne',
    'new_itinerary' => 'Neuer Reiseplan',
    'itinerary_items' => 'Reiseplanpunkte',
    'item_title' => 'Punkt-Titel',
    'item_description' => 'Punkt-Beschreibung',
    'add_item' => 'Punkt hinzufügen',
    'edit_itinerary' => 'Reiseplan bearbeiten',
    'no_itinerary_info' => 'Keine Reiseplaninformationen.',
    'whats_included' => 'Was ist enthalten',

    // 8. HOTELS (DETAIL)
    'hotels' => 'Hotels',
    'hotel' => 'Hotel',
    'select_hotel' => 'Hotel',
    'hotel_other' => 'Andere (manuell angeben)',
    'hotel_name' => 'Hotelname',
    'other_hotel' => 'Anderes Hotel (angeben)',
    'hotel_pickup' => 'Hotelabholung',
    'outside_area' => 'Dieses Hotel liegt außerhalb des Abdeckungsbereichs. Bitte kontaktieren Sie uns, um Ihre Optionen zu besprechen.',
    'pickup_valid' => 'Das ausgewählte Hotel ist gültig! Nach Bestätigung der Buchung kontaktieren wir Sie, um die Abholzeit zu koordinieren.',
    'pickup_details' => 'Abholdetails',
    'pickup_note' => 'Kostenlose Abholung gilt nur für Hotels im Bereich La Fortuna...',
    'pickup_points' => 'Abholpunkte',
    'select_pickup' => 'Abholpunkt auswählen',
    'type_to_search' => 'Zum Suchen tippen...',
    'no_pickup_available' => 'Keine Abholpunkte verfügbar.',
    'pickup_not_found' => 'Hotel nicht gefunden.',
    'meeting_points' => 'Treffpunkte',
    'select_meeting' => 'Treffpunkt auswählen',
    'meeting_not_found' => 'Treffpunkt nicht gefunden.',
    'main_street_entrance' => 'Haupteingang',
    'example_address' => 'Beispieladresse 123',
    'hotels_meeting_points' => 'Hotels und Treffpunkte',
    'selectFromList'      => 'Wählen Sie ein Element aus der Liste',
    'fillThisField'       => 'Bitte füllen Sie dieses Feld aus',
    'pickupRequiredTitle' => 'Abholung erforderlich',
    'pickupRequiredBody'  => 'Sie müssen entweder ein Hotel oder einen Treffpunkt auswählen, um fortzufahren.',
    'ok'                  => 'OK',

    // 9. CART AND BOOKINGS
    'cart' => 'Warenkorb',
    'myCart' => 'Mein Warenkorb',
    'my_reservations' => 'Meine Buchungen',
    'your_cart' => 'Ihr Warenkorb',
    'add_to_cart' => 'In den Warenkorb',
    'remove_from_cart' => 'Aus dem Warenkorb entfernen',
    'confirm_reservation' => 'Buchung bestätigen',
    'confirmBooking' => 'Buchung bestätigen',
    'cart_updated' => 'Warenkorb erfolgreich aktualisiert.',
    'itemUpdated' => 'Warenkorb-Artikel erfolgreich aktualisiert.',
    'cartItemAdded' => 'Tour erfolgreich zum Warenkorb hinzugefügt.',
    'cartItemDeleted' => 'Tour erfolgreich aus dem Warenkorb entfernt.',
    'emptyCart' => 'Ihr Warenkorb ist leer.',
    'no_items_in_cart' => 'Ihr Warenkorb ist leer.',
    'reservation_success' => 'Buchung erfolgreich abgeschlossen!',
    'reservation_failed' => 'Beim Buchen ist ein Fehler aufgetreten.',
    'booking_reference' => 'Buchungsreferenz',
    'booking_date' => 'Buchungsdatum',
    'reservation_status' => 'Buchungsstatus',
    'blocked_date_for_tour' => 'Das Datum :date ist für ":tour" gesperrt.',
    'tourCapacityFull' => 'Die maximale Kapazität für diese Tour ist bereits erreicht.',
    'totalEstimated' => 'Geschätzte Gesamtsumme',
    'total_price' => 'Gesamtpreis',
    'total' => 'Gesamt',
    'date'=> 'Datum',
    'status' => 'Status',
    'actions' => 'Aktionen',
    'active'=> 'Aktiv',
    'delete'=> 'Löschen',
    'promoCode' => 'Haben Sie einen Aktionscode?',
    'promoCodePlaceholder' => 'Geben Sie Ihren Aktionscode ein',
    'apply' => 'Anwenden',
    'deleteItemTitle' => 'Artikel löschen',
    'deleteItemText' => 'Sind Sie sicher, dass Sie diesen Artikel löschen möchten? Diese Aktion kann nicht rückgängig gemacht werden.',
    'deleteItemConfirm' => 'Löschen',
    'deleteItemCancel' => 'Abbrechen',
    'selectOption' => 'Option auswählen',

    // 10. VALIDATION
    'required_field' => 'Dieses Feld ist erforderlich.',
    'invalid_email' => 'Ungültige E-Mail.',
    'invalid_date' => 'Ungültiges Datum.',
    'select_option' => 'Option auswählen',

    // 11. BUTTONS AND CRUD
    'create' => 'Erstellen',
    'edit' => 'Bearbeiten',
    'update' => 'Aktualisieren',
    'activate' => 'Aktivieren',
    'deactivate' => 'Deaktivieren',
    'confirm' => 'Bestätigen',
    'cancel' => 'Abbrechen',
    'save' => 'Speichern',
    'save_changes' => 'Änderungen speichern',
    'are_you_sure' => 'Sind Sie sicher?',
    'optional' => 'Optional',
    'edit_profile' => 'Profil bearbeiten',
    'read_more' => 'Mehr lesen',
    'read_less' => 'Weniger lesen',
    'switch_view' => 'Ansicht wechseln',
    'close' => 'Schließen',

    // 12. FOOTER
    'contact_us' => 'Kontaktieren Sie uns',
    'location' => 'San José, Costa Rica',

    // 13. WHATSAPP
    'whatsapp_title' => 'Green Vacations CR',
    'whatsapp_subtitle' => 'Antwortet normalerweise sofort',
    'whatsapp_attention_schedule' => 'Montag bis Sonntag, von 7:30 bis 19:30 Uhr (GMT-6)',
    'whatsapp_attention_language' => 'Support nur auf Spanisch und Englisch',
    'whatsapp_greeting' => '👋 Hallo! Wie können wir Ihnen helfen, Ihr Abenteuer in Costa Rica zu planen?',
    'whatsapp_placeholder' => 'Hallo, ich interessiere mich für eine Ihrer Touren. Können Sie mir mehr Informationen geben?',
    'whatsapp_button' => 'Nachricht senden',
    'whatsapp_footer' => 'Verbunden mit WhatsApp Business',

    // 14. REVIEWS
    'what_customers_thinks_about' => 'Was unsere Kunden über',
    'loading_reviews' => 'Bewertungen werden geladen',
    'redirect_to_tour' => 'Zur Tour weiterleiten',
    'would_you_like_to_visit' => 'Möchten Sie besuchen ',
    'this_tour' => 'diese Tour',
    'no_reviews_found' => 'Keine Bewertungen für diese Tour gefunden.',
    'no_reviews_available' => 'Keine Bewertungen verfügbar.',
    'error_loading_reviews' => 'Fehler beim Laden der Bewertungen.',
    'anonymous_user' => 'Anonym',
    'see_more' => 'Mehr anzeigen',
    'see_less' => 'Weniger anzeigen',
    'powered_by_viator' => 'Bereitgestellt von Viator',
    'go_to_tour' => 'Möchten Sie zur Tour ":name" gehen?',
    'view_in_viator' => 'Zeige :name auf Viator',

    // 15. TRAVELERS
    'select_travelers' => 'Reisende auswählen',
    'max_travelers_info' => 'Sie können insgesamt bis zu 12 Personen auswählen.',
    'adult' => 'Erwachsener',
    'adults' => 'Erwachsene',
    'adults_quantity' => 'Anzahl der Erwachsenen',
    'kid' => 'Kind',
    'kids' => 'Kinder',
    'kids_quantity' => 'Anzahl der Kinder',
    'age_10_plus' => 'Alter 10+',
    'age_4_to_9' => 'Alter 4–9',
    'max_limits_info' => 'Max. 12 Reisende, max. 2 Kinder.',

    // 16. CONTACT
    'name' => 'Name',
    'subject' => 'Betreff',
    'message' => 'Nachricht',
    'send_message' => 'Nachricht senden',
    'message_sent' => 'Nachricht gesendet',
    'business_hours' => 'Geschäftszeiten',
    'business_schedule' => 'Montag bis Sonntag, von 7:30 bis 19:30 Uhr',

    // 17. ERRORS
    'access_denied' => 'Zugriff verweigert',

    // 18. CART LOGIN MODAL
    'login' => 'Anmelden',
    'view_cart' => 'Warenkorb anzeigen',
    'login_required_title' => 'Sie müssen sich anmelden',
    'login_required_text' => 'Um zum Warenkorb hinzuzufügen, müssen Sie sich anmelden.',
    'login_required_text_confirm' => 'Um zum Warenkorb hinzuzufügen, müssen Sie sich anmelden. Zum Login gehen?',
    'pax' => 'Pers.',
    'remove_item_title' => 'Aus dem Warenkorb entfernen',
    'remove_item_text' => 'Möchten Sie diese Tour aus dem Warenkorb entfernen?',
    'success' => 'Erfolg',
    'error' => 'Fehler',
    'validation_error' => 'Unvollständige Daten',
    'editItem'          => 'Artikel bearbeiten',
    'scheduleHelp'      => 'Wenn die Tour keinen Zeitplan benötigt, lassen Sie das Feld leer.',
    'customHotel'       => 'Eigenes Hotel…',
    'otherHotel'        => 'Eigenes Hotel verwenden',
    'customHotelName'   => 'Name des eigenen Hotels',
    'customHotelHelp'   => 'Wenn Sie ein eigenes Hotel eingeben, wird die Auswahl aus der Liste ignoriert.',
    'inactive'          => 'Inaktiv',
    'notSpecified'      => 'Nicht angegeben',
    'saving'            => 'Speichern…',

    // 19. SWEETALERTS (ACTIONS)
    'confirmReservationTitle' => 'Sind Sie sicher?',
    'confirmReservationText' => 'Ihre Buchung wird bestätigt',
    'confirmReservationConfirm' => 'Ja, bestätigen',
    'confirmReservationCancel' => 'Abbrechen',

    // 20. SUCCESSES (USED IN CONTROLLERS)
    'edit_profile_of' => 'Profil bearbeiten',
    'profile_information' => 'Profilinformationen',
    'new_password_optional' => 'Neues Passwort (optional)',
    'leave_blank_if_no_change' => 'Leer lassen, wenn Sie es nicht ändern möchten',
    'confirm_new_password_placeholder' => 'Neues Passwort bestätigen',

    'policies' => 'Richtlinien',
    'no_reservations_yet' => 'Sie haben noch keine Buchungen!',
    'no_reservations_message' => 'Sie haben noch keine Abenteuer bei uns gebucht. Warum entdecken Sie nicht unsere tollen Touren?',
    'view_available_tours' => 'Verfügbare Touren anzeigen',
    'pending_reservations' => 'Ausstehende Buchungen',
    'confirmed_reservations' => 'Bestätigte Buchungen',
    'cancelled_reservations' => 'Stornierte Buchungen',
    'reservations_generic' => 'Buchungen',
    'generic_tour' => 'Allgemeine Tour',
    'unknown_tour' => 'Unbekannte Tour',
    'tour_date' => 'Tourdatum',
    'participants' => 'Teilnehmer',
    'children' => 'Kinder',
    'not_specified' => 'Nicht angegeben',
    'status_pending' => 'Ausstehend',
    'status_confirmed' => 'Bestätigt',
    'status_cancelled' => 'Storniert',
    'status_unknown' => 'Unbekannt',

    'view_receipt' => 'Beleg anzeigen',

    'validation.unique' => 'Diese E-Mail wird bereits verwendet',

    'validation' => [
        'too_many_attempts' => 'Zu viele fehlgeschlagene Versuche. Versuchen Sie es in :seconds Sekunden erneut.',
    ],

    'open_tour'          => 'Zur Tour gehen?',
    'open_tour_text_pre' => 'Sie sind dabei, die Tour-Seite zu öffnen',
    'open_tour_confirm'  => 'Jetzt gehen',
    'open_tour_cancel'   => 'Abbrechen',

    // Successes (used in controllers)
    'show_password' => 'Passwort anzeigen',
    'user_registered_successfully'   => 'Benutzer erfolgreich registriert.',
    'user_updated_successfully'      => 'Benutzer erfolgreich aktualisiert.',
    'user_reactivated_successfully'  => 'Benutzer erfolgreich reaktiviert.',
    'user_deactivated_successfully'  => 'Benutzer erfolgreich deaktiviert.',
    'profile_updated_successfully'   => 'Profil erfolgreich aktualisiert.',
    'user_unlocked_successfully' => 'Ihr Konto wurde entsperrt. Sie können sich jetzt anmelden.',
    'user_locked_successfully' => 'Benutzer erfolgreich gesperrt.',
    'auth_required_title' => 'Sie müssen sich anmelden, um zu buchen',
    'auth_required_body'  => 'Melden Sie sich an oder registrieren Sie sich, um Ihren Einkauf zu starten. Felder sind gesperrt, bis Sie sich anmelden.',
    'login_now'           => 'Anmelden',
    'back_to_login'       => 'Zurück zum Login',

    // 21. MAIL
    'mail' => [
        'trouble_clicking' => 'Wenn Sie Probleme beim Klicken auf die Schaltfläche ":actionText" haben, kopieren Sie die untenstehende URL und fügen Sie sie in Ihren Webbrowser ein',
    ],

    // 22. DASHBOARD (Sección)
    'dashboard' => [
        'title'      => 'Dashboard',
        'greeting'   => 'Hallo :name! 👋',
        'welcome_to' => 'Willkommen im :app Admin-Dashboard.',
        'hint'       => 'Verwenden Sie das Seitenmenü, um mit der Verwaltung von Inhalten zu beginnen.',
    ],

    // 23. ENTITIES
    'entities' => [
        'users'        => 'Benutzer',
        'tours'        => 'Touren',
        'tour_types'   => 'Tourtypen',
        'languages'    => 'Sprachen',
        'schedules'    => 'Zeitpläne',
        'amenities'    => 'Ausstattungen',
        'bookings'     => 'Buchungen',
        'total_bookings'=> 'Gesamtbuchungen',
        'itineraries'  => 'Reisepläne',
        'items'        => 'Punkte',
    ],

    // 24. SECTIONS
    'sections' => [
        'available_itineraries' => 'Verfügbare Reisepläne',
        'upcoming_bookings'     => 'Bevorstehende Buchungen',
    ],

    // 25. EMPTY STATES
    'empty' => [
        'itinerary_items'   => 'Dieser Reiseplan hat noch keine Punkte.',
        'itineraries'       => 'Keine Reisepläne gefunden.',
        'upcoming_bookings' => 'Keine bevorstehenden Buchungen.',
    ],

    // 26. BUTTONS (GENERIC)
    'buttons' => [
        'view' => 'Anzeigen',
    ],

    // 27. LABELS
    'labels' => [
        'reference' => 'Referenz',
        'date'      => 'Datum',
    ],
        'pickup'      => 'Abholort',

];
