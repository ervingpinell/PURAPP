<?php

/**
 * Inhaltsverzeichnis
 *
 * 1. AUTHENTIFIZIERUNG UND REGISTRIERUNG ...... Zeile 37
 * 2. HOTELS ................................... Zeile 57
 * 3. ALLGEMEINE NAVIGATION .................... Zeile 67
 * 4. INHALT UND SEITEN ........................ Zeile 82
 * 5. TOUREN UND BEWERTUNGEN ................... Zeile 97
 * 6. ZEITPLÄNE ................................ Zeile 131
 * 7. REISEROUTEN .............................. Zeile 144
 * 8. HOTELS (DETAIL) .......................... Zeile 156
 * 9. WARENKORB UND BUCHUNGEN .................. Zeile 180
 * 10. VALIDIERUNG ............................. Zeile 219
 * 11. BUTTONS UND CRUD ........................ Zeile 225
 * 12. FUSSZEILE ................................Zeile 243
 * 13. WHATSAPP ................................ Zeile 247
 * 14. BEWERTUNGEN ............................. Zeile 257
 * 15. REISENDE ................................ Zeile 273
 * 16. KONTAKT ................................. Zeile 286
 * 17. FEHLER .................................. Zeile 295
 * 18. LOGIN-MODAL FÜR WARENKORB ............... Zeile 298
 * 19. SWEETALERTS (AKTIONEN) .................. Zeile 322
 * 20. ERFOLGE (IN CONTROLLERN) ................ Zeile 328
 * 21. E-MAIL .................................. Zeile 381
 * 22. DASHBOARD ............................... Zeile 386
 * 23. ENTITÄTEN ................................Zeile 394
 * 24. SEKTIONEN ................................Zeile 408
 * 25. LEERE ZUSTÄNDE .......................... Zeile 414
 * 26. SCHALTFLÄCHEN (GENERISCH) ............... Zeile 421
 * 27. LABELS .................................. Zeile 426
 */

return [
    'contact_throttled' => "Sie haben in kurzer Zeit zu viele Nachrichten gesendet. Bitte warten Sie einen Moment, bevor Sie es erneut versuchen.",

    'pending_email_title'  => 'Ausstehende E-Mail-Änderung',
    'pending_email_notice' => 'Du hast beantragt, deine E-Mail von <strong>:current</strong> auf <strong>:pending</strong> zu ändern. Wir haben dir einen Bestätigungslink an deine neue E-Mail gesendet. Bis du den Wechsel bestätigst, verwenden wir weiterhin deine aktuelle E-Mail.',
    'email_change_warning' => 'Wenn du deine E-Mail änderst, senden wir dir einen Bestätigungslink an die neue Adresse. Deine aktuelle E-Mail bleibt aktiv, bis du die Änderung bestätigst.',
    'profile_updated_email_change_pending' => 'Dein Profil wurde aktualisiert. Wir haben dir einen Link an deine neue E-Mail gesendet, um die Änderung zu bestätigen. Bis zur Bestätigung verwenden wir weiterhin deine aktuelle E-Mail.',
    'email_change_confirmed' => 'Deine E-Mail-Adresse wurde erfolgreich aktualisiert und verifiziert.',

    'no_slots_for_date' => 'Für dieses Datum sind keine Plätze verfügbar',
    // 1. AUTHENTIFIZIERUNG UND REGISTRIERUNG
    'hello' => 'Hallo',
    'full_name' => 'Vollständiger Name',
    'email' => 'E-Mail',
    'password' => 'Passwort',
    'phone' => 'Telefon',
    'address' => 'Adresse',
    'city' => 'Stadt',
    'state' => 'Bundesland/Provinz',
    'zip' => 'Postleitzahl',
    'retype_password' => 'Passwort wiederholen',
    'remember_me' => 'Angemeldet bleiben',
    'remember_me_hint' => 'Sitzung unbegrenzt geöffnet lassen oder bis zur manuellen Abmeldung',
    'register' => 'Registrieren',
    'i_already_have_a_membership' => 'Ich habe bereits ein Konto',
    'promo_invalid' => 'Ungültiger Promo-Code.',
    'promo_already_used' => 'Dieser Promo-Code wurde bereits für eine andere Buchung verwendet.',
    'no_past_dates' => 'Buchungen für vergangene Daten sind nicht möglich.',
    'dupe_submit_cart' => 'Eine ähnliche Buchung wird bereits verarbeitet. Bitte versuche es in wenigen Sekunden erneut.',
    'schedule_not_available' => 'Dieser Zeitplan ist für diese Tour nicht verfügbar (inaktiv oder nicht zugewiesen).',
    'date_blocked' => 'Das gewählte Datum ist für diese Tour gesperrt.',
    'capacity_left' => 'Nur noch :available Plätze für diesen Zeitpunkt.',
    'booking_created_success' => 'Buchung erfolgreich erstellt.',
    'booking_updated_success' => 'Buchung erfolgreich aktualisiert.',
    'two_factor_authentication' => 'Zwei-Faktor-Authentifizierung (2FA)',
    'enable_2fa_to_continue' => 'Sie müssen die Zwei-Faktor-Authentifizierung (2FA) aktivieren, um auf das Administrationspanel zugreifen zu können.',

    // 2. HOTELS
    'hotel_name_required' => 'Hotelname ist erforderlich.',
    'hotel_name_unique'   => 'Ein Hotel mit diesem Namen existiert bereits.',
    'hotel_name_max'      => 'Der Hotelname darf :max Zeichen nicht überschreiten.',
    'hotel_created_success' => 'Hotel erfolgreich erstellt.',
    'hotel_updated_success' => 'Hotel erfolgreich aktualisiert.',
    'is_active_required'  => 'Status ist erforderlich.',
    'is_active_boolean'   => 'Status muss wahr oder falsch sein.',
    'outside_list' => 'Dieses Hotel befindet sich nicht auf unserer Liste. Bitte kontaktiere uns, um zu prüfen, ob wir einen Transfer anbieten können.',

    // 3. ALLGEMEINE NAVIGATION
    'back' => 'Zurück',
    'home' => 'Startseite',
    'dashboard_menu' => 'Dashboard',
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

    // 4. INHALT UND SEITEN
    'faq' => 'Häufig gestellte Fragen',
    'faqpage' => 'Häufig gestellte Fragen',
    'no_faqs_available' => 'Keine FAQs verfügbar.',
    'contact' => 'Kontakt',
    'about' => 'Über uns',
    'privacy_policy' => 'Datenschutzerklärung',
    'terms_and_conditions' => 'Allgemeine Geschäftsbedingungen',
    'all_policies' => 'Alle unsere Richtlinien',
    'cancellation_and_refunds_policies' => 'Stornierungs- und Erstattungsrichtlinien',
    'reports' => 'Berichte',
    'footer_text' => config('app.name', 'Green Vacations CR'),
    'quick_links' => 'Schnellzugriffe',
    'rights_reserved' => 'Alle Rechte vorbehalten',

    // 5. TOUREN UND BEWERTUNGEN
    'tours' => 'Touren',
    'tour' => 'Tour',
    'tour_name' => 'Tourname',
    'overview' => 'Überblick',
    'duration' => 'Dauer',
    'price' => 'Preis',
    'type' => 'Tourtyp',
    'languages_available' => 'Verfügbare Sprachen',
    'amenities_included' => 'Inklusive Leistungen',
    'excluded_amenities' => 'Nicht enthaltene Leistungen',
    'tour_details' => 'Tourdetails',
    'select_tour' => 'Tour auswählen',
    'reviews' => 'Bewertungen',
    'hero_title' => 'Entdecke die Magie Costa Ricas',
    'hero_subtext' => 'Erkunde unsere einzigartigen Touren und erlebe das Abenteuer.',
    'book_now' => 'Jetzt buchen',
    'our_tours' => 'Unsere Touren',
    'half_day' => 'Halbtägig',
    'full_day' => 'Ganztägig',
    'full_day_description' => 'Perfekt für alle, die an einem Tag ein komplettes Erlebnis möchten',
    'half_day_description' => 'Ideal für ein kurzes Abenteuer bei wenig Zeit.',
    'full_day_tours' => 'Ganztagestouren',
    'half_day_tours' => 'Halbtagestouren',
    'see_tour' => 'Tour ansehen',
    'see_tours' => 'Touren ansehen',
    'see_tour_details' => 'Tourdetails ansehen',
    'what_visitors_say' => 'Das sagen unsere Besucher',
    'quote_1' => 'Ein unvergessliches Erlebnis!',
    'guest_1' => 'Carlos M.',
    'quote_2' => 'Ich komme auf jeden Fall zurück.',
    'guest_2' => 'Ana G.',
    'tour_information' => 'Tour-Informationen',
    'group_size' => 'Gruppengröße',
    'no_prices_available' => 'Keine Preise verfügbar',
    'no_prices_configured' => 'Für diese Tour sind keine Preise konfiguriert',
    'total_persons' => 'Gesamtpersonen',
    'quantity' => 'Menge',
    'decrease' => 'Verringern',
    'increase' => 'Erhöhen',
    'max_persons_reached' => 'Maximal :max Personen pro Buchung',
    'min_category_required' => 'Mindestens :min in :category erforderlich',
    'max_category_exceeded' => 'Maximal :max in :category erlaubt',
    'max_persons_exceeded' => 'Maximal :max Personen insgesamt',
    'min_one_person' => 'Mindestens eine Person erforderlich',
    'persons_max' => 'Personen max.',
    'or' => 'Oder',
    'open_map' => 'Standort anzeigen',

    // 6. ZEITPLÄNE
    'schedule' => 'Zeitplan',
    'schedule_am' => 'Vormittagszeit',
    'schedule_pm' => 'Nachmittagszeit',
    'start_time' => 'Startzeit',
    'end_time' => 'Endzeit',
    'select_date' => 'Datum auswählen',
    'select_time' => 'Uhrzeit auswählen',
    'select_language' => 'Sprache auswählen',
    'schedules' => 'Zeitpläne',
    'horas' => 'Stunden',
    'hours' => 'Stunden',

    // 7. REISEROUTEN
    'itinerary' => 'Reiseroute',
    'itineraries' => 'Reiserouten',
    'new_itinerary' => 'Neue Reiseroute',
    'itinerary_items' => 'Positionen der Reiseroute',
    'item_title' => 'Titel',
    'item_description' => 'Beschreibung',
    'add_item' => 'Position hinzufügen',
    'edit_itinerary' => 'Reiseroute bearbeiten',
    'no_itinerary_info' => 'Keine Reiserouteninformationen.',
    'whats_included' => 'Enthalten',

    // 8. HOTELS (DETAIL)
    'hotels' => 'Hotels',
    'hotel' => 'Hotel',
    'select_hotel' => 'Hotel',
    'hotel_other' => 'Andere (manuell angeben)',
    'hotel_name' => 'Hotelname',
    'other_hotel' => 'Anderes Hotel (angeben)',
    'hotel_pickup' => 'Abholung im Hotel',
    'outside_area' => 'Dieses Hotel liegt außerhalb des Abdeckungsbereichs. Bitte kontaktiere uns für Optionen.',
    'pickup_valid' => 'Das ausgewählte Hotel ist gültig! Nach Bestätigung der Buchung kontaktieren wir dich zur Terminabstimmung.',
    'pickup_details' => 'Abholdetails',
    'pickup_note' => 'Kostenlose Abholung gilt nur für Hotels im Bereich La Fortuna...',
    'pickup_points' => 'Abholpunkte',
    'select_pickup' => 'Abholpunkt wählen',
    'type_to_search' => 'Zum Suchen tippen…',
    'no_pickup_available' => 'Keine Abholpunkte verfügbar.',
    'pickup_not_found' => 'Hotel nicht gefunden.',
    'meeting_points' => 'Treffpunkte',
    'select_meeting' => 'Treffpunkt wählen',
    'meeting_point_details' => 'Details zum Treffpunkt',
    'meeting_not_found' => 'Treffpunkt nicht gefunden.',
    'main_street_entrance' => 'Eingang Hauptstraße',
    'example_address' => 'Beispielstraße 123',
    'hotels_meeting_points' => 'Hotels und Treffpunkte',
    'meeting_valid' => 'Der gewählte Treffpunkt ist gültig! Nach Bestätigung senden wir Anweisungen und die genaue Uhrzeit.',
    'meeting_point' => 'Treffpunkt',
    'meetingPoint'  => 'Treffpunkt',
    'selectHotelHelp' => 'Wähle dein Hotel aus der Liste.',
    'selectFromList'      => 'Wähle ein Element aus der Liste',
    'fillThisField'       => 'Fülle dieses Feld aus',
    'pickupRequiredTitle' => 'Abholung erforderlich',
    'pickupRequiredBody'  => 'Bitte wähle ein Hotel oder einen Treffpunkt, um fortzufahren.',
    'ok'                  => 'OK',
    'pickup_time' => 'Abholzeit',
    'pickupTime'  => 'Abholzeit',
    'meeting_time' => 'Treffzeit',
    'open_map' => 'Karte öffnen',
    'openMap'  => 'Karte öffnen',

    // 9. WARENKORB UND BUCHUNGEN
    'cart' => 'Warenkorb',
    'myCart' => 'Mein Warenkorb',
    'my_reservations' => 'Meine Buchungen',
    'your_cart' => 'Dein Warenkorb',
    'add_to_cart' => 'In den Warenkorb',
    'remove_from_cart' => 'Aus dem Warenkorb entfernen',
    'confirm_reservation' => 'Buchung bestätigen',
    'confirmBooking' => 'Buchung bestätigen',
    'cart_updated' => 'Warenkorb erfolgreich aktualisiert.',
    'itemUpdated' => 'Warenkorbartikel erfolgreich aktualisiert.',
    'cartItemAdded' => 'Tour erfolgreich zum Warenkorb hinzugefügt.',
    'cartItemDeleted' => 'Tour erfolgreich aus dem Warenkorb entfernt.',
    'emptyCart' => 'Dein Warenkorb ist leer.',
    'no_items_in_cart' => 'Dein Warenkorb ist leer.',
    'reservation_success' => 'Buchung erfolgreich abgeschlossen!',
    'reservation_failed' => 'Beim Buchen ist ein Fehler aufgetreten.',
    'booking_reference' => 'Buchungsreferenz',
    'booking_date' => 'Buchungsdatum',
    'reservation_status' => 'Buchungsstatus',
    'blocked_date_for_tour' => 'Das Datum :date ist für „:tour“ gesperrt.',
    'tourCapacityFull' => 'Die maximale Kapazität für diese Tour ist bereits erreicht.',
    'totalEstimated' => 'Geschätzter Gesamtbetrag',
    'total_price' => 'Gesamtpreis',
    'total' => 'Gesamt',
    'date' => 'Datum',
    'status' => 'Status',
    'actions' => 'Aktionen',
    'active' => 'Aktiv',
    'delete' => 'Löschen',
    'promoCode' => 'Hast du einen Promo-Code?',
    'promoCodePlaceholder' => 'Promo-Code eingeben',
    'apply' => 'Anwenden',
    'remove' => 'Entfernen',
    'deleteItemTitle' => 'Element löschen',
    'deleteItemText' => 'Möchtest du dieses Element wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.',
    'deleteItemConfirm' => 'Löschen',
    'deleteItemCancel' => 'Abbrechen',
    'selectOption' => 'Option auswählen',
    'breakdown' => 'Aufschlüsselung',
    'subtotal'  => 'Zwischensumme',
    'senior'    => 'Senior',
    'student'   => 'Student',
    'custom' => 'Benutzerdefiniert',
    'notes'             => 'Hinweise',
    'notes_placeholder' => 'Gibt es etwas, das wir wissen sollten? (Allergien, Mobilität, Feiern usw.)',
    'notes_help'        => 'Diese Hinweise werden zusammen mit Ihrer Buchung an unser Team gesendet.',


    // 10. VALIDIERUNG
    'required_field' => 'Dieses Feld ist erforderlich.',
    'invalid_email' => 'Ungültige E-Mail.',
    'invalid_date' => 'Ungültiges Datum.',
    'select_option' => 'Option auswählen',

    // 11. BUTTONS UND CRUD
    'create' => 'Erstellen',
    'edit' => 'Bearbeiten',
    'update' => 'Aktualisieren',
    'activate' => 'Aktivieren',
    'deactivate' => 'Deaktivieren',
    'confirm' => 'Bestätigen',
    'cancel' => 'Abbrechen',
    'save' => 'Speichern',
    'save_changes' => 'Änderungen speichern',
    'are_you_sure' => 'Bist du sicher?',
    'optional' => 'Optional',
    'edit_profile' => 'Profil bearbeiten',
    'read_more' => 'Mehr lesen',
    'read_less' => 'Weniger lesen',
    'switch_view' => 'Ansicht wechseln',
    'close' => 'Schließen',

    // 12. FUSSZEILE
    'contact_us' => 'Kontaktiere uns',
    'location' => 'San José, Costa Rica',

    // 13. WHATSAPP
    'whatsapp_title' => config('app.name', 'Green Vacations CR'),
    'whatsapp_subtitle' => 'Antwortet in der Regel sofort',
    'whatsapp_attention_schedule' => 'Montag bis Sonntag, 07:30–19:30 Uhr (GMT-6)',
    'whatsapp_attention_language' => 'Support nur auf Spanisch und Englisch',
    'whatsapp_greeting' => '👋 Hallo! Wie können wir dir bei deiner Costa-Rica-Reise helfen?',
    'whatsapp_placeholder' => 'Hallo, ich interessiere mich für eine eurer Touren. Könnt ihr mir mehr Infos geben?',
    'whatsapp_button' => 'Nachricht senden',
    'whatsapp_footer' => 'Verbunden über WhatsApp Business',

    // 14. BEWERTUNGEN
    'what_customers_thinks_about' => 'Was unsere Kund:innen über',
    'loading_reviews' => 'Bewertungen werden geladen',
    'redirect_to_tour' => 'Zur Tour weiterleiten',
    'would_you_like_to_visit' => 'Möchtest du besuchen: ',
    'this_tour' => 'diese Tour',
    'no_reviews_found' => 'Keine Bewertungen für diese Tour gefunden.',
    'no_reviews_available' => 'Keine Bewertungen verfügbar.',
    'error_loading_reviews' => 'Fehler beim Laden der Bewertungen.',
    'anonymous_user' => 'Anonym',
    'see_more' => 'Mehr ansehen',
    'see_less' => 'Weniger anzeigen',
    'powered_by_viator' => 'Bereitgestellt von Viator',
    'go_to_tour' => 'Möchtest du zur Tour „:name“ gehen?',
    'view_in_viator' => ':name auf Viator ansehen',

    // 15. REISENDE
    'select_travelers' => 'Reisende auswählen',
    'max_travelers_info' => 'Du kannst insgesamt bis zu 12 Personen auswählen.',
    'adult' => 'Erwachsener',
    'adults' => 'Erwachsene',
    'adults_quantity' => 'Anzahl der Erwachsenen',
    'kid' => 'Kind',
    'kids' => 'Kinder',
    'kids_quantity' => 'Anzahl der Kinder',
    'age_10_plus' => 'Alter 10+',
    'age_4_to_9' => 'Alter 4–9',
    'max_limits_info' => 'Max. 12 Reisende, max. 2 Kinder.',
    'total_persons' => 'Gesamtpersonen',
    'or' => 'oder',
    'min' => 'Min',

    // 16. KONTAKT
    'name' => 'Name',
    'subject' => 'Betreff',
    'message' => 'Nachricht',
    'send_message' => 'Nachricht senden',
    'message_sent' => 'Nachricht gesendet',
    'business_hours' => 'Geschäftszeiten',
    'business_schedule' => 'Montag bis Sonntag, 07:30–19:30 Uhr',
    'field_required'              => 'Dieses Feld ist erforderlich.',
    'email_invalid'               => 'Bitte gib eine gültige E-Mail-Adresse ein.',

    // Placeholders
    'contact_name_placeholder'    => 'Ihr vollständiger Name',
    'contact_email_placeholder'   => 'ihremail@beispiel.de',
    'contact_subject_placeholder' => 'Wie können wir Ihnen helfen?',
    'contact_message_placeholder' => 'Erzählen Sie uns, wie wir Ihnen helfen können …',
    'contact_spam_success' => 'Ihre Nachricht wurde gesendet.',
    'contact_success'      => 'Ihre Nachricht wurde erfolgreich gesendet. Wir werden uns in Kürze bei Ihnen melden.',
    'contact_error'        => 'Beim Senden Ihrer Nachricht ist ein Fehler aufgetreten. Bitte versuchen Sie es in ein paar Minuten erneut.',

    // SweetAlert
    'message_sent'                => 'Nachricht gesendet',
    'validation_error'            => 'Bitte überprüfen Sie die markierten Felder.',
    'swal_ok'                     => 'OK',

    // 17. FEHLER
    'access_denied' => 'Zugriff verweigert',
    'need_language' => 'Bitte wählen Sie eine Sprache aus.',
    'need_pickup'   => 'Bitte wählen Sie ein Hotel oder einen Treffpunkt aus.',
    'need_schedule_title' => 'Uhrzeit erforderlich',
    'need_schedule'       => 'Bitte wählen Sie eine Uhrzeit aus.',
    'need_language_title' => 'Sprache erforderlich',
    'need_pickup_title'   => 'Abholort erforderlich',
    'no_slots_title'      => 'Keine verfügbaren Uhrzeiten',
    'no_slots'            => 'Für das ausgewählte Datum sind keine Uhrzeiten verfügbar. Bitte wählen Sie ein anderes Datum.',

    // 18. LOGIN-MODAL FÜR WARENKORB
    'login' => 'Anmelden',
    'view_cart' => 'Warenkorb anzeigen',
    'login_required_title' => 'Anmeldung erforderlich',
    'login_required_text' => 'Zum Hinzufügen zum Warenkorb musst du dich anmelden.',
    'login_required_text_confirm' => 'Zum Hinzufügen zum Warenkorb musst du dich anmelden. Jetzt anmelden?',
    'pax' => 'Pax',
    'remove_item_title' => 'Aus dem Warenkorb entfernen',
    'remove_item_text' => 'Möchtest du diese Tour aus dem Warenkorb entfernen?',
    'success' => 'Erfolg',
    'error' => 'Fehler',
    'validation_error' => 'Unvollständige Daten',
    'editItem' => 'Element bearbeiten',
    'scheduleHelp' => 'Wenn die Tour keinen Zeitplan erfordert, lasse dieses Feld leer.',
    'customHotel' => 'Benutzerdefiniertes Hotel…',
    'otherHotel' => 'Benutzerdefiniertes Hotel verwenden',
    'customHotelName' => 'Name des benutzerdefinierten Hotels',
    'customHotelHelp' => 'Wenn du ein benutzerdefiniertes Hotel angibst, wird die Listenwahl ignoriert.',
    'inactive' => 'Inaktiv',
    'notSpecified' => 'Nicht angegeben',
    'saving' => 'Speichern…',

    // 19. SWEETALERTS (AKTIONEN)
    'confirmReservationTitle' => 'Bist du sicher?',
    'confirmReservationText' => 'Deine Buchung wird bestätigt',
    'confirmReservationConfirm' => 'Ja, bestätigen',
    'confirmReservationCancel' => 'Abbrechen',

    // 20. ERFOLGE (IN CONTROLLERN)
    'edit_profile_of' => 'Profil bearbeiten',
    'profile_information' => 'Profilinformationen',
    'new_password_optional' => 'Neues Passwort (optional)',
    'leave_blank_if_no_change' => 'Leer lassen, wenn keine Änderung gewünscht',
    'confirm_new_password_placeholder' => 'Neues Passwort bestätigen',

    'policies' => 'Richtlinien',
    'no_reservations_yet' => 'Du hast noch keine Buchungen!',
    'no_reservations_message' => 'Es sieht so aus, als hättest du noch keine Abenteuer bei uns gebucht. Entdecke unsere tollen Touren!',
    'view_available_tours' => 'Verfügbare Touren ansehen',
    'pending_reservations' => 'Ausstehende Buchungen',
    'confirmed_reservations' => 'Bestätigte Buchungen',
    'cancelled_reservations' => 'Stornierte Buchungen',
    'reservations_generic' => 'Buchungen',
    'generic_tour' => 'Allgemeine Tour',
    'unknown_tour' => 'Unbekannte Tour',
    'tour_date' => 'Tour-Datum',
    'participants' => 'Teilnehmende',
    'children' => 'Kinder',
    'not_specified' => 'Nicht angegeben',
    'status_pending' => 'Ausstehend',
    'status_confirmed' => 'Bestätigt',
    'status_cancelled' => 'Storniert',
    'status_unknown' => 'Unbekannt',

    'view_receipt' => 'Beleg anzeigen',

    'validation.unique' => 'Diese E-Mail wird bereits verwendet',

    'validation' => [
        'too_many_attempts' => 'Zu viele fehlgeschlagene Versuche. Versuche es in :seconds Sekunden erneut.',
    ],

    'open_tour'          => 'Zur Tour gehen?',
    'open_tour_text_pre' => 'Du bist dabei, die Tourseite zu öffnen',
    'open_tour_confirm'  => 'Jetzt gehen',
    'open_tour_cancel'   => 'Abbrechen',

    // Weitere Erfolgsnachrichten (in Controllern)
    'show_password' => 'Passwort anzeigen',
    'user_registered_successfully'   => 'Benutzer erfolgreich registriert.',
    'user_updated_successfully'      => 'Benutzer erfolgreich aktualisiert.',
    'user_reactivated_successfully'  => 'Benutzer erfolgreich reaktiviert.',
    'user_deactivated_successfully'  => 'Benutzer erfolgreich deaktiviert.',
    'profile_updated_successfully'   => 'Profil erfolgreich aktualisiert.',
    'user_unlocked_successfully' => 'Dein Konto wurde entsperrt. Du kannst dich jetzt anmelden.',
    'user_locked_successfully' => 'Benutzer erfolgreich gesperrt.',
    'auth_required_title' => 'Zum Buchen musst du dich anmelden',
    'auth_required_body'  => 'Melde dich an oder registriere dich, um mit dem Kauf zu beginnen. Felder sind gesperrt, bis du angemeldet bist.',
    'login_now'           => 'Anmelden',
    'back_to_login'       => 'Zur Anmeldung zurück',

    // 21. E-MAIL
    'mail' => [
        'trouble_clicking' => 'Wenn du Probleme hast, auf die Schaltfläche „:actionText“ zu klicken, kopiere die untenstehende URL und füge sie in deinen Webbrowser ein',
    ],

    // 22. DASHBOARD
    'dashboard' => [
        'title'      => 'Dashboard',
        'greeting'   => 'Hallo :name! 👋',
        'welcome_to' => 'Willkommen im Administrations-Dashboard von :app.',
        'hint'       => 'Nutze das Seitenmenü, um mit der Verwaltung zu beginnen.',
    ],

    // 23. ENTITÄTEN
    'entities' => [
        'users'        => 'Benutzer',
        'tours'        => 'Touren',
        'tour_types'   => 'Tourtypen',
        'languages'    => 'Sprachen',
        'schedules'    => 'Zeitpläne',
        'amenities'    => 'Ausstattung',
        'bookings'     => 'Buchungen',
        'total_bookings' => 'Gesamtbuchungen',
        'itineraries'  => 'Reiserouten',
        'items'        => 'Elemente',
    ],

    // 24. SEKTIONEN
    'sections' => [
        'available_tours' => 'Touren verfügbar',
        'upcoming_bookings'     => 'Bevorstehende Buchungen',
    ],

    // 25. LEERE ZUSTÄNDE
    'empty' => [
        'itinerary_items'   => 'Diese Reiseroute hat noch keine Elemente.',
        'itineraries'       => 'Keine Reiserouten gefunden.',
        'upcoming_bookings' => 'Keine bevorstehenden Buchungen.',
    ],

    // 26. SCHALTFLÄCHEN (GENERISCH)
    'buttons' => [
        'view' => 'Ansehen',
    ],

    'persons' => [
        'count' => '{0} 0 Personen|{1} 1 Person|[2,*] :count Personen',
        'title'            => 'Personen',
        'pax'              => 'PAX',
        'adults'           => 'Erwachsene',
        'kids'             => 'Kinder',
        'seniors'          => 'Senioren',
        'infants'          => 'Kleinkinder',
        'students'         => 'Studierende',
        'guides'           => 'Reiseleiter',
        'drivers'          => 'Fahrer',
        'free'             => 'Kostenlos',
        'other'            => 'Andere',
        'category'         => 'Kategorie',
        'categories'       => 'Kategorien',
        'quantity'         => 'Menge',
        'min'              => 'Min.',
        'max'              => 'Max.',
        'per_person'       => 'pro Person',
        'price'            => 'Preis',
        'subtotal'         => 'Zwischensumme',
        'total'            => 'Gesamt',
        'add_category'     => 'Kategorie hinzufügen',
        'remove_category'  => 'Entfernen',
        'select_category'  => 'Kategorie auswählen',
        'required'         => 'Erforderlich',
        'optional'         => 'Optional',
        'min_required'     => 'Mindestens erforderlich: :min',
        'max_allowed'      => 'Maximal erlaubt: :max',
        'invalid_quantity' => 'Ungültige Menge für „:category“.',
    ],

    // 27. LABELS
    'labels' => [
        'reference' => 'Referenz',
        'date'      => 'Datum',
    ],

    'pickup'      => 'Abholort',
    'filters_title'            => 'Ergebnisse filtern',
    'filters_subtitle'         => 'Kombiniere Textsuche und Kategorien, um die passende Tour zu finden.',
    'search_tours_placeholder' => 'Nach Name oder Beschreibung suchen…',

    'all_categories'           => 'Alle Kategorien',
    'category_label'           => 'Kategorie',

    'filters_active'           => 'Aktive Filter',
    'clear_filters'            => 'Filter löschen',
    'clear_short'              => 'Löschen',
    'all_tours_title'       => 'Alle Touren',
    'all_tours_subtitle'    => 'Entdecke all unsere verfügbaren Erlebnisse und finde dein nächstes Abenteuer.',
    'filters_btn' => 'Filtern',
    'more_categories' => 'Weitere Kategorien',
    'tours_index_title'     => 'Touren',
    'tours_index_subtitle'  => 'Entdecke unsere verfügbaren Erlebnisse und Aktivitäten.',

    'tours_count' => '1 Tour verfügbar|:count Touren verfügbar',

    // Quantities
    'quantities' => 'Mengen',
    'quantitiesHelp' => 'Passen Sie die Mengen nach Bedarf an. Sie können nicht verwendete Kategorien auf 0 belassen.',
    'no_tours_found' => 'Keine Touren gefunden.',

    // COOKIES (Cookie Consent)
    'cookies' => [
        'banner_aria' => 'Cookie-Hinweis',
        'title' => 'Wir verwenden Cookies',
        'message' => 'Diese Website verwendet Cookies, um Ihre Erfahrung zu verbessern. Sie können alle akzeptieren, nicht wesentliche ablehnen oder Ihre Einstellungen anpassen.',
        'accept_all' => 'Alle akzeptieren',
        'reject' => 'Ablehnen',
        'customize' => 'Anpassen',
        'customize_title' => 'Cookies anpassen',
        'save_preferences' => 'Einstellungen speichern',
        'change_preferences' => 'Cookie-Einstellungen',
        'close' => 'Schließen',
        'always_active' => 'Immer aktiv',
        'learn_more' => 'Mehr über Cookies erfahren',

        'essential' => 'Wesentliche Cookies',
        'essential_desc' => 'Notwendig für die Grundfunktionen der Website (Login, Warenkorb, Sicherheit)',

        'functional' => 'Funktionale Cookies',
        'functional_desc' => 'Speichern Ihre Einstellungen wie Sprache, Währung oder Theme',

        'analytics' => 'Analytische Cookies',
        'analytics_desc' => 'Helfen uns zu verstehen, wie Sie die Website nutzen, um sie zu verbessern (Google Analytics)',

        'marketing' => 'Marketing-Cookies',
        'marketing_desc' => 'Ermöglichen es uns, Ihnen relevante Anzeigen zu zeigen und Kampagnen zu messen (Facebook Pixel)',
    ],

    'meta' => [
        'home_title' => 'Green Vacations Costa Rica | Touren und Abenteuer in La Fortuna',
        'home_description' => 'Entdecken Sie die besten Touren in La Fortuna und am Vulkan Arenal. Nachhaltige Abenteuer, Wanderungen und mehr mit Green Vacations Costa Rica.',
        'tours_title' => 'Touren und Aktivitäten in La Fortuna | Green Vacations',
        'tours_description' => 'Entdecken Sie unsere Auswahl an Touren in La Fortuna. Von Vulkanwanderungen bis zu Wasseraktivitäten. Buchen Sie Ihr Abenteuer noch heute!',
        'contact_title' => 'Kontaktieren Sie uns | Green Vacations Costa Rica',
        'contact_description' => 'Haben Sie Fragen? Kontaktieren Sie uns, um Ihre Reise nach Costa Rica zu planen. Wir sind hier, um Ihnen bei der Buchung von Touren und Transporten zu helfen.',
        'faq_description' => 'Finden Sie Antworten auf häufig gestellte Fragen zu unseren Touren in La Fortuna, Buchungsprozess, Stornierungen und mehr. Planen Sie Ihr Abenteuer in Costa Rica ganz einfach.',
    ],
];
