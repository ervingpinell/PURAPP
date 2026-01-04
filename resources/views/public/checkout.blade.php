{{-- resources/views/public/checkout.blade.php --}}
@extends('layouts.app')

@section('title', __('m_checkout.title'))

{{-- Importante: para que se oculten WhatsApp + timer en checkout --}}
@section('body_class', 'checkout-page')

@push('styles')
@vite(entrypoints: 'resources/css/checkout.css')
@endpush

@section('content')
@php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Lang;

$fmt = fn($n)=>number_format((float)$n,2,'.','');
$promo = session('public_cart_promo');

// Subtotal por ítem
$itemSub = function($it){
$s=0;$c=collect($it->categories??[]);
if($c->isNotEmpty()){
foreach($c as $x){ $s+=(int)($x['quantity']??0)*(float)($x['price']??0); }
return (float)$s;
}
$s+=(int)($it->adults_quantity??0)*(float)($it->tour->adult_price??0);
$s+=(int)($it->kids_quantity??0)*(float)($it->tour->kid_price??0);
return (float)$s;
};

// Mapa category_id -> nombre traducido
$loc = app()->getLocale();
$fb = config('app.fallback_locale','es');

$categoryIdsInCart = collect($cart?->items ?? [])
->flatMap(fn($it) => collect($it->categories ?? [])->pluck('category_id')->filter())
->unique()->values();

$categoryNamesById = collect();
if ($categoryIdsInCart->isNotEmpty()) {
$catModels = \App\Models\CustomerCategory::whereIn('category_id', $categoryIdsInCart)
->with('translations')->get();

$categoryNamesById = $catModels->mapWithKeys(function($c) use ($loc, $fb) {
$name = method_exists($c, 'getTranslated')
? ($c->getTranslated('name') ?? $c->name)
: (optional($c->translations->firstWhere('locale', $loc))->name
?? optional($c->translations->firstWhere('locale', $fb))->name
?? $c->name);
return [$c->category_id => $name];
});
}

// Resolutor label de categoría
$resolveCatLabel = function(array $cat) use ($categoryNamesById) {
$name = data_get($cat,'i18n_name')
?? data_get($cat,'name')
?? data_get($cat,'label')
?? data_get($cat,'category_name')
?? data_get($cat,'category.name');

$cid = (int) (data_get($cat,'category_id') ?? data_get($cat,'id') ?? 0);
if (!$name && $cid && $categoryNamesById->has($cid)) {
$name = $categoryNamesById->get($cid);
}

if (!$name) {
$code = Str::lower((string) data_get($cat,'code',''));
if (in_array($code,['adult','adults'])) {
$name = __('adminlte::adminlte.adult');
} elseif (in_array($code,['kid','kids','child','children'])) {
$name = __('adminlte::adminlte.kid');
} elseif ($code !== '') {
$tr = __($code);
$name = ($tr === $code) ? $code : $tr;
}
}

if (!$name) {
$slug = (string) (data_get($cat,'category_slug') ?? data_get($cat,'slug') ?? '');
if ($slug) $name = Str::of($slug)->replace(['_','-'],' ')->title();
}

return $name ?: __('adminlte::adminlte.category');
};

$raw=(float)$cart->items->sum(fn($it)=>$itemSub($it));
$total=$raw; if($promo){$op=(($promo['operation']??'subtract')==='add')?1:-1;$total=max(0,round($raw+$op*(float)($promo['adjustment']??0),2));}

// ===== Texto "Cancelación gratuita hasta :time el :date" (debajo del total)
$freeCancelText = null;
if (!empty($freeCancelUntil)) {
$tz = config('app.timezone', 'America/Costa_Rica');
$locCut = $freeCancelUntil->copy()->setTimezone($tz)->locale(app()->getLocale());

// Usa Moment/ICU tokens válidos con isoFormat
$cutTime = $locCut->isoFormat('LT'); // ej: 3:15 p. m.
$cutDate = $locCut->isoFormat('LL'); // ej: 9 de noviembre de 2025

$key = 'policies.checkout.free_cancellation_until';
if (\Illuminate\Support\Facades\Lang::has($key)) {
$freeCancelText = __($key, ['time' => $cutTime, 'date' => $cutDate]);
} else {
// Fallback legible si falta la traducción
$freeCancelText = __('m_checkout.summary.free_cancellation') . ' — ' . $cutTime . ' · ' . $cutDate;
}
}
@endphp

<div class="checkout-container">
  <div class="progress-steps">
    <div class="step active">
      <div class="num">1</div><span>{{ __('m_checkout.steps.review') }}</span>
    </div>
    <div class="step-connector"></div>
    <div class="step">
      <div class="num">2</div><span>{{ __('m_checkout.steps.payment') }}</span>
    </div>
    <div class="step-connector"></div>
    <div class="step">
      <div class="num">3</div><span>{{ __('m_checkout.steps.confirmation') }}</span>
    </div>
  </div>

  @php
  // Timer configuration
  $expiryMinutes = (int) \App\Models\Setting::getValue('cart.expiration_minutes', 30);
  $expiresAtIso = null;

  if ($cart) {
  if ($cart->is_guest_cart ?? false) {
  // Guest cart expiration
  $guestCartCreated = session('guest_cart_created_at');
  if ($guestCartCreated) {
  $expiresAt = \Carbon\Carbon::parse($guestCartCreated)->addMinutes($expiryMinutes);
  $expiresAtIso = $expiresAt->toIso8601String();
  }
  } else {
  // Authenticated user cart
  $expiresAt = $cart->expires_at ? \Carbon\Carbon::parse($cart->expires_at) : null;
  $expiresAtIso = $expiresAt ? $expiresAt->toIso8601String() : null;
  }
  }
  @endphp

  {{-- Cart Expiration Timer Banner --}}
  @if($cart && $cart->items->count() && !empty($expiresAtIso) && (($cart->is_guest_cart ?? false) || ($cart->is_active && !$cart->isExpired())))
  <div id="cart-timer"
    class="gv-timer shadow-sm"
    role="alert"
    data-expires-at="{{ $expiresAtIso }}"
    data-total-minutes="{{ $expiryMinutes }}"
    data-expire-endpoint="{{ ($cart->is_guest_cart ?? false) ? route('public.guest-carts.expire') : route('public.carts.expire') }}">
    <div class="gv-timer-head">
      <div class="gv-timer-icon">
        <i class="fas fa-hourglass-half"></i>
      </div>
      <div class="gv-timer-text">
        <div class="gv-timer-title">{{ __('carts.timer.will_expire') }}</div>
        <div class="gv-timer-sub">
          {{ __('carts.timer.time_left') }}
          <span id="cart-timer-remaining" class="gv-timer-remaining">--:--</span>
        </div>
      </div>
    </div>
    <div class="gv-timer-bar">
      <div class="gv-timer-bar-fill" id="cart-timer-bar" style="width:100%"></div>
    </div>
  </div>
  @endif

  <div class="checkout-grid">
    {{-- Left: Customer Information --}}
    <div class="form-panel">
      <div class="panel-title">
        <i class="fas fa-user-circle"></i>
        {{ __('m_checkout.customer_info.title') }}
      </div>
      <div class="panel-subtitle">
        <i class="fas fa-info-circle"></i>
        {{ __('m_checkout.customer_info.subtitle') }}
      </div>

      {{-- Guest Information Form (for non-authenticated users) --}}
      @guest
      <div class="mt-4">
        <form id="guest-info-form">
          <div class="row g-3">
            <div class="col-12">
              <label for="guest_name" class="form-label">
                {{ __('m_checkout.customer_info.full_name') }} <span class="text-danger">*</span>
              </label>
              <input
                type="text"
                class="form-control"
                id="guest_name"
                name="guest_name"
                value="{{ old('guest_name') }}"
                required
                placeholder="{{ __('m_checkout.customer_info.placeholder_name') }}">
              @error('guest_name')
              <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              <label for="guest_email" class="form-label">
                {{ __('m_checkout.customer_info.email') }} <span class="text-danger">*</span>
              </label>
              <input
                type="email"
                class="form-control"
                id="guest_email"
                name="guest_email"
                value="{{ old('guest_email') }}"
                required
                placeholder="{{ __('m_checkout.customer_info.placeholder_email') }}">
              @error('guest_email')
              <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              @php
              // Parse old phone value to extract country code and number
              $oldPhone = old('guest_phone', '');
              $oldCode = old('guest_country_code', '+506');
              $phoneNumber = $oldPhone;

              // If old phone contains a country code pattern (+XXX), extract it
              if (preg_match('/^(\+\d+(?:-\d+)?)\s+(.+)$/', $oldPhone, $matches)) {
              $oldCode = $matches[1]; // e.g., "+506"
              $phoneNumber = $matches[2]; // e.g., "1234-5678"
              } elseif (preg_match('/^(\+\d+(?:-\d+)?)(.+)$/', $oldPhone, $matches)) {
              $oldCode = $matches[1];
              $phoneNumber = ltrim($matches[2]);
              }
              @endphp
              <label for="guest_phone" class="form-label">
                {{ __('m_checkout.customer_info.phone') }} <span class="text-muted">({{ __('m_checkout.customer_info.optional') }})</span>
              </label>
              <div class="row g-2">
                <div class="col-5 col-sm-4">
                  <select class="form-select" id="guest_country_code" name="guest_country_code">
                    @include('partials.country-codes', ['selected' => $oldCode, 'showNames' => true])
                  </select>
                </div>
                <div class="col-7 col-sm-8">
                  <input
                    type="tel"
                    class="form-control"
                    id="guest_phone"
                    name="guest_phone"
                    value="{{ $phoneNumber }}"
                    placeholder="1234-5678">
                </div>
              </div>
              @error('guest_phone')
              <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
              @error('guest_country_code')
              <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="alert alert-info mt-3 d-flex align-items-start gap-2">
            <i class="fas fa-info-circle mt-1"></i>
            <div>
              <strong>{{ __('m_checkout.customer_info.why_need_title') }}</strong>
              <p class="mb-0 small">
                {{ __('m_checkout.customer_info.why_need_text') }}
              </p>
            </div>
          </div>
        </form>
      </div>
      @endguest

      {{-- Authenticated user info --}}
      @auth
      <div class="mt-4">
        <div class="alert alert-success d-flex align-items-start gap-2">
          <i class="fas fa-check-circle mt-1"></i>
          <div>
            <strong>{{ __('m_checkout.customer_info.logged_in_as') }}</strong>
            <p class="mb-0 small">{{ Auth::user()->name }} ({{ Auth::user()->email }})</p>
          </div>
        </div>
      </div>
      @endauth

      {{-- Form with submission buttons --}}
      <form method="POST" action="{{ route('public.checkout.process') }}" id="checkout-form" class="mt-4">
        @csrf
        <input type="hidden" name="scroll_ok" id="scroll_ok" value="0">

        {{-- Include guest data in submission if present --}}
        @guest
        <input type="hidden" name="guest_name" id="hidden_guest_name">
        <input type="hidden" name="guest_email" id="hidden_guest_email">
        <input type="hidden" name="guest_phone" id="hidden_guest_phone">
        @endguest

        {{-- 🔐 Notas que vienen del carrito: se preservan en el POST al process --}}
        <input
          type="hidden"
          name="notes"
          value="{{ old('notes', request('notes', $cart->notes ?? '')) }}">

        {{-- Terms acceptance moved to payment page --}}

        <div class="d-flex gap-3">
          @if(!isset($isBookingPayment) || !$isBookingPayment)
          <a href="{{ route('public.carts.index') }}" class="btn btn-back">
            <i class="fas fa-arrow-left"></i>{{ __('m_checkout.buttons.back') }}
          </a>
          @endif
          <button
            type="submit"
            id="btn-proceed"
            class="btn btn-payment">
            {{ __('m_checkout.buttons.go_to_payment') }}
            <i class="fas fa-arrow-right"></i>
          </button>
        </div>
      </form>
    </div>

    {{-- Right: Summary with internal scroll --}}
    <div class="summary-panel">
      <div class="summary-header">
        <h3>{{ __('m_checkout.summary.title') }}</h3>
        @php $cnt = $cart->items->count(); @endphp
        <span class="count">
          {{ $cnt }}
          {{ $cnt === 1 ? __('m_checkout.summary.item') : __('m_checkout.summary.items') }}
        </span>
      </div>

      <div class="items-scroll">
        @foreach($cart->items as $it)
        @php
        $s = $itemSub($it);
        $cats = collect($it->categories ?? []);
        $totalPax = $cats->isNotEmpty()
        ? $cats->sum(fn($c) => (int) data_get($c,'quantity',0))
        : (int)($it->adults_quantity ?? 0) + (int)($it->kids_quantity ?? 0);

        $ref = $cart->public_reference ?? $cart->id ?? null;
        $line = $it->id ?? $it->cart_item_id ?? null;
        $mp = data_get($it,'meetingPoint');
        $hotel = data_get($it,'hotel');
        $pickupAt = data_get($it,'pickup_time');
        $addons = collect(data_get($it,'addons',[]));
        $duration = data_get($it,'tour.length') ?? data_get($it,'duration');
        $guide = data_get($it,'guide.name');
        $notes = data_get($it,'notes') ?? data_get($it,'special_requests');
        $tz = config('app.timezone','America/Costa_Rica');
        @endphp

        <div class="tour-item">
          <div class="tour-name">
            {{ $it->tour->getTranslatedName() ?? $it->tour->name }}
          </div>

          @if($ref || $line)
          <div class="d-flex flex-wrap" style="gap:.4rem; margin-bottom:.35rem">
            @if($ref)
            <span class="badge-ghost">
              <i class="fas fa-hashtag me-1"></i>
              {{ __('m_checkout.blocks.ref') }}: {{ $ref }}
            </span>
            @endif
            @if($line)
            <span class="badge-ghost">
              <i class="fas fa-stream me-1"></i>
              {{ __('m_checkout.blocks.item') }}: {{ $line }}
            </span>
            @endif
          </div>
          @endif

          <div class="tour-details">
            <span>
              <i class="far fa-calendar-alt"></i>
              {{ \Carbon\Carbon::parse($it->tour_date)->format('l, F d, Y') }}
            </span>

            @if($it->schedule)
            <span>
              <i class="far fa-clock"></i>
              {{ __('m_checkout.misc.at') }}
              {{ \Carbon\Carbon::parse($it->schedule->start_time)->format('g:i A') }}
            </span>
            @endif

            @if($totalPax > 0)
            <span>
              <i class="fas fa-user"></i>
              {{ $totalPax }}
              {{ $totalPax === 1 ? __('m_checkout.misc.participant') : __('m_checkout.misc.participants') }}
            </span>
            @endif

            @if($it->language)
            <span>
              <i class="fas fa-language"></i>
              {{ $it->language->name }}
            </span>
            @endif
          </div>

          @if($hotel || $mp || $pickupAt)
          <div class="secondary-block">
            <div class="block-title">
              <i class="fas fa-map-marker-alt"></i>
              <span>{{ __('m_checkout.blocks.pickup_meeting') }}</span>
            </div>
            <div class="small" style="color:var(--g700)">
              @if($hotel)
              <div class="mb-1">
                <strong>{{ __('m_checkout.blocks.hotel') }}:</strong>
                {{ $hotel->name ?? '' }}
                @if(data_get($hotel,'address'))
                — {{ $hotel->address }}
                @endif
              </div>
              @endif

              @if($mp)
              <div class="mb-1">
                <strong>{{ __('m_checkout.blocks.meeting_point') }}:</strong>
                {{ $mp->name ?? '' }}
                @if(data_get($mp,'address'))
                — {{ $mp->address }}
                @endif
              </div>
              @if(data_get($mp,'notes'))
              <div class="text-muted">{{ $mp->notes }}</div>
              @endif
              @endif

              @if($pickupAt)
              <div class="mt-1">
                <i class="far fa-clock"></i>
                <strong>{{ __('m_checkout.blocks.pickup_time') }}:</strong>
                {{ \Carbon\Carbon::parse($pickupAt)->format('g:i A') }}
                ({{ $tz }})
              </div>
              @endif
            </div>
          </div>
          @endif

          @if($addons->isNotEmpty())
          <div class="secondary-block">
            <div class="block-title">
              <i class="fas fa-plus-circle"></i>
              <span>{{ __('m_checkout.blocks.add_ons') }}</span>
            </div>

            @foreach($addons as $ad)
            @php
            $aq = (int) data_get($ad,'quantity',0);
            $ap = (float) data_get($ad,'price',0);
            $at = $aq * $ap;
            @endphp
            @if($aq > 0)
            <div class="d-flex justify-content-between align-items-center"
              style="gap:.5rem;padding:.25rem 0">
              <div class="d-flex align-items-center" style="gap:.5rem">
                <i class="fas fa-tag" style="color:var(--p)"></i>
                <span class="small">{{ data_get($ad,'name','Extra') }}</span>
                <span class="qty-badge">{{ $aq }}x</span>
                <span class="price-detail small">
                  (${{ $fmt($ap) }} × {{ $aq }})
                </span>
              </div>
              <div class="fw-bold">${{ $fmt($at) }}</div>
            </div>
            @endif
            @endforeach
          </div>
          @endif

          @if($duration || $guide)
          <div class="secondary-block">
            <div class="d-flex flex-wrap gap-3 small" style="color:var(--g700)">
              @if($duration)
              <div>
                <i class="far fa-hourglass" style="color:var(--p)"></i>
                <strong>{{ __('m_checkout.blocks.duration') }}:</strong>
                {{ $duration }} {{ __('m_checkout.blocks.hours') }}
              </div>
              @endif
              @if($guide)
              <div>
                <i class="fas fa-user-tie" style="color:var(--p)"></i>
                <strong>{{ __('m_checkout.blocks.guide') }}:</strong>
                {{ $guide }}
              </div>
              @endif
            </div>
          </div>
          @endif

          @if($notes)
          <div class="secondary-block">
            <div class="block-title">
              <i class="fas fa-sticky-note"></i>
              <span>{{ __('m_checkout.blocks.notes') }}</span>
            </div>
            <div class="small" style="color:var(--g700)">{{ $notes }}</div>
          </div>
          @endif

          @php
          $showCats = $cats->isNotEmpty()
          || (int)($it->adults_quantity ?? 0) > 0
          || (int)($it->kids_quantity ?? 0) > 0;
          @endphp

          @if($showCats)
          <div class="categories-section" style="margin-top:.5rem">
            @if($cats->isNotEmpty())
            @foreach($cats as $c)
            @php
            $q = (int) data_get($c,'quantity',0);
            $u = (float) data_get($c,'price',0);
            $sub = $q * $u;
            $lab = $resolveCatLabel((array)$c);
            $code = Str::lower((string) data_get($c,'code',''));
            $isAdult = in_array($code,['adult','adults']);
            $isKid = in_array($code,['kid','kids','child','children']);
            @endphp
            @if($q > 0)
            <div class="category-line">
              <div class="category-left">
                @if($isAdult)
                <i class="fas fa-user"></i>
                <strong>{{ __('m_checkout.categories.adult') }}</strong>
                @elseif($isKid)
                <i class="fas fa-child"></i>
                <strong>{{ __('m_checkout.categories.kid') }}</strong>
                @else
                <i class="fas fa-user-friends"></i>
                <span>{{ $lab }}</span>
                @endif
                <span class="qty-badge">{{ $q }}x</span>
                <span class="price-detail">
                  (${{ $fmt($u) }} × {{ $q }})
                </span>
              </div>
              <div class="category-total">${{ $fmt($sub) }}</div>
            </div>
            @endif
            @endforeach
            @else
            @if((int)($it->adults_quantity ?? 0) > 0)
            @php
            $q = (int) $it->adults_quantity;
            $u = (float) ($it->tour->adult_price ?? 0);
            @endphp
            <div class="category-line">
              <div class="category-left">
                <i class="fas fa-user"></i>
                <strong>{{ __('m_checkout.categories.adult') }}</strong>
                <span class="qty-badge">{{ $q }}x</span>
                <span class="price-detail">
                  (${{ $fmt($u) }} × {{ $q }})
                </span>
              </div>
              <div class="category-total">${{ $fmt($q * $u) }}</div>
            </div>
            @endif

            @if((int)($it->kids_quantity ?? 0) > 0)
            @php
            $q = (int) $it->kids_quantity;
            $u = (float) ($it->tour->kid_price ?? 0);
            @endphp
            <div class="category-line">
              <div class="category-left">
                <i class="fas fa-child"></i>
                <strong>{{ __('m_checkout.categories.kid') }}</strong>
                <span class="qty-badge">{{ $q }}x</span>
                <span class="price-detail">
                  (${{ $fmt($u) }} × {{ $q }})
                </span>
              </div>
              <div class="category-total">${{ $fmt($q * $u) }}</div>
            </div>
            @endif
            @endif
          </div>
          @endif

          <div class="tour-price">${{ $fmt($s) }}</div>
        </div>
        @endforeach
      </div>

      <div class="summary-footer">
        <div class="totals-section">
          @php
          // Check if this is a booking payment (stdClass) or regular cart (Cart model)
          if (isset($isBookingPayment) && $isBookingPayment && isset($booking)) {
          // For booking payments, use the booking total
          $calculatedTotal = (float) $booking->total;
          $displaySubtotal = (float) ($booking->detail?->total ?? $booking->total);
          } elseif (isset($cart->is_guest_cart) && $cart->is_guest_cart) {
          // Guest cart - manually calculate from categories
          $calculatedTotal = 0;
          foreach ($cart->items as $item) {
          $categories = $item->categories ?? [];
          foreach ($categories as $cat) {
          $calculatedTotal += ((float)($cat['price'] ?? 0)) * ((int)($cat['quantity'] ?? 0));
          }
          }
          $displaySubtotal = $calculatedTotal;
          } else {
          // Use the centralized Cart::calculateTotal() method
          // This uses prices from the stored snapshot (already calculated with correct date)
          $calculatedTotal = $cart->calculateTotal();
          $displaySubtotal = $calculatedTotal;
          }
          @endphp

          <div class="total-row">
            <span>{{ __('m_checkout.summary.subtotal') }}</span>
            <span>${{ $fmt($displaySubtotal) }}</span>
          </div>

          @if($promo)
          <div class="total-row promo">
            <span>
              {{ __('m_checkout.summary.promo_code') }}
              <span class="promo-code">{{ $promo['code'] }}</span>
            </span>
            <span>
              {{ ($promo['operation'] ?? 'subtract') === 'subtract' ? '-' : '+' }}
              ${{ $fmt($promo['adjustment'] ?? 0) }}
            </span>
          </div>
          @endif

          @php
          // Apply promo to calculated total
          if ($promo) {
          $op = (($promo['operation'] ?? 'subtract') === 'add') ? 1 : -1;
          $calculatedTotal = max(0, round($calculatedTotal + $op * (float) ($promo['adjustment'] ?? 0), 2));
          }
          @endphp

          <div class="total-row final">
            <span>{{ __('m_checkout.summary.total') }}</span>
            <span>${{ $fmt($calculatedTotal) }}</span>
          </div>

          <div class="total-note">
            {{ __('m_checkout.summary.taxes_included') }}
          </div>

          @if($freeCancelText)
          <div class="cancellation-badge">
            <i class="fas fa-check-circle"></i>
            <div>
              <strong>{{ __('m_checkout.summary.free_cancellation') }}</strong>
              <small>{{ $freeCancelText }}</small>
            </div>
          </div>
          @endif
        </div>

        <button class="btn btn-details" data-bs-toggle="modal" data-bs-target="#orderModal">
          <i class="fas fa-list"></i>{{ __('m_checkout.buttons.view_details') }}
        </button>

        @if(!isset($isBookingPayment) || !$isBookingPayment)
        <a href="{{ route('public.carts.index') }}" class="btn btn-edit">
          <i class="fas fa-edit"></i>{{ __('m_checkout.buttons.edit') }}
        </a>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Modal de detalle --}}
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-receipt me-2"></i>
          {{ __('m_checkout.summary.order_details') }}
        </h5>
        <button
          type="button"
          class="btn-close btn-close-white"
          data-bs-dismiss="modal"
          aria-label="{{ __('m_checkout.buttons.close') }}"></button>
      </div>

      <div class="modal-body">
        @foreach($cart->items as $it)
        @php $s = $itemSub($it); @endphp
        <div class="item-detail">
          <div class="item-header d-flex justify-content-between align-items-center">
            <strong>{{ $it->tour->getTranslatedName() ?? $it->tour->name }}</strong>
            <span class="price">${{ $fmt($s) }}</span>
          </div>

          <div class="item-meta d-flex flex-wrap gap-3">
            <span>
              <i class="far fa-calendar-alt"></i>
              {{ \Carbon\Carbon::parse($it->tour_date)->format('l, F d, Y') }}
            </span>
            @if($it->schedule)
            <span>
              <i class="far fa-clock"></i>
              {{ \Carbon\Carbon::parse($it->schedule->start_time)->format('g:i A') }}
              -
              {{ \Carbon\Carbon::parse($it->schedule->end_time)->format('g:i A') }}
            </span>
            @endif
            @if($it->language)
            <span>
              <i class="fas fa-language"></i>
              {{ $it->language->name }}
            </span>
            @endif
          </div>

          @php
          $cats = collect($it->categories ?? []);
          $ref = $cart->public_reference ?? $cart->id ?? null;
          $line = $it->id ?? $it->cart_item_id ?? null;
          $mp = data_get($it,'meetingPoint');
          $hotel = data_get($it,'hotel');
          $pickupAt= data_get($it,'pickup_time');
          $addons = collect(data_get($it,'addons',[]));
          $duration= data_get($it,'tour.length') ?? data_get($it,'duration');
          $guide = data_get($it,'guide.name');
          $notes = data_get($it,'notes') ?? data_get($it,'special_requests');
          $tz = config('app.timezone','America/Costa_Rica');
          @endphp

          @if($ref || $line)
          <div class="d-flex flex-wrap gap-2 mt-2">
            @if($ref)
            <span class="badge-ghost">
              <i class="fas fa-hashtag me-1"></i>
              {{ __('m_checkout.blocks.ref') }}: {{ $ref }}
            </span>
            @endif
            @if($line)
            <span class="badge-ghost">
              <i class="fas fa-stream me-1"></i>
              {{ __('m_checkout.blocks.item') }}: {{ $line }}
            </span>
            @endif
          </div>
          @endif

          @if($hotel || $mp || $pickupAt)
          <div class="categories-section mt-2">
            <div class="d-flex align-items-center mb-2" style="gap:.5rem">
              <i class="fas fa-map-marker-alt" style="color:var(--p)"></i>
              <strong>{{ __('m_checkout.blocks.pickup_meeting') }}</strong>
            </div>
            <div class="small" style="color:var(--g700)">
              @if($hotel)
              <div class="mb-1">
                <strong>{{ __('m_checkout.blocks.hotel') }}:</strong>
                {{ $hotel->name ?? '' }}
                @if(data_get($hotel,'address'))
                — {{ $hotel->address }}
                @endif
              </div>
              @endif

              @if($mp)
              <div class="mb-1">
                <strong>{{ __('m_checkout.blocks.meeting_point') }}:</strong>
                {{ $mp->name ?? '' }}
                @if(data_get($mp,'address'))
                — {{ $mp->address }}
                @endif
              </div>
              @endif

              @if(data_get($mp,'notes'))
              <div class="text-muted">{{ $mp->notes }}</div>
              @endif

              @if($pickupAt)
              <div class="mt-1">
                <i class="far fa-clock"></i>
                <strong>{{ __('m_checkout.blocks.pickup_time') }}:</strong>
                {{ \Carbon\Carbon::parse($pickupAt)->format('g:i A') }}
                ({{ $tz }})
              </div>
              @endif
            </div>
          </div>
          @endif

          @if($addons->isNotEmpty())
          <div class="categories-section mt-2">
            <div class="d-flex align-items-center mb-2" style="gap:.5rem">
              <i class="fas fa-plus-circle" style="color:var(--p)"></i>
              <strong>{{ __('m_checkout.blocks.add_ons') }}</strong>
            </div>
            @foreach($addons as $ad)
            @php
            $aq = (int) data_get($ad,'quantity',0);
            $ap = (float) data_get($ad,'price',0);
            $at = $aq * $ap;
            @endphp
            @if($aq > 0)
            <div class="category-line">
              <div class="category-left">
                <i class="fas fa-tag"></i>
                <span>{{ data_get($ad,'name','Extra') }}</span>
                <span class="qty-badge">{{ $aq }}x</span>
                <span class="price-detail">
                  (${{ $fmt($ap) }} × {{ $aq }})
                </span>
              </div>
              <div class="category-total">${{ $fmt($at) }}</div>
            </div>
            @endif
            @endforeach
          </div>
          @endif

          @if($duration || $guide)
          <div class="d-flex flex-wrap gap-3 mt-2 small" style="color:var(--g700)">
            @if($duration)
            <div>
              <i class="far fa-hourglass"></i>
              <strong>{{ __('m_checkout.blocks.duration') }}:</strong>
              {{ $duration }} {{ __('m_checkout.blocks.hours') }}
            </div>
            @endif
            @if($guide)
            <div>
              <i class="fas fa-user-tie"></i>
              <strong>{{ __('m_checkout.blocks.guide') }}:</strong>
              {{ $guide }}
            </div>
            @endif
          </div>
          @endif

          @if($cats->isNotEmpty())
          <div class="categories-section">
            @foreach($cats as $c)
            @php
            $q = (int) data_get($c,'quantity',0);
            $u = (float) data_get($c,'price',0);
            $sub = $q * $u;
            $lab = $resolveCatLabel((array)$c);
            $code = Str::lower((string) data_get($c,'code',''));
            $isAdult = in_array($code,['adult','adults']);
            $isKid = in_array($code,['kid','kids','child','children']);
            @endphp
            @if($q > 0)
            <div class="category-line">
              <div class="category-left">
                @if($isAdult)
                <i class="fas fa-user"></i>
                <strong>{{ __('m_checkout.categories.adult') }}</strong>
                @elseif($isKid)
                <i class="fas fa-child"></i>
                <strong>{{ __('m_checkout.categories.kid') }}</strong>
                @else
                <i class="fas fa-user-friends"></i>
                <span>{{ $lab }}</span>
                @endif
                <span class="qty-badge">{{ $q }}x</span>
                <span class="price-detail">
                  (${{ $fmt($u) }} × {{ $q }})
                </span>
              </div>
              <div class="category-total">${{ $fmt($sub) }}</div>
            </div>
            @endif
            @endforeach
          </div>
          @else
          <div class="categories-section">
            @if((int)($it->adults_quantity ?? 0) > 0)
            @php
            $q = (int) $it->adults_quantity;
            $u = (float) ($it->tour->adult_price ?? 0);
            @endphp
            <div class="category-line">
              <div class="category-left">
                <i class="fas fa-user"></i>
                <strong>{{ __('m_checkout.categories.adult') }}</strong>
                <span class="qty-badge">{{ $q }}x</span>
                <span class="price-detail">
                  (${{ $fmt($u) }} × {{ $q }})
                </span>
              </div>
              <div class="category-total">${{ $fmt($q * $u) }}</div>
            </div>
            @endif
            @if((int)($it->kids_quantity ?? 0) > 0)
            @php
            $q = (int) $it->kids_quantity;
            $u = (float) ($it->tour->kid_price ?? 0);
            @endphp
            <div class="category-line">
              <div class="category-left">
                <i class="fas fa-child"></i>
                <strong>{{ __('m_checkout.categories.kid') }}</strong>
                <span class="qty-badge">{{ $q }}x</span>
                <span class="price-detail">
                  (${{ $fmt($u) }} × {{ $q }})
                </span>
              </div>
              <div class="category-total">${{ $fmt($q * $u) }}</div>
            </div>
            @endif
          </div>
          @endif
        </div>
        @endforeach
      </div>

      <div class="modal-footer">
        <div class="w-100">
          <div class="total-row d-flex justify-content-between">
            <span>{{ __('m_checkout.summary.subtotal') }}</span>
            <span style="font-weight:600">${{ $fmt($raw) }}</span>
          </div>

          @if($promo)
          <div class="total-row promo d-flex justify-content-between">
            <span>
              {{ __('m_checkout.summary.promo_code') }}
              <span class="promo-code">{{ $promo['code'] }}</span>
            </span>
            <span style="font-weight:600">
              {{ ($promo['operation'] ?? 'subtract') === 'subtract' ? '-' : '+' }}
              ${{ $fmt($promo['adjustment'] ?? 0) }}
            </span>
          </div>
          @endif

          <div class="total-row final d-flex justify-content-between">
            <span>{{ __('m_checkout.summary.total') }}</span>
            <span>${{ $fmt($total) }}</span>
          </div>

          @if($freeCancelText)
          <div class="cancellation-badge mt-2">
            <i class="fas fa-check-circle"></i>
            <div>
              <strong>{{ __('m_checkout.summary.free_cancellation') }}</strong>
              <small>{{ $freeCancelText }}</small>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    /* 1) Limpia títulos duplicados en el include */
    const cont = document.querySelector('.policy-content');
    if (cont) {
      const norm = s => (s || '')
        .replace(/\b(vers(i|í)on|version|v)\s*\d+(\.\d+)?/ig, '')
        .replace(/[^\p{L}\p{N}\s]/gu, '')
        .trim().toLowerCase();
      const hs = [...cont.querySelectorAll('h1,h2,h3')];
      for (let i = 0; i < hs.length - 1; i++) {
        const a = hs[i],
          b = hs[i + 1];
        if (['H1', 'H2', 'H3'].includes(a.tagName) && ['H1', 'H2', 'H3'].includes(b.tagName)) {
          if (norm(a.textContent) && norm(a.textContent) === norm(b.textContent)) {
            b.remove();
          }
        }
      }
    }

    /* 2) Desbloquear checkbox al leer hasta el final */
    const box = document.querySelector('[data-scroll-guard]');
    const chk = document.getElementById('accept_terms');
    const btn = document.getElementById('btn-proceed');
    const flag = document.getElementById('scroll_ok');
    const abox = document.getElementById('accept-box');

    if (box && chk && btn && flag) {
      const unlock = () => {
        chk.disabled = false;
        flag.value = '1';
        abox?.classList.remove('disabled');
      };

      const atBottom = el => el.scrollTop + el.clientHeight >= el.scrollHeight - 6;

      if (chk.checked || box.scrollHeight <= box.clientHeight || atBottom(box)) {
        unlock();
      } else {
        box.addEventListener('scroll', function onS() {
          if (atBottom(box)) {
            unlock();
            box.removeEventListener('scroll', onS);
          }
        });
      }

      chk.addEventListener('change', () => {
        btn.disabled = !chk.checked;
      });
    }

    /* 3) Guest form handling - copy values to hidden inputs on submit */
    const checkoutForm = document.getElementById('checkout-form');
    const guestForm = document.getElementById('guest-info-form');

    if (checkoutForm && guestForm) {
      checkoutForm.addEventListener('submit', function(e) {
        // Copy guest form values to hidden inputs
        const guestName = document.getElementById('guest_name');
        const guestEmail = document.getElementById('guest_email');
        const guestPhone = document.getElementById('guest_phone');

        const hiddenName = document.getElementById('hidden_guest_name');
        const hiddenEmail = document.getElementById('hidden_guest_email');
        const guestCountryCode = document.getElementById('guest_country_code');
        const hiddenPhone = document.getElementById('hidden_guest_phone');

        if (guestName && hiddenName) {
          hiddenName.value = guestName.value;
        }
        if (guestEmail && hiddenEmail) {
          hiddenEmail.value = guestEmail.value;
        }
        if (guestPhone && guestCountryCode && hiddenPhone) {
          // Combine country code + phone number for backward compatibility
          const countryCode = guestCountryCode.value || '+506';
          const phoneNumber = guestPhone.value || '';
          hiddenPhone.value = phoneNumber ? countryCode + ' ' + phoneNumber : '';
        }

        // Validate guest form
        if (guestName && guestEmail) {
          if (!guestName.value.trim() || !guestEmail.value.trim()) {
            e.preventDefault();
            alert('{{ __("Please fill in your name and email to continue") }}');
            return false;
          }

          // Basic email validation
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRegex.test(guestEmail.value)) {
            e.preventDefault();
            alert('{{ __("Please enter a valid email address") }}');
            return false;
          }
        }
      });
    }

    /* 4) Cart Timer Countdown */
    (function() {
      const box = document.getElementById('cart-timer');
      if (!box) return;

      const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const remainingEl = document.getElementById('cart-timer-remaining');
      const barEl = document.getElementById('cart-timer-bar');
      const expireEndpoint = box.getAttribute('data-expire-endpoint');

      const totalSecondsCfg = Number(box.getAttribute('data-total-minutes') || '15') * 60;
      let serverExpires = new Date(box.getAttribute('data-expires-at')).getTime();

      let rafId = null;
      const fmt = (sec) => {
        const s = Math.max(0, sec | 0);
        const m = Math.floor(s / 60);
        const r = s % 60;
        return String(m).padStart(2, '0') + ':' + String(r).padStart(2, '0');
      };
      const setBar = (remainingSec) => {
        const frac = Math.max(0, Math.min(1, remainingSec / totalSecondsCfg));
        if (barEl) barEl.style.width = (frac * 100).toFixed(2) + '%';
      };

      const tick = () => {
        const now = Date.now();
        const remainingSec = Math.ceil((serverExpires - now) / 1000);
        if (remainingEl) remainingEl.textContent = fmt(remainingSec);
        setBar(remainingSec);
        if (remainingSec <= 0) {
          cancelAnimationFrame(rafId);
          return handleExpire();
        }
        rafId = requestAnimationFrame(tick);
      };

      const handleExpire = async () => {
        try {
          await fetch(expireEndpoint, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrf,
              'Accept': 'application/json'
            }
          });
        } catch {}
        // Show SweetAlert before redirecting
        await Swal.fire({
          icon: 'warning',
          title: '{{ __("carts.timer.expired_title") }}',
          text: '{{ __("carts.timer.expired_text") }}',
          confirmButtonText: 'OK',
          allowOutsideClick: false,
          allowEscapeKey: false,
          timer: 7000,
          timerProgressBar: true
        });
        // Redirect to home instead of cart page
        window.location.replace('{{ route(app()->getLocale() . ".home") }}');
      };

      tick();
    })();
  });
</script>
@endpush