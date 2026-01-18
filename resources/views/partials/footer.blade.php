<footer class="footer-nature">
  @php
  use App\Models\Policy;

  // Helper: devuelve Policy por type (independiente de ID o slug)
  $getPolicy = function(string $type) {
  return Policy::byType($type);
  };

  $terms = $getPolicy(Policy::TYPE_TERMS);
  $privacy = $getPolicy(Policy::TYPE_PRIVACY);
  $cancellation = $getPolicy(Policy::TYPE_CANCELLATION);
  $refund = $getPolicy(Policy::TYPE_REFUND);
  $warranty = $getPolicy(Policy::TYPE_WARRANTY);

  // URL directa al slug (evita redirección SEO)
  $policy_url = fn(App\Models\Policy $p) => localized_route('policies.show', ['policy' => $p]);

  $mapUrl = 'https://www.google.com/maps?ll=10.455662,-84.653203&z=16&t=m&hl=en&gl=US&mapclient=embed&cid=8940439748623688530';
  @endphp

  <div class="footer-main-content">
    <div class="footer-brand d-none d-md-block">
      <img src="{{ config('company.logo_url') }}"
        alt="{{ config('company.short_name') }}"
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
            href="https://www.tripadvisor.com/Attraction_Review-g309226-d6817241-Reviews-Green_Vacations_Costa_Rica-La_Fortuna_de_San_Carlos_Arenal_Volcano_National_Park_.html">TripAdvisor</a>
        </li>
        <li>
          <a target="_blank" rel="noopener"
            href="https://www.getyourguide.es/green-vacations-costa-rica-s26615/">GetYourGuide</a>
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
            data-scroll-first="true">{{ $translatedTitle }}</a>
        </li>
        @endforeach
      </ul>

      <h4 class="mt-3"><i class="fas fa-file-contract me-2"></i>{{ __('adminlte::adminlte.policies') }}</h4>
      <ul>
        @if($cancellation)
        <li class="d-flex align-items-center mb-2">
          <i class="fas fa-ban me-2"></i>
          <a href="{{ $policy_url($cancellation) }}">{{ $cancellation->display_name }}</a>
        </li>
        @endif

        @if($refund)
        <li class="d-flex align-items-center mb-2">
          <i class="fas fa-undo me-2"></i>
          <a href="{{ $policy_url($refund) }}">{{ $refund->display_name }}</a>
        </li>
        @endif

        @if($warranty)
        <li class="d-flex align-items-center mb-2">
          <i class="fas fa-certificate me-2"></i>
          <a href="{{ $policy_url($warranty) }}">{{ $warranty->display_name }}</a>
        </li>
        @endif
      </ul>
    </div>

    <div class="contact-info">
      <h4>{{ __('adminlte::adminlte.contact_us') }}</h4>
      <p class="mb-2">
        <i class="fas fa-map-marker-alt me-2"></i>
        <a href="{{ $mapUrl }}" target="_blank" rel="noopener" class="text-white text-decoration-none">
          {{ config('company.address.city') }}, {{ config('company.address.state') }}, {{ config('company.address.country') }}
        </a>
      </p>
      <p>
        <i class="fas fa-phone me-2"></i>
        <a href="tel:{{ str_replace(' ', '', config('company.phone')) }}" class="text-white text-decoration-none">{{ config('company.phone') }}</a>
      </p>
      <p>
        <i class="fas fa-envelope me-2"></i>
        <a href="mailto:{{ config('company.email') }}" class="text-white text-decoration-none">
          {{ config('company.email') }}
        </a>
      </p>
    </div>
  </div>

  <div class="footer-bottom text-center">
    <!-- Legal Links -->
    <div class="mb-2">
      @if($terms)
        <a href="{{ $policy_url($terms) }}" class="text-white mx-2 small">{{ __('adminlte::adminlte.terms_and_conditions') }}</a>
      @endif
      @if($privacy)
        <span class="text-white opacity-50">|</span>
        <a href="{{ $policy_url($privacy) }}" class="text-white mx-2 small">{{ __('adminlte::adminlte.privacy_policy') }}</a>
      @endif
      <span class="text-white opacity-50">|</span>
      <a href="#" id="cookie-settings-link" class="text-white mx-2 small">
        {{ __('adminlte::adminlte.cookies.change_preferences') }}
      </a>
    </div>

    &copy; {{ date('Y') }} {{ config('company.name') }}. {{ __('adminlte::adminlte.rights_reserved') }}
    
    <div class="small opacity-75 mt-1">
      Developed by
      <a href="https://github.com/ervingpinell" target="_blank" rel="noopener" class="text-white text-decoration-underline">Erving Pinell</a>
      &amp;
      <a href="https://github.com/AxlPaniagua" target="_blank" rel="noopener" class="text-white text-decoration-underline">Axel Paniagua</a>.
    </div>
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
          if (toursSection) toursSection.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
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
        if (toursSection) toursSection.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
        setTimeout(() => {
          const bsModal = new bootstrap.Modal(modal);
          bsModal.show();
        }, 600);
      }
    }
  });
</script>
@endpush