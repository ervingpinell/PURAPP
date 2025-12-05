<?php

return [

    'messages' => [
        'date_no_longer_available'   => 'La date :date n’est plus disponible pour la réservation (minimum : :min).',
        'limited_seats_available'    => 'Il ne reste que :available places pour « :tour » le :date.',
        'bookings_created_from_cart' => 'Vos réservations ont été créées avec succès à partir du panier.',
        'capacity_exceeded'          => 'Capacité dépassée',
        'meeting_point_hint'         => 'Seul le nom du point est affiché dans la liste.',
    ],

    'validation' => [
        'max_persons_exceeded'    => 'Maximum :max personnes au total par réservation.',
        'min_adults_required'     => 'Un minimum de :min adultes est requis par réservation.',
        'max_kids_exceeded'       => 'Maximum :max enfants par réservation.',
        'no_active_categories'    => 'Cette excursion n’a pas de catégories de clients actives.',
        'min_category_not_met'    => 'Un minimum de :min personnes est requis dans la catégorie « :category ».',
        'max_category_exceeded'   => 'Maximum :max personnes autorisées dans la catégorie « :category ».',
        'min_one_person_required' => 'Il doit y avoir au moins une personne dans la réservation.',
        'category_not_available'  => 'La catégorie avec l’ID :category_id n’est pas disponible pour cette excursion.',
        'max_persons_label'       => 'Nombre maximum de personnes autorisées par réservation',
        'date_range_hint'         => 'Sélectionnez une date entre :from — :to',
    ],

    // =========================================================
    // [01] DISPONIBILITÉ
    // =========================================================
    'availability' => [
        'fields' => [
            'tour'       => 'Excursion',
            'date'       => 'Date',
            'start_time' => 'Heure de début',
            'end_time'   => 'Heure de fin',
            'available'  => 'Disponible',
            'is_active'  => 'Actif',
        ],

        'success' => [
            'created'     => 'Disponibilité créée avec succès.',
            'updated'     => 'Disponibilité mise à jour avec succès.',
            'deactivated' => 'Disponibilité désactivée avec succès.',
        ],

        'error' => [
            'create'     => 'Impossible de créer la disponibilité.',
            'update'     => 'Impossible de mettre à jour la disponibilité.',
            'deactivate' => 'Impossible de désactiver la disponibilité.',
        ],

        'validation' => [
            'tour_id' => [
                'required' => 'Le champ :attribute est obligatoire.',
                'integer'  => 'Le champ :attribute doit être un nombre entier.',
                'exists'   => 'Le :attribute sélectionné n’existe pas.',
            ],
            'date' => [
                'required'    => 'Le champ :attribute est obligatoire.',
                'date_format' => 'Le champ :attribute doit être au format AAAA-MM-JJ.',
            ],
            'start_time' => [
                'date_format'   => 'Le champ :attribute doit être au format HH:MM (24h).',
                'required_with' => 'Le champ :attribute est obligatoire lorsque l’heure de fin est indiquée.',
            ],
            'end_time' => [
                'date_format'    => 'Le champ :attribute doit être au format HH:MM (24h).',
                'after_or_equal' => 'Le champ :attribute doit être supérieur ou égal à l’heure de début.',
            ],
            'available' => [
                'boolean' => 'Le champ :attribute est invalide.',
            ],
            'is_active' => [
                'boolean' => 'Le champ :attribute est invalide.',
            ],
        ],

        'ui' => [
            'page_title'           => 'Disponibilité',
            'page_heading'         => 'Disponibilité',
            'blocked_page_title'   => 'Excursions bloquées',
            'blocked_page_heading' => 'Excursions bloquées',
            'tours_count'          => '( :count excursions )',
            'blocked_count'        => '( :count bloquées )',
        ],

        'filters' => [
            'date'               => 'Date',
            'days'               => 'Jours',
            'product'            => 'Produit',
            'search_placeholder' => 'Rechercher une excursion...',
            'update_state'       => 'Mettre à jour l’état',
            'view_blocked'       => 'Voir les bloquées',
            'tip'                => 'Astuce : cochez des lignes et utilisez une action du menu.',
        ],

        'blocks' => [
            'am_tours'    => 'Excursions AM (toutes les excursions commençant avant 12h00)',
            'pm_tours'    => 'Excursions PM (toutes les excursions commençant après 12h00)',
            'am_blocked'  => 'AM bloquées',
            'pm_blocked'  => 'PM bloquées',
            'empty_block' => 'Il n’y a aucune excursion dans ce bloc.',
            'empty_am'    => 'Aucune excursion bloquée le matin.',
            'empty_pm'    => 'Aucune excursion bloquée l’après-midi.',
            'no_data'     => 'Aucune donnée pour les filtres sélectionnés.',
            'no_blocked'  => 'Il n’y a pas d’excursions bloquées dans l’intervalle sélectionné.',
        ],

        'states' => [
            'available' => 'Disponible',
            'blocked'   => 'Bloquée',
        ],

        'buttons' => [
            'mark_all'         => 'Tout sélectionner',
            'unmark_all'       => 'Tout désélectionner',
            'block_all'        => 'Tout bloquer',
            'unblock_all'      => 'Tout débloquer',
            'block_selected'   => 'Bloquer la sélection',
            'unblock_selected' => 'Débloquer la sélection',
            'back'             => 'Retour',
            'open'             => 'Ouvrir',
            'cancel'           => 'Annuler',
            'block'            => 'Bloquer',
            'unblock'          => 'Débloquer',
        ],

        'confirm' => [
            'view_blocked_title'    => 'Voir les excursions bloquées',
            'view_blocked_text'     => 'La vue des excursions bloquées sera ouverte pour pouvoir les débloquer.',
            'block_title'           => 'Bloquer l’excursion ?',
            'block_html'            => '<b>:label</b> sera bloquée pour la date <b>:day</b>.',
            'block_btn'             => 'Oui, bloquer',
            'unblock_title'         => 'Débloquer l’excursion ?',
            'unblock_html'          => '<b>:label</b> sera débloquée pour la date <b>:day</b>.',
            'unblock_btn'           => 'Oui, débloquer',
            'bulk_title'            => 'Confirmer l’action',
            'bulk_items_html'       => 'Éléments affectés : <b>:count</b>.',
            'bulk_block_day_html'   => 'Bloquer toutes les disponibles pour le jour <b>:day</b>',
            'bulk_block_block_html' => 'Bloquer toutes les disponibles dans le bloc <b>:block</b> le <b>:day</b>',
        ],

        'toasts' => [
            'applying_filters'   => 'Application des filtres...',
            'searching'          => 'Recherche...',
            'updating_range'     => 'Mise à jour de l’intervalle...',
            'invalid_date_title' => 'Date invalide',
            'invalid_date_text'  => 'Les dates passées ne sont pas autorisées.',
            'marked_n'           => ':n sélectionné(s)',
            'unmarked_n'         => ':n désélectionné(s)',
            'updated'            => 'Modification appliquée',
            'updated_count'      => 'Mis à jour : :count',
            'unblocked_count'    => 'Débloqués : :count',
            'no_selection_title' => 'Aucune sélection',
            'no_selection_text'  => 'Sélectionnez au moins une excursion.',
            'no_changes_title'   => 'Aucun changement',
            'no_changes_text'    => 'Aucun élément applicable.',
            'error_generic'      => 'Impossible de réaliser la mise à jour.',
            'error_update'       => 'Impossible de mettre à jour.',
        ],
    ],

    // =========================================================
    // [02] RÉSERVATIONS
    // =========================================================
    'bookings' => [
        'singular' => 'Réservation',
        'plural' => 'Réservations',
        'customer' => 'Client',
        'payment_link_regenerated' => 'Lien de paiement régénéré avec succès',
        'regenerate_payment_link' => 'Régénérer le lien de paiement',
        'confirm_regenerate_payment_link' => 'Êtes-vous sûr de vouloir régénérer le lien de paiement ? L\'ancien lien ne fonctionnera plus.',
        'payment_link_expired_label' => 'Lien expiré',
        'payment_link_info' => 'Lien de paiement pour le client',
        'regenerate_warning' => 'Attention : La régénération du lien invalidera le précédent.',
        'steps' => [
            'customer' => 'Client',
            'select_tour_date' => 'Sélectionner Tour et Date',
            'select_schedule_language' => 'Sélectionner Horaire et Langue',
            'select_participants' => 'Sélectionner Participants',
            'customer_details' => 'Client et Détails',
        ],
        'ui' => [
            'page_title'        => 'Réservations',
            'page_heading'      => 'Gestion des réservations',
            'register_booking'  => 'Enregistrer une réservation',
            'add_booking'       => 'Ajouter une réservation',
            'edit_booking'      => 'Modifier la réservation',
            'booking_details'   => 'Détails de la réservation',
            'download_receipt'  => 'Télécharger le reçu',
            'actions'           => 'Actions',
            'view_details'      => 'Voir les détails',
            'click_to_view'     => 'Cliquez pour voir les détails',
            'zoom_in'           => 'Agrandir',
            'zoom_out'          => 'Réduire',
            'zoom_reset'        => 'Réinitialiser le zoom',
            'no_promo'          => 'Aucun code promotionnel appliqué',
            'create_booking'    => 'Créer une réservation',
            'create_title'      => 'Créer une Nouvelle Réservation',
            'booking_info'      => 'Informations sur la réservation',
            'select_customer'   => 'Sélectionner un client',
            'select_tour'       => 'Sélectionner une excursion',
            'select_tour_first' => 'Sélectionnez d’abord une excursion',
            'select_option'     => 'Sélectionner',
            'select_tour_to_see_categories' => 'Sélectionnez une excursion pour voir les catégories',
            'loading'           => 'Chargement...',
            'no_results'        => 'Aucun résultat',
            'error_loading'     => 'Erreur lors du chargement des données',
            'tour_without_categories' => 'Cette excursion n’a pas de catégories configurées',
            'verifying'         => 'Vérification...',
            'min'               => 'Minimum',
            'max'               => 'Maximum',
            'confirm_booking' => 'Confirmer la réservation',
            'subtotal' => 'Sous-total',
            'total' => 'Total',
            'select_meeting_point' => 'Sélectionner un point de rendez-vous',
            'no_pickup' => 'Pas de prise en charge',
            'hotel' => 'Hôtel',
            'meeting_point' => 'Point de rendez-vous',
            'surcharge' => 'Supplément',
            'discount' => 'Remise',
            'participants' => 'Participants',
            'price_breakdown' => 'Détail des prix',
            'enter_promo' => 'Entrez le code promo',
            'select_hotel' => 'Sélectionner un hôtel',
            'payment_link' => 'Lien de paiement',
            'view_payment' => 'Voir le paiement',
            'hotel_pickup' => 'Prise en charge à l\'hôtel',
            'meeting_point_pickup' => 'Point de rencontre',
            'no_pickup' => 'Pas de prise en charge',
            'optional' => '(Optionnel)',
            'pickup_info' => 'Définir l\'heure de prise en charge pour cette réservation.',
            'confirm_booking_alert' => 'Confirmer cette réservation enverra un email de confirmation au client.',
            'regenerating' => 'Régénération...',
            'copied' => 'Copié !',
            'copy_failed' => 'Échec de la copie',
            'pickup_warning' => 'Attention : Heure de prise en charge :pickup mais le tour commence à :tour. Veuillez vérifier.',
        ],

        'fields' => [
            'booking_id'        => 'ID de réservation',
            'status'            => 'Statut',
            'booking_date'      => 'Date de réservation',
            'booking_origin'    => 'Date de réservation (origine)',
            'reference'         => 'Référence',
            'booking_reference' => 'Référence de Réservation',
            'customer'          => 'Client',
            'email'             => 'E-mail',
            'phone'             => 'Téléphone',
            'tour'              => 'Excursion',
            'language'          => 'Langue',
            'tour_date'         => 'Date de l’excursion',
            'hotel'             => 'Hôtel',
            'other_hotel'       => 'Nom d’un autre hôtel',
            'meeting_point'     => 'Point de rendez-vous',
            'pickup_location'   => 'Lieu de prise en charge',
            'schedule'          => 'Horaire',
            'type'              => 'Type',
            'adults'            => 'Adultes',
            'adults_quantity'   => 'Nombre d’adultes',
            'children'          => 'Enfants',
            'children_quantity' => 'Nombre d’enfants',
            'promo_code'        => 'Code promotionnel',
            'total'             => 'Total',
            'total_to_pay'      => 'Total à payer',
            'adult_price'       => 'Tarif adulte',
            'child_price'       => 'Tarif enfant',
            'notes'             => 'Remarques',
            'hotel_name'        => 'Nom de l’hôtel',
            'travelers'         => 'Voyageurs',
            'subtotal'          => 'Sous-total',
            'discount'          => 'Remise',
            'total_persons'     => 'Personnes',
            'pickup_place'      => 'Lieu de ramassage',
            'pickup_time'       => 'Heure de prise en charge',
            'date'              => 'Date',
            'category'          => 'Catégorie',
            'quantity'          => 'Quantité',
            'price'             => 'Prix',
            'pickup'            => 'Ramassage',
        ],

        'placeholders' => [
            'select_customer'  => 'Sélectionner un client',
            'select_tour'      => 'Sélectionner une excursion',
            'select_schedule'  => 'Sélectionner un horaire',
            'select_language'  => 'Sélectionner une langue',
            'select_hotel'     => 'Sélectionner un hôtel',
            'select_point'     => 'Sélectionner un point de rendez-vous',
            'select_status'    => 'Sélectionner un statut',
            'enter_hotel_name' => 'Entrez le nom de l\'hôtel',
            'enter_promo_code' => 'Entrez le code promo',
            'other'            => 'Autre…',
        ],

        'statuses' => [
            'pending'   => 'En attente',
            'confirmed' => 'Confirmée',
            'cancelled' => 'Annulée',
        ],

        'buttons' => [
            'save'            => 'Enregistrer',
            'cancel'          => 'Annuler',
            'edit'            => 'Modifier',
            'delete'          => 'Supprimer',
            'confirm_changes' => 'Confirmer les modifications',
            'apply'           => 'Appliquer',
            'update'          => 'Mettre à jour',
            'close'           => 'Fermer',
            'back'            => 'Retour',
        ],

        'meeting_point' => [
            'time'     => 'Heure :',
            'view_map' => 'Voir la carte',
        ],

        'pricing' => [
            'title' => 'Récapitulatif des prix',
        ],

        'optional' => 'optionnel',

        'messages' => [
            'past_booking_warning'  => 'Cette réservation correspond à une date passée et ne peut pas être modifiée.',
            'tour_archived_warning' => 'L’excursion de cette réservation a été supprimée/archivée et n’a pas pu être chargée. Sélectionnez une excursion pour voir ses horaires.',
            'no_schedules'          => 'Aucun horaire disponible',
            'deleted_tour'          => 'Excursion supprimée',
            'deleted_tour_snapshot' => 'Excursion supprimée (:name)',
            'tour_archived'         => '(archivée)',
            'meeting_point_hint'    => 'Seul le nom du point est affiché dans la liste.',
            'customer_locked'       => 'Le client est bloqué et ne peut pas être modifié.',
            'promo_applied_subtract' => 'Remise appliquée :',
            'promo_applied_add'     => 'Supplément appliqué :',
            'hotel_locked_by_meeting_point'   => 'Un point de rendez-vous a été sélectionné ; il n’est pas possible de sélectionner un hôtel.',
            'meeting_point_locked_by_hotel'   => 'Un hôtel a été sélectionné ; il n’est pas possible de sélectionner un point de rendez-vous.',
            'promo_removed'         => 'Code promotionnel supprimé',
        ],

        'alerts' => [
            'error_summary' => 'Veuillez corriger les erreurs suivantes :',
        ],

        'validation' => [
            'past_date'          => 'Vous ne pouvez pas réserver pour une date antérieure à aujourd’hui.',
            'promo_required'     => 'Saisissez d’abord un code promotionnel.',
            'promo_checking'     => 'Vérification du code…',
            'promo_invalid'      => 'Code promotionnel invalide.',
            'promo_error'        => 'Impossible de valider le code.',
            'promo_apply_required' => 'Veuillez cliquer sur Appliquer pour valider votre code promotionnel d\'abord.',
            'promo_empty'        => 'Saisissez d’abord un code.',
            'promo_needs_subtotal' => 'Ajoutez au moins 1 passager pour calculer la remise.',
        ],

        'promo' => [
            'applied'         => 'Code appliqué',
            'applied_percent' => 'Code appliqué : -:percent%',
            'applied_amount'  => 'Code appliqué : -$:amount',
        ],

        'loading' => [
            'saving'     => 'Enregistrement...',
            'validating' => 'Validation…',
            'updating'   => 'Mise à jour...',
        ],

        'success' => [
            'created'          => 'Réservation créée avec succès.',
            'updated'          => 'Réservation mise à jour avec succès.',
            'deleted'          => 'Réservation supprimée avec succès.',
            'status_updated'   => 'Statut de la réservation mis à jour avec succès.',
            'status_confirmed' => 'Réservation confirmée avec succès.',
            'status_cancelled' => 'Réservation annulée avec succès.',
            'status_pending'   => 'Réservation définie comme « en attente » avec succès.',
        ],

        'errors' => [
            'create'               => 'Impossible de créer la réservation.',
            'update'               => 'Impossible de mettre à jour la réservation.',
            'delete'               => 'Impossible de supprimer la réservation.',
            'status_update_failed' => 'Impossible de mettre à jour le statut de la réservation.',
            'detail_not_found'     => 'Détails de la réservation introuvables.',
            'schedule_not_found'   => 'Horaire introuvable.',
            'insufficient_capacity' => 'Capacité insuffisante pour « :tour » le :date à :time. Demandé : :requested, disponible : :available (max : :max).',
        ],

        'confirm' => [
            'delete' => 'Êtes-vous sûr de vouloir supprimer cette réservation ?',
        ],

        // SoftDelete & Corbeille
        'trash' => [
            'active_bookings' => 'Réservations actives',
            'trash' => 'Corbeille',
            'restore_booking' => 'Restaurer la réservation',
            'permanently_delete' => 'Supprimer définitivement',
            'force_delete_title' => 'SUPPRESSION DÉFINITIVE',
            'force_delete_warning' => 'Cette action NE PEUT PAS être annulée !',
            'force_delete_message' => 'sera supprimée définitivement.',
            'force_delete_data_loss' => 'Toutes les données associées seront perdues à jamais.',
            'force_delete_confirm' => 'Oui, SUPPRIMER DÉFINITIVEMENT',
            'booking_deleted' => 'Réservation supprimée.',
            'booking_restored' => 'Réservation restaurée avec succès.',
            'booking_force_deleted' => 'Réservation supprimée définitivement. Enregistrements de paiement conservés pour audit.',
            'force_delete_failed' => 'Impossible de supprimer définitivement la réservation.',
            'deleted_booking_indicator' => '(SUPPRIMÉE)',
        ],

        // Checkout Links (for admin-created bookings)
        'checkout_link_label' => 'Lien de paiement client',
        'checkout_link_description' => 'Envoyez ce lien au client pour qu\'il puisse finaliser le paiement de sa réservation.',
        'checkout_link_copy' => 'Copier le lien',
        'checkout_link_copied' => 'Lien copié!',
        'checkout_link_copy_failed' => 'Impossible de copier le lien. Veuillez le copier manuellement.',
        'checkout_link_valid_until' => 'Valide jusqu\'au',
        'checkout_link_expired' => 'Ce lien de paiement a expiré ou n\'est plus valide.',
        'checkout_link_accessed' => 'Le client a accédé au paiement',

        // Payment Status
        'payment_status' => [
            'label' => 'État de Paiement',
            'pending' => 'En attente',
            'paid' => 'Payé',
            'failed' => 'Échoué',
            'refunded' => 'Remboursé',
        ],
    ],

    // =========================================================
    // [03] ACTIONS
    // =========================================================
    'actions' => [
        'confirm'        => 'Confirmer',
        'cancel'         => 'Annuler la réservation',
        'confirm_cancel' => 'Êtes-vous sûr de vouloir annuler cette réservation ?',
        'remove' => 'Supprimer',
        'confirm_create' => 'Confirmer et créer',
        'review_booking' => 'Vérifier la réservation',
        'apply'          => 'Appliquer',
    ],

    // =========================================================
    // [04] FILTRES
    // =========================================================
    'filters' => [
        'advanced_filters' => 'Filtres avancés',
        'dates'            => 'Dates',
        'booked_from'      => 'Réservé à partir du',
        'booked_until'     => 'Réservé jusqu’au',
        'tour_from'        => 'Excursion à partir du',
        'tour_until'       => 'Excursion jusqu’au',
        'all'              => 'Tous',
        'apply'            => 'Appliquer',
        'clear'            => 'Effacer',
        'close_filters'    => 'Fermer les filtres',
        'search_reference' => 'Rechercher une référence...',
        'enter_reference'  => 'Saisir la référence de réservation',
    ],

    // =========================================================
    // [05] RAPPORTS
    // =========================================================
    'reports' => [
        'excel_title'          => 'Exportation des réservations',
        'pdf_title'            => 'Rapport de réservations - Green Vacations CR',
        'general_report_title' => 'Rapport général de réservations - Green Vacations Costa Rica',
        'download_pdf'         => 'Télécharger le PDF',
        'export_excel'         => 'Exporter en Excel',
        'coupon'               => 'Coupon',
        'adjustment'           => 'Ajustement',
        'totals'               => 'Totaux',
        'adults_qty'           => 'Adultes (x:qty)',
        'kids_qty'             => 'Enfants (x:qty)',
        'people'               => 'Personnes',
        'subtotal'             => 'Sous-total',
        'discount'             => 'Remise',
        'surcharge'            => 'Supplément',
        'original_price'       => 'Prix d’origine',
        'total_adults'         => 'Total adultes',
        'total_kids'           => 'Total enfants',
        'total_people'         => 'Total personnes',
    ],

    // =========================================================
    // [06] REÇU
    // =========================================================
    'receipt' => [
        'title'         => 'Reçu de réservation',
        'company'       => 'Green Vacations CR',
        'code'          => 'Code',
        'client'        => 'Client',
        'tour'          => 'Excursion',
        'booking_date'  => 'Date de réservation',
        'tour_date'     => 'Date de l’excursion',
        'schedule'      => 'Horaire',
        'hotel'         => 'Hôtel',
        'meeting_point' => 'Point de rendez-vous',
        'status'        => 'Statut',
        'adults_x'      => 'Adultes (x:count)',
        'kids_x'        => 'Enfants (x:count)',
        'people'        => 'Personnes',
        'subtotal'      => 'Sous-total',
        'discount'      => 'Remise',
        'surcharge'     => 'Supplément',
        'total'         => 'TOTAL',
        'no_schedule'   => 'Aucun horaire',
        'qr_alt'        => 'Code QR',
        'qr_scan'       => 'Scannez pour voir la réservation',
        'thanks'        => 'Merci d’avoir choisi :company !',
        'payment_status' => 'État de paiement:',
    ],

    // =========================================================
    // [07] MODAL DE DÉTAILS
    // =========================================================
    'details' => [
        'booking_info'  => 'Informations sur la réservation',
        'customer_info' => 'Informations sur le client',
        'tour_info'     => 'Informations sur l’excursion',
        'pricing_info'  => 'Informations tarifaires',
        'subtotal'      => 'Sous-total',
        'discount'      => 'Remise',
        'total_persons' => 'Nombre total de personnes',
    ],

    // =========================================================
    // [08] VOYAGEURS (MODAL)
    // =========================================================
    'travelers' => [
        'title_warning'        => 'Attention',
        'title_info'           => 'Information',
        'title_error'          => 'Erreur',
        'max_persons_reached'  => 'Maximum :max personnes par réservation.',
        'max_category_reached' => 'Le maximum pour cette catégorie est :max.',
        'invalid_quantity'     => 'Quantité invalide. Veuillez saisir un nombre valide.',
        'age_between'          => 'Âge :min-:max',
        'age_from'             => 'Âge :min+',
        'age_to'               => 'Jusqu’à :max ans',
    ],


    'excluded_dates' => [

        'ui' => [
            'page_title'           => 'Gestion de la disponibilité et de la capacité',
            'page_heading'         => 'Gestion de la disponibilité et de la capacité',
            'tours_count'          => 'excursions',
            'blocked_page_title'   => 'Excursions bloquées',
            'blocked_page_heading' => 'Excursions bloquées',
            'blocked_count'        => ':count excursions bloquées',
        ],

        'legend' => [
            'title'                 => 'Légende des capacités',
            'base_tour'             => 'Excursion de base',
            'override_schedule'     => 'Override horaire',
            'override_day'          => 'Override jour',
            'override_day_schedule' => 'Override jour+horaire',
            'blocked'               => 'Bloquée',
        ],

        'filters' => [
            'date'               => 'Date',
            'days'               => 'Jours',
            'product'            => 'Rechercher une excursion',
            'search_placeholder' => 'Nom de l’excursion…',
            'bulk_actions'       => 'Actions groupées',
            'update_state'       => 'Mettre à jour l’état',
        ],

        'blocks' => [
            'am'          => 'EXCURSIONS AM',
            'pm'          => 'EXCURSIONS PM',
            'am_blocked'  => 'EXCURSIONS AM (bloquées)',
            'pm_blocked'  => 'EXCURSIONS PM (bloquées)',
            'empty_am'    => 'Aucune excursion dans ce bloc',
            'empty_pm'    => 'Aucune excursion dans ce bloc',
            'no_data'     => 'Aucune donnée à afficher',
            'no_blocked'  => 'Aucune excursion bloquée pour l’intervalle sélectionné',
        ],

        'buttons' => [
            'mark_all'          => 'Tout sélectionner',
            'unmark_all'        => 'Tout désélectionner',
            'block_all'         => 'Tout bloquer',
            'unblock_all'       => 'Tout débloquer',
            'block_selected'    => 'Bloquer la sélection',
            'unblock_selected'  => 'Débloquer la sélection',
            'set_capacity'      => 'Ajuster la capacité',
            'capacity'          => 'Capacité',
            'view_blocked'      => 'Voir les bloquées',
            'capacity_settings' => 'Paramètres de capacité',
            'block'             => 'Bloquer',
            'unblock'           => 'Débloquer',
            'apply'             => 'Appliquer',
            'save'              => 'Enregistrer',
            'cancel'            => 'Annuler',
            'back'              => 'Retour',
        ],

        'states' => [
            'available' => 'Disponible',
            'blocked'   => 'Bloquée',
        ],

        'badges' => [
            'tooltip_prefix' => 'Occupés/Capacité -',
        ],

        'modals' => [
            'capacity_title'          => 'Ajuster la capacité',
            'selected_capacity_title' => 'Ajuster la capacité des éléments sélectionnés',
            'date'                    => 'Date :',
            'hierarchy_title'         => 'Hiérarchie des capacités :',
            'new_capacity'            => 'Nouvelle capacité',
            'hint_zero_blocks'        => 'Mettre 0 pour bloquer complètement',
            'selected_count'          => 'La capacité de :count élément(s) sélectionné(s) sera mise à jour.',
            'capacity_day_title'      => 'Ajuster la capacité pour la journée',
            'capacity_day_subtitle'   => 'Tous les horaires du jour',
        ],

        'confirm' => [
            'block_title'       => 'Bloquer ?',
            'unblock_title'     => 'Débloquer ?',
            'block_html'        => '<strong>:label</strong><br>Date : :day',
            'unblock_html'      => '<strong>:label</strong><br>Date : :day',
            'block_btn'         => 'Bloquer',
            'unblock_btn'       => 'Débloquer',
            'bulk_title'        => 'Confirmer l’opération groupée',
            'bulk_items_html'   => ':count élément(s) seront affectés',
            'block_day_title'   => 'Bloquer toute la journée',
            'block_block_title' => 'Bloquer le bloc :block du :day',
        ],

        'toasts' => [
            'invalid_date_title' => 'Date invalide',
            'invalid_date_text'  => 'Vous ne pouvez pas sélectionner de dates passées',
            'searching'          => 'Recherche…',
            'applying_filters'   => 'Application des filtres…',
            'updating_range'     => 'Mise à jour de l’intervalle…',
            'no_selection_title' => 'Aucune sélection',
            'no_selection_text'  => 'Vous devez sélectionner au moins un élément',
            'no_changes_title'   => 'Aucun changement',
            'no_changes_text'    => 'Aucun élément à mettre à jour',
            'marked_n'           => ':n élément(s) sélectionné(s)',
            'unmarked_n'         => ':n élément(s) désélectionné(s)',
            'error_generic'      => 'L’opération n’a pas pu être réalisée',
            'updated'            => 'Mis à jour',
            'updated_count'      => ':count élément(s) mis à jour',
            'unblocked_count'    => ':count élément(s) débloqués',
            'blocked'            => 'Bloquée',
            'unblocked'          => 'Débloquée',
            'capacity_updated'   => 'Capacité mise à jour',
        ],

    ],

    'capacity' => [

        // =========================================================
        // [01] TITRES & EN-TÊTES UI
        // =========================================================
        'ui' => [
            'page_title'   => 'Gestion des capacités',
            'page_heading' => 'Gestion des capacités',
        ],

        // =========================================================
        // [02] ONGLETS
        // =========================================================
        'tabs' => [
            'global'        => 'Globales',
            'by_tour'       => 'Par excursion + horaire',
            'day_schedules' => 'Overrides jour + horaire',
        ],

        // =========================================================
        // [03] ALERTES
        // =========================================================
        'alerts' => [
            'global_info'        => '<strong>Capacités globales :</strong> définissent la limite de base pour chaque excursion (tous les jours et tous les horaires).',
            'by_tour_info'       => '<strong>Par excursion + horaire :</strong> override de capacité spécifique pour chaque horaire de chaque excursion. Ces overrides ont priorité sur la capacité globale de l’excursion.',
            'day_schedules_info' => '<strong>Jour + horaire :</strong> override de plus haute priorité pour un jour et un horaire spécifiques. Ils sont gérés depuis la vue « Disponibilité et capacité ».',

        ],

        // =========================================================
        // [04] EN-TÊTES DE TABLEAU
        // =========================================================
        'tables' => [
            'global' => [
                'tour'     => 'Excursion',
                'type'     => 'Type',
                'capacity' => 'Capacité globale',
                'level'    => 'Niveau',
            ],
            'by_tour' => [
                'schedule'    => 'Horaire',
                'capacity'    => 'Capacité override',
                'level'       => 'Niveau',
                'no_schedules' => 'Cette excursion n’a pas d’horaires assignés',
            ],
            'day_schedules' => [
                'date'        => 'Date',
                'tour'        => 'Excursion',
                'schedule'    => 'Horaire',
                'capacity'    => 'Capacité',
                'actions'     => 'Actions',
                'no_overrides' => 'Aucun override jour + horaire',
            ],
        ],

        // =========================================================
        // [05] BADGES / LIBELLÉS
        // =========================================================
        'badges' => [
            'base'      => 'Base',
            'override'  => 'Override',
            'global'    => 'Global',
            'blocked'   => 'BLOQUÉ',
            'unlimited' => '∞',
        ],

        // =========================================================
        // [06] BOUTONS
        // =========================================================
        'buttons' => [
            'save'   => 'Enregistrer',
            'delete' => 'Supprimer',
            'back'   => 'Retour',
            'apply'  => 'Appliquer',
            'cancel' => 'Annuler',
        ],

        // =========================================================
        // [07] MESSAGES
        // =========================================================
        'messages' => [
            'empty_placeholder' => 'Vide = utiliser la capacité globale (:capacity)',
            'deleted_confirm'   => 'Supprimer cet override ?',
            'no_day_overrides'  => 'Aucun override jour + horaire.',
        ],

        // =========================================================
        // [08] TOASTS (SweetAlert2)
        // =========================================================
        'toasts' => [
            'success_title' => 'Succès',
            'error_title'   => 'Erreur',
        ],
    ],

];
