<footer class="footer-nature">
  @php
    $terms   = \App\Models\Policy::byType('terminos');
    $privacy = \App\Models\Policy::byType('privacidad');

    if ($terms)   { $terms->loadMissing('translations'); }
    if ($privacy) { $privacy->loadMissing('translations'); }
  @endphp

  <div class="footer-main-content">
    <div class="footer-brand">
      <img src="{{ asset('images/logoCompanyWhite.png') }}" alt="Green Vacations" />
      <p>{{ __('adminlte::adminlte.footer_text') }}</p>
    </div>

    <div class="footer-links">
      <h4>{{ __('adminlte::adminlte.quick_links') }}</h4>
      <ul>
        <li><a href="{{ url('/') }}">{{ __('adminlte::adminlte.home') }}</a></li>
        <li><a href="{{ url('/tours') }}">{{ __('adminlte::adminlte.tours') }}</a></li>
        <li><a href="{{ url('/reviews') }}">{{ __('adminlte::adminlte.reviews') }}</a></li>
        <li>
          <a target="_blank" rel="noopener"
             href="https://www.tripadvisor.com/Attraction_Review-g309226-d6817241-Reviews-Green_Vacations_Costa_Rica-La_Fortuna_de_San_Carlos_Arenal_Volcano_National_Park_.html">
            TripAdvisor
          </a>
        </li>
        <li>
          <a target="_blank" rel="noopener"
             href="https://www.getyourguide.es/green-vacations-costa-rica-s26615/">
            GetYourGuide
          </a>
        </li>
      </ul>
    </div>

    <div class="footer-tours">
      <h4><i class="fas fa-map-signs me-2"></i>{{ __('adminlte::adminlte.our_tours') }}</h4>
      <ul>
        <li class="d-flex align-items-center mb-2">
          <i class="fas fa-sun me-2"></i>
          <a href="#" class="nav-link scroll-to-tours">{{ __('adminlte::adminlte.half_day') }}</a>
        </li>
        <li class="d-flex align-items-center mb-2">
          <i class="fas fa-mountain me-2"></i>
          <a href="#" class="nav-link scroll-to-tours">{{ __('adminlte::adminlte.full_day') }}</a>
        </li>
      </ul>

      <h4 class="mt-3">
        <i class="fas fa-file-contract me-2"></i>{{ __('adminlte::adminlte.policies') }}
      </h4>
      <ul>
        @if($terms)
          <li class="d-flex align-items-center mb-2">
            <i class="fas fa-balance-scale me-2"></i>
            <a href="{{ route('policies.show', $terms) }}">
              {{ __('adminlte::adminlte.terms_and_conditions') }}
            </a>
          </li>
        @endif

        @if($privacy)
          <li class="d-flex align-items-center mb-2">
            <i class="fas fa-shield-alt me-2"></i>
            <a href="{{ route('policies.show', $privacy) }}">
              {{ __('adminlte::adminlte.privacy_policy') }}
            </a>
          </li>
        @endif

        <li class="d-flex align-items-center mb-2">
          <i class="fas fa-list me-2"></i>
          <a href="{{ route('policies.index') }}">
            {{ __('adminlte::adminlte.all_policies') }}
          </a>
        </li>
      </ul>
    </div>

    <div class="contact-info">
      <h4>{{ __('adminlte::adminlte.contact_us') }}</h4>
      <p><i class="fas fa-map-marker-alt me-2"></i> La Fortuna, San Carlos, Costa Rica</p>
      <p>
        <i class="fas fa-phone me-2"></i>
        <a href="tel:+50624791471" class="text-white text-decoration-none">(+506) 2479 1471</a>
      </p>
      <p>
        <i class="fas fa-envelope me-2"></i>
        <a href="mailto:info@greenvacationscr.com" class="text-white text-decoration-none">
          info@greenvacationscr.com
        </a>
      </p>
    </div>
  </div>

  <div class="footer-bottom">
    &copy; {{ date('Y') }} Green Vacations Costa Rica. {{ __('adminlte::adminlte.rights_reserved') }}
  </div>
</footer>
