<?php

return [

    'messages' => [
        'date_no_longer_available' => 'La date :date n\'est plus disponible pour la réservation (minimum: :min).',
        'limited_seats_available' => 'Il ne reste que :available places pour ":tour" le :date.',
        'bookings_created_from_cart' => 'Vos réservations ont été créées avec succès depuis le panier.',
        'capacity_exceeded' => 'Capacité Dépassée',
        'meeting_point_hint' => 'Seul le nom du point est affiché dans la liste.',
    ],

    'availability' => [
        'fields' => [
            'tour'        => 'Tour',
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

        'ui' => [
            'page_title'           => 'Disponibilité',
            'page_heading'         => 'Disponibilité',
            'blocked_page_title'   => 'Tours bloqués',
            'blocked_page_heading' => 'Tours bloqués',
        ],

        'states' => [
            'available' => 'Disponible',
            'blocked'   => 'Bloqué',
        ],

        'buttons' => [
            'mark_all'         => 'Tout marquer',
            'unmark_all'       => 'Tout démarquer',
            'block_all'        => 'Tout bloquer',
            'unblock_all'      => 'Tout débloquer',
            'block_selected'   => 'Bloquer sélectionnés',
            'unblock_selected' => 'Débloquer sélectionnés',
            'back'             => 'Retour',
            'open'             => 'Ouvrir',
            'cancel'           => 'Annuler',
            'block'            => 'Bloquer',
            'unblock'          => 'Débloquer',
        ],
    ],

    'bookings' => [
        'ui' => [
            'page_title'         => 'Réservations',
            'page_heading'       => 'Gestion des Réservations',
            'register_booking'   => 'Enregistrer une Réservation',
            'add_booking'        => 'Ajouter une Réservation',
            'edit_booking'       => 'Modifier la Réservation',
            'booking_details'    => 'Détails de la Réservation',
            'download_receipt'   => 'Télécharger le reçu',
            'actions'            => 'Actions',
            'view_details'       => 'Voir les Détails',
            'click_to_view'      => 'Cliquez pour voir les détails',
            'zoom_in'            => 'Zoomer',
            'zoom_out'           => 'Dézoomer',
            'zoom_reset'         => 'Réinitialiser le Zoom',
            'no_promo'        => 'Aucun code promo appliqué',

        ],

        'fields' => [
            'booking_id'        => 'ID de Réservation',
            'status'            => 'Statut',
            'booking_date'      => 'Date de Réservation',
            'booking_origin'    => 'Date de Réservation (origine)',
            'reference'         => 'Référence',
            'customer'          => 'Client',
            'email'             => 'Email',
            'phone'             => 'Téléphone',
            'tour'              => 'Tour',
            'language'          => 'Langue',
            'tour_date'         => 'Date du Tour',
            'hotel'             => 'Hôtel',
            'other_hotel'       => 'Nom d\'un autre hôtel',
            'meeting_point'     => 'Point de Rencontre',
            'pickup_location'   => 'Lieu de Prise en Charge',
            'schedule'          => 'Horaire',
            'type'              => 'Type',
            'adults'            => 'Adultes',
            'adults_quantity'   => 'Quantité d\'Adultes',
            'children'          => 'Enfants',
            'children_quantity' => 'Quantité d\'Enfants',
            'promo_code'        => 'Code promo',
            'total'             => 'Total',
            'total_to_pay'      => 'Total à Payer',
            'adult_price'       => 'Prix Adulte',
            'child_price'       => 'Prix Enfant',
            'notes'             => 'Notes',
        ],

        'placeholders' => [
            'select_customer'  => 'Sélectionner un client',
            'select_tour'      => 'Sélectionner un tour',
            'select_schedule'  => 'Sélectionner un horaire',
            'select_language'  => 'Sélectionner une langue',
            'select_hotel'     => 'Sélectionner un hôtel',
            'select_point'     => 'Sélectionner un point de rencontre',
            'select_status'    => 'Sélectionner un statut',
            'enter_hotel_name' => 'Entrer le nom de l\'hôtel',
            'enter_promo_code' => 'Entrer le code promo',
            'other'            => 'Autre…',
        ],

        'statuses' => [
            'pending'   => 'En Attente',
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
        ],

        'meeting_point' => [
            'time'     => 'Heure:',
            'view_map' => 'Voir la carte',
        ],

        'pricing' => [
            'title' => 'Résumé des Prix',
        ],

        'optional' => 'optionnel',

        'messages' => [
            'past_booking_warning'  => 'Cette réservation correspond à une date passée et ne peut pas être modifiée.',
            'tour_archived_warning' => 'Le tour de cette réservation a été supprimé/archivé et n\'a pas pu être chargé. Sélectionnez un tour pour voir ses horaires.',
            'no_schedules'          => 'Aucun horaire disponible',
            'deleted_tour'          => 'Tour supprimé',
            'deleted_tour_snapshot' => 'Tour Supprimé (:name)',
            'tour_archived'         => '(archivé)',
            'meeting_point_hint'    => 'Seul le nom du point est affiché dans la liste.',
            'customer_locked'       => 'Le client est verrouillé et ne peut pas être modifié.',

        ],

        'alerts' => [
            'error_summary' => 'Veuillez corriger les erreurs suivantes:',
        ],

        'validation' => [
            'past_date'      => 'Vous ne pouvez pas réserver pour des dates antérieures à aujourd\'hui.',
            'promo_required' => 'Entrez d\'abord un code promo.',
            'promo_checking' => 'Vérification du code…',
            'promo_invalid'  => 'Code promo invalide.',
            'promo_error'    => 'Impossible de valider le code.',
        ],

        'promo' => [
            'applied'         => 'Code appliqué',
            'applied_percent' => 'Code appliqué: -:percent%',
            'applied_amount'  => 'Code appliqué: -$:amount',
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
            'status_pending'   => 'Réservation définie comme en attente avec succès.',
        ],

        'errors' => [
            'create'                => 'Impossible de créer la réservation.',
            'update'                => 'Impossible de mettre à jour la réservation.',
            'delete'                => 'Impossible de supprimer la réservation.',
            'status_update_failed'  => 'Impossible de mettre à jour le statut de la réservation.',
            'detail_not_found'      => 'Détails de la réservation non trouvés.',
            'schedule_not_found'    => 'Horaire non trouvé.',
            'insufficient_capacity' => 'Impossible de confirmer la réservation. Capacité insuffisante pour :tour le :date à :time. Demandé: :requested personnes, Disponible: :available/:max.',
        ],

        'confirm' => [
            'delete' => 'Êtes-vous sûr de vouloir supprimer cette réservation?',
        ],
    ],

    'actions' => [
        'confirm'        => 'Confirmer',
        'cancel'         => 'Annuler la Réservation',
        'confirm_cancel' => 'Êtes-vous sûr de vouloir annuler cette réservation?',
    ],

    'filters' => [
        'advanced_filters' => 'Filtres Avancés',
        'dates'            => 'Dates',
        'booked_from'      => 'Réservé depuis',
        'booked_until'     => 'Réservé jusqu\'à',
        'tour_from'        => 'Tour depuis',
        'tour_until'       => 'Tour jusqu\'à',
        'all'              => 'Tous',
        'apply'            => 'Appliquer',
        'clear'            => 'Effacer',
        'close_filters'    => 'Fermer les filtres',
        'search_reference' => 'Rechercher référence...',
        'enter_reference'  => 'Entrer la référence de réservation',
    ],

    'reports' => [
        'excel_title'          => 'Export des Réservations',
        'pdf_title'            => 'Rapport des Réservations - Green Vacations CR',
        'general_report_title' => 'Rapport Général des Réservations - Green Vacations Costa Rica',
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

    'receipt' => [
        'title'         => 'Reçu de Réservation',
        'company'       => 'Green Vacations CR',
        'code'          => 'Code',
        'client'        => 'Client',
        'tour'          => 'Tour',
        'booking_date'  => 'Date de Réservation',
        'tour_date'     => 'Date du Tour',
        'schedule'      => 'Horaire',
        'hotel'         => 'Hôtel',
        'meeting_point' => 'Point de Rencontre',
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
        'qr_scan'       => 'Scanner pour voir la réservation',
        'thanks'        => 'Merci d\'avoir choisi :company!',
    ],

    'details' => [
        'booking_info'  => 'Informations de Réservation',
        'customer_info' => 'Informations Client',
        'tour_info'     => 'Informations du Tour',
        'pricing_info'  => 'Informations de Prix',
        'subtotal'      => 'Sous-total',
        'discount'      => 'Remise',
    ],

];
