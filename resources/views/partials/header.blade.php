<nav class="navbar-custom">

  <div class="navbar-container">
    <!-- IZQUIERDA: Hamburguesa solo Mobile -->
    <div class="navbar-left d-md-none align-items-center gap-2">
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
    <div class="navbar-actions d-md-none align-items-center gap-2">
      <!-- Carrito -->
      <a href="{{ route('home') }}">
        <i class="fas fa-shopping-cart"></i>
      </a>

      <!-- Usuario Mobile -->
      @auth
        <div class="dropdown">
          <a href="#" class="nav-link dropdown-toggle text-white" id="userDropdownMobile" role="button" data-bs-toggle="dropdown">
            <i class="fas fa-user-circle"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownMobile">
            @if(Auth::user()->role_id === 3)
              <a class="dropdown-item" href="#">{{ __('adminlte::adminlte.my_reservations') }}</a>
            @endif
            <a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('adminlte::adminlte.profile') }}</a>
            <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit" class="dropdown-item">{{ __('adminlte::adminlte.logout') }}</button>
            </form>
          </div>
        </div>
        @if(in_array(Auth::user()->role_id, [1, 2]))
          <a href="{{ route('admin.home') }}" class="btn btn-outline-light btn-sm d-flex align-items-center gap-2">
            <i class="fas fa-toolbox"></i> Admin
          </a>
        @endif
      @else
        <a href="{{ route('login') }}" class="text-white">
          <i class="fas fa-user"></i>
        </a>
      @endauth
    </div>

    <!-- LINKS Desktop -->
    <div class="navbar-links d-none d-md-flex">
      <a href="{{ route('home') }}">Inicio</a>
      <a href="#">Tours</a>
      <a href="#">Reseñas</a>
      <a href="#">Preguntas frecuentes</a>
      <a href="#">Contacto</a>
    </div>

    <!-- ACCIONES Desktop -->
    <div class="navbar-actions d-none d-md-flex align-items-center gap-3">
      @include('partials.language-switcher')

      <!-- Carrito -->
      <a href="{{ route('home') }}">
        <i class="fas fa-shopping-cart"></i>
      </a>

      <!-- Usuario Desktop -->
      @auth
        <div class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdownDesktop" role="button" data-bs-toggle="dropdown">
            <i class="fas fa-user-circle"></i>
            <span>{{ Auth::user()->full_name }}</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownDesktop">
            @if(Auth::user()->role_id === 3)
              <li><a class="dropdown-item" href="#">{{ __('adminlte::adminlte.my_reservations') }}</a></li>
            @endif
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('adminlte::adminlte.profile') }}</a></li>
            <li>
              <form id="logout-form-desktop" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item">{{ __('adminlte::adminlte.logout') }}</button>
              </form>
            </li>
          </ul>
        </div>
        @if(in_array(Auth::user()->role_id, [1, 2]))
          <a href="{{ route('admin.home') }}" class="btn btn-outline-light btn-sm d-flex align-items-center gap-2">
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
    @include('partials.language-switcher')
  </div>

</nav>
