<?php

return [
    'title'  => 'Rapports',
    'header' => 'Rapports de ventes',

    'filters' => [
        'quick_range'         => 'Plage rapide',
        'select_placeholder'  => '— Sélectionner —',
        'today'               => 'Aujourd’hui',
        'last7'               => '7 derniers jours',
        'this_week'           => 'Cette semaine',
        'this_month'          => 'Ce mois-ci',
        'last_month'          => 'Mois dernier',
        'this_year'           => 'Cette année',
        'last_year'           => 'Année dernière',
        'from'                => 'De',
        'to'                  => 'À',
        'period'              => 'Période',
        'period_day'          => 'Quotidien',
        'period_week'         => 'Hebdomadaire',
        'period_month'        => 'Mensuel',
        'group_by'            => 'Grouper par',
        'group_booking_date'  => 'Date de réservation',
        'group_tour_date'     => 'Date du tour',
        'reset'               => 'Réinitialiser',
        'apply'               => 'Appliquer',
        'more_filters'        => 'Plus de filtres',
        'status'              => 'Statut',
        'all'                 => '(Tous)',
        'tours_multi'         => 'Tours (multi)',
        'languages_multi'     => 'Langues (multi)',
    ],

    'status_options' => [
        'paid'       => 'Payée',
        'confirmed'  => 'Confirmée',
        'completed'  => 'Terminée',
        'cancelled'  => 'Annulée',
    ],

    'kpi' => [
        'revenue_range'      => 'Revenus (période)',
        'avg_ticket'         => 'Panier moyen',
        'bookings'           => 'Réservations',
        'pax'                => 'PAX',
        'confirmed_bookings' => 'Réservations confirmées',
    ],

    'sections' => [
        'revenue_by_period_title' => 'Revenus par :period',
        'period_names'            => ['day' => 'jour', 'week' => 'semaine', 'month' => 'mois'],
        'top_tours_title'         => 'Top Tours (par revenus)',
        'sales_by_language'       => 'Ventes par langue',
        'pending_bookings'        => 'Réservations en attente',
    ],

    'table' => [
        'hash'          => '#',
        'tour'          => 'Tour',
        'bookings'      => 'Réservations',
        'pax'           => 'PAX',
        'revenue'       => 'Revenus',
        'no_data'       => 'Aucune donnée',
        'ref'           => 'Réf.',
        'customer'      => 'Client',
        'tour_date'     => 'Date du tour',
        'booking_date'  => 'Date de réservation',
        'total'         => 'Total',
        'none_pending'  => 'Aucune réservation en attente',
    ],

    'footnotes' => [
        'pending_limit' => '* Jusqu’à 8 selon les filtres.',
    ],

    'buttons' => [
        'csv' => 'CSV',
    ],

    'charts' => [
        'aria_revenue_by_period'  => 'Revenus par période',
        'aria_sales_by_language'  => 'Ventes par langue',
        'tooltip_revenue'         => 'Revenus',
    ],

    'csv' => [
        'revenue_by_period_filename'  => 'revenus-par-periode',
        'top_tours_filename'          => 'top-tours',
        'sales_by_language_filename'  => 'ventes-par-langue',
        'headers_revenue'             => ['Période','Revenus','Réservations','PAX'],
        'headers_top'                 => ['#','Tour','Réservations','PAX','Revenus'],
        'headers_language'            => ['Langue','Revenus','Réservations'],
        'period'                      => 'Période',
        'language'                    => 'Langue',
    ],
];
