@php
  $variant = $variant ?? 'desktop';
  $isDesktop = $variant === 'desktop';

  $triggerId = $id ?? ($isDesktop ? 'cartDropdownDesktop' : 'cartDropdownMobile');
  $triggerClasses = $isDesktop
      ? 'nav-link cart-icon-wrapper position-relative dropdown-toggle'
      : 'cart-icon-wrapper position-relative dropdown-toggle';
  $iconClasses = $isDesktop ? 'fas fa-shopping-cart' : 'fas fa-shopping-cart text-white';
@endphp

<div class="dropdown">
  @auth
    <a href="#"
       class="{{ $triggerClasses }}"
       id="{{ $triggerId }}"
       data-bs-toggle="dropdown"
       data-bs-auto-close="outside"
       aria-expanded="false">
      <i class="{{ $iconClasses }}" title="{{ __('adminlte::adminlte.cart') }}"></i>

      <span class="cart-count-badge badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle"
            style="font-size:.7rem; {{ $headerCount ? '' : 'display:none;' }}">{{ $headerCount }}</span>

    </a>

    <div class="dropdown-menu dropdown-menu-end p-0 mini-cart-menu" aria-labelledby="{{ $triggerId }}">
      @if($headerCart && $headerCount)
        <div class="mini-cart-list" style="max-height:60vh;overflow:auto;">
          @foreach($headerCart->items as $it)
            @php
              $img = $it->tour->image_path ? asset('storage/'.$it->tour->image_path) : asset('images/volcano.png');
              $sum = ($it->tour->adult_price ?? 0) * ($it->adults_quantity ?? 0)
                   + ($it->tour->kid_price   ?? 0) * ($it->kids_quantity   ?? 0);
            @endphp

            <div class="d-flex gap-2 p-3 border-bottom position-relative mini-cart-item">
              {{-- Botón eliminar --}}
              <form class="mini-cart-remove-form"
                    action="{{ route('public.cart.destroy', $it->item_id) }}"
                    method="POST"
                    onsubmit="return confirmMiniRemove(event, this);">
                @csrf @method('DELETE')
                <button type="submit"
                        class="mini-cart-remove"
                        aria-label="{{ __('adminlte::adminlte.remove_from_cart') }}">
                  <i class="fas fa-times"></i>
                </button>
              </form>

              <img src="{{ $img }}" class="rounded" alt="" style="width:56px;height:56px;object-fit:cover;">
              <div class="flex-grow-1 pe-4">
                <div class="fw-semibold small">{{ $it->tour->getTranslatedName() }}</div>

                {{-- Fecha --}}
                <div class="text-muted" style="font-size:.82rem;">
                  {{ \Carbon\Carbon::parse($it->tour_date)->translatedFormat('d M, Y') }}
                </div>

                {{-- Horario --}}
                @if($it->schedule)
                  <div class="text-muted" style="font-size:.82rem;">
                    {{ \Carbon\Carbon::parse($it->schedule->start_time)->format('g:i A') }}
                    – {{ \Carbon\Carbon::parse($it->schedule->end_time)->format('g:i A') }}
                  </div>
                @endif

                {{-- Cantidades --}}
                <div class="text-muted" style="font-size:.82rem;">
                  {{ $it->adults_quantity ?? 0 }} {{ __('adminlte::adminlte.adult') }},
                  {{ $it->kids_quantity ?? 0 }} {{ __('adminlte::adminlte.kid') }}
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
            <span class="fw-bold" style="color:#006633">${{ number_format($headerTotal, 2) }}</span>
          </div>
          <div class="d-grid gap-2">
            <a href="{{ route('public.cart.index') }}" class="btn btn-success btn-sm">
              {{ __('adminlte::adminlte.view_cart') ?? 'Ver carrito' }}
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
    {{-- Invitado: abrir login (con Swal si está disponible) --}}
    @php
      $guestLinkClasses = $isDesktop
        ? 'nav-link cart-icon-wrapper position-relative'
        : 'cart-icon-wrapper position-relative';
    @endphp
    <a href="{{ route('login') }}"
       class="{{ $guestLinkClasses }}"
       onclick="return askLoginWithSwal(event, this.href);">
      <i class="{{ $iconClasses }}" title="{{ __('adminlte::adminlte.cart') }}"></i>
      <span class="cart-count-badge badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle"
            style="font-size:.7rem;display:none;">0</span>
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
        if (confirm(@json(__('adminlte::adminlte.login_required_text_confirm')))) window.location.href = loginUrl;
      }
      return false;
    }

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
          }
        });
      } else {
        if (confirm(@json(__('adminlte::adminlte.remove_item_text')))) formEl.submit();
      }
      return false;
    }
  </script>

  <style>
    /* === FIX: sin subrayado y caret del color actual === */
    .cart-icon-wrapper {
      text-decoration: none !important;
      -webkit-tap-highlight-color: transparent; /* quita highlight azul en Android */
    }
    .cart-icon-wrapper:hover,
    .cart-icon-wrapper:focus,
    .cart-icon-wrapper:active {
      text-decoration: none !important;
      outline: none !important;
      box-shadow: none !important;
    }
    /* El caret de Bootstrap usa currentColor → heredará blanco si el header es oscuro */
    .cart-icon-wrapper.dropdown-toggle::after {
      border-top-color: currentColor !important;
    }

    /* Si tu header es oscuro, fuerza blanco en mobile para el <a> */
    .navbar-dark .cart-icon-wrapper,
    .bg-dark .cart-icon-wrapper {
      color: #fff !important;
    }

    /* ===== Mini-cart estilos existentes ===== */
    .mini-cart-menu{ width:360px; border-radius:14px; box-shadow:0 10px 30px rgba(0,0,0,.15); }
    .mini-cart-list .d-flex:hover{ background:#f8f9fa; }
    .mini-cart-item{ padding-right: 3rem; }
    .mini-cart-price{ margin-right: 36px; }
    .mini-cart-remove-form{ position:absolute; top:8px; right:8px; z-index:2; }
    .mini-cart-remove{
      width:28px; height:28px; border-radius:999px;
      background:#fff; border:1px solid #e5e7eb;
      box-shadow:0 2px 8px rgba(0,0,0,.08);
      display:flex; align-items:center; justify-content:center;
      color:#6c757d; line-height:1;
    }
    .mini-cart-remove:hover{ background:#fee2e2; color:#dc3545; border-color:#fecaca; transform:scale(1.05); }
  </style>
@endonce
