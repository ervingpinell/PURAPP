@props(['variant' => 'desktop'])

@php
use Illuminate\Support\Facades\Storage;

$isDesktop = $variant === 'desktop';
$triggerId = $isDesktop ? 'cartDropdownDesktop' : 'cartDropdownMobile';
$menuId = $triggerId.'Menu';
$triggerCls = $isDesktop
? 'nav-link cart-icon-wrapper position-relative dropdown-toggle'
: 'cart-icon-wrapper position-relative dropdown-toggle';
$iconCls = $isDesktop ? 'fas fa-shopping-cart' : 'fas fa-shopping-cart text-white';

$headerCart = $headerCart ?? null;
$headerCount = $headerCount ?? ($headerCart?->items?->count() ?? 0);

$headerTotal = $headerTotal ?? (
$headerCart
? (float) $headerCart->items->sum(function ($i) {
$cats = collect($i->categories ?? []);
if ($cats->isNotEmpty()) {
return (float) $cats->sum(fn($c) =>
((float)($c['price'] ?? 0)) * ((int)($c['quantity'] ?? 0))
);
}
return (float) (
(($i->tour->adult_price ?? 0) * ($i->adults_quantity ?? 0)) +
(($i->tour->kid_price ?? 0) * ($i->kids_quantity ?? 0))
);
})
: 0.0
);

// Apply promo code discount if exists in session
$promoData = session('public_cart_promo');
if ($promoData && $headerTotal > 0) {
$adjustment = (float) ($promoData['adjustment'] ?? 0);
$operation = $promoData['operation'] ?? 'subtract';

if ($operation === 'add') {
$headerTotal += $adjustment;
} else {
$headerTotal -= $adjustment;
}

$headerTotal = max(0, $headerTotal);
}

$expiresIso = optional($headerCart?->expires_at)->toIso8601String();
$totalMinutesCfg = (int) \App\Models\Setting::getValue('cart.expiration_minutes', 30);

$coverFromTour = function ($tour) {
if (!$tour) return asset('images/volcano.png');
if (!empty($tour->image_path)) return asset('storage/'.$tour->image_path);

$tid = $tour->product_id ?? $tour->id ?? null;
if ($tid) {
$folder = "tours/{$tid}/gallery";
if (Storage::disk('public')->exists($folder)) {
$file = collect(Storage::disk('public')->files($folder))
->filter(fn($p) => preg_match('/\.(jpe?g|png|webp)$/i', $p))
->sort(fn($a,$b) => strnatcasecmp($a,$b))
->first();
if ($file) return asset('storage/'.$file);
}
}
return asset('images/volcano.png');
};
@endphp

<div class="dropdown cart-dropdown-{{ $variant }}">
  @auth
  <a href="#"
    id="{{ $triggerId }}"
    class="{{ $triggerCls }}"
    data-bs-toggle="dropdown"
    data-bs-auto-close="outside"
    data-bs-reference="parent"
    data-bs-offset="0,8"
    aria-expanded="false"
    aria-controls="{{ $menuId }}">
    <i class="{{ $iconCls }}" title="{{ __('adminlte::adminlte.cart') }}"></i>
    <span class="cart-count-badge badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle"
      style="{{ $headerCount ? '' : 'display:none;' }}">
      {{ $headerCount }}
    </span>
    @if($isDesktop)
    {{ __('adminlte::adminlte.cart') }}
    @endif
  </a>

  <div id="{{ $menuId }}"
    class="dropdown-menu dropdown-menu-end p-0 mini-cart-menu mini-cart-menu-{{ $variant }}"
    aria-labelledby="{{ $triggerId }}"
    data-variant="{{ $variant }}"
    data-expires-at="{{ $expiresIso }}"
    data-now="{{ now()->toIso8601String() }}"
    data-total-minutes="{{ $totalMinutesCfg }}"
    data-expire-endpoint="{{ route('public.carts.expire') }}">

    @if($headerCart && $headerCount)
    @if($expiresIso)
    <div class="mini-cart-timer-simple">
      <div class="timer-header">
        <div class="timer-text">
          <i class="fas fa-clock"></i>
          <span id="{{ $menuId }}-remaining">--:--</span>
        </div>
      </div>
      <div class="timer-bar-container">
        <div id="{{ $menuId }}-bar" class="timer-bar"></div>
      </div>
    </div>
    @endif

    <div class="mini-cart-list">
      @foreach($headerCart->items as $it)
      @php
      $img = $coverFromTour($it->tour);
      $itemCats = collect($it->categories ?? []);
      $sum = $itemCats->isNotEmpty()
      ? (float) $itemCats->sum(fn($c) => ((float)($c['price'] ?? 0)) * ((int)($c['quantity'] ?? 0)))
      : (float) (
      (($it->tour->adult_price ?? 0) * ($it->adults_quantity ?? 0)) +
      (($it->tour->kid_price ?? 0) * ($it->kids_quantity ?? 0))
      );

      $pax = $itemCats->isNotEmpty()
      ? (int) $itemCats->sum('quantity')
      : (int) (($it->adults_quantity ?? 0) + ($it->kids_quantity ?? 0));
      @endphp

      <div class="mini-cart-item-wrapper">
        <form class="mini-cart-remove-form"
          action="{{ route('public.carts.destroy', $it->item_id) }}"
          method="POST"
          onsubmit="return confirmMiniRemove(event, this);">
          @csrf @method('DELETE')
          <button type="submit" class="mini-cart-remove"
            aria-label="{{ __('adminlte::adminlte.remove_from_cart') }}">
            <i class="fas fa-times"></i>
          </button>
        </form>

        <div class="mini-cart-item">
          <img src="{{ $img }}" class="mini-cart-img" alt="">

          <div class="mini-cart-info">
            <div class="mini-cart-title">
              {{ $it->tour?->getTranslatedName() ?? '-' }}
            </div>

            <div class="mini-cart-meta">
              <i class="far fa-calendar-alt"></i>
              <span>{{ \Carbon\Carbon::parse($it->tour_date)->format('d/M/Y') }}</span>
              @if($it->schedule)
              <i class="fas fa-clock" style="margin-left:6px;"></i>
              <span>
                {{ \Carbon\Carbon::parse($it->schedule->start_time)->format('g:i A') }}
                – {{ \Carbon\Carbon::parse($it->schedule->end_time)->format('g:i A') }}
              </span>
              @endif
            </div>

            @if($itemCats->isNotEmpty())
            <div class="mini-cart-categories">
              @foreach($itemCats as $c)
              @php
              $cid = (int)($c['category_id'] ?? ($c['id'] ?? 0));
              $raw = $c['i18n_name'] ?? $c['name'] ?? null;

              $fromMap = null;
              if (!$raw && $cid > 0 && !empty($categoryNamesById[$cid])) {
              $fromMap = $categoryNamesById[$cid];
              }

              $fallbackSlug = null;
              if (!$raw && !$fromMap) {
              $slug = (string)($c['category_slug'] ?? '');
              $fallbackSlug = $slug
              ? \Illuminate\Support\Str::of($slug)->replace(['_','-'],' ')->title()
              : '—';
              }

              $cName = $raw ?: ($fromMap ?: $fallbackSlug);
              $cQty = (int) ($c['quantity'] ?? 0);
              @endphp
              <span class="category-badge">{{ $cName }} x{{ $cQty }}</span>
              @endforeach
            </div>
            @else
            @php
            $adults = (int) ($it->adults_quantity ?? 0);
            $kids = (int) ($it->kids_quantity ?? 0);
            $adultLabel = $adults === 1 ? __('adminlte::adminlte.adult') : __('adminlte::adminlte.adults');
            $kidLabel = $kids === 1 ? __('adminlte::adminlte.kid') : __('adminlte::adminlte.kids');
            @endphp
            <div class="mini-cart-categories">
              @if($adults > 0)<span class="category-badge">{{ $adultLabel }} x{{ $adults }}</span>@endif
              @if($kids > 0)<span class="category-badge">{{ $kidLabel }} x{{ $kids }}</span>@endif
            </div>
            @endif
          </div>

          <div class="mini-cart-price">
            ${{ number_format($sum, 2) }}
          </div>
        </div>
      </div>
      @endforeach
    </div>

    <div class="mini-cart-footer">
      {{-- Use the centralized Cart::calculateTotal() method --}}
      @php
      // This uses prices from the stored snapshot (already calculated with correct date)
      $calculatedTotal = $headerCart->calculateTotal();
      $displaySubtotal = $calculatedTotal;
      @endphp

      <div class="mini-cart-total">
        <span class="mini-cart-total-label">{{ __('adminlte::adminlte.subtotal') }}</span>
        <span class="mini-cart-total-amount">${{ number_format($displaySubtotal, 2) }}</span>
      </div>

      @if($promoData)
      <div class="mini-cart-promo">
        <div class="promo-info">
          <span class="promo-code">
            <i class="fas fa-tag"></i> {{ $promoData['code'] ?? 'PROMO' }}
          </span>
          <span class="promo-amount {{ $promoData['operation'] === 'add' ? 'text-danger' : 'text-success' }}">
            {{ $promoData['operation'] === 'add' ? '+' : '-' }}${{ number_format($promoData['adjustment'] ?? 0, 2) }}
          </span>
        </div>
      </div>
      @endif

      @php
      // Apply promo to calculated total
      if ($promoData) {
      $op = (($promoData['operation'] ?? 'subtract') === 'add') ? 1 : -1;
      $calculatedTotal = max(0, round($calculatedTotal + $op * (float)($promoData['adjustment'] ?? 0), 2));
      }
      @endphp

      <div class="mini-cart-total mini-cart-final-total">
        <span class="mini-cart-total-label">{{ __('adminlte::adminlte.total') }}</span>
        <span class="mini-cart-total-amount">${{ number_format($calculatedTotal, 2) }}</span>
      </div>

      <div class="mini-cart-actions">
        <a class="btn btn-cart-view btn-sm w-100 mb-2 rounded" href="{{ route('public.carts.index') }}">
          {{ __('adminlte::adminlte.view_cart') }}
        </a>

        {{-- 👇 Cambiado: ir al checkout (términos) --}}
        <a class="btn btn-primary w-100 rounded"
          href="{{ route('public.checkout.show') }}"
          id="mini-cart-confirm">
          {{ __('adminlte::adminlte.confirmBooking') }}
        </a>
      </div>
    </div>
    @else
    <div class="mini-cart-empty">
      {{ __('adminlte::adminlte.emptyCart') }}
    </div>
    @endif
  </div>
  @endauth

  @guest
  @php
  $allowGuestCheckout = config('site.allow_guest_checkout', true);
  
  $guestLinkCls = $isDesktop
  ? 'nav-link cart-icon-wrapper position-relative dropdown-toggle'
  : 'cart-icon-wrapper position-relative dropdown-toggle';

  // Get guest cart items from session (only if guest checkout is allowed)
  $guestCartItems = $allowGuestCheckout ? session('guest_cart_items', []) : [];
  $guestCount = count($guestCartItems);

  // Calculate total for guest cart
  $guestTotal = 0;
  foreach ($guestCartItems as $guestItem) {
  $categories = $guestItem['categories'] ?? [];
  foreach ($categories as $cat) {
  $guestTotal += ((float)($cat['price'] ?? 0)) * ((int)($cat['quantity'] ?? 0));
  }
  }

  $guestTriggerId = $isDesktop ? 'guestCartDropdownDesktop' : 'guestCartDropdownMobile';
  $guestMenuId = $guestTriggerId.'Menu';
  @endphp


  <a href="#"
    id="{{ $guestTriggerId }}"
    class="{{ $guestLinkCls }}"
    data-bs-toggle="dropdown"
    data-bs-auto-close="outside"
    data-bs-reference="parent"
    data-bs-offset="0,8"
    aria-expanded="false"
    aria-controls="{{ $guestMenuId }}">
    <i class="{{ $iconCls }}" title="{{ __('adminlte::adminlte.cart') }}"></i>
    <span class="cart-count-badge badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle"
      data-guest-count="{{ $guestCount }}"
      style="z-index: 1050; display: {{ $guestCount > 0 ? 'inline-block' : 'none' }} !important;">{{ $guestCount }}</span>
    @if($isDesktop) {{ __('adminlte::adminlte.cart') }} @endif
  </a>

  <div id="{{ $guestMenuId }}"
    class="dropdown-menu dropdown-menu-end p-0 mini-cart-menu mini-cart-menu-{{ $variant }}"
    aria-labelledby="{{ $guestTriggerId }}"
    data-variant="{{ $variant }}">

    @if($guestCount > 0)
    <div class="mini-cart-list">
      @foreach($guestCartItems as $index => $guestItem)
      @php
      $tour = \App\Models\Tour::find($guestItem['product_id']);
      $img = $tour ? $coverFromTour($tour) : asset('images/volcano.png');
      $itemCats = collect($guestItem['categories'] ?? []);

      $sum = 0;
      foreach ($itemCats as $c) {
      $sum += ((float)($c['price'] ?? 0)) * ((int)($c['quantity'] ?? 0));
      }

      $pax = (int) $itemCats->sum('quantity');
      @endphp

      <div class="mini-cart-item-wrapper">
        {{-- Remove button for guests --}}
        <form class="mini-cart-remove-form"
          action="{{ route('public.carts.removeGuestItem') }}"
          method="POST"
          onsubmit="return confirmMiniRemoveGuest(event, this);">
          @csrf
          <input type="hidden" name="item_index" value="{{ $index }}">
          <button type="submit" class="mini-cart-remove"
            aria-label="{{ __('adminlte::adminlte.remove_from_cart') }}">
            <i class="fas fa-times"></i>
          </button>
        </form>

        <div class="mini-cart-item">
          <img src="{{ $img }}" class="mini-cart-img" alt="">

          <div class="mini-cart-info">
            <div class="mini-cart-title">
              {{ $tour?->getTranslatedName() ?? '-' }}
            </div>

            <div class="mini-cart-meta">
              <i class="far fa-calendar-alt"></i>
              <span>{{ \Carbon\Carbon::parse($guestItem['tour_date'])->format('d/M/Y') }}</span>
              @php
              $schedule = isset($guestItem['schedule_id']) ? \App\Models\Schedule::find($guestItem['schedule_id']) : null;
              @endphp
              @if($schedule)
              <i class="fas fa-clock" style="margin-left:6px;"></i>
              <span>
                {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }}
                – {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
              </span>
              @endif
            </div>

            @if($itemCats->isNotEmpty())
            <div class="mini-cart-categories">
              @foreach($itemCats as $c)
              @php
              $cName = $c['i18n_name'] ?? $c['name'] ?? $c['category_slug'] ?? '—';
              $cQty = (int) ($c['quantity'] ?? 0);
              @endphp
              <span class="category-badge">{{ $cName }} x{{ $cQty }}</span>
              @endforeach
            </div>
            @endif
          </div>

          <div class="mini-cart-price">
            ${{ number_format($sum, 2) }}
          </div>
        </div>
      </div>
      @endforeach
    </div>

    <div class="mini-cart-footer">
      <div class="mini-cart-total mini-cart-final-total">
        <span class="mini-cart-total-label">{{ __('adminlte::adminlte.total') }}</span>
        <span class="mini-cart-total-amount">${{ number_format($guestTotal, 2) }}</span>
      </div>

      <div class="mini-cart-actions">
        <a class="btn btn-cart-view btn-sm w-100 mb-2 rounded" href="{{ route('public.carts.index') }}">
          {{ __('adminlte::adminlte.view_cart') }}
        </a>

        <a class="btn btn-primary w-100 rounded"
          href="{{ route('public.checkout.show') }}"
          id="mini-cart-confirm">
          {{ __('adminlte::adminlte.confirmBooking') }}
        </a>
      </div>
    </div>
    @else
    <div class="mini-cart-empty">
      {{ __('adminlte::adminlte.emptyCart') }}
    </div>
    @endif
  </div>
  @endguest


  <script>
    // Guest cart badge management - hybrid approach with visibility insurance
    (function() {
      let lastKnownCount = 0;

      function updateGuestBadge() {
        const guestBadges = document.querySelectorAll('[data-guest-count]');
        guestBadges.forEach(badge => {
          // Read both optimistic (textContent) and server (data-guest-count) values
          const textCount = parseInt(badge.textContent) || 0;
          const dataCount = parseInt(badge.getAttribute('data-guest-count')) || 0;

          // Use the maximum (allows optimistic updates to work)
          const count = Math.max(textCount, dataCount, lastKnownCount);

          if (count > 0) {
            lastKnownCount = count;
            badge.textContent = count;
            // Keep data attribute in sync
            badge.setAttribute('data-guest-count', count);
            badge.style.setProperty('display', 'inline-block', 'important');
          } else {
            badge.style.display = 'none';
          }
        });
      }

      // Run on page load
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateGuestBadge);
      } else {
        updateGuestBadge();
      }

      // Also run when cart changes (optimistic updates)
      window.addEventListener('cart:changed', updateGuestBadge);

      // Periodic check to ensure visibility (less aggressive - every 2 seconds)
      setInterval(updateGuestBadge, 2000);
    })();
  </script>
</div>

@once
<style>
  /* Category badges */
  .mini-cart-categories {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-top: 6px;
  }

  .category-badge {
    display: inline-block;
    padding: 2px 8px;
    background: #e9ecef;
    border-radius: 12px;
    font-size: 0.75rem;
    color: #495057;
    white-space: nowrap;
  }

  /* Promo code section */
  .mini-cart-promo {
    padding: 8px 12px;
    background: #f8f9fa;
    border-top: 1px dashed #dee2e6;
    border-bottom: 1px dashed #dee2e6;
    margin: 8px 0;
  }

  .promo-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
  }

  .promo-code {
    color: #6c757d;
    font-weight: 500;
    font-size: 0.75rem;
  }

  .promo-code i {
    color: #28a745;
    margin-right: 4px;
  }

  .promo-amount {
    font-weight: 600;
    font-size: 0.85rem;
  }

  .promo-amount.text-success {
    color: #dc3545 !important;
    /* primary-red for discount */
  }

  .promo-amount.text-danger {
    color: #dc3545 !important;
    /* primary-red for surcharge */
  }

  /* Tax breakdown section */
  .mini-cart-taxes {
    padding: 8px 12px;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    margin: 8px 0;
  }

  .tax-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    padding: 4px 0;
    color: #6c757d;
  }

  .tax-name {
    font-weight: 500;
  }

  .tax-amount {
    font-weight: 600;
    color: #495057;
  }

  .mini-cart-final-total {
    border-top: 2px solid #dee2e6;
    padding-top: 12px;
    margin-top: 8px;
  }

  .mini-cart-final-total .mini-cart-total-label {
    font-weight: 600;
    font-size: 1rem;
  }

  .mini-cart-final-total .mini-cart-total-amount {
    font-weight: 700;
    font-size: 1.15rem;
    color: #006633 !important;
    /* primary-dark */
  }

  /* Mobile optimizations */
  @media (max-width: 576px) {
    .mini-cart-menu-mobile {
      max-width: 95vw !important;
      min-width: 320px !important;
    }

    .mini-cart-item {
      padding: 8px !important;
      gap: 8px !important;
    }

    .mini-cart-img {
      width: 50px !important;
      height: 50px !important;
      min-width: 50px !important;
    }

    .mini-cart-title {
      font-size: 0.85rem !important;
      line-height: 1.2 !important;
      max-height: 2.4em !important;
      -webkit-line-clamp: 2 !important;
    }

    .mini-cart-meta {
      font-size: 0.7rem !important;
      gap: 4px !important;
      flex-wrap: wrap !important;
    }

    .mini-cart-meta i {
      font-size: 0.65rem !important;
    }

    .mini-cart-price {
      font-size: 0.9rem !important;
      min-width: 70px !important;
    }

    .category-badge {
      font-size: 0.7rem !important;
      padding: 1px 6px !important;
    }

    .mini-cart-remove {
      width: 20px !important;
      height: 20px !important;
      font-size: 0.7rem !important;
    }

    .mini-cart-timer-simple .timer-header {
      padding: 8px 10px !important;
    }

    .timer-text {
      font-size: 0.85rem !important;
    }

    .timer-extend-btn {
      font-size: 0.75rem !important;
      padding: 4px 8px !important;
    }

    .timer-extend-btn .btn-text {
      display: none !important;
    }

    .timer-extend-btn i {
      margin-right: 0 !important;
    }

    .mini-cart-total-label {
      font-size: 0.85rem !important;
    }

    .mini-cart-total-amount {
      font-size: 1.1rem !important;
    }

    .mini-cart-actions .btn {
      font-size: 0.85rem !important;
      padding: 8px 12px !important;
    }
  }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function askLoginWithSwal(e, loginUrl) {
    e.preventDefault();
    if (window.Swal) {
      Swal.fire({
        title: @json(__('adminlte::adminlte.login_required_title')),
        text: @json(__('adminlte::adminlte.login_required_text')),
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: @json(__('adminlte::adminlte.login')),
        cancelButtonText: @json(__('adminlte::adminlte.cancel')),
        confirmButtonColor: '#198754',
        allowOutsideClick: false
      }).then(res => {
        if (res.isConfirmed) window.location.href = loginUrl;
      });
    } else {
      if (confirm(@json(__('adminlte::adminlte.login_required_text_confirm'))))
        window.location.href = loginUrl;
    }
    return false;
  }

  function confirmMiniRemove(e, formEl) {
    e.preventDefault();
    e.stopPropagation();

    const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
    openDropdowns.forEach(dd => {
      const trigger = document.querySelector(`[aria-controls="${dd.id}"]`);
      if (trigger && window.bootstrap) {
        const bsDropdown = bootstrap.Dropdown.getInstance(trigger);
        if (bsDropdown) bsDropdown.hide();
      }
    });

    if (window.Swal) {
      setTimeout(() => {
        Swal.fire({
          title: @json(__('adminlte::adminlte.remove_item_title')),
          text: @json(__('adminlte::adminlte.remove_item_text')),
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: @json(__('adminlte::adminlte.delete')),
          cancelButtonText: @json(__('adminlte::adminlte.cancel')),
          confirmButtonColor: '#dc3545'
        }).then(res => {
          if (res.isConfirmed) {
            const btn = formEl.querySelector('button[type="submit"]');
            if (btn) {
              btn.disabled = true;
              btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
            formEl.submit();
            window.dispatchEvent(new Event('cart:changed'));
          }
        });
      }, 150);
    } else {
      if (confirm(@json(__('adminlte::adminlte.remove_item_text')))) {
        formEl.submit();
        window.dispatchEvent(new Event('cart:changed'));
      }
    }
    return false;
  }

  // Guest cart remove confirmation
  function confirmMiniRemoveGuest(e, formEl) {
    e.preventDefault();
    e.stopPropagation();

    const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
    openDropdowns.forEach(dd => {
      const trigger = document.querySelector(`[aria-controls="${dd.id}"]`);
      if (trigger && window.bootstrap) {
        const bsDropdown = bootstrap.Dropdown.getInstance(trigger);
        if (bsDropdown) bsDropdown.hide();
      }
    });

    if (window.Swal) {
      setTimeout(() => {
        Swal.fire({
          title: @json(__('adminlte::adminlte.remove_item_title')),
          text: @json(__('adminlte::adminlte.remove_item_text')),
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: @json(__('adminlte::adminlte.delete')),
          cancelButtonText: @json(__('adminlte::adminlte.cancel')),
          confirmButtonColor: '#dc3545'
        }).then(res => {
          if (res.isConfirmed) {
            const btn = formEl.querySelector('button[type="submit"]');
            if (btn) {
              btn.disabled = true;
              btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
            formEl.submit();
            // Reload page to update guest cart
            setTimeout(() => window.location.reload(), 300);
          }
        });
      }, 150);
    } else {
      if (confirm(@json(__('adminlte::adminlte.remove_item_text')))) {
        formEl.submit();
        setTimeout(() => window.location.reload(), 300);
      }
    }
    return false;
  }

  document.addEventListener('DOMContentLoaded', function() {
    initCartTimer('cartDropdownDesktopMenu', 'cartDropdownDesktop');
    initCartTimer('cartDropdownMobileMenu', 'cartDropdownMobile');

    // Update guest cart badge when cart changes
    window.addEventListener('cart:changed', function(e) {
      // If no count provided in event, do nothing (prevent hiding badge on empty events)
      if (e.detail === undefined || e.detail.count === undefined) return;

      // Update all cart count badges
      const badges = document.querySelectorAll('.cart-count-badge');
      const count = e.detail.count;

      badges.forEach(badge => {
        badge.textContent = count;
        badge.style.display = count > 0 ? '' : 'none';
      });
    });
  });

  function initCartTimer(menuId, triggerId) {
    const menu = document.getElementById(menuId);
    if (!menu) return;

    const remainingEl = document.getElementById(menuId + '-remaining');
    const barEl = document.getElementById(menuId + '-bar');
    const btnExtend = document.getElementById(menuId + '-extend');
    const triggerEl = document.getElementById(triggerId);
    const timerBox = menu.querySelector('.mini-cart-timer-simple');

    if (!remainingEl || !barEl) return;

    const parseIsoSafe = (s) => {
      if (!s) return NaN;
      const clean = s.replace(/\.\d{1,6}(Z)?$/, '$1');
      const t = Date.parse(clean);
      return isNaN(t) ? Date.parse(s) : t;
    };

    const fmt = (sec) => {
      const s = Math.max(0, sec | 0);
      const m = Math.floor(s / 60),
        r = s % 60;
      return String(m).padStart(2, '0') + ':' + String(r).padStart(2, '0');
    };

    const setBar = (remainingSec, totalMins) => {
      if (!barEl || !timerBox) return;
      const totalS = Math.max(1, (Number(totalMins) || 15) * 60);
      const frac = Math.max(0, Math.min(1, remainingSec / totalS));
      barEl.style.width = (frac * 100).toFixed(2) + '%';

      if (frac > 0.5) {
        barEl.style.background = 'linear-gradient(90deg, #28a745, #20c997)';
        timerBox.style.background = '#f8f9fa';
        timerBox.classList.remove('timer-warning', 'timer-danger');
      } else if (frac > 0.25) {
        barEl.style.background = 'linear-gradient(90deg, #ffc107, #fd7e14)';
        timerBox.style.background = '#fff3cd';
        timerBox.classList.add('timer-warning');
        timerBox.classList.remove('timer-danger');
      } else {
        barEl.style.background = 'linear-gradient(90deg, #dc3545, #c82333)';
        timerBox.style.background = '#f8d7da';
        timerBox.classList.add('timer-danger');
        timerBox.classList.remove('timer-warning');
      }
    };

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const expStr = menu.getAttribute('data-expires-at') || '';
    const nowStr = menu.getAttribute('data-now') || '';
    const totalM = Number(menu.getAttribute('data-total-minutes') || '15');
    const expireEndpoint = menu.getAttribute('data-expire-endpoint') || '';
    let extendedCount = 0;
    const maxExt = 0;

    if (!expStr) {
      if (timerBox) timerBox.style.display = 'none';
      return;
    }

    let serverExpires = parseIsoSafe(expStr);
    const serverNow = parseIsoSafe(nowStr);
    const clientNow = Date.now();
    const skewMs = isNaN(serverNow) ? 0 : (serverNow - clientNow);

    let intervalId = null;

    const handleExpire = async () => {
      try {
        await fetch(expireEndpoint, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json'
          }
        });
      } catch (e) {}

      const list = menu.querySelector('.mini-cart-list');
      const footer = menu.querySelector('.mini-cart-footer');
      if (list) list.innerHTML = '';
      if (footer) footer.remove();

      const empty = document.createElement('div');
      empty.className = 'mini-cart-empty';
      empty.textContent = @json(__('adminlte::adminlte.emptyCart'));
      menu.appendChild(empty);

      document.querySelectorAll('.cart-count-badge').forEach(b => {
        b.textContent = '0';
        b.style.display = 'none';
      });

      window.dispatchEvent(new Event('cart:changed'));

      if (window.Swal) {
        Swal.fire({
          icon: 'info',
          title: @json(__('carts.messages.cart_expired')),
          timer: 1800,
          showConfirmButton: false
        });
      }
    };

    const updateOnce = (targetMs) => {
      const now = Date.now() + skewMs;
      const remainingSec = Math.ceil((targetMs - now) / 1000);
      remainingEl.textContent = fmt(remainingSec);
      setBar(remainingSec, totalM);
      if (remainingSec <= 0) {
        stopTick();
        handleExpire();
      }
    };

    const startTick = () => {
      stopTick();
      updateOnce(serverExpires);
      intervalId = setInterval(() => updateOnce(serverExpires), 1000);
    };

    const stopTick = () => {
      if (intervalId) {
        clearInterval(intervalId);
        intervalId = null;
      }
    };

    if (triggerEl) {
      triggerEl.addEventListener('shown.bs.dropdown', startTick);
      triggerEl.addEventListener('hidden.bs.dropdown', stopTick);
    }

    if (menu.classList.contains('show')) startTick();
  }
</script>
@endonce