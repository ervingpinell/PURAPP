<?php

return [

    'messages' => [
        'date_no_longer_available' => 'La date :date n’est plus disponible pour la réservation (minimum : :min).',
        'limited_seats_available' => 'Il ne reste que :available places pour « :tour » le :date.',
        'bookings_created_from_cart' => 'Vos réservations ont été créées avec succès depuis le panier.',
        'capacity_exceeded' => 'Capacité dépassée',
        'meeting_point_hint' => 'Seul le nom du point de rencontre est affiché dans la liste.',
    ],

    'validation' => [
        'max_persons_exceeded' => 'Maximum :max personnes par réservation au total.',
        'min_adults_required' => 'Au minimum :min adultes par réservation requis.',
        'max_kids_exceeded' => 'Maximum :max enfants par réservation.',
        'no_active_categories' => 'Cette visite n’a pas de catégories clients actives.',
        'min_category_not_met' => 'Au minimum :min personnes sont requises dans la catégorie « :category ».',
        'max_category_exceeded' => 'Maximum :max personnes autorisées dans la catégorie « :category ».',
        'min_one_person_required' => 'Au moins une personne est requise dans la réservation.',
        'category_not_available' => 'La catégorie avec l’ID :category_id n’est pas disponible pour cette visite.',
        'max_persons_label' => 'Nombre maximal de personnes autorisées par réservation',
        'date_range_hint' => 'Sélectionnez une date entre :from — :to',
    ],

    // =========================================================
    // [01] DISPONIBILITÉ
    // =========================================================
    'availability' => [
        'fields' => [
            'tour'        => 'Visite',
            'date'        => 'Date',
            'start_time'  => 'Heure de début',
            'end_time'    => 'Heure de fin',
            'available'   => 'Disponible',
            'is_active'   => 'Actif',
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
                'required' => 'Le :attribute est requis.',
                'integer'  => 'Le :attribute doit être un entier.',
                'exists'   => 'Le :attribute sélectionné n’existe pas.',
            ],
            'date' => [
                'required'    => 'La :attribute est requise.',
                'date_format' => 'La :attribute doit être au format YYYY-MM-DD.',
            ],
            'start_time' => [
                'date_format'   => 'La :attribute doit être au format HH:MM (24h).',
                'required_with' => 'La :attribute est requise lorsque l’heure de fin est spécifiée.',
            ],
            'end_time' => [
                'date_format'    => 'La :attribute doit être au format HH:MM (24h).',
                'after_or_equal' => 'La :attribute doit être postérieure ou égale à l’heure de début.',
            ],
            'available' => [
                'boolean' => 'Le champ :attribute est invalide.',
            ],
            'is_active' => [
                'boolean' => 'Le :attribute est invalide.',
            ],
        ],

        'ui' => [
            'page_title'           => 'Disponibilité',
            'page_heading'         => 'Disponibilité',
            'blocked_page_title'   => 'Visites bloquées',
            'blocked_page_heading' => 'Visites bloquées',
            'tours_count'          => '( :count visites )',
            'blocked_count'        => '( :count bloquées )',
        ],

        'filters' => [
            'date'               => 'Date',
            'days'               => 'Jours',
            'product'            => 'Produit',
            'search_placeholder' => 'Rechercher une visite…',
            'update_state'       => 'Mettre à jour l’état',
            'view_blocked'       => 'Voir bloquées',
            'tip'                => 'Astuce : cochez les lignes et utilisez une action de menu.',
        ],

        'blocks' => [
            'am_tours'    => 'Visites AM (toutes les visites débutant avant 12:00pm)',
            'pm_tours'    => 'Visites PM (toutes les visites débutant après 12:00pm)',
            'am_blocked'  => 'AM bloquées',
            'pm_blocked'  => 'PM bloquées',
            'empty_block' => 'Aucune visite dans ce bloc.',
            'empty_am'    => 'Aucune visite AM bloquée.',
            'empty_pm'    => 'Aucune visite PM bloquée.',
            'no_data'     => 'Aucune donnée pour les filtres sélectionnés.',
            'no_blocked'  => 'Aucune visite bloquée dans la plage sélectionnée.',
        ],

        'states' => [
            'available' => 'Disponible',
            'blocked'   => 'Bloquée',
        ],

        'buttons' => [
            'mark_all'         => 'Tout cocher',
            'unmark_all'       => 'Tout décocher',
            'block_all'        => 'Tout bloquer',
            'unblock_all'      => 'Tout débloquer',
            'block_selected'   => 'Bloquer les sélectionnés',
            'unblock_selected' => 'Débloquer les sélectionnés',
            'back'             => 'Retour',
            'open'             => 'Ouvrir',
            'cancel'           => 'Annuler',
            'block'            => 'Bloquer',
            'unblock'          => 'Débloquer',
        ],

        'confirm' => [
            'view_blocked_title'    => 'Voir les visites bloquées',
            'view_blocked_text'     => 'La vue avec les visites bloquées s’ouvrira pour les débloquer.',
            'block_title'           => 'Bloquer la visite ?',
            'block_html'            => '<b>:label</b> sera bloquée pour la date <b>:day</b>.',
            'block_btn'             => 'Oui, bloquer',
            'unblock_title'         => 'Débloquer la visite ?',
            'unblock_html'          => '<b>:label</b> sera débloquée pour la date <b>:day</b>.',
            'unblock_btn'           => 'Oui, débloquer',
            'bulk_title'            => 'Confirmer l’action',
            'bulk_items_html'       => 'Éléments à affecter : <b>:count</b>.',
            'bulk_block_day_html'   => 'Bloquer tout le disponible pour le jour <b>:day</b>',
            'bulk_block_block_html' => 'Bloquer tout le disponible dans le bloc <b>:block</b> le <b>:day</b>',
        ],

        'toasts' => [
            'applying_filters'   => 'Application des filtres…',
            'searching'          => 'Recherche…',
            'updating_range'     => 'Mise à jour de la plage…',
            'invalid_date_title' => 'Date invalide',
            'invalid_date_text'  => 'Les dates passées ne sont pas autorisées.',
            'marked_n'           => 'Coché(s) :n',
            'unmarked_n'         => 'Décoché(s) :n',
            'updated'            => 'Changement appliqué',
            'updated_count'      => 'Mis à jour : :count',
            'unblocked_count'    => 'Débloqué(s) : :count',
            'no_selection_title' => 'Aucune sélection',
            'no_selection_text'  => 'Veuillez cocher au moins une visite.',
            'no_changes_title'   => 'Aucun changement',
            'no_changes_text'    => 'Il n’y a pas d’éléments applicables.',
            'error_generic'      => 'Impossible de finaliser la mise à jour.',
            'error_update'       => 'Mise à jour impossible.',
        ],
    ],

    // =========================================================
    // [02] RÉSERVATIONS
    // =========================================================
    'bookings' => [
        'ui' => [
            'page_title'         => 'Réservations',
            'page_heading'       => 'Gestion des réservations',
            'register_booking'   => 'Enregistrer réservation',
            'add_booking'        => 'Ajouter réservation',
            'edit_booking'       => 'Modifier réservation',
            'booking_details'    => 'Détails de la réservation',
            'download_receipt'   => 'Télécharger reçu',
            'actions'            => 'Actions',
            'view_details'       => 'Voir les détails',
            'click_to_view'      => 'Cliquez pour voir les détails',
            'zoom_in'            => 'Zoom avant',
            'zoom_out'           => 'Zoom arrière',
            'zoom_reset'         => 'Réinitialiser zoom',
            'no_promo'           => 'Aucun code promotionnel appliqué',
            'create_booking'     => 'Créer réservation',
            'booking_info'       => 'Informations de la réservation',
            'select_customer'    => 'Sélectionner client',
            'select_tour'        => 'Sélectionner visite',
            'select_tour_first'  => 'Sélectionnez d’abord une visite',
            'select_option'      => 'Sélectionner',
            'select_tour_to_see_categories' => 'Sélectionnez une visite pour voir les catégories',
            'loading'            => 'Chargement…',
            'no_results'         => 'Aucun résultat',
            'error_loading'      => 'Erreur de chargement des données',
            'tour_without_categories' => 'Cette visite n’a pas de catégories configurées',
            'verifying'          => 'Vérification…',
        ],

        'fields' => [
            'booking_id'        => 'ID réservation',
            'status'            => 'Statut',
            'booking_date'      => 'Date réservation',
            'booking_origin'    => 'Date réservation (origine)',
            'reference'         => 'Référence',
            'customer'          => 'Client',
            'email'             => 'Email',
            'phone'             => 'Téléphone',
            'tour'              => 'Visite',
            'language'          => 'Langue',
            'tour_date'         => 'Date de la visite',
            'hotel'             => 'Hôtel',
            'other_hotel'       => 'Nom d’un autre hôtel',
            'meeting_point'     => 'Point de rencontre',
            'pickup_location'   => 'Lieu de prise en charge',
            'schedule'          => 'Horaire',
            'type'              => 'Type',
            'adults'            => 'Adultes',
            'adults_quantity'   => 'Quantité adult es',
            'children'          => 'Enfants',
            'children_quantity' => 'Quantité enfants',
            'promo_code'        => 'Code promo',
            'total'             => 'Total',
            'total_to_pay'      => 'Total à payer',
            'adult_price'       => 'Prix adulte',
            'child_price'       => 'Prix enfant',
            'notes'             => 'Notes',
            'hotel_name'        => 'Nom de l’hôtel',
            'travelers'         => 'Voyageurs',
            'subtotal'          => 'Sous-total',
            'discount'          => 'Remise',
            'total_persons'     => 'Personnes',
        ],

        'placeholders' => [
            'select_customer'  => 'Sélectionner client',
            'select_tour'      => 'Sélectionner une visite',
            'select_schedule'  => 'Sélectionner horaire',
            'select_language'  => 'Sélectionner langue',
            'select_hotel'     => 'Sélectionner hôtel',
            'select_point'     => 'Sélectionner point de rencontre',
            'select_status'    => 'Sélectionner statut',
            'enter_hotel_name' => 'Entrez le nom de l’hôtel',
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
            'view_map' => 'Voir carte',
        ],

        'pricing' => [
            'title' => 'Résumé des prix',
        ],

        'optional' => 'optionnel',

        'messages' => [
            'past_booking_warning'   => 'Cette réservation correspond à une date passée et ne peut pas être modifiée.',
            'tour_archived_warning'  => 'La visite de cette réservation a été supprimée/archivée et n’a pas pu être chargée. Veuillez sélectionner une visite pour voir ses horaires.',
            'no_schedules'           => 'Aucun horaire disponible',
            'deleted_tour'           => 'Visite supprimée',
            'deleted_tour_snapshot'  => 'Visite supprimée (:name)',
            'tour_archived'          => '(archivée)',
            'meeting_point_hint'     => 'Seul le nom du point de rencontre est affiché dans la liste.',
            'customer_locked'        => 'Le client est bloqué et ne peut pas être modifié.',
            'promo_applied_subtract' => 'Remise appliquée :',
            'promo_applied_add'      => 'Supplément appliqué :',
            'hotel_locked_by_meeting_point' => 'Un point de rencontre a été sélectionné ; l’hôtel ne peut pas être sélectionné.',
            'meeting_point_locked_by_hotel' => 'Un hôtel a été sélectionné ; le point de rencontre ne peut pas être sélectionné.',
            'promo_removed'          => 'Code promo retiré',
        ],

        'alerts' => [
            'error_summary' => 'Veuillez corriger les erreurs suivantes :',
        ],

        'validation' => [
            'past_date'       => 'Vous ne pouvez pas réserver pour des dates antérieures à aujourd’hui.',
            'promo_required'  => 'Saisissez d’abord un code promo.',
            'promo_checking'  => 'Vérification du code…',
            'promo_invalid'   => 'Code promo invalide.',
            'promo_error'     => 'Échec de la validation du code.',
            'promo_empty'     => 'Entrez d’abord un code.',
            'promo_needs_subtotal' => 'Ajoutez au moins 1 passager pour calculer la remise.',
        ],

        'promo' => [
            'applied'         => 'Code appliqué',
            'applied_percent' => 'Code appliqué : -:percent%',
            'applied_amount'  => 'Code appliqué : -$:amount',
        ],

        'loading' => [
            'saving'     => 'Enregistrement…',
            'validating' => 'Validation…',
            'updating'   => 'Mise à jour…',
        ],

        'success' => [
            'created'          => 'Réservation créée avec succès.',
            'updated'          => 'Réservation mise à jour avec succès.',
            'deleted'          => 'Réservation supprimée avec succès.',
            'status_updated'   => 'Statut de réservation mis à jour avec succès.',
            'status_confirmed' => 'Réservation confirmée avec succès.',
            'status_cancelled' => 'Réservation annulée avec succès.',
            'status_pending'   => 'Réservation mise en attente avec succès.',
        ],

        'errors' => [
            'create'               => 'La réservation n’a pas pu être créée.',
            'update'               => 'La réservation n’a pas pu être mise à jour.',
            'delete'               => 'La réservation n’a pas pu être supprimée.',
            'status_update_failed' => 'Le statut de réservation n’a pas pu être mis à jour.',
            'detail_not_found'     => 'Détails de la réservation introuvables.',
            'schedule_not_found'   => 'Horaire introuvable.',
            'insufficient_capacity'=> 'Capacité insuffisante pour « :tour » le :date à :time. Demandé : :requested, disponible : :available (max : :max).',
        ],

        'confirm' => [
            'delete' => 'Êtes-vous sûr de vouloir supprimer cette réservation ?',
        ],
    ],

    // =========================================================
    // [03] ACTIONS
    // =========================================================
    'actions' => [
        'confirm'        => 'Confirmer',
        'cancel'         => 'Annuler la réservation',
        'confirm_cancel' => 'Êtes-vous sûr de vouloir annuler cette réservation ?',
    ],

    // =========================================================
    // [04] FILTRES
    // =========================================================
    'filters' => [
        'advanced_filters' => 'Filtres avancés',
        'dates'            => 'Dates',
        'booked_from'      => 'Réservé à partir de',
        'booked_until'     => 'Réservé jusqu’à',
        'tour_from'        => 'Visite à partir de',
        'tour_until'       => 'Visite jusqu’à',
        'all'              => 'Tous',
        'apply'            => 'Appliquer',
        'clear'            => 'Effacer',
        'close_filters'    => 'Fermer les filtres',
        'search_reference' => 'Rechercher référence…',
        'enter_reference'  => 'Entrez la référence de réservation',
    ],

    // =========================================================
    // [05] RAPPORTS
    // =========================================================
    'reports' => [
        'excel_title'          => 'Exportation des réservations',
        'pdf_title'            => 'Rapport de réservations – Green Vacations CR',
        'general_report_title' => 'Rapport général des réservations – Green Vacations Costa Rica',
        'download_pdf'         => 'Télécharger PDF',
        'export_excel'         => 'Exporter Excel',
        'coupon'               => 'Coupon',
        'adjustment'           => 'Ajustement',
        'totals'               => 'Totaux',
        'adults_qty'           => 'Adultes (x:qty)',
        'kids_qty'             => 'Enfants (x:qty)',
        'people'               => 'Personnes',
        'subtotal'             => 'Sous-total',
        'discount'             => 'Remise',
        'surcharge'            => 'Supplément',
        'original_price'       => 'Prix original',
        'total_adults'         => 'Total Adultes',
        'total_kids'           => 'Total Enfants',
        'total_people'         => 'Total Personnes',
    ],

    // =========================================================
    // [06] REÇU
    // =========================================================
    'receipt' => [
        'title'         => 'Reçu de réservation',
        'company'       => 'Green Vacations CR',
        'code'          => 'Code',
        'client'        => 'Client',
        'tour'          => 'Visite',
        'booking_date'  => 'Date de réservation',
        'tour_date'     => 'Date de la visite',
        'schedule'      => 'Horaire',
        'hotel'         => 'Hôtel',
        'meeting_point' => 'Point de rencontre',
        'status'        => 'Statut',
        'adults_x'      => 'Adultes (x:count)',
        'kids_x'        => 'Enfants (x:count)',
        'people'        => 'Personnes',
        'subtotal'      => 'Sous-total',
        'discount'      => 'Remise',
        'surcharge'     => 'Supplément',
        'total'         => 'TOTAL',
        'no_schedule'   => 'Pas d’horaire',
        'qr_alt'        => 'Code QR',
        'qr_scan'       => 'Scannez pour voir la réservation',
        'thanks'        => 'Merci d’avoir choisi :company !',
    ],

    // =========================================================
    // [07] MODAL DE DÉTAILS
    // =========================================================
    'details' => [
        'booking_info'  => 'Informations de la réservation',
        'customer_info' => 'Informations client',
        'tour_info'     => 'Informations de la visite',
        'pricing_info'  => 'Informations tarifaires',
        'subtotal'      => 'Sous-total',
        'discount'      => 'Remise',
    ],

    'excluded_dates' => [

        'ui' => [
            'page_title'           => 'Gestion de la disponibilité & capacité',
            'page_heading'         => 'Gestion de la disponibilité & capacité',
            'tours_count'          => 'visites',
            'blocked_page_title'   => 'Visites bloquées',
            'blocked_page_heading' => 'Visites bloquées',
            'blocked_count'        => ':count visites bloquées',
        ],

        'legend' => [
            'title'                  => 'Légende de capacité',
            'base_tour'              => 'Visite de base',
            'override_schedule'      => 'Remplacement horaire',
            'override_day'           => 'Remplacement jour',
            'override_day_schedule'  => 'Remplacement jour+horaire',
            'blocked'                => 'Bloquée',
        ],

        'filters' => [
            'date'               => 'Date',
            'days'               => 'Jours',
            'product'            => 'Rechercher visite',
            'search_placeholder' => 'Nom de la visite…',
            'bulk_actions'       => 'Actions groupées',
            'update_state'       => 'Mettre à jour l’état',
        ],

        'blocks' => [
            'am'          => 'VISITES AM',
            'pm'          => 'VISITES PM',
            'am_blocked'  => 'VISITES AM (bloquées)',
            'pm_blocked'  => 'VISITES PM (bloquées)',
            'empty_am'    => 'Aucune visite dans ce bloc',
            'empty_pm'    => 'Aucune visite dans ce bloc',
            'no_data'     => 'Aucune donnée à afficher',
            'no_blocked'  => 'Aucune visite bloquée pour la plage sélectionnée',
        ],

        'buttons' => [
            'mark_all'         => 'Tout cocher',
            'unmark_all'       => 'Tout décocher',
            'block_all'        => 'Tout bloquer',
            'unblock_all'      => 'Tout débloquer',
            'block_selected'   => 'Bloquer les sélectionnés',
            'unblock_selected' => 'Débloquer les sélectionnés',
            'set_capacity'     => 'Ajuster capacité',
            'capacity'         => 'Capacité',
            'view_blocked'     => 'Voir bloqués',
            'capacity_settings'=> 'Paramètres capacité',
            'block'            => 'Bloquer',
            'unblock'          => 'Débloquer',
            'apply'            => 'Appliquer',
            'save'             => 'Enregistrer',
            'cancel'           => 'Annuler',
            'back'             => 'Retour',
        ],

        'states' => [
            'available' => 'Disponible',
            'blocked'   => 'Bloquée',
        ],

        'badges' => [
            'tooltip_prefix' => 'Occupé/Capacité -',
        ],

        'modals' => [
            'capacity_title'            => 'Ajuster capacité',
            'selected_capacity_title'   => 'Ajuster capacité des sélectionnés',
            'date'                      => 'Date :',
            'hierarchy_title'           => 'Hiérarchie de capacité :',
            'new_capacity'              => 'Nouvelle capacité',
            'hint_zero_blocks'          => 'Laissez à 0 pour bloquer entièrement',
            'selected_count'            => 'La capacité sera mise à jour pour :count éléments sélectionnés.',
            'capacity_day_title'        => 'Ajuster capacité pour la journée',
            'capacity_day_subtitle'     => 'Tous les horaires du jour',
        ],

        'confirm' => [
            'block_title'        => 'Bloquer ?',
            'unblock_title'      => 'Débloquer ?',
            'block_html'         => '<strong>:label</strong><br>Date : :day',
            'unblock_html'       => '<strong>:label</strong><br>Date : :day',
            'block_btn'          => 'Bloquer',
            'unblock_btn'        => 'Débloquer',
            'bulk_title'         => 'Confirmer opération en masse',
            'bulk_items_html'    => ':count éléments seront affectés',
            'block_day_title'    => 'Bloquer toute la journée',
            'block_block_title'  => 'Bloquer bloc :block du :day',
        ],

        'toasts' => [
            'invalid_date_title' => 'Date invalide',
            'invalid_date_text'  => 'Vous ne pouvez pas sélectionner des dates passées',
            'searching'          => 'Recherche…',
            'applying_filters'   => 'Application des filtres…',
            'updating_range'     => 'Mise à jour de la plage…',
            'no_selection_title' => 'Aucune sélection',
            'no_selection_text'  => 'Vous devez sélectionner au moins un élément',
            'no_changes_title'   => 'Aucun changement',
            'no_changes_text'    => 'Il n’y a aucun élément à mettre à jour',
            'marked_n'           => ':n éléments cochés',
            'unmarked_n'         => ':n éléments décochés',
            'error_generic'      => 'Impossible de terminer l’opération',
            'updated'            => 'Mis à jour',
            'updated_count'      => ':count éléments mis à jour',
            'unblocked_count'    => ':count éléments débloqués',
            'blocked'            => 'Bloquée',
            'unblocked'          => 'Débloquée',
            'capacity_updated'   => 'Capacité mise à jour',
        ],
    ],

    'capacity' => [

    'ui' => [
        'page_title'   => 'Gestion des Capacités',
        'page_heading' => 'Gestion des Capacités',
    ],

    'tabs' => [
        'global'         => 'Global',
        'by_tour'        => 'Par Tour + Horaire',
        'day_schedules'  => 'Remplacements Jour + Horaire',
    ],

    'alerts' => [
        'global_info' => '<strong>Capacités globales :</strong> Définit la limite de base pour chaque tour (tous les jours et horaires).',
        'by_tour_info' => '<strong>Par Tour + Horaire :</strong> Remplacement spécifique de la capacité pour chaque horaire de chaque tour. Ces remplacements ont priorité sur la capacité globale du tour.',
        'day_schedules_info' => '<strong>Jour + Horaire :</strong> Remplacement de la plus haute priorité pour un jour et un horaire spécifiques. Ils sont gérés depuis la vue "Disponibilité et Capacité".',
    ],

    'tables' => [
        'global' => [
            'tour'       => 'Tour',
            'type'       => 'Type',
            'capacity'   => 'Capacité Globale',
            'level'      => 'Niveau',
        ],
        'by_tour' => [
            'schedule'   => 'Horaire',
            'capacity'   => 'Remplacement de Capacité',
            'level'      => 'Niveau',
            'no_schedules' => 'Ce tour n’a pas d’horaires assignés',
        ],
        'day_schedules' => [
            'date'       => 'Date',
            'tour'       => 'Tour',
            'schedule'   => 'Horaire',
            'capacity'   => 'Capacité',
            'actions'    => 'Actions',
            'no_overrides' => 'Aucun remplacement Jour + Horaire',
        ],
    ],

    'badges' => [
        'base'       => 'Base',
        'override'   => 'Remplacement',
        'global'     => 'Global',
        'blocked'    => 'BLOQUÉ',
        'unlimited'  => '∞',
    ],

    'buttons' => [
        'save'      => 'Enregistrer',
        'delete'    => 'Supprimer',
        'back'      => 'Retour',
        'apply'     => 'Appliquer',
        'cancel'    => 'Annuler',
    ],

    'messages' => [
        'empty_placeholder' => 'Vide = utilise la capacité globale (:capacity)',
        'deleted_confirm'   => 'Supprimer ce remplacement ?',
        'no_day_overrides'  => 'Aucun remplacement Jour + Horaire disponible.',
    ],

    'toasts' => [
        'success_title' => 'Succès',
        'error_title'   => 'Erreur',
    ],
],

];
