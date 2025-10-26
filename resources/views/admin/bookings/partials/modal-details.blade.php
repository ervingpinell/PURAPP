{{-- resources/views/admin/bookings/partials/modal-details.blade.php --}}

<div class="modal fade" id="modalDetails{{ $booking->booking_id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title">
          <i class="fas fa-info-circle me-2"></i>
          {{ __('m_bookings.bookings.ui.booking_details') }} #{{ $booking->booking_id }}
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        @php
          $detail = $booking->detail;
          $tour = $booking->tour;
          $liveName = optional($tour)->name;
          $snapName = $detail->tour_name_snapshot ?: ($booking->tour_name_snapshot ?? null);
          $tourName = $liveName ?? ($snapName ? "Deleted Tour ({$snapName})" : "Deleted tour");
        @endphp

        {{-- Status Alert with Actions --}}
        <div class="alert alert-{{ $booking->status === 'pending' ? 'warning' : ($booking->status === 'confirmed' ? 'success' : 'danger') }} d-flex justify-content-between align-items-center">
          <div>
            <strong>{{ __('m_bookings.bookings.fields.status') }}:</strong>
            <span class="ms-2">{{ __('m_bookings.bookings.statuses.' . $booking->status) }}</span>
          </div>
          <div class="btn-group btn-group-sm" role="group">
            @if($booking->status !== 'confirmed')
              <form action="{{ route('admin.bookings.update-status', $booking->booking_id) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="confirmed">
                <button type="submit" class="btn btn-success btn-sm" title="{{ __('m_bookings.actions.confirm') }}">
                  <i class="fas fa-check-circle"></i> {{ __('m_bookings.actions.confirm') }}
                </button>
              </form>
            @endif

            @if($booking->status !== 'cancelled')
              <form action="{{ route('admin.bookings.update-status', $booking->booking_id) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="cancelled">
                <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('{{ __('m_bookings.actions.confirm_cancel') }}')"
                        title="{{ __('m_bookings.actions.cancel') }}">
                  <i class="fas fa-times-circle"></i> {{ __('m_bookings.actions.cancel') }}
                </button>
              </form>
            @endif
          </div>
        </div>

        {{-- Booking Information --}}
        <div class="row mb-3">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header bg-primary text-white">
                <strong>{{ __('m_bookings.details.booking_info') }}</strong>
              </div>
              <div class="card-body">
                <dl class="row mb-0">
                  <dt class="col-sm-5">{{ __('m_bookings.bookings.fields.reference') }}:</dt>
                  <dd class="col-sm-7"><strong>{{ $booking->booking_reference }}</strong></dd>

                  <dt class="col-sm-5">{{ __('m_bookings.bookings.fields.booking_date') }}:</dt>
                  <dd class="col-sm-7">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</dd>

                  @if($booking->promoCode)
                    <dt class="col-sm-5">{{ __('m_bookings.bookings.fields.promo_code') }}:</dt>
                    <dd class="col-sm-7"><span class="badge bg-success">{{ $booking->promoCode->code }}</span></dd>
                  @endif
                </dl>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card">
              <div class="card-header bg-success text-white">
                <strong>{{ __('m_bookings.details.customer_info') }}</strong>
              </div>
              <div class="card-body">
                <dl class="row mb-0">
                  <dt class="col-sm-4">{{ __('m_bookings.bookings.fields.customer') }}:</dt>
                  <dd class="col-sm-8">{{ $booking->user->full_name ?? '-' }}</dd>

                  <dt class="col-sm-4">{{ __('m_bookings.bookings.fields.email') }}:</dt>
                  <dd class="col-sm-8"><a href="mailto:{{ $booking->user->email }}">{{ $booking->user->email ?? '-' }}</a></dd>

                  <dt class="col-sm-4">{{ __('m_bookings.bookings.fields.phone') }}:</dt>
                  <dd class="col-sm-8">{{ $booking->user->phone ?? '-' }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        {{-- Tour Information --}}
        <div class="card mb-3">
          <div class="card-header bg-warning">
            <strong>{{ __('m_bookings.details.tour_info') }}</strong>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <dl class="row">
                  <dt class="col-sm-4">{{ __('m_bookings.bookings.fields.tour') }}:</dt>
                  <dd class="col-sm-8">{{ $tourName }}</dd>

                  <dt class="col-sm-4">{{ __('m_bookings.bookings.fields.tour_date') }}:</dt>
                  <dd class="col-sm-8">{{ optional($detail)->tour_date?->format('M d, Y') ?? '-' }}</dd>

                  <dt class="col-sm-4">{{ __('m_bookings.bookings.fields.schedule') }}:</dt>
                  <dd class="col-sm-8">
                    @if($detail->schedule)
                      {{ \Carbon\Carbon::parse($detail->schedule->start_time)->format('g:i A') }} -
                      {{ \Carbon\Carbon::parse($detail->schedule->end_time)->format('g:i A') }}
                    @else
                      —
                    @endif
                  </dd>

                  <dt class="col-sm-4">{{ __('m_bookings.bookings.fields.language') }}:</dt>
                  <dd class="col-sm-8">{{ optional($detail->tourLanguage)->name ?? '-' }}</dd>
                </dl>
              </div>

              <div class="col-md-6">
                <dl class="row">
                  <dt class="col-sm-4">{{ __('m_bookings.bookings.fields.hotel') }}:</dt>
                  <dd class="col-sm-8">{{ $detail->hotel->name ?? $detail->other_hotel_name ?? '-' }}</dd>

                  <dt class="col-sm-4">{{ __('m_bookings.bookings.fields.meeting_point') }}:</dt>
                  <dd class="col-sm-8">{{ optional($detail->meetingPoint)->name ?? '—' }}</dd>

                  <dt class="col-sm-4">{{ __('m_bookings.bookings.fields.type') }}:</dt>
                  <dd class="col-sm-8">{{ data_get($booking, 'tour.tourType.name', '—') }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        {{-- Pricing Information --}}
        <div class="card mb-3">
          <div class="card-header bg-danger text-white">
            <strong>{{ __('m_bookings.details.pricing_info') }}</strong>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <dl class="row">
                  <dt class="col-sm-6">{{ __('m_bookings.bookings.fields.adults') }}:</dt>
                  <dd class="col-sm-6">{{ $detail->adults_quantity }} × ${{ number_format($tour->adult_price ?? 0, 2) }}</dd>

                  <dt class="col-sm-6">{{ __('m_bookings.bookings.fields.children') }}:</dt>
                  <dd class="col-sm-6">{{ $detail->kids_quantity }} × ${{ number_format($tour->kid_price ?? 0, 2) }}</dd>
                </dl>
              </div>

              <div class="col-md-6">
                <dl class="row">
                  <dt class="col-sm-6">{{ __('m_bookings.details.subtotal') }}:</dt>
                  <dd class="col-sm-6">
                    ${{ number_format(($detail->adults_quantity * ($tour->adult_price ?? 0)) + ($detail->kids_quantity * ($tour->kid_price ?? 0)), 2) }}
                  </dd>

                  @if($booking->promoCode)
                    <dt class="col-sm-6">{{ __('m_bookings.details.discount') }}:</dt>
                    <dd class="col-sm-6 text-success">
                      @if($booking->promoCode->discount_percent)
                        -{{ $booking->promoCode->discount_percent }}%
                      @elseif($booking->promoCode->discount_amount)
                        -${{ number_format($booking->promoCode->discount_amount, 2) }}
                      @endif
                    </dd>
                  @endif

                  <dt class="col-sm-6"><strong>{{ __('m_bookings.bookings.fields.total') }}:</strong></dt>
                  <dd class="col-sm-6"><strong class="text-success fs-5">${{ number_format($booking->total, 2) }}</strong></dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        {{-- Notes --}}
        @if($booking->notes)
          <div class="alert alert-info">
            <strong><i class="fas fa-sticky-note me-2"></i>{{ __('m_bookings.bookings.fields.notes') }}:</strong>
            <p class="mb-0 mt-2">{{ $booking->notes }}</p>
          </div>
        @endif
      </div>

      <div class="modal-footer">
        <a href="{{ route('admin.bookings.receipt', $booking->booking_id) }}" class="btn btn-primary" target="_blank">
          <i class="fas fa-file-pdf me-1"></i> {{ __('m_bookings.bookings.ui.download_receipt') }}
        </a>
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $booking->booking_id }}">
          <i class="fas fa-edit me-1"></i> {{ __('m_bookings.bookings.buttons.edit') }}
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_bookings.bookings.buttons.close') }}</button>
      </div>
    </div>
  </div>
</div>
