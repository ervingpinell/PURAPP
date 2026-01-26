@extends('adminlte::page')

@section('title', __('reports.category_reports.page_title'))

@push('css')
<style>
    :root {
        --card-bg: #fff;
        --card-shadow: 0 8px 18px rgba(0, 0, 0, .06);
        --text-muted: rgba(0, 0, 0, .65);
        --border-soft: rgba(0, 0, 0, .08)
    }

    body.dark-mode {
        --card-bg: #1f2937;
        --card-shadow: 0 8px 18px rgba(0, 0, 0, .25);
        --text-muted: rgba(255, 255, 255, .7);
        --border-soft: rgba(255, 255, 255, .08)
    }

    .card-elevated {
        background: var(--card-bg);
        box-shadow: var(--card-shadow);
        border: 1px solid var(--border-soft);
        border-radius: 12px
    }

    .filter-label {
        font-size: .85rem;
        color: var(--text-muted);
        margin-bottom: .25rem;
        font-weight: 500
    }

    .kpi-card {
        display: flex;
        gap: .75rem;
        padding: 1rem;
        background: var(--card-bg);
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        height: 100%;
        min-height: 80px
    }

    .kpi-icon {
        font-size: 1.05rem;
        opacity: .85;
        flex-shrink: 0
    }

    .kpi-label {
        font-size: .85rem;
        color: var(--text-muted);
        margin-bottom: .25rem
    }

    .kpi-value {
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1.1;
        word-break: break-word
    }

    .table td,
    .table th {
        vertical-align: middle;
        white-space: nowrap
    }

    .table thead th {
        border-bottom: 1px solid var(--border-soft);
        font-weight: 600;
        font-size: .9rem
    }

    .table tbody tr+tr td {
        border-top: 1px solid var(--border-soft)
    }

    .section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap
    }

    .chart-wrap {
        position: relative;
        width: 100%
    }

    /* Mobile First - Extra Small Devices */
    @media (max-width:575.98px) {
        .card-elevated {
            padding: .75rem !important;
            border-radius: 8px
        }

        .h-chart-1 {
            height: 260px
        }

        .h-chart-2 {
            height: 220px
        }

        .kpi-card {
            padding: .75rem;
            gap: .5rem
        }

        .kpi-value {
            font-size: 1.25rem
        }

        .kpi-label {
            font-size: .8rem
        }

        .table {
            font-size: .85rem
        }

        .table td,
        .table th {
            padding: .5rem .25rem
        }

        .table thead th {
            font-size: .8rem
        }

        /* Stack buttons vertically on mobile */
        .btn-group {
            width: 100%
        }

        .btn-group .dropdown-toggle {
            width: 100%
        }

        /* Better filter spacing */
        .filter-label {
            font-size: .8rem
        }

        .form-control,
        .form-select {
            font-size: .9rem
        }

        /* Adjust section titles */
        .section-title h5 {
            font-size: 1rem
        }
    }

    /* Small Devices (Tablets) */
    @media (min-width:576px) and (max-width:767.98px) {
        .h-chart-1 {
            height: 280px
        }

        .h-chart-2 {
            height: 240px
        }

        .kpi-value {
            font-size: 1.35rem
        }

        .table {
            font-size: .9rem
        }
    }

    /* Medium Devices (Tablets Landscape) */
    @media (min-width:768px) and (max-width:991.98px) {
        .h-chart-1 {
            height: 320px
        }

        .h-chart-2 {
            height: 260px
        }
    }

    /* Large Devices (Desktop) */
    @media (min-width:992px) {
        .h-chart-1 {
            height: 360px
        }

        .h-chart-2 {
            height: 280px
        }

        .card-elevated {
            padding: 1.25rem !important
        }
    }

    /* Extra Large Devices */
    @media (min-width:1200px) {
        .h-chart-1 {
            height: 400px
        }

        .h-chart-2 {
            height: 320px
        }
    }

    /* Table Responsiveness */
    @media (max-width:991.98px) {
        .table-responsive {
            -webkit-overflow-scrolling: touch;
            border-radius: 8px
        }

        .table-responsive::-webkit-scrollbar {
            height: 6px
        }

        .table-responsive::-webkit-scrollbar-track {
            background: var(--border-soft)
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--text-muted);
            border-radius: 3px
        }
    }

    /* Print Styles */
    @media print {
        .card-elevated {
            box-shadow: none;
            border: 1px solid #ddd
        }

        .btn,
        .dropdown {
            display: none
        }

        .h-chart-1,
        .h-chart-2 {
            height: 300px !important
        }
    }
</style>
@endpush

@section('content_header')
<h1 class="mb-2">{{ __('reports.category_reports.title') }}</h1>
@stop

@section('content')
<div class="container-fluid">

    {{-- ===== FILTROS ===== --}}
    <div class="card-elevated p-3 mb-3">
        <form method="GET" id="filtersForm">
            <div class="row g-2 align-items-end">
                {{-- Desde / Hasta --}}
                <div class="col-6 col-sm-3 col-lg-2">
                    <label class="filter-label">{{ __('reports.filters.from') }}</label>
                    <input type="date" name="from" id="from" class="form-control" value="{{ $from->toDateString() }}">
                </div>
                <div class="col-6 col-sm-3 col-lg-2">
                    <label class="filter-label">{{ __('reports.filters.to') }}</label>
                    <input type="date" name="to" id="to" class="form-control" value="{{ $to->toDateString() }}">
                </div>

                {{-- Periodo y Agrupar --}}
                <div class="col-6 col-sm-3 col-lg-2">
                    <label class="filter-label">{{ __('reports.filters.period') }}</label>
                    @php $periodSel = $period ?? 'month'; @endphp
                    <select name="period" id="period" class="form-control">
                        <option value="day" @selected($periodSel==='day' )>{{ __('reports.filters.period_day') }}</option>
                        <option value="week" @selected($periodSel==='week' )>{{ __('reports.filters.period_week') }}</option>
                        <option value="month" @selected($periodSel==='month' )>{{ __('reports.filters.period_month') }}</option>
                    </select>
                </div>

                <div class="col-6 col-sm-3 col-lg-2">
                    <label class="filter-label">{{ __('reports.filters.group_by') }}</label>
                    @php $gb = $groupBy ?? 'booking_date'; @endphp
                    <select name="group_by" class="form-control">
                        <option value="booking_date" @selected($gb==='booking_date' )>{{ __('reports.filters.group_booking_date') }}</option>
                        <option value="tour_date" @selected($gb==='tour_date' )>{{ __('reports.filters.group_tour_date') }}</option>
                    </select>
                </div>

                {{-- Acciones --}}
                <div class="col-12 col-lg-4">
                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <a href="{{ route('admin.reports.by-category') }}" class="btn btn-outline-secondary">{{ __('reports.filters.reset') }}</a>
                        <button class="btn btn-primary flex-fill">{{ __('reports.filters.apply') }}</button>
                        <div class="btn-group flex-sm-shrink-0">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-download"></i> <span class="d-none d-sm-inline">{{ __('reports.filters.export') }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" id="export-excel">
                                        <i class="fas fa-file-excel text-success me-2"></i> {{ __('reports.filters.excel') }}
                                    </a></li>
                                <li><a class="dropdown-item" href="#" id="export-csv">
                                        <i class="fas fa-file-csv text-primary me-2"></i> {{ __('reports.filters.csv_powerbi') }}
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Toggle Más filtros (mobile) --}}
                <div class="col-12 d-lg-none mt-2">
                    <a class="text-decoration-none" data-bs-toggle="collapse" href="#moreFilters" role="button">
                        <i class="fas fa-filter me-1"></i> {{ __('reports.filters.more_filters') }}
                    </a>
                </div>
            </div>

            {{-- Más filtros --}}
            <div class="collapse d-lg-block mt-2" id="moreFilters">
                <div class="row g-2">
                    <div class="col-12 col-md-3">
                        <label class="filter-label">{{ __('reports.filters.status') }}</label>
                        <select name="status" class="form-control">
                            <option value="">{{ __('reports.filters.all_statuses') }}</option>
                            <option value="confirmed" @selected($status==='confirmed' )>{{ __('reports.status_options.confirmed') }}</option>
                            <option value="pending" @selected($status==='pending' )>{{ __('reports.status_options.pending') }}</option>
                            <option value="cancelled" @selected($status==='cancelled' )>{{ __('reports.status_options.cancelled') }}</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="filter-label">{{ __('reports.filters.tours') }}</label>
                        @php $tSel = $tourIds ?? []; @endphp
                        <select name="product_id[]" class="form-control" multiple size="5">
                            @foreach($toursMap as $id => $name)
                            <option value="{{ $id }}" @selected(in_array($id, $tSel))>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="filter-label">{{ __('reports.filters.languages') }}</label>
                        @php $lSel = $langIds ?? []; @endphp
                        <select name="tour_language_id[]" class="form-control" multiple size="5">
                            @foreach($langsMap as $id => $name)
                            <option value="{{ $id }}" @selected(in_array($id, $lSel))>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="filter-label">{{ __('reports.filters.categories') }}</label>
                        @php $cSel = $categoryIds ?? []; @endphp
                        <select name="category_id[]" class="form-control" multiple size="5">
                            @foreach($categoriesMap as $id => $name)
                            <option value="{{ $id }}" @selected(in_array($id, $cSel))>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ===== KPIs ===== --}}
    <div class="row g-2 mb-3">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-icon mt-1"><i class="fas fa-coins"></i></div>
                <div>
                    <div class="kpi-label">{{ __('reports.kpi.total_revenue') }}</div>
                    <div class="kpi-value">$ {{ number_format($kpis['total_revenue'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-icon mt-1"><i class="fas fa-users"></i></div>
                <div>
                    <div class="kpi-label">{{ __('reports.kpi.total_quantity') }}</div>
                    <div class="kpi-value">{{ number_format($kpis['total_quantity']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-icon mt-1"><i class="fas fa-receipt"></i></div>
                <div>
                    <div class="kpi-label">{{ __('reports.kpi.total_bookings') }}</div>
                    <div class="kpi-value">{{ number_format($kpis['total_bookings']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-icon mt-1"><i class="fas fa-tags"></i></div>
                <div>
                    <div class="kpi-label">{{ __('reports.filters.categories') }}</div>
                    <div class="kpi-value">{{ $kpis['categories_count'] }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Contenido ===== --}}
    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="card-elevated p-3">
                <div class="section-title">
                    <h5 class="mb-0">{{ __('reports.sections.category_trends') }}</h5>
                </div>
                <div class="chart-wrap h-chart-1">
                    <canvas id="chartTrends" aria-label="{{ __('reports.charts.aria_category_trends') }}" role="img"></canvas>
                </div>
            </div>

            <div class="card-elevated mt-3">
                <div class="d-flex align-items-center justify-content-between px-3 pt-3">
                    <h5 class="mb-0">{{ __('reports.sections.category_statistics') }}</h5>
                </div>
                <div class="p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('reports.table.category') }}</th>
                                    <th class="text-end">{{ __('reports.table.quantity') }}</th>
                                    <th class="text-end">{{ __('reports.table.revenue') }}</th>
                                    <th class="text-end">{{ __('reports.kpi.avg_unit_price') }}</th>
                                    <th class="text-end">{{ __('reports.table.bookings') }}</th>
                                    <th class="text-end">{{ __('reports.table.percent_revenue') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categoryStats as $stat)
                                <tr>
                                    <td><strong>{{ $stat->category_name }}</strong></td>
                                    <td class="text-end">{{ number_format($stat->total_quantity) }}</td>
                                    <td class="text-end">$ {{ number_format($stat->total_revenue, 2) }}</td>
                                    <td class="text-end">$ {{ number_format($stat->avg_unit_price, 2) }}</td>
                                    <td class="text-end">{{ number_format($stat->bookings_count) }}</td>
                                    <td class="text-end">
                                        {{ $kpis['total_revenue'] > 0 ? number_format(($stat->total_revenue / $kpis['total_revenue']) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center p-4">{{ __('reports.table.no_data') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card-elevated p-3 mb-3">
                <div class="section-title">
                    <h5 class="mb-0">{{ __('reports.sections.category_breakdown') }}</h5>
                </div>
                <div class="chart-wrap h-chart-2">
                    <canvas id="chartBreakdown" aria-label="{{ __('reports.charts.aria_category_breakdown') }}" role="img"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function() {
        const $ = s => document.querySelector(s);
        const fmtMoney = v => '$ ' + Number(v || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        // Parámetros
        const p = new URLSearchParams(window.location.search);

        // Chart 1: Trends
        async function loadTrends() {
            const res = await fetch("{{ route('admin.reports.chart.category-trends') }}?" + p.toString());
            const data = await res.json();

            const datasets = Object.entries(data).map(([name, values]) => ({
                label: name,
                data: values.revenue,
                borderWidth: 2,
                fill: false
            }));

            const labels = datasets[0]?.data ? Object.keys(datasets[0].data) : [];

            new Chart($('#chartTrends'), {
                type: 'line',
                data: {
                    labels,
                    datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: (c) => ` ${c.dataset.label}: ${fmtMoney(c.parsed.y)}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                callback: (v) => fmtMoney(v)
                            }
                        }
                    }
                }
            });
        }

        // Chart 2: Breakdown
        async function loadBreakdown() {
            const res = await fetch("{{ route('admin.reports.chart.category-breakdown') }}?" + p.toString());
            const data = await res.json();

            new Chart($('#chartBreakdown'), {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.revenue,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: (c) => ` ${c.label}: ${fmtMoney(c.parsed)}`
                            }
                        }
                    }
                }
            });
        }

        // Export handlers
        $('#export-excel')?.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '{{ route("admin.reports.export.categories.excel") }}?' + p.toString();
        });

        $('#export-csv')?.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '{{ route("admin.reports.export.categories.csv") }}?' + p.toString();
        });

        loadTrends();
        loadBreakdown();
    })();
</script>
@endpush