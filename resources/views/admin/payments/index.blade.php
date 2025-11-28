@extends('adminlte::page')

@section('title', __('m_payments.ui.page_title'))

@section('content_header')
<h1>{{ __('m_payments.ui.page_heading') }}</h1>
@stop

@section('content')
<div class="container-fluid">
    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>${{ number_format($stats['total_amount'], 2) }}</h3>
                    <p>{{ __('m_payments.statistics.total_revenue') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_count'] }}</h3>
                    <p>{{ __('m_payments.statistics.completed_payments') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['pending_count'] }}</h3>
                    <p>{{ __('m_payments.statistics.pending_payments') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['failed_count'] }}</h3>
                    <p>{{ __('m_payments.statistics.failed_payments') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters and Actions --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('m_payments.ui.filters') }}</h3>
            <div class="card-tools">
                <a href="{{ route('admin.payments.export', request()->query()) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel"></i>
                    <span class="d-none d-sm-inline">{{ __('m_payments.buttons.export_csv') }}</span>
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.payments.index') }}" class="filters-form">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label>{{ __('m_payments.filters.search') }}</label>
                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="{{ __('m_payments.filters.search_placeholder') }}"
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-2">
                        <div class="form-group">
                            <label>{{ __('m_payments.filters.status') }}</label>
                            <select name="status" class="form-control">
                                <option value="">{{ __('m_payments.filters.all') }}</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                    {{ __('m_payments.statuses.pending') }}
                                </option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>
                                    {{ __('m_payments.statuses.processing') }}
                                </option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                    {{ __('m_payments.statuses.completed') }}
                                </option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>
                                    {{ __('m_payments.statuses.failed') }}
                                </option>
                                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>
                                    {{ __('m_payments.statuses.refunded') }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-2">
                        <div class="form-group">
                            <label>{{ __('m_payments.filters.gateway') }}</label>
                            <select name="gateway" class="form-control">
                                <option value="">{{ __('m_payments.filters.all') }}</option>
                                <option value="stripe" {{ request('gateway') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                                <option value="tilopay" {{ request('gateway') == 'tilopay' ? 'selected' : '' }}>TiloPay</option>
                                <option value="banco_nacional" {{ request('gateway') == 'banco_nacional' ? 'selected' : '' }}>Banco Nacional</option>
                                <option value="bac" {{ request('gateway') == 'bac' ? 'selected' : '' }}>BAC</option>
                                <option value="bcr" {{ request('gateway') == 'bcr' ? 'selected' : '' }}>BCR</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-2">
                        <div class="form-group">
                            <label>{{ __('m_payments.filters.date_from') }}</label>
                            <input type="date"
                                   name="date_from"
                                   class="form-control"
                                   value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-2">
                        <div class="form-group">
                            <label>{{ __('m_payments.filters.date_to') }}</label>
                            <input type="date"
                                   name="date_to"
                                   class="form-control"
                                   value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-1">
                        <div class="form-group">
                            <label class="d-none d-lg-block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i>
                                <span class="d-inline d-lg-none ml-2">{{ __('m_payments.buttons.search') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Payments Table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('m_payments.ui.payments_list') }}</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped payments-table">
                    <thead>
                        <tr>
                            <th class="text-nowrap">{{ __('m_payments.fields.payment_id') }}</th>
                            <th class="text-nowrap">{{ __('m_payments.fields.booking_ref') }}</th>
                            <th class="text-nowrap d-none d-lg-table-cell">{{ __('m_payments.fields.customer') }}</th>
                            <th class="text-nowrap d-none d-xl-table-cell">{{ __('m_payments.fields.tour') }}</th>
                            <th class="text-nowrap">{{ __('m_payments.fields.amount') }}</th>
                            <th class="text-nowrap d-none d-md-table-cell">{{ __('m_payments.fields.gateway') }}</th>
                            <th class="text-nowrap">{{ __('m_payments.fields.status') }}</th>
                            <th class="text-nowrap d-none d-sm-table-cell">{{ __('m_payments.fields.date') }}</th>
                            <th class="text-nowrap text-center">{{ __('m_payments.ui.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td class="align-middle">
                                <span class="text-monospace">#{{ $payment->payment_id }}</span>
                            </td>
                            <td class="align-middle">
                                @if($payment->booking)
                                    <a href="{{ route('admin.bookings.show', $payment->booking) }}"
                                       class="text-primary font-weight-bold">
                                        {{ $payment->booking->booking_reference }}
                                    </a>
                                @elseif(isset($payment->metadata['deleted_booking_snapshot']))
                                    @php $snapshot = $payment->metadata['deleted_booking_snapshot']; @endphp
                                    <span class="text-danger"
                                          title="{{ __('m_payments.messages.booking_deleted_on') }} {{ $snapshot['deleted_at'] ?? 'N/A' }}">
                                        {{ $snapshot['booking_reference'] ?? 'N/A' }}
                                        <i class="fas fa-trash-alt ml-1"></i>
                                    </span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="align-middle d-none d-lg-table-cell">
                                @if($payment->booking && $payment->booking->user)
                                    <div class="customer-info">
                                        <div class="font-weight-bold text-truncate" style="max-width: 150px;">
                                            {{ $payment->booking->user->full_name }}
                                        </div>
                                        <small class="text-muted text-truncate d-block" style="max-width: 150px;">
                                            {{ $payment->booking->user->email }}
                                        </small>
                                    </div>
                                @elseif(isset($payment->metadata['deleted_booking_snapshot']['user']))
                                    @php $user = $payment->metadata['deleted_booking_snapshot']['user']; @endphp
                                    <div class="customer-info text-danger"
                                         title="{{ __('m_payments.messages.booking_deleted') }}">
                                        <div class="font-weight-bold text-truncate" style="max-width: 150px;">
                                            {{ $user['name'] ?? 'N/A' }}
                                        </div>
                                        <small class="text-muted text-truncate d-block" style="max-width: 150px;">
                                            {{ $user['email'] ?? 'N/A' }}
                                        </small>
                                        <i class="fas fa-trash-alt"></i>
                                    </div>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="align-middle d-none d-xl-table-cell">
                                @if($payment->booking && $payment->booking->tour)
                                    <span class="text-truncate d-inline-block" style="max-width: 200px;"
                                          title="{{ $payment->booking->tour->name }}">
                                        {{ $payment->booking->tour->name }}
                                    </span>
                                @elseif(isset($payment->metadata['deleted_booking_snapshot']['tour']))
                                    @php $tour = $payment->metadata['deleted_booking_snapshot']['tour']; @endphp
                                    <span class="text-danger text-truncate d-inline-block" style="max-width: 200px;"
                                          title="{{ $tour['name'] ?? 'N/A' }} - {{ __('m_payments.messages.booking_deleted') }}">
                                        {{ $tour['name'] ?? 'N/A' }}
                                        <i class="fas fa-trash-alt ml-1"></i>
                                    </span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="amount-cell">
                                    <strong class="d-block">${{ number_format($payment->amount, 2) }}</strong>
                                    <small class="text-muted">{{ strtoupper($payment->currency) }}</small>
                                </div>
                            </td>
                            <td class="align-middle d-none d-md-table-cell">
                                <span class="badge badge-secondary text-nowrap">
                                    {{ ucfirst($payment->gateway) }}
                                </span>
                            </td>
                            <td class="align-middle">
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'completed' => 'success',
                                        'failed' => 'danger',
                                        'refunded' => 'secondary',
                                    ];
                                    $color = $statusColors[$payment->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $color }} text-nowrap">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="align-middle d-none d-sm-table-cell">
                                <div class="date-cell">
                                    <div class="text-nowrap">{{ $payment->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $payment->created_at->format('H:i') }}</small>
                                </div>
                            </td>
                            <td class="align-middle text-center">
                                <a href="{{ route('admin.payments.show', $payment) }}"
                                   class="btn btn-sm btn-info"
                                   title="{{ __('m_payments.buttons.view_details') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <div class="empty-state">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    <p class="mb-0">{{ __('m_payments.messages.no_payments_found') }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($payments->hasPages())
        <div class="card-footer clearfix">
            <div class="float-left">
                <small class="text-muted">
                    {{ __('m_payments.pagination.showing') }}
                    <strong>{{ $payments->firstItem() }}</strong>
                    {{ __('m_payments.pagination.to') }}
                    <strong>{{ $payments->lastItem() }}</strong>
                    {{ __('m_payments.pagination.of') }}
                    <strong>{{ $payments->total() }}</strong>
                    {{ __('m_payments.pagination.results') }}
                </small>
            </div>
            <div class="float-right">
                {{ $payments->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('css')
<style>
/* ============================================
   PAYMENTS TABLE STYLES
   ============================================ */

/* Table base styles */
.payments-table {
    margin-bottom: 0;
    min-width: 100%;
}

.payments-table thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    font-size: 0.875rem;
    padding: 0.75rem;
    white-space: nowrap;
}

.payments-table tbody td {
    padding: 0.75rem;
    vertical-align: middle;
    border-top: 1px solid #dee2e6;
}

/* Text truncation utility */
.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Customer info cell */
.customer-info {
    min-width: 150px;
}

/* Amount cell */
.amount-cell strong {
    font-size: 1rem;
    color: #28a745;
}

/* Date cell */
.date-cell {
    min-width: 100px;
}

/* Empty state */
.empty-state {
    padding: 2rem 0;
}

.empty-state i {
    color: #dee2e6;
}

/* Badge improvements */
.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
    font-weight: 600;
}

/* Button improvements */
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

/* Statistics cards */
.small-box {
    margin-bottom: 1rem;
}

.small-box .inner h3 {
    font-size: 2rem;
    font-weight: 700;
}

/* Filters form */
.filters-form .form-group {
    margin-bottom: 1rem;
}

.filters-form label {
    font-weight: 600;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

/* ============================================
   RESPONSIVE STYLES
   ============================================ */

/* Tablets and below (< 992px) */
@media (max-width: 991.98px) {
    .payments-table thead th {
        font-size: 0.8rem;
        padding: 0.5rem;
    }

    .payments-table tbody td {
        padding: 0.5rem;
        font-size: 0.875rem;
    }

    .customer-info {
        min-width: 120px;
    }

    .amount-cell strong {
        font-size: 0.9rem;
    }

    .small-box .inner h3 {
        font-size: 1.75rem;
    }
}

/* Mobile landscape and below (< 768px) */
@media (max-width: 767.98px) {
    .payments-table thead th {
        font-size: 0.75rem;
        padding: 0.4rem;
    }

    .payments-table tbody td {
        padding: 0.4rem;
        font-size: 0.8rem;
    }

    .badge {
        font-size: 0.65rem;
        padding: 0.25em 0.5em;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .btn-sm i {
        font-size: 0.875rem;
    }

    .small-box .inner h3 {
        font-size: 1.5rem;
    }

    .small-box .inner p {
        font-size: 0.875rem;
    }

    /* Stack filter inputs */
    .filters-form .col-12 {
        margin-bottom: 0.5rem;
    }
}

/* Mobile portrait (< 576px) */
@media (max-width: 575.98px) {
    .payments-table {
        font-size: 0.75rem;
    }

    .payments-table thead th,
    .payments-table tbody td {
        padding: 0.35rem;
    }

    .amount-cell strong {
        font-size: 0.85rem;
    }

    .badge {
        font-size: 0.6rem;
        padding: 0.2em 0.4em;
    }

    .small-box .inner h3 {
        font-size: 1.25rem;
    }

    .small-box .inner p {
        font-size: 0.8rem;
    }

    /* Make action buttons smaller */
    .btn-sm {
        min-width: 32px;
        min-height: 32px;
        padding: 0.25rem;
    }

    /* Improve card spacing */
    .card {
        margin-bottom: 0.75rem;
    }

    .card-header h3 {
        font-size: 1rem;
    }
}

/* Extra small devices (< 400px) */
@media (max-width: 399.98px) {
    .payments-table {
        font-size: 0.7rem;
    }

    .text-monospace {
        font-size: 0.7rem;
    }

    .badge {
        font-size: 0.55rem;
    }
}

/* ============================================
   TOUCH IMPROVEMENTS
   ============================================ */

/* Improve touch targets for mobile devices */
@media (hover: none) and (pointer: coarse) {
    .btn {
        min-width: 44px;
        min-height: 44px;
    }

    .nav-link,
    .page-link {
        padding: 0.75rem 1rem;
    }

    .form-control,
    .custom-select {
        min-height: 44px;
    }
}

/* ============================================
   PRINT STYLES
   ============================================ */

@media print {
    .card-tools,
    .filters-form,
    .btn,
    .pagination {
        display: none !important;
    }

    .payments-table {
        font-size: 10pt;
    }

    .d-none {
        display: table-cell !important;
    }
}

/* ============================================
   DARK MODE SUPPORT (optional)
   ============================================ */

@media (prefers-color-scheme: dark) {
    .payments-table thead th {
        background-color: #343a40;
        color: #fff;
    }

    .payments-table tbody td {
        border-color: #495057;
    }
}
</style>
@endpush

@push('js')
<script>
// Optional: Add JavaScript enhancements
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change (optional)
    const filterSelects = document.querySelectorAll('.filters-form select');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            // Uncomment to enable auto-submit
            // this.closest('form').submit();
        });
    });

    // Add loading state to export button
    const exportBtn = document.querySelector('a[href*="export"]');
    if (exportBtn) {
        exportBtn.addEventListener('click', function(e) {
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-file-excel');
                icon.classList.add('fa-spinner', 'fa-spin');
            }
            this.classList.add('disabled');
        });
    }
});
</script>
@endpush
