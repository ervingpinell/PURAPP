<?php

return [
    'title'  => 'Reports',
    'header' => 'Sales Reports',

    // Category Reports
    'category_reports' => [
        'title' => 'Category Reports',
        'page_title' => 'Category Reports',
    ],

    'filters' => [
        'quick_range'         => 'Quick range',
        'select_placeholder'  => '— Select —',
        'today'               => 'Today',
        'last7'               => 'Last 7 days',
        'this_week'           => 'This week',
        'this_month'          => 'This month',
        'last_month'          => 'Last month',
        'this_year'           => 'This year',
        'last_year'           => 'Last year',
        'from'                => 'From',
        'to'                  => 'To',
        'period'              => 'Period',
        'period_day'          => 'Daily',
        'period_week'         => 'Weekly',
        'period_month'        => 'Monthly',
        'group_by'            => 'Group by',
        'group_booking_date'  => 'Booking date',
        'group_tour_date'     => 'Tour date',
        'reset'               => 'Reset',
        'apply'               => 'Apply Filters',
        'more_filters'        => 'More filters',
        'status'              => 'Status',
        'all'                 => '(All)',
        'all_statuses'        => 'All Statuses',
        'tours_multi'         => 'Tours (multi)',
        'tours'               => 'Tours',
        'languages_multi'     => 'Languages (multi)',
        'languages'           => 'Languages',
        'categories'          => 'Categories',
        'export'              => 'Export',
        'excel'               => 'Excel',
        'csv_powerbi'         => 'CSV (Power BI)',
    ],

    'status_options' => [
        'paid'       => 'Paid',
        'confirmed'  => 'Confirmed',
        'pending'    => 'Pending',
        'completed'  => 'Completed',
        'cancelled'  => 'Cancelled',
    ],

    'kpi' => [
        'revenue_range'      => 'Revenue (range)',
        'avg_ticket'         => 'Average Ticket',
        'bookings'           => 'Bookings',
        'pax'                => 'PAX',
        'confirmed_bookings' => 'Confirmed bookings',
        'total_revenue'      => 'Total Revenue',
        'total_quantity'     => 'Total Quantity',
        'total_bookings'     => 'Total Bookings',
        'avg_unit_price'     => 'Average Price',
    ],

    'sections' => [
        'revenue_by_period_title' => 'Revenue by :period',
        'period_names'            => ['day' => 'day', 'week' => 'week', 'month' => 'month'],
        'top_tours_title'         => 'Top Tours (by revenue)',
        'sales_by_language'       => 'Sales by language',
        'pending_bookings'        => 'Pending bookings',
        'category_trends'         => 'Category Trends Over Time',
        'category_statistics'     => 'Category Statistics',
        'category_breakdown'      => 'Category Breakdown',
    ],

    'table' => [
        'hash'          => '#',
        'tour'          => 'Tour',
        'bookings'      => 'Bookings',
        'pax'           => 'PAX',
        'revenue'       => 'Revenue',
        'no_data'       => 'No data',
        'ref'           => 'Ref.',
        'customer'      => 'Customer',
        'tour_date'     => 'Tour date',
        'booking_date'  => 'Booking date',
        'total'         => 'Total',
        'none_pending'  => 'No pending',
        'category'      => 'Category',
        'quantity'      => 'Quantity',
        'percent_revenue' => '% Revenue',
    ],

    'footnotes' => [
        'pending_limit' => '* Up to 8 based on filters.',
    ],

    'buttons' => [
        'csv' => 'CSV',
    ],

    'charts' => [
        'aria_revenue_by_period'  => 'Revenue by period',
        'aria_sales_by_language'  => 'Sales by language',
        'aria_category_trends'    => 'Category trends',
        'aria_category_breakdown' => 'Category breakdown',
        'tooltip_revenue'         => 'Revenue',
    ],

    'csv' => [
        'revenue_by_period_filename'  => 'revenue-by-period',
        'top_tours_filename'          => 'top-tours',
        'sales_by_language_filename'  => 'sales-by-language',
        'headers_revenue'             => ['Period', 'Revenue', 'Bookings', 'PAX'],
        'headers_top'                 => ['#', 'Tour', 'Bookings', 'PAX', 'Revenue'],
        'headers_language'            => ['Language', 'Revenue', 'Bookings'],
        'period'                      => 'Period',
        'language'                    => 'Language',
    ],
];
