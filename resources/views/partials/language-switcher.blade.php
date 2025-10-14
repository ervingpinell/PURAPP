<div class="dropdown language-switcher">
  @php
    // Locale interno (puede ser "pt" o "pt_BR")
    $currentLocale  = app()->getLocale();

    // Prefijo UI de 2 letras (usa el helper nuevo)
    $currentPrefix  = function_exists('locale_to_prefix')
        ? locale_to_prefix($currentLocale)
        : substr(strtolower(str_replace('-', '_', $currentLocale)), 0, 2);

    // Locales soportados con sus nombres (prefijos) desde config/routes.php
    $locales = config('routes.locales', []); // ['es'=>..., 'en'=>..., 'pt'=>..., ...]

    // Banderas por prefijo
    $flags = [
      'es' => 'es.svg',
      'en' => 'en.svg',
      'fr' => 'fr.svg',
      'pt' => 'pt.svg', // <- usará esta incluso si el locale real es pt_BR
      'de' => 'de.svg',
    ];

    $flag  = $flags[$currentPrefix] ?? 'es.svg';
    $label = strtoupper($currentPrefix); // "PT" aunque el locale sea pt_BR
  @endphp

  <button class="language-switcher-toggle"
          type="button"
          id="languageDropdown"
          data-bs-toggle="dropdown"
          aria-expanded="false">
    <img src="{{ asset('svg/flags/' . $flag) }}" alt="Current language" width="20" height="14" class="me-1">
    {{ $label }}
  </button>

  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
    @foreach($locales as $code => $config)
      @php
        // $code ya es prefijo (es, en, fr, pt, de)
        $flagFile = $flags[$code] ?? ($code . '.svg');
        $isActive = ($code === $currentPrefix);
      @endphp
      <li>
        <a class="dropdown-item language-switcher-item {{ $isActive ? 'active' : '' }}"
           @if($isActive) aria-current="true" @endif
           href="{{ route('switch.language', $code) }}">
          <img src="{{ asset('svg/flags/' . $flagFile) }}" width="20" height="14" class="me-1" alt="{{ $config['name'] }}">
          {{ $config['name'] }}
        </a>
      </li>
    @endforeach
  </ul>
</div>

@push('css')
<style>
.language-switcher { position: relative; z-index: 1060; }
.language-switcher .dropdown-menu { z-index: 1080; }
.dark-mode .language-switcher .dropdown-menu,
[data-theme="dark"] .language-switcher .dropdown-menu {
  background-color: #2b2f3a !important; color: #e9ecef !important;
}
</style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.language-switcher').forEach(function (wrapper) {
    const btn = wrapper.querySelector('[data-bs-toggle="dropdown"], [data-toggle="dropdown"]');
    const menu = wrapper.querySelector('.dropdown-menu');

    if (window.bootstrap && bootstrap.Dropdown) { new bootstrap.Dropdown(btn); return; }
    if (window.jQuery && jQuery.fn.dropdown) { jQuery(btn).dropdown(); return; }

    if (btn && menu) {
      btn.addEventListener('click', function (e) {
        e.preventDefault(); e.stopPropagation();
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
