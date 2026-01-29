@extends('adminlte::page')

@section('title', 'Payment Details')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <h1 class="mb-2 mb-md-0">Payment #{{ $payment->payment_id }}</h1>
    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i>
        <span class="d-none d-sm-inline ml-1">Back to List</span>
    </a>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Left Column: Payment Details --}}
        <div class="col-12 col-lg-8 mb-3">
            {{-- Payment Information --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-credit-card mr-2"></i>Payment Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-5">Payment ID:</dt>
                                <dd class="col-7">
                                    <span class="text-monospace">#{{ $payment->payment_id }}</span>
                                </dd>

                                <dt class="col-5">Status:</dt>
                                <dd class="col-7">
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
                                    <span class="badge badge-{{ $color }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </dd>

                                <dt class="col-5">Amount:</dt>
                                <dd class="col-7">
                                    <strong class="text-success">
                                        ${{ number_format($payment->amount, 2) }} {{ strtoupper($payment->currency) }}
                                    </strong>
                                </dd>

                                <dt class="col-5">Gateway:</dt>
                                <dd class="col-7">
                                    <span class="badge badge-secondary">
                                        {{ ucfirst($payment->gateway) }}
                                    </span>
                                </dd>

                                <dt class="col-5">Payment Method:</dt>
                                <dd class="col-7">
                                    @if($payment->payment_method_type)
                                        <div>{{ ucfirst($payment->payment_method_type) }}</div>
                                        @if($payment->card_brand && $payment->card_last4)
                                            <small class="text-muted">
                                                {{ ucfirst($payment->card_brand) }} •••• {{ $payment->card_last4 }}
                                            </small>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                        <div class="col-12 col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-5">Created:</dt>
                                <dd class="col-7">
                                    <div>{{ $payment->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $payment->created_at->format('H:i') }}</small>
                                </dd>

                                <dt class="col-5">Paid At:</dt>
                                <dd class="col-7">
                                    @if($payment->paid_at)
                                        <div>{{ $payment->paid_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $payment->paid_at->format('H:i') }}</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </dd>

                                <dt class="col-5">Transaction ID:</dt>
                                <dd class="col-7">
                                    @if($payment->gateway_transaction_id)
                                        <small class="text-muted text-break">
                                            {{ Str::limit($payment->gateway_transaction_id, 30) }}
                                        </small>
                                        @if(strlen($payment->gateway_transaction_id) > 30)
                                            <button type="button"
                                                    class="btn btn-link btn-sm p-0 ml-1"
                                                    data-toggle="modal"
                                                    data-target="#transactionIdModal">
                                                <i class="fas fa-expand-alt"></i>
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </dd>

                                <dt class="col-5">Intent ID:</dt>
                                <dd class="col-7">
                                    @if($payment->gateway_payment_intent_id)
                                        <small class="text-muted text-break">
                                            {{ Str::limit($payment->gateway_payment_intent_id, 30) }}
                                        </small>
                                        @if(strlen($payment->gateway_payment_intent_id) > 30)
                                            <button type="button"
                                                    class="btn btn-link btn-sm p-0 ml-1"
                                                    data-toggle="modal"
                                                    data-target="#intentIdModal">
                                                <i class="fas fa-expand-alt"></i>
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </dd>

                                @if($payment->refunded_amount > 0)
                                    <dt class="col-5">Refunded:</dt>
                                    <dd class="col-7">
                                        <span class="text-danger">
                                            ${{ number_format($payment->refunded_amount, 2) }}
                                        </span>
                                    </dd>

                                    <dt class="col-5">Net Amount:</dt>
                                    <dd class="col-7">
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
                    <h3 class="card-title">
                        <i class="fas fa-calendar-check mr-2"></i>Booking Information
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.bookings.show', $payment->booking) }}"
                           class="btn btn-sm btn-primary">
                            <i class="fas fa-external-link-alt"></i>
                            <span class="d-none d-sm-inline ml-1">View Booking</span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-5">Reference:</dt>
                                <dd class="col-7">
                                    <span class="font-weight-bold">
                                        {{ $payment->booking->booking_reference }}
                                    </span>
                                </dd>

                                <dt class="col-5">Customer:</dt>
                                <dd class="col-7">
                                    @if($payment->booking->user)
                                        <div>{{ $payment->booking->user->full_name }}</div>
                                        <small class="text-muted text-break">
                                            {{ $payment->booking->user->email }}
                                        </small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </dd>

                                <dt class="col-5">Tour:</dt>
                                <dd class="col-7">
                                    @if($payment->booking->product)
                                        <span class="text-break">
                                            {{ $payment->booking->product->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                        <div class="col-12 col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-5">Product Date:</dt>
                                <dd class="col-7">
                                    @if($payment->booking->detail)
                                        {{ \Carbon\Carbon::parse($payment->booking->detail->product_date)->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </dd>

                                <dt class="col-5">Participants:</dt>
                                <dd class="col-7">
                                    <span class="badge badge-info">
                                        {{ $payment->booking->detail->total_pax ?? 'N/A' }} PAX
                                    </span>
                                </dd>

                                <dt class="col-5">Booking Status:</dt>
                                <dd class="col-7">
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
                    <h3 class="card-title">
                        <i class="fas fa-code mr-2"></i>Gateway Response
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button"
                                class="btn btn-tool"
                                onclick="copyToClipboard('gateway-response-json')">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <pre id="gateway-response-json" class="gateway-response-pre">{{ json_encode($payment->gateway_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column: Actions & Timeline --}}
        <div class="col-12 col-lg-4">
            {{-- Quick Actions --}}
            <div class="card sticky-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tasks mr-2"></i>Actions
                    </h3>
                </div>
                <div class="card-body">
                    @if($payment->is_refundable)
                        <button type="button"
                                class="btn btn-danger btn-block"
                                data-toggle="modal"
                                data-target="#refundModal">
                            <i class="fas fa-undo"></i> Process Refund
                        </button>
                    @else
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Cannot Refund</strong>
                            @if($payment->status == 'refunded')
                                <br><small>Already fully refunded.</small>
                            @elseif($payment->status != 'completed')
                                <br><small>Only completed payments can be refunded.</small>
                            @endif
                        </div>
                    @endif

                    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>

                    @if($payment->booking)
                        <a href="{{ route('admin.bookings.show', $payment->booking) }}"
                           class="btn btn-outline-primary btn-block">
                            <i class="fas fa-calendar-check"></i> View Booking
                        </a>
                    @endif
                </div>
            </div>

            {{-- Payment Timeline --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-2"></i>Timeline
                    </h3>
                </div>
                <div class="card-body">
                    <div class="timeline timeline-sm">
                        <div class="time-label">
                            <span class="bg-primary">{{ $payment->created_at->format('M d, Y') }}</span>
                        </div>
                        <div>
                            <i class="fas fa-plus bg-info"></i>
                            <div class="timeline-item">
                                <span class="time">
                                    <i class="fas fa-clock"></i> {{ $payment->created_at->format('H:i') }}
                                </span>
                                <h3 class="timeline-header">Payment Created</h3>
                            </div>
                        </div>
                        @if($payment->paid_at)
                        <div>
                            <i class="fas fa-check bg-success"></i>
                            <div class="timeline-item">
                                <span class="time">
                                    <i class="fas fa-clock"></i> {{ $payment->paid_at->format('H:i') }}
                                </span>
                                <h3 class="timeline-header">Payment Completed</h3>
                                <div class="timeline-body">
                                    Amount: ${{ number_format($payment->amount, 2) }}
                                </div>
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
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.payments.refund', $payment) }}" id="refundForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-undo mr-2"></i>Process Refund
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> This action cannot be undone.
                    </div>

                    <div class="form-group">
                        <label>Refund Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number"
                                   name="amount"
                                   id="refundAmount"
                                   class="form-control"
                                   step="0.01"
                                   min="0.01"
                                   max="{{ $payment->net_amount }}"
                                   value="{{ $payment->net_amount }}"
                                   required>
                            <div class="input-group-append">
                                <span class="input-group-text">{{ strtoupper($payment->currency) }}</span>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Maximum refundable: <strong>${{ number_format($payment->net_amount, 2) }}</strong>
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Reason <span class="text-danger">*</span></label>
                        <textarea name="reason"
                                  class="form-control"
                                  rows="3"
                                  required
                                  placeholder="Enter reason for refund..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger" id="refundBtn">
                        <i class="fas fa-undo"></i> Process Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Transaction ID Modal --}}
@if($payment->gateway_transaction_id && strlen($payment->gateway_transaction_id) > 30)
<div class="modal fade" id="transactionIdModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Full Transaction ID</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-0">
                    <textarea class="form-control text-monospace"
                              rows="3"
                              readonly>{{ $payment->gateway_transaction_id }}</textarea>
                    <button type="button"
                            class="btn btn-sm btn-outline-primary mt-2"
                            onclick="copyToClipboard('transactionIdModal textarea')">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Intent ID Modal --}}
@if($payment->gateway_payment_intent_id && strlen($payment->gateway_payment_intent_id) > 30)
<div class="modal fade" id="intentIdModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Full Payment Intent ID</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-0">
                    <textarea class="form-control text-monospace"
                              rows="3"
                              readonly>{{ $payment->gateway_payment_intent_id }}</textarea>
                    <button type="button"
                            class="btn btn-sm btn-outline-primary mt-2"
                            onclick="copyToClipboard('intentIdModal textarea')">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('css')
<style>
/* ============================================
   PAYMENT DETAILS STYLES
   ============================================ */

/* Definition lists */
dl.row {
    margin-bottom: 0.5rem;
}

dl.row dt {
    font-weight: 600;
    font-size: 0.875rem;
    color: #6c757d;
    padding: 0.25rem 0;
}

dl.row dd {
    padding: 0.25rem 0;
    margin-bottom: 0;
    word-wrap: break-word;
}

/* Gateway Response */
.gateway-response-pre {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 1rem;
    margin: 0;
    max-height: 400px;
    overflow-y: auto;
    font-size: 0.8rem;
    line-height: 1.5;
    white-space: pre-wrap;
    word-wrap: break-word;
}

/* Sticky card for actions */
.sticky-card {
    position: sticky;
    top: 1rem;
    z-index: 1;
}

/* Timeline improvements */
.timeline-sm {
    margin: 0;
}

.timeline-sm .timeline-item {
    margin-left: 15px;
    padding-left: 15px;
}

.timeline-sm .time {
    font-size: 0.75rem;
}

.timeline-sm .timeline-header {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.timeline-sm .timeline-body {
    font-size: 0.8rem;
}

/* Badge improvements */
.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

/* Card spacing */
.card {
    margin-bottom: 1rem;
}

/* Alert improvements */
.alert {
    border-radius: 0.25rem;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

/* Button improvements */
.btn-block + .btn-block {
    margin-top: 0.5rem;
}

/* Text utilities */
.text-break {
    word-wrap: break-word;
    word-break: break-word;
}

.text-monospace {
    font-family: 'Courier New', monospace;
}

/* ============================================
   RESPONSIVE STYLES
   ============================================ */

/* Tablets and below (< 992px) */
@media (max-width: 991.98px) {
    .sticky-card {
        position: static;
    }

    dl.row dt,
    dl.row dd {
        font-size: 0.85rem;
    }

    .gateway-response-pre {
        font-size: 0.75rem;
        max-height: 300px;
    }

    .card {
        margin-bottom: 1rem;
    }
}

/* Mobile landscape and below (< 768px) */
@media (max-width: 767.98px) {
    /* Stack definition lists */
    dl.row dt {
        text-align: left !important;
        padding-right: 0;
        margin-bottom: 0.25rem;
    }

    dl.row dd {
        padding-left: 0;
        margin-bottom: 0.75rem;
    }

    /* Smaller fonts */
    .card-title {
        font-size: 1rem;
    }

    dl.row dt,
    dl.row dd {
        font-size: 0.8rem;
    }

    /* Timeline adjustments */
    .timeline-sm .timeline-item {
        margin-left: 10px;
        padding-left: 10px;
    }

    .timeline-sm i {
        width: 25px;
        height: 25px;
        font-size: 0.75rem;
        line-height: 25px;
    }

    /* Gateway response */
    .gateway-response-pre {
        font-size: 0.7rem;
        padding: 0.75rem;
        max-height: 250px;
    }

    /* Buttons */
    .btn {
        font-size: 0.875rem;
    }

    .btn-block {
        padding: 0.5rem;
    }
}

/* Mobile portrait (< 576px) */
@media (max-width: 575.98px) {
    /* Header adjustments */
    .content-header h1 {
        font-size: 1.5rem;
    }

    /* Card adjustments */
    .card-header h3 {
        font-size: 0.95rem;
    }

    .card-body {
        padding: 0.75rem;
    }

    /* Definition lists - full width */
    dl.row dt,
    dl.row dd {
        flex: 0 0 100%;
        max-width: 100%;
    }

    dl.row dt {
        font-size: 0.75rem;
        margin-bottom: 0.15rem;
    }

    dl.row dd {
        font-size: 0.8rem;
        margin-bottom: 0.5rem;
        padding-left: 1rem;
    }

    /* Badges */
    .badge {
        font-size: 0.7rem;
        padding: 0.25em 0.5em;
    }

    /* Timeline */
    .timeline-sm .time {
        font-size: 0.7rem;
    }

    .timeline-sm .timeline-header {
        font-size: 0.8rem;
    }

    /* Modal adjustments */
    .modal-dialog {
        margin: 0.5rem;
    }

    .modal-body {
        padding: 1rem;
    }

    /* Gateway response */
    .gateway-response-pre {
        font-size: 0.65rem;
        padding: 0.5rem;
        max-height: 200px;
    }
}

/* Extra small devices (< 400px) */
@media (max-width: 399.98px) {
    .content-header h1 {
        font-size: 1.25rem;
    }

    .card-header h3 {
        font-size: 0.875rem;
    }

    dl.row dt {
        font-size: 0.7rem;
    }

    dl.row dd {
        font-size: 0.75rem;
    }

    .btn {
        font-size: 0.8rem;
        padding: 0.375rem 0.5rem;
    }
}

/* ============================================
   TOUCH IMPROVEMENTS
   ============================================ */

@media (hover: none) and (pointer: coarse) {
    .btn {
        min-width: 44px;
        min-height: 44px;
    }

    .btn-tool {
        min-width: 44px;
        min-height: 44px;
    }

    .form-control {
        min-height: 44px;
    }
}

/* ============================================
   PRINT STYLES
   ============================================ */

@media print {
    .btn,
    .card-tools,
    .sticky-card,
    .modal {
        display: none !important;
    }

    .card {
        page-break-inside: avoid;
    }

    .gateway-response-pre {
        max-height: none;
        font-size: 8pt;
    }
}
</style>
@endpush

@push('js')
<script>
// Copy to clipboard function
function copyToClipboard(elementId) {
    let element;

    if (elementId.includes(' ')) {
        // It's a selector
        element = document.querySelector(elementId);
    } else {
        element = document.getElementById(elementId);
    }

    if (!element) {
        console.error('Element not found:', elementId);
        return;
    }

    const text = element.textContent || element.value;

    if (navigator.clipboard && window.isSecureContext) {
        // Modern clipboard API
        navigator.clipboard.writeText(text).then(() => {
            showCopyNotification();
        }).catch(err => {
            console.error('Copy failed:', err);
            fallbackCopy(text);
        });
    } else {
        // Fallback for older browsers
        fallbackCopy(text);
    }
}

function fallbackCopy(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();

    try {
        document.execCommand('copy');
        showCopyNotification();
    } catch (err) {
        console.error('Fallback copy failed:', err);
        alert('Failed to copy to clipboard');
    }

    document.body.removeChild(textarea);
}

function showCopyNotification() {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = 'alert alert-success';
    toast.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 200px;';
    toast.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Copied to clipboard!';

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.transition = 'opacity 0.3s';
        toast.style.opacity = '0';
        setTimeout(() => document.body.removeChild(toast), 300);
    }, 2000);
}

// Refund form validation
document.addEventListener('DOMContentLoaded', function() {
    const refundForm = document.getElementById('refundForm');
    const refundBtn = document.getElementById('refundBtn');
    const refundAmount = document.getElementById('refundAmount');

    if (refundForm) {
        refundForm.addEventListener('submit', function(e) {
            // Disable button to prevent double submission
            refundBtn.disabled = true;
            refundBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            // Validate amount
            const amount = parseFloat(refundAmount.value);
            const maxAmount = parseFloat(refundAmount.max);

            if (amount > maxAmount) {
                e.preventDefault();
                alert('Refund amount cannot exceed $' + maxAmount.toFixed(2));
                refundBtn.disabled = false;
                refundBtn.innerHTML = '<i class="fas fa-undo"></i> Process Refund';
                return false;
            }

            // Confirm action
            if (!confirm('Are you sure you want to process this refund? This action cannot be undone.')) {
                e.preventDefault();
                refundBtn.disabled = false;
                refundBtn.innerHTML = '<i class="fas fa-undo"></i> Process Refund';
                return false;
            }
        });
    }
});
</script>
@endpush
