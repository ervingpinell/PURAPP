<?php

return [
    'title'  => 'Rapports',
    'header' => 'Rapports de Ventes',

    // Category Reports
    'category_reports' => [
        'title' => 'Rapports par Catégorie',
        'page_title' => 'Rapports par Catégorie',
    ],

    'filters' => [
        'quick_range'         => 'Plage rapide',
        'select_placeholder'  => '— Sélectionner —',
        'today'               => 'Aujourd\'hui',
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
        'group_tour_date'     => 'Date du circuit',
        'reset'               => 'Réinitialiser',
        'apply'               => 'Appliquer les Filtres',
        'more_filters'        => 'Plus de filtres',
        'status'              => 'Statut',
        'all'                 => '(Tous)',
        'all_statuses'        => 'Tous les Statuts',
        'tours_multi'         => 'Circuits (multi)',
        'tours'               => 'Circuits',
        'languages_multi'     => 'Langues (multi)',
        'languages'           => 'Langues',
        'categories'          => 'Catégories',
        'export'              => 'Exporter',
        'excel'               => 'Excel',
        'csv_powerbi'         => 'CSV (Power BI)',
    ],

    'status_options' => [
        'paid'       => 'Payée',
        'confirmed'  => 'Confirmée',
        'pending'    => 'En attente',
        'completed'  => 'Terminée',
        'cancelled'  => 'Annulée',
    ],

    'kpi' => [
        'revenue_range'      => 'Revenus (plage)',
        'avg_ticket'         => 'Ticket Moyen',
        'bookings'           => 'Réservations',
        'pax'                => 'PAX',
        'confirmed_bookings' => 'Réservations confirmées',
        'total_revenue'      => 'Revenus Totaux',
        'total_quantity'     => 'Quantité Totale',
        'total_bookings'     => 'Réservations Totales',
        'avg_unit_price'     => 'Prix Moyen',
    ],

    'sections' => [
        'revenue_by_period_title' => 'Revenus par :period',
        'period_names'            => ['day' => 'jour', 'week' => 'semaine', 'month' => 'mois'],
        'top_tours_title'         => 'Meilleurs Circuits (par revenus)',
        'sales_by_language'       => 'Ventes par langue',
        'pending_bookings'        => 'Réservations en attente',
        'category_trends'         => 'Tendances des Catégories dans le Temps',
        'category_statistics'     => 'Statistiques par Catégorie',
        'category_breakdown'      => 'Répartition par Catégorie',
    ],

    'table' => [
        'hash'          => '#',
        'tour'          => 'Circuit',
        'bookings'      => 'Réservations',
        'pax'           => 'PAX',
        'revenue'       => 'Revenus',
        'no_data'       => 'Aucune donnée',
        'ref'           => 'Réf.',
        'customer'      => 'Client',
        'tour_date'     => 'Date du circuit',
        'booking_date'  => 'Date de réservation',
        'total'         => 'Total',
        'none_pending'  => 'Aucune en attente',
        'category'      => 'Catégorie',
        'quantity'      => 'Quantité',
        'percent_revenue' => '% Revenus',
    ],

    'footnotes' => [
        'pending_limit' => '* Jusqu\'à 8 selon les filtres.',
    ],

    'buttons' => [
        'csv' => 'CSV',
    ],

    'charts' => [
        'aria_revenue_by_period'  => 'Revenus par période',
        'aria_sales_by_language'  => 'Ventes par langue',
        'aria_category_trends'    => 'Tendances des catégories',
        'aria_category_breakdown' => 'Répartition par catégorie',
        'tooltip_revenue'         => 'Revenus',
    ],

    'csv' => [
        'revenue_by_period_filename'  => 'revenus-par-periode',
        'top_tours_filename'          => 'meilleurs-circuits',
        'sales_by_language_filename'  => 'ventes-par-langue',
        'headers_revenue'             => ['Période', 'Revenus', 'Réservations', 'PAX'],
        'headers_top'                 => ['#', 'Circuit', 'Réservations', 'PAX', 'Revenus'],
        'headers_language'            => ['Langue', 'Revenus', 'Réservations'],
        'period'                      => 'Période',
        'language'                    => 'Langue',
    ],
];
