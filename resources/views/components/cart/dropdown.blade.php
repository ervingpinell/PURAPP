@props(['variant' => 'desktop'])

@php
  use Illuminate\Support\Facades\Storage;

  $isDesktop   = $variant === 'desktop';
  $triggerId   = $isDesktop ? 'cartDropdownDesktop' : 'cartDropdownMobile';
  $menuId      = $triggerId.'Menu';
  $triggerCls  = $isDesktop
      ? 'nav-link cart-icon-wrapper position-relative dropdown-toggle'
      : 'cart-icon-wrapper position-relative dropdown-toggle';
  $iconCls     = $isDesktop ? 'fas fa-shopping-cart' : 'fas fa-shopping-cart text-white';

  $headerCart  = $headerCart  ?? null;
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
            (($i->tour->kid_price   ?? 0) * ($i->kids_quantity   ?? 0))
          );
        })
      : 0.0
  );

  $expiresIso      = optional($headerCart?->expires_at)->toIso8601String();
  $totalMinutesCfg = (int) config('cart.expiry_minutes', 15);
  $extendMinutes   = (int) config('cart.extend_minutes', 10);
  $maxExt          = (int) config('cart.max_extensions', 1);
  $extendedCount   = (int) ($headerCart->extended_count ?? 0);

  $coverFromTour = function ($tour) {
      if (!$tour) return asset('images/volcano.png');
      if (!empty($tour->image_path)) return asset('storage/'.$tour->image_path);

      $tid = $tour->tour_id ?? $tour->id ?? null;
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
      <span class="cart-count-badge badge rounded-pill position-absolute top-0 start-100 translate-middle"
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
         data-extend-minutes="{{ $extendMinutes }}"
         data-extended-count="{{ $extendedCount }}"
         data-max-extensions="{{ $maxExt }}"
         data-expire-endpoint="{{ route('public.carts.expire') }}"
         data-refresh-endpoint="{{ route('public.carts.refreshExpiry') }}">

      @if($headerCart && $headerCount)
        @if($expiresIso)
          <div class="mini-cart-timer-simple">
            <div class="timer-header">
              <div class="timer-text">
                <i class="fas fa-clock"></i>
                <span id="{{ $menuId }}-remaining">--:--</span>
              </div>
              <button id="{{ $menuId }}-extend"
                      type="button"
                      class="timer-extend-btn"
                      data-menu-id="{{ $menuId }}">
                <i class="fas fa-plus-circle"></i>
                <span class="btn-text">{{ trans_choice('carts.timer.extend', $extendMinutes, ['count'=>$extendMinutes]) }}</span>
              </button>
            </div>
            <div class="timer-bar-container">
              <div id="{{ $menuId }}-bar" class="timer-bar"></div>
            </div>
          </div>
        @endif

        <div class="mini-cart-list">
          @foreach($headerCart->items as $it)
            @php
              $img      = $coverFromTour($it->tour);
              $itemCats = collect($it->categories ?? []);
              $sum      = $itemCats->isNotEmpty()
                ? (float) $itemCats->sum(fn($c) => ((float)($c['price'] ?? 0)) * ((int)($c['quantity'] ?? 0)))
                : (float) (
                    (($it->tour->adult_price ?? 0) * ($it->adults_quantity ?? 0)) +
                    (($it->tour->kid_price   ?? 0) * ($it->kids_quantity   ?? 0))
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
                    <ul class="mini-cart-cats">
                      @foreach($itemCats as $c)
                        @php
                          $cid   = (int)($c['category_id'] ?? ($c['id'] ?? 0));
                          $raw   = $c['i18n_name'] ?? $c['name'] ?? null;

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

                          $cName  = $raw ?: ($fromMap ?: $fallbackSlug);
                          $cQty   = (int)   ($c['quantity'] ?? 0);
                          $cPrice = (float) ($c['price'] ?? 0);
                          $cSub   = $cQty * $cPrice;
                        @endphp
                        <li class="mini-cart-cat-row">
                          <span class="mini-cart-cat-name">{{ $cName }}</span>
                          <span class="mini-cart-cat-qty">x{{ $cQty }}</span>
                          <span class="mini-cart-cat-price">${{ number_format($cPrice, 2) }}</span>
                          <span class="mini-cart-cat-sub">=${{ number_format($cSub, 2) }}</span>
                        </li>
                      @endforeach
                    </ul>
                    <div class="mini-cart-cat-total small text-muted">
                      {{ trans_choice('adminlte::adminlte.persons.count', $pax, ['count' => $pax]) }}
                    </div>
                  @else
                    @php
                      $adults = (int) ($it->adults_quantity ?? 0);
                      $kids   = (int) ($it->kids_quantity   ?? 0);
                      $adultLabel = $adults === 1 ? __('adminlte::adminlte.adult') : __('adminlte::adminlte.adults');
                      $kidLabel   = $kids   === 1 ? __('adminlte::adminlte.kid')   : __('adminlte::adminlte.kids');
                    @endphp
                    <div class="mini-cart-cat-total small text-muted">
                      {{ $adults }} {{ $adultLabel }}@if($kids > 0) · {{ $kids }} {{ $kidLabel }}@endif
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
          <div class="mini-cart-total">
            <span class="mini-cart-total-label">{{ __('adminlte::adminlte.totalEstimated') }}</span>
            <span class="mini-cart-total-amount">${{ number_format($headerTotal, 2) }}</span>
          </div>

          <div class="mini-cart-actions">
            <a class="btn btn-success btn-sm w-100 mb-2" href="{{ route('public.carts.index') }}">
              {{ __('adminlte::adminlte.view_cart') }}
            </a>

            {{-- 👇 Cambiado: ir al checkout (términos) --}}
            <a class="btn btn-outline-success btn-sm w-100"
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
  @else
    @php
      $guestLinkCls = $isDesktop
        ? 'nav-link cart-icon-wrapper position-relative'
        : 'cart-icon-wrapper position-relative';
    @endphp
    <a href="{{ route('login') }}"
       class="{{ $guestLinkCls }}"
       onclick="return askLoginWithSwal(event, this.href);">
      <i class="{{ $iconCls }}" title="{{ __('adminlte::adminlte.cart') }}"></i>
      <span class="cart-count-badge badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle"
            style="font-size:.7rem;display:none;">0</span>
      @if($isDesktop) {{ __('adminlte::adminlte.cart') }} @endif
    </a>
  @endauth
</div>

@once
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    function askLoginWithSwal(e, loginUrl) {
      e.preventDefault();
      if (window.Swal) {
        Swal.fire({
          title: @json(__('adminlte::adminlte.login_required_title')),
          text:  @json(__('adminlte::adminlte.login_required_text')),
          icon: 'info',
          showCancelButton: true,
          confirmButtonText: @json(__('adminlte::adminlte.login')),
          cancelButtonText:  @json(__('adminlte::adminlte.cancel')),
          confirmButtonColor: '#198754',
          allowOutsideClick: false
        }).then(res => { if (res.isConfirmed) window.location.href = loginUrl; });
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
            text:  @json(__('adminlte::adminlte.remove_item_text')),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: @json(__('adminlte::adminlte.delete')),
            cancelButtonText:  @json(__('adminlte::adminlte.cancel')),
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

    document.addEventListener('DOMContentLoaded', function() {
      initCartTimer('cartDropdownDesktopMenu', 'cartDropdownDesktop');
      initCartTimer('cartDropdownMobileMenu',  'cartDropdownMobile');
    });

    function initCartTimer(menuId, triggerId) {
      const menu = document.getElementById(menuId);
      if (!menu) return;

      const remainingEl = document.getElementById(menuId + '-remaining');
      const barEl       = document.getElementById(menuId + '-bar');
      const btnExtend   = document.getElementById(menuId + '-extend');
      const triggerEl   = document.getElementById(triggerId);
      const timerBox    = menu.querySelector('.mini-cart-timer-simple');

      if (!remainingEl || !barEl) return;

      const parseIsoSafe = (s) => {
        if (!s) return NaN;
        const clean = s.replace(/\.\d{1,6}(Z)?$/, '$1');
        const t = Date.parse(clean);
        return isNaN(t) ? Date.parse(s) : t;
      };

      const fmt = (sec) => {
        const s = Math.max(0, sec | 0);
        const m = Math.floor(s / 60), r = s % 60;
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

      const csrf            = document.querySelector('meta[name="csrf-token"]')?.content || '';
      const expStr          = menu.getAttribute('data-expires-at') || '';
      const nowStr          = menu.getAttribute('data-now') || '';
      const totalM          = Number(menu.getAttribute('data-total-minutes') || '15');
      const expireEndpoint  = menu.getAttribute('data-expire-endpoint') || '';
      const refreshEndpoint = menu.getAttribute('data-refresh-endpoint') || '';
      let   extendedCount   = Number(menu.getAttribute('data-extended-count') || '0');
      const maxExt          = Number(menu.getAttribute('data-max-extensions') || '1');

      if (!expStr) {
        if (timerBox) timerBox.style.display = 'none';
        return;
      }

      let serverExpires = parseIsoSafe(expStr);
      const serverNow   = parseIsoSafe(nowStr);
      const clientNow   = Date.now();
      const skewMs      = isNaN(serverNow) ? 0 : (serverNow - clientNow);

      let intervalId = null;

      const disableExtendIfNeeded = () => {
        if (!btnExtend) return;
        const can = extendedCount < maxExt;
        btnExtend.disabled = !can;
        btnExtend.style.opacity = can ? '1' : '.5';
        btnExtend.style.cursor  = can ? 'pointer' : 'not-allowed';
      };

      const handleExpire = async () => {
        try {
          await fetch(expireEndpoint, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
        } catch (e) {}

        const list   = menu.querySelector('.mini-cart-list');
        const footer = menu.querySelector('.mini-cart-footer');
        if (list)   list.innerHTML = '';
        if (footer) footer.remove();

        const empty = document.createElement('div');
        empty.className = 'mini-cart-empty';
        empty.textContent = @json(__('adminlte::adminlte.emptyCart'));
        menu.appendChild(empty);

        document.querySelectorAll('.cart-count-badge').forEach(b => {
          b.textContent = '0'; b.style.display = 'none';
        });

        window.dispatchEvent(new Event('cart:changed'));

        if (window.Swal) {
          Swal.fire({ icon: 'info', title: @json(__('carts.messages.cart_expired')), timer: 1800, showConfirmButton: false });
        }
      };

      const updateOnce = (targetMs) => {
        const now = Date.now() + skewMs;
        const remainingSec = Math.ceil((targetMs - now) / 1000);
        remainingEl.textContent = fmt(remainingSec);
        setBar(remainingSec, totalM);
        if (remainingSec <= 0) { stopTick(); handleExpire(); }
      };

      const startTick = () => {
        stopTick();
        disableExtendIfNeeded();
        updateOnce(serverExpires);
        intervalId = setInterval(() => updateOnce(serverExpires), 1000);
      };

      const stopTick = () => {
        if (intervalId) { clearInterval(intervalId); intervalId = null; }
      };

      if (btnExtend) {
        btnExtend.addEventListener('click', async (e) => {
          e.preventDefault();
          e.stopPropagation();
          if (extendedCount >= maxExt) { disableExtendIfNeeded(); return; }

          try {
            btnExtend.disabled = true;
            const originalHTML = btnExtend.innerHTML;
            btnExtend.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            const res  = await fetch(refreshEndpoint, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }});
            const data = await res.json();

            if (data?.ok && data?.expires_at) {
              const next = parseIsoSafe(String(data.expires_at));
              if (!isNaN(next)) {
                serverExpires = next;
                extendedCount = Number(data.extended_count ?? (extendedCount + 1));
                menu.setAttribute('data-extended-count', extendedCount);
                disableExtendIfNeeded();
                updateOnce(serverExpires);
                stopTick(); intervalId = setInterval(() => updateOnce(serverExpires), 1000);
              }
              if (window.Swal) Swal.fire({ icon: 'success', title: @json(__('carts.messages.extend_success')), timer: 1200, showConfirmButton: false });
            } else if (data?.expired) {
              stopTick(); await handleExpire();
            }

            btnExtend.innerHTML = originalHTML;
          } catch (err) {
            if (window.Swal) Swal.fire({ icon: 'error', title: @json(__('carts.messages.code_apply_failed')), timer: 1500, showConfirmButton: false });
          } finally {
            btnExtend.disabled = extendedCount >= maxExt;
          }
        });
      }

      if (triggerEl) {
        triggerEl.addEventListener('shown.bs.dropdown', startTick);
        triggerEl.addEventListener('hidden.bs.dropdown', stopTick);
      }

      if (menu.classList.contains('show')) startTick();
    }
  </script>
@endonce
