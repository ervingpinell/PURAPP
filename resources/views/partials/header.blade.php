@php
  $headerCart = auth()->check()
    ? \App\Models\Cart::with(['items.tour','items.schedule','items.language','items.hotel'])
        ->where('user_id', auth()->id())->first()
    : null;

  $headerCount = $headerCart?->items->count() ?? 0;

  $headerTotal = $headerCart
    ? $headerCart->items->sum(fn($i) =>
        $i->tour->adult_price * $i->adults_quantity +
        $i->tour->kid_price   * $i->kids_quantity)
    : 0;
@endphp

<nav class="navbar-custom">
  <div class="navbar-container">
    <!-- IZQUIERDA: Hamburguesa solo Mobile -->
    <div class="navbar-left d-md-none">
      <button class="navbar-toggle" id="navbar-toggle">
        <i class="fas fa-bars"></i>
      </button>
    </div>

    <!-- CENTRO: Logo -->
    <div class="navbar-logo">
      <a href="{{ route('home') }}">
        <img src="{{ asset('images/logoCompanyWhite.png') }}" alt="Green Vacations">
      </a>
    </div>

    <!-- DERECHA: Acciones Mobile -->
    <div class="navbar-actions d-md-none">
      <!-- Carrito Mobile con dropdown -->
      <div class="dropdown">
        @auth
          <a href="#" class="cart-icon-wrapper position-relative dropdown-toggle"
             id="cartDropdownMobile" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
            <i class="fas fa-shopping-cart text-white" title="{{ __('adminlte::adminlte.cart') }}"></i>
            <span class="cart-count-badge badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle"
                  style="font-size:.7rem; {{ $headerCount ? '' : 'display:none;' }}">{{ $headerCount }}</span>
          </a>

          <div class="dropdown-menu dropdown-menu-end p-0 mini-cart-menu" aria-labelledby="cartDropdownMobile">
            @if($headerCart && $headerCount)
              <div class="mini-cart-list" style="max-height:60vh;overflow:auto;">
                @foreach($headerCart->items as $it)
                  @php
                    $img = $it->tour->image_path ? asset('storage/'.$it->tour->image_path) : asset('images/volcano.png');
                    $sum = $it->tour->adult_price * $it->adults_quantity + $it->tour->kid_price * $it->kids_quantity;
                  @endphp
                  <div class="d-flex gap-2 p-3 border-bottom position-relative mini-cart-item">
                    {{-- X eliminar (MOBILE) --}}
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
                      <div class="text-muted" style="font-size:.82rem;">
                        {{ \Carbon\Carbon::parse($it->tour_date)->translatedFormat('d MMM, Y') }}
                        @if($it->schedule)
                          · {{ \Carbon\Carbon::parse($it->schedule->start_time)->format('g:i A') }}–{{ \Carbon\Carbon::parse($it->schedule->end_time)->format('g:i A') }}
                        @endif
                        · {{ $it->adults_quantity + $it->kids_quantity }} {{ __('adminlte::adminlte.pax') ?? 'pax' }}
                      </div>
                    </div>
                    <div class="fw-bold small text-success mini-cart-price">
                      ${{ number_format($sum,2) }}
                    </div>
                  </div>
                @endforeach
              </div>
              <div class="p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="fw-semibold">{{ __('adminlte::adminlte.totalEstimated') }}</span>
                  <span class="fw-bold" style="color:#006633">${{ number_format($headerTotal,2) }}</span>
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
          <a href="{{ route('login') }}" class="cart-icon-wrapper position-relative"
             onclick="return askLoginWithSwal(event, this.href);">
            <i class="fas fa-shopping-cart text-white" title="{{ __('adminlte::adminlte.cart') }}"></i>
            <span class="cart-count-badge badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle"
                  style="font-size:.7rem;display:none;">0</span>
          </a>
        @endauth
      </div>

      <!-- Usuario Mobile -->
      @auth
        <div class="dropdown">
          <a href="#" class="nav-link dropdown-toggle text-white" id="userDropdownMobile" role="button" data-bs-toggle="dropdown">
            <i class="fas fa-user-circle"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownMobile">
            <a class="dropdown-item d-flex align-items-center" href="{{ route('my-reservations') }}">
              <i class="fas fa-calendar-check me-2 text-success"></i> {{ __('My Reservations') }}
            </a>
            <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
              <i class="fas fa-id-card-alt me-2 text-primary"></i> {{ __('Profile') }}
            </a>
            <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-inline w-100">
              @csrf
              <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                <i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}
              </button>
            </form>
          </div>
        </div>
      @else
        <a href="{{ route('login') }}" class="text-white">
          <i class="fas fa-user"></i>
        </a>
      @endauth
    </div>

    <!-- LINKS Desktop -->
    <div class="navbar-links d-none d-md-flex">
      <a href="{{ route('home') }}">{{ __('adminlte::adminlte.home') }}</a>
      <a href="#" class="nav-link scroll-to-tours">{{ __('adminlte::adminlte.tours') }}</a>
      <a href="{{ route('reviews') }}">{{ __('adminlte::adminlte.reviews') }}</a>
      <a href="{{ route('faq.index') }}">{{ __('adminlte::adminlte.faq') }}</a>
      <a href="{{ route('contact') }}">{{ __('adminlte::adminlte.contact_us') }}</a>
    </div>

    <!-- ACCIONES Desktop -->
    <div class="navbar-actions d-none d-md-flex">
      @include('partials.language-switcher')

      <!-- Carrito Desktop con dropdown -->
      <div class="dropdown">
        @auth
          <a class="nav-link cart-icon-wrapper position-relative dropdown-toggle"
             href="#" id="cartDropdownDesktop" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
            <i class="fas fa-shopping-cart" title="{{ __('adminlte::adminlte.cart') }}"></i>
            <span class="cart-count-badge badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle"
                  style="font-size:.7rem; {{ $headerCount ? '' : 'display:none;' }}">{{ $headerCount }}</span>
            {{ __('adminlte::adminlte.cart') }}
          </a>

          <div class="dropdown-menu dropdown-menu-end p-0 mini-cart-menu" aria-labelledby="cartDropdownDesktop">
            @if($headerCart && $headerCount)
              <div class="mini-cart-list" style="max-height:60vh;overflow:auto;">
                @foreach($headerCart->items as $it)
                  @php
                    $img = $it->tour->image_path ? asset('storage/'.$it->tour->image_path) : asset('images/volcano.png');
                    $sum = $it->tour->adult_price * $it->adults_quantity + $it->tour->kid_price * $it->kids_quantity;
                  @endphp
                  <div class="d-flex gap-2 p-3 border-bottom position-relative mini-cart-item">
                    {{-- X eliminar (DESKTOP) --}}
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
                      <div class="text-muted" style="font-size:.82rem;">
                        {{ \Carbon\Carbon::parse($it->tour_date)->translatedFormat('d MMM, Y') }}
                        @if($it->schedule)
                          · {{ \Carbon\Carbon::parse($it->schedule->start_time)->format('g:i A') }}–{{ \Carbon\Carbon::parse($it->schedule->end_time)->format('g:i A') }}
                        @endif
                        · {{ $it->adults_quantity + $it->kids_quantity }} {{ __('adminlte::adminlte.pax') ?? 'pax' }}
                      </div>
                    </div>
                    <div class="fw-bold small text-success mini-cart-price">
                      ${{ number_format($sum,2) }}
                    </div>
                  </div>
                @endforeach
              </div>
              <div class="p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="fw-semibold">{{ __('adminlte::adminlte.totalEstimated') }}</span>
                  <span class="fw-bold" style="color:#006633">${{ number_format($headerTotal,2) }}</span>
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
          <a class="nav-link cart-icon-wrapper position-relative"
             href="{{ route('login') }}" onclick="return askLoginWithSwal(event, this.href);">
            <i class="fas fa-shopping-cart" title="{{ __('adminlte::adminlte.cart') }}"></i>
            <span class="cart-count-badge badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle"
                  style="font-size:.7rem;display:none;">0</span>
            {{ __('adminlte::adminlte.cart') }}
          </a>
        @endauth
      </div>

      <!-- Usuario Desktop -->
      @auth
        <div class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdownDesktop" role="button" data-bs-toggle="dropdown">
            <i class="fas fa-user-circle"></i>
            <span>{{ Auth::user()->full_name }}</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownDesktop">
            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{ route('my-reservations') }}">
                <i class="fas fa-calendar-check me-2 text-success"></i> {{ __('adminlte::adminlte.my_reservations') }}
              </a>
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
                <i class="fas fa-id-card-alt me-2 text-primary"></i> {{ __('adminlte::adminlte.profile') }}
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <form id="logout-form-desktop" action="{{ route('logout') }}" method="POST" class="d-inline w-100">
                @csrf
                <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                  <i class="fas fa-sign-out-alt me-2"></i> {{ __('adminlte::adminlte.log_out') }}
                </button>
              </form>
            </li>
          </ul>
        </div>
        @if(in_array(Auth::user()->role_id, [1, 2]))
          <a href="{{ route('admin.home') }}" class="btn btn-outline-light btn-sm d-flex align-items-center">
            <i class="fas fa-toolbox"></i> Admin
          </a>
        @endif
      @else
        <a href="{{ route('login') }}" class="text-white">
          <i class="fas fa-user"></i>
        </a>
      @endauth
    </div>
  </div>

  <!-- MOBILE MENU desplegable -->
  <div class="navbar-links d-md-none" id="navbar-links">
    <a href="{{ route('home') }}">{{ __('adminlte::adminlte.home') }}</a>
    <a href="#" class="nav-link scroll-to-tours">{{ __('adminlte::adminlte.tours') }}</a>
    <a href="{{ route('reviews') }}">{{ __('adminlte::adminlte.reviews') }}</a>
    <a href="{{ route('faq.index') }}">{{ __('adminlte::adminlte.faq') }}</a>
    <a href="{{ route('contact') }}">{{ __('adminlte::adminlte.contact_us') }}</a>

    <div class="language-switcher-wrapper">
      @include('partials.language-switcher')
    </div>

    @auth
      @if(in_array(Auth::user()->role_id, [1, 2]))
        <div class="admin-link-wrapper">
          <a href="{{ route('admin.home') }}" class="btn btn-outline-light btn-sm d-flex align-items-center">
            <i class="fas fa-toolbox"></i> Admin
          </a>
        </div>
      @endif
    @endauth
  </div>
</nav>

@once
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // Invitados: alerta para ir a login
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
        }).then(res => {
          if (res.isConfirmed) window.location.href = loginUrl;
        });
      } else {
        if (confirm(@json(__('adminlte::adminlte.login_required_text_confirm')))) window.location.href = loginUrl;
      }
      return false;
    }

    // Confirmación para eliminar un item en el mini-carrito (no cierra el dropdown)
    function confirmMiniRemove(e, formEl) {
      e.preventDefault();
      e.stopPropagation();
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
