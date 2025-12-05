<?php

return [
    // Payment Page
    'payment' => 'Paiement',
    'stripe_description' => 'Paiement par carte de crédit/débit',
    'paypal_description' => 'Paiement PayPal',
    'tilopay_description' => 'Paiement par carte de crédit/débit (Tilopay)',
    'banco_nacional_description' => 'Virement Banco Nacional',
    'bac_description' => 'Virement BAC Credomatic',
    'bcr_description' => 'Virement Banco de Costa Rica',
    'payment_information' => 'Informations de Paiement',
    'secure_payment' => 'Paiement Sécurisé',
    'select_payment_method' => 'Sélectionnez le mode de paiement',
    'payment_secure_encrypted' => 'Votre paiement est sécurisé et crypté',
    'powered_by_stripe' => 'Powered by Stripe. Les informations de votre carte ne sont jamais stockées sur nos serveurs.',
    'pay' => 'Payer',
    'back' => 'Retour',
    'processing' => 'Traitement...',
    'terms_agreement' => 'En effectuant ce paiement, vous acceptez nos conditions générales.',

    // Order Summary
    'order_summary' => 'Récapitulatif de la Commande',
    'subtotal' => 'Sous-total',
    'total' => 'Total',
    'participants' => 'participants',
    'free_cancellation' => 'Annulation gratuite disponible',

    // Confirmation Page
    'payment_successful' => 'Paiement Réussi!',
    'booking_confirmed' => 'Votre réservation a été confirmée',
    'booking_reference' => 'Référence de Réservation',
    'what_happens_next' => 'Et maintenant?',
    'view_my_bookings' => 'Voir Mes Réservations',
    'back_to_home' => 'Retour à l\'Accueil',

    // Next Steps
    'next_step_email' => 'Vous recevrez un e-mail de confirmation avec tous les détails de votre réservation',
    'next_step_confirmed' => 'Votre visite est confirmée pour la date et l\'heure sélectionnées',
    'next_step_manage' => 'Vous pouvez consulter et gérer votre réservation dans "Mes Réservations"',
    'next_step_support' => 'Si vous avez des questions, veuillez contacter notre équipe d\'assistance',

    // Countdown Timer
    'time_remaining' => 'Temps Restant',
    'complete_payment_in' => 'Complétez votre paiement dans',
    'payment_expires_in' => 'Le paiement expire dans',
    'session_expired' => 'Votre session de paiement a expiré',
    'session_expired_message' => 'Veuillez retourner à votre panier et réessayer.',

    // Errors
    'payment_failed' => 'Paiement Échoué',
    'payment_error' => 'Une erreur s\'est produite lors du traitement de votre paiement',
    'payment_declined' => 'Votre paiement a été refusé',
    'try_again' => 'Veuillez réessayer',
    'no_pending_bookings' => 'Aucune réservation en attente trouvée',
    'bookings_not_found' => 'Réservations non trouvées',
    'payment_not_successful' => 'Le paiement n\'a pas réussi. Veuillez réessayer.',
    'payment_confirmation_error' => 'Une erreur s\'est produite lors de la confirmation de votre paiement.',

    // Progress Steps
    'checkout' => 'Paiement',
    'confirmation' => 'Confirmation',

    // Messages
    'complete_payment_message' => 'Veuillez compléter le paiement pour confirmer votre réservation',
    'payment_cancelled' => 'Le paiement a été annulé. Vous pouvez réessayer quand vous êtes prêt.',
    'redirect_paypal' => 'Cliquez sur Payer pour être redirigé vers PayPal et finaliser votre paiement en toute sécurité.',
    'no_cart_data' => 'Aucune donnée de panier trouvée',

    // Admin / Management (merged from m_payments)
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

    'pagination' => [
        'showing' => 'Affichage',
        'to' => 'à',
        'of' => 'de',
        'results' => 'résultats',
    ],
];
