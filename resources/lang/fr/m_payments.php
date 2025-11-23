<?php

return [

    'ui' => [
        'page_title' => 'Paiements',
        'page_heading' => 'Gestion des Paiements',
        'payment_details' => 'Détails du Paiement',
        'payments_list' => 'Liste des Paiements',
        'filters' => 'Filtres',
        'actions' => 'Actions',
        'quick_actions' => 'Actions Rapides',
    ],

    'statistics' => [
        'total_revenue' => 'Revenus Totaux',
        'completed_payments' => 'Paiements Complétés',
        'pending_payments' => 'Paiements en Attente',
        'failed_payments' => 'Paiements Échoués',
    ],

    'fields' => [
        'payment_id' => 'ID de Paiement',
        'booking_ref' => 'Réf. Réservation',
        'customer' => 'Client',
        'tour' => 'Excursion',
        'amount' => 'Montant',
        'gateway' => 'Passerelle',
        'status' => 'Statut',
        'date' => 'Date',
        'payment_method' => 'Méthode de Paiement',
        'tour_date' => 'Date de l\'Excursion',
        'booking_status' => 'Statut de Réservation',
    ],

    'filters' => [
        'search' => 'Rechercher',
        'search_placeholder' => 'Réf. réservation, email, nom...',
        'status' => 'Statut',
        'gateway' => 'Passerelle',
        'date_from' => 'Date Depuis',
        'date_to' => 'Date Jusqu\'à',
        'all' => 'Tous',
    ],

    'statuses' => [
        'pending' => 'En attente',
        'processing' => 'En traitement',
        'completed' => 'Complété',
        'failed' => 'Échoué',
        'refunded' => 'Remboursé',
    ],

    'buttons' => [
        'export_csv' => 'Exporter CSV',
        'view_details' => 'Voir Détails',
        'view_booking' => 'Voir Réservation',
        'process_refund' => 'Traiter Remboursement',
        'back_to_list' => 'Retour à la Liste',
    ],

    'messages' => [
        'no_payments_found' => 'Aucun paiement trouvé',
        'booking_deleted' => 'La réservation a été supprimée définitivement',
        'booking_deleted_on' => 'La réservation a été supprimée définitivement le',
    ],

    'info' => [
        'payment_information' => 'Informations de Paiement',
        'booking_information' => 'Informations de Réservation',
        'gateway_response' => 'Réponse de la Passerelle',
        'payment_timeline' => 'Chronologie du Paiement',
        'payment_created' => 'Paiement Créé',
        'payment_completed' => 'Paiement Complété',
    ],

];
