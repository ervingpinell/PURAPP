@extends('layouts.app')

@section('title', __('adminlte::adminlte.myCart'))

@push('styles')
<style>
  /* ===== Desglose bonito ===== */
  .cat-line {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
    font-size: .92rem
  }

  .cat-left {
    display: flex;
    align-items: center;
    gap: .5rem
  }

  .cat-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.6rem;
    height: 1.35rem;
    padding: 0 .4rem;
    font-size: .75rem;
    border-radius: .25rem;
    background: #f1f3f5;
    color: #212529
  }

  .cat-name {
    font-weight: 500
  }

  .cat-sub {
    white-space: nowrap;
    font-weight: 600
  }
</style>
@endpush

@section('content')
@php
// --- Fallback Meeting Points si el controlador no los pasó ---
$meetingPoints = $meetingPoints
?? \App\Models\MeetingPoint::where('is_active', true)
->with('translations')
->orderByRaw('sort_order IS NULL, sort_order ASC')
->orderBy('name', 'asc')
->get();

// JSON para data-attributes (ya traducido)
$mpListJson = ($meetingPoints ?? collect())
->map(fn($mp) => [
'id' => $mp->id,
'name' => $mp->getTranslated('name'),
'pickup_time' => $mp->pickup_time,
'description' => $mp->getTranslated('description'),
'map_url' => $mp->map_url,
])->values()->toJson();

$pickupLabel = __('adminlte::adminlte.pickupTime');

// Columnas condicionales
$showHotelColumn = ($cart && $cart->items)
? $cart->items->contains(fn($it) => $it->hotel || $it->is_other_hotel || $it->other_hotel_name)
: false;

$showMeetingPointColumn = ($cart && $cart->items)
? $cart->items->contains(fn($it) => !$it->hotel && !$it->is_other_hotel && ($it->meeting_point_id))
: false;

// Timer config
$expiryMinutes = (int) \App\Models\Setting::getValue('cart.expiration_minutes', 30);

// Promo en sesión
$promoSession = session('public_cart_promo');

// ==== Helpers de dinero ====
$fmt2 = fn ($n) => number_format((float)$n, 2, '.', '');

// ======================================================
// Mapa: category_id -> nombre traducido (para el carrito)
// ======================================================
$loc = app()->getLocale();
$fb = config('app.fallback_locale', 'es');

$categoryIdsInCart = collect($cart?->items ?? [])
->flatMap(function($it){
$cats = collect($it->categories ?? []);
return $cats->pluck('category_id')->filter();
})
->unique()->values();

$categoryNamesById = collect();
if ($categoryIdsInCart->isNotEmpty()) {
$catModels = \App\Models\CustomerCategory::whereIn('category_id', $categoryIdsInCart)
->with('translations')
->get();

$categoryNamesById = $catModels->mapWithKeys(function($c) use ($loc, $fb) {
$name = method_exists($c, 'getTranslated')
? ($c->getTranslated('name') ?? $c->name)
: (optional($c->translations->firstWhere('locale', $loc))->name
?? optional($c->translations->firstWhere('locale', $fb))->name
?? $c->name);
return [$c->category_id => $name];
});
}

// ==== Resolutor de categorías (nombre, qty, price, subtotal) =====
$catLinesFn = function($it) use ($categoryNamesById) {
// map opcional para códigos conocidos → label traducido
$codeMap = [
'adult' => __('adminlte::adminlte.adult'),
'adults' => __('adminlte::adminlte.adults'),
'kid' => __('adminlte::adminlte.kid'),
'kids' => __('adminlte::adminlte.kids'),
'child' => __('adminlte::adminlte.kid'),
'children' => __('adminlte::adminlte.kids'),
'senior' => __('adminlte::adminlte.senior') ?? 'Senior',
'student' => __('adminlte::adminlte.student') ?? 'Student',
];

$cats = collect($it->categories ?? []);
if ($cats->isNotEmpty()) {
return $cats->map(function($c) use ($codeMap, $categoryNamesById) {
$q = (int) data_get($c, 'quantity', 0);
$p = (float) data_get($c, 'price', 0);

// ID y slug para posibles resoluciones
$cid = (int) (data_get($c, 'category_id') ?? data_get($c, 'id') ?? 0);
$slug = (string) (data_get($c, 'category_slug') ?? data_get($c, 'slug') ?? '');

$name = null;

// 1. Prioridad: Traducción fresca desde BD (si existe)
if ($cid && $categoryNamesById->has($cid)) {
$name = $categoryNamesById->get($cid);
}

// 2. Fallback: Datos del snapshot del carrito
if (!$name) {
$name = data_get($c, 'i18n_name') ??
data_get($c, 'name') ??
data_get($c, 'label') ??
data_get($c, 'category_name') ??
data_get($c, 'category.name');
}

// 3) Si no, resolver por 'code' con mapa/traducción
if (!$name) {
$code = data_get($c, 'code');
if ($code) {
if (isset($codeMap[$code])) {
$name = $codeMap[$code];
} else {
$tr = __($code);
$name = ($tr === $code) ? (string)$code : $tr;
}
}
}

// 4) Si tampoco, un "bonito" del slug
if (!$name && $slug) {
$name = \Illuminate\Support\Str::of($slug)->replace(['_','-'],' ')->title();
}

if (!$name) $name = 'Category';

return [
'name' => (string) $name,
'quantity' => $q,
'price' => $p,
'subtotal' => $p * $q,
];
})->filter(fn($c) => $c['quantity'] > 0)->values();
}

// Fallback legacy: adultos/niños
$fallback = [
[
'name' => __('adminlte::adminlte.adults'),
'quantity' => (int)($it->adults_quantity ?? 0),
'price' => (float)($it->tour->adult_price ?? 0),
],
[
'name' => __('adminlte::adminlte.kids'),
'quantity' => (int)($it->kids_quantity ?? 0),
'price' => (float)($it->tour->kid_price ?? 0),
],
];
return collect($fallback)->map(function($c){
$c['subtotal'] = $c['price'] * $c['quantity'];
return $c;
})->filter(fn($c) => $c['quantity'] > 0)->values();
};

// ==== Subtotal item (sumando categorías) ====
$itemSubtotalFn = fn($it) => (float) collect($catLinesFn($it))->sum('subtotal');

// ==== Total carrito (con promo si aplica) ====
$rawTotal = $cart
? (float) $cart->items->sum(fn($it) => $itemSubtotalFn($it))
: 0.0;

$total = $rawTotal;
if ($promoSession) {
$op = (($promoSession['operation'] ?? 'subtract') === 'add') ? 1 : -1;
$total = max(0, round($rawTotal + $op * (float)($promoSession['adjustment'] ?? 0), 2));
}

// Notas iniciales (si el carrito ya las tiene o viene de old())
$initialNotes = old('notes', $cart->notes ?? '');
@endphp

{{-- ========== TIMER ========== --}}
@if($cart && $cart->items->count() && !empty($expiresAtIso) && (($cart->is_guest_cart ?? false) || ($cart->is_active && !$cart->isExpired())))
<div id="cart-timer"
  class="brand-timer shadow-sm"
  role="alert"
  data-expires-at="{{ $expiresAtIso }}"
  data-total-minutes="{{ $expiryMinutes }}"
  data-expire-endpoint="{{ ($cart->is_guest_cart ?? false) ? route('public.guest-carts.expire') : route('public.carts.expire') }}">
  <div class="brand-timer-head">
    <div class="brand-timer-icon">
      <i class="fas fa-hourglass-half"></i>
    </div>
    <div class="brand-timer-text">
      <div class="brand-timer-title">{{ __('carts.timer.will_expire') }}</div>
      <div class="brand-timer-sub">
        {{ __('carts.timer.time_left') }}
        <span id="cart-timer-remaining" class="brand-timer-remaining">--:--</span>
      </div>
    </div>
  </div>
  <div class="brand-timer-bar">
    <div class="brand-timer-bar-fill" id="cart-timer-bar" style="width:100%"></div>
  </div>
</div>
@endif

<div class="container py-5 mb-5" id="mp-config" data-pickup-label="{{ $pickupLabel }}">

  <h1 class="mb-4 d-flex align-items-center">
    <i class="fas fa-shopping-cart me-2"></i>
    {{ __('adminlte::adminlte.myCart') }}
  </h1>

  {{-- Errores de validación --}}
  @if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  {{-- Toasts / Payment Error SweetAlerts --}}
  @php
  // Check for payment error from URL query params
  $paymentError = request('error');
  $paymentCancelled = request('cancelled');

  // Get classification from URL if available (set by webhook)
  $paymentClassification = request('classification') ?? 'error';

  // Determine icon and title based on classification
  $alertIcon = 'error';
  $alertTitle = __('adminlte::adminlte.error');

  if ($paymentCancelled) {
  $alertIcon = 'warning';
  $alertTitle = __('m_checkout.payment.operation_cancelled', [], app()->getLocale()) ?? 'Operación Cancelada';
  $paymentError = __('m_checkout.payment.cancelled_by_user', [], app()->getLocale()) ?? 'El pago fue cancelado';
  } elseif ($paymentClassification === 'denied') {
  $alertTitle = __('m_checkout.payment.operation_denied', [], app()->getLocale()) ?? 'Operación Denegada';
  } elseif ($paymentClassification === 'rejected') {
  $alertTitle = __('m_checkout.payment.operation_rejected', [], app()->getLocale()) ?? 'Operación Rechazada';
  }
  @endphp

  @if (session('success') || session('error') || $paymentError || $paymentCancelled)
  @once
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @endonce
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      @if(session('success'))
      Swal.fire({
        icon: 'success',
        title: @json(__('adminlte::adminlte.success')),
        text: @json(session('success')),
        confirmButtonColor: '#198754',
        allowOutsideClick: false
      });
      @endif

      @if(session('error'))
      Swal.fire({
        icon: 'error',
        title: @json(__('adminlte::adminlte.error')),
        text: @json(session('error')),
        confirmButtonColor: '#dc3545',
        allowOutsideClick: false
      });
      @endif

      @if($paymentError || $paymentCancelled)
      Swal.fire({
        icon: @json($alertIcon),
        title: @json($alertTitle),
        text: @json($paymentError ?? ''),
        confirmButtonColor: @json($paymentCancelled ? '#f0ad4e' : '#dc3545'),
        allowOutsideClick: false
      }).then(() => {
        // Clean URL after showing alert (remove query params)
        if (window.history.replaceState) {
          const cleanUrl = window.location.pathname;
          window.history.replaceState({}, document.title, cleanUrl);
        }
      });
      @endif
    });
  </script>
  @endif

  @if($cart && $cart->items->count())

  {{-- Tabla (desktop) --}}
  <div class="table-responsive d-none d-md-block mb-4">
    <table class="table table-bordered table-striped table-hover align-middle">
      <thead>
        <tr class="text-center">
          <th>{{ __('adminlte::adminlte.tour') }}</th>
          <th>{{ __('adminlte::adminlte.date') }}</th>
          <th>{{ __('adminlte::adminlte.schedule') }}</th>
          <th>{{ __('adminlte::adminlte.language') }}</th>
          <th>{{ __('adminlte::adminlte.breakdown') }}</th>
          @if($showHotelColumn)
          <th>{{ __('adminlte::adminlte.hotel') }}</th>
          @endif
          @if($showMeetingPointColumn)
          <th>{{ __('adminlte::adminlte.meeting_point') }}</th>
          @endif
          <th>{{ __('adminlte::adminlte.subtotal') }}</th>
          <th>{{ __('adminlte::adminlte.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($cart->items as $item)
        @php
        $lines = $catLinesFn($item);
        $itemSubtotal = $itemSubtotalFn($item);
        @endphp
        <tr class="text-center cart-item-row"
          data-item-id="{{ $item->item_id }}"
          data-subtotal="{{ $fmt2($itemSubtotal) }}">
          <td class="text-start">{{ $item->tour->getTranslatedName(app()->getLocale()) ?? $item->tour->name }}</td>
          <td>{{ \Carbon\Carbon::parse($item->tour_date)->format('d/M/Y') }}</td>
          <td>
            @if($item->schedule)
            {{ \Carbon\Carbon::parse($item->schedule->start_time)->format('g:i A') }} -
            {{ \Carbon\Carbon::parse($item->schedule->end_time)->format('g:i A') }}
            @else
            {{ __('adminlte::adminlte.noSchedule') }}
            @endif
          </td>
          <td>{{ $item->language?->name ?? __('adminlte::adminlte.notSpecified') }}</td>

          {{-- Breakdown categorías --}}
          <td class="text-start">
            @foreach($lines as $L)
            <div class="cat-line">
              <div class="cat-left">
                <span class="cat-badge">{{ $L['quantity'] }}x</span>
                <span class="cat-name">{{ $L['name'] }}</span>
              </div>
              <div class="cat-sub">${{ $fmt2($L['subtotal']) }}</div>
            </div>
            @endforeach
          </td>

          @if($showHotelColumn)
          <td class="text-start">
            @if($item->is_other_hotel && $item->other_hotel_name)
            {{ $item->other_hotel_name }} <small class="text-muted">({{ __('adminlte::adminlte.custom') }})</small>
            @elseif($item->hotel)
            {{ $item->hotel->name }}
            @endif
          </td>
          @endif

          @if($showMeetingPointColumn)
          <td class="text-start">
            @php $mp = $item->meetingPoint; @endphp
            @if(!$item->hotel && !$item->is_other_hotel && $mp)
            <div class="fw-semibold">{{ $mp->getTranslated('name') }}</div>
            @if($mp->pickup_time)
            <div class="small text-muted">
              {{ __('adminlte::adminlte.pickupTime') }}: {{ $mp->pickup_time }}
            </div>
            @endif
            @php $mpDesc = $mp->getTranslated('description'); @endphp
            @if($mpDesc)
            <div class="small text-muted">
              <i class="fas fa-map-marker-alt me-1"></i>{{ $mpDesc }}
            </div>
            @endif
            @if($mp->map_url)
            <a href="{{ $mp->map_url }}" target="_blank" class="small">
              <i class="fas fa-external-link-alt me-1"></i>{{ __('adminlte::adminlte.openMap') }}
            </a>
            @endif
            @endif
          </td>
          @endif

          <td class="fw-bold">${{ $fmt2($itemSubtotal) }}</td>

          <td class="text-nowrap">
            <button type="button"
              class="btn btn-sm btn-primary me-1"
              data-bs-toggle="modal"
              data-bs-target="#editItemModal-{{ $item->item_id }}">
              <i class="fas fa-edit"></i> {{ __('adminlte::adminlte.edit') }}
            </button>

            @if(isset($cart->is_guest_cart) && $cart->is_guest_cart)
            <form action="{{ route('public.carts.removeGuestItem') }}" method="POST" class="d-inline delete-item-form">
              @csrf
              <input type="hidden" name="item_index" value="{{ $item->item_id }}">
              <button type="submit" class="btn btn-danger btn-sm">
                <i class="fas fa-trash"></i> {{ __('adminlte::adminlte.delete') }}
              </button>
            </form>
            @else
            <form action="{{ route('public.carts.destroy', $item->item_id) }}"
              method="POST"
              class="d-inline delete-item-form">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm">
                <i class="fas fa-trash"></i> {{ __('adminlte::adminlte.delete') }}
              </button>
            </form>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Tarjetas (móvil) --}}
  <div class="d-block d-md-none">
    @foreach($cart->items as $item)
    @php
    $lines = $catLinesFn($item);
    $itemSubtotal = $itemSubtotalFn($item);
    $showHotelInCard = ($item->is_other_hotel && $item->other_hotel_name) || $item->hotel;
    $showMpInCard = !$item->hotel && !$item->is_other_hotel && $item->meeting_point_id;
    @endphp
    <div class="card mb-3 shadow-sm cart-item-card"
      data-item-id="{{ $item->item_id }}"
      data-subtotal="{{ $fmt2($itemSubtotal) }}">
      <div class="card-header fw-semibold">
        {{ $item->tour->getTranslatedName(app()->getLocale()) ?? $item->tour->name }}
      </div>
      <div class="card-body">
        <div class="mb-2">
          <i class="far fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($item->tour_date)->format('d/M/Y') }}
          @if($item->schedule)
          <span class="ms-2"><i class="fas fa-clock me-1"></i>
            {{ \Carbon\Carbon::parse($item->schedule->start_time)->format('g:i A') }}
            – {{ \Carbon\Carbon::parse($item->schedule->end_time)->format('g:i A') }}
          </span>
          @endif
        </div>
        <div class="mb-2"><strong>{{ __('adminlte::adminlte.language') }}:</strong> {{ $item->language?->name ?? __('adminlte::adminlte.notSpecified') }}</div>

        {{-- Breakdown categorías (card) --}}
        <div class="mb-2">
          <strong>{{ __('adminlte::adminlte.breakdown') }}:</strong>
          <div class="mt-2">
            @foreach($lines as $L)
            <div class="cat-line">
              <div class="cat-left">
                <span class="cat-badge">{{ $L['quantity'] }}x</span>
                <span class="cat-name">{{ $L['name'] }}</span>
              </div>
              <div class="cat-sub">${{ $fmt2($L['subtotal']) }}</div>
            </div>
            @endforeach
          </div>
        </div>

        @if($showHotelInCard)
        <div class="mb-3"><strong>{{ __('adminlte::adminlte.hotel') }}:</strong>
          @if($item->is_other_hotel && $item->other_hotel_name)
          {{ $item->other_hotel_name }} <small class="text-muted">({{ __('adminlte::adminlte.custom') }})</small>
          @elseif($item->hotel)
          {{ $item->hotel->name }}
          @endif
        </div>
        @endif

        @if($showMpInCard)
        @php $mp = $item->meetingPoint; @endphp
        @if($mp)
        <div class="mb-3"><strong>{{ __('adminlte::adminlte.meeting_point') }}:</strong>
          <div>{{ $mp->getTranslated('name') }}</div>
          @if($mp->pickup_time)
          <div class="small text-muted">
            {{ __('adminlte::adminlte.pickupTime') }}: {{ $mp->pickup_time }}
          </div>
          @endif
          @php $mpDesc = $mp->getTranslated('description'); @endphp
          @if($mpDesc)
          <div class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $mpDesc }}</div>
          @endif
          @if($mp->map_url)
          <a href="{{ $mp->map_url }}" class="small" target="_blank">
            <i class="fas fa-external-link-alt me-1"></i>{{ __('adminlte::adminlte.openMap') }}
          </a>
          @endif
        </div>
        @endif
        @endif

        <div class="d-flex justify-content-between align-items-center border-top pt-2">
          <span class="fw-semibold">{{ __('adminlte::adminlte.subtotal') }}</span>
          <span class="fw-bold">${{ $fmt2($itemSubtotal) }}</span>
        </div>

        <div class="d-grid gap-2 mt-3">
          <button type="button"
            class="btn btn-success"
            data-bs-toggle="modal"
            data-bs-target="#editItemModal-{{ $item->item_id }}">
            <i class="fas fa-edit"></i> {{ __('adminlte::adminlte.edit') }}
          </button>

          @if(isset($cart->is_guest_cart) && $cart->is_guest_cart)
          <form action="{{ route('public.carts.removeGuestItem') }}" method="POST" class="delete-item-form">
            @csrf
            <input type="hidden" name="item_index" value="{{ $item->item_id }}">
            <button type="submit" class="btn btn-danger">
              <i class="fas fa-trash"></i> {{ __('adminlte::adminlte.delete') }}
            </button>
          </form>
          @else
          <form action="{{ route('public.carts.destroy', $item->item_id) }}"
            method="POST"
            class="delete-item-form">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">
              <i class="fas fa-trash"></i> {{ __('adminlte::adminlte.delete') }}
            </button>
          </form>
          @endif
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Total + Promo code --}}
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      @php
      // Use the centralized Cart::calculateTotal() method for authenticated users
      // For guests, calculate manually from items
      if (isset($cart->is_guest_cart) && $cart->is_guest_cart) {
      $calculatedTotal = $cart->items->sum(function($item) {
      return collect($item->categories ?? [])->sum(function($cat) {
      return ((float)($cat['price'] ?? 0)) * ((int)($cat['quantity'] ?? 0));
      });
      });
      } else {
      $calculatedTotal = $cart->calculateTotal();
      }
      $displaySubtotal = $calculatedTotal;
      @endphp

      {{-- Subtotal --}}
      <div class="d-flex justify-content-between mb-2">
        <span class="fw-semibold">{{ __('adminlte::adminlte.subtotal') }}:</span>
        <span class="fw-semibold">${{ number_format($displaySubtotal, 2) }}</span>
      </div>

      {{-- Promo Code Adjustment --}}
      @if($promoSession)
      <div id="promo-discount-line" class="d-flex justify-content-between text-{{ $promoSession['operation'] === 'add' ? 'danger' : 'success' }} mb-2">
        <span>
          <i class="fas fa-tag"></i> {{ $promoSession['code'] ?? 'PROMO' }}
        </span>
        <span>
          {{ $promoSession['operation'] === 'add' ? '+' : '-' }}${{ number_format($promoSession['adjustment'] ?? 0, 2) }}
        </span>
      </div>
      @endif

      @php
      // Apply promo to calculated total
      // CRITICAL: operation 'add' means ADD to the cart total (like a service fee)
      // operation 'subtract' means DISCOUNT from the cart total
      if ($promoSession) {
      $adjustment = (float)($promoSession['adjustment'] ?? 0);
      $operation = $promoSession['operation'] ?? 'subtract';

      if ($operation === 'add') {
      // Add to total (like a fee)
      $calculatedTotal = round($calculatedTotal + $adjustment, 2);
      } else {
      // Subtract from total (discount)
      $calculatedTotal = max(0, round($calculatedTotal - $adjustment, 2));
      }
      }
      @endphp

      {{-- Total --}}
      <div class="border-top pt-3 mt-2">
        <h4 class="mb-3">
          <strong>{{ __('adminlte::adminlte.totalEstimated') }}:</strong>
          <span class="currency-symbol">$</span>
          <span id="cart-total" class="brand-total">{{ number_format($calculatedTotal, 2) }}</span>
        </h4>
      </div>

      <label for="promo-code" class="form-label fw-semibold">{{ __('adminlte::adminlte.promoCode') }}</label>
      <div class="d-flex flex-column flex-sm-row gap-2">
        <input
          type="text"
          id="promo-code"
          name="promo_code"
          class="form-control"
          placeholder="{{ __('adminlte::adminlte.promoCodePlaceholder') }}"
          value="{{ $promoSession['code'] ?? '' }}">
        <button
          type="button"
          id="toggle-promo"
          class="btn {{ $promoSession ? 'btn-outline-danger' : 'btn-outline-primary' }}"
          data-state="{{ $promoSession ? 'applied' : 'idle' }}">
          {{ $promoSession ? __('adminlte::adminlte.remove') : __('adminlte::adminlte.apply') }}
        </button>
      </div>
      <div id="promo-message" class="mt-2 small {{ $promoSession ? 'text-success' : '' }}">
        @if($promoSession)
        <i class="fas fa-check-circle me-1"></i>{{ __('carts.messages.code_applied') }}.
        @endif
      </div>
    </div>
  </div>

  {{-- Confirmar → Checkout (con notas) --}}
  <form action="{{ route('public.checkout.show') }}" method="GET" id="confirm-reserva-form">
    {{-- Notas del cliente antes del cierre --}}
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <label for="notes" class="form-label fw-semibold">
          <i class="fas fa-sticky-note me-1"></i>
          {{ __('adminlte::adminlte.notes') }}
        </label>
        <textarea
          name="notes"
          id="notes"
          class="form-control"
          rows="3"
          placeholder="{{ __('adminlte::adminlte.notes_placeholder') }}">{{ $initialNotes }}</textarea>
        <div class="form-text">
          {{ __('adminlte::adminlte.notes_help', [], app()->getLocale()) }}
        </div>
      </div>
    </div>

    <div class="d-grid">
      <button type="submit" class="btn btn-success btn-lg">
        <i class="fas fa-check"></i> {{ __('adminlte::adminlte.confirmBooking') }}
      </button>
    </div>
  </form>

  @else
  <div class="alert alert-info">
    <i class="fas fa-info-circle"></i> {{ __('adminlte::adminlte.emptyCart') }}
  </div>
  @endif
</div>

{{-- ============================= --}}
{{-- MODALES: edición por ítem     --}}
{{-- ============================= --}}
@foreach(($cart->items ?? collect()) as $item)
@php
$currentScheduleId = $item->schedule?->schedule_id ?? null;
$currentTourLangId = $item->tour_language_id ?? $item->language?->tour_language_id;
$currentHotelId = $item->hotel?->hotel_id ?? null;
$currentMeetingPoint = $item->meeting_point_id ?? null;

$schedules = $item->tour->schedules ?? collect();
$tourLangs = $item->tour->languages ?? collect();
$initPickup = $item->meeting_point_id ? 'mp' : ($item->is_other_hotel ? 'custom' : ($item->hotel ? 'hotel' : 'hotel'));
@endphp
<div class="modal fade" id="editItemModal-{{ $item->item_id }}" tabindex="-1" aria-labelledby="editItemLabel-{{ $item->item_id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
    <div class="modal-content">
      <form action="{{ route('public.carts.update', $item->item_id) }}" method="POST" class="edit-item-form">
        @csrf @method('PUT')

        <input type="hidden" name="is_active" value="1" />
        <input type="hidden" name="is_other_hotel" id="is-other-hidden-{{ $item->item_id }}" value="{{ $item->is_other_hotel ? 1 : 0 }}">

        <div class="modal-header">
          <h5 class="modal-title" id="editItemLabel-{{ $item->item_id }}">
            <i class="fas fa-pencil-alt me-2"></i>
            {{ __('adminlte::adminlte.editItem') }} — {{ $item->tour->getTranslatedName(app()->getLocale()) ?? $item->tour->name }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('adminlte::adminlte.close') }}"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            {{-- Fecha --}}
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">{{ __('adminlte::adminlte.date') }}</label>
              <input type="date"
                name="tour_date"
                class="form-control"
                value="{{ \Carbon\Carbon::parse($item->tour_date)->format('Y-m-d') }}"
                min="{{ now()->format('Y-m-d') }}"
                required>
            </div>

            {{-- Horario --}}
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">{{ __('adminlte::adminlte.schedule') }}</label>
              <select name="schedule_id" class="form-select">
                <option value="">{{ __('adminlte::adminlte.selectOption') }}</option>
                @foreach($schedules as $sch)
                @php
                $label = \Carbon\Carbon::parse($sch->start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::parse($sch->end_time)->format('g:i A');
                @endphp
                <option value="{{ $sch->schedule_id }}" @selected($currentScheduleId==$sch->schedule_id)>{{ $label }}</option>
                @endforeach
              </select>
              <div class="form-text">{{ __('adminlte::adminlte.scheduleHelp') }}</div>
            </div>

            {{-- Idioma --}}
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">{{ __('adminlte::adminlte.language') }}</label>
              <select name="tour_language_id" class="form-select" required>
                @forelse($tourLangs as $tl)
                <option value="{{ $tl->tour_language_id }}" @selected($currentTourLangId==$tl->tour_language_id)>
                  {{ $tl->name ?? $tl->language->name ?? __('adminlte::adminlte.language') }}
                </option>
                @empty
                @if($item->language)
                <option value="{{ $item->tour_language_id }}" selected>{{ $item->language->name }}</option>
                @else
                <option value="" selected>—</option>
                @endif
                @endforelse
              </select>
            </div>

            {{-- Category Quantities - Show ALL available categories from tour pricing --}}
            @php
            // Get all available categories from tour pricing
            $tourPrices = $item->tour->prices ?? collect();

            // Get current quantities from item
            $itemCategories = is_string($item->categories) ? json_decode($item->categories, true) : ($item->categories ?? []);
            $currentQuantities = [];
            foreach ($itemCategories as $cat) {
            $catId = $cat['category_id'] ?? null;
            if ($catId) {
            $currentQuantities[$catId] = $cat['quantity'] ?? 0;
            }
            }
            @endphp

            @if($tourPrices->isNotEmpty())
            <div class="col-12">
              <label class="form-label fw-semibold">
                <i class="fas fa-users me-1"></i> {{ __('adminlte::adminlte.quantities') }}
              </label>
              <div class="row g-2">
                @foreach($tourPrices as $price)
                @php
                $category = $price->category;
                $categoryId = $category->category_id;
                $currentQty = $currentQuantities[$categoryId] ?? 0;

                // Get translated category name
                $categoryName = $category->getTranslatedName(app()->getLocale()) ?? $category->name ?? 'Category';

                // Calculate the actual max for this category (respect both category and global limits)
                $categoryMax = min(
                $price->max_quantity ?? 12,
                (int) config('booking.max_persons_per_booking', 12)
                );
                @endphp
                <div class="col-6 col-md-4">
                  <label class="form-label small">
                    {{ $categoryName }}
                    @if($category->age_range)
                    <span class="text-muted">({{ $category->age_range }})</span>
                    @endif
                  </label>
                  <input type="number"
                    name="categories[{{ $categoryId }}]"
                    class="form-control category-quantity-input"
                    value="{{ $currentQty }}"
                    min="0"
                    max="{{ $categoryMax }}"
                    data-category-id="{{ $categoryId }}"
                    data-category-name="{{ $categoryName }}"
                    data-category-price="{{ $price->price }}"
                    data-category-max="{{ $price->max_quantity }}"
                    data-global-max="{{ config('booking.max_persons_per_booking', 12) }}"
                    placeholder="0">
                  <small class="form-text text-muted">
                    Min: {{ $price->min_quantity ?? 0 }} - Máx: {{ $price->max_quantity }}
                  </small>
                </div>
                @endforeach
              </div>

              {{-- Real-time validation alert --}}
              <div id="validation-alert-{{ $item->item_id }}" class="alert alert-warning mt-3" style="display:none;" role="alert">
                <i class="fas fa-exclamation-triangle me-1"></i>
                <span id="validation-message-{{ $item->item_id }}"></span>
              </div>

              {{-- Price breakdown display --}}
              <div class="card mt-3 bg-light">
                <div class="card-body py-2">
                  <div id="price-breakdown-{{ $item->item_id }}" class="small">
                    {{-- Populated by JavaScript --}}
                  </div>
                  <div class="border-top pt-2 mt-2">
                    <div class="d-flex justify-content-between align-items-center">
                      <strong>{{ __('adminlte::adminlte.total') }}:</strong>
                      <strong class="text-primary fs-5">
                        $<span id="modal-total-{{ $item->item_id }}">0.00</span>
                      </strong>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-text">
                <i class="fas fa-info-circle me-1"></i>
                {{ __('adminlte::adminlte.quantitiesHelp') ?? 'Ajusta las cantidades según necesites. Puedes dejar en 0 las categorías que no uses.' }}
              </div>
            </div>
            @endif

            {{-- ====== PICKUP (segmentado) ====== --}}
            <div class="col-12">
              <label class="form-label fw-semibold d-flex align-items-center gap-2">
                <i class="fas fa-bus"></i> {{ __('adminlte::adminlte.pickup') }}
              </label>

              <div class="btn-group w-100 mb-2 pickup-tabs" role="group" aria-label="Pickup options"
                data-item="{{ $item->item_id }}" data-init="{{ $initPickup }}">
                <button type="button" class="btn btn-outline-secondary flex-fill" data-pickup-tab="hotel">
                  <i class="fas fa-hotel me-1"></i>{{ __('adminlte::adminlte.hotel') }}
                </button>
                <button type="button" class="btn btn-outline-secondary flex-fill" data-pickup-tab="custom">
                  <i class="fas fa-pen me-1"></i>{{ __('adminlte::adminlte.otherHotel') }}
                </button>
                <button type="button" class="btn btn-outline-secondary flex-fill" data-pickup-tab="mp">
                  <i class="fas fa-map-marker-alt me-1"></i>{{ __('adminlte::adminlte.meeting_point') }}
                </button>
              </div>

              <div id="pickup-panes-{{ $item->item_id }}">
                <div class="pickup-pane" id="pane-hotel-{{ $item->item_id }}" style="display:none">
                  <select name="hotel_id" id="hotel-select-{{ $item->item_id }}" class="form-select">
                    <option value="">{{ __('adminlte::adminlte.selectOption') }}</option>
                    @foreach(($hotels ?? []) as $hotel)
                    <option value="{{ $hotel->hotel_id }}" @selected($currentHotelId==$hotel->hotel_id)>{{ $hotel->name }}</option>
                    @endforeach
                  </select>
                  <div class="form-text">{{ __('adminlte::adminlte.selectHotelHelp') }}</div>
                </div>

                <div class="pickup-pane" id="pane-custom-{{ $item->item_id }}" style="display:none">
                  <input type="text" name="other_hotel_name" id="custom-hotel-input-{{ $item->item_id }}" class="form-control" value="{{ $item->other_hotel_name }}" placeholder="{{ __('adminlte::adminlte.customHotelName') }}">
                  <div class="form-text">{{ __('adminlte::adminlte.customHotelHelp') }}</div>
                </div>

                <div class="pickup-pane" id="pane-mp-{{ $item->item_id }}" style="display:none">
                  <select name="meeting_point_id"
                    class="form-select meetingpoint-select text-dark"
                    id="meetingpoint-select-{{ $item->item_id }}"
                    data-target="#mp-info-{{ $item->item_id }}"
                    data-mplist='{!! $mpListJson !!}'>
                    <option value="">{{ __('adminlte::adminlte.selectOption') }}</option>
                    @foreach($meetingPoints as $mp)
                    <option value="{{ $mp->id }}" @selected($currentMeetingPoint==$mp->id)>{{ $mp->getTranslated('name') }}</option>
                    @endforeach
                  </select>

                  <div class="border rounded p-2 mt-2 bg-light small" id="mp-info-{{ $item->item_id }}" style="display:none">
                    <div class="mp-name fw-semibold"></div>
                    <div class="mp-time text-muted"></div>
                    <div class="mp-addr mt-1"></div>
                    <a class="mp-link mt-1 d-inline-block" href="#" target="_blank" style="display:none">
                      <i class="fas fa-external-link-alt me-1"></i>{{ __('adminlte::adminlte.openMap') }}
                    </a>
                  </div>
                </div>
              </div>
            </div>
            {{-- ====== /PICKUP ====== --}}
          </div>
        </div>

        <div class="modal-footer d-block d-sm-flex">
          <button type="button" class="btn btn-secondary w-100 w-sm-auto me-sm-2 mb-2 mb-sm-0" data-bs-dismiss="modal">
            <i class="fas fa-times"></i> {{ __('adminlte::adminlte.cancel') }}
          </button>
          <button type="submit" id="submit-btn-{{ $item->item_id }}" class="btn btn-primary w-100 w-sm-auto">
            <i class="fas fa-save"></i> {{ __('adminlte::adminlte.update') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach
@endsection

@push('scripts')
@include('partials.cart.cart-scripts')
@endpush