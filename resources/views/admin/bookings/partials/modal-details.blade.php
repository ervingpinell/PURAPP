{{-- resources/views/admin/bookings/partials/modal-details.blade.php --}}
@php
use App\Models\CustomerCategory;

$currency = config('app.currency_symbol', '$');
$locale = app()->getLocale();

// Resolver nombres de categorías (cache simple por request)
static $CAT_NAME_BY_ID = null;
static $CAT_NAME_BY_SLUG = null;

if ($CAT_NAME_BY_ID === null || $CAT_NAME_BY_SLUG === null) {
$allCats = CustomerCategory::active()->with('translations')->get();
$CAT_NAME_BY_ID = $allCats->mapWithKeys(function($c) use ($locale) {
return [$c->category_id => ($c->getTranslatedName($locale) ?: $c->slug ?: '')];
})->all();
$CAT_NAME_BY_SLUG = $allCats->filter(fn($c) => $c->slug)->mapWithKeys(function($c) use ($locale) {
$label = $c->getTranslatedName($locale);
if (!$label && $c->slug) {
$try = __('customer_categories.labels.' . $c->slug);
if ($try !== 'customer_categories.labels.' . $c->slug) $label = $try;
if (!$label) {
$try2 = __('m_tours.customer_categories.labels.' . $c->slug);
if ($try2 !== 'm_tours.customer_categories.labels.' . $c->slug) $label = $try2;
}
}
return [$c->slug => ($label ?: $c->slug)];
})->all();
}

$resolveCatName = function(array $cat) use ($CAT_NAME_BY_ID, $CAT_NAME_BY_SLUG) {
// 1) por id
$id = $cat['category_id'] ?? $cat['id'] ?? null;
if ($id && isset($CAT_NAME_BY_ID[$id]) && $CAT_NAME_BY_ID[$id]) {
return $CAT_NAME_BY_ID[$id];
}
// 2) por slug
$slug = $cat['slug'] ?? null;
if ($slug && isset($CAT_NAME_BY_SLUG[$slug]) && $CAT_NAME_BY_SLUG[$slug]) {
return $CAT_NAME_BY_SLUG[$slug];
}
// 3) por archivos de idioma (si vino el slug pero no está en mapa)
if ($slug) {
$tr = __('customer_categories.labels.' . $slug);
if ($tr !== 'customer_categories.labels.' . $slug) return $tr;
$tr2 = __('m_tours.customer_categories.labels.' . $slug);
if ($tr2 !== 'm_tours.customer_categories.labels.' . $slug) return $tr2;
}
// 4) fallback al snapshot
return $cat['name'] ?? $cat['category_name'] ?? __('m_bookings.bookings.fields.category');
};
@endphp

<div class="modal fade" id="modalDetails{{ $booking->booking_id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title">
          <i class="fas fa-info-circle me-2"></i>
          {{ __('m_bookings.bookings.ui.booking_details') }} #{{ $booking->booking_id }}
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('m_bookings.bookings.buttons.close') }}"></button>
      </div>

      <div class="modal-body">
        @php
        // ===== Datos base
        $detail = $booking->detail;
        $tour = $booking->tour;

        // Nombre del tour (live o snapshot) con i18n
        $liveName = optional($tour)->name;
        $snapName = $detail->tour_name_snapshot ?? ($booking->tour_name_snapshot ?? null);
        $tourName = $liveName
        ?? ($snapName
        ? __('m_bookings.bookings.messages.deleted_tour_snapshot', ['name' => $snapName])
        : __('m_bookings.bookings.messages.deleted_tour'));

        // ========== CATEGORÍAS DINÁMICAS ==========
        $categoriesData = [];
        $subtotalSnap = 0.0;
        $totalPersons = 0;

        if ($detail?->categories && is_string($detail->categories)) {
        try { $categoriesData = json_decode($detail->categories, true) ?: []; }
        catch (\Exception $e) {
        \Log::error('Error decoding categories JSON', ['booking_id' => $booking->booking_id, 'error' => $e->getMessage()]);
        }
        } elseif (is_array($detail?->categories)) {
        $categoriesData = $detail->categories;
        }

        $categories = [];
        if (!empty($categoriesData)) {
        // Lista
        if (isset($categoriesData[0]) && is_array($categoriesData[0])) {
        foreach ($categoriesData as $cat) {
        $qty = (int)($cat['quantity'] ?? 0);
        $price = (float)($cat['price'] ?? 0);
        if ($qty > 0) {
        $name = $resolveCatName($cat);
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
        } else {
        // Mapa
        foreach ($categoriesData as $catId => $cat) {
        $qty = (int)($cat['quantity'] ?? 0);
        $price = (float)($cat['price'] ?? 0);
        if ($qty > 0) {
        if (!isset($cat['category_id']) && is_numeric($catId)) $cat['category_id'] = (int)$catId;
        $name = $resolveCatName($cat);
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
        }

        // Fallback legacy (adults/kids)
        if (empty($categories)) {
        $adultsQty = (int)($detail->adults_quantity ?? 0);
        $kidsQty = (int)($detail->kids_quantity ?? 0);
        $adultPrice = (float)($detail->adult_price ?? $tour?->adult_price ?? 0);
        $kidPrice = (float)($detail->kid_price ?? $tour?->kid_price ?? 0);

        if ($adultsQty > 0) {
        $name = __('customer_categories.labels.adult');
        if ($name === 'customer_categories.labels.adult') $name = 'Adults';
        $categories[] = [
        'name' => $name,
        'quantity' => $adultsQty,
        'price' => $adultPrice,
        'total' => $adultsQty * $adultPrice
        ];
        $subtotalSnap += $adultsQty * $adultPrice;
        $totalPersons += $adultsQty;
        }

        if ($kidsQty > 0) {
        $name = __('customer_categories.labels.child');
        if ($name === 'customer_categories.labels.child') $name = 'Kids';
        $categories[] = [
        'name' => $name,
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
        $redemption = $booking->redemption;
        $promoModel = optional($redemption)->promoCode ?: $booking->promoCode;
        $promoCode = $promoModel?->code;

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
        $amountSnapshot = $redemption->amount_snapshot ?? $promoModel->discount_amount ?? null;

        // Etiqueta según operación
        $adjustLabel = $operation === 'add'
        ? __('m_config.promocode.operations.surcharge')
        : __('m_config.promocode.operations.discount');

        // Signo para el display
        $sign = $operation === 'add' ? '+' : '−';

        // Total (preferir el guardado si existe)
        $grandTotal = (float) ($booking->total ?? max(0, $operation === 'add'
        ? $subtotalSnap + $appliedAmount
        : $subtotalSnap - $appliedAmount));

        // ========== HOTEL O MEETING POINT ==========
        $hasHotel = !empty($detail?->hotel_id) || !empty($detail?->other_hotel_name);
        $hasMeetingPoint = !empty($detail?->meeting_point_id) || !empty($detail?->meeting_point_name);

        $hotelName = null;
        $meetingPointName = null;

        if ($hasHotel) {
        $hotelName = $detail?->other_hotel_name ?? optional($detail?->hotel)->name ?? '—';
        } elseif ($hasMeetingPoint) {
        $meetingPointName = $detail?->meeting_point_name ?? optional($detail?->meetingPoint)->name ?? '—';
        }

        // Hora de recogida formateada
        $pickupTime = $detail?->pickup_time
        ? \Carbon\Carbon::parse($detail->pickup_time)->format('g:i A')
        : null;
        @endphp

        {{-- Status Alert with Actions --}}
        <div class="alert alert-{{ $booking->status === 'pending' ? 'warning' : ($booking->status === 'confirmed' ? 'success' : 'danger') }} d-flex justify-content-between align-items-center">
          <div>
            <div class="mb-2">
              <strong>{{ __('m_bookings.bookings.fields.status') }}:</strong>
              <span class="ms-2">{{ __('m_bookings.bookings.statuses.' . $booking->status) }}</span>
            </div>
            @php
            // Determinar estado de pago
            $paymentStatus = 'pending';
            $paymentBadgeClass = 'warning';
            $paymentText = __('Pending');

            if ($booking->relationLoaded('payments') && $booking->payments->isNotEmpty()) {
            $latestPayment = $booking->payments->sortByDesc('created_at')->first();
            if ($latestPayment && $latestPayment->status === 'completed') {
            $paymentStatus = 'paid';
            $paymentBadgeClass = 'success';
            $paymentText = __('Paid');
            }
            }
            @endphp
            <div>
              <strong>{{ __('Payment Status') }}:</strong>
              <span class="badge bg-{{ $paymentBadgeClass }} ms-2">{{ $paymentText }}</span>
            </div>
          </div>
          <div class="btn-group btn-group-sm" role="group">
            @if($booking->status !== 'confirmed')
            <button type="button" class="btn btn-success btn-sm"
              data-bs-toggle="modal"
              data-bs-target="#confirmBookingModal{{ $booking->booking_id }}"
              title="{{ __('m_bookings.actions.confirm') }}">
              <i class="fas fa-check-circle"></i> {{ __('m_bookings.actions.confirm') }}
            </button>
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
                  <dd class="col-sm-8">{{ $booking->user->full_name ?? $booking->user->name ?? '—' }}</dd>

                  <dt class="col-sm-4">{{ __('m_bookings.bookings.fields.email') }}:</dt>
                  <dd class="col-sm-8">
                    @if(!empty($booking->user->email))
                    <a href="mailto:{{ $booking->user->email }}">{{ $booking->user->email }}</a>
                    @else
                    —
                    @endif
                  </dd>

                  <dt class="col-sm-4">{{ __('m_bookings.bookings.fields.phone') }}:</dt>
                  <dd class="col-sm-8">{{ $booking->user->phone ?? '—' }}</dd>
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
                  <dd class="col-sm-8">{{ optional($detail?->tour_date)?->format('M d, Y') ?? '—' }}</dd>

                  <dt class="col-sm-4">{{ __('m_bookings.bookings.fields.schedule') }}:</dt>
                  <dd class="col-sm-8">
                    @if($detail?->schedule)
                    {{ \Carbon\Carbon::parse($detail->schedule->start_time)->format('g:i A') }}
                    @if($detail?->schedule?->end_time)
                    - {{ \Carbon\Carbon::parse($detail->schedule->end_time)->format('g:i A') }}
                    @endif
                    @else
                    —
                    @endif
                  </dd>

                  {{-- Hora de recogida --}}
                  <dt class="col-sm-4">{{ __('m_bookings.bookings.fields.pickup_time') }}:</dt>
                  <dd class="col-sm-8">{{ $pickupTime ?? '—' }}</dd>

                  <dt class="col-sm-4">{{ __('m_bookings.bookings.fields.language') }}:</dt>
                  <dd class="col-sm-8">{{ optional($detail?->tourLanguage)->name ?? '—' }}</dd>
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
                  <dd class="col-sm-8">{{ optional($tour?->tourType)->name ?? '—' }}</dd>
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
                  <dd class="col-sm-6">{{ $cat['quantity'] }} × {{ $currency }}{{ number_format((float)$cat['price'], 2) }}</dd>
                  @endforeach

                  <dt class="col-sm-6"><strong>{{ __('m_bookings.details.total_persons') }}:</strong></dt>
                  <dd class="col-sm-6"><strong>{{ $totalPersons }}</strong></dd>
                </dl>
              </div>

              <div class="col-md-6">
                <dl class="row">
                  <dt class="col-sm-6">{{ __('m_bookings.details.subtotal') }}:</dt>
                  <dd class="col-sm-6">{{ $currency }}{{ number_format($subtotalSnap, 2) }}</dd>

                  @if($promoCode && $appliedAmount > 0)
                  <dt class="col-sm-6">
                    {{ $adjustLabel }}:
                    <span class="ms-1">
                      @if(!is_null($percentSnapshot))
                      <span class="badge bg-secondary">{{ number_format($percentSnapshot,0) }}%</span>
                      @elseif(!is_null($amountSnapshot))
                      <span class="badge bg-secondary">{{ $currency }}{{ number_format($amountSnapshot,2) }}</span>
                      @endif
                    </span>
                  </dt>
                  <dd class="col-sm-6 {{ $operation === 'add' ? 'text-danger' : 'text-success' }}">
                    {{ $sign }}{{ $currency }}{{ number_format($appliedAmount, 2) }}
                  </dd>

                  <dt class="col-sm-6">{{ __('m_bookings.bookings.fields.promo_code') }}:</dt>
                  <dd class="col-sm-6">
                    <span class="badge bg-success">{{ $promoCode }}</span>
                  </dd>
                  @endif

                  <dt class="col-sm-6"><strong>{{ __('m_bookings.bookings.fields.total') }}:</strong></dt>
                  <dd class="col-sm-6">
                    <strong class="text-success fs-5">{{ $currency }}{{ number_format($grandTotal, 2) }}</strong>
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
        <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-warning">
          <i class="fas fa-edit me-1"></i> {{ __('m_bookings.bookings.buttons.edit') }}
        </a>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          {{ __('m_bookings.bookings.buttons.close') }}
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Confirm Booking Modal --}}
<div class="modal fade" id="confirmBookingModal{{ $booking->booking_id }}" tabindex="-1" aria-labelledby="confirmBookingModalLabel{{ $booking->booking_id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('admin.bookings.update-status', $booking->booking_id) }}" method="POST" id="confirmBookingForm{{ $booking->booking_id }}">
        @csrf @method('PATCH')
        <input type="hidden" name="status" value="confirmed">

        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="confirmBookingModalLabel{{ $booking->booking_id }}">
            <i class="fas fa-check-circle me-2"></i>{{ __('m_bookings.actions.confirm') }} {{ __('m_bookings.bookings.singular') }}
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>{{ __('m_bookings.bookings.fields.booking_reference') }}:</strong> {{ $booking->booking_reference }}
          </div>

          {{-- Tour Schedule Info --}}
          @if($detail?->schedule)
          @php
          $scheduleStart = \Carbon\Carbon::parse($detail->schedule->start_time);
          $scheduleEnd = $detail->schedule->end_time ? \Carbon\Carbon::parse($detail->schedule->end_time) : null;
          $tourPeriod = $scheduleStart->hour < 12 ? 'AM' : 'PM' ;
            @endphp
            <div class="alert alert-warning mb-3">
            <i class="fas fa-clock me-2"></i>
            <strong>{{ __('m_bookings.bookings.fields.schedule') }}:</strong>
            {{ $scheduleStart->format('g:i A') }}
            @if($scheduleEnd)
            - {{ $scheduleEnd->format('g:i A') }}
            @endif
            <span class="badge bg-{{ $tourPeriod === 'AM' ? 'info' : 'warning' }} ms-2">{{ $tourPeriod }} Tour</span>
        </div>
        @endif

        {{-- Pickup Location --}}
        <div class="mb-3">
          <label class="form-label">
            <i class="fas fa-map-marker-alt me-1"></i>{{ __('Pickup Location') }}
          </label>
          <div class="row">
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="pickup_type" id="pickup_hotel_{{ $booking->booking_id }}" value="hotel"
                  {{ $booking->detail?->hotel_id || $booking->detail?->other_hotel_name ? 'checked' : '' }}
                  onchange="togglePickupFields{{ $booking->booking_id }}()">
                <label class="form-check-label" for="pickup_hotel_{{ $booking->booking_id }}">
                  {{ __('Hotel Pickup') }}
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="pickup_type" id="pickup_meeting_{{ $booking->booking_id }}" value="meeting_point"
                  {{ $booking->detail?->meeting_point_id ? 'checked' : '' }}
                  onchange="togglePickupFields{{ $booking->booking_id }}()">
                <label class="form-check-label" for="pickup_meeting_{{ $booking->booking_id }}">
                  {{ __('Meeting Point') }}
                </label>
              </div>
            </div>
          </div>

          {{-- Hotel Selection --}}
          <div id="hotel_section_{{ $booking->booking_id }}" class="mt-2" style="display: {{ $booking->detail?->hotel_id || $booking->detail?->other_hotel_name ? 'block' : 'none' }};">
            <select name="hotel_id" class="form-select">
              <option value="">{{ __('Select Hotel') }}</option>
              @php
              $hotels = \App\Models\HotelList::where('is_active', true)->orderBy('name')->get();
              @endphp
              @foreach($hotels as $hotel)
              <option value="{{ $hotel->hotel_id }}" {{ $booking->detail?->hotel_id == $hotel->hotel_id ? 'selected' : '' }}>
                {{ $hotel->name }}
              </option>
              @endforeach
            </select>
            <input type="text" name="other_hotel_name" class="form-control mt-2" placeholder="{{ __('Or enter hotel name') }}"
              value="{{ $booking->detail?->other_hotel_name }}">
          </div>

          {{-- Meeting Point Selection --}}
          <div id="meeting_section_{{ $booking->booking_id }}" class="mt-2" style="display: {{ $booking->detail?->meeting_point_id ? 'block' : 'none' }};">
            <select name="meeting_point_id" class="form-select">
              <option value="">{{ __('Select Meeting Point') }}</option>
              @php
              $meetingPoints = \App\Models\MeetingPoint::where('is_active', true)->orderBy('name')->get();
              @endphp
              @foreach($meetingPoints as $mp)
              <option value="{{ $mp->meeting_point_id }}" {{ $booking->detail?->meeting_point_id == $mp->meeting_point_id ? 'selected' : '' }}>
                {{ $mp->name }}
              </option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- Pickup Time --}}
        <div class="mb-3">
          <label for="pickup_time_{{ $booking->booking_id }}" class="form-label">
            <i class="fas fa-clock me-1"></i>{{ __('m_bookings.bookings.fields.pickup_time') }}
            <span class="text-muted">({{ __('Optional') }})</span>
          </label>
          <input type="time"
            class="form-control"
            id="pickup_time_{{ $booking->booking_id }}"
            name="pickup_time"
            value="{{ $booking->detail?->pickup_time ? \Carbon\Carbon::parse($booking->detail->pickup_time)->format('H:i') : '' }}">
          <small class="form-text text-muted">
            {{ __('Set the pickup time for this booking.') }}
          </small>
          <div id="pickup_time_warning_{{ $booking->booking_id }}" class="alert alert-danger mt-2" style="display: none;">
            <i class="fas fa-exclamation-triangle me-1"></i>
            <span id="pickup_time_warning_text_{{ $booking->booking_id }}"></span>
          </div>
        </div>

        <div class="alert alert-warning">
          <small>
            <i class="fas fa-exclamation-triangle me-1"></i>
            {{ __('Confirming this booking will send a confirmation email to the customer.') }}
          </small>
        </div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        <i class="fas fa-times me-1"></i>{{ __('Cancel') }}
      </button>
      <button type="submit" class="btn btn-success" id="confirm_btn_{{ $booking->booking_id }}">
        <i class="fas fa-check-circle me-1"></i>{{ __('m_bookings.actions.confirm') }} {{ __('m_bookings.bookings.singular') }}
      </button>
    </div>
    </form>
  </div>
</div>
</div>

<script>
  function togglePickupFields {
    {
      $booking->booking_id
    }
  }() {
    const hotelRadio = document.getElementById('pickup_hotel_{{ $booking->booking_id }}');
    const meetingRadio = document.getElementById('pickup_meeting_{{ $booking->booking_id }}');
    const hotelSection = document.getElementById('hotel_section_{{ $booking->booking_id }}');
    const meetingSection = document.getElementById('meeting_section_{{ $booking->booking_id }}');

    if (hotelRadio.checked) {
      hotelSection.style.display = 'block';
      meetingSection.style.display = 'none';
    } else if (meetingRadio.checked) {
      hotelSection.style.display = 'none';
      meetingSection.style.display = 'block';
    }
  }

  @if($detail ?->schedule)
  // Validate pickup time against tour schedule
  (function() {
    const pickupInput = document.getElementById('pickup_time_{{ $booking->booking_id }}');
    const warningDiv = document.getElementById('pickup_time_warning_{{ $booking->booking_id }}');
    const warningText = document.getElementById('pickup_time_warning_text_{{ $booking->booking_id }}');
    const confirmBtn = document.getElementById('confirm_btn_{{ $booking->booking_id }}');

    const tourStartHour = {
      {
        $scheduleStart->hour
      }
    };
    const tourPeriod = '{{ $tourPeriod }}';

    pickupInput.addEventListener('change', function() {
      if (!this.value) {
        warningDiv.style.display = 'none';
        confirmBtn.disabled = false;
        return;
      }

      const [hours, minutes] = this.value.split(':').map(Number);
      const pickupPeriod = hours < 12 ? 'AM' : 'PM';

      if (pickupPeriod !== tourPeriod) {
        warningText.textContent = `Warning: Pickup time is ${pickupPeriod} but tour starts at ${tourPeriod}. Please verify this is correct.`;
        warningDiv.style.display = 'block';
        warningDiv.classList.remove('alert-danger');
        warningDiv.classList.add('alert-warning');
        confirmBtn.disabled = false;
      } else {
        warningDiv.style.display = 'none';
        confirmBtn.disabled = false;
      }
    });
  })();
  @endif
</script>
