<?php

return [
    'title'  => 'Relatórios',
    'header' => 'Relatórios de Vendas',

    'filters' => [
        'quick_range'         => 'Intervalo rápido',
        'select_placeholder'  => '— Selecionar —',
        'today'               => 'Hoje',
        'last7'               => 'Últimos 7 dias',
        'this_week'           => 'Esta semana',
        'this_month'          => 'Este mês',
        'last_month'          => 'Mês passado',
        'this_year'           => 'Este ano',
        'last_year'           => 'Ano passado',
        'from'                => 'De',
        'to'                  => 'Até',
        'period'              => 'Período',
        'period_day'          => 'Diário',
        'period_week'         => 'Semanal',
        'period_month'        => 'Mensal',
        'group_by'            => 'Agrupar por',
        'group_booking_date'  => 'Data da reserva',
        'group_tour_date'     => 'Data do tour',
        'reset'               => 'Limpar',
        'apply'               => 'Aplicar',
        'more_filters'        => 'Mais filtros',
        'status'              => 'Status',
        'all'                 => '(Todos)',
        'tours_multi'         => 'Tours (múltiplos)',
        'languages_multi'     => 'Idiomas (múltiplos)',
    ],

    'status_options' => [
        'paid'       => 'Paga',
        'confirmed'  => 'Confirmada',
        'completed'  => 'Concluída',
        'cancelled'  => 'Cancelada',
    ],

    'kpi' => [
        'revenue_range'      => 'Receita (período)',
        'avg_ticket'         => 'Ticket Médio',
        'bookings'           => 'Reservas',
        'pax'                => 'PAX',
        'confirmed_bookings' => 'Reservas confirmadas',
    ],

    'sections' => [
        'revenue_by_period_title' => 'Receita por :period',
        'period_names'            => ['day' => 'dia', 'week' => 'semana', 'month' => 'mês'],
        'top_tours_title'         => 'Top Tours (por receita)',
        'sales_by_language'       => 'Vendas por idioma',
        'pending_bookings'        => 'Reservas pendentes',
    ],

    'table' => [
        'hash'          => '#',
        'tour'          => 'Tour',
        'bookings'      => 'Reservas',
        'pax'           => 'PAX',
        'revenue'       => 'Receita',
        'no_data'       => 'Sem dados',
        'ref'           => 'Ref.',
        'customer'      => 'Cliente',
        'tour_date'     => 'Data do tour',
        'booking_date'  => 'Data da reserva',
        'total'         => 'Total',
        'none_pending'  => 'Nenhuma pendente',
    ],

    'footnotes' => [
        'pending_limit' => '* Até 8 de acordo com os filtros.',
    ],

    'buttons' => [
        'csv' => 'CSV',
    ],

    'charts' => [
        'aria_revenue_by_period'  => 'Receita por período',
        'aria_sales_by_language'  => 'Vendas por idioma',
        'tooltip_revenue'         => 'Receita',
    ],

    'csv' => [
        'revenue_by_period_filename'  => 'receita-por-periodo',
        'top_tours_filename'          => 'top-tours',
        'sales_by_language_filename'  => 'vendas-por-idioma',
        'headers_revenue'             => ['Período','Receita','Reservas','PAX'],
        'headers_top'                 => ['#','Tour','Reservas','PAX','Receita'],
        'headers_language'            => ['Idioma','Receita','Reservas'],
        'period'                      => 'Período',
        'language'                    => 'Idioma',
    ],
];
