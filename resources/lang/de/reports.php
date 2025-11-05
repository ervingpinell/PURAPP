<?php

return [
    'title'  => 'Berichte',
    'header' => 'Verkaufsberichte',

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
        'apply'               => 'Anwenden',
        'more_filters'        => 'Weitere Filter',
        'status'              => 'Status',
        'all'                 => '(Alle)',
        'tours_multi'         => 'Touren (mehrfach)',
        'languages_multi'     => 'Sprachen (mehrfach)',
    ],

    'status_options' => [
        'paid'       => 'Bezahlt',
        'confirmed'  => 'Bestätigt',
        'completed'  => 'Abgeschlossen',
        'cancelled'  => 'Storniert',
    ],

    'kpi' => [
        'revenue_range'      => 'Umsatz (Zeitraum)',
        'avg_ticket'         => 'Durchschnittlicher Ticketwert',
        'bookings'           => 'Buchungen',
        'pax'                => 'PAX',
        'confirmed_bookings' => 'Bestätigte Buchungen',
    ],

    'sections' => [
        'revenue_by_period_title' => 'Umsatz nach :period',
        'period_names'            => ['day' => 'Tag', 'week' => 'Woche', 'month' => 'Monat'],
        'top_tours_title'         => 'Top-Touren (nach Umsatz)',
        'sales_by_language'       => 'Verkäufe nach Sprache',
        'pending_bookings'        => 'Ausstehende Buchungen',
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
        'total'         => 'Summe',
        'none_pending'  => 'Keine ausstehend',
    ],

    'footnotes' => [
        'pending_limit' => '* Bis zu 8 je nach Filtern.',
    ],

    'buttons' => [
        'csv' => 'CSV',
    ],

    'charts' => [
        'aria_revenue_by_period'  => 'Umsatz nach Zeitraum',
        'aria_sales_by_language'  => 'Verkäufe nach Sprache',
        'tooltip_revenue'         => 'Umsatz',
    ],

    'csv' => [
        'revenue_by_period_filename'  => 'umsatz-nach-zeitraum',
        'top_tours_filename'          => 'top-touren',
        'sales_by_language_filename'  => 'verkaeufe-nach-sprache',
        'headers_revenue'             => ['Zeitraum','Umsatz','Buchungen','PAX'],
        'headers_top'                 => ['#','Tour','Buchungen','PAX','Umsatz'],
        'headers_language'            => ['Sprache','Umsatz','Buchungen'],
        'period'                      => 'Zeitraum',
        'language'                    => 'Sprache',
    ],
];
