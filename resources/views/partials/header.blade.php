<nav class="navbar-custom">
    <div class="navbar-logo">
        <img src="{{ asset('images/logoCompanyWhite.png') }}" alt="Green Vacations" style="height: 70px; margin-right: 30px;">
        <strong>Green Vacations</strong>
    </div>
    <div class="navbar-links">
        <a href="{{ route('home') }}">{{ __('adminlte::adminlte.home') }}</a>
        <a href="#">{{ __('adminlte::adminlte.tours') }}</a>
        <!--<a href="#">{{ __('adminlte::adminlte.restaurant') }}</a>-->
        <a href="#">{{ __('adminlte::adminlte.reviews') }}</a>
        <a href="#">{{ __('adminlte::adminlte.faq') }}</a>
        <a href="#">{{ __('adminlte::adminlte.contact') }}</a>
        <!--<a href="#">{{ __('adminlte::adminlte.about') }}</a>-->
    </div>
    <div class="navbar-actions">
        <i class="fas fa-shopping-cart" title="{{ __('adminlte::adminlte.cart') }}"></i>
        @auth
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle"></i> {{ Auth::user()->full_name }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown" style="min-width:180px;">
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
                <i class="fas fa-user" title="Login"></i>
            </a>
        @endauth

        @if(Auth::check() && in_array(Auth::user()->role_id, [1, 2])) 
            <a href="{{ route('admin.home') }}"
               class="btn btn-outline-light btn-sm d-flex align-items-center gap-2"
               style="border-radius: 25px;">
                <i class="fas fa-toolbox"></i> Admin
            </a>
        @endif

        {{-- Dropdown de idiomas: Partial --}}
        @include('partials.language-switcher')

    </div>
</nav>