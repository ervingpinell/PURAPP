{{-- resources/views/admin/bookings/show.blade.php --}}
@extends('adminlte::page')

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
$taxesTotal = (float) ($detail->taxes_total ?? 0);
$grandTotal = (float) ($booking->total ?? max(0, ($operation === 'add'
? $subtotalSnap + $appliedAmount
: $subtotalSnap - $appliedAmount) + $taxesTotal));

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

// Schedule info for validation
$scheduleStart = null;
$scheduleEnd = null;
$tourPeriod = null;
if ($detail?->schedule) {
$scheduleStart = \Carbon\Carbon::parse($detail->schedule->start_time);
$scheduleEnd = $detail->schedule->end_time ? \Carbon\Carbon::parse($detail->schedule->end_time) : null;
$tourPeriod = $scheduleStart->hour < 12 ? 'AM' : 'PM' ;
    }
    @endphp

    @section('title', 'Detalle de Reserva #' . ($booking->booking_reference ?? $booking->booking_id))

    @section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="m-0">
                <i class="fas fa-ticket-alt"></i>
                {{ __('m_bookings.bookings.ui.booking_details') }} #{{ $booking->booking_reference ?? $booking->booking_id }}
            </h1>
        </div>

        <div class="btn-group">
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('m_bookings.bookings.buttons.back') }}
            </a>
            <a href="{{ route('admin.bookings.receipt', $booking->booking_id) }}" class="btn btn-primary" target="_blank">
                <i class="fas fa-file-pdf"></i> {{ __('m_bookings.bookings.ui.download_receipt') }}
            </a>
            <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> {{ __('m_bookings.bookings.buttons.edit') }}
            </a>
            @if($booking->status === 'pending' && !$booking->isPaid())
            <button type="button" class="btn btn-success" id="btn-payment-link-show">
                <i class="fas fa-link"></i> {{ __('m_bookings.bookings.ui.payment_link') }}
            </button>
            @endif

        </div>
    </div>
    @stop

    @section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
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
                    $paymentText = __('m_bookings.bookings.payment_status.pending');

                    if ($booking->relationLoaded('payments') && $booking->payments->isNotEmpty()) {
                    $latestPayment = $booking->payments->sortByDesc('created_at')->first();
                    if ($latestPayment && $latestPayment->status === 'completed') {
                    $paymentStatus = 'paid';
                    $paymentBadgeClass = 'success';
                    $paymentText = __('m_bookings.bookings.payment_status.paid');
                    }
                    }
                    @endphp
                    <div>
                        <strong>{{ __('m_bookings.bookings.payment_status.label') }}:</strong>
                        <span class="badge badge-{{ $paymentBadgeClass }} ml-2">{{ $paymentText }}</span>
                        @if(isset($latestPayment))
                        <a href="{{ route('admin.payments.show', $latestPayment->payment_id) }}" class="btn btn-sm btn-info ml-2" title="View Payment Details">
                            <i class="fas fa-credit-card"></i> {{ __('m_bookings.bookings.ui.view_payment') }}
                        </a>
                        @if($latestPayment->card_brand && $latestPayment->card_last4)
                        <br><small class="text-muted ml-2">{{ ucfirst($latestPayment->card_brand) }} •••• {{ $latestPayment->card_last4 }}</small>
                        @endif
                        @endif
                    </div>
                    <small class="text">
                        {{ __('m_bookings.bookings.fields.booking_date') }}: {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}
                    </small>
                </div>
                <div class="btn-group btn-group-sm" role="group">
                    @if($booking->status !== 'confirmed')
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#confirmModal">
                        <i class="fas fa-check-circle"></i> {{ __('m_bookings.actions.confirm') }}
                    </button>
                    @endif
                    @if($booking->status !== 'cancelled')
                    <form action="{{ route('admin.bookings.update-status', $booking->booking_id) }}" method="POST" class="d-inline">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('{{ __('m_bookings.actions.confirm_cancel') }}')">
                            <i class="fas fa-times-circle"></i> {{ __('m_bookings.actions.cancel') }}
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Checkout Link Accordion for Pending Bookings --}}
            @if($booking->status === 'pending' && !$booking->isPaid())
            <div class="accordion mb-3" id="paymentLinkAccordion">
                <div class="card">
                    <div class="card-header p-2" id="headingPaymentLink">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapsePaymentLink" aria-expanded="false" aria-controls="collapsePaymentLink">
                                <i class="fas fa-link mr-2"></i>{{ __('m_bookings.bookings.checkout_link_label') }}
                                <i class="fas fa-chevron-down float-right mt-1"></i>
                            </button>
                        </h2>
                    </div>
                    <div id="collapsePaymentLink" class="collapse" aria-labelledby="headingPaymentLink" data-parent="#paymentLinkAccordion">
                        <div class="card-body">
                            <p class="mb-2">{{ __('m_bookings.bookings.checkout_link_description') }}</p>
                            <div class="input-group">
                                <input type="text" class="form-control" id="checkout-link"
                                    value="{{ $booking->getPaymentUrl() }}" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" id="copy-checkout-btn">
                                        <i class="fas fa-copy"></i> {{ __('m_bookings.bookings.checkout_link_copy') }}
                                    </button>
                                </div>
                            </div>

                            @php
                            $expirationHours = (int) \App\Models\Setting::getValue('booking.payment_link_expiration_hours', 2);
                            $tokenCreatedAt = $booking->payment_token_created_at ?? $booking->created_at;
                            $expiresAt = $tokenCreatedAt->copy()->addHours($expirationHours);
                            $isExpired = $expiresAt->isPast();
                            @endphp

                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                @if($isExpired)
                                <span class="text-danger">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ __('m_bookings.bookings.checkout_link_expired') }}
                                    ({{ __('m_bookings.bookings.checkout_link_valid_until') }}: {{ $expiresAt->format('M d, Y g:i A') }})
                                </span>
                                @else
                                {{ __('m_bookings.bookings.payment_link_info') ?? 'This payment link does not expire and can be used multiple times until the booking is paid.' }}
                                <br>
                                <strong>{{ __('m_bookings.bookings.checkout_link_valid_until') }}:</strong> {{ $expiresAt->format('M d, Y g:i A') }}
                                ({{ $expiresAt->diffForHumans() }})
                                @endif
                            </small>

                            {{-- Regenerate button (always available for unpaid bookings) --}}
                            <div class="mt-3">
                                <button type="button" class="btn btn-warning btn-sm" id="btn-regenerate-payment-link">
                                    <i class="fas fa-sync-alt"></i> {{ __('m_bookings.bookings.regenerate_payment_link') ?? 'Regenerate Payment Link' }}
                                </button>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ __('m_bookings.bookings.regenerate_warning') ?? 'Regenerating will invalidate the old link.' }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Compact Booking Details Table --}}
            <div class="card mb-2">
                <div class="card-body p-0">
                    <table class="table table-bordered table-striped table-sm mb-0">
                        <tbody>
                            <tr>
                                <td class="bg-light" style="width: 200px;"><strong>{{ __('m_bookings.bookings.fields.tour') }}</strong></td>
                                <td>{{ $tourName }}</td>
                            </tr>
                            <tr>
                                <td class="bg-light"><strong>{{ __('m_bookings.bookings.fields.tour_date') }}</strong></td>
                                <td>{{ optional($detail?->tour_date)?->format('Y-m-d') ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="bg-light"><strong>{{ __('m_bookings.bookings.fields.schedule') }}</strong></td>
                                <td>
                                    @if($detail?->schedule)
                                    {{ \Carbon\Carbon::parse($detail->schedule->start_time)->format('g:i A') }}
                                    @if($detail?->schedule?->end_time)
                                    - {{ \Carbon\Carbon::parse($detail->schedule->end_time)->format('g:i A') }}
                                    @endif
                                    @else
                                    —
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-light"><strong>{{ __('m_bookings.bookings.fields.language') }}</strong></td>
                                <td>{{ optional($detail?->tourLanguage)->name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="bg-light"><strong>{{ __('m_bookings.bookings.fields.customer') }}</strong></td>
                                <td>{{ $booking->user->full_name ?? $booking->user->name ?? '—' }} ({{ $booking->user->email ?? '—' }})</td>
                            </tr>
                            <tr>
                                <td class="bg-light"><strong>{{ __('m_bookings.bookings.fields.pickup_location') }}</strong></td>
                                <td>
                                    @if($hasHotel && $hotelName)
                                    {{ $hotelName }}
                                    @elseif(!$hasHotel && $hasMeetingPoint && $meetingPointName)
                                    {{ $meetingPointName }}
                                    @else
                                    {{ __('m_bookings.bookings.ui.no_pickup') }}
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Participants Table --}}
            <div class="card mb-2">
                <div class="card-header bg-secondary py-2">
                    <h3 class="card-title mb-0"><strong>{{ __('m_bookings.bookings.ui.participants') }}:</strong></h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('m_bookings.bookings.fields.category') }}</th>
                                <th class="text-center">{{ __('m_bookings.bookings.fields.quantity') }}</th>
                                <th class="text-right">{{ __('m_bookings.bookings.fields.price') }}</th>
                                <th class="text-right">{{ __('m_bookings.bookings.fields.subtotal') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $cat)
                            <tr>
                                <td>{{ $cat['name'] }}</td>
                                <td class="text-center">{{ $cat['quantity'] }}</td>
                                <td class="text-right">{{ $currency }}{{ number_format((float)$cat['price'], 2) }}</td>
                                <td class="text-right">{{ $currency }}{{ number_format($cat['total'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Price Breakdown --}}
            <div class="card mb-2">
                <div class="card-header bg-secondary py-2">
                    <h3 class="card-title mb-0"><strong>{{ __('Desglose de Precios') }}:</strong></h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-sm mb-0">
                        <tbody>
                            <tr>
                                <td class="bg-light" style="width: 200px;"><strong>{{ __('m_bookings.details.subtotal') }}</strong></td>
                                <td class="text-right">{{ $currency }}{{ number_format((float)($detail->total ?? $subtotalSnap), 2) }}</td>
                            </tr>
                            @if($promoCode && $appliedAmount > 0)
                            <tr>
                                <td class="bg-light">
                                    <strong>{{ $adjustLabel }}</strong>
                                    @if(!is_null($percentSnapshot))
                                    <span class="badge badge-secondary ml-1">{{ number_format($percentSnapshot,0) }}%</span>
                                    @elseif(!is_null($amountSnapshot))
                                    <span class="badge badge-secondary ml-1">{{ $currency }}{{ number_format($amountSnapshot,2) }}</span>
                                    @endif
                                </td>
                                <td class="text-right {{ $operation === 'add' ? 'text-danger' : 'text-success' }}">
                                    {{ $sign }}{{ $currency }}{{ number_format($appliedAmount, 2) }}
                                </td>
                            </tr>
                            @endif
                            {{-- Impuestos --}}
                            @if(!empty($detail->taxes_breakdown))
                            @foreach($detail->taxes_breakdown as $tax)
                            <tr>
                                <td class="bg-light">
                                    <strong>{{ $tax['name'] }}</strong>
                                    <small>({{ $tax['rate'] ?? 0 }}{{ ($tax['type'] ?? '') == 'percentage' ? '%' : '' }})</small>
                                </td>
                                <td class="text-right">+{{ $currency }}{{ number_format($tax['amount'], 2) }}</td>
                            </tr>
                            @endforeach
                            @endif
                            <tr class="bg-light">
                                <td><strong>{{ __('m_bookings.bookings.fields.total') }}</strong></td>
                                <td class="text-right"><strong style="font-size: 1.2em;">{{ $currency }}{{ number_format((float)$booking->total, 2) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Notes --}}
            @if($booking->notes)
            <div class="alert alert-info">
                <strong><i class="fas fa-sticky-note mr-2"></i>{{ __('m_bookings.bookings.fields.notes') }}:</strong>
                <p class="mb-0 mt-2">{{ $booking->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Confirm Booking Modal --}}
    @if($booking->status !== 'confirmed')
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="confirmModalLabel">
                        <i class="fas fa-check-circle mr-2"></i>{{ __('m_bookings.actions.confirm') }} {{ __('m_bookings.bookings.singular') }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.bookings.update-status', $booking->booking_id) }}" method="POST" id="confirmBookingForm">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="confirmed">

                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>{{ __('m_bookings.bookings.fields.booking_reference') }}:</strong> {{ $booking->booking_reference }}
                        </div>

                        {{-- Tour Schedule Info --}}
                        @if($scheduleStart)
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-clock mr-2"></i>
                            <strong>{{ __('m_bookings.bookings.fields.schedule') }}:</strong>
                            {{ $scheduleStart->format('g:i A') }}
                            @if($scheduleEnd)
                            - {{ $scheduleEnd->format('g:i A') }}
                            @endif
                            <span class="badge badge-{{ $tourPeriod === 'AM' ? 'info' : 'warning' }} ml-2">{{ $tourPeriod }} Tour</span>
                        </div>
                        @endif

                        {{-- Pickup Location --}}
                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt mr-1"></i>{{ __('m_bookings.bookings.fields.pickup_location') }}</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input pickup-radio" type="radio" name="pickup_type" id="pickup_hotel" value="hotel"
                                            {{ $booking->detail?->hotel_id || $booking->detail?->other_hotel_name ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="pickup_hotel">
                                            {{ __('m_bookings.bookings.ui.hotel_pickup') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input pickup-radio" type="radio" name="pickup_type" id="pickup_meeting" value="meeting_point"
                                            {{ $booking->detail?->meeting_point_id ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="pickup_meeting">
                                            {{ __('m_bookings.bookings.ui.meeting_point_pickup') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Hotel Selection --}}
                            <div id="hotel_section" class="mt-3" style="display: {{ $booking->detail?->hotel_id || $booking->detail?->other_hotel_name ? 'block' : 'none' }};">
                                <select name="hotel_id" class="form-control">
                                    <option value="">{{ __('m_bookings.bookings.placeholders.select_hotel') }}</option>
                                    @php
                                    $hotels = \App\Models\HotelList::where('is_active', true)->orderBy('name')->get();
                                    @endphp
                                    @foreach($hotels as $hotel)
                                    <option value="{{ $hotel->hotel_id }}" {{ $booking->detail?->hotel_id == $hotel->hotel_id ? 'selected' : '' }}>
                                        {{ $hotel->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <input type="text" name="other_hotel_name" class="form-control mt-2" placeholder="{{ __('m_bookings.bookings.placeholders.enter_hotel_name') }}"
                                    value="{{ $booking->detail?->other_hotel_name }}">
                            </div>

                            {{-- Meeting Point Selection --}}
                            <div id="meeting_section" class="mt-3" style="display: {{ $booking->detail?->meeting_point_id ? 'block' : 'none' }};">
                                <select name="meeting_point_id" class="form-control">
                                    <option value="">{{ __('m_bookings.bookings.placeholders.select_point') }}</option>
                                    @php
                                    $meetingPoints = \App\Models\MeetingPoint::where('is_active', true)
                                    ->with('translations')
                                    ->orderByRaw('sort_order IS NULL, sort_order ASC')
                                    ->get();
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
                        <div class="form-group">
                            <label for="pickup_time">
                                <i class="fas fa-clock mr-1"></i>{{ __('m_bookings.bookings.fields.pickup_time') }}
                                <span class="text-muted">{{ __('m_bookings.bookings.ui.optional') }}</span>
                            </label>
                            <input type="time"
                                class="form-control"
                                id="pickup_time"
                                name="pickup_time"
                                value="{{ $booking->detail?->pickup_time ? \Carbon\Carbon::parse($booking->detail->pickup_time)->format('H:i') : '' }}">
                            <small class="form-text text-muted">
                                {{ __('m_bookings.bookings.ui.pickup_info') }}
                            </small>
                            <div id="pickup_time_warning" class="alert alert-danger mt-2" style="display: none;">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                <span id="pickup_time_warning_text"></span>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <small>
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ __('m_bookings.bookings.ui.confirm_booking_alert') }}
                            </small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>{{ __('m_bookings.actions.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-success" id="confirm_btn">
                            <i class="fas fa-check-circle mr-1"></i>{{ __('m_bookings.actions.confirm') }} {{ __('m_bookings.bookings.singular') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endsection

    @section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing booking detail scripts');

            // Copy Checkout Link (Input Group Button)
            const copyCheckoutBtn = document.getElementById('copy-checkout-btn');
            if (copyCheckoutBtn) {
                copyCheckoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const input = document.getElementById('checkout-link');
                    if (input) {
                        input.select();
                        input.setSelectionRange(0, 99999);

                        navigator.clipboard.writeText(input.value).then(() => {
                            const originalHTML = this.innerHTML;
                            this.innerHTML = '<i class="fas fa-check"></i> {{ __("m_bookings.bookings.ui.copied") }}';
                            this.classList.remove('btn-primary');
                            this.classList.add('btn-success');

                            setTimeout(() => {
                                this.innerHTML = originalHTML;
                                this.classList.remove('btn-success');
                                this.classList.add('btn-primary');
                            }, 2000);
                        }).catch(err => {
                            console.error('Copy failed', err);
                            alert('{{ __("m_bookings.bookings.ui.copy_failed") }}');
                        });
                    }
                });
            }

            // Payment Link Handler for Show Page Header Button
            const headerLinkBtn = document.getElementById('btn-payment-link-show');
            if (headerLinkBtn) {
                headerLinkBtn.addEventListener('click', function() {
                    const checkoutLink = document.getElementById('checkout-link');
                    if (checkoutLink) {
                        navigator.clipboard.writeText(checkoutLink.value).then(() => {
                            const originalHTML = this.innerHTML;
                            this.innerHTML = '<i class="fas fa-check"></i> {{ __("m_bookings.bookings.checkout_link_copied") ?? "Copied!" }}';
                            this.classList.remove('btn-success');
                            this.classList.add('btn-info');

                            setTimeout(() => {
                                this.innerHTML = originalHTML;
                                this.classList.remove('btn-info');
                                this.classList.add('btn-success');
                            }, 2000);
                        }).catch(err => {
                            console.error('Copy failed', err);
                            alert('{{ __("m_bookings.bookings.ui.copy_failed") }}');
                        });
                    }
                });
            }

            // Regenerate Payment Link Handler
            const regenerateLinkBtn = document.getElementById('btn-regenerate-payment-link');
            if (regenerateLinkBtn) {
                regenerateLinkBtn.addEventListener('click', function() {
                    Swal.fire({
                        title: '{{ __("m_bookings.bookings.regenerate_payment_link") }}',
                        text: '{{ __("m_bookings.bookings.confirm_regenerate_payment_link") }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#f0ad4e',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '{{ __("m_bookings.bookings.regenerate_payment_link") }}',
                        cancelButtonText: '{{ __("Cancel") }}'
                    }).then((result) => {
                        if (!result.isConfirmed) {
                            return;
                        }

                        const btn = this;
                        const originalHTML = btn.innerHTML;
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("m_bookings.bookings.ui.regenerating") }}';

                        fetch('{{ route("admin.bookings.regenerate_payment_link", $booking) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Update the link input
                                    const checkoutLink = document.getElementById('checkout-link');
                                    if (checkoutLink) {
                                        checkoutLink.value = data.url;
                                    }

                                    // Show success message
                                    Swal.fire({
                                        title: '{{ __("Success") }}',
                                        text: data.message || '{{ __("m_bookings.bookings.payment_link_regenerated") }}',
                                        icon: 'success',
                                        confirmButtonColor: '#28a745'
                                    }).then(() => {
                                        // Reload page to update UI
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: '{{ __("Error") }}',
                                        text: data.message || 'Error regenerating link',
                                        icon: 'error',
                                        confirmButtonColor: '#dc3545'
                                    });
                                    btn.disabled = false;
                                    btn.innerHTML = originalHTML;
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    title: '{{ __("Error") }}',
                                    text: 'Error regenerating link',
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545'
                                });
                                btn.disabled = false;
                                btn.innerHTML = originalHTML;
                            });
                    });
                });
            }

            // Toggle Pickup Fields
            const pickupRadios = document.querySelectorAll('.pickup-radio');
            const hotelSection = document.getElementById('hotel_section');
            const meetingSection = document.getElementById('meeting_section');

            function togglePickupFields() {
                const hotelRadio = document.getElementById('pickup_hotel');
                const meetingRadio = document.getElementById('pickup_meeting');

                console.log('Toggle called - Hotel checked:', hotelRadio?.checked, 'Meeting checked:', meetingRadio?.checked);

                if (hotelRadio && hotelRadio.checked) {
                    if (hotelSection) hotelSection.style.display = 'block';
                    if (meetingSection) meetingSection.style.display = 'none';
                } else if (meetingRadio && meetingRadio.checked) {
                    if (hotelSection) hotelSection.style.display = 'none';
                    if (meetingSection) meetingSection.style.display = 'block';
                }
            }

            pickupRadios.forEach(radio => {
                radio.addEventListener('change', togglePickupFields);
            });

            // Initial check
            togglePickupFields();

            @if($scheduleStart)
            // Validate pickup time against tour schedule
            const pickupInput = document.getElementById('pickup_time');
            const warningDiv = document.getElementById('pickup_time_warning');
            const warningText = document.getElementById('pickup_time_warning_text');
            const confirmBtn = document.getElementById('confirm_btn');

            if (pickupInput) {
                const tourPeriod = '{{ $tourPeriod }}';

                pickupInput.addEventListener('change', function() {
                    if (!this.value) {
                        if (warningDiv) warningDiv.style.display = 'none';
                        if (confirmBtn) confirmBtn.disabled = false;
                        return;
                    }

                    const [hours, minutes] = this.value.split(':').map(Number);
                    const pickupPeriod = hours < 12 ? 'AM' : 'PM';

                    if (pickupPeriod !== tourPeriod) {
                        if (warningText) warningText.textContent = `{{ __('m_bookings.bookings.ui.pickup_warning', ['pickup' => '${pickupPeriod}', 'tour' => '${tourPeriod}']) }}`;
                        if (warningDiv) {
                            warningDiv.style.display = 'block';
                            warningDiv.classList.remove('alert-danger');
                            warningDiv.classList.add('alert-warning');
                        }
                        if (confirmBtn) confirmBtn.disabled = false;
                    } else {
                        if (warningDiv) warningDiv.style.display = 'none';
                        if (confirmBtn) confirmBtn.disabled = false;
                    }
                });
            }
            @endif
        });
    </script>
    @endsection