@extends('adminlte::page')

@section('title', __('payment.ui.page_title'))

@section('content_header')
<h1>{{ __('payment.ui.page_heading') }}</h1>
@stop

@section('content')
<div class="container-fluid">
    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>${{ number_format($stats['total_amount'], 2) }}</h3>
                    <p>{{ __('payment.statistics.total_revenue') }}</p>
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
                    <p>{{ __('payment.statistics.completed_payments') }}</p>
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
                    <p>{{ __('payment.statistics.pending_payments') }}</p>
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
                    <p>{{ __('payment.statistics.failed_payments') }}</p>
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
            <h3 class="card-title">{{ __('payment.ui.filters') }}</h3>
            <div class="card-tools">
                <a href="{{ route('admin.payments.export', request()->query()) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel"></i>
                    <span class="d-none d-sm-inline">{{ __('payment.buttons.export_csv') }}</span>
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.payments.index') }}" class="filters-form">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label>{{ __('payment.filters.search') }}</label>
                            <input type="text"
                                name="search"
                                class="form-control"
                                placeholder="{{ __('payment.filters.search_placeholder') }}"
                                value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-2">
                        <div class="form-group">
                            <label>{{ __('payment.filters.status') }}</label>
                            <select name="status" class="form-control">
                                <option value="">{{ __('payment.filters.all') }}</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                    {{ __('payment.statuses.pending') }}
                                </option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>
                                    {{ __('payment.statuses.processing') }}
                                </option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                    {{ __('payment.statuses.completed') }}
                                </option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>
                                    {{ __('payment.statuses.failed') }}
                                </option>
                                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>
                                    {{ __('payment.statuses.refunded') }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-2">
                        <div class="form-group">
                            <label>{{ __('payment.filters.gateway') }}</label>
                            <select name="gateway" class="form-control">
                                <option value="">{{ __('payment.filters.all') }}</option>
                                @foreach($enabledGateways as $gateway)
                                <option value="{{ $gateway['id'] }}" {{ request('gateway') == $gateway['id'] ? 'selected' : '' }}>
                                    {{ $gateway['name'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-2">
                        <div class="form-group">
                            <label>{{ __('payment.filters.date_from') }}</label>
                            <input type="date"
                                name="date_from"
                                class="form-control"
                                value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-2">
                        <div class="form-group">
                            <label>{{ __('payment.filters.date_to') }}</label>
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
                                <span class="d-inline d-lg-none ml-2">{{ __('payment.buttons.search') }}</span>
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
            <h3 class="card-title">{{ __('payment.ui.payments_list') }}</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped payments-table">
                    <thead>
                        <tr>
                            <th class="text-nowrap">{{ __('payment.fields.payment_id') }}</th>
                            <th class="text-nowrap">{{ __('payment.fields.booking_ref') }}</th>
                            <th class="text-nowrap d-none d-lg-table-cell">{{ __('payment.fields.customer') }}</th>
                            <th class="text-nowrap d-none d-xl-table-cell">{{ __('payment.fields.tour') }}</th>
                            <th class="text-nowrap">{{ __('payment.fields.amount') }}</th>
                            <th class="text-nowrap d-none d-md-table-cell">{{ __('payment.fields.gateway') }}</th>
                            <th class="text-nowrap">{{ __('payment.fields.status') }}</th>
                            <th class="text-nowrap d-none d-sm-table-cell">{{ __('payment.fields.date') }}</th>
                            <th class="text-nowrap text-center">{{ __('payment.ui.actions') }}</th>
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
                                    title="{{ __('payment.messages.booking_deleted_on') }} {{ $snapshot['deleted_at'] ?? 'N/A' }}">
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
                                    title="{{ __('payment.messages.booking_deleted') }}">
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
                                @if($payment->booking && $payment->booking->product)
                                <span class="text-truncate d-inline-block" style="max-width: 200px;"
                                    title="{{ $payment->booking->product->name }}">
                                    {{ $payment->booking->product->name }}
                                </span>
                                @elseif(isset($payment->metadata['deleted_booking_snapshot']['tour']))
                                @php $product = $payment->metadata['deleted_booking_snapshot']['product']; @endphp
                                <span class="text-danger text-truncate d-inline-block" style="max-width: 200px;"
                                    title="{{ $product['name'] ?? 'N/A' }} - {{ __('payment.messages.booking_deleted') }}">
                                    {{ $product['name'] ?? 'N/A' }}
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
                                    title="{{ __('payment.buttons.view_details') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <div class="empty-state">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    <p class="mb-0">{{ __('payment.messages.no_payments_found') }}</p>
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
                    {{ __('payment.pagination.showing') }}
                    <strong>{{ $payments->firstItem() }}</strong>
                    {{ __('payment.pagination.to') }}
                    <strong>{{ $payments->lastItem() }}</strong>
                    {{ __('payment.pagination.of') }}
                    <strong>{{ $payments->total() }}</strong>
                    {{ __('payment.pagination.results') }}
                </small>
            </div>
            <div class="float-right">
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Page Link --}}
                        @if ($payments->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                        </li>
                        @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $payments->previousPageUrl() }}" rel="prev">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($payments->getUrlRange(1, $payments->lastPage()) as $page => $url)
                        @if ($page == $payments->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                        @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                        @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($payments->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $payments->nextPageUrl() }}" rel="next">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                        @else
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                        </li>
                        @endif
                    </ul>
                </nav>
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

    /* ============================================
   PAGINATION FIXES
   ============================================ */

    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.375rem 0.75rem;
        margin-left: -1px;
        line-height: 1.25;
        color: #007bff;
        background-color: #fff;
        border: 1px solid #dee2e6;
        min-width: 36px;
        min-height: 36px;
        font-size: 0.875rem;
    }

    .pagination .page-link:hover {
        z-index: 2;
        color: #0056b3;
        text-decoration: none;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }

    .pagination .page-item.active .page-link {
        z-index: 3;
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        cursor: auto;
        background-color: #fff;
        border-color: #dee2e6;
    }

    .pagination .page-link i {
        font-size: 0.75rem;
    }

    .pagination .page-item:first-child .page-link {
        border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem;
    }

    .pagination .page-item:last-child .page-link {
        border-top-right-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
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