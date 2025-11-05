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
          // ===== Datos base
          $detail = $booking->detail;
          $tour   = $booking->tour;

          // Nombre del tour (live o snapshot)
          $liveName = optional($tour)->name;
          $snapName = $detail->tour_name_snapshot ?? ($booking->tour_name_snapshot ?? null);
          $tourName = $liveName ?? ($snapName ? "Deleted Tour ({$snapName})" : "Deleted tour");

          // ========== CATEGORÍAS DINÁMICAS ==========
          $categoriesData = [];
          $subtotalSnap = 0;
          $totalPersons = 0;

          if ($detail->categories && is_string($detail->categories)) {
            try {
              $categoriesData = json_decode($detail->categories, true);
            } catch (\Exception $e) {
              \Log::error('Error decoding categories JSON', [
                'booking_id' => $booking->booking_id,
                'error' => $e->getMessage()
              ]);
            }
          } elseif (is_array($detail->categories)) {
            $categoriesData = $detail->categories;
          }

          // Normalizar formato
          $categories = [];
          if (!empty($categoriesData)) {
            // Array de objetos
            if (isset($categoriesData[0]) && is_array($categoriesData[0])) {
              foreach ($categoriesData as $cat) {
                $qty = (int)($cat['quantity'] ?? 0);
                $price = (float)($cat['price'] ?? 0);
                $name = $cat['name'] ?? $cat['category_name'] ?? 'Category';

                $categories[] = [
                  'name' => $name,
                  'quantity' => $qty,
                  'price' => $price,
                  'total' => $qty * $price
                ];

                $subtotalSnap += $qty * $price;
                $totalPersons += $qty;
              }
            }
            // Array asociativo
            else {
              foreach ($categoriesData as $catId => $cat) {
                $qty = (int)($cat['quantity'] ?? 0);
                $price = (float)($cat['price'] ?? 0);
                $name = $cat['name'] ?? $cat['category_name'] ?? "Category #{$catId}";

                $categories[] = [
                  'name' => $name,
                  'quantity' => $qty,
                  'price' => $price,
                  'total' => $qty * $price
                ];

                $subtotalSnap += $qty * $price;
                $totalPersons += $qty;
              }
            }
          }

          // Fallback a legacy
          if (empty($categories)) {
            $adultsQty = (int)($detail->adults_quantity ?? 0);
            $kidsQty = (int)($detail->kids_quantity ?? 0);
            $adultPrice = (float)($detail->adult_price ?? $tour->adult_price ?? 0);
            $kidPrice = (float)($detail->kid_price ?? $tour->kid_price ?? 0);

            if ($adultsQty > 0) {
              $categories[] = [
                'name' => 'Adults',
                'quantity' => $adultsQty,
                'price' => $adultPrice,
                'total' => $adultsQty * $adultPrice
              ];
              $subtotalSnap += $adultsQty * $adultPrice;
              $totalPersons += $adultsQty;
            }

            if ($kidsQty > 0) {
              $categories[] = [
                'name' => 'Kids',
                'quantity' => $kidsQty,
                'price' => $kidPrice,
                'total' => $kidsQty * $kidPrice
              ];
              $subtotalSnap += $kidsQty * $kidPrice;
              $totalPersons += $kidsQty;
            }
          }

          // ===== Promo / ajuste desde pivot con snapshots
          $booking->loadMissing('redemption.promoCode');
          $redemption   = $booking->redemption;
          $promoModel   = optional($redemption)->promoCode ?: $booking->promoCode;
          $promoCode    = $promoModel?->code;

          // Operación aplicada
          $operation = ($redemption && $redemption->operation_snapshot === 'add') ? 'add' : 'subtract';

          // Monto aplicado
          $appliedAmount = (float) ($redemption->applied_amount ?? 0.0);
          if (!$appliedAmount && $promoModel) {
            if ($promoModel->discount_percent) {
              $appliedAmount = round($subtotalSnap * ($promoModel->discount_percent/100), 2);
            } elseif ($promoModel->discount_amount) {
              $appliedAmount = (float)$promoModel->discount_amount;
            }
          }

          // Badges de valor
          $percentSnapshot = $redemption->percent_snapshot ?? $promoModel->discount_percent ?? null;
          $amountSnapshot  = $redemption->amount_snapshot  ?? $promoModel->discount_amount  ?? null;

          // Etiqueta según operación
          $adjustLabel = $operation === 'add'
              ? __('m_config.promocode.operations.surcharge')
              : __('m_config.promocode.operations.discount');

          // Signo para el display
          $sign = $operation === 'add' ? '+' : '−';

          // Total
          $grandTotal = (float) ($booking->total ?? max(0, $operation === 'add'
                ? $subtotalSnap + $appliedAmount
                : $subtotalSnap - $appliedAmount));

          // ========== HOTEL O MEETING POINT ==========
          $hasHotel = !empty($detail->hotel_id) || !empty($detail->other_hotel_name);
          $hasMeetingPoint = !empty($detail->meeting_point_id) || !empty($detail->meeting_point_name);

          $hotelName = null;
          $meetingPointName = null;

          if ($hasHotel) {
            $hotelName = $detail->other_hotel_name ?? optional($detail->hotel)->name ?? '—';
          } elseif ($hasMeetingPoint) {
            $meetingPointName = $detail->meeting_point_name ?? optional($detail->meetingPoint)->name ?? '—';
          }
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
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="confirmed">
                <button type="submit" class="btn btn-success btn-sm" title="{{ __('m_bookings.actions.confirm') }}">
                  <i class="fas fa-check-circle"></i> {{ __('m_bookings.actions.confirm') }}
                </button>
              </form>
            @endif
            @if($booking->status !== 'cancelled')
              <form action="{{ route('admin.bookings.update-status', $booking->booking_id) }}" method="POST" class="d-inline">
                @csrf @method('PATCH')
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
                  @if($hasHotel && $hotelName)
                    <dt class="col-sm-4">{{ __('m_bookings.bookings.fields.hotel') }}:</dt>
                    <dd class="col-sm-8">{{ $hotelName }}</dd>
                  @endif

                  @if(!$hasHotel && $hasMeetingPoint && $meetingPointName)
                    <dt class="col-sm-4">{{ __('m_bookings.bookings.fields.meeting_point') }}:</dt>
                    <dd class="col-sm-8">{{ $meetingPointName }}</dd>
                  @endif

                  <dt class="col-sm-4">{{ __('m_bookings.bookings.fields.type') }}:</dt>
                  <dd class="col-sm-8">{{ optional($tour->tourType)->name ?? '—' }}</dd>
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
                  @foreach($categories as $cat)
                    <dt class="col-sm-6">{{ $cat['name'] }}:</dt>
                    <dd class="col-sm-6">{{ $cat['quantity'] }} × ${{ number_format($cat['price'], 2) }}</dd>
                  @endforeach

                  <dt class="col-sm-6"><strong>{{ __('m_bookings.details.total_persons') }}:</strong></dt>
                  <dd class="col-sm-6"><strong>{{ $totalPersons }}</strong></dd>
                </dl>
              </div>

              <div class="col-md-6">
                <dl class="row">
                  <dt class="col-sm-6">{{ __('m_bookings.details.subtotal') }}:</dt>
                  <dd class="col-sm-6">${{ number_format($subtotalSnap, 2) }}</dd>

                  @if($promoCode && $appliedAmount > 0)
                    <dt class="col-sm-6">
                      {{ $adjustLabel }}:
                      <span class="ms-1">
                        @if(!is_null($percentSnapshot))
                          <span class="badge bg-secondary">{{ number_format($percentSnapshot,0) }}%</span>
                        @elseif(!is_null($amountSnapshot))
                          <span class="badge bg-secondary">${{ number_format($amountSnapshot,2) }}</span>
                        @endif
                      </span>
                    </dt>
                    <dd class="col-sm-6 {{ $operation === 'add' ? 'text-primary' : 'text-success' }}">
                      {{ $sign }}${{ number_format($appliedAmount, 2) }}
                    </dd>

                    <dt class="col-sm-6">{{ __('m_bookings.bookings.fields.promo_code') }}:</dt>
                    <dd class="col-sm-6">
                      <span class="badge bg-success">{{ $promoCode }}</span>
                    </dd>
                  @endif

                  <dt class="col-sm-6"><strong>{{ __('m_bookings.bookings.fields.total') }}:</strong></dt>
                  <dd class="col-sm-6">
                    <strong class="text-success fs-5">${{ number_format($grandTotal, 2) }}</strong>
                  </dd>
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
<a href="{{ route('admin.bookings.edit', $booking) }}"
   class="btn btn-warning">
  <i class="fas fa-edit me-1"></i> {{ __('m_bookings.bookings.buttons.edit') }}
</a>


        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_bookings.bookings.buttons.close') }}</button>
      </div>
    </div>
  </div>
</div>
