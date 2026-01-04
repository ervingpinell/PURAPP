{{-- Unpaid Bookings Widget (Phase 9) --}}
@if(isset($unpaidBookings) && $unpaidCount > 0)
<div class="card card-warning">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-exclamation-triangle"></i>
            Reservas Sin Pagar
        </h3>
        <div class="card-tools">
            <span class="badge badge-warning">{{ $unpaidCount }} total</span>
        </div>
    </div>
    <div class="card-body p-0">
        @if($unpaidBookings->isEmpty())
        <p class="text-center p-3 text-muted">
            <i class="fas fa-check-circle"></i> No hay reservas expirando pronto
        </p>
        @else
        <div class="list-group list-group-flush">
            @foreach($unpaidBookings as $booking)
            <a href="{{ route('admin.bookings.show', $booking->booking_id) }}" class="list-group-item list-group-item-action">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $booking->booking_reference }}</strong>
                        <br>
                        <small class="text-muted">
                            {{ $booking->user->name ?? 'N/A' }} - {{ $booking->tour->title ?? 'N/A' }}
                        </small>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-danger">
                            <i class="fas fa-clock"></i>
                            {{ $booking->pending_expires_at->diffForHumans() }}
                        </span>
                        <br>
                        <small class="text-muted">${{ number_format($booking->total, 2) }}</small>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        <div class="card-footer text-center">
            <a href="{{ route('admin.bookings.unpaid') }}" class="btn btn-sm btn-warning">
                <i class="fas fa-list"></i> Ver Todas ({{ $unpaidCount }})
            </a>
        </div>
        @endif
    </div>
</div>
@endif