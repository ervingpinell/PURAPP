<footer class="footer-nature">
  @php
  use App\Models\Policy;

  $getPolicy = function(string $type) {
    return Policy::byType($type);
  };

  $terms = $getPolicy(Policy::TYPE_TERMS);
  $privacy = $getPolicy(Policy::TYPE_PRIVACY);
  $cancellation = $getPolicy(Policy::TYPE_CANCELLATION);
  $refund = $getPolicy(Policy::TYPE_REFUND);
  $warranty = $getPolicy(Policy::TYPE_WARRANTY);

  $policy_url = fn(App\Models\Policy $p) => localized_route('policies.show', ['policy' => $p]);

  $mapUrl = 'https://www.google.com/maps?ll=10.455662,-84.653203&z=16&t=m&hl=en&gl=US&mapclient=embed&cid=8940439748623688530';
  
  $socialLinks = array_filter([
    'facebook' => config('company.social.facebook'),
    'instagram' => config('company.social.instagram'),
    'twitter' => config('company.social.twitter'),
    'google' => config('company.social.google'),
    'tripadvisor' => config('company.social.tripadvisor'),
    'getyourguide' => config('company.social.getyourguide'),
    'viator' => config('company.social.viator'),
  ]);
  @endphp

  <div class="footer-main-content">
    <!-- Brand -->
    <div class="footer-brand">
      <img src="{{ config('company.logo_url') }}"
        alt="{{ config('company.short_name') }}"
        decoding="async"
        fetchpriority="low" />
    </div>

    <!-- Enlaces Rápidos -->
    <div class="footer-links">
      <h4>{{ __('adminlte::adminlte.quick_links') }}</h4>
      <ul>
        <li><a href="{{ localized_route('home') }}">{{ __('adminlte::adminlte.home') }}</a></li>
        <li><a href="{{ localized_route('tours.index') }}">{{ __('adminlte::adminlte.tours') }}</a></li>
        <li><a href="{{ localized_route('reviews.index') }}">{{ __('adminlte::adminlte.reviews') }}</a></li>
        <li><a href="{{ localized_route('faq.index') }}">{{ __('adminlte::adminlte.faq') }}</a></li>
        <li><a href="{{ localized_route('contact') }}">{{ __('adminlte::adminlte.contact_us') }}</a></li>
      </ul>
    </div>

    <!-- Políticas -->
    <div class="footer-policies">
      <h4><i class="fas fa-file-contract me-2"></i>{{ __('adminlte::adminlte.policies') }}</h4>
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
        
        <li class="d-flex align-items-center mb-2">
          <i class="fas fa-list me-2"></i>
          <a href="{{ localized_route('policies.index') }}">{{ __('adminlte::adminlte.all_policies') }}</a>
        </li>
      </ul>
    </div>

    <!-- Contacto -->
    <div class="contact-info">
      <h4>{{ __('adminlte::adminlte.contact_us') }}</h4>
      <p class="mb-2">
        <i class="fas fa-map-marker-alt me-2"></i>
        <a href="{{ $mapUrl }}" target="_blank" rel="noopener" class="text-white text-decoration-none">
          {{ config('company.address.city') }}, {{ config('company.address.state') }}, {{ config('company.address.country') }}
        </a>
      </p>
      <p class="mb-2">
        <i class="fas fa-phone me-2"></i>
        <a href="tel:{{ str_replace(' ', '', config('company.phone')) }}" class="text-white text-decoration-none">{{ config('company.phone') }}</a>
      </p>
      <p class="mb-2">
        <i class="fas fa-envelope me-2"></i>
        <a href="mailto:{{ config('company.email') }}" class="text-white text-decoration-none">
          {{ config('company.email') }}
        </a>
      </p>
    </div>
  </div>

  <!-- Social Media Section -->
  <div class="footer-social">
    <span class="social-label">{{ __('adminlte::adminlte.follow_us') }}</span>
    <div class="social-icons">
      @if(isset($socialLinks['facebook']))
      <a href="{{ $socialLinks['facebook'] }}" 
         target="_blank" 
         rel="noopener noreferrer" 
         aria-label="Facebook"
         class="social-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
          <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
        </svg>
      </a>
      @endif

      @if(isset($socialLinks['instagram']))
      <a href="{{ $socialLinks['instagram'] }}" 
         target="_blank" 
         rel="noopener noreferrer" 
         aria-label="Instagram"
         class="social-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
        </svg>
      </a>
      @endif

      @if(isset($socialLinks['twitter']))
      <a href="{{ $socialLinks['twitter'] }}" 
         target="_blank" 
         rel="noopener noreferrer" 
         aria-label="Twitter"
         class="social-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
          <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
        </svg>
      </a>
      @endif

      @if(isset($socialLinks['google']))
      <a href="{{ $socialLinks['google'] }}" 
         target="_blank" 
         rel="noopener noreferrer" 
         aria-label="Google"
         class="social-icon">
        <img src="{{ asset('images/social/google.svg') }}" alt="Google" width="24" height="24" />
      </a>
      @endif

      @if(isset($socialLinks['tripadvisor']))
      <a href="{{ $socialLinks['tripadvisor'] }}" 
         target="_blank" 
         rel="noopener noreferrer" 
         aria-label="TripAdvisor"
         class="social-icon">
        <img src="{{ asset('images/social/tripadvisor.svg') }}" alt="TripAdvisor" width="24" height="24" />
      </a>
      @endif

      @if(isset($socialLinks['getyourguide']))
      <a href="{{ $socialLinks['getyourguide'] }}" 
         target="_blank" 
         rel="noopener noreferrer" 
         aria-label="GetYourGuide"
         class="social-icon">
        <img src="{{ asset('images/social/getyourguide.svg') }}" alt="GetYourGuide" width="24" height="24" />
      </a>
      @endif

      @if(isset($socialLinks['viator']))
      <a href="{{ $socialLinks['viator'] }}" 
         target="_blank" 
         rel="noopener noreferrer" 
         aria-label="Viator"
         class="social-icon">
        <img src="{{ asset('images/social/viator.svg') }}" alt="Viator" width="24" height="24" />
      </a>
      @endif
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