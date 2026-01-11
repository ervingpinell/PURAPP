@extends('adminlte::page')

@section('title', 'Time Analysis')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">⏰ Time Analysis & Patterns</h1>
        <div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Main Dashboard
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.time-analysis') }}">
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
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Busiest Day</div>
                    <div class="h5 mb-0 font-weight-bold">{{ $kpis['busiest_day'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Peak Hour</div>
                    <div class="h5 mb-0 font-weight-bold">{{ $kpis['peak_hour'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Peak Month</div>
                    <div class="h5 mb-0 font-weight-bold">{{ $kpis['peak_month'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Bookings</div>
                    <div class="h5 mb-0 font-weight-bold">{{ number_format($kpis['total_bookings']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bookings by Day of Week</h6>
                </div>
                <div class="card-body">
                    <canvas id="dayOfWeekChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bookings by Hour of Day</h6>
                </div>
                <div class="card-body">
                    <canvas id="hourChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Seasonality</h6>
                </div>
                <div class="card-body">
                    <canvas id="seasonalityChart" height="60"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Heatmap -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Booking Heatmap (Day vs Hour)</h6>
                    <small class="text-muted">Darker = More bookings</small>
                </div>
                <div class="card-body">
                    <div id="heatmapContainer" style="min-height: 400px;">
                        <p class="text-center text-muted">Loading heatmap...</p>
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

        // Day of Week Chart
        fetch(`{{ route('admin.reports.time-analysis.chart.day-of-week') }}?${filters}`)
            .then(res => res.json())
            .then(data => {
                new Chart(document.getElementById('dayOfWeekChart'), {
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
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
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

        // Hour Chart
        fetch(`{{ route('admin.reports.time-analysis.chart.hour') }}?${filters}`)
            .then(res => res.json())
            .then(data => {
                new Chart(document.getElementById('hourChart'), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Bookings',
                            data: data.bookings,
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
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

        // Seasonality Chart
        fetch(`{{ route('admin.reports.time-analysis.chart.seasonality') }}?${filters}`)
            .then(res => res.json())
            .then(data => {
                new Chart(document.getElementById('seasonalityChart'), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Bookings',
                            data: data.bookings,
                            borderColor: 'rgb(255, 99, 132)',
                            backgroundColor: 'rgba(255, 99, 132, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: 'Revenue',
                            data: data.revenue,
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: true
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                beginAtZero: true
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                beginAtZero: true,
                                grid: {
                                    drawOnChartArea: false
                                },
                                ticks: {
                                    callback: (value) => '$' + value.toLocaleString()
                                }
                            }
                        }
                    }
                });
            });

        // Heatmap
        fetch(`{{ route('admin.reports.time-analysis.chart.heatmap') }}?${filters}`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('heatmapContainer');

                // Create simple HTML heatmap
                let html = '<div class="table-responsive"><table class="table table-bordered table-sm" style="font-size: 0.85rem;">';
                html += '<thead><tr><th>Day / Hour</th>';

                // Hour headers (show every 2 hours)
                for (let h = 0; h < 24; h += 2) {
                    html += `<th class="text-center" colspan="2">${h}:00</th>`;
                }
                html += '</tr></thead><tbody>';

                // Find max value for color scaling
                const maxValue = Math.max(...data.data.flat());

                // Rows for each day
                data.days.forEach((day, dayIdx) => {
                    html += `<tr><th>${day}</th>`;
                    data.data[dayIdx].forEach((value) => {
                        const intensity = maxValue > 0 ? (value / maxValue) : 0;
                        const bgColor = `rgba(75, 192, 192, ${intensity})`;
                        const textColor = intensity > 0.5 ? 'white' : 'black';
                        html += `<td class="text-center" style="background-color: ${bgColor}; color: ${textColor}; min-width: 30px;">${value || ''}</td>`;
                    });
                    html += '</tr>';
                });

                html += '</tbody></table></div>';
                container.innerHTML = html;
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