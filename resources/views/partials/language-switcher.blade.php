<div class="dropdown language-switcher">
  @php
      $locale = app()->getLocale();
      $flag = match($locale) {
          'es', 'es_CR' => 'es.svg',
          'en'          => 'en.svg',
          'fr'          => 'fr.svg',
          'pt', 'pt_BR' => 'pt.svg',
          'de'          => 'de.svg',
          default       => 'es.svg',
      };
      $label = match($locale) {
          'es', 'es_CR' => 'ES',
          'pt', 'pt_BR' => 'PT',
          'en'          => 'EN',
          'fr'          => 'FR',
          'de'          => 'DE',
          default       => strtoupper($locale),
      };
  @endphp

  <button
      class="language-switcher-toggle btn btn-outline-secondary dropdown-toggle"
      type="button"
      id="languageDropdown"
      data-bs-toggle="dropdown"
      data-toggle="dropdown"
      aria-expanded="false">
      <img src="{{ asset('svg/flags/' . $flag) }}" alt="Current language" width="20" class="me-1">
      {{ $label }}
  </button>

  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-right" aria-labelledby="languageDropdown">
    <li>
      <a class="dropdown-item language-switcher-item" href="{{ route('switch.language', 'es') }}">
        <img src="{{ asset('svg/flags/es.svg') }}" width="20" class="me-1"> Español
      </a>
    </li>
    <li>
      <a class="dropdown-item language-switcher-item" href="{{ route('switch.language', 'en') }}">
        <img src="{{ asset('svg/flags/en.svg') }}" width="20" class="me-1"> English
      </a>
    </li>
    <li>
      <a class="dropdown-item language-switcher-item" href="{{ route('switch.language', 'fr') }}">
        <img src="{{ asset('svg/flags/fr.svg') }}" width="20" class="me-1"> Français
      </a>
    </li>
    <li>
      <a class="dropdown-item language-switcher-item" href="{{ route('switch.language', 'pt') }}">
        <img src="{{ asset('svg/flags/pt.svg') }}" width="20" class="me-1"> Português
      </a>
    </li>
    <li>
      <a class="dropdown-item language-switcher-item" href="{{ route('switch.language', 'de') }}">
        <img src="{{ asset('svg/flags/de.svg') }}" width="20" class="me-1"> Deutsch
      </a>
    </li>
  </ul>
</div>

@push('css')
<style>
  .login-card-body, .register-card-body, .card, .card-body { overflow: visible !important; }
  .language-switcher { position: relative; z-index: 1060; }
  .language-switcher .dropdown-menu { z-index: 1080; }
  .dark-mode .language-switcher .dropdown-menu,
  [data-theme="dark"] .language-switcher .dropdown-menu {
    background-color: #2b2f3a !important;
    color: #e9ecef !important;
  }
</style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.language-switcher').forEach(function (wrapper) {
    const btn  = wrapper.querySelector('[data-bs-toggle="dropdown"], [data-toggle="dropdown"]');
    const menu = wrapper.querySelector('.dropdown-menu');

    if (window.bootstrap && bootstrap.Dropdown) {
      new bootstrap.Dropdown(btn);
      return;
    }
    if (window.jQuery && jQuery.fn.dropdown) {
      jQuery(btn).dropdown();
      return;
    }
    if (btn && menu) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        menu.classList.toggle('show');
      });
      document.addEventListener('click', function (e) {
        if (!wrapper.contains(e.target)) menu.classList.remove('show');
      });
    }
  });
});
</script>
@endpush
