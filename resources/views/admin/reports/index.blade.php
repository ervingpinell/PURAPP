@extends('adminlte::page')

@section('title', __('reports.title'))

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
    margin-bottom: .25rem
  }

  .actions-bar {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap
  }

  .kpi-card {
    display: flex;
    gap: .75rem;
    padding: 1rem;
    background: var(--card-bg);
    border: 1px solid var(--border-soft);
    border-radius: 12px;
    height: 100%
  }

  .kpi-icon {
    font-size: 1.05rem;
    opacity: .85
  }

  .kpi-label {
    font-size: .85rem;
    color: var(--text-muted)
  }

  .kpi-value {
    font-size: 1.45rem;
    font-weight: 800;
    line-height: 1.1
  }

  .table td,
  .table th {
    vertical-align: middle
  }

  .table thead th {
    border-bottom: 1px solid var(--border-soft)
  }

  .table tbody tr+tr td {
    border-top: 1px solid var(--border-soft)
  }

  .section-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem
  }

  .chip {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .15rem .5rem;
    border-radius: 999px;
    font-size: .8rem;
    border: 1px solid var(--border-soft);
    background: var(--card-bg)
  }

  .chart-wrap {
    position: relative;
    width: 100%
  }

  /* Report Type Cards */
  .report-type-card {
    position: relative;
    display: flex;
    gap: 1rem;
    padding: 1.25rem;
    background: var(--card-bg);
    border: 2px solid var(--border-soft);
    border-radius: 12px;
    height: 100%;
    transition: all 0.3s ease;
  }

  .report-type-card.clickable {
    cursor: pointer;
  }

  .report-type-card.clickable:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, .12);
    border-color: #4e73df;
  }

  .report-type-card.active {
    border-color: #1cc88a;
    background: linear-gradient(135deg, rgba(28, 200, 138, 0.05) 0%, rgba(28, 200, 138, 0.02) 100%);
  }

  .report-type-card.disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  .report-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    font-size: 1.5rem;
    flex-shrink: 0;
  }

  .report-icon.bg-gradient-primary {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
  }

  .report-icon.bg-secondary {
    background: linear-gradient(135deg, #858796 0%, #60616f 100%);
  }

  .report-content {
    flex: 1;
  }

  .report-title {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: inherit;
  }

  .report-desc {
    font-size: 0.875rem;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
  }

  .report-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    background: rgba(28, 200, 138, 0.1);
    color: #1cc88a;
  }

  .report-badge.badge-new {
    background: linear-gradient(135deg, #f6c23e 0%, #f4b619 100%);
    color: #fff;
  }

  .report-badge.badge-soon {
    background: rgba(133, 135, 150, 0.1);
    color: #858796;
  }

  .report-arrow {
    display: flex;
    align-items: center;
    font-size: 1.25rem;
    color: var(--text-muted);
    opacity: 0;
    transform: translateX(-10px);
    transition: all 0.3s ease;
  }

  .report-type-card.clickable:hover .report-arrow {
    opacity: 1;
    transform: translateX(0);
  }

  /* Filter Actions Buttons */
  .filter-actions {
    display: flex;
    gap: .5rem;
    align-items: center;
  }

  .filter-actions .btn {
    white-space: nowrap;
    min-width: auto;
  }

  /* Multi-select controls */
  select[multiple] {
    max-height: 150px;
    overflow-y: auto;
  }

  @media (max-width: 767.98px) {
    select[multiple] {
      max-height: 120px;
      font-size: .9rem;
    }
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
      gap: .5rem;
      min-height: 70px
    }

    .kpi-value {
      font-size: 1.25rem
    }

    .kpi-label {
      font-size: .8rem
    }

    .kpi-icon {
      font-size: .95rem
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

    /* Report Type Cards - Mobile */
    .report-type-card {
      padding: 1rem;
      gap: .75rem
    }

    .report-icon {
      width: 40px;
      height: 40px;
      font-size: 1.25rem
    }

    .report-title {
      font-size: 1rem
    }

    .report-desc {
      font-size: .8rem
    }

    .report-arrow {
      display: none
    }

    /* Filter adjustments */
    .filter-label {
      font-size: .8rem
    }

    .form-control,
    .form-select {
      font-size: .9rem
    }

    /* Actions bar */
    .filter-actions {
      flex-direction: column;
      width: 100%;
    }

    .filter-actions .btn {
      width: 100%
    }

    /* Section titles */
    .section-title {
      flex-direction: column;
      align-items: flex-start
    }

    .section-title h5 {
      font-size: 1rem
    }
  }

  /* Small Devices (Tablets Portrait) */
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

    .report-type-card {
      padding: 1.15rem
    }

    .report-icon {
      width: 44px;
      height: 44px;
      font-size: 1.35rem
    }

    .filter-actions {
      justify-content: flex-start;
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

    .report-type-card {
      padding: 1.2rem
    }

    .filter-actions {
      justify-content: flex-start;
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

    .filter-actions {
      justify-content: flex-end;
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

  /* Very Large Screens */
  @media (min-width:1400px) {
    .container-fluid {
      max-width: 1600px;
      margin: 0 auto;
    }

    .h-chart-1 {
      height: 450px
    }

    .h-chart-2 {
      height: 350px
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
    .dropdown,
    .actions-bar,
    .filter-actions {
      display: none
    }

    .h-chart-1,
    .h-chart-2 {
      height: 300px !important
    }

    .report-type-card {
      break-inside: avoid
    }
  }
</style>
@endpush

@section('content_header')
<h1 class="mb-2">{{ __('reports.header') }}</h1>
@stop

@section('content')
<div class="container-fluid">

  {{-- ===== FILTROS ===== --}}
  <div class="card-elevated p-3 mb-3">
    <form method="GET" id="filtersForm">
      <div class="row g-2 align-items-end">
        {{-- Rango rápido --}}
        <div class="col-12 col-sm-6 col-lg-3 col-xl-2">
          <label class="filter-label">{{ __('reports.filters.quick_range') }}</label>
          @php $preset = request('preset'); @endphp
          <select name="preset" id="preset" class="form-control">
            <option value="">{{ __('reports.filters.select_placeholder') }}</option>
            <option value="today" @selected($preset==='today' )>{{ __('reports.filters.today') }}</option>
            <option value="last7" @selected($preset==='last7' )>{{ __('reports.filters.last7') }}</option>
            <option value="this_week" @selected($preset==='this_week' )>{{ __('reports.filters.this_week') }}</option>
            <option value="this_month" @selected($preset==='this_month' )>{{ __('reports.filters.this_month') }}</option>
            <option value="last_month" @selected($preset==='last_month' )>{{ __('reports.filters.last_month') }}</option>
            <option value="this_year" @selected($preset==='this_year' )>{{ __('reports.filters.this_year') }}</option>
            <option value="last_year" @selected($preset==='last_year' )>{{ __('reports.filters.last_year') }}</option>
          </select>
        </div>

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
        <div class="col-6 col-sm-3 col-lg-2 col-xl-auto">
          <label class="filter-label">{{ __('reports.filters.period') }}</label>
          @php $periodSel = request('period', $period ?? 'month'); @endphp
          <select name="period" id="period" class="form-control">
            <option value="day" @selected($periodSel==='day' )>{{ __('reports.filters.period_day') }}</option>
            <option value="week" @selected($periodSel==='week' )>{{ __('reports.filters.period_week') }}</option>
            <option value="month" @selected($periodSel==='month' )>{{ __('reports.filters.period_month') }}</option>
          </select>
        </div>

        <div class="col-6 col-sm-3 col-lg-2 col-xl-auto">
          <label class="filter-label">{{ __('reports.filters.group_by') }}</label>
          @php $gb = request('group_by', $groupBy ?? 'booking_date'); @endphp
          <select name="group_by" class="form-control">
            <option value="booking_date" @selected($gb==='booking_date' )>{{ __('reports.filters.group_booking_date') }}</option>
            <option value="tour_date" @selected($gb==='tour_date' )>{{ __('reports.filters.group_tour_date') }}</option>
          </select>
        </div>

        {{-- Acciones --}}
        <div class="col-12 col-sm-6 col-lg-auto ms-lg-auto">
          <div class="filter-actions">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
              <i class="fas fa-redo d-lg-none"></i>
              <span class="d-none d-lg-inline">{{ __('reports.filters.reset') }}</span>
            </a>
            <button class="btn btn-primary">
              <i class="fas fa-filter d-lg-none"></i>
              <span class="d-none d-lg-inline">{{ __('reports.filters.apply') }}</span>
            </button>
          </div>
        </div>

        {{-- Toggle Más filtros (mobile) --}}
        <div class="col-12 d-lg-none mt-2">
          <a class="text-decoration-none" data-bs-toggle="collapse" href="#moreFilters" role="button" aria-expanded="false" aria-controls="moreFilters">
            <i class="fas fa-filter me-1"></i> {{ __('reports.filters.more_filters') }}
          </a>
        </div>
      </div>

      {{-- Más filtros --}}
      <div class="collapse d-lg-block mt-2" id="moreFilters">
        <div class="row g-2">
          <div class="col-12 col-md-6 col-lg-3 col-xl-2">
            <label class="filter-label">{{ __('reports.filters.status') }}</label>
            <select name="status" class="form-control">
              <option value="">{{ __('reports.filters.all') }}</option>
              @foreach(['paid','confirmed','completed','cancelled'] as $st)
              <option value="{{ $st }}" @selected($status===$st)>{{ __('reports.status_options.'.$st) }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-6 col-lg-4 col-xl-5">
            <label class="filter-label">{{ __('reports.filters.tours_multi') }}</label>
            @php $tSel = collect((array)request('product_id', []))->map(fn($v)=>(int)$v)->all(); @endphp
            <select name="product_id[]" class="form-control" multiple size="4">
              @foreach($toursMap as $tid => $tname)
              <option value="{{ $tid }}" @selected(in_array((int)$tid, $tSel))>{{ $tname }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-12 col-lg-5">
            <label class="filter-label">{{ __('reports.filters.languages_multi') }}</label>
            @php $lSel = collect((array)request('tour_language_id', []))->map(fn($v)=>(int)$v)->all(); @endphp
            <select name="tour_language_id[]" class="form-control" multiple size="4">
              @foreach($langsMap as $lid => $lname)
              <option value="{{ $lid }}" @selected(in_array((int)$lid, $lSel))>{{ $lname }}</option>
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
          <div class="kpi-label">{{ __('reports.kpi.revenue_range') }}</div>
          <div class="kpi-value">$ {{ number_format($kpis['revenue'], 2) }}</div>
          <div class="kpi-label">{{ __('reports.kpi.avg_ticket') }}: $ {{ number_format($kpis['atv'], 2) }}</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
      <div class="kpi-card">
        <div class="kpi-icon mt-1"><i class="fas fa-receipt"></i></div>
        <div>
          <div class="kpi-label">{{ __('reports.kpi.bookings') }}</div>
          <div class="kpi-value">{{ $kpis['bookings'] }}</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
      <div class="kpi-card">
        <div class="kpi-icon mt-1"><i class="fas fa-users"></i></div>
        <div>
          <div class="kpi-label">{{ __('reports.kpi.pax') }}</div>
          <div class="kpi-value">{{ $kpis['pax'] }}</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
      <div class="kpi-card">
        <div class="kpi-icon mt-1"><i class="fas fa-check-circle"></i></div>
        <div>
          <div class="kpi-label">{{ __('reports.kpi.confirmed_bookings') }}</div>
          <div class="kpi-value">{{ $confirmedBookings }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- ===== REPORT TYPES CARDS ===== --}}
  <div class="card-elevated p-3 mb-3">
    <h5 class="mb-3"><i class="fas fa-chart-bar me-2"></i>📊 Report Dashboards</h5>
    <div class="row g-3">
      {{-- Overview Report --}}
      <div class="col-12 col-md-6 col-xl-4">
        <div class="report-type-card active">
          <div class="report-icon">
            <i class="fas fa-chart-line"></i>
          </div>
          <div class="report-content">
            <h6 class="report-title">Overview Report</h6>
            <p class="report-desc">Revenue trends, top tours, and language breakdown</p>
            <div class="report-badge">
              <i class="fas fa-check-circle text-success me-1"></i> Current View
            </div>
          </div>
        </div>
      </div>

      {{-- Sales Analytics --}}
      <div class="col-12 col-md-6 col-xl-4">
        <a href="{{ route('admin.reports.sales', request()->all()) }}" class="text-decoration-none">
          <div class="report-type-card clickable">
            <div class="report-icon bg-gradient-primary">
              <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="report-content">
              <h6 class="report-title">Sales Analytics</h6>
              <p class="report-desc">Revenue by payment method, language, and trends</p>
              <div class="report-badge badge-new">
                <i class="fas fa-star me-1"></i> New!
              </div>
            </div>
            <div class="report-arrow">
              <i class="fas fa-arrow-right"></i>
            </div>
          </div>
        </a>
      </div>

      {{-- Tours Performance --}}
      <div class="col-12 col-md-6 col-xl-4">
        <a href="{{ route('admin.reports.products', request()->all()) }}" class="text-decoration-none">
          <div class="report-type-card clickable">
            <div class="report-icon" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);">
              <i class="fas fa-route"></i>
            </div>
            <div class="report-content">
              <h6 class="report-title">Tours Performance</h6>
              <p class="report-desc">Top tours, capacity utilization, and tour types</p>
              <div class="report-badge badge-new">
                <i class="fas fa-star me-1"></i> New!
              </div>
            </div>
            <div class="report-arrow">
              <i class="fas fa-arrow-right"></i>
            </div>
          </div>
        </a>
      </div>

      {{-- Customer Analytics --}}
      <div class="col-12 col-md-6 col-xl-4">
        <a href="{{ route('admin.reports.customers', request()->all()) }}" class="text-decoration-none">
          <div class="report-type-card clickable">
            <div class="report-icon" style="background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);">
              <i class="fas fa-users"></i>
            </div>
            <div class="report-content">
              <h6 class="report-title">Customer Analytics</h6>
              <p class="report-desc">Geographic distribution, CLV, and customer growth</p>
              <div class="report-badge badge-new">
                <i class="fas fa-star me-1"></i> New!
              </div>
            </div>
            <div class="report-arrow">
              <i class="fas fa-arrow-right"></i>
            </div>
          </div>
        </a>
      </div>

      {{-- Time Analysis --}}
      <div class="col-12 col-md-6 col-xl-4">
        <a href="{{ route('admin.reports.time-analysis', request()->all()) }}" class="text-decoration-none">
          <div class="report-type-card clickable">
            <div class="report-icon" style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);">
              <i class="fas fa-clock"></i>
            </div>
            <div class="report-content">
              <h6 class="report-title">Time Analysis</h6>
              <p class="report-desc">Peak hours, day patterns, and seasonality trends</p>
              <div class="report-badge badge-new">
                <i class="fas fa-star me-1"></i> New!
              </div>
            </div>
            <div class="report-arrow">
              <i class="fas fa-arrow-right"></i>
            </div>
          </div>
        </a>
      </div>

      {{-- Category Report --}}
      <div class="col-12 col-md-6 col-xl-4">
        <a href="{{ route('admin.reports.by-category', request()->all()) }}" class="text-decoration-none">
          <div class="report-type-card clickable">
            <div class="report-icon" style="background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);">
              <i class="fas fa-chart-pie"></i>
            </div>
            <div class="report-content">
              <h6 class="report-title">Category Reports</h6>
              <p class="report-desc">Detailed breakdown by customer category with trends</p>
              <div class="report-badge badge-new">
                <i class="fas fa-star me-1"></i> New!
              </div>
            </div>
            <div class="report-arrow">
              <i class="fas fa-arrow-right"></i>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>

  {{-- ===== Contenido ===== --}}
  <div class="row g-3">
    <div class="col-12 col-xl-8">
      <div class="card-elevated p-3">
        <div class="section-title">
          <h5 class="mb-0">
            {{ __('reports.sections.revenue_by_period_title', [
              'period' => __('reports.sections.period_names.' . request('period','month'))
            ]) }}
          </h5>
          <button id="exportMonthlyCsv" type="button" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-file-csv me-1"></i>
            <span class="d-none d-sm-inline">{{ __('reports.buttons.csv') }}</span>
          </button>
        </div>
        <div class="chart-wrap h-chart-1">
          <canvas id="chartMonthly" aria-label="{{ __('reports.charts.aria_revenue_by_period') }}" role="img"></canvas>
        </div>
      </div>

      <div class="card-elevated mt-3">
        <div class="d-flex align-items-center justify-content-between px-3 pt-3">
          <h5 class="mb-0">{{ __('reports.sections.top_tours_title') }}</h5>
          <button id="exportTopCsv" type="button" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-file-csv me-1"></i>
            <span class="d-none d-sm-inline">{{ __('reports.buttons.csv') }}</span>
          </button>
        </div>
        <div class="p-0">
          <div class="table-responsive">
            <table class="table mb-0">
              <thead>
                <tr>
                  <th style="width:60px">{{ __('reports.table.hash') }}</th>
                  <th>{{ __('reports.table.tour') }}</th>
                  <th style="width:140px">{{ __('reports.table.bookings') }}</th>
                  <th style="width:120px">{{ __('reports.table.pax') }}</th>
                  <th class="text-end" style="width:180px">{{ __('reports.table.revenue') }}</th>
                </tr>
              </thead>
              <tbody id="topToursTbody">
                @forelse($topTours as $i => $row)
                <tr>
                  <td>{{ $i+1 }}</td>
                  <td>{{ $toursMap[$row->product_id] ?? ('#'.$row->product_id) }}</td>
                  <td>{{ $row->bookings }}</td>
                  <td>{{ $row->pax }}</td>
                  <td class="text-end">$ {{ number_format($row->revenue, 2) }}</td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center p-4">{{ __('reports.table.no_data') }}</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-4">
      <div class="card-elevated p-3 mb-3">
        <div class="section-title">
          <h5 class="mb-0">{{ __('reports.sections.sales_by_language') }}</h5>
          <button id="exportLangCsv" type="button" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-file-csv"></i>
            <span class="d-none d-sm-inline">{{ __('reports.buttons.csv') }}</span>
          </button>
        </div>
        <div class="chart-wrap h-chart-2">
          <canvas id="chartLanguage" aria-label="{{ __('reports.charts.aria_sales_by_language') }}" role="img"></canvas>
        </div>
      </div>

      <div class="card-elevated p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="mb-0">{{ __('reports.sections.pending_bookings') }}</h5>
          <span class="chip"><i class="fas fa-clock"></i> {{ $pendingCount }}</span>
        </div>
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead>
              <tr>
                <th>{{ __('reports.table.ref') }}</th>
                <th>{{ __('reports.table.customer') }}</th>
                <th>{{ __('reports.table.tour') }}</th>
                <th>
                  {{ ($gb==='tour_date') ? __('reports.table.tour_date') : __('reports.table.booking_date') }}
                </th>
                <th class="text-end">{{ __('reports.table.total') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($pendingItems as $p)
              <tr>
                <td>{{ $p->booking_reference }}</td>
                <td>{{ $p->customer_email ?? '—' }}</td>
                <td>{{ $p->tour_name ?? '—' }}</td>
                <td>
                  @if($gb==='tour_date')
                  {{ $p->tour_date ? \Carbon\Carbon::parse($p->tour_date)->toDateString() : '—' }}
                  @else
                  {{ $p->booking_date ? \Carbon\Carbon::parse($p->booking_date)->toDateString() : '—' }}
                  @endif
                </td>
                <td class="text-end">$ {{ number_format($p->total ?? 0, 2) }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-3">{{ __('reports.table.none_pending') }}</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="small text-muted mt-2">{{ __('reports.footnotes.pending_limit') }}</div>
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
    const fmtMoney = v => '$ ' + Number(v || 0).toLocaleString('es-CR', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });

    // Preset -> rellena fechas (cliente)
    const preset = $('#preset'),
      $from = $('#from'),
      $to = $('#to');
    const pad = n => String(n).padStart(2, '0');
    const iso = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
    const startOfWeek = d => {
      const t = new Date(d);
      const day = (t.getDay() + 6) % 7;
      t.setDate(t.getDate() - day);
      t.setHours(0, 0, 0, 0);
      return t;
    }
    const endOfWeek = d => {
      const s = startOfWeek(d);
      const e = new Date(s);
      e.setDate(s.getDate() + 6);
      e.setHours(23, 59, 59, 999);
      return e;
    }
    const startOfMonth = d => new Date(d.getFullYear(), d.getMonth(), 1);
    const endOfMonth = d => new Date(d.getFullYear(), d.getMonth() + 1, 0);
    preset?.addEventListener('change', () => {
      if (!preset.value) return;
      const today = new Date();
      let a = today,
        b = today;
      switch (preset.value) {
        case 'today':
          a = new Date(today);
          b = new Date(today);
          break;
        case 'last7':
          a = new Date(today);
          a.setDate(a.getDate() - 6);
          b = new Date(today);
          break;
        case 'this_week':
          a = startOfWeek(today);
          b = endOfWeek(today);
          break;
        case 'this_month':
          a = startOfMonth(today);
          b = endOfMonth(today);
          break;
        case 'last_month':
          const lm = new Date(today.getFullYear(), today.getMonth() - 1, 1);
          a = startOfMonth(lm);
          b = endOfMonth(lm);
          break;
        case 'this_year':
          a = new Date(today.getFullYear(), 0, 1);
          b = new Date(today.getFullYear(), 11, 31);
          break;
        case 'last_year':
          a = new Date(today.getFullYear() - 1, 0, 1);
          b = new Date(today.getFullYear() - 1, 11, 30);
          break;
      }
      $from.value = iso(a);
      $to.value = iso(b);
    });

    // Parámetros
    const p = new URLSearchParams({
      from: '{{ $from->toDateString() }}',
      to: '{{ $to->toDateString() }}',
      status: '{{ $status }}',
      group_by: '{{ request('
      group_by ', $groupBy ?? "booking_date") }}',
      period: '{{ request('
      period ', $period ?? "month") }}',
    });
    @php $tSel = collect((array) request('product_id', [])) -> map(fn($v) => (int) $v) -> all();
    @endphp
    @php $lSel = collect((array) request('tour_language_id', [])) -> map(fn($v) => (int) $v) -> all();
    @endphp
      ({
        !!json_encode($tSel) !!
      }).forEach(v => p.append('product_id[]', v));
    ({
      !!json_encode($lSel) !!
    }).forEach(v => p.append('tour_language_id[]', v));

    // Textos traducidos usados en JS
    const i18n = {
      revenue: "{{ __('reports.table.revenue') }}",
      tooltipRevenue: "{{ __('reports.charts.tooltip_revenue') }}",
      langLabel: "{{ __('reports.csv.language') }}",
      periodLabel: "{{ __('reports.csv.period') }}",
      csv: {
        headersRevenue: @json(trans('reports.csv.headers_revenue')),
        headersTop: @json(trans('reports.csv.headers_top')),
        headersLanguage: @json(trans('reports.csv.headers_language')),
        fileRevenue: "{{ __('reports.csv.revenue_by_period_filename') }}",
        fileTop: "{{ __('reports.csv.top_tours_filename') }}",
        fileLang: "{{ __('reports.csv.sales_by_language_filename') }}",
      }
    };

    // Chart 1
    async function loadMonthly() {
      const res = await fetch("{{ route('admin.reports.chart.monthly') }}?" + p.toString());
      const m = await res.json();
      new Chart($('#chartMonthly'), {
        type: 'bar',
        data: {
          labels: m.labels,
          datasets: [{
            label: i18n.revenue,
            data: m.series.revenue,
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true
            },
            tooltip: {
              callbacks: {
                label: (c) => ` ${i18n.tooltipRevenue}: ${fmtMoney(c.parsed.y)}`
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

    // Chart 2
    async function loadByLanguage() {
      const res = await fetch("{{ route('admin.reports.chart.language') }}?" + p.toString());
      const l = await res.json();
      const langsMap = @json($langsMap);
      new Chart($('#chartLanguage'), {
        type: 'bar',
        data: {
          labels: l.labels.map(id => langsMap[id] ?? ('#' + id)),
          datasets: [{
            label: i18n.revenue,
            data: l.series.revenue,
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true
            },
            tooltip: {
              callbacks: {
                label: (c) => ` ${i18n.tooltipRevenue}: ${fmtMoney(c.parsed.y)}`
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

    // CSV helpers
    const dl = (name, rows) => {
      const esc = v => v == null ? '' : /[",;\n]/.test(String(v)) ? `"${String(v).replace(/"/g,'""')}"` : v;
      const csv = rows.map(r => r.map(esc).join(';')).join('\n');
      const b = new Blob([csv], {
        type: 'text/csv;charset=utf-8;'
      });
      const u = URL.createObjectURL(b);
      const a = document.createElement('a');
      a.href = u;
      a.download = name;
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(u);
    };

    document.getElementById('exportMonthlyCsv')?.addEventListener('click', async (e) => {
      e.preventDefault();
      const r = await fetch("{{ route('admin.reports.chart.monthly') }}?" + p.toString());
      const m = await r.json();
      const rows = [i18n.csv.headersRevenue];
      m.labels.forEach((lbl, i) => rows.push([lbl, m.series.revenue[i] ?? 0, m.series.bookings[i] ?? 0, m.series.pax[i] ?? 0]));
      dl(`${i18n.csv.fileRevenue}_${new Date().toISOString().slice(0,10)}.csv`, rows);
    });

    document.getElementById('exportTopCsv')?.addEventListener('click', (e) => {
      e.preventDefault();
      const rows = [i18n.csv.headersTop];
      document.querySelectorAll('#topToursTbody tr').forEach(tr => {
        const t = [...tr.querySelectorAll('td')].map(td => td.innerText.trim());
        if (t.length === 5) rows.push(t);
      });
      dl(`${i18n.csv.fileTop}_${new Date().toISOString().slice(0,10)}.csv`, rows);
    });

    document.getElementById('exportLangCsv')?.addEventListener('click', async (e) => {
      e.preventDefault();
      const r = await fetch("{{ route('admin.reports.chart.language') }}?" + p.toString());
      const l = await r.json();
      const map = @json($langsMap);
      const rows = [i18n.csv.headersLanguage];
      l.labels.forEach((id, i) => rows.push([map[id] ?? ('#' + id), l.series.revenue[i] ?? 0, l.series.bookings[i] ?? 0]));
      dl(`${i18n.csv.fileLang}_${new Date().toISOString().slice(0,10)}.csv`, rows);
    });

    loadMonthly();
    loadByLanguage();
  })();
</script>
@endpush
