<?php

return [
    'title'  => 'Reportes',
    'header' => 'Reportes de Ventas',

    // Category Reports
    'category_reports' => [
        'title' => 'Reportes por Categoría',
        'page_title' => 'Reportes por Categoría',
    ],

    'filters' => [
        'quick_range'         => 'Rango rápido',
        'select_placeholder'  => '— Selecciona —',
        'today'               => 'Hoy',
        'last7'               => 'Últimos 7 días',
        'this_week'           => 'Esta semana',
        'this_month'          => 'Este mes',
        'last_month'          => 'Mes pasado',
        'this_year'           => 'Este año',
        'last_year'           => 'Año pasado',
        'from'                => 'Desde',
        'to'                  => 'Hasta',
        'period'              => 'Periodo',
        'period_day'          => 'Diario',
        'period_week'         => 'Semanal',
        'period_month'        => 'Mensual',
        'group_by'            => 'Agrupar por',
        'group_booking_date'  => 'Fecha reserva',
        'group_tour_date'     => 'Fecha del tour',
        'reset'               => 'Reiniciar',
        'apply'               => 'Aplicar Filtros',
        'more_filters'        => 'Más filtros',
        'status'              => 'Estado',
        'all'                 => '(Todos)',
        'all_statuses'        => 'Todos los Estados',
        'tours_multi'         => 'Tours (multi)',
        'tours'               => 'Tours',
        'languages_multi'     => 'Idiomas (multi)',
        'languages'           => 'Idiomas',
        'categories'          => 'Categorías',
        'export'              => 'Exportar',
        'excel'               => 'Excel',
        'csv_powerbi'         => 'CSV (Power BI)',
    ],

    'status_options' => [
        'paid'       => 'Pagada',
        'confirmed'  => 'Confirmada',
        'pending'    => 'Pendiente',
        'completed'  => 'Completada',
        'cancelled'  => 'Cancelada',
    ],

    'kpi' => [
        'revenue_range'      => 'Ingresos (rango)',
        'avg_ticket'         => 'Ticket Promedio',
        'bookings'           => 'Reservas',
        'pax'                => 'PAX',
        'confirmed_bookings' => 'Reservas confirmadas',
        'total_revenue'      => 'Ingresos Totales',
        'total_quantity'     => 'Cantidad Total',
        'total_bookings'     => 'Reservas Totales',
        'avg_unit_price'     => 'Precio Promedio',
    ],

    'sections' => [
        'revenue_by_period_title' => 'Ingresos por :period',
        'period_names'            => ['day' => 'día', 'week' => 'semana', 'month' => 'mes'],
        'top_tours_title'         => 'Top Tours (por ingresos)',
        'sales_by_language'       => 'Ventas por idioma',
        'pending_bookings'        => 'Reservas pendientes',
        'category_trends'         => 'Tendencias de Categorías en el Tiempo',
        'category_statistics'     => 'Estadísticas por Categoría',
        'category_breakdown'      => 'Desglose por Categoría',
    ],

    'table' => [
        'hash'          => '#',
        'tour'          => 'Tour',
        'bookings'      => 'Reservas',
        'pax'           => 'PAX',
        'revenue'       => 'Ingresos',
        'no_data'       => 'Sin datos',
        'ref'           => 'Ref.',
        'customer'      => 'Cliente',
        'tour_date'     => 'Fecha tour',
        'booking_date'  => 'Fecha reserva',
        'total'         => 'Total',
        'none_pending'  => 'No hay pendientes',
        'category'      => 'Categoría',
        'quantity'      => 'Cantidad',
        'percent_revenue' => '% Ingresos',
    ],

    'footnotes' => [
        'pending_limit' => '* Hasta 8 según filtros.',
    ],

    'buttons' => [
        'csv' => 'CSV',
    ],

    'charts' => [
        'aria_revenue_by_period'  => 'Ingresos por periodo',
        'aria_sales_by_language'  => 'Ventas por idioma',
        'aria_category_trends'    => 'Tendencias de categorías',
        'aria_category_breakdown' => 'Desglose por categoría',
        'tooltip_revenue'         => 'Ingresos',
    ],

    'csv' => [
        'revenue_by_period_filename'  => 'ingresos-por-periodo',
        'top_tours_filename'          => 'top-tours',
        'sales_by_language_filename'  => 'ventas-por-idioma',
        'headers_revenue'             => ['Periodo', 'Ingresos', 'Reservas', 'PAX'],
        'headers_top'                 => ['#', 'Tour', 'Reservas', 'PAX', 'Ingresos'],
        'headers_language'            => ['Idioma', 'Ingresos', 'Reservas'],
        'period'                      => 'Periodo',
        'language'                    => 'Idioma',
    ],
];
