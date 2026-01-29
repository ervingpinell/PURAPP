@extends('adminlte::page')

@section('title', 'Products Performance')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">🎫 Products Performance</h1>
        <div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Main Dashboard
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.products') }}">
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
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Active Products</div>
                    <div class="h5 mb-0 font-weight-bold">{{ $kpis['total_active_tours'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Products with Bookings</div>
                    <div class="h5 mb-0 font-weight-bold">{{ $kpis['tours_with_bookings'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Most Profitable</div>
                    <div class="h6 mb-0 font-weight-bold text-truncate">{{ $kpis['most_profitable_tour'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Most Booked</div>
                    <div class="h6 mb-0 font-weight-bold text-truncate">{{ $kpis['most_booked_tour'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 10 Products by Revenue</h6>
                </div>
                <div class="card-body">
                    <canvas id="topRevenueChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 10 Products by Bookings</h6>
                </div>
                <div class="card-body">
                    <canvas id="topBookingsChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bookings by Product Type</h6>
                </div>
                <div class="card-body">
                    <canvas id="productTypeChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Products Details</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-end">Revenue</th>
                                    <th class="text-end">Bookings</th>
                                    <th class="text-end">PAX</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProductsByRevenue->take(10) as $product)
                                <tr>
                                    <td class="text-truncate" style="max-width: 200px;">{{ $product->product_name }}</td>
                                    <td class="text-end">${{ number_format($product->revenue, 2) }}</td>
                                    <td class="text-end">{{ $product->bookings }}</td>
                                    <td class="text-end">{{ $product->pax ?? 0 }}</td>
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

        // Top Products by Revenue
        fetch(`{{ route('admin.reports.products.chart.top-revenue') }}?${filters}&limit=10`)
            .then(res => res.json())
            .then(data => {
                new Chart(document.getElementById('topRevenueChart'), {
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
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) => `Revenue: $${context.parsed.x.toLocaleString()}`
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    callback: (value) => '$' + value.toLocaleString()
                                }
                            }
                        }
                    }
                });
            });

        // Top Products by Bookings
        fetch(`{{ route('admin.reports.products.chart.top-bookings') }}?${filters}&limit=10`)
            .then(res => res.json())
            .then(data => {
                new Chart(document.getElementById('topBookingsChart'), {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Bookings',
                            data: data.bookings,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgb(54, 162, 235)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });

        // Product Type Chart
        fetch(`{{ route('admin.reports.products.chart.product-type') }}?${filters}`)
            .then(res => res.json())
            .then(data => {
                new Chart(document.getElementById('productTypeChart'), {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.bookings,
                            backgroundColor: [
                                'rgb(255, 99, 132)',
                                'rgb(54, 162, 235)',
                                'rgb(255, 205, 86)',
                                'rgb(75, 192, 192)',
                                'rgb(153, 102, 255)',
                                'rgb(255, 159, 64)'
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