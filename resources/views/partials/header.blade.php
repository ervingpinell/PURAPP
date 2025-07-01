<nav class="navbar-custom">
  <div class="navbar-group">
    <!-- Toggle hamburguesa: IZQUIERDA (solo mobile) -->
    <div class="navbar-left">
      <button class="navbar-toggle d-md-none" id="navbar-toggle">
        <i class="fas fa-bars"></i>
      </button>
    </div>

    <!-- Logo centrado absoluto -->
    <div class="navbar-logo">
      <img src="{{ asset('images/logoCompanyWhite.png') }}" alt="Green Vacations" class="navbar-logo-img">
    </div>

    <!-- Acciones: DERECHA SOLO MOBILE -->
    <div class="navbar-right navbar-actions d-md-none">
      <!-- ✅ Idioma fijo en mobile -->
      @include('partials.language-switcher')

      <!-- Carrito -->
      <i class="fas fa-shopping-cart" title="{{ __('adminlte::adminlte.cart') }}"></i>

      <!-- Usuario -->
      @auth
        <div class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdownMobile" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdownMobile" style="min-width:180px;">
            @if(Auth::user()->role_id === 3)
              <li>
                <a class="dropdown-item d-flex align-items-center" href="{{ route('my-reservations') }}">
                  <i class="fas fa-calendar-check me-2 text-success"></i>
                  {{ __('My Reservations') }}
                </a>
              </li>
            @endif
            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
                <i class="fas fa-id-card-alt me-2 text-primary"></i>
                {{ __('Profile') }}
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <form action="{{ route('logout') }}" method="POST" class="d-inline w-100">
                @csrf
                <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                  <i class="fas fa-sign-out-alt me-2"></i>
                  {{ __('Logout') }}
                </button>
              </form>
            </li>
          </ul>
        </div>
      @else
        <a href="{{ route('login') }}" class="text-white">
          <i class="fas fa-user"></i>
        </a>
      @endauth
    </div>
  </div>

  <!-- Links del menú hamburguesa -->
  <div class="navbar-links" id="navbar-links">
    <a href="{{ route('home') }}">{{ __('adminlte::adminlte.home') }}</a>
    <a href="#">{{ __('adminlte::adminlte.tours') }}</a>
    <a href="#">{{ __('adminlte::adminlte.reviews') }}</a>
    <a href="#">{{ __('adminlte::adminlte.faq') }}</a>
    <a href="#">{{ __('adminlte::adminlte.contact') }}</a>

    <!-- ✅ NO HAY SWITCHER dentro del menú -->

    @if(Auth::check() && in_array(Auth::user()->role_id, [1, 2]))
      <a href="{{ route('admin.home') }}"
         class="btn btn-outline-light btn-sm d-inline-flex align-items-center gap-2 mt-2 d-md-none"
         style="border-radius: 25px;">
        <i class="fas fa-toolbox"></i> Admin
      </a>
    @endif
  </div>

  <!-- Acciones desktop -->
  <div class="navbar-actions d-none d-md-flex align-items-center gap-3">
    <!-- ✅ Idioma fijo desktop -->
    @include('partials.language-switcher')

    @if(Auth::check() && in_array(Auth::user()->role_id, [1, 2]))
      <a href="{{ route('admin.home') }}"
         class="btn btn-outline-light btn-sm d-inline-flex align-items-center gap-2"
         style="border-radius: 25px;">
        <i class="fas fa-toolbox"></i> Admin
      </a>
    @endif

    <i class="fas fa-shopping-cart" title="{{ __('adminlte::adminlte.cart') }}"></i>

    @auth
      <!-- Dropdown usuario desktop -->
      <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdownDesktop" role="button"
           data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fas fa-user-circle"></i>
          <span class="d-none d-md-inline">{{ Auth::user()->full_name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdownDesktop" style="min-width:180px;">
          @if(Auth::user()->role_id === 3)
            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{ route('my-reservations') }}">
                <i class="fas fa-calendar-check me-2 text-success"></i>
                {{ __('My Reservations') }}
              </a>
            </li>
          @endif
          <li>
            <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
              <i class="fas fa-id-card-alt me-2 text-primary"></i>
              {{ __('Profile') }}
            </a>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form action="{{ route('logout') }}" method="POST" class="d-inline w-100">
              @csrf
              <button type="submit" class="dropdown-item d-flex align-items-center text-danger">
                <i class="fas fa-sign-out-alt me-2"></i>
                {{ __('Logout') }}
              </button>
            </form>
          </li>
        </ul>
      </div>
    @else
      <a href="{{ route('login') }}" class="text-white">
        <i class="fas fa-user"></i>
      </a>
    @endauth
  </div>
</nav>
