<nav class="navbar-custom">
  <div class="navbar-container">
    {{-- Izquierda (MOBILE/TABLET): Hamburguesa + Idiomas --}}
    <div class="navbar-left d-lg-none">
      <button
        class="navbar-toggle"
        id="navbar-toggle"
        type="button"
        aria-label="{{ __('adminlte::adminlte.toggle_navigation') }}"
        aria-controls="navbar-links"
        aria-expanded="false">
        <i class="fas fa-bars"></i>
      </button>

      {{-- Language switcher (solo mobile/tablet) --}}
      <div class="mobile-lang ms-2">
        @include('partials.language-switcher')
      </div>
    </div>

    {{-- Logo --}}
    <div class="navbar-logo">
      <a href="{{ localized_route('home') }}">
        <img src="{{ cdn('logos/brand-logo-white.png') }}" alt="Green Vacations" decoding="async" fetchpriority="high">
      </a>
    </div>

    {{-- Acciones compactas (MOBILE/TABLET) --}}
    <div class="navbar-actions d-lg-none">
      <x-cart.dropdown variant="mobile" />
      @auth
      <div class="dropdown">
        <a href="#" class="nav-link dropdown-toggle text-white" id="userDropdownMobile" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fas fa-user-circle"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownMobile">
          <a class="dropdown-item d-flex align-items-center" href="{{ route('my-bookings') }}">
            <i class="fas fa-calendar-check me-2 text-success"></i> {{ __('adminlte::adminlte.my_reservations') }}
          </a>
          <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
            <i class="fas fa-id-card-alt me-2 text-primary"></i> {{ __('adminlte::adminlte.profile') }}
          </a>
          <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-inline w-100">
            @csrf
            <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
              <i class="fas fa-sign-out-alt me-2"></i> {{ __('adminlte::adminlte.log_out') }}
            </button>
          </form>
        </div>
      </div>
      @else
      <a href="{{ route('login') }}" class="text-white" aria-label="{{ __('adminlte::adminlte.login') }}">
        <i class="fas fa-user"></i>
      </a>
      @endauth
    </div>

    {{-- Links de navegación (ESCRITORIO) --}}
    <div class="navbar-links d-none d-lg-flex">
      <a href="{{ localized_route('home') }}">{{ __('adminlte::adminlte.home') }}</a>
      <a href="{{ localized_route('tours.index') }}">{{ __('adminlte::adminlte.tours') }}</a>
      <a href="{{ localized_route('reviews.index') }}">{{ __('adminlte::adminlte.reviews') }}</a>
      <a href="{{ localized_route('faq.index') }}">{{ __('adminlte::adminlte.faq') }}</a>
      <a href="{{ localized_route('contact') }}">{{ __('adminlte::adminlte.contact_us') }}</a>
    </div>

    {{-- Acciones (ESCRITORIO) --}}
    <div class="navbar-actions d-none d-lg-flex">
      {{-- En desktop, el switcher se queda aquí como antes --}}
      @include('partials.language-switcher')
      <x-cart.dropdown variant="desktop" />
      @auth
      <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdownDesktop" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fas fa-user-circle"></i>
          <span class="user-name d-none d-xl-inline ms-1">{{ Auth::user()->full_name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownDesktop">
          <li>
            <a class="dropdown-item d-flex align-items-center" href="{{ route('my-bookings') }}">
              <i class="fas fa-calendar-check me-2 text-success"></i>
              {{ __('adminlte::adminlte.my_reservations') }}
            </a>
          </li>
          <li>
            <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
              <i class="fas fa-id-card-alt me-2 text-primary"></i> {{ __('adminlte::adminlte.profile') }}
            </a>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>
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

      @can('access-admin')
      <a href="{{ route('admin.home') }}" class="btn btn-outline-light btn-sm d-flex align-items-center">
        <i class="fas fa-toolbox me-1"></i> <span class="d-none d-xl-inline">Admin</span>
      </a>
      @endif
      @else
      <a href="{{ route('login') }}" class="text-white" aria-label="{{ __('adminlte::adminlte.login') }}">
        <i class="fas fa-user"></i>
      </a>
      @endauth
    </div>
  </div>

  {{-- Dropdown del menú (MÓVIL/TABLET) --}}
  <div class="navbar-links d-lg-none" id="navbar-links">
    <a href="{{ localized_route('home') }}">{{ __('adminlte::adminlte.home') }}</a>
    <a href="#" class="scroll-to-tours">{{ __('adminlte::adminlte.tours') }}</a>
    <a href="{{ localized_route('reviews.index') }}">{{ __('adminlte::adminlte.reviews') }}</a>
    <a href="{{ localized_route('faq.index') }}">{{ __('adminlte::adminlte.faq') }}</a>
    <a href="{{ localized_route('contact') }}">{{ __('adminlte::adminlte.contact_us') }}</a>

    {{-- Eliminamos el switcher aquí para no duplicarlo en mobile --}}
    {{-- <div class="language-switcher-wrapper">
      @include('partials.language-switcher')
    </div> --}}

    @auth
    @can('access-admin')
    <div class="admin-link-wrapper">
      <a href="{{ route('admin.home') }}" class="btn btn-outline-light btn-sm d-flex align-items-center justify-content-center">
        <i class="fas fa-toolbox me-2"></i> Admin
      </a>
    </div>
    @endif
    @endauth
  </div>
</nav>