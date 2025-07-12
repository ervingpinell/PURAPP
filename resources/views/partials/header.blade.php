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
      <!-- Carrito -->
      <a href="{{ route('public.cart.index') }}">
        <i class="fas fa-shopping-cart"></i>
      </a>

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
      <a href="#">{{ __('adminlte::adminlte.tours') }}</a>
      <a href="#">{{ __('adminlte::adminlte.reviews') }}</a>
      <a href="#">{{ __('adminlte::adminlte.faq') }}</a>
      <a href="#">{{ __('adminlte::adminlte.contact_us') }}</a>
    </div>

    <!-- ACCIONES Desktop -->
    <div class="navbar-actions d-none d-md-flex">
      @include('partials.language-switcher')

      <!-- Carrito -->
      <li class="nav-item">
          <a class="nav-link" href="{{ route('public.cart.index') }}">
              <i class="fas fa-shopping-cart"></i> Mi Carrito
          </a>
      </li>

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
                  <i class="fas fa-sign-out-alt me-2"></i> {{ __('adminlte::adminlte.logout') }}
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
    <a href="{{ route('home') }}">Inicio</a>
    <a href="#">Tours</a>
    <a href="#">Reseñas</a>
    <a href="#">Preguntas frecuentes</a>
    <a href="#">Contacto</a>
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
