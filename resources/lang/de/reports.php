<?php

return [
    'title'  => 'Berichte',
    'header' => 'Verkaufsberichte',

    'filters' => [
        'quick_range'         => 'Schnellbereich',
        'select_placeholder'  => '— Wählen —',
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
        'group_tour_date'     => 'Tour-Datum',
        'reset'               => 'Zurücksetzen',
        'apply'               => 'Anwenden',
        'more_filters'        => 'Weitere Filter',
        'status'              => 'Status',
        'all'                 => '(Alle)',
        'tours_multi'         => 'Touren (Multi)',
        'languages_multi'     => 'Sprachen (Multi)',
    ],

    'status_options' => [
        'paid'       => 'Bezahlt',
        'confirmed'  => 'Bestätigt',
        'completed'  => 'Abgeschlossen',
        'cancelled'  => 'Storniert',
    ],

    'kpi' => [
        'revenue_range'      => 'Einnahmen (Zeitraum)',
        'avg_ticket'         => 'Durchschnittliches Ticket',
        'bookings'           => 'Buchungen',
        'pax'                => 'PAX',
        'confirmed_bookings' => 'Bestätigte Buchungen',
    ],

    'sections' => [
        'revenue_by_period_title' => 'Einnahmen pro :period',
        'period_names'            => ['day' => 'Tag', 'week' => 'Woche', 'month' => 'Monat'],
        'top_tours_title'         => 'Top-Touren (nach Einnahmen)',
        'sales_by_language'       => 'Verkäufe nach Sprache',
        'pending_bookings'        => 'Ausstehende Buchungen',
    ],

    'table' => [
        'hash'          => '#',
        'tour'          => 'Tour',
        'bookings'      => 'Buchungen',
        'pax'           => 'PAX',
        'revenue'       => 'Einnahmen',
        'no_data'       => 'Keine Daten',
        'ref'           => 'Ref.',
        'customer'      => 'Kunde',
        'tour_date'     => 'Tour-Datum',
        'booking_date'  => 'Buchungsdatum',
        'total'         => 'Gesamt',
        'none_pending'  => 'Keine ausstehenden Buchungen',
    ],

    'footnotes' => [
        'pending_limit' => '* Bis zu 8 je nach Filter.',
    ],

    'buttons' => [
        'csv' => 'CSV',
    ],

    'charts' => [
        'aria_revenue_by_period'  => 'Einnahmen pro Zeitraum',
        'aria_sales_by_language'  => 'Verkäufe nach Sprache',
        'tooltip_revenue'         => 'Einnahmen',
    ],

    'csv' => [
        'revenue_by_period_filename'  => 'einnahmen-nach-zeitraum',
        'top_tours_filename'          => 'top-touren',
        'sales_by_language_filename'  => 'verkaeufe-nach-sprache',
        'headers_revenue'             => ['Zeitraum','Einnahmen','Buchungen','PAX'],
        'headers_top'                 => ['#','Tour','Buchungen','PAX','Einnahmen'],
        'headers_language'            => ['Sprache','Einnahmen','Buchungen'],
        'period'                      => 'Zeitraum',
        'language'                    => 'Sprache',
    ],
];
