@extends('adminlte::page')

@section('title', __('m_payments.ui.page_title'))

@section('content_header')
<h1>{{ __('m_payments.ui.page_heading') }}</h1>
@stop

@section('content')
<div class="container-fluid">
    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
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
        <div class="col-md-3">
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
        <div class="col-md-3">
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
        <div class="col-md-3">
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
                    <i class="fas fa-file-excel"></i> {{ __('m_payments.buttons.export_csv') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.payments.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('m_payments.filters.search') }}</label>
                            <input type="text" name="search" class="form-control" placeholder="{{ __('m_payments.filters.search_placeholder') }}" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>{{ __('m_payments.filters.status') }}</label>
                            <select name="status" class="form-control">
                                <option value="">{{ __('m_payments.filters.all') }}</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('m_payments.statuses.pending') }}</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>{{ __('m_payments.statuses.processing') }}</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('m_payments.statuses.completed') }}</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>{{ __('m_payments.statuses.failed') }}</option>
                                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>{{ __('m_payments.statuses.refunded') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
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
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>{{ __('m_payments.filters.date_from') }}</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>{{ __('m_payments.filters.date_to') }}</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i>
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
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>{{ __('m_payments.fields.payment_id') }}</th>
                        <th>{{ __('m_payments.fields.booking_ref') }}</th>
                        <th>{{ __('m_payments.fields.customer') }}</th>
                        <th>{{ __('m_payments.fields.tour') }}</th>
                        <th>{{ __('m_payments.fields.amount') }}</th>
                        <th>{{ __('m_payments.fields.gateway') }}</th>
                        <th>{{ __('m_payments.fields.status') }}</th>
                        <th>{{ __('m_payments.fields.date') }}</th>
                        <th>{{ __('m_payments.ui.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_id }}</td>
                        <td>
                            @if($payment->booking)
                            <a href="{{ route('admin.bookings.show', $payment->booking) }}">
                                {{ $payment->booking->booking_reference }}
                            </a>
                            @elseif(isset($payment->metadata['deleted_booking_snapshot']))
                            @php $snapshot = $payment->metadata['deleted_booking_snapshot']; @endphp
                            <span class="text-danger" title="{{ __('m_payments.messages.booking_deleted_on') }} {{ $snapshot['deleted_at'] ?? 'N/A' }}">
                                {{ $snapshot['booking_reference'] ?? 'N/A' }}
                                <i class="fas fa-trash-alt ml-1"></i>
                            </span>
                            @else
                            N/A
                            @endif
                        </td>
                        <td>
                            @if($payment->booking && $payment->booking->user)
                            <small>
                                {{ $payment->booking->user->full_name }}<br>
                                <span class="text-muted">{{ $payment->booking->user->email }}</span>
                            </small>
                            @elseif(isset($payment->metadata['deleted_booking_snapshot']['user']))
                            @php $user = $payment->metadata['deleted_booking_snapshot']['user']; @endphp
                            <small class="text-danger" title="{{ __('m_payments.messages.booking_deleted') }}">
                                {{ $user['name'] ?? 'N/A' }}<br>
                                <span class="text-muted">{{ $user['email'] ?? 'N/A' }}</span>
                                <i class="fas fa-trash-alt ml-1"></i>
                            </small>
                            @else
                            N/A
                            @endif
                        </td>
                        <td>
                            @if($payment->booking && $payment->booking->tour)
                            <small>{{ Str::limit($payment->booking->tour->name, 30) }}</small>
                            @elseif(isset($payment->metadata['deleted_booking_snapshot']['tour']))
                            @php $tour = $payment->metadata['deleted_booking_snapshot']['tour']; @endphp
                            <small class="text-danger" title="{{ __('m_payments.messages.booking_deleted') }}">
                                {{ Str::limit($tour['name'] ?? 'N/A', 30) }}
                                <i class="fas fa-trash-alt ml-1"></i>
                            </small>
                            @else
                            N/A
                            @endif
                        </td>
                        <td>
                            <strong>${{ number_format($payment->amount, 2) }}</strong>
                            <small class="text-muted d-block">{{ strtoupper($payment->currency) }}</small>
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ ucfirst($payment->gateway) }}</span>
                        </td>
                        <td>
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
                            <span class="badge badge-{{ $color }}">{{ ucfirst($payment->status) }}</span>
                        </td>
                        <td>
                            <small>
                                {{ $payment->created_at->format('M d, Y') }}<br>
                                <span class="text-muted">{{ $payment->created_at->format('H:i') }}</span>
                            </small>
                        </td>
                        <td>
                            <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-info" title="{{ __('m_payments.buttons.view_details') }}">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>{{ __('m_payments.messages.no_payments_found') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="card-footer">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('css')
<style>
    /* Mobile Responsive Styles */
    @media (max-width: 768px) {

        /* Stack statistics cards */
        .small-box {
            margin-bottom: 1rem;
        }

        /* Smaller table font */
        .table {
            font-size: 0.8rem;
        }

        .table td,
        .table th {
            padding: 0.4rem;
        }

        /* Hide less important columns on mobile */
        .table th:nth-child(4),
        .table td:nth-child(4) {
            display: none;
            /* Hide Tour column */
        }

        /* Make buttons smaller */
        .btn-sm {
            min-width: 36px;
            min-height: 36px;
            padding: 0.35rem 0.5rem;
            font-size: 0.75rem;
        }

        /* Stack filter inputs */
        .row>div[class*="col-"] {
            margin-bottom: 0.5rem;
        }

        /* Deleted booking indicator - smaller on mobile */
        .fa-trash-alt {
            font-size: 0.7rem;
        }
    }

    @media (max-width: 576px) {

        /* Very small screens - hide even more columns */
        .table th:nth-child(6),
        .table td:nth-child(6) {
            display: none;
            /* Hide Gateway column */
        }

        /* Make badges smaller */
        .badge {
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
        }
    }

    /* Improve touch targets for mobile */
    @media (hover: none) and (pointer: coarse) {
        .btn {
            min-width: 44px;
            min-height: 44px;
        }

        .nav-link {
            padding: 0.75rem 1rem;
        }
    }
</style>
@endpush