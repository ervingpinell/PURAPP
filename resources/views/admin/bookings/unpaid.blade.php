@extends('adminlte::page')

@section('title', 'Unpaid Bookings')

@section('content_header')
<h1 class="m-0 text-dark">
    <i class="fas fa-exclamation-triangle text-warning"></i>
    Unpaid Bookings (Pay-Later System)
</h1>
@stop

@section('content')
<div class="container-fluid">
    <!-- Filters -->
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.bookings.unpaid') }}" class="form-inline">
                <div class="form-group mr-3">
                    <label for="expiry_filter" class="mr-2">Status:</label>
                    <select name="expiry_filter" id="expiry_filter" class="form-control">
                        <option value="">All</option>
                        <option value="expired" {{ request('expiry_filter') === 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="expiring_soon" {{ request('expiry_filter') === 'expiring_soon' ? 'selected' : '' }}>Expiring Soon (< 2h)</option>
                        <option value="active" {{ request('expiry_filter') === 'active' ? 'selected' : '' }}>Active (> 2h)</option>
                    </select>
                </div>

                <div class="form-group mr-3">
                    <label for="is_pay_later" class="mr-2">Type:</label>
                    <select name="is_pay_later" id="is_pay_later" class="form-control">
                        <option value="">All</option>
                        <option value="1" {{ request('is_pay_later') === '1' ? 'selected' : '' }}>Pay-Later</option>
                        <option value="0" {{ request('is_pay_later') === '0' ? 'selected' : '' }}>Standard</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('admin.bookings.unpaid') }}" class="btn btn-secondary ml-2">
                    <i class="fas fa-times"></i> Clear
                </a>
            </form>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $bookings->total() }} Unpaid Bookings</h3>
        </div>
        <div class="card-body p-0">
            @if($bookings->isEmpty())
            <div class="p-4 text-center text-muted">
                <i class="fas fa-check-circle fa-3x mb-3"></i>
                <p>No unpaid bookings found</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Customer</th>
                            <th>Tour</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Expires</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                        @php
                        $now = now();
                        $expiry = $booking->pending_expires_at;
                        $isExpired = $expiry < $now;
                            $isExpiringSoon=!$isExpired && $expiry <=$now->copy()->addHours(2);

                            $statusClass = $isExpired ? 'danger' : ($isExpiringSoon ? 'warning' : 'success');
                            $statusText = $isExpired ? 'EXPIRED' : ($isExpiringSoon ? 'EXPIRING SOON' : 'ACTIVE');
                            @endphp
                            <tr class="{{ $isExpired ? 'table-danger' : ($isExpiringSoon ? 'table-warning' : '') }}">
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking->booking_id) }}" class="font-weight-bold">
                                        {{ $booking->booking_reference }}
                                    </a>
                                </td>
                                <td>
                                    {{ $booking->user->name }}<br>
                                    <small class="text-muted">{{ $booking->user->email }}</small>
                                </td>
                                <td>{{ $booking->tour->title ?? 'N/A' }}</td>
                                <td>{{ $booking->details->first()->tour_date ?? 'N/A' }}</td>
                                <td class="font-weight-bold">${{ number_format($booking->total, 2) }}</td>
                                <td>
                                    @if($booking->is_pay_later)
                                    <span class="badge badge-info">Pay-Later</span>
                                    @else
                                    <span class="badge badge-secondary">Standard</span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $expiry->format('Y-m-d H:i') }}</div>
                                    <small class="text-muted">{{ $expiry->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td class="text-right">
                                    <div class="btn-group">
                                        <!-- Extend Button -->
                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#extendModal{{ $booking->booking_id }}">
                                            <i class="fas fa-clock"></i> Extend
                                        </button>

                                        <!-- Cancel Button -->
                                        <form action="{{ route('admin.bookings.cancel_unpaid', $booking->booking_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Extend Modal -->
                                    <div class="modal fade" id="extendModal{{ $booking->booking_id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.bookings.extend', $booking->booking_id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Extend Booking {{ $booking->booking_reference }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="extend_hours">Extend by (hours):</label>
                                                            <input type="number" name="extend_hours" id="extend_hours" class="form-control" min="1" max="72" value="12" required>
                                                            <small class="form-text text-muted">Max: 72 hours</small>
                                                        </div>
                                                        <div class="alert alert-info">
                                                            <strong>Current expiry:</strong> {{ $expiry->format('Y-m-d H:i') }}<br>
                                                            <strong>Extensions:</strong> {{ $booking->extension_count ?? 0 }}
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Extend</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @if($bookings->hasPages())
        <div class="card-footer">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
</div>
@stop

@section('css')
<style>
    .table-danger td {
        background-color: #f8d7da !important;
    }

    .table-warning td {
        background-color: #fff3cd !important;
    }
</style>
@stop

@section('js')
<script>
    @if(session('success'))
    toastr.success('{{ session('
        success ') }}');
    @endif
    @if(session('error'))
    toastr.error('{{ session('
        error ') }}');
    @endif
</script>
@stop