<?php

return [
    'title'  => 'Relatórios',
    'header' => 'Relatórios de Vendas',

    // Category Reports
    'category_reports' => [
        'title' => 'Relatórios por Categoria',
        'page_title' => 'Relatórios por Categoria',
    ],

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
        'group_tour_date'     => 'Data do passeio',
        'reset'               => 'Redefinir',
        'apply'               => 'Aplicar Filtros',
        'more_filters'        => 'Mais filtros',
        'status'              => 'Status',
        'all'                 => '(Todos)',
        'all_statuses'        => 'Todos os Status',
        'tours_multi'         => 'Passeios (multi)',
        'tours'               => 'Passeios',
        'languages_multi'     => 'Idiomas (multi)',
        'languages'           => 'Idiomas',
        'categories'          => 'Categorias',
        'export'              => 'Exportar',
        'excel'               => 'Excel',
        'csv_powerbi'         => 'CSV (Power BI)',
    ],

    'status_options' => [
        'paid'       => 'Pago',
        'confirmed'  => 'Confirmado',
        'pending'    => 'Pendente',
        'completed'  => 'Concluído',
        'cancelled'  => 'Cancelado',
    ],

    'kpi' => [
        'revenue_range'      => 'Receita (intervalo)',
        'avg_ticket'         => 'Ticket Médio',
        'bookings'           => 'Reservas',
        'pax'                => 'PAX',
        'confirmed_bookings' => 'Reservas confirmadas',
        'total_revenue'      => 'Receita Total',
        'total_quantity'     => 'Quantidade Total',
        'total_bookings'     => 'Reservas Totais',
        'avg_unit_price'     => 'Preço Médio',
    ],

    'sections' => [
        'revenue_by_period_title' => 'Receita por :period',
        'period_names'            => ['day' => 'dia', 'week' => 'semana', 'month' => 'mês'],
        'top_tours_title'         => 'Melhores Passeios (por receita)',
        'sales_by_language'       => 'Vendas por idioma',
        'pending_bookings'        => 'Reservas pendentes',
        'category_trends'         => 'Tendências de Categorias ao Longo do Tempo',
        'category_statistics'     => 'Estatísticas por Categoria',
        'category_breakdown'      => 'Distribuição por Categoria',
    ],

    'table' => [
        'hash'          => '#',
        'tour'          => 'Passeio',
        'bookings'      => 'Reservas',
        'pax'           => 'PAX',
        'revenue'       => 'Receita',
        'no_data'       => 'Sem dados',
        'ref'           => 'Ref.',
        'customer'      => 'Cliente',
        'tour_date'     => 'Data do passeio',
        'booking_date'  => 'Data da reserva',
        'total'         => 'Total',
        'none_pending'  => 'Nenhuma pendente',
        'category'      => 'Categoria',
        'quantity'      => 'Quantidade',
        'percent_revenue' => '% Receita',
    ],

    'footnotes' => [
        'pending_limit' => '* Até 8 com base nos filtros.',
    ],

    'buttons' => [
        'csv' => 'CSV',
    ],

    'charts' => [
        'aria_revenue_by_period'  => 'Receita por período',
        'aria_sales_by_language'  => 'Vendas por idioma',
        'aria_category_trends'    => 'Tendências de categorias',
        'aria_category_breakdown' => 'Distribuição por categoria',
        'tooltip_revenue'         => 'Receita',
    ],

    'csv' => [
        'revenue_by_period_filename'  => 'receita-por-periodo',
        'top_tours_filename'          => 'melhores-passeios',
        'sales_by_language_filename'  => 'vendas-por-idioma',
        'headers_revenue'             => ['Período', 'Receita', 'Reservas', 'PAX'],
        'headers_top'                 => ['#', 'Passeio', 'Reservas', 'PAX', 'Receita'],
        'headers_language'            => ['Idioma', 'Receita', 'Reservas'],
        'period'                      => 'Período',
        'language'                    => 'Idioma',
    ],
];
