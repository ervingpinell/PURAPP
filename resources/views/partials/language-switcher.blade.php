{{-- resources/views/partials/language-switcher.blade.php --}}
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
      class="language-switcher-toggle"
      type="button"
      id="languageDropdown"
      data-bs-toggle="dropdown"  {{-- BS5 --}}
      data-toggle="dropdown"     {{-- BS4 --}}
      aria-expanded="false">
      <img src="{{ asset('svg/flags/' . $flag) }}" alt="Current language" width="20">
      {{ $label }}
  </button>

  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-right" aria-labelledby="languageDropdown">
    <li>
      <a class="language-switcher-item" href="{{ route('switch.language', 'es') }}">
        <img src="{{ asset('svg/flags/es.svg') }}" width="20"> Español
      </a>
    </li>
    <li>
      <a class="language-switcher-item" href="{{ route('switch.language', 'en') }}">
        <img src="{{ asset('svg/flags/en.svg') }}" width="20"> English
      </a>
    </li>
    <li>
      <a class="language-switcher-item" href="{{ route('switch.language', 'fr') }}">
        <img src="{{ asset('svg/flags/fr.svg') }}" width="20"> Français
      </a>
    </li>
    <li>
      <a class="language-switcher-item" href="{{ route('switch.language', 'pt') }}">
        <img src="{{ asset('svg/flags/pt.svg') }}" width="20"> Português
      </a>
    </li>
    <li>
      <a class="language-switcher-item" href="{{ route('switch.language', 'de') }}">
        <img src="{{ asset('svg/flags/de.svg') }}" width="20"> Deutsch
      </a>
    </li>
  </ul>
</div>
