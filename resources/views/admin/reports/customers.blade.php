@extends('adminlte::page')

@section('title', 'Customer Analytics')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">👥 Customer Analytics</h1>
        <div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Main Dashboard
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.customers') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from" class="form-control" value="{{ $from->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to" class="form-control" value="{{ $to->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Customers</div>
                    <div class="h5 mb-0 font-weight-bold">{{ number_format($kpis['total_customers']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">New Customers</div>
                    <div class="h5 mb-0 font-weight-bold">{{ number_format($kpis['new_customers']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Avg CLV</div>
                    <div class="h5 mb-0 font-weight-bold">${{ number_format($kpis['avg_clv'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Repeat Rate</div>
                    <div class="h5 mb-0 font-weight-bold">{{ number_format($kpis['repeat_rate'], 1) }}%</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Countries by Revenue</h6>
                </div>
                <div class="card-body">
                    <canvas id="geographicChart" height="80"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">New vs Returning</h6>
                </div>
                <div class="card-body">
                    <canvas id="newVsReturningChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Growth Trend</h6>
                </div>
                <div class="card-body">
                    <canvas id="growthChart" height="60"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Countries Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Countries Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th class="text-end">Revenue</th>
                                    <th class="text-end">Bookings</th>
                                    <th class="text-end">Avg per Booking</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCountries as $country)
                                <tr>
                                    <td>{{ $country->billing_country }}</td>
                                    <td class="text-end">${{ number_format($country->revenue, 2) }}</td>
                                    <td class="text-end">{{ number_format($country->bookings) }}</td>
                                    <td class="text-end">${{ number_format($country->revenue / max(1, $country->bookings), 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filters = new URLSearchParams(window.location.search);

        // Geographic Distribution
        fetch(`{{ route('admin.reports.customers.chart.geographic') }}?${filters}&limit=15`)
            .then(res => res.json())
            .then(data => {
                new Chart(document.getElementById('geographicChart'), {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Revenue',
                            data: data.revenue,
                            backgroundColor: 'rgba(75, 192, 192, 0.5)',
                            borderColor: 'rgb(75, 192, 192)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) => `Revenue: $${context.parsed.y.toLocaleString()}`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: (value) => '$' + value.toLocaleString()
                                }
                            }
                        }
                    }
                });
            });

        // New vs Returning
        fetch(`{{ route('admin.reports.customers.chart.new-vs-returning') }}?${filters}`)
            .then(res => res.json())
            .then(data => {
                new Chart(document.getElementById('newVsReturningChart'), {
                    type: 'pie',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.values,
                            backgroundColor: [
                                'rgb(54, 162, 235)',
                                'rgb(255, 205, 86)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            });

        // Customer Growth
        fetch(`{{ route('admin.reports.customers.chart.growth') }}?${filters}&period=month`)
            .then(res => res.json())
            .then(data => {
                new Chart(document.getElementById('growthChart'), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'New Customers',
                            data: data.new_customers,
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                            tension: 0.4
                        }, {
                            label: 'Total Customers',
                            data: data.total_customers,
                            borderColor: 'rgb(54, 162, 235)',
                            backgroundColor: 'rgba(54, 162, 235, 0.1)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
    });
</script>
@endpush

<style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }

    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }

    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }

    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
</style>
@endsection