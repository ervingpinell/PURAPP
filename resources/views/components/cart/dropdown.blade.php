@props(['variant' => 'desktop'])

@php
  use Illuminate\Support\Facades\Storage;

  $isDesktop   = $variant === 'desktop';
  $triggerId   = $isDesktop ? 'cartDropdownDesktop' : 'cartDropdownMobile';
  $triggerCls  = $isDesktop
      ? 'nav-link cart-icon-wrapper position-relative dropdown-toggle'
      : 'cart-icon-wrapper position-relative dropdown-toggle';
  $iconCls     = $isDesktop ? 'fas fa-shopping-cart' : 'fas fa-shopping-cart text-white';
  $headerCart  = $headerCart  ?? null;
  $headerCount = $headerCount ?? ($headerCart?->items?->count() ?? 0);
  $headerTotal = $headerTotal ?? ($headerCart
                     ? $headerCart->items->sum(fn($i) =>
                         ($i->tour->adult_price ?? 0) * ($i->adults_quantity ?? 0) +
                         ($i->tour->kid_price   ?? 0) * ($i->kids_quantity   ?? 0))
                     : 0);

  // Obtiene la imagen de portada del tour
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

<div class="dropdown">
  @auth
    <a href="#"
       id="{{ $triggerId }}"
       class="{{ $triggerCls }}"
       data-bs-toggle="dropdown"
       data-bs-auto-close="outside"
       aria-expanded="false">
      <i class="{{ $iconCls }}" title="{{ __('adminlte::adminlte.cart') }}"></i>
      <span class="cart-count-badge badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle"
            style="font-size:.7rem; {{ $headerCount ? '' : 'display:none;' }}">
        {{ $headerCount }}
      </span>
      @if($isDesktop)
        {{ __('adminlte::adminlte.cart') }}
      @endif
    </a>

    <div class="dropdown-menu dropdown-menu-end p-0 mini-cart-menu" aria-labelledby="{{ $triggerId }}">
      @if($headerCart && $headerCount)
        <div class="mini-cart-list" style="max-height:60vh;overflow:auto;">
          @foreach($headerCart->items as $it)
            @php
              $img = $coverFromTour($it->tour);
              $sum = ($it->tour->adult_price ?? 0) * ($it->adults_quantity ?? 0)
                   + ($it->tour->kid_price   ?? 0) * ($it->kids_quantity   ?? 0);
            @endphp

            <div class="d-flex gap-2 p-3 border-bottom position-relative mini-cart-item">
              {{-- Eliminar item --}}
              <form class="mini-cart-remove-form"
                    action="{{ route('public.cart.destroy', $it->item_id) }}"
                    method="POST"
                    onsubmit="return confirmMiniRemove(event, this);">
                @csrf @method('DELETE')
                <button type="submit" class="mini-cart-remove"
                        aria-label="{{ __('adminlte::adminlte.remove_from_cart') }}">
                  <i class="fas fa-times"></i>
                </button>
              </form>

              <img src="{{ $img }}" class="rounded" alt=""
                   style="width:56px;height:56px;object-fit:cover;">
              <div class="flex-grow-1 pe-4">
                <div class="fw-semibold small">{{ $it->tour?->getTranslatedName() ?? '-' }}</div>
                <div class="text-muted" style="font-size:.82rem;">
                  {{ \Carbon\Carbon::parse($it->tour_date)->translatedFormat('d M Y') }}<br>
                  @if($it->schedule)
                    {{ \Carbon\Carbon::parse($it->schedule->start_time)->format('g:i A') }}
                    – {{ \Carbon\Carbon::parse($it->schedule->end_time)->format('g:i A') }}<br>
                  @endif

                  @php
                    $adults = (int) ($it->adults_quantity ?? 0);
                    $kids   = (int) ($it->kids_quantity ?? 0);

                    $adultLabel = $adults === 1
                        ? __('adminlte::adminlte.adult')
                        : __('adminlte::adminlte.adults');
                    $kidLabel = $kids === 1
                        ? __('adminlte::adminlte.kid')
                        : __('adminlte::adminlte.kids');
                  @endphp

                  {{ $adults }} {{ $adultLabel }}
                  @if($kids > 0) | {{ $kids }} {{ $kidLabel }} @endif
                </div>
              </div>

              <div class="fw-bold small text-success mini-cart-price">
                ${{ number_format($sum, 2) }}
              </div>
            </div>
          @endforeach
        </div>

        <div class="p-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-semibold">{{ __('adminlte::adminlte.totalEstimated') }}</span>
            <span class="fw-bold" style="color:#006633">
              ${{ number_format($headerTotal, 2) }}
            </span>
          </div>
          <div class="d-grid gap-2">
            <a class="btn btn-success btn-sm" href="{{ route('public.cart.index') }}">
              {{ __('adminlte::adminlte.view_cart') }}
            </a>
            <form action="{{ route('public.reservas.storeFromCart') }}" method="POST">
              @csrf
              <button class="btn btn-outline-success btn-sm w-100">
                {{ __('adminlte::adminlte.confirmBooking') }}
              </button>
            </form>
          </div>
        </div>
      @else
        <div class="p-4 text-center text-muted small">
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
    // 🔒 Confirmar login
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

    // 🗑️ Confirmar eliminación de ítem
    function confirmMiniRemove(e, formEl) {
      e.preventDefault(); e.stopPropagation();
      if (window.Swal) {
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
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; }
            formEl.submit();
            // 🔄 Actualiza contador global
            window.dispatchEvent(new Event('cart:changed'));
          }
        });
      } else {
        if (confirm(@json(__('adminlte::adminlte.remove_item_text')))) {
          formEl.submit();
          window.dispatchEvent(new Event('cart:changed'));
        }
      }
      return false;
    }
  </script>

  <style>
    .cart-icon-wrapper { text-decoration:none!important; -webkit-tap-highlight-color:transparent; }
    .cart-icon-wrapper:hover,
    .cart-icon-wrapper:focus,
    .cart-icon-wrapper:active { text-decoration:none!important; outline:none!important; box-shadow:none!important; }
    .cart-icon-wrapper.dropdown-toggle::after { border-top-color: currentColor!important; }
    .navbar-dark .cart-icon-wrapper, .bg-dark .cart-icon-wrapper { color:#fff!important; }

    .mini-cart-menu{ width:360px; border-radius:14px; box-shadow:0 10px 30px rgba(0,0,0,.15); }
    .mini-cart-list .d-flex:hover{ background:#f8f9fa; }
    .mini-cart-item{ padding-right:3rem; }
    .mini-cart-price{ margin-right:36px; }

    .mini-cart-remove-form{ position:absolute; top:8px; right:8px; z-index:2; }
    .mini-cart-remove{
      width:28px; height:28px; border-radius:999px;
      background:#fff; border:1px solid #e5e7eb;
      box-shadow:0 2px 8px rgba(0,0,0,.08);
      display:flex; align-items:center; justify-content:center;
      color:#6c757d; line-height:1;
      transition:all .2s ease;
    }
    .mini-cart-remove:hover{
      background:#fee2e2; color:#dc3545;
      border-color:#fecaca; transform:scale(1.05);
    }
  </style>
@endonce
