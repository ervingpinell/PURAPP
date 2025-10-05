<footer class="footer-nature">
  @php
    $terms   = \App\Models\Policy::byType('terminos');
    $privacy = \App\Models\Policy::byType('privacidad');

    if ($terms)   { $terms->loadMissing('translations'); }
    if ($privacy) { $privacy->loadMissing('translations'); }

    $mapUrl = 'https://www.google.com/maps?ll=10.455662,-84.653203&z=16&t=m&hl=en&gl=US&mapclient=embed&cid=8940439748623688530';
  @endphp

  <div class="footer-main-content">
    <div class="footer-brand d-none d-md-block">
      <img src="{{ asset('images/logoCompanyWhite.png') }}"
           alt="Green Vacations"
           decoding="async"
           fetchpriority="low" />
      <p>{{ __('adminlte::adminlte.footer_text') }}</p>
    </div>

    <div class="footer-links">
      <h4>{{ __('adminlte::adminlte.quick_links') }}</h4>
      <ul>
        <li><a href="{{ localized_route('home') }}">{{ __('adminlte::adminlte.home') }}</a></li>
        <li><a href="#" class="scroll-to-tours">{{ __('adminlte::adminlte.tours') }}</a></li>
        <li><a href="{{ localized_route('reviews.index') }}">{{ __('adminlte::adminlte.reviews') }}</a></li>

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
        @foreach ($typeMeta ?? [] as $key => $meta)
          @php
            $group = ($toursByType[$key] ?? collect());
            if ($group->isEmpty()) continue;

            $translatedTitle = $meta['title'] ?? '';
            $modalId = 'modal-' . \Illuminate\Support\Str::slug((string)$key);
          @endphp
          <li class="d-flex align-items-center mb-2">
            <i class="fas fa-{{ $key == 1 ? 'sun' : 'mountain' }} me-2"></i>
            <a href="#"
               class="open-tour-modal"
               data-tour-modal="{{ $modalId }}"
               data-scroll-first="true">
              {{ $translatedTitle }}
            </a>
          </li>
        @endforeach
      </ul>

      <h4 class="mt-3">
        <i class="fas fa-file-contract me-2"></i>{{ __('adminlte::adminlte.policies') }}
      </h4>
      <ul>
        @if($terms)
          <li class="d-flex align-items-center mb-2">
            <i class="fas fa-balance-scale me-2"></i>
            <a href="{{ localized_route('policies.show', ['policy' => $terms->slug]) }}">
              {{ __('adminlte::adminlte.terms_and_conditions') }}
            </a>
          </li>
        @endif

        @if($privacy)
          <li class="d-flex align-items-center mb-2">
            <i class="fas fa-shield-alt me-2"></i>
            <a href="{{ localized_route('policies.show', ['policy' => $privacy->slug]) }}">
              {{ __('adminlte::adminlte.privacy_policy') }}
            </a>
          </li>
        @endif

        <li class="d-flex align-items-center mb-2">
          <i class="fas fa-list me-2"></i>
          <a href="{{ localized_route('policies.index') }}">
            {{ __('adminlte::adminlte.all_policies') }}
          </a>
        </li>
      </ul>
    </div>

    <div class="contact-info">
      <h4>{{ __('adminlte::adminlte.contact_us') }}</h4>
      <p class="mb-2">
        <i class="fas fa-map-marker-alt me-2"></i>
        <a href="{{ $mapUrl }}" target="_blank" rel="noopener"
           class="text-white text-decoration-none">
          La Fortuna, San Carlos, Costa Rica
        </a>
      </p>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.open-tour-modal').forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const modalId = this.getAttribute('data-tour-modal');
      const shouldScroll = this.getAttribute('data-scroll-first') === 'true';

      if (!document.getElementById(modalId)) {
        window.location.href = '{{ localized_route("home") }}#' + modalId;
        return;
      }

      if (shouldScroll) {
        const toursSection = document.getElementById('tours');
        if (toursSection) toursSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }

      setTimeout(() => {
        const modal = document.getElementById(modalId);
        if (modal) {
          const bsModal = new bootstrap.Modal(modal);
          bsModal.show();
        }
      }, shouldScroll ? 600 : 0);
    });
  });

  if (window.location.hash && window.location.hash.startsWith('#modal-')) {
    const modalId = window.location.hash.substring(1);
    const modal = document.getElementById(modalId);

    if (modal) {
      const toursSection = document.getElementById('tours');
      if (toursSection) toursSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
      setTimeout(() => {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
      }, 600);
    }
  }
});
</script>
@endpush
