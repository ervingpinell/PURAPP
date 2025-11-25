<?php

return [
    'title'  => 'Berichte',
    'header' => 'Verkaufsberichte',

    // Category Reports
    'category_reports' => [
        'title' => 'Berichte nach Kategorie',
        'page_title' => 'Berichte nach Kategorie',
    ],

    'filters' => [
        'quick_range'         => 'Schnellbereich',
        'select_placeholder'  => '— Auswählen —',
        'today'               => 'Heute',
        'last7'               => 'Letzte 7 Tage',
        'this_week'           => 'Diese Woche',
        'this_month'          => 'Dieser Monat',
        'last_month'          => 'Letzter Monat',
        'this_year'           => 'Dieses Jahr',
        'last_year'           => 'Letztes Jahr',
        'from'                => 'Von',
        'to'                  => 'Bis',
        'period'              => 'Zeitraum',
        'period_day'          => 'Täglich',
        'period_week'         => 'Wöchentlich',
        'period_month'        => 'Monatlich',
        'group_by'            => 'Gruppieren nach',
        'group_booking_date'  => 'Buchungsdatum',
        'group_tour_date'     => 'Tourdatum',
        'reset'               => 'Zurücksetzen',
        'apply'               => 'Filter Anwenden',
        'more_filters'        => 'Weitere Filter',
        'status'              => 'Status',
        'all'                 => '(Alle)',
        'all_statuses'        => 'Alle Status',
        'tours_multi'         => 'Touren (multi)',
        'tours'               => 'Touren',
        'languages_multi'     => 'Sprachen (multi)',
        'languages'           => 'Sprachen',
        'categories'          => 'Kategorien',
        'export'              => 'Exportieren',
        'excel'               => 'Excel',
        'csv_powerbi'         => 'CSV (Power BI)',
    ],

    'status_options' => [
        'paid'       => 'Bezahlt',
        'confirmed'  => 'Bestätigt',
        'pending'    => 'Ausstehend',
        'completed'  => 'Abgeschlossen',
        'cancelled'  => 'Storniert',
    ],

    'kpi' => [
        'revenue_range'      => 'Umsatz (Bereich)',
        'avg_ticket'         => 'Durchschnittliches Ticket',
        'bookings'           => 'Buchungen',
        'pax'                => 'PAX',
        'confirmed_bookings' => 'Bestätigte Buchungen',
        'total_revenue'      => 'Gesamtumsatz',
        'total_quantity'     => 'Gesamtmenge',
        'total_bookings'     => 'Gesamtbuchungen',
        'avg_unit_price'     => 'Durchschnittspreis',
    ],

    'sections' => [
        'revenue_by_period_title' => 'Umsatz nach :period',
        'period_names'            => ['day' => 'Tag', 'week' => 'Woche', 'month' => 'Monat'],
        'top_tours_title'         => 'Top-Touren (nach Umsatz)',
        'sales_by_language'       => 'Verkäufe nach Sprache',
        'pending_bookings'        => 'Ausstehende Buchungen',
        'category_trends'         => 'Kategorietrends im Zeitverlauf',
        'category_statistics'     => 'Statistiken nach Kategorie',
        'category_breakdown'      => 'Aufschlüsselung nach Kategorie',
    ],

    'table' => [
        'hash'          => '#',
        'tour'          => 'Tour',
        'bookings'      => 'Buchungen',
        'pax'           => 'PAX',
        'revenue'       => 'Umsatz',
        'no_data'       => 'Keine Daten',
        'ref'           => 'Ref.',
        'customer'      => 'Kunde',
        'tour_date'     => 'Tourdatum',
        'booking_date'  => 'Buchungsdatum',
        'total'         => 'Gesamt',
        'none_pending'  => 'Keine ausstehend',
        'category'      => 'Kategorie',
        'quantity'      => 'Menge',
        'percent_revenue' => '% Umsatz',
    ],

    'footnotes' => [
        'pending_limit' => '* Bis zu 8 basierend auf Filtern.',
    ],

    'buttons' => [
        'csv' => 'CSV',
    ],

    'charts' => [
        'aria_revenue_by_period'  => 'Umsatz nach Zeitraum',
        'aria_sales_by_language'  => 'Verkäufe nach Sprache',
        'aria_category_trends'    => 'Kategorietrends',
        'aria_category_breakdown' => 'Aufschlüsselung nach Kategorie',
        'tooltip_revenue'         => 'Umsatz',
    ],

    'csv' => [
        'revenue_by_period_filename'  => 'umsatz-nach-zeitraum',
        'top_tours_filename'          => 'top-touren',
        'sales_by_language_filename'  => 'verkaufe-nach-sprache',
        'headers_revenue'             => ['Zeitraum', 'Umsatz', 'Buchungen', 'PAX'],
        'headers_top'                 => ['#', 'Tour', 'Buchungen', 'PAX', 'Umsatz'],
        'headers_language'            => ['Sprache', 'Umsatz', 'Buchungen'],
        'period'                      => 'Zeitraum',
        'language'                    => 'Sprache',
    ],
];
