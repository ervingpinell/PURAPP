@extends('adminlte::page')

@section('title', 'Payment Details')

@section('content_header')
<h1>Payment Details #{{ $payment->payment_id }}</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Left Column: Payment Details --}}
        <div class="col-md-8">
            {{-- Payment Information --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Payment ID:</dt>
                                <dd class="col-sm-7">{{ $payment->payment_id }}</dd>

                                <dt class="col-sm-5">Status:</dt>
                                <dd class="col-sm-7">
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
                                </dd>

                                <dt class="col-sm-5">Amount:</dt>
                                <dd class="col-sm-7">
                                    <strong>${{ number_format($payment->amount, 2) }} {{ strtoupper($payment->currency) }}</strong>
                                </dd>

                                <dt class="col-sm-5">Gateway:</dt>
                                <dd class="col-sm-7">
                                    <span class="badge badge-secondary">{{ ucfirst($payment->gateway) }}</span>
                                </dd>

                                <dt class="col-sm-5">Payment Method:</dt>
                                <dd class="col-sm-7">
                                    @if($payment->payment_method_type)
                                    {{ ucfirst($payment->payment_method_type) }}
                                    @if($payment->card_brand && $payment->card_last4)
                                    <br><small class="text-muted">{{ ucfirst($payment->card_brand) }} •••• {{ $payment->card_last4 }}</small>
                                    @endif
                                    @else
                                    N/A
                                    @endif
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Created:</dt>
                                <dd class="col-sm-7">{{ $payment->created_at->format('M d, Y H:i') }}</dd>

                                <dt class="col-sm-5">Paid At:</dt>
                                <dd class="col-sm-7">{{ $payment->paid_at ? $payment->paid_at->format('M d, Y H:i') : 'N/A' }}</dd>

                                <dt class="col-sm-5">Transaction ID:</dt>
                                <dd class="col-sm-7">
                                    <small class="text-muted">{{ $payment->gateway_transaction_id ?? 'N/A' }}</small>
                                </dd>

                                <dt class="col-sm-5">Intent ID:</dt>
                                <dd class="col-sm-7">
                                    <small class="text-muted">{{ $payment->gateway_payment_intent_id ?? 'N/A' }}</small>
                                </dd>

                                @if($payment->refunded_amount > 0)
                                <dt class="col-sm-5">Refunded:</dt>
                                <dd class="col-sm-7">
                                    <span class="text-danger">${{ number_format($payment->refunded_amount, 2) }}</span>
                                </dd>

                                <dt class="col-sm-5">Net Amount:</dt>
                                <dd class="col-sm-7">
                                    <strong>${{ number_format($payment->net_amount, 2) }}</strong>
                                </dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Booking Information --}}
            @if($payment->booking)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Booking Information</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.bookings.show', $payment->booking) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-external-link-alt"></i> View Booking
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Reference:</dt>
                                <dd class="col-sm-7">{{ $payment->booking->booking_reference }}</dd>

                                <dt class="col-sm-5">Customer:</dt>
                                <dd class="col-sm-7">
                                    @if($payment->booking->user)
                                    {{ $payment->booking->user->full_name }}<br>
                                    <small class="text-muted">{{ $payment->booking->user->email }}</small>
                                    @else
                                    N/A
                                    @endif
                                </dd>

                                <dt class="col-sm-5">Tour:</dt>
                                <dd class="col-sm-7">{{ $payment->booking->tour->name ?? 'N/A' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Tour Date:</dt>
                                <dd class="col-sm-7">
                                    @if($payment->booking->detail)
                                    {{ \Carbon\Carbon::parse($payment->booking->detail->tour_date)->format('M d, Y') }}
                                    @else
                                    N/A
                                    @endif
                                </dd>

                                <dt class="col-sm-5">Participants:</dt>
                                <dd class="col-sm-7">{{ $payment->booking->detail->total_pax ?? 'N/A' }}</dd>

                                <dt class="col-sm-5">Booking Status:</dt>
                                <dd class="col-sm-7">
                                    <span class="badge badge-{{ $payment->booking->status == 'confirmed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($payment->booking->status) }}
                                    </span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Gateway Response --}}
            @if($payment->gateway_response)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gateway Response</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3" style="max-height: 300px; overflow-y: auto;">{{ json_encode($payment->gateway_response, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column: Actions --}}
        <div class="col-md-4">
            {{-- Quick Actions --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    @if($payment->is_refundable)
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#refundModal">
                        <i class="fas fa-undo"></i> Process Refund
                    </button>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> This payment cannot be refunded.
                        @if($payment->status == 'refunded')
                        <br><small>Already fully refunded.</small>
                        @elseif($payment->status != 'completed')
                        <br><small>Only completed payments can be refunded.</small>
                        @endif
                    </div>
                    @endif

                    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-block mt-2">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            {{-- Payment Timeline --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Timeline</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="time-label">
                            <span class="bg-primary">{{ $payment->created_at->format('M d, Y') }}</span>
                        </div>
                        <div>
                            <i class="fas fa-plus bg-info"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ $payment->created_at->format('H:i') }}</span>
                                <h3 class="timeline-header">Payment Created</h3>
                            </div>
                        </div>
                        @if($payment->paid_at)
                        <div>
                            <i class="fas fa-check bg-success"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ $payment->paid_at->format('H:i') }}</span>
                                <h3 class="timeline-header">Payment Completed</h3>
                            </div>
                        </div>
                        @endif
                        @if($payment->refunded_amount > 0)
                        <div>
                            <i class="fas fa-undo bg-warning"></i>
                            <div class="timeline-item">
                                <h3 class="timeline-header">Refunded</h3>
                                <div class="timeline-body">
                                    Amount: ${{ number_format($payment->refunded_amount, 2) }}
                                </div>
                            </div>
                        </div>
                        @endif
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Refund Modal --}}
<div class="modal fade" id="refundModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.payments.refund', $payment) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Process Refund</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> This action cannot be undone.
                    </div>

                    <div class="form-group">
                        <label>Refund Amount</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" name="amount" class="form-control"
                                step="0.01" min="0.01" max="{{ $payment->net_amount }}"
                                value="{{ $payment->net_amount }}" required>
                        </div>
                        <small class="form-text text-muted">
                            Maximum refundable: ${{ number_format($payment->net_amount, 2) }}
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Enter reason for refund..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-undo"></i> Process Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection