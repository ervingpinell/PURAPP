<?php

/**
 * Table des matières
 *
 * 1. AUTHENTIFICATION ET INSCRIPTION .......... Ligne 37
 * 2. HÔTELS ................................... Ligne 57
 * 3. NAVIGATION GÉNÉRALE ...................... Ligne 67
 * 4. CONTENU ET PAGES ......................... Ligne 82
 * 5. CIRCUITS ET AVIS ......................... Ligne 97
 * 6. HORAIRES ................................. Ligne 131
 * 7. ITINÉRAIRES .............................. Ligne 144
 * 8. HÔTELS (DÉTAIL) .......................... Ligne 156
 * 9. PANIER ET RÉSERVATIONS ................... Ligne 180
 * 10. VALIDATION .............................. Ligne 219
 * 11. BOUTONS ET CRUD ......................... Ligne 225
 * 12. PIED DE PAGE ............................ Ligne 243
 * 13. WHATSAPP ................................ Ligne 247
 * 14. AVIS .................................... Ligne 257
 * 15. VOYAGEURS ............................... Ligne 273
 * 16. CONTACT ................................. Ligne 286
 * 17. ERREURS ................................. Ligne 295
 * 18. MODALE CONNEXION PANIER ................. Ligne 298
 * 19. SWEETALERTS (ACTIONS) ................... Ligne 322
 * 20. SUCCÈS (DANS LES CONTRÔLEURS) ........... Ligne 328
 * 21. COURRIEL ................................ Ligne 381
 * 22. TABLEAU DE BORD ......................... Ligne 386
 * 23. ENTITÉS ..................................Ligne 394
 * 24. SECTIONS ................................ Ligne 408
 * 25. ÉTATS VIDES ............................. Ligne 414
 * 26. BOUTONS (GÉNÉRIQUES) .................... Ligne 421
 * 27. LIBELLÉS ................................ Ligne 426
 */

return [
    'contact_throttled' => "Vous avez envoyé trop de messages en peu de temps. Veuillez patienter un instant avant de réessayer.",

    'pending_email_title'  => 'Changement d’e-mail en attente',
    'pending_email_notice' => 'Vous avez demandé à changer votre e-mail de <strong>:current</strong> à <strong>:pending</strong>. Nous avons envoyé un lien de confirmation à votre nouvel e-mail. Tant que vous ne confirmez pas, nous continuerons d’utiliser votre e-mail actuel.',
    'email_change_warning' => 'Si vous changez votre e-mail, nous enverrons un lien de confirmation à la nouvelle adresse. Votre e-mail actuel restera actif jusqu’à ce que vous confirmiez le changement.',
    'profile_updated_email_change_pending' => 'Votre profil a été mis à jour. Nous avons envoyé un lien à votre nouvel e-mail pour confirmer le changement. Tant que vous ne confirmez pas, nous continuerons d’utiliser votre e-mail actuel.',
    'email_change_confirmed' => 'Votre adresse e-mail a été mise à jour et vérifiée avec succès.',

    'no_slots_for_date' => 'Accune place disponible pour cette date',
    // 1. AUTHENTIFICATION ET INSCRIPTION
    'hello' => 'Bonjour',
    'full_name' => 'Nom complet',
    'email' => 'E-mail',
    'password' => 'Mot de passe',
    'phone' => 'Téléphone',
    'address' => 'Adresse',
    'city' => 'Ville',
    'state' => 'Province/État',
    'zip' => 'Code postal',
    'retype_password' => 'Ressaisir le mot de passe',
    'remember_me' => 'Se souvenir de moi',
    'remember_me_hint' => 'Garder la session ouverte indéfiniment ou jusqu’à la déconnexion manuelle',
    'register' => 'S’inscrire',
    'i_already_have_a_membership' => 'J’ ai déjà un compte',
    'promo_invalid' => 'Code promotionnel invalide.',
    'promo_already_used' => 'Ce code promotionnel a déjà été utilisé pour une autre réservation.',
    'no_past_dates' => 'Vous ne pouvez pas réserver pour une date antérieure à aujourd’hui.',
    'dupe_submit_cart' => 'Une réservation similaire est déjà en cours de traitement. Réessayez dans quelques secondes.',
    'schedule_not_available' => 'Cet horaire n’est pas disponible pour ce circuit (inactif ou non assigné).',
    'date_blocked' => 'La date sélectionnée est bloquée pour ce circuit.',
    'capacity_left' => 'Plus que :available places pour cet horaire.',
    'booking_created_success' => 'Réservation créée avec succès.',
    'booking_updated_success' => 'Réservation mise à jour avec succès.',
    'two_factor_authentication' => 'Authentification à deux facteurs (2FA)',
    'enable_2fa_to_continue' => 'Vous devez activer l\'authentification à deux facteurs (2FA) pour accéder au panneau d\'administration.',

    // 2. HÔTELS
    'hotel_name_required' => 'Le nom de l’hôtel est obligatoire.',
    'hotel_name_unique'   => 'Un hôtel portant ce nom existe déjà.',
    'hotel_name_max'      => 'Le nom de l’hôtel ne peut pas dépasser :max caractères.',
    'hotel_created_success' => 'Hôtel créé avec succès.',
    'hotel_updated_success' => 'Hôtel mis à jour avec succès.',
    'is_active_required'  => 'Le statut est obligatoire.',
    'is_active_boolean'   => 'Le statut doit être vrai ou faux.',
    'outside_list' => 'Cet hôtel ne figure pas sur notre liste. Contactez-nous pour vérifier si nous pouvons proposer un transfert.',

    // 3. NAVIGATION GÉNÉRALE
    'back' => 'Retour',
    'home' => 'Accueil',
    'dashboard_menu' => 'Tableau de bord',
    'profile' => 'Profil',
    'settings' => 'Paramètres',
    'users' => 'Utilisateurs',
    'roles' => 'Rôles',
    'notifications' => 'Notifications',
    'messages' => 'Messages',
    'help' => 'Aide',
    'language' => 'Langue',
    'support' => 'Support',
    'admin_panel' => 'Panneau d’administration',

    // 4. CONTENU ET PAGES
    'faq' => 'Foire aux questions',
    'faqpage' => 'Foire aux questions',
    'no_faqs_available' => 'Aucune FAQ disponible.',
    'contact' => 'Contact',
    'about' => 'À propos de nous',
    'privacy_policy' => 'Politique de confidentialité',
    'terms_and_conditions' => 'Conditions générales',
    'all_policies' => 'Toutes nos politiques',
    'cancellation_and_refunds_policies' => 'Politiques d’annulation et de remboursement',
    'reports' => 'Rapports',
    'footer_text' => config('app.name', 'Company Name'),
    'quick_links' => 'Liens rapides',
    'rights_reserved' => 'Tous droits réservés',

    // 5. CIRCUITS ET AVIS
    'tours' => 'Circuits',
    'tour' => 'Circuit',
    'tour_name' => 'Nom du circuit',
    'overview' => 'Aperçu',
    'duration' => 'Durée',
    'price' => 'Prix',
    'type' => 'Type de circuit',
    'languages_available' => 'Langues disponibles',
    'amenities_included' => 'Services inclus',
    'excluded_amenities' => 'Services non inclus',
    'tour_details' => 'Détails du circuit',
    'select_tour' => 'Sélectionnez un circuit',
    'reviews' => 'Avis',
    'hero_title' => 'Découvrez la magie du Costa Rica',
    'hero_subtext' => 'Explorez nos circuits uniques et vivez l’aventure.',
    'book_now' => 'Réserver maintenant',
    'our_tours' => 'Nos circuits',
    'our_services' => 'Nos services',
    'half_day' => 'Demi-journée',
    'full_day' => 'Journée entière',
    'full_day_description' => 'Parfait pour vivre une expérience complète en une journée',
    'half_day_description' => 'Idéal pour une aventure rapide quand le temps est compté.',
    'full_day_tours' => 'Circuits d’une journée',
    'half_day_tours' => 'Circuits d’une demi-journée',
    'see_tour' => 'Voir le circuit',
    'see_tours' => 'Voir les circuits',
    'see_tour_details' => 'Voir les détails du circuit',
    'what_visitors_say' => 'Ce que disent nos visiteurs',
    'quote_1' => 'Une expérience inoubliable !',
    'guest_1' => 'Carlos M.',
    'quote_2' => 'Je reviendrai sans hésiter.',
    'guest_2' => 'Ana G.',
    'tour_information' => 'Informations sur le circuit',
    'group_size' => 'Taille du groupe',
    'small_groups' => 'Petits groupes',
    'best_value' => 'Meilleur rapport qualité-prix',
    'no_prices_available' => 'Aucun tarif disponible',
    'no_prices_configured' => 'Aucun tarif configuré pour ce circuit',
    'total_persons' => 'Nombre total de personnes',
    'quantity' => 'Quantité',
    'decrease' => 'Diminuer',
    'increase' => 'Augmenter',
    'max_persons_reached' => 'Maximum :max personnes par réservation',
    'min_category_required' => 'Au moins :min requis dans :category',
    'max_category_exceeded' => 'Maximum :max autorisé dans :category',
    'max_persons_exceeded' => 'Maximum :max personnes au total',
    'min_one_person' => 'Au moins une personne est requise',
    'persons_max' => 'personnes max.',
    'or' => 'Ou',
    'open_map' => 'Voir l’emplacement',

    // 6. HORAIRES
    'schedule' => 'Horaire',
    'schedule_am' => 'Horaire matin',
    'schedule_pm' => 'Horaire après-midi',
    'start_time' => 'Heure de début',
    'end_time' => 'Heure de fin',
    'select_date' => 'Sélectionnez une date',
    'select_time' => 'Sélectionnez une heure',
    'select_language' => 'Sélectionnez une langue',
    'schedules' => 'Horaires',
    'horas' => 'heures',
    'hours' => 'heures',

    // 7. ITINÉRAIRES
    'itinerary' => 'Itinéraire',
    'itineraries' => 'Itinéraires',
    'new_itinerary' => 'Nouvel itinéraire',
    'itinerary_items' => 'Éléments de l’itinéraire',
    'item_title' => 'Titre de l’élément',
    'item_description' => 'Description de l’élément',
    'add_item' => 'Ajouter un élément',
    'edit_itinerary' => 'Modifier l’itinéraire',
    'no_itinerary_info' => 'Aucune information d’itinéraire.',
    'whats_included' => 'Ce qui est inclus',

    // 8. HÔTELS (DÉTAIL)
    'hotels' => 'Hôtels',
    'hotel' => 'Hôtel',
    'select_hotel' => 'Hôtel',
    'hotel_other' => 'Autre (préciser manuellement)',
    'hotel_name' => 'Nom de l’hôtel',
    'other_hotel' => 'Autre hôtel (préciser)',
    'hotel_pickup' => 'Prise en charge à l’hôtel',
    'outside_area' => 'Cet hôtel est en dehors de la zone de couverture. Contactez-nous pour voir les options.',
    'pickup_valid' => 'L’hôtel sélectionné est valide ! Après confirmation, nous vous contacterons pour convenir de l’heure de prise en charge.',
    'pickup_details' => 'Détails de la prise en charge',
    'pickup_note' => 'Les prises en charge gratuites s’appliquent uniquement aux hôtels de la zone de La Fortuna…',
    'pickup_points' => 'Points de prise en charge',
    'select_pickup' => 'Sélectionnez un point de prise en charge',
    'type_to_search' => 'Tapez pour rechercher…',
    'no_pickup_available' => 'Aucun point de prise en charge disponible.',
    'pickup_not_found' => 'Hôtel introuvable.',
    'meeting_points' => 'Points de rendez-vous',
    'select_meeting' => 'Sélectionnez un point de rendez-vous',
    'meeting_point_details' => 'Détails du point de rendez-vous',
    'meeting_not_found' => 'Point de rendez-vous introuvable.',
    'main_street_entrance' => 'Entrée rue principale',
    'example_address' => 'Adresse d’exemple 123',
    'hotels_meeting_points' => 'Hôtels et points de rendez-vous',
    'meeting_valid' => 'Le point de rendez-vous sélectionné est valide ! Après confirmation, nous vous enverrons les instructions et l’heure exacte.',
    'meeting_point' => 'Point de rendez-vous',
    'meetingPoint'  => 'Point de rendez-vous',
    'selectHotelHelp' => 'Sélectionnez votre hôtel dans la liste.',
    'selectFromList'      => 'Sélectionnez un élément de la liste',
    'fillThisField'       => 'Remplissez ce champ',
    'pickupRequiredTitle' => 'Prise en charge requise',
    'pickupRequiredBody'  => 'Vous devez sélectionner un hôtel ou un point de rendez-vous pour continuer.',
    'ok'                  => 'OK',
    'pickup_time' => 'Heure de prise en charge',
    'pickupTime'  => 'Heure de prise en charge',
    'meeting_time' => 'Heure de rendez-vous',
    'open_map' => 'Ouvrir la carte',
    'openMap'  => 'Ouvrir la carte',
    'select_pickup_type' => 'Sélectionnez la préférence de prise en charge',
    'no_pickup' => 'Je n\'ai pas besoin de prise en charge',
    'other_hotel_option' => 'Mon hôtel n\'est pas dans la liste',
    'custom_pickup_notice' => 'Vous avez sélectionné un emplacement personnalisé. Veuillez nous contacter pour vérifier si la prise en charge est possible à cet endroit, car il est en dehors de notre liste standard.',

    // 9. PANIER ET RÉSERVATIONS
    'cart' => 'Panier',
    'myCart' => 'Mon panier',
    'my_reservations' => 'Mes réservations',
    'your_cart' => 'Votre panier',
    'add_to_cart' => 'Ajouter au panier',
    'remove_from_cart' => 'Retirer du panier',
    'confirm_reservation' => 'Confirmer la réservation',
    'confirmBooking' => 'Confirmer la réservation',
    'cart_updated' => 'Panier mis à jour avec succès.',
    'itemUpdated' => 'Élément du panier mis à jour avec succès.',
    'cartItemAdded' => 'Circuit ajouté au panier avec succès.',
    'cartItemDeleted' => 'Circuit retiré du panier avec succès.',
    'emptyCart' => 'Votre panier est vide.',
    'no_items_in_cart' => 'Votre panier est vide.',
    'reservation_success' => 'Réservation effectuée avec succès !',
    'reservation_failed' => 'Une erreur est survenue lors de la réservation.',
    'booking_reference' => 'Référence de réservation',
    'booking_date' => 'Date de réservation',
    'reservation_status' => 'Statut de la réservation',
    'blocked_date_for_tour' => 'La date :date est bloquée pour « :tour ».',
    'tourCapacityFull' => 'La capacité maximale pour ce circuit est déjà atteinte.',
    'totalEstimated' => 'Total estimé',
    'total_price' => 'Prix total',
    'total' => 'Total',
    'date' => 'Date',
    'status' => 'Statut',
    'actions' => 'Actions',
    'active' => 'Actif',
    'delete' => 'Supprimer',
    'promoCode' => 'Vous avez un code promo ?',
    'promoCodePlaceholder' => 'Saisissez votre code promo',
    'apply' => 'Appliquer',
    'remove' => 'Retirer',
    'deleteItemTitle' => 'Supprimer l’élément',
    'deleteItemText' => 'Voulez-vous vraiment supprimer cet élément ? Cette action est irréversible.',
    'deleteItemConfirm' => 'Supprimer',
    'deleteItemCancel' => 'Annuler',
    'selectOption' => 'Sélectionnez une option',
    'breakdown' => 'Détail',
    'subtotal'  => 'Sous-total',
    'senior'    => 'Senior',
    'student'   => 'Étudiant',
    'custom' => 'Personnalisé',
    'notes'             => 'Remarques',
    'notes_placeholder' => 'Quelque chose que nous devrions savoir ? (allergies, mobilité, célébrations, etc.)',
    'notes_help'        => 'Ces remarques seront envoyées à notre équipe avec votre réservation.',


    // 10. VALIDATION
    'required_field' => 'Ce champ est obligatoire.',
    'invalid_email' => 'E-mail invalide.',
    'invalid_date' => 'Date invalide.',
    'select_option' => 'Sélectionnez une option',

    // 11. BOUTONS ET CRUD
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
    'read_more' => 'Lire plus',
    'read_less' => 'Lire moins',
    'switch_view' => 'Changer de vue',
    'close' => 'Fermer',

    // 12. PIED DE PAGE
    'contact_us' => 'Contactez-nous',
    'location' => 'San José, Costa Rica',

    // 13. WHATSAPP
    'whatsapp_title' => config('app.name', 'Company Name'),
    'whatsapp_subtitle' => 'Répond généralement immédiatement',
    'whatsapp_attention_schedule' => 'Du lundi au dimanche, de 7h30 à 19h30 (GMT-6)',
    'whatsapp_attention_language' => 'Assistance uniquement en espagnol et en anglais',
    'whatsapp_greeting' => '👋 Bonjour ! Comment pouvons-nous vous aider à planifier votre aventure au Costa Rica ?',
    'whatsapp_placeholder' => 'Bonjour, je suis intéressé par l’un de vos circuits. Pourriez-vous me donner plus d’informations ?',
    'whatsapp_button' => 'Envoyer le message',
    'whatsapp_footer' => 'Connecté via WhatsApp Business',

    // 14. AVIS
    'what_customers_thinks_about' => 'Ce que pensent nos clients de',
    'loading_reviews' => 'Chargement des avis',
    'redirect_to_tour' => 'Rediriger vers le circuit',
    'would_you_like_to_visit' => 'Souhaitez-vous visiter ',
    'this_tour' => 'ce circuit',
    'no_reviews_found' => 'Aucun avis trouvé pour ce circuit.',
    'no_reviews_available' => 'Aucun avis disponible.',
    'error_loading_reviews' => 'Erreur lors du chargement des avis.',
    'anonymous_user' => 'Anonyme',
    'see_more' => 'Voir plus',
    'see_less' => 'Voir moins',
    'powered_by_viator' => 'Propulsé par Viator',
    'go_to_tour' => 'Souhaitez-vous aller au circuit « :name » ?',
    'view_in_viator' => 'Voir :name sur Viator',

    // 15. VOYAGEURS
    'select_travelers' => 'Sélectionner les voyageurs',
    'max_travelers_info' => 'Vous pouvez sélectionner jusqu’à 12 personnes au total.',
    'adult' => 'Adulte',
    'adults' => 'Adultes',
    'adults_quantity' => 'Nombre d’adultes',
    'kid' => 'Enfant',
    'kids' => 'Enfants',
    'kids_quantity' => 'Nombre d’enfants',
    'age_10_plus' => 'Âge 10+',
    'age_4_to_9' => 'Âge 4–9',
    'max_limits_info' => 'Max. 12 voyageurs, max. 2 enfants.',
    'total_persons' => 'Total de personnes',
    'or' => 'ou',
    'min' => 'Min',

    // 16. CONTACT
    'name' => 'Nom',
    'subject' => 'Objet',
    'message' => 'Message',
    'send_message' => 'Envoyer le message',
    'message_sent' => 'Message envoyé',
    'business_hours' => 'Heures d’ouverture',
    'business_schedule' => 'Du lundi au dimanche, de 7h30 à 19h30',
    'field_required'              => 'Ce champ est obligatoire.',
    'email_invalid'               => "Veuillez saisir une adresse e-mail valide.",
    'contact_spam_success' => 'Votre message a été envoyé.',
    'contact_success'      => 'Votre message a été envoyé avec succès. Nous vous contacterons très prochainement.',
    'contact_error'        => "Une erreur s’est produite lors de l’envoi de votre message. Veuillez réessayer dans quelques minutes.",


    // Placeholders
    'contact_name_placeholder'    => 'Votre nom complet',
    'contact_email_placeholder'   => 'votreemail@exemple.com',
    'contact_subject_placeholder' => 'Comment pouvons-nous vous aider ?',
    'contact_message_placeholder' => 'Dites-nous comment nous pouvons vous aider…',

    // SweetAlert
    'message_sent'                => 'Message envoyé',
    'validation_error'            => 'Veuillez vérifier les champs indiqués.',
    'swal_ok'                     => 'OK',

    // 17. ERREURS
    'access_denied' => 'Accès refusé',
    'need_language' => 'Veuillez sélectionner une langue.',
    'need_pickup'   => 'Veuillez sélectionner un hôtel ou un point de rendez-vous.',
    'need_schedule_title' => 'Heure obligatoire',
    'need_schedule'       => 'Veuillez sélectionner une heure.',
    'need_language_title' => 'Langue obligatoire',
    'need_pickup_title'   => 'Prise en charge obligatoire',
    'no_slots_title'      => 'Aucun horaire disponible',
    'no_slots'            => 'Aucun horaire disponible pour la date sélectionnée. Veuillez choisir une autre date.',

    // 18. MODALE CONNEXION PANIER
    'login' => 'Se connecter',
    'view_cart' => 'Voir le panier',
    'login_required_title' => 'Connexion requise',
    'login_required_text' => 'Vous devez vous connecter pour ajouter au panier.',
    'login_required_text_confirm' => 'Vous devez vous connecter pour ajouter au panier. Aller se connecter ?',
    'pax' => 'pax',
    'remove_item_title' => 'Retirer du panier',
    'remove_item_text' => 'Souhaitez-vous retirer ce circuit du panier ?',
    'success' => 'Succès',
    'error' => 'Erreur',
    'validation_error' => 'Données incomplètes',
    'editItem' => 'Modifier l’élément',
    'scheduleHelp' => 'Si le circuit ne nécessite pas d’horaire, laissez vide.',
    'customHotel' => 'Hôtel personnalisé…',
    'otherHotel' => 'Utiliser un hôtel personnalisé',
    'customHotelName' => 'Nom de l’hôtel personnalisé',
    'customHotelHelp' => 'Si vous renseignez un hôtel personnalisé, la sélection de la liste sera ignorée.',
    'inactive' => 'Inactif',
    'notSpecified' => 'Non spécifié',
    'saving' => 'Enregistrement…',

    // 19. SWEETALERTS (ACTIONS)
    'confirmReservationTitle' => 'Êtes-vous sûr ?',
    'confirmReservationText' => 'Votre réservation sera confirmée',
    'confirmReservationConfirm' => 'Oui, confirmer',
    'confirmReservationCancel' => 'Annuler',

    // 20. SUCCÈS (DANS LES CONTRÔLEURS)
    'edit_profile_of' => 'Modifier le profil',
    'profile_information' => 'Informations du profil',
    'new_password_optional' => 'Nouveau mot de passe (optionnel)',
    'leave_blank_if_no_change' => 'Laissez vide si vous ne souhaitez pas le changer',
    'confirm_new_password_placeholder' => 'Confirmer le nouveau mot de passe',

    'policies' => 'Politiques',
    'no_reservations_yet' => 'Vous n’avez pas encore de réservations !',
    'no_reservations_message' => 'Il semble que vous n’ayez pas encore réservé d’aventure avec nous. Pourquoi ne pas explorer nos circuits ?',
    'view_available_tours' => 'Voir les circuits disponibles',
    'pending_reservations' => 'Réservations en attente',
    'confirmed_reservations' => 'Réservations confirmées',
    'cancelled_reservations' => 'Réservations annulées',
    'reservations_generic' => 'Réservations',
    'generic_tour' => 'Circuit générique',
    'unknown_tour' => 'Circuit inconnu',
    'tour_date' => 'Date du circuit',
    'participants' => 'Participants',
    'children' => 'Enfants',
    'not_specified' => 'Non spécifié',
    'status_pending' => 'En attente',
    'status_confirmed' => 'Confirmée',
    'status_cancelled' => 'Annulée',
    'status_unknown' => 'Inconnu',

    'view_receipt' => 'Voir le reçu',

    'validation.unique' => 'Cet e-mail est déjà utilisé',

    'validation' => [
        'too_many_attempts' => 'Trop de tentatives infructueuses. Réessayez dans :seconds secondes.',
    ],

    'open_tour'          => 'Aller au circuit ?',
    'open_tour_text_pre' => 'Vous êtes sur le point d’ouvrir la page du circuit',
    'open_tour_confirm'  => 'Y aller maintenant',
    'open_tour_cancel'   => 'Annuler',

    // Autres succès (dans les contrôleurs)
    'show_password' => 'Afficher le mot de passe',
    'user_registered_successfully'   => 'Utilisateur enregistré avec succès.',
    'user_updated_successfully'      => 'Utilisateur mis à jour avec succès.',
    'user_reactivated_successfully'  => 'Utilisateur réactivé avec succès.',
    'user_deactivated_successfully'  => 'Utilisateur désactivé avec succès.',
    'profile_updated_successfully'   => 'Profil mis à jour avec succès.',
    'user_unlocked_successfully' => 'Votre compte a été déverrouillé. Vous pouvez maintenant vous connecter.',
    'user_locked_successfully' => 'Utilisateur verrouillé avec succès.',
    'auth_required_title' => 'Vous devez vous connecter pour réserver',
    'auth_required_body'  => 'Connectez-vous ou inscrivez-vous pour commencer votre achat. Les champs sont verrouillés jusqu’à la connexion.',
    'login_now'           => 'Se connecter',
    'back_to_login'       => 'Retour à la connexion',

    // 21. COURRIEL
    'mail' => [
        'trouble_clicking' => 'Si vous avez des difficultés à cliquer sur le bouton « :actionText », copiez et collez l’URL ci-dessous dans votre navigateur web',
    ],

    // 22. TABLEAU DE BORD
    'dashboard' => [
        'title'      => 'Tableau de bord',
        'greeting'   => 'Bonjour :name ! 👋',
        'welcome_to' => 'Bienvenue sur le tableau de bord d’administration de :app.',
        'hint'       => 'Utilisez le menu latéral pour commencer à gérer le contenu.',
    ],

    // 23. ENTITÉS
    'entities' => [
        'users'        => 'Utilisateurs',
        'tours'        => 'Circuits',
        'tour_types'   => 'Types de circuit',
        'languages'    => 'Langues',
        'schedules'    => 'Horaires',
        'amenities'    => 'Services',
        'bookings'     => 'Réservations',
        'total_bookings' => 'Total des réservations',
        'itineraries'  => 'Itinéraires',
        'items'        => 'Éléments',
    ],

    // 24. SECTIONS
    'sections' => [
        'available_tours' => 'Tours disponibles',
        'upcoming_bookings'     => 'Réservations à venir',
    ],

    // 25. ÉTATS VIDES
    'empty' => [
        'itinerary_items'   => 'Cet itinéraire ne comporte pas encore d’éléments.',
        'itineraries'       => 'Aucun itinéraire trouvé.',
        'upcoming_bookings' => 'Aucune réservation à venir.',
    ],

    // 26. BOUTONS (GÉNÉRIQUES)
    'buttons' => [
        'view' => 'Voir',
    ],

    'persons' => [
        'count' => '{0} 0 personnes|{1} 1 personne|[2,*] :count personnes',
        'title'            => 'Personnes',
        'pax'              => 'PAX',
        'adults'           => 'Adultes',
        'kids'             => 'Enfants',
        'seniors'          => 'Seniors',
        'infants'          => 'Nourrissons',
        'students'         => 'Étudiants',
        'guides'           => 'Guides',
        'drivers'          => 'Chauffeurs',
        'free'             => 'Gratuit',
        'other'            => 'Autres',
        'category'         => 'Catégorie',
        'categories'       => 'Catégories',
        'quantity'         => 'Quantité',
        'min'              => 'Min',
        'max'              => 'Max',
        'per_person'       => 'par personne',
        'price'            => 'Prix',
        'subtotal'         => 'Sous-total',
        'total'            => 'Total',
        'add_category'     => 'Ajouter une catégorie',
        'remove_category'  => 'Retirer',
        'select_category'  => 'Sélectionnez une catégorie',
        'required'         => 'Obligatoire',
        'optional'         => 'Optionnel',
        'min_required'     => 'Minimum requis : :min',
        'max_allowed'      => 'Maximum autorisé : :max',
        'invalid_quantity' => 'Quantité invalide pour « :category ».',
    ],

    // 27. LIBELLÉS
    'labels' => [
        'reference' => 'Référence',
        'date'      => 'Date',
    ],
    'pickup'      => 'Lieu de prise en charge',
    'filters_title'            => 'Filtrer les résultats',
    'filters_subtitle'         => 'Combinez recherche par texte et catégorie pour trouver la visite idéale.',
    'search_tours_placeholder' => 'Rechercher par nom ou description…',

    'all_categories'           => 'Toutes les catégories',
    'category_label'           => 'Catégorie',

    'filters_active'           => 'Filtres actifs',
    'clear_filters'            => 'Effacer les filtres',
    'clear_short'              => 'Effacer',
    'all_tours_title'       => 'Tous les services',
    'all_tours_subtitle'    => 'Découvrez toutes nos expériences disponibles et trouvez votre prochaine aventure.',
    'filters_btn' => 'Filtrer',
    'more_categories' => 'Plus de catégories',


    // Quantities
    'quantities' => 'Quantités',
    'quantitiesHelp' => 'Ajustez les quantités selon vos besoins. Vous pouvez laisser à 0 les catégories que vous n\'utilisez pas.',
    'no_tours_found' => 'Aucun tour trouvé.',

    // COOKIES (Cookie Consent)
    'cookies' => [
        'banner_aria' => 'Avis sur les cookies',
        'title' => 'Nous utilisons des cookies',
        'message' => 'Ce site utilise des cookies pour améliorer votre expérience. Vous pouvez tout accepter, refuser les cookies non essentiels ou personnaliser vos préférences.',
        'accept_all' => 'Tout accepter',
        'reject' => 'Refuser',
        'customize' => 'Personnaliser',
        'customize_title' => 'Personnaliser les cookies',
        'save_preferences' => 'Enregistrer les préférences',
        'change_preferences' => 'Préférences des cookies',
        'close' => 'Fermer',
        'always_active' => 'Toujours actifs',
        'learn_more' => 'En savoir plus sur les cookies',

        'essential' => 'Cookies essentiels',
        'essential_desc' => 'Nécessaires au fonctionnement de base du site (connexion, panier, sécurité)',

        'functional' => 'Cookies fonctionnels',
        'functional_desc' => 'Mémorisent vos préférences telles que la langue, la devise ou le thème',

        'analytics' => 'Cookies analytiques',
        'analytics_desc' => 'Nous aident à comprendre comment vous utilisez le site pour l\'améliorer (Google Analytics)',

        'marketing' => 'Cookies marketing',
        'marketing_desc' => 'Permettent de vous montrer des publicités pertinentes et de mesurer les campagnes (Facebook Pixel)',
    ],

    'meta' => [
        'home_title' => 'Green Vacations Costa Rica | Tours et Aventures à La Fortuna',
        'home_description' => 'Explorez les meilleurs tours à La Fortuna et au volcan Arenal. Aventures durables, randonnées et plus avec Green Vacations Costa Rica.',
        'tours_title' => 'Tours et Activités à La Fortuna | Green Vacations',
        'tours_description' => 'Découvrez notre sélection de tours à La Fortuna. Des randonnées au volcan aux activités nautiques. Réservez votre aventure dès aujourd\'hui !',
        'contact_title' => 'Contactez-nous | Green Vacations Costa Rica',
        'contact_description' => 'Des questions ? Contactez-nous pour planifier votre voyage au Costa Rica. Nous sommes là pour vous aider à réserver vos tours et transports.',
        'faq_description' => 'Trouvez des réponses aux questions fréquentes sur nos circuits à La Fortuna, le processus de réservation, les annulations et plus encore. Planifiez votre aventure au Costa Rica en toute simplicité.',
    ],
    'faq_more_questions' => 'Avez-vous d\'autres questions ?',

    // 29. EMAIL PREVIEW
    'email_preview' => [
        'page_title' => 'Prévisualisation des e-mails',
        'title' => 'Système de prévisualisation',
        'description' => 'Prévisualisez tous les modèles d\'e-mail sans les envoyer. Utilise des données réelles si disponibles, ou des données fictives.',
        'labels' => [
            'bookings' => 'Réservations Clients',
            'reviews' => 'Avis',
            'auth' => 'Utilisateur et Compte',
            'admin' => 'Admin et Rapports',
        ],
        'types' => [
            'booking-created' => 'Réservation Créée',
            'booking-confirmed' => 'Réservation Confirmée',
            'booking-updated' => 'Réservation Mise à jour',
            'booking-cancelled' => 'Réservation Annulée',
            'booking-expired' => 'Réservation Expirée (Non payée)',
            'payment-success' => 'Paiement Réussi',
            'payment-failed' => 'Paiement Échoué',
            'payment-reminder' => 'Rappel de Paiement',
            'review-request' => 'Lien de Demande d\'Avis',
            'review-reply' => 'Notification de Réponse à un Avis',
            'review-submitted' => 'Avis Soumis (Admin)',
            'welcome' => 'Bienvenue Utilisateur',
            'password-setup' => 'Configuration du Compte (Mot de passe)',
            'verify-email' => 'Vérifier l\'Adresse E-mail',
            'reset-password' => 'Demande de Réinitialisation de Mot de passe',
            'password-updated' => 'Avis de Mise à jour du Mot de passe',
            'account-locked' => 'Notification de Compte Verrouillé',
            'email-change-verification' => 'Vérification de Changement d\'E-mail',
            'email-change-completed' => 'Changement d\'E-mail Terminé',
            'admin-booking-created' => 'Nouvelle Réservation (Admin)',
            'admin-paid-booking' => 'Réservation Payée (Admin)',
            'admin-booking-expiring' => 'Alerte Réservation Expirante',
            'admin-daily-report' => 'Rapport Opérationnel Quotidien',
            'contact-message' => 'Message du Formulaire de Contact',
        ],
        'tools_title' => 'Outils de Messagerie',
        'view_telescope' => 'Voir E-mails Envoyés (Telescope)',
        'config_button' => 'Configuration E-mail',
        'tip_title' => 'Conseil :',
        'tip_text' => 'Cliquez sur n\'importe quel lien pour l\'ouvrir dans un nouvel onglet. L\'aperçu utilise la même mise en page et le même style que les e-mails réels.',
        'config_title' => 'Configuration des Adresses E-mail',
        'table' => [
            'purpose' => 'But',
            'address' => 'Adresse',
            'env_var' => 'Variable d\'environnement',
            'from' => 'De (No-Reply)',
            'reply_to' => 'Répondre à (Support)',
            'admin_notify' => 'Notifications Admin',
            'booking_notify' => 'Notifications Réservations',
        ],
    ],
    'follow_us' => 'Suivez-nous',
];
