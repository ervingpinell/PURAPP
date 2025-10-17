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
    'hello' => 'Bonjour',
    'full_name' => 'Nom complet',
    'email' => 'E-mail',
    'password' => 'Mot de passe',
    'phone' => 'Téléphone',
    'retype_password' => 'Retaper le mot de passe',
    'remember_me' => 'Se souvenir de moi',
    'remember_me_hint' => 'Garder la session ouverte indéfiniment ou jusqu\'à fermeture manuelle',
    'register' => 'S\'inscrire',
    'promo_invalid' => 'Code promotionnel invalide.',
    'promo_already_used' => 'Ce code promotionnel a déjà été utilisé dans une autre réservation.',
    'no_past_dates' => 'Vous ne pouvez pas réserver pour des dates antérieures à aujourd\'hui.',
    'dupe_submit_cart' => 'Une réservation similaire est déjà en cours de traitement. Veuillez réessayer dans quelques secondes.',
    'schedule_not_available' => 'L\'horaire n\'est pas disponible pour cette visite (inactif ou non assigné).',
    'date_blocked' => 'La date sélectionnée est bloquée pour cette visite.',
    'capacity_left' => 'Il ne reste que :available places pour cet horaire.',
    'booking_created_success' => 'Réservation créée avec succès.',
    'booking_updated_success' => 'Réservation mise à jour avec succès.',

    // 2. HOTELS
    'hotel_name_required' => 'Le nom de l\'hôtel est requis.',
    'hotel_name_unique'   => 'Un hôtel avec ce nom existe déjà.',
    'hotel_name_max'      => 'Le nom de l\'hôtel ne peut pas dépasser :max caractères.',
    'hotel_created_success' => 'Hôtel créé avec succès.',
    'hotel_updated_success' => 'Hôtel mis à jour avec succès.',
    'is_active_required'  => 'Le statut est requis.',
    'is_active_boolean'   => 'Le statut doit être vrai ou faux.',
    'outside_list' => 'Cet hôtel est en dehors de notre liste. Veuillez nous contacter pour vérifier si nous pouvons vous offrir un transport.',

    // 3. GENERAL NAVIGATION
    'back' => 'Retour',
    'home' => 'Accueil',
    'dashboard' => 'Tableau de bord',
    'profile' => 'Profil',
    'settings' => 'Paramètres',
    'users' => 'Utilisateurs',
    'roles' => 'Rôles',
    'notifications' => 'Notifications',
    'messages' => 'Messages',
    'help' => 'Aide',
    'language' => 'Langue',
    'support' => 'Support',
    'admin_panel' => 'Panneau d\'administration',

    // 4. CONTENT AND PAGES
    'faq' => 'Foire aux questions',
    'faqpage' => 'Foire aux questions',
    'no_faqs_available' => 'Aucune FAQ disponible.',
    'contact' => 'Contact',
    'about' => 'À propos de nous',
    'privacy_policy' => 'Politique de confidentialité',
    'terms_and_conditions' => 'Termes et conditions',
    'all_policies' => 'Toutes nos politiques',
    'cancellation_and_refunds_policies' => 'Politiques d\'annulation et de remboursement',
    'reports' => 'Rapports',
    'footer_text'=> 'Green Vacations CR',
    'quick_links'=> 'Liens rapides',
    'rights_reserved' => 'Tous droits réservés',

    // 5. TOURS AND REVIEWS
    'tours' => 'Excursions',
    'tour' => 'Excursion',
    'tour_name' => 'Nom de l\'excursion',
    'overview' => 'Aperçu',
    'duration' => 'Durée',
    'price' => 'Prix',
    'type' => 'Type d\'excursion',
    'languages_available' => 'Langues disponibles',
    'amenities_included' => 'Commodités incluses',
    'excluded_amenities' => 'Commodités exclues',
    'tour_details' => 'Détails de l\'excursion',
    'select_tour' => 'Sélectionner une excursion',
    'reviews' => 'Avis',
    'hero_title' => 'Découvrez la magie du Costa Rica',
    'hero_subtext' => 'Explorez nos excursions uniques et vivez l\'aventure.',
    'book_now' => 'Réserver maintenant',
    'our_tours' => 'Nos excursions',
    'half_day' => 'Demi-journée',
    'full_day' => 'Journée complète',
    'full_day_description' => 'Parfait pour ceux qui recherchent une expérience complète en une journée',
    'half_day_description' => 'Excursions idéales pour une aventure rapide pour ceux qui manquent de temps.',
    'full_day_tours' => 'Excursions journée complète',
    'half_day_tours' => 'Excursions demi-journée',
    'see_tour' => 'Voir l\'excursion',
    'see_tours' => 'Voir les excursions',
    'what_visitors_say' => 'Ce que disent nos visiteurs',
    'quote_1' => 'Une expérience inoubliable !',
    'guest_1' => 'Carlos M.',
    'quote_2' => 'Je reviendrai sans aucun doute.',
    'guest_2' => 'Ana G.',
    'tour_information'=> 'Informations sur l\'excursion',
    'group_size'=> 'Taille du groupe',

    // 6. SCHEDULES
    'schedule' => 'Horaire',
    'schedule_am' => 'Horaire du matin',
    'schedule_pm' => 'Horaire de l\'après-midi',
    'start_time' => 'Heure de début',
    'end_time' => 'Heure de fin',
    'select_date' => 'Sélectionner une date',
    'select_time' => 'Sélectionner une heure',
    'select_language' => 'Sélectionner une langue',
    'schedules' => 'Horaires',
    'horas' => 'heures',
    'hours' => 'heures',

    // 7. ITINERARIES
    'itinerary' => 'Itinéraire',
    'itineraries' => 'Itinéraires',
    'new_itinerary' => 'Nouvel itinéraire',
    'itinerary_items' => 'Éléments de l\'itinéraire',
    'item_title' => 'Titre de l\'élément',
    'item_description' => 'Description de l\'élément',
    'add_item' => 'Ajouter un élément',
    'edit_itinerary' => 'Modifier l\'itinéraire',
    'no_itinerary_info' => 'Aucune information sur l\'itinéraire.',
    'whats_included' => 'Ce qui est inclus',

    // 8. HOTELS (DETAIL)
    'hotels' => 'Hôtels',
    'hotel' => 'Hôtel',
    'select_hotel' => 'Hôtel ou point de prise en charge',
    'hotel_other' => 'Autre (spécifier manuellement)',
    'hotel_name' => 'Nom de l\'hôtel',
    'other_hotel' => 'Autre hôtel (spécifier)',
    'hotel_pickup' => 'Prise en charge à l\'hôtel',
    'outside_area' => 'Cet hôtel est en dehors de la zone de couverture. Veuillez nous contacter pour examiner vos options.',
    'pickup_valid' => 'L\'hôtel sélectionné est valide ! Une fois la réservation confirmée, nous vous contacterons pour coordonner l\'heure de prise en charge.',
    'pickup_details' => 'Détails de la prise en charge',
    'pickup_note' => 'Les prises en charge gratuites s\'appliquent uniquement aux hôtels de la région de La Fortuna...',
    'pickup_points' => 'Points de prise en charge',
    'select_pickup' => 'Sélectionner un point de prise en charge',
    'type_to_search' => 'Tapez pour rechercher...',
    'no_pickup_available' => 'Aucun point de prise en charge disponible.',
    'pickup_not_found' => 'Hôtel non trouvé.',
    'meeting_points' => 'Points de rencontre',
    'select_meeting' => 'Sélectionner un point de rencontre',
    'meeting_point_details' => 'Détails du point de rencontre',
    'meeting_not_found' => 'Point de rencontre non trouvé.',
    'main_street_entrance' => 'Entrée de la rue principale',
    'example_address' => 'Adresse exemple 123',
    'hotels_meeting_points' => 'Hôtels et points de rencontre',
    'meeting_valid' => 'Le point de rendez-vous sélectionné est valide ! Une fois votre réservation confirmée, nous vous enverrons les instructions et l’heure exacte du rendez-vous.',
    'meeting_point' => 'Point de rencontre',
    'meetingPoint'  => 'Point de rencontre',
    'selectHotelHelp' => "Sélectionnez votre hôtel dans la liste.",
    'selectFromList'      => 'Sélectionnez un élément de la liste',
    'fillThisField'       => 'Veuillez remplir ce champ',
    'pickupRequiredTitle' => 'Prise en charge requise',
    'pickupRequiredBody'  => 'Vous devez sélectionner un hôtel ou un point de rendez-vous pour continuer.',
    'ok'                  => 'OK',

    'pickup_time' => 'Heure de prise en charge',
    'pickupTime'  => 'Heure de prise en charge',

    'open_map' => 'Ouvrir la carte',
    'openMap'  => 'Ouvrir la carte',

    // 9. CART AND BOOKINGS
    'cart' => 'Panier',
    'myCart' => 'Mon panier',
    'my_reservations' => 'Mes réservations',
    'your_cart' => 'Votre panier',
    'add_to_cart' => 'Ajouter au panier',
    'remove_from_cart' => 'Retirer du panier',
    'confirm_reservation' => 'Confirmer la réservation',
    'confirmBooking' => 'Confirmer la réservation',
    'cart_updated' => 'Panier mis à jour avec succès.',
    'itemUpdated' => 'Article du panier mis à jour avec succès.',
    'cartItemAdded' => 'Excursion ajoutée au panier avec succès.',
    'cartItemDeleted' => 'Excursion retirée du panier avec succès.',
    'emptyCart' => 'Votre panier est vide.',
    'no_items_in_cart' => 'Votre panier est vide.',
    'reservation_success' => 'Réservation effectuée avec succès !',
    'reservation_failed' => 'Une erreur s\'est produite lors de la réservation.',
    'booking_reference' => 'Référence de réservation',
    'booking_date' => 'Date de réservation',
    'reservation_status' => 'Statut de la réservation',
    'blocked_date_for_tour' => 'La date :date est bloquée pour ":tour".',
    'tourCapacityFull' => 'La capacité maximale pour cette excursion est déjà atteinte.',
    'totalEstimated' => 'Total estimé',
    'total_price' => 'Prix total',
    'total' => 'Total',
    'date'=> 'Date',
    'status' => 'Statut',
    'actions' => 'Actions',
    'active'=> 'Actif',
    'delete'=> 'Supprimer',
    'promoCode' => 'Avez-vous un code promotionnel ?',
    'promoCodePlaceholder' => 'Entrez votre code promotionnel',
    'apply' => 'Appliquer',
    'deleteItemTitle' => 'Supprimer l\'article',
    'deleteItemText' => 'Êtes-vous sûr de vouloir supprimer cet article ? Cette action est irréversible.',
    'deleteItemConfirm' => 'Supprimer',
    'deleteItemCancel' => 'Annuler',
    'selectOption' => 'Sélectionner une option',

    // 10. VALIDATION
    'required_field' => 'Ce champ est requis.',
    'invalid_email' => 'E-mail invalide.',
    'invalid_date' => 'Date invalide.',
    'select_option' => 'Sélectionner une option',

    // 11. BUTTONS AND CRUD
    'create' => 'Créer',
    'edit' => 'Modifier',
    'update' => 'Mettre à jour',
    'activate' => 'Activer',
    'deactivate' => 'Désactiver',
    'confirm' => 'Confirmer',
    'cancel' => 'Annuler',
    'save' => 'Enregistrer',
    'save_changes' => 'Enregistrer les modifications',
    'are_you_sure' => 'Êtes-vous sûr ?',
    'optional' => 'Optionnel',
    'edit_profile' => 'Modifier le profil',
    'read_more' => 'Lire la suite',
    'read_less' => 'Lire moins',
    'switch_view' => 'Changer de vue',
    'close' => 'Fermer',

    // 12. FOOTER
    'contact_us' => 'Contactez-nous',
    'location' => 'San José, Costa Rica',

    // 13. WHATSAPP
    'whatsapp_title' => 'Green Vacations CR',
    'whatsapp_subtitle' => 'Répond généralement instantanément',
    'whatsapp_attention_schedule' => 'Lundi à dimanche, de 7h30 à 19h30 (GMT-6)',
    'whatsapp_attention_language' => 'Support uniquement en espagnol et en anglais',
    'whatsapp_greeting' => '👋 Bonjour ! Comment pouvons-nous vous aider à planifier votre aventure au Costa Rica ?',
    'whatsapp_placeholder' => 'Bonjour, je suis intéressé par l\'une de vos excursions. Pouvez-vous me donner plus d\'informations ?',
    'whatsapp_button' => 'Envoyer le message',
    'whatsapp_footer' => 'Connecté par WhatsApp Business',

    // 14. REVIEWS
    'what_customers_thinks_about' => 'Ce que nos clients pensent de',
    'loading_reviews' => 'Chargement des avis',
    'redirect_to_tour' => 'Rediriger vers l\'excursion',
    'would_you_like_to_visit' => 'Souhaitez-vous visiter ',
    'this_tour' => 'cette excursion',
    'no_reviews_found' => 'Aucun avis trouvé pour cette excursion.',
    'no_reviews_available' => 'Aucun avis disponible.',
    'error_loading_reviews' => 'Erreur lors du chargement des avis.',
    'anonymous_user' => 'Anonyme',
    'see_more' => 'Voir plus',
    'see_less' => 'Voir moins',
    'powered_by_viator' => 'Propulsé par Viator',
    'go_to_tour' => 'Voulez-vous aller à l\'excursion ":name" ?',
    'view_in_viator' => 'Voir :name sur Viator',

    // 15. TRAVELERS
    'select_travelers' => 'Sélectionner les voyageurs',
    'max_travelers_info' => 'Vous pouvez sélectionner jusqu\'à 12 personnes au total.',
    'adult' => 'Adulte',
    'adults' => 'Adultes',
    'adults_quantity' => 'Nombre d\'adultes',
    'kid' => 'Enfant',
    'kids' => 'Enfants',
    'kids_quantity' => 'Nombre d\'enfants',
    'age_10_plus' => 'Âge 10+',
    'age_4_to_9' => 'Âge 0-9',
    'max_limits_info' => 'Max. 12 voyageurs, max. 2 enfants.',

    // 16. CONTACT
    'name' => 'Nom',
    'subject' => 'Sujet',
    'message' => 'Message',
    'send_message' => 'Envoyer le message',
    'message_sent' => 'Message envoyé',
    'business_hours' => 'Heures d\'ouverture',
    'business_schedule' => 'Lundi à dimanche, de 7h30 à 19h30.',

    // 17. ERRORS
    'access_denied' => 'Accès refusé',

    // 18. CART LOGIN MODAL
    'login' => 'Connexion',
    'view_cart' => 'Voir le panier',
    'login_required_title' => 'Vous devez vous connecter',
    'login_required_text' => 'Pour ajouter au panier, vous devez vous connecter.',
    'login_required_text_confirm' => 'Pour ajouter au panier, vous devez vous connecter. Aller à la connexion ?',
    'pax' => 'pers.',
    'remove_item_title' => 'Retirer du panier',
    'remove_item_text' => 'Voulez-vous retirer cette excursion du panier ?',
    'success' => 'Succès',
    'error' => 'Erreur',
    'validation_error' => 'Données incomplètes',
    'editItem'          => 'Modifier l\'article',
    // Removed duplicate keys: date, schedule, language, adults, kids, hotel, status, active, cancel, update
    'scheduleHelp'      => 'Si l\'excursion ne nécessite pas d\'horaire, laissez vide.',
    'customHotel'       => 'Hôtel personnalisé…',
    'otherHotel'        => 'Utiliser un hôtel personnalisé',
    'customHotelName'   => 'Nom de l\'hôtel personnalisé',
    'customHotelHelp'   => 'Si vous saisissez un hôtel personnalisé, la sélection de la liste sera ignorée.',
    'inactive'          => 'Inactif',
    'close'             => 'Fermer',
    'notSpecified'     => 'Non spécifié',
    'saving' => 'Enregistrement…',

    // 19. SWEETALERTS (ACTIONS)
    'confirmReservationTitle' => 'Êtes-vous sûr ?',
    'confirmReservationText' => 'Votre réservation sera confirmée',
    'confirmReservationConfirm' => 'Oui, confirmer',
    'confirmReservationCancel' => 'Annuler',

    // 20. SUCCESSES (USED IN CONTROLLERS)
    'edit_profile_of' => 'Modifier le profil',
    'profile_information' => 'Informations du profil',
    'new_password_optional' => 'Nouveau mot de passe (optionnel)',
    'leave_blank_if_no_change' => 'Laissez vide si vous ne souhaitez pas le changer',
    'confirm_new_password_placeholder' => 'Confirmer le nouveau mot de passe',

    'policies' => 'Politiques',
    'no_reservations_yet' => 'Vous n\'avez pas encore de réservations !',
    'no_reservations_message' => 'Il semble que vous n\'ayez pas encore réservé d\'aventures avec nous. Pourquoi ne pas explorer nos excursions incroyables ?',
    'view_available_tours' => 'Voir les excursions disponibles',
    'pending_reservations' => 'Réservations en attente',
    'confirmed_reservations' => 'Réservations confirmées',
    'cancelled_reservations' => 'Réservations annulées',
    'reservations_generic' => 'Réservations',
    'generic_tour' => 'Excursion générique',
    'unknown_tour' => 'Excursion inconnue',
    'tour_date' => 'Date de l\'excursion',
    'participants' => 'Participants',
    'children' => 'Enfants',
    'not_specified' => 'Non spécifié',
    'status_pending' => 'En attente',
    'status_confirmed' => 'Confirmée',
    'status_cancelled' => 'Annulée',
    'status_unknown' => 'Inconnue',

    'view_receipt' => 'Voir le reçu',

    'validation.unique' => 'Cet e-mail est déjà utilisé',

    'validation' => [
        'too_many_attempts' => 'Trop de tentatives échouées. Réessayez dans :seconds secondes.',
    ],

    'open_tour'          => 'Aller à l\'excursion ?',
    'open_tour_text_pre' => 'Vous êtes sur le point d\'ouvrir la page de l\'excursion',
    'open_tour_confirm'  => 'Aller maintenant',
    'open_tour_cancel'   => 'Annuler',

    // Successes (used in controllers)
    'show_password' => 'Afficher le mot de passe',
    'user_registered_successfully'   => 'Utilisateur enregistré avec succès.',
    'user_updated_successfully'      => 'Utilisateur mis à jour avec succès.',
    'user_reactivated_successfully'  => 'Utilisateur réactivé avec succès.',
    'user_deactivated_successfully'  => 'Utilisateur désactivé avec succès.',
    'profile_updated_successfully'   => 'Profil mis à jour avec succès.',
    'user_unlocked_successfully' => 'Votre compte a été déverrouillé. Vous pouvez maintenant vous connecter.',
    'user_locked_successfully' => 'Utilisateur verrouillé avec succès.',
    'auth_required_title' => 'Vous devez vous connecter pour réserver',
    'auth_required_body'  => 'Connectez-vous ou inscrivez-vous pour commencer votre achat. Les champs sont verrouillés jusqu\'à la connexion.',
    'login_now'           => 'Connexion',
    'back_to_login'      => 'Retour à la connexion',

    // 21. MAIL
    'mail' => [
        'trouble_clicking' => 'Si vous avez des difficultés à cliquer sur le bouton ":actionText", copiez et collez l\'URL ci-dessous dans votre navigateur web',
    ],

    // 22. DASHBOARD
    'dashboard' => [
        'title'      => 'Tableau de bord',
        'greeting'   => 'Bonjour :name ! 👋',
        'welcome_to' => 'Bienvenue sur le tableau de bord d\'administration de :app.',
        'hint'       => 'Utilisez le menu latéral pour commencer à gérer le contenu.',
    ],

    // 23. ENTITIES
    'entities' => [
        'users'        => 'Utilisateurs',
        'tours'        => 'Excursions',
        'tour_types'   => 'Types d\'excursions',
        'languages'    => 'Langues',
        'schedules'    => 'Horaires',
        'amenities'    => 'Commodités',
        'bookings'     => 'Réservations',
        'total_bookings'=> 'Total des réservations',
        'itineraries'  => 'Itinéraires',
        'items'        => 'Éléments',
    ],

    // 24. SECTIONS
    'sections' => [
        'available_itineraries' => 'Itinéraires disponibles',
        'upcoming_bookings'     => 'Réservations à venir',
    ],

    // 25. EMPTY STATES
    'empty' => [
        'itinerary_items'   => 'Cet itinéraire n\'a pas encore d\'éléments.',
        'itineraries'       => 'Aucun itinéraire trouvé.',
        'upcoming_bookings' => 'Aucune réservation à venir.',
    ],

    // 26. BUTTONS (GENERIC)
    'buttons' => [
        'view' => 'Voir',
    ],

    // 27. LABELS
    'labels' => [
        'reference' => 'Référence',
        'date'      => 'Date',
    ],
        'pickup'      => 'Lieu de prise en charge',
];
